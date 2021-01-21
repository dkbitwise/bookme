<?php
const URL_POSTBACK_IPN_LIVE = 'https://www.paypal.com/cgi-bin/webscr';
const URL_POSTBACK_IPN_SANDBOX = 'https://www.sandbox.paypal.com/cgi-bin/webscr';

if (!session_id()) {
    @session_start();
}

if (isset($_GET['bookme_action']) && $_GET['bookme_action'] == 'paypal-cancel') {
    header('Location: ' . add_query_arg(array(
            'bookme_action' => 'error',
            'error_msg' => (isset($_GET['error_msg'])) ? $_GET['error_msg'] : __('You cancelled your appointment booking, please try again.', 'bookme'),
        ), bookme_getCurrentPageURL()
        ));
    exit;
}

function sendECRequest()
{
    if (isset($_POST['pay_access_token'])) {
        $current_url = bookme_getCurrentPageURL();
        $cart_enable = bookme_get_settings('bookme_enable_cart', 0) && (!bookme_get_settings('enable_woocommerce', 0));
        if ($cart_enable) {
            $access_token = 1;
            $total = 0;
            foreach ($_SESSION['bookme']['cart'] as $cart) {
                if (isset($cart['off_price'])) {
                    $total += $cart['off_price'];
                } else {
                    global $wpdb;
                    $table_book_service = $wpdb->prefix . 'bookme_service';
                    $service = $cart['service'];
                    $resultS = $wpdb->get_results("SELECT price FROM $table_book_service WHERE id='$service'");
                    $total += $resultS[0]->price * $cart['person'];
                }
            }
            $total = number_format($total, 2);
        } else {
            $access_token = $_POST['pay_access_token'];
            if (isset($_SESSION['bookme'][$access_token]['discount']['price'])) {
                $total = number_format((float)$_SESSION['bookme'][$access_token]['discount']['price'], 2);
            } else {
                $total = $_SESSION['bookme'][$access_token]['person'] * $_SESSION['bookme'][$access_token]['price'];
            }
        }

        $data = array(
            'SOLUTIONTYPE' => 'Sole',
            'PAYMENTREQUEST_0_PAYMENTACTION' => 'Sale',
            'PAYMENTREQUEST_0_CURRENCYCODE' => bookme_get_settings('pmt_currency', 'USD'),
            'NOSHIPPING' => 1,
            'RETURNURL' => add_query_arg(array('bookme_action' => 'paypal-return', 'access_token' => $access_token), $current_url),
            'CANCELURL' => add_query_arg(array('bookme_action' => 'paypal-cancel', 'access_token' => $access_token), $current_url)
        );

        $data['L_PAYMENTREQUEST_0_NAME0'] = $_SESSION['bookme'][$access_token]['name'];
        $data['L_PAYMENTREQUEST_0_AMT0'] = $total;
        $data['L_PAYMENTREQUEST_0_QTY0'] = 1;

        $data['PAYMENTREQUEST_0_AMT'] = $total;
        $data['PAYMENTREQUEST_0_ITEMAMT'] = $total;

        $response = sendNvpRequest('SetExpressCheckout', $data);

        // Respond according to message we receive from PayPal
        $ack = strtoupper($response['ACK']);
        if ($ack == 'SUCCESS' || $ack == 'SUCCESSWITHWARNING') {
            // Redirect to PayPal.
            $paypal_url = sprintf(
                'https://www%s.paypal.com/cgi-bin/webscr?cmd=_express-checkout&useraction=commit&token=%s',
                (bookme_get_settings('pmt_paypal_sandbox') == 'yes') ?
                    '.sandbox' :
                    '',
                urlencode($response['TOKEN'])
            );
            header('Location: ' . $paypal_url);
        } else {
            header('Location: ' . $current_url . "?bookme_action=paypal-cancel&error_msg=" . urlencode($response['L_LONGMESSAGE0']));
        }

        exit;
    }
}

function sendNvpRequest($method, array $data)
{
    $sandbox_url = 'https://api-3t.sandbox.paypal.com/nvp';
    $url = 'https://api-3t.paypal.com/nvp';
    $url = (bookme_get_settings('pmt_paypal_sandbox') == 'yes') ?
        $sandbox_url :
        $url;

    $curl = new Curl();
    $curl->options['CURLOPT_SSL_VERIFYPEER'] = false;
    $curl->options['CURLOPT_SSL_VERIFYHOST'] = false;

    $data['METHOD'] = $method;
    $data['VERSION'] = '76.0';
    $data['USER'] = bookme_get_settings('pmt_paypal_api_username');
    $data['PWD'] = bookme_get_settings('pmt_paypal_api_password');
    $data['SIGNATURE'] = bookme_get_settings('pmt_paypal_api_signature');

    $httpResponse = $curl->post($url, $data);
    if (!$httpResponse) {
        exit($curl->error());
    }

    // Extract the response details.
    parse_str($httpResponse, $PayPalResponse);

    if (!array_key_exists('ACK', $PayPalResponse)) {
        exit('Invalid HTTP Response for POST request to ' . $url);
    }

    return $PayPalResponse;
}

function verifyIPN()
{
    $paypalUrl = (bookme_get_settings('pmt_paypal_sandbox') == 'yes') ?
        URL_POSTBACK_IPN_SANDBOX :
        URL_POSTBACK_IPN_LIVE;

    $raw_post_data = file_get_contents('php://input');
    $raw_post_array = explode('&', $raw_post_data);
    $postData = array();
    foreach ($raw_post_array as $keyval) {
        $keyval = explode('=', $keyval);
        if (count($keyval) == 2) {
            $postData[$keyval[0]] = urldecode($keyval[1]);
        }
    }

    $req = 'cmd=_notify-validate';
    foreach ($postData as $key => $value) {
        if (
            (function_exists('get_magic_quotes_gpc') === true)
            && (get_magic_quotes_gpc() === 1)
        ) {
            $value = urlencode(stripslashes($value));
        } else {
            $value = urlencode($value);
        }
        $req .= "&$key=$value";
    }

    $response = wp_safe_remote_post(
        $paypalUrl,
        array(
            'sslcertificates' => __DIR__ . '/cert/cacert.pem',
            'body' => $req,
        )
    );

    if (is_wp_error($response)) {
        return false;
    }

    return strcmp($response['body'], 'VERIFIED') === 0;
}

/**
 * Process Express Checkout return request.
 */
function ecReturn()
{
    $error_message = '';

    if (isset($_REQUEST['token']) && isset($_REQUEST['PayerID'])) {
        $access_token = $_REQUEST['access_token'];
        $token = $_REQUEST['token'];
        $data = array('TOKEN' => $token);
        // Send the request to PayPal.
        $response = sendNvpRequest('GetExpressCheckoutDetails', $data);

        if (strtoupper($response['ACK']) == 'SUCCESS') {
            $data['PAYERID'] = $_REQUEST['PayerID'];
            $data['PAYMENTREQUEST_0_PAYMENTACTION'] = 'Sale';

            foreach (array('PAYMENTREQUEST_0_AMT', 'PAYMENTREQUEST_0_ITEMAMT', 'PAYMENTREQUEST_0_CURRENCYCODE', 'L_PAYMENTREQUEST_0') as $parameter) {
                if (array_key_exists($parameter, $response)) {
                    $data[$parameter] = $response[$parameter];
                }
            }

            global $wpdb;

            $table_coupans = $wpdb->prefix . 'bookme_coupons';
            $table_settings = $wpdb->prefix . 'bookme_settings';
            $table_all_employee = $wpdb->prefix . 'bookme_employee';
            $table = $wpdb->prefix . 'bookme_category';
            $table_enotification = $wpdb->prefix . 'bookme_email_notification';
            $table_sms_notification = $wpdb->prefix . 'bookme_sms_notification';
            $table_current_booking_fields = $wpdb->prefix . 'bookme_current_booking_fields';
            $table_current_booking = $wpdb->prefix . 'bookme_current_booking';
            $table_book_service = $wpdb->prefix . 'bookme_service';
            $table_customer_booking = $wpdb->prefix . 'bookme_customer_booking';
            $table_customers = $wpdb->prefix . 'bookme_customers';
            $table_payments = $wpdb->prefix . 'bookme_payments';
            $table_holidays = $wpdb->prefix . 'bookme_holidays';

            add_filter('wp_mail_content_type', 'bookme_set_html_mail_content_type');

            $cart_enable = bookme_get_settings('bookme_enable_cart', 0) && (!bookme_get_settings('enable_woocommerce', 0));
            if ($cart_enable) {
                $access_token = 1;
                $name = $_SESSION['bookme'][$access_token]['name'];
                $email = $_SESSION['bookme'][$access_token]['email'];
                $phone = $_SESSION['bookme'][$access_token]['phone'];
                $notes = $_SESSION['bookme'][$access_token]['notes'];

                $custom_text = $_SESSION['bookme'][$access_token]['custom_text'];
                $custom_textarea = $_SESSION['bookme'][$access_token]['custom_textarea'];
                $custom_content = $_SESSION['bookme'][$access_token]['custom_content'];
                $custom_checkbox = $_SESSION['bookme'][$access_token]['custom_checkbox'];
                $custom_radio = $_SESSION['bookme'][$access_token]['custom_radio'];
                $custom_select = $_SESSION['bookme'][$access_token]['custom_select'];

                foreach ($_SESSION['bookme']['cart'] as $key => $cart) {
                    $code = isset($cart['disc_code']) ? $cart['disc_code'] : '';
                    $dic_price = isset($cart['off_price']) ? $cart['off_price'] : '';
                    $appointstart = $cart['time_s'];
                    $appointend = $cart['time_e'];
                    $category = $cart['category'];
                    $service = $cart['service'];
                    $employee = $cart['employee'];
                    $dates = $cart['date'];
                    $person = $cart['person'];

                    $resultS = $wpdb->get_results("SELECT price FROM $table_book_service WHERE id='$service'");
                    $price = $resultS[0]->price;

                    $dt = new DateTime($dates);
                    $booking_date = $dt->format('Y-m-d');

                    $hodiday = 0;
                    $result = $wpdb->get_results("SELECT * FROM $table_holidays WHERE staff_id = $employee");
                    foreach ($result as $holiday) {
                        if ($holiday->holi_date == $booking_date) {
                            $hodiday = 1;
                        }
                    }

                    if ($hodiday == 0) {
                        $rowcount = $wpdb->get_var("SELECT COUNT(cb.id) FROM $table_customer_booking cb LEFT JOIN $table_current_booking b ON cb.booking_id = b.id LEFT JOIN $table_customers c ON cb.customer_id = c.id WHERE b.cat_id='" . $category . "' and b.ser_id='" . $service . "' and b.emp_id='" . $employee . "' and b.date='" . $dates . "' and b.time = '" . $appointstart . "' and c.email='" . $email . "'");
                        if ($rowcount >= 1) {
                            $error_message = __('Booking already exist.', 'bookme');
                        } else {
                            $booked = 0;
                            $resultSs = $wpdb->get_results("SELECT capacity,duration,name FROM $table_book_service WHERE id=$service and catId=$category");
                            $capacity = $resultSs[0]->capacity;
                            $duration = $resultSs[0]->duration;
                            $servname = $resultSs[0]->name;
                            $countAppoint = $wpdb->get_results("SELECT SUM(cb.no_of_person) as sump FROM $table_customer_booking cb LEFT JOIN $table_current_booking b ON b.id = cb.booking_id WHERE  b.ser_id='$service' and b.emp_id='$employee' and b.date='$dates' and b.time = '$appointstart' and b.duration = '$duration'");
                            if (empty($countAppoint[0]->sump)) {
                                $booked = 0;
                            } else {
                                $booked = $countAppoint[0]->sump;
                            }
                            $avl = 0;
                            $avl = $capacity - $booked;
                            if ($person > $avl) {
                                $error_message = __('Seats not available for this time period.', 'bookme');
                            }
                        }
                    }
                }
                if (empty($error_message)) {
                    $response = sendNvpRequest('DoExpressCheckoutPayment', $data);
                    if ('SUCCESS' == strtoupper($response['ACK']) || 'SUCCESSWITHWARNING' == strtoupper($response['ACK'])) {
                        // Get transaction info
                        $response = sendNvpRequest('GetTransactionDetails', array('TRANSACTIONID' => $response['PAYMENTINFO_0_TRANSACTIONID']));
                        if ('SUCCESS' == strtoupper($response['ACK']) || 'SUCCESSWITHWARNING' == strtoupper($response['ACK'])) {

                            foreach ($_SESSION['bookme']['cart'] as $key => $cart) {
                                $code = isset($cart['disc_code']) ? $cart['disc_code'] : '';
                                $dic_price = isset($cart['off_price']) ? $cart['off_price'] : '';
                                $appointstart = $cart['time_s'];
                                $appointend = $cart['time_e'];
                                $category = $cart['category'];
                                $service = $cart['service'];
                                $employee = $cart['employee'];
                                $dates = $cart['date'];
                                $person = $cart['person'];

                                $resultS = $wpdb->get_results("SELECT name,price,duration FROM $table_book_service WHERE id='$service'");
                                $price = $resultS[0]->price;
                                $duration = $resultS[0]->duration;
                                $servname = $resultS[0]->name;

                                $resultE = $wpdb->get_results("SELECT name,email,google_data,phone FROM $table_all_employee WHERE id=" . $employee);
                                $employee_name = $resultE[0]->name;
                                $employee_email = $resultE[0]->email;
                                $employee_phone = $resultE[0]->phone;
                                $google_data = $resultE[0]->google_data;

                                if ($wpdb->insert($table_customers, array(
                                    'name' => $name,
                                    'email' => $email,
                                    'phone' => $phone,
                                    'notes' => $notes
                                ))
                                ) {
                                    $customer_id = $wpdb->insert_id;
                                    $booking_result = $wpdb->get_results("select id,google_event_id from $table_current_booking where ser_id = $service and emp_id = $employee and date = '$dates' and time = '$appointstart'");
                                    $gc_event_id = null;
                                    if ($wpdb->num_rows > 0) {
                                        $booking_id = $booking_result[0]->id;
                                        $gc_event_id = $booking_result[0]->google_event_id;
                                    }else{
                                        $wpdb->insert($table_current_booking, array('cat_id' => $category, 'ser_id' => $service, 'emp_id' => $employee, 'date' => $dates, 'time' => $appointstart, 'duration' => $duration));
                                        $booking_id = $wpdb->insert_id;
                                    }

                                    $wpdb->insert($table_payments, array(
                                        'created' => current_time('mysql'),
                                        'type' => 'Paypal',
                                        'price' => $price,
                                        'discount_price' => $dic_price,
                                        'status' => 'Completed'
                                    ));
                                    $payment_id = $wpdb->insert_id;
                                    $wpdb->insert($table_customer_booking, array(
                                        'customer_id' => $customer_id,
                                        'booking_id' => $booking_id,
                                        'payment_id' => $payment_id,
                                        'no_of_person' => $person,
                                        'status' => 'Approved'
                                    ));
                                    $customer_booking_id = $wpdb->insert_id;
                                    $limit = 0;
                                    if ($code) {
                                        $resultOption = $wpdb->get_results("SELECT * FROM $table_coupans where coupon_code='$code' and find_in_set($service,ser_id) <> 0");
                                        if ($wpdb->num_rows >= 1) {
                                            $limit = $resultOption[0]->coupon_used_limit + 1;
                                            $wpdb->update($table_coupans, array('coupon_used_limit' => $limit), array('id' => $resultOption[0]->id), array('%s'), array( '%d'));
                                        }

                                    }

                                    $resultcompanyName = $wpdb->get_results("SELECT book_value FROM $table_settings WHERE book_key='companyName'");
                                    $resultcompanyAddress = $wpdb->get_results("SELECT book_value FROM $table_settings WHERE book_key='companyAddress'");
                                    $resultcompanyPhone = $wpdb->get_results("SELECT book_value FROM $table_settings WHERE book_key='companyPhone'");
                                    $resultcompanyWebsite = $wpdb->get_results("SELECT book_value FROM $table_settings WHERE book_key='companyWebsite'");

                                    $company_name = $resultcompanyName[0]->book_value;
                                    $company_address = $resultcompanyAddress[0]->book_value;
                                    $company_phone = $resultcompanyPhone[0]->book_value;
                                    $company_website = $resultcompanyWebsite[0]->book_value;

                                    $endtime = strtotime($appointstart);
                                    $appointment_time = date_i18n(get_option('time_format'), strtotime($appointstart));
                                    $appointment_end_time = date_i18n(get_option('time_format'), $endtime + $duration);

                                    $results = $wpdb->get_results("SELECT name FROM $table where id=" . $category . "");
                                    $category_name = $results[0]->name;
                                    $service_name = $servname;

                                    $booking_date = date_i18n(get_option('date_format'), strtotime($dates));
                                    $ttl_person = $person;
                                    $customer_name = ucfirst($name);
                                    $customer_email = $email;
                                    $customer_phone = $phone;
                                    $customer_note = $notes;
                                    $custom_fields_text = '';
                                    $custom_fields_html = '';

                                    foreach ($custom_text as $custom) {
                                        $wpdb->insert($table_current_booking_fields, array('booking_id' => $customer_booking_id, 'key_field' => base64_decode($custom['name']), 'field_val' => $custom['value'], 'status' => 'valid'));

                                        if ( $custom['value'] != '' ) {
                                            $custom_fields_text .= sprintf(
                                                "%s: %s\n",
                                                base64_decode($custom['name']), $custom['value']
                                            );

                                            $custom_fields_html .= sprintf(
                                                '<tr valign=top><td>%s:&nbsp;</td><td>%s</td></tr>',
                                                base64_decode($custom['name']), $custom['value']
                                            );
                                        }
                                    }
                                    foreach ($custom_textarea as $custom) {

                                        $wpdb->insert($table_current_booking_fields, array('booking_id' => $customer_booking_id, 'key_field' => base64_decode($custom['name']), 'field_val' => $custom['value'], 'status' => 'valid'));

                                        if ( $custom['value'] != '' ) {
                                            $custom_fields_text .= sprintf(
                                                "%s: %s\n",
                                                base64_decode($custom['name']), $custom['value']
                                            );

                                            $custom_fields_html .= sprintf(
                                                '<tr valign=top><td>%s:&nbsp;</td><td>%s</td></tr>',
                                                base64_decode($custom['name']), $custom['value']
                                            );
                                        }
                                    }
                                    foreach ($custom_content as $custom) {

                                        $wpdb->insert($table_current_booking_fields, array('booking_id' => $customer_booking_id, 'key_field' => base64_decode($custom['name']), 'field_val' => $custom['value'], 'status' => 'valid'));

                                        if ( $custom['value'] != '' ) {
                                            $custom_fields_text .= sprintf(
                                                "%s: %s\n",
                                                base64_decode($custom['name']), $custom['value']
                                            );

                                            $custom_fields_html .= sprintf(
                                                '<tr valign=top><td>%s:&nbsp;</td><td>%s</td></tr>',
                                                base64_decode($custom['name']), $custom['value']
                                            );
                                        }
                                    }
                                    foreach ($custom_select as $custom) {

                                        $wpdb->insert($table_current_booking_fields, array('booking_id' => $customer_booking_id, 'key_field' => base64_decode($custom['name']), 'field_val' => $custom['value'], 'status' => 'valid'));

                                        if ( $custom['value'] != '' ) {
                                            $custom_fields_text .= sprintf(
                                                "%s: %s\n",
                                                base64_decode($custom['name']), $custom['value']
                                            );

                                            $custom_fields_html .= sprintf(
                                                '<tr valign=top><td>%s:&nbsp;</td><td>%s</td></tr>',
                                                base64_decode($custom['name']), $custom['value']
                                            );
                                        }
                                    }
                                    foreach ($custom_radio as $custom) {

                                        $wpdb->insert($table_current_booking_fields, array('booking_id' => $customer_booking_id, 'key_field' => base64_decode($custom['name']), 'field_val' => $custom['value'], 'status' => 'valid'));

                                        if ( $custom['value'] != '' ) {
                                            $custom_fields_text .= sprintf(
                                                "%s: %s\n",
                                                base64_decode($custom['name']), $custom['value']
                                            );

                                            $custom_fields_html .= sprintf(
                                                '<tr valign=top><td>%s:&nbsp;</td><td>%s</td></tr>',
                                                base64_decode($custom['name']), $custom['value']
                                            );
                                        }
                                    }
                                    foreach ($custom_checkbox as $custom) {

                                        $wpdb->insert($table_current_booking_fields, array('booking_id' => $customer_booking_id, 'key_field' => base64_decode($custom['name']), 'field_val' => implode(',', $custom['value']), 'status' => 'valid'));

                                        if ( $custom['value'] != '' ) {
                                            $custom_fields_text .= sprintf(
                                                "%s: %s\n",
                                                base64_decode($custom['name']), implode(',', $custom['value'])
                                            );

                                            $custom_fields_html .= sprintf(
                                                '<tr valign=top><td>%s:&nbsp;</td><td>%s</td></tr>',
                                                base64_decode($custom['name']), implode(',', $custom['value'])
                                            );
                                        }
                                    }
                                    if ( $custom_fields_html != '' ) {
                                        $custom_fields_html = "<table cellspacing=0 cellpadding=0 border=0>$custom_fields_html</table>";
                                    }

                                    $headers = array('From: ' . bookme_get_settings('bookme_email_sender_name', get_bloginfo('name')) . ' <' . bookme_get_settings('bookme_email_sender_email', get_bloginfo('admin_email')) . '>');

                                    if (bookme_get_settings('email_customer', 'true') == 'true') {
                                        $sqlblbc = "SELECT * FROM $table_enotification where email_key='customer_sub' and key_type='customer_confirm'";
                                        $reslabl = $wpdb->get_results($sqlblbc);
                                        $subject = $reslabl[0]->email_value;
                                        $sqlblbc = "SELECT * FROM $table_enotification where email_key='customer_msg' and key_type='customer_confirm'";
                                        $reslabl = $wpdb->get_results($sqlblbc);
                                        $message = $reslabl[0]->email_value;

                                        $message = str_replace("{booking_time}", $appointment_time, $message);
                                        $message = str_replace("{booking_end_time}", $appointment_end_time, $message);
                                        $message = str_replace("{booking_date}", $booking_date, $message);
                                        $message = str_replace("{number_of_persons}", $ttl_person, $message);
                                        $message = str_replace("{customer_name}", $customer_name, $message);
                                        $message = str_replace("{customer_email}", $customer_email, $message);
                                        $message = str_replace("{customer_phone}", $customer_phone, $message);
                                        $message = str_replace("{customer_note}", $customer_note, $message);
                                        $message = str_replace("{company_name}", $company_name, $message);
                                        $message = str_replace("{company_address}", $company_address, $message);
                                        $message = str_replace("{company_phone}", $company_phone, $message);
                                        $message = str_replace("{company_website}", $company_website, $message);
                                        $message = str_replace("{employee_name}", $employee_name, $message);
                                        $message = str_replace("{category_name}", $category_name, $message);
                                        $message = str_replace("{service_name}", $service_name, $message);
                                        $message = str_replace("{custom_field}", $custom_fields_text, $message);
                                        $message = str_replace("{custom_field_2col}", $custom_fields_html, $message);

                                        wdm_mail_new($customer_email, $subject, $message, $headers);
                                    }

                                    if (bookme_get_settings('email_employee', 'true') == 'true') {
                                        $sqlblbc = "SELECT * FROM $table_enotification where email_key='employee_sub' and key_type='employee_confirm'";
                                        $reslabl = $wpdb->get_results($sqlblbc);
                                        $subject = $reslabl[0]->email_value;
                                        $sqlblbc = "SELECT * FROM $table_enotification where email_key='employee_msg' and key_type='employee_confirm'";
                                        $reslabl = $wpdb->get_results($sqlblbc);
                                        $message = $reslabl[0]->email_value;

                                        $message = str_replace("{booking_time}", $appointment_time, $message);
                                        $message = str_replace("{booking_end_time}", $appointment_end_time, $message);
                                        $message = str_replace("{booking_date}", $booking_date, $message);
                                        $message = str_replace("{number_of_persons}", $ttl_person, $message);
                                        $message = str_replace("{customer_name}", $customer_name, $message);
                                        $message = str_replace("{customer_email}", $customer_email, $message);
                                        $message = str_replace("{customer_phone}", $customer_phone, $message);
                                        $message = str_replace("{customer_note}", $customer_note, $message);
                                        $message = str_replace("{company_name}", $company_name, $message);
                                        $message = str_replace("{company_address}", $company_address, $message);
                                        $message = str_replace("{company_phone}", $company_phone, $message);
                                        $message = str_replace("{company_website}", $company_website, $message);
                                        $message = str_replace("{employee_name}", $employee_name, $message);
                                        $message = str_replace("{category_name}", $category_name, $message);
                                        $message = str_replace("{service_name}", $service_name, $message);
                                        $message = str_replace("{custom_field}", $custom_fields_text, $message);
                                        $message = str_replace("{custom_field_2col}", $custom_fields_html, $message);

                                        wdm_mail_new($employee_email, $subject, $message, $headers);
                                    }

                                    if (bookme_get_settings('email_admin', 'true') == 'true') {
                                        $sqlblbc = "SELECT * FROM $table_enotification where email_key='admin_sub' and key_type='admin_confirm'";
                                        $reslabl = $wpdb->get_results($sqlblbc);
                                        $subject = $reslabl[0]->email_value;
                                        $sqlblbc = "SELECT * FROM $table_enotification where email_key='admin_msg' and key_type='admin_confirm'";
                                        $reslabl = $wpdb->get_results($sqlblbc);
                                        $message = $reslabl[0]->email_value;

                                        $message = str_replace("{booking_time}", $appointment_time, $message);
                                        $message = str_replace("{booking_end_time}", $appointment_end_time, $message);
                                        $message = str_replace("{booking_date}", $booking_date, $message);
                                        $message = str_replace("{number_of_persons}", $ttl_person, $message);
                                        $message = str_replace("{customer_name}", $customer_name, $message);
                                        $message = str_replace("{customer_email}", $customer_email, $message);
                                        $message = str_replace("{customer_phone}", $customer_phone, $message);
                                        $message = str_replace("{customer_note}", $customer_note, $message);
                                        $message = str_replace("{company_name}", $company_name, $message);
                                        $message = str_replace("{company_address}", $company_address, $message);
                                        $message = str_replace("{company_phone}", $company_phone, $message);
                                        $message = str_replace("{company_website}", $company_website, $message);
                                        $message = str_replace("{employee_name}", $employee_name, $message);
                                        $message = str_replace("{category_name}", $category_name, $message);
                                        $message = str_replace("{service_name}", $service_name, $message);
                                        $message = str_replace("{custom_field}", $custom_fields_text, $message);
                                        $message = str_replace("{custom_field_2col}", $custom_fields_html, $message);

                                        wdm_mail_new(get_bloginfo('admin_email'), $subject, $message, $headers);
                                    }

                                    remove_filter('wp_mail_content_type', 'wpdocs_set_html_mail_content_type');

                                    if (bookme_get_settings('sms_customer', 'true') == 'true') {
                                        $sqlblbc = "SELECT * FROM $table_sms_notification where sms_key='customer_msg' and key_type='customer_confirm'";
                                        $reslabl = $wpdb->get_results($sqlblbc);
                                        $message = $reslabl[0]->sms_value;

                                        $message = str_replace("{booking_time}", $appointment_time, $message);
                                        $message = str_replace("{booking_end_time}", $appointment_end_time, $message);
                                        $message = str_replace("{booking_date}", $booking_date, $message);
                                        $message = str_replace("{number_of_persons}", $ttl_person, $message);
                                        $message = str_replace("{customer_name}", $customer_name, $message);
                                        $message = str_replace("{customer_email}", $customer_email, $message);
                                        $message = str_replace("{customer_phone}", $customer_phone, $message);
                                        $message = str_replace("{customer_note}", $customer_note, $message);
                                        $message = str_replace("{company_name}", $company_name, $message);
                                        $message = str_replace("{company_address}", $company_address, $message);
                                        $message = str_replace("{company_phone}", $company_phone, $message);
                                        $message = str_replace("{company_website}", $company_website, $message);
                                        $message = str_replace("{employee_name}", $employee_name, $message);
                                        $message = str_replace("{category_name}", $category_name, $message);
                                        $message = str_replace("{service_name}", $service_name, $message);
                                        $message = str_replace("{custom_field}", $custom_fields_text, $message);
                                        $message = str_replace("{custom_field_2col}", $custom_fields_text, $message);

                                        bookme_send_sms_twilio($customer_phone, $message);
                                    }

                                    if (bookme_get_settings('sms_employee', 'true') == 'true') {
                                        $sqlblbc = "SELECT * FROM $table_sms_notification where sms_key='employee_msg' and key_type='employee_confirm'";
                                        $reslabl = $wpdb->get_results($sqlblbc);
                                        $message = $reslabl[0]->sms_value;

                                        $message = str_replace("{booking_time}", $appointment_time, $message);
                                        $message = str_replace("{booking_end_time}", $appointment_end_time, $message);
                                        $message = str_replace("{booking_date}", $booking_date, $message);
                                        $message = str_replace("{number_of_persons}", $ttl_person, $message);
                                        $message = str_replace("{customer_name}", $customer_name, $message);
                                        $message = str_replace("{customer_email}", $customer_email, $message);
                                        $message = str_replace("{customer_phone}", $customer_phone, $message);
                                        $message = str_replace("{customer_note}", $customer_note, $message);
                                        $message = str_replace("{company_name}", $company_name, $message);
                                        $message = str_replace("{company_address}", $company_address, $message);
                                        $message = str_replace("{company_phone}", $company_phone, $message);
                                        $message = str_replace("{company_website}", $company_website, $message);
                                        $message = str_replace("{employee_name}", $employee_name, $message);
                                        $message = str_replace("{category_name}", $category_name, $message);
                                        $message = str_replace("{service_name}", $service_name, $message);
                                        $message = str_replace("{custom_field}", $custom_fields_text, $message);
                                        $message = str_replace("{custom_field_2col}", $custom_fields_text, $message);

                                        bookme_send_sms_twilio($employee_phone, $message);
                                    }

                                    if (bookme_get_settings('sms_admin', 'true') == 'true') {
                                        $admin_phone = bookme_get_settings('bookme_admin_phone_no');
                                        if($admin_phone) {
                                            $sqlblbc = "SELECT * FROM $table_sms_notification where sms_key='admin_msg' and key_type='admin_confirm'";
                                            $reslabl = $wpdb->get_results($sqlblbc);
                                            $message = $reslabl[0]->sms_value;

                                            $message = str_replace("{booking_time}", $appointment_time, $message);
                                            $message = str_replace("{booking_end_time}", $appointment_end_time, $message);
                                            $message = str_replace("{booking_date}", $booking_date, $message);
                                            $message = str_replace("{number_of_persons}", $ttl_person, $message);
                                            $message = str_replace("{customer_name}", $customer_name, $message);
                                            $message = str_replace("{customer_email}", $customer_email, $message);
                                            $message = str_replace("{customer_phone}", $customer_phone, $message);
                                            $message = str_replace("{customer_note}", $customer_note, $message);
                                            $message = str_replace("{company_name}", $company_name, $message);
                                            $message = str_replace("{company_address}", $company_address, $message);
                                            $message = str_replace("{company_phone}", $company_phone, $message);
                                            $message = str_replace("{company_website}", $company_website, $message);
                                            $message = str_replace("{employee_name}", $employee_name, $message);
                                            $message = str_replace("{category_name}", $category_name, $message);
                                            $message = str_replace("{service_name}", $service_name, $message);
                                            $message = str_replace("{custom_field}", $custom_fields_text, $message);
                                            $message = str_replace("{custom_field_2col}", $custom_fields_text, $message);

                                            bookme_send_sms_twilio($admin_phone, $message);
                                        }
                                    }


                                    if (bookme_get_settings('bookme_gc_client_id') != null) {
                                        if($google_data) {
                                            include_once plugin_dir_path(__FILE__) . '/../../google.php';
                                            $bookme_gc_client = new Google_Client();
                                            $bookme_gc_client->setClientId(bookme_get_settings('bookme_gc_client_id'));
                                            $bookme_gc_client->setClientSecret(bookme_get_settings('bookme_gc_client_secret'));

                                            try {
                                                $bookme_gc_client->setAccessToken($google_data);
                                                if ($bookme_gc_client->isAccessTokenExpired()) {
                                                    $bookme_gc_client->refreshToken($bookme_gc_client->getRefreshToken());
                                                    $wpdb->update($table_all_employee, array('google_data' => $bookme_gc_client->getAccessToken()), array('id' => $employee), array('%s'), array('%d'));
                                                }
                                                $bookme_gc_service = new Google_Service_Calendar($bookme_gc_client);
                                                $google_calendar_id = 'primary';
                                                $gc_calendar = $bookme_gc_service->calendarList->get( $google_calendar_id );
                                                $gc_access = $gc_calendar->getAccessRole();
                                                if ( in_array( $gc_access, array( 'writer', 'owner' ) ) ) {
                                                    if($gc_event_id == null) {
                                                        $event_data = array(
                                                            'start' => $dates.' '.$appointstart,
                                                            'end' => $dates.' '.$appointend,
                                                            'name' => array($name),
                                                            'email' => array($email),
                                                            'phone' => array($phone),
                                                            'service' => $service_name,
                                                            'category' => $category_name,
                                                            'employee' => $employee_name,
                                                            'service_id' => $service,
                                                            'customer_id' => array($customer_id),
                                                            'booking_id' => $booking_id
                                                        );
                                                        $gc_event = bookme_get_event_data($event_data);

                                                        $createdEvent = $bookme_gc_service->events->insert($google_calendar_id, $gc_event);

                                                        $event_id = $createdEvent->getId();
                                                        if ($event_id) {
                                                            $wpdb->update($table_current_booking, array('google_event_id' => $event_id), array('id' => $booking_id), array('%s'), array('%d'));
                                                        }
                                                    }else{
                                                        $customer_result = $wpdb->get_results("select customer_id from $table_customer_booking where booking_id = $booking_id");
                                                        $customer_ids = array();
                                                        $name = array();
                                                        $email = array();
                                                        $phone = array();
                                                        foreach($customer_result as $customer_id){
                                                            $customer_ids[] = $customer_id->customer_id;
                                                            $customer_data = $wpdb->get_results("select name,email,phone from $table_customers where id = $customer_id->customer_id");
                                                            $name[] = $customer_data[0]->name;
                                                            $email[] = $customer_data[0]->email;
                                                            $phone[] = $customer_data[0]->phone;
                                                        }

                                                        $event_data = array(
                                                            'start' => $dates.' '.$appointstart,
                                                            'end' => $dates.' '.$appointend,
                                                            'name' => $name,
                                                            'email' => $email,
                                                            'phone' => $phone,
                                                            'service' => $service_name,
                                                            'category' => $category_name,
                                                            'employee' => $employee_name,
                                                            'service_id' => $service,
                                                            'customer_id' => $customer_ids,
                                                            'booking_id' => $booking_id
                                                        );
                                                        $gc_event = bookme_get_event_data($event_data);
                                                        $bookme_gc_service->events->update( $google_calendar_id, $gc_event_id, $gc_event );
                                                    }
                                                }
                                            } catch (Exception $e) {

                                            }
                                        }
                                    }

                                }
                            }
                        } else {
                            $error_message = $response['L_LONGMESSAGE0'];
                        }
                    } else {
                        $error_message = $response['L_LONGMESSAGE0'];
                    }
                    if (empty($error_message)) {
                        unset($_SESSION['bookme']);
                        $remove_parameters = array('bookme_action', 'access_token', 'error_msg', 'token', 'PayerID', 'type');
                        header('Location: ' . remove_query_arg($remove_parameters, bookme_getCurrentPageURL()) . '?status=success');
                        exit;
                    }
                }
            } else {
                $code = isset($_SESSION['bookme'][$access_token]['disc_code']) ? $_SESSION['bookme'][$access_token]['disc_code'] : '';
                $dic_price = isset($_SESSION['bookme'][$access_token]['discount']['off_price']) ? $_SESSION['bookme'][$access_token]['discount']['off_price'] : '';

                $appointstart = $_SESSION['bookme'][$access_token]['time_s'];
                $appointend = $_SESSION['bookme'][$access_token]['time_e'];
                $category = $_SESSION['bookme'][$access_token]['category'];
                $service = $_SESSION['bookme'][$access_token]['service'];
                $employee = $_SESSION['bookme'][$access_token]['employee'];
                $dates = $_SESSION['bookme'][$access_token]['date'];
                $person = $_SESSION['bookme'][$access_token]['person'];
                $name = $_SESSION['bookme'][$access_token]['name'];
                $email = $_SESSION['bookme'][$access_token]['email'];
                $phone = $_SESSION['bookme'][$access_token]['phone'];
                $notes = $_SESSION['bookme'][$access_token]['notes'];
                $price = $_SESSION['bookme'][$access_token]['price'];

                $custom_text = $_SESSION['bookme'][$access_token]['custom_text'];
                $custom_textarea = $_SESSION['bookme'][$access_token]['custom_textarea'];
                $custom_content = $_SESSION['bookme'][$access_token]['custom_content'];
                $custom_checkbox = $_SESSION['bookme'][$access_token]['custom_checkbox'];
                $custom_radio = $_SESSION['bookme'][$access_token]['custom_radio'];
                $custom_select = $_SESSION['bookme'][$access_token]['custom_select'];

                $dt = new DateTime($dates);
                $booking_date = $dt->format('Y-m-d');

                $hodiday = 0;
                $result = $wpdb->get_results("SELECT * FROM $table_holidays WHERE staff_id = $employee");
                foreach ($result as $holiday) {
                    if ($holiday->holi_date == $booking_date) {
                        $hodiday = 1;
                    }
                }

                if ($hodiday == 0) {
                    $rowcount = $wpdb->get_var("SELECT COUNT(cb.id) FROM $table_customer_booking cb LEFT JOIN $table_current_booking b ON cb.booking_id = b.id LEFT JOIN $table_customers c ON cb.customer_id = c.id WHERE b.cat_id='" . $category . "' and b.ser_id='" . $service . "' and b.emp_id='" . $employee . "' and b.date='" . $dates . "' and b.time = '" . $appointstart . "' and c.email='" . $email . "'");
                    if ($rowcount >= 1) {
                        $error_message = __('Booking already exist.', 'bookme');
                    } else {
                        $booked = 0;
                        $resultSs = $wpdb->get_results("SELECT capacity,duration,name FROM $table_book_service WHERE id=$service and catId=$category");
                        $capacity = $resultSs[0]->capacity;
                        $duration = $resultSs[0]->duration;
                        $servname = $resultSs[0]->name;

                        $resultE = $wpdb->get_results("SELECT name,email,google_data,phone FROM $table_all_employee WHERE id=" . $employee);
                        $employee_name = $resultE[0]->name;
                        $employee_email = $resultE[0]->email;
                        $employee_phone = $resultE[0]->phone;
                        $google_data = $resultE[0]->google_data;

                        $countAppoint = $wpdb->get_results("SELECT SUM(cb.no_of_person) as sump FROM $table_customer_booking cb LEFT JOIN $table_current_booking b ON b.id = cb.booking_id WHERE  b.ser_id='$service' and b.emp_id='$employee' and b.date='$dates' and b.time = '$appointstart' and b.duration = '$duration'");
                        if (empty($countAppoint[0]->sump)) {
                            $booked = 0;
                        } else {
                            $booked = $countAppoint[0]->sump;
                        }
                        $avl = 0;
                        $avl = $capacity - $booked;
                        if ($person <= $avl) {
                            if ($wpdb->insert($table_customers, array(
                                'name' => $name,
                                'email' => $email,
                                'phone' => $phone,
                                'notes' => $notes
                            ))
                            ) {
                                $customer_id = $wpdb->insert_id;

                                // We need to execute the "DoExpressCheckoutPayment" at this point to Receive payment from user.
                                $response = sendNvpRequest('DoExpressCheckoutPayment', $data);
                                if ('SUCCESS' == strtoupper($response['ACK']) || 'SUCCESSWITHWARNING' == strtoupper($response['ACK'])) {
                                    // Get transaction info
                                    $response = sendNvpRequest('GetTransactionDetails', array('TRANSACTIONID' => $response['PAYMENTINFO_0_TRANSACTIONID']));
                                    if ('SUCCESS' == strtoupper($response['ACK']) || 'SUCCESSWITHWARNING' == strtoupper($response['ACK'])) {

                                        $booking_result = $wpdb->get_results("select id,google_event_id from $table_current_booking where ser_id = $service and emp_id = $employee and date = '$dates' and time = '$appointstart'");
                                        $gc_event_id = null;
                                        if ($wpdb->num_rows > 0) {
                                            $booking_id = $booking_result[0]->id;
                                            $gc_event_id = $booking_result[0]->google_event_id;
                                        }else{
                                            $wpdb->insert($table_current_booking, array('cat_id' => $category, 'ser_id' => $service, 'emp_id' => $employee, 'date' => $dates, 'time' => $appointstart, 'duration' => $duration));
                                            $booking_id = $wpdb->insert_id;
                                        }

                                        $wpdb->insert($table_payments, array(
                                            'created' => current_time('mysql'),
                                            'type' => 'Paypal',
                                            'price' => $price,
                                            'discount_price' => $dic_price,
                                            'status' => 'Completed'
                                        ));
                                        $payment_id = $wpdb->insert_id;
                                        $wpdb->insert($table_customer_booking, array(
                                            'customer_id' => $customer_id,
                                            'booking_id' => $booking_id,
                                            'payment_id' => $payment_id,
                                            'no_of_person' => $person,
                                            'status' => 'Approved'
                                        ));
                                        $customer_booking_id = $wpdb->insert_id;
                                        $limit = 0;
                                        if (isset($_SESSION['bookme'][$access_token]['disc_code'])) {

                                            $resultOption = $wpdb->get_results("SELECT * FROM $table_coupans where coupon_code='$code' and ser_id='$service'");
                                            if ($wpdb->num_rows >= 1) {
                                                $limit = $resultOption[0]->coupon_used_limit + 1;
                                                $wpdb->update($table_coupans, array('coupon_used_limit' => $limit), array('coupon_code' => $code, 'ser_id' => $service,), array('%s'), array('%s', '%d'));
                                            }

                                        }

                                        $resultcompanyName = $wpdb->get_results("SELECT book_value FROM $table_settings WHERE book_key='companyName'");
                                        $resultcompanyAddress = $wpdb->get_results("SELECT book_value FROM $table_settings WHERE book_key='companyAddress'");
                                        $resultcompanyPhone = $wpdb->get_results("SELECT book_value FROM $table_settings WHERE book_key='companyPhone'");
                                        $resultcompanyWebsite = $wpdb->get_results("SELECT book_value FROM $table_settings WHERE book_key='companyWebsite'");

                                        $company_name = $resultcompanyName[0]->book_value;
                                        $company_address = $resultcompanyAddress[0]->book_value;
                                        $company_phone = $resultcompanyPhone[0]->book_value;
                                        $company_website = $resultcompanyWebsite[0]->book_value;

                                        $endtime = strtotime($appointstart);
                                        $appointment_time = date_i18n(get_option('time_format'), strtotime($appointstart));
                                        $appointment_end_time = date_i18n(get_option('time_format'), $endtime + $duration);

                                        $results = $wpdb->get_results("SELECT name FROM $table where id=" . $category . "");
                                        $category_name = $results[0]->name;
                                        $service_name = $servname;


                                        $booking_date = date_i18n(get_option('date_format'), strtotime($dates));
                                        $ttl_person = $person;
                                        $customer_name = ucfirst($name);
                                        $customer_email = $email;
                                        $customer_phone = $phone;
                                        $customer_note = $notes;
                                        $custom_fields_text = '';
                                        $custom_fields_html = '';

                                        foreach ($custom_text as $custom) {
                                            $wpdb->insert($table_current_booking_fields, array('booking_id' => $customer_booking_id, 'key_field' => base64_decode($custom['name']), 'field_val' => $custom['value'], 'status' => 'valid'));

                                            if ( $custom['value'] != '' ) {
                                                $custom_fields_text .= sprintf(
                                                    "%s: %s\n",
                                                    base64_decode($custom['name']), $custom['value']
                                                );

                                                $custom_fields_html .= sprintf(
                                                    '<tr valign=top><td>%s:&nbsp;</td><td>%s</td></tr>',
                                                    base64_decode($custom['name']), $custom['value']
                                                );
                                            }
                                        }
                                        foreach ($custom_textarea as $custom) {

                                            $wpdb->insert($table_current_booking_fields, array('booking_id' => $customer_booking_id, 'key_field' => base64_decode($custom['name']), 'field_val' => $custom['value'], 'status' => 'valid'));

                                            if ( $custom['value'] != '' ) {
                                                $custom_fields_text .= sprintf(
                                                    "%s: %s\n",
                                                    base64_decode($custom['name']), $custom['value']
                                                );

                                                $custom_fields_html .= sprintf(
                                                    '<tr valign=top><td>%s:&nbsp;</td><td>%s</td></tr>',
                                                    base64_decode($custom['name']), $custom['value']
                                                );
                                            }
                                        }
                                        foreach ($custom_content as $custom) {

                                            $wpdb->insert($table_current_booking_fields, array('booking_id' => $customer_booking_id, 'key_field' => base64_decode($custom['name']), 'field_val' => $custom['value'], 'status' => 'valid'));

                                            if ( $custom['value'] != '' ) {
                                                $custom_fields_text .= sprintf(
                                                    "%s: %s\n",
                                                    base64_decode($custom['name']), $custom['value']
                                                );

                                                $custom_fields_html .= sprintf(
                                                    '<tr valign=top><td>%s:&nbsp;</td><td>%s</td></tr>',
                                                    base64_decode($custom['name']), $custom['value']
                                                );
                                            }
                                        }
                                        foreach ($custom_select as $custom) {

                                            $wpdb->insert($table_current_booking_fields, array('booking_id' => $customer_booking_id, 'key_field' => base64_decode($custom['name']), 'field_val' => $custom['value'], 'status' => 'valid'));

                                            if ( $custom['value'] != '' ) {
                                                $custom_fields_text .= sprintf(
                                                    "%s: %s\n",
                                                    base64_decode($custom['name']), $custom['value']
                                                );

                                                $custom_fields_html .= sprintf(
                                                    '<tr valign=top><td>%s:&nbsp;</td><td>%s</td></tr>',
                                                    base64_decode($custom['name']), $custom['value']
                                                );
                                            }
                                        }
                                        foreach ($custom_radio as $custom) {

                                            $wpdb->insert($table_current_booking_fields, array('booking_id' => $customer_booking_id, 'key_field' => base64_decode($custom['name']), 'field_val' => $custom['value'], 'status' => 'valid'));

                                            if ( $custom['value'] != '' ) {
                                                $custom_fields_text .= sprintf(
                                                    "%s: %s\n",
                                                    base64_decode($custom['name']), $custom['value']
                                                );

                                                $custom_fields_html .= sprintf(
                                                    '<tr valign=top><td>%s:&nbsp;</td><td>%s</td></tr>',
                                                    base64_decode($custom['name']), $custom['value']
                                                );
                                            }
                                        }
                                        foreach ($custom_checkbox as $custom) {

                                            $wpdb->insert($table_current_booking_fields, array('booking_id' => $customer_booking_id, 'key_field' => base64_decode($custom['name']), 'field_val' => implode(',', $custom['value']), 'status' => 'valid'));

                                            if ( $custom['value'] != '' ) {
                                                $custom_fields_text .= sprintf(
                                                    "%s: %s\n",
                                                    base64_decode($custom['name']), implode(',', $custom['value'])
                                                );

                                                $custom_fields_html .= sprintf(
                                                    '<tr valign=top><td>%s:&nbsp;</td><td>%s</td></tr>',
                                                    base64_decode($custom['name']), implode(',', $custom['value'])
                                                );
                                            }
                                        }
                                        if ( $custom_fields_html != '' ) {
                                            $custom_fields_html = "<table cellspacing=0 cellpadding=0 border=0>$custom_fields_html</table>";
                                        }

                                        $headers = array('From: ' . bookme_get_settings('bookme_email_sender_name', get_bloginfo('name')) . ' <' . bookme_get_settings('bookme_email_sender_email', get_bloginfo('admin_email')) . '>');

                                        if (bookme_get_settings('email_customer', 'true') == 'true') {
                                            $sqlblbc = "SELECT * FROM $table_enotification where email_key='customer_sub' and key_type='customer_confirm'";
                                            $reslabl = $wpdb->get_results($sqlblbc);
                                            $subject = $reslabl[0]->email_value;
                                            $sqlblbc = "SELECT * FROM $table_enotification where email_key='customer_msg' and key_type='customer_confirm'";
                                            $reslabl = $wpdb->get_results($sqlblbc);
                                            $message = $reslabl[0]->email_value;

                                            $message = str_replace("{booking_time}", $appointment_time, $message);
                                            $message = str_replace("{booking_end_time}", $appointment_end_time, $message);
                                            $message = str_replace("{booking_date}", $booking_date, $message);
                                            $message = str_replace("{customer_name}", $customer_name, $message);
                                            $message = str_replace("{customer_email}", $customer_email, $message);
                                            $message = str_replace("{customer_phone}", $customer_phone, $message);
                                            $message = str_replace("{customer_note}", $customer_note, $message);
                                            $message = str_replace("{number_of_persons}", $ttl_person, $message);
                                            $message = str_replace("{company_name}", $company_name, $message);
                                            $message = str_replace("{company_address}", $company_address, $message);
                                            $message = str_replace("{company_phone}", $company_phone, $message);
                                            $message = str_replace("{company_website}", $company_website, $message);
                                            $message = str_replace("{employee_name}", $employee_name, $message);
                                            $message = str_replace("{category_name}", $category_name, $message);
                                            $message = str_replace("{service_name}", $service_name, $message);
                                            $message = str_replace("{custom_field}", $custom_fields_text, $message);
                                            $message = str_replace("{custom_field_2col}", $custom_fields_html, $message);

                                            wp_mail($customer_email, $subject, $message, $headers);
                                        }

                                        if (bookme_get_settings('email_employee', 'true') == 'true') {
                                            $sqlblbc = "SELECT * FROM $table_enotification where email_key='employee_sub' and key_type='employee_confirm'";
                                            $reslabl = $wpdb->get_results($sqlblbc);
                                            $subject = $reslabl[0]->email_value;
                                            $sqlblbc = "SELECT * FROM $table_enotification where email_key='employee_msg' and key_type='employee_confirm'";
                                            $reslabl = $wpdb->get_results($sqlblbc);
                                            $message = $reslabl[0]->email_value;

                                            $message = str_replace("{booking_time}", $appointment_time, $message);
                                            $message = str_replace("{booking_end_time}", $appointment_end_time, $message);
                                            $message = str_replace("{booking_date}", $booking_date, $message);
                                            $message = str_replace("{customer_name}", $customer_name, $message);
                                            $message = str_replace("{customer_email}", $customer_email, $message);
                                            $message = str_replace("{customer_phone}", $customer_phone, $message);
                                            $message = str_replace("{customer_note}", $customer_note, $message);
                                            $message = str_replace("{number_of_persons}", $ttl_person, $message);
                                            $message = str_replace("{company_name}", $company_name, $message);
                                            $message = str_replace("{company_address}", $company_address, $message);
                                            $message = str_replace("{company_phone}", $company_phone, $message);
                                            $message = str_replace("{company_website}", $company_website, $message);
                                            $message = str_replace("{employee_name}", $employee_name, $message);
                                            $message = str_replace("{category_name}", $category_name, $message);
                                            $message = str_replace("{service_name}", $service_name, $message);
                                            $message = str_replace("{custom_field}", $custom_fields_text, $message);
                                            $message = str_replace("{custom_field_2col}", $custom_fields_html, $message);

                                            wp_mail($employee_email, $subject, $message, $headers);
                                        }

                                        if (bookme_get_settings('email_admin', 'true') == 'true') {
                                            $sqlblbc = "SELECT * FROM $table_enotification where email_key='admin_sub' and key_type='admin_confirm'";
                                            $reslabl = $wpdb->get_results($sqlblbc);
                                            $subject = $reslabl[0]->email_value;
                                            $sqlblbc = "SELECT * FROM $table_enotification where email_key='admin_msg' and key_type='admin_confirm'";
                                            $reslabl = $wpdb->get_results($sqlblbc);
                                            $message = $reslabl[0]->email_value;

                                            $message = str_replace("{booking_time}", $appointment_time, $message);
                                            $message = str_replace("{booking_end_time}", $appointment_end_time, $message);
                                            $message = str_replace("{booking_date}", $booking_date, $message);
                                            $message = str_replace("{customer_name}", $customer_name, $message);
                                            $message = str_replace("{customer_email}", $customer_email, $message);
                                            $message = str_replace("{customer_phone}", $customer_phone, $message);
                                            $message = str_replace("{customer_note}", $customer_note, $message);
                                            $message = str_replace("{number_of_persons}", $ttl_person, $message);
                                            $message = str_replace("{company_name}", $company_name, $message);
                                            $message = str_replace("{company_address}", $company_address, $message);
                                            $message = str_replace("{company_phone}", $company_phone, $message);
                                            $message = str_replace("{company_website}", $company_website, $message);
                                            $message = str_replace("{employee_name}", $employee_name, $message);
                                            $message = str_replace("{category_name}", $category_name, $message);
                                            $message = str_replace("{service_name}", $service_name, $message);
                                            $message = str_replace("{custom_field}", $custom_fields_text, $message);
                                            $message = str_replace("{custom_field_2col}", $custom_fields_html, $message);

                                            wp_mail(get_bloginfo('admin_email'), $subject, $message, $headers);
                                        }

                                        remove_filter('wp_mail_content_type', 'wpdocs_set_html_mail_content_type');

                                        if (bookme_get_settings('sms_customer', 'true') == 'true') {
                                            $sqlblbc = "SELECT * FROM $table_sms_notification where sms_key='customer_msg' and key_type='customer_confirm'";
                                            $reslabl = $wpdb->get_results($sqlblbc);
                                            $message = $reslabl[0]->sms_value;

                                            $message = str_replace("{booking_time}", $appointment_time, $message);
                                            $message = str_replace("{booking_end_time}", $appointment_end_time, $message);
                                            $message = str_replace("{booking_date}", $booking_date, $message);
                                            $message = str_replace("{customer_name}", $customer_name, $message);
                                            $message = str_replace("{customer_email}", $customer_email, $message);
                                            $message = str_replace("{customer_phone}", $customer_phone, $message);
                                            $message = str_replace("{customer_note}", $customer_note, $message);
                                            $message = str_replace("{number_of_persons}", $ttl_person, $message);
                                            $message = str_replace("{company_name}", $company_name, $message);
                                            $message = str_replace("{company_address}", $company_address, $message);
                                            $message = str_replace("{company_phone}", $company_phone, $message);
                                            $message = str_replace("{company_website}", $company_website, $message);
                                            $message = str_replace("{employee_name}", $employee_name, $message);
                                            $message = str_replace("{category_name}", $category_name, $message);
                                            $message = str_replace("{service_name}", $service_name, $message);
                                            $message = str_replace("{custom_field}", $custom_fields_text, $message);
                                            $message = str_replace("{custom_field_2col}", $custom_fields_text, $message);

                                            bookme_send_sms_twilio($customer_phone, $message);
                                        }

                                        if (bookme_get_settings('sms_employee', 'true') == 'true') {
                                            $sqlblbc = "SELECT * FROM $table_sms_notification where sms_key='employee_msg' and key_type='employee_confirm'";
                                            $reslabl = $wpdb->get_results($sqlblbc);
                                            $message = $reslabl[0]->sms_value;

                                            $message = str_replace("{booking_time}", $appointment_time, $message);
                                            $message = str_replace("{booking_end_time}", $appointment_end_time, $message);
                                            $message = str_replace("{booking_date}", $booking_date, $message);
                                            $message = str_replace("{customer_name}", $customer_name, $message);
                                            $message = str_replace("{customer_email}", $customer_email, $message);
                                            $message = str_replace("{customer_phone}", $customer_phone, $message);
                                            $message = str_replace("{customer_note}", $customer_note, $message);
                                            $message = str_replace("{number_of_persons}", $ttl_person, $message);
                                            $message = str_replace("{company_name}", $company_name, $message);
                                            $message = str_replace("{company_address}", $company_address, $message);
                                            $message = str_replace("{company_phone}", $company_phone, $message);
                                            $message = str_replace("{company_website}", $company_website, $message);
                                            $message = str_replace("{employee_name}", $employee_name, $message);
                                            $message = str_replace("{category_name}", $category_name, $message);
                                            $message = str_replace("{service_name}", $service_name, $message);
                                            $message = str_replace("{custom_field}", $custom_fields_text, $message);
                                            $message = str_replace("{custom_field_2col}", $custom_fields_text, $message);

                                            bookme_send_sms_twilio($employee_phone, $message);
                                        }

                                        if (bookme_get_settings('sms_admin', 'true') == 'true') {
                                            $admin_phone = bookme_get_settings('bookme_admin_phone_no');
                                            if($admin_phone) {
                                                $sqlblbc = "SELECT * FROM $table_sms_notification where sms_key='admin_msg' and key_type='admin_confirm'";
                                                $reslabl = $wpdb->get_results($sqlblbc);
                                                $message = $reslabl[0]->sms_value;

                                                $message = str_replace("{booking_time}", $appointment_time, $message);
                                                $message = str_replace("{booking_end_time}", $appointment_end_time, $message);
                                                $message = str_replace("{booking_date}", $booking_date, $message);
                                                $message = str_replace("{customer_name}", $customer_name, $message);
                                                $message = str_replace("{customer_email}", $customer_email, $message);
                                                $message = str_replace("{customer_phone}", $customer_phone, $message);
                                                $message = str_replace("{customer_note}", $customer_note, $message);
                                                $message = str_replace("{number_of_persons}", $ttl_person, $message);
                                                $message = str_replace("{company_name}", $company_name, $message);
                                                $message = str_replace("{company_address}", $company_address, $message);
                                                $message = str_replace("{company_phone}", $company_phone, $message);
                                                $message = str_replace("{company_website}", $company_website, $message);
                                                $message = str_replace("{employee_name}", $employee_name, $message);
                                                $message = str_replace("{category_name}", $category_name, $message);
                                                $message = str_replace("{service_name}", $service_name, $message);
                                                $message = str_replace("{custom_field}", $custom_fields_text, $message);
                                                $message = str_replace("{custom_field_2col}", $custom_fields_text, $message);

                                                bookme_send_sms_twilio($admin_phone, $message);
                                            }
                                        }


                                        /* Google Calendar integration */
                                        if (bookme_get_settings('bookme_gc_client_id') != null) {
                                            if($google_data) {
                                                include_once plugin_dir_path(__FILE__) . '/../../google.php';
                                                $bookme_gc_client = new Google_Client();
                                                $bookme_gc_client->setClientId(bookme_get_settings('bookme_gc_client_id'));
                                                $bookme_gc_client->setClientSecret(bookme_get_settings('bookme_gc_client_secret'));

                                                try {
                                                    $bookme_gc_client->setAccessToken($google_data);
                                                    if ($bookme_gc_client->isAccessTokenExpired()) {
                                                        $bookme_gc_client->refreshToken($bookme_gc_client->getRefreshToken());
                                                        $wpdb->update($table_all_employee, array('google_data' => $bookme_gc_client->getAccessToken()), array('id' => $employee), array('%s'), array('%d'));
                                                    }
                                                    $bookme_gc_service = new Google_Service_Calendar($bookme_gc_client);
                                                    $google_calendar_id = 'primary';
                                                    $gc_calendar = $bookme_gc_service->calendarList->get( $google_calendar_id );
                                                    $gc_access = $gc_calendar->getAccessRole();
                                                    if ( in_array( $gc_access, array( 'writer', 'owner' ) ) ) {
                                                        if($gc_event_id == null) {
                                                            $event_data = array(
                                                                'start' => $dates.' '.$appointstart,
                                                                'end' => $dates.' '.$appointend,
                                                                'name' => array($name),
                                                                'email' => array($email),
                                                                'phone' => array($phone),
                                                                'service' => $service_name,
                                                                'category' => $category_name,
                                                                'employee' => $employee_name,
                                                                'service_id' => $service,
                                                                'customer_id' => array($customer_id),
                                                                'booking_id' => $booking_id
                                                            );
                                                            $gc_event = bookme_get_event_data($event_data);

                                                            $createdEvent = $bookme_gc_service->events->insert($google_calendar_id, $gc_event);

                                                            $event_id = $createdEvent->getId();
                                                            if ($event_id) {
                                                                $wpdb->update($table_current_booking, array('google_event_id' => $event_id), array('id' => $booking_id), array('%s'), array('%d'));
                                                            }
                                                        }else{
                                                            $customer_result = $wpdb->get_results("select customer_id from $table_customer_booking where booking_id = $booking_id");
                                                            $customer_ids = array();
                                                            $name = array();
                                                            $email = array();
                                                            $phone = array();
                                                            foreach($customer_result as $customer_id){
                                                                $customer_ids[] = $customer_id->customer_id;
                                                                $customer_data = $wpdb->get_results("select name,email,phone from $table_customers where id = $customer_id->customer_id");
                                                                $name[] = $customer_data[0]->name;
                                                                $email[] = $customer_data[0]->email;
                                                                $phone[] = $customer_data[0]->phone;
                                                            }

                                                            $event_data = array(
                                                                'start' => $dates.' '.$appointstart,
                                                                'end' => $dates.' '.$appointend,
                                                                'name' => $name,
                                                                'email' => $email,
                                                                'phone' => $phone,
                                                                'service' => $service_name,
                                                                'category' => $category_name,
                                                                'employee' => $employee_name,
                                                                'service_id' => $service,
                                                                'customer_id' => $customer_ids,
                                                                'booking_id' => $booking_id
                                                            );
                                                            $gc_event = bookme_get_event_data($event_data);
                                                            $bookme_gc_service->events->update( $google_calendar_id, $gc_event_id, $gc_event );
                                                        }
                                                    }
                                                } catch (Exception $e) {

                                                }
                                            }
                                        }

                                        unset($_SESSION['bookme']);
                                        $remove_parameters = array('bookme_action', 'access_token', 'error_msg', 'token', 'PayerID', 'type');
                                        header('Location: ' . remove_query_arg($remove_parameters, bookme_getCurrentPageURL()) . '?status=success');
                                        exit;
                                    } else {
                                        $error_message = $response['L_LONGMESSAGE0'];
                                    }
                                } else {
                                    $error_message = $response['L_LONGMESSAGE0'];
                                }
                            } else {
                                $error_message = __('Something went wrong, please try again.', 'bookme');
                            }
                        } else {
                            $error_message = __('Seats not available for this time period.', 'bookme');
                        }
                    }
                }
            }
        }
    } else {
        $error_message = __('Invalid token provided', 'bookme');
    }

    if (!empty($error_message)) {
        header('Location: ' . add_query_arg(array(
                'bookme_action' => 'error',
                'error_msg' => urlencode($error_message),
            ), bookme_getCurrentPageURL()
            ));
        exit;
    }
}