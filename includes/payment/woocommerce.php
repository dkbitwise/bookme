<?php 

if ( bookme_get_settings( 'enable_woocommerce' ) ) {

    add_action( 'woocommerce_add_order_item_meta', 'bookme_addOrderItemMeta' , 10, 3 );
    add_action( 'woocommerce_after_order_itemmeta', 'bookme_orderItemMeta' , 10, 1 );
//    add_action( 'woocommerce_before_calculate_totals', 'bookme_beforeCalculateTotals' , 10, 1 );
    add_action( 'woocommerce_before_cart_contents', 'bookme_checkAvailableTimeForCart', 10, 0 );
    add_action( 'woocommerce_order_item_meta_end', 'bookme_orderItemMeta', 10, 1 );
    add_action( 'woocommerce_order_status_cancelled', 'bookme_cancelOrder', 10, 1 );
    add_action( 'woocommerce_order_status_completed', 'bookme_paymentComplete', 10, 1 );
    add_action( 'woocommerce_order_status_on-hold', 'bookme_paymentComplete', 10, 1 );
    add_action( 'woocommerce_order_status_processing', 'bookme_paymentComplete', 10, 1 );
    add_action( 'woocommerce_order_status_refunded', 'bookme_cancelOrder', 10, 1 );

    add_filter( 'woocommerce_checkout_get_value', 'bookme_checkoutValue', 10, 2 );
    add_filter( 'woocommerce_get_item_data', 'bookme_getItemData', 10, 2 );
    add_filter( 'woocommerce_quantity_input_args', 'bookme_quantityArgs', 10, 2 );
    //add_filter( 'woocommerce_cart_item_price', 'bookme_getCartItemPrice', 10, 3 );
}


function bookme_checkAvailableTimeForCart()
{
    $recalculate_totals = false;
    foreach ( WC()->cart->get_cart() as $wc_key => $wc_item ) {
        if ( array_key_exists( 'bookme', $wc_item ) ) {
            
            $appointstart = $wc_item['bookme']['appointstart'];
		    $appointend = $wc_item['bookme']['appointend'];
		    $category = $wc_item['bookme']['category'];
		    $service = $wc_item['bookme']['service'];
		    $employee = $wc_item['bookme']['employee'];
		    $dates = $wc_item['bookme']['dates'];
		    $person = $wc_item['bookme']['person'];
            $month_date=$wc_item['bookme']['month_date'];

		    $name = $wc_item['bookme']['name'];
		    $email = $wc_item['bookme']['email'];
		    $phone = $wc_item['bookme']['phone'];
		    $notes = $wc_item['bookme']['notes'];
		    $price = $wc_item['bookme']['price'];


		    global $wpdb;
		    $table_coupans = $wpdb->prefix . 'bookme_coupons';
		    $table_settings = $wpdb->prefix . 'bookme_settings';
		    $table_all_employee = $wpdb->prefix . 'bookme_employee';
		    $table = $wpdb->prefix . 'bookme_category';
		    $table_enotification = $wpdb->prefix . 'bookme_email_notification';
		    $table_current_booking_fields = $wpdb->prefix . 'bookme_current_booking_fields';
		    $table_current_booking = $wpdb->prefix . 'bookme_current_booking';
		    $table_book_service = $wpdb->prefix . 'bookme_service';
		    $table_customer_booking = $wpdb->prefix . 'bookme_customer_booking';
		    $table_customers = $wpdb->prefix . 'bookme_customers';
		    $table_payments = $wpdb->prefix . 'bookme_payments';
		    $table_holidays = $wpdb->prefix . 'bookme_holidays';

            if ( $wc_item['quantity'] > 1 ) {
                $person *= 1;
            }
            // Check if appointment's time is still available
            $booking_date = date("Y-m-d", strtotime($dates));

		    $hodiday = 0;

			
		    $result = $wpdb->get_results("SELECT * FROM $table_holidays WHERE staff_id = $employee");
		    foreach ($result as $holiday) {
		        if ($holiday->holi_date == $booking_date){
		            $hodiday = 1;
		        }
		    }

		    if ($hodiday == 0) {
                /* TODO:ANAND-time_conversion convert student timezone to UTC for check is record already exist  */
                $utc_date_time=get_utc_timezone($dates.' '.$appointstart);
                $utc_dates=$utc_date_time->format('Y-m-d');
                $utc_appointstart=$utc_date_time->format('g:i a');

		    	$rowcount = $wpdb->get_var("SELECT COUNT(cb.id) FROM $table_customer_booking cb LEFT JOIN $table_current_booking b ON cb.booking_id = b.id LEFT JOIN $table_customers c ON cb.customer_id = c.id WHERE b.cat_id='" . $category . "' and b.ser_id='" . $service . "' and b.emp_id='" . $employee . "' and b.date='" . $utc_dates . "' and b.time = '" . $utc_appointstart . "' and c.email='" . $email . "'");
		        if ($rowcount >= 1) {
		            $notice = __( 'You have already booked this course for dates '.implode(',',$month_date), 'bookme' );
		            wc_print_notice( $notice, 'notice' );
	                WC()->cart->set_quantity( $wc_key, 0, false );
	                $recalculate_totals = true;
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
						$notice = __('Seats not available. Please try again!', 'bookme');
						wc_print_notice( $notice, 'notice' );
		                WC()->cart->set_quantity( $wc_key, 0, false );
		                $recalculate_totals = true;
		            }
		        }
		    } else {
		    	$notice = __( 'Sorry, this service is not availabe for that day. Please book another date.', 'bookme' );
		    	wc_print_notice( $notice, 'notice' );
                WC()->cart->set_quantity( $wc_key, 0, false );
                $recalculate_totals = true;
		    }
        }
    }
    if ( $recalculate_totals ) {
        WC()->cart->calculate_totals();
    }
}

function bookme_checkoutValue( $null, $field_name )
{
    $checkout_info = array();
    foreach ( WC()->cart->get_cart() as $wc_key => $wc_item ) {
        if ( array_key_exists( 'bookme', $wc_item ) ) {
            $name = $wc_item['bookme']['name'];
		    $email = $wc_item['bookme']['email'];
		    $phone = $wc_item['bookme']['phone'];
		    $first_name = strtok($name, ' ');
		    $last_name = strtok(' ');

            $checkout_info = array(
                'billing_first_name' => $first_name,
                'billing_last_name'  => $last_name,
                'billing_email'      => $email,
                'billing_phone'      => $phone
            );
            break;
        }
    }
    if ( array_key_exists( $field_name, $checkout_info ) ) {
        return $checkout_info[ $field_name ];
    }
    return $null;
}

function bookme_paymentComplete( $order_id )
{
    $order = new \WC_Order( $order_id );
    foreach ( $order->get_items() as $item_id => $wc_item ) {
        $data = wc_get_order_item_meta( $item_id, 'bookme' );
        if ( $data && ! isset ( $data['processed'] ) ) {

            $code = $data['code'];
		    $dic_price = $data['dic_price'];
		    $appointstart = $data['appointstart'];
		    $appointend = $data['appointend'];
		    $category = $data['category'];
		    $service = $data['service'];
		    $employee = $data['employee'];
		    $dates = $data['dates'];
            $month_date = $data['month_date'];
		    $person = $data['person'];
		    $student = $data['student'];

		    $name = $data['name'];
		    $email = $data['email'];
		    $phone = $data['phone'];
			if($phone == NULL || $phone == '')
			{
				//$phone = "9944516004";
				$phone = get_user_meta(get_current_user_id(),'parent_phone',true);
			}
		    $notes = $data['notes'];
		    if($notes == NULL || $notes == '')
			{
				$notes = "NA";
			}
		    $price = $data['price'];

		    $custom_text = $data['custom_text'];
		    $custom_textarea = $data['custom_textarea'];
		    $custom_content = $data['custom_content'];
		    $custom_checkbox = $data['custom_checkbox'];
		    $custom_radio = $data['custom_radio'];
		    $custom_select = $data['custom_select'];

			add_filter('wp_mail_content_type', 'bookme_set_html_mail_content_type');

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
		    $table_customer_booking_ref = $wpdb->prefix . 'bookme_customer_booking_ref';

            if ( $wc_item['qty'] > 1 ) {
                $person *= 1;
            }
            $customer_id=$wpdb->get_var( $wpdb->prepare('select id from '.$table_customers.' where email=%s ',$email) );
            if( $customer_id==null ){
                $wpdb->insert($table_customers, array(
                    'name' => $name,
                    'email' => $email,
                    'phone' => $phone,
                    'notes' => $notes
                ));
                $customer_id = $wpdb->insert_id;
            }
			if ($customer_id) {

                $resultSs = $wpdb->get_results("SELECT duration,name FROM $table_book_service WHERE id=$service and catId=$category");
                $duration = $resultSs[0]->duration;
                $servname = $resultSs[0]->name;

                $resultE = $wpdb->get_results("SELECT name,email,google_data,phone FROM $table_all_employee WHERE id=" . $employee);
                $employee_name = $resultE[0]->name;
                $employee_email = $resultE[0]->email;
                $employee_phone = $resultE[0]->phone;
                $google_data = $resultE[0]->google_data;

                $wpdb->insert($table_payments, array(
                    'created' => current_time( 'mysql' ),
                    'type' => 'WooCommerce',
                    'price' => $price/count($month_date),
                   // 'discount_price' => $dic_price/count($month_date),
                    'status' => 'Completed'
                ));
                $payment_id = $wpdb->insert_id;

                $booking_id_list=array();
                foreach ($month_date as $m_date){
                    $m_date=date('Y-m-d',strtotime($m_date));

                    /* TODO:ANAND-time_conversion  convert student timezone to UTC for getting booking details  */
                    $utc_date_time=get_utc_timezone($m_date.' '.$appointstart);
                    $utc_m_date=$utc_date_time->format('Y-m-d');
                    $utc_appointstart=$utc_date_time->format('g:i a');

                    $booking_result = $wpdb->get_results("select id,google_event_id from $table_current_booking where ser_id = $service and emp_id = $employee and date = '$utc_m_date' and time = '$utc_appointstart'");

                    $gc_event_id = null;
                    if ($wpdb->num_rows > 0) {
                        $booking_id = $booking_result[0]->id;
                        $gc_event_id = $booking_result[0]->google_event_id;
                    }else{
                        /* TODO:ANAND-time_conversion  convert student timezone to UTC for saving booking details  */
                        $utc_date_time=get_utc_timezone($m_date.' '.$appointstart);
                        $utc_m_date=$utc_date_time->format('Y-m-d');
                        $utc_appointstart=$utc_date_time->format('g:i a');

                        $wpdb->insert($table_current_booking, array('cat_id' => $category, 'ser_id' => $service, 'emp_id' => $employee, 'date' => $utc_m_date, 'time' => $utc_appointstart, 'duration' => $duration));

                        $booking_id = $wpdb->insert_id;
                    }

                    $wpdb->insert($table_customer_booking, array(
                        'customer_id' => $customer_id,
                        'booking_id' => $booking_id,
                        'payment_id' => $payment_id,
                        'no_of_person' => $person,
                        'status' => 'Approved'
                    ));
                    $customer_booking_id = $wpdb->insert_id;
                    
                    $wpdb->insert($table_customer_booking_ref, array(
                        'customer_id' => $customer_id,
                        'student_id' => $student,
                        'booking_id' => $booking_id,
                        'cus_booking_id' => $customer_booking_id
                    ));
                    
                    $booking_id_list[]=$booking_id;
                }

                $limit = 0;
                if (isset($code) && $code != '') {
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

                $results = $wpdb->get_results("SELECT name FROM $table where id=" . $category );
                $category_name = $results[0]->name;
                $service_name = $servname;

                //$booking_date = date_i18n(get_option('date_format'), strtotime($dates));
                $booking_date = date_i18n('F j, Y', strtotime($dates));
                $booking_day = date_i18n('D', strtotime($dates));
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


                $headers = array('From: '.bookme_get_settings('bookme_email_sender_name', get_bloginfo('name')).' <'.bookme_get_settings('bookme_email_sender_email', get_bloginfo('admin_email')).'>');

                if(bookme_get_settings('email_customer', 'true') == 'true'){
                    $sqlblbc = "SELECT * FROM $table_enotification where email_key='customer_sub' and key_type='customer_confirm'";
                    $reslabl = $wpdb->get_results($sqlblbc);
                    $subject = $reslabl[0]->email_value;
                    $sqlblbc = "SELECT * FROM $table_enotification where email_key='customer_msg' and key_type='customer_confirm'";
                    $reslabl = $wpdb->get_results($sqlblbc);
                    $message = $reslabl[0]->email_value;
                    $message_stu = $reslabl[0]->email_value;

                    $m_testes=$month_date;
		    $ij=0;
                    $totalmonth=0;
		    $newdatenew = "";
		    foreach ($wc_item['bookme']['month_date'] as $m_date) {
		    if(isset($wc_item['bookme']['month_date'][$ij]))
		    {
	
				if($ij==0){
						$newdatenew.= "<br><span><b>" .date('M', strtotime($wc_item['bookme']['month_date'][0])). " " .date('Y', strtotime($wc_item['bookme']['month_date'][0])). "</b>";
				}
						
				if($ij!=0){
							
						$newdate=date('M', strtotime($wc_item['bookme']['month_date'][$ij]));
						$olddate=date('M', strtotime($wc_item['bookme']['month_date'][$ij-1]));
					
						if($newdate!=$olddate){
						 
							$newdatenew.=  "<b><span>" .$newdate. " " .date('Y', strtotime($wc_item['bookme']['month_date'][$ij])). "</span></b>";
						
						}
				}

				if($ij==0)
				{
					$newdatenew.= " (" .date('d', strtotime($wc_item['bookme']['month_date'][$ij])). ",";
				}
				else
				{
					$newdate=date('M', strtotime($wc_item['bookme']['month_date'][$ij]));
					$olddate=date('M', strtotime($wc_item['bookme']['month_date'][$ij-1]));

					if($newdate!=$olddate){
						$newdatenew.= " (" .date('d', strtotime($wc_item['bookme']['month_date'][$ij])). ",";
					}
					else
					{
						$newdatenew.= "" .date('d', strtotime($wc_item['bookme']['month_date'][$ij])). ",";
					}
				}

				++$ij;

				if($ij!=0){
				
					$newdate=date('M', strtotime($wc_item['bookme']['month_date'][$ij]));
					$olddate=date('M', strtotime($wc_item['bookme']['month_date'][$ij-1]));
					
					if($newdate!=$olddate){

						$newdatenew.= " )</span><br>";
					
					}
				
				}
		    }

			}

					//$newd = "";
					$newd = str_replace(", )",")",$newdatenew);
					
					$table_stu = $wpdb->prefix . 'bwlive_students';
					$sql_stu = "SELECT student_email,student_fname,student_lname FROM $table_stu where student_id='".$student."'";
					$results_stu = $wpdb->get_results($sql_stu);
					$student_email = $results_stu[0]->student_email;
					$student_fname = $results_stu[0]->student_fname;
					$student_lname = $results_stu[0]->student_lname;
					$fullname = $student_fname." ".$student_lname;

                    $message = str_replace("{booking_time}", $appointment_time, $message);
                    $message = str_replace("{booking_end_time}", $appointment_end_time." (".$booking_day.")", $message);
                    $message = str_replace("{booking_date}", $newd, $message);
                    $message = str_replace("{number_of_persons}", $ttl_person, $message);
                    $message = str_replace("{customer_name}", $customer_name, $message);
                    $message = str_replace("{customer_email}", $customer_email, $message);
                    $message = str_replace("{customer_phone}", $customer_phone, $message);
                    $message = str_replace("{customer_note}", $customer_note, $message);
                    $message = str_replace("{company_name}", $company_name, $message);
                    $message = str_replace("{company_address}", $company_address, $message);
                    $message = str_replace("{company_phone}", $company_phone, $message);
                    $message = str_replace("{company_website}", $company_website, $message);
                    $message = str_replace("{employee_name}", $employee_name."<br>Student Name: ".$fullname, $message);
                    $message = str_replace("{category_name}", $category_name, $message);
                    $message = str_replace("{service_name}", $service_name, $message);
                    $message = str_replace("{custom_field}", $custom_fields_text, $message);
                    $message = str_replace("{custom_field_2col}", $custom_fields_html, $message);

                    wdm_mail_new($customer_email, $subject, $message, $headers);
                    
                    $message_stu = str_replace("{booking_time}", $appointment_time, $message_stu);
                    $message_stu = str_replace("{booking_end_time}", $appointment_end_time." (".$booking_day.")", $message_stu);
                    $message_stu = str_replace("{booking_date}", $newd, $message_stu);
                    $message_stu = str_replace("{number_of_persons}", $ttl_person, $message_stu);
                    $message_stu = str_replace("{customer_name}", $fullname, $message_stu);
                    $message_stu = str_replace("{customer_email}", $customer_email, $message_stu);
                    $message_stu = str_replace("{customer_phone}", $customer_phone, $message_stu);
                    $message_stu = str_replace("{customer_note}", $customer_note, $message_stu);
                    $message_stu = str_replace("{company_name}", $company_name, $message_stu);
                    $message_stu = str_replace("{company_address}", $company_address, $message_stu);
                    $message_stu = str_replace("{company_phone}", $company_phone, $message_stu);
                    $message_stu = str_replace("{company_website}", $company_website, $message_stu);
                    $message_stu = str_replace("{employee_name}", $employee_name, $message_stu);
                    $message_stu = str_replace("{category_name}", $category_name, $message_stu);
                    $message_stu = str_replace("{service_name}", $service_name, $message_stu);
                    $message_stu = str_replace("{custom_field}", $custom_fields_text, $message_stu);
                    $message_stu = str_replace("{custom_field_2col}", $custom_fields_html, $message_stu);

                    wdm_mail_new($student_email, $subject, $message_stu, $headers);
                }

                if(bookme_get_settings('email_employee', 'true') == 'true'){
                    $sqlblbc = "SELECT * FROM $table_enotification where email_key='employee_sub' and key_type='employee_confirm'";
                    $reslabl = $wpdb->get_results($sqlblbc);
                    $subject = $reslabl[0]->email_value;
                    $sqlblbc = "SELECT * FROM $table_enotification where email_key='employee_msg' and key_type='employee_confirm'";
                    $reslabl = $wpdb->get_results($sqlblbc);
                    $message = $reslabl[0]->email_value;

                    $m_testes=$month_date;
		    $ij=0;
                    $totalmonth=0;
		    $newdatenew = "";
		    foreach ($wc_item['bookme']['month_date'] as $m_date) {
		    
		    if(isset($wc_item['bookme']['month_date'][$ij]))
		    {
				if($ij==0){
						$newdatenew.= "<br><span><b>" .date('M', strtotime($wc_item['bookme']['month_date'][0])). " " .date('Y', strtotime($wc_item['bookme']['month_date'][0])). "</b>";
				}
						
				if($ij!=0){
							
						$newdate=date('M', strtotime($wc_item['bookme']['month_date'][$ij]));
						$olddate=date('M', strtotime($wc_item['bookme']['month_date'][$ij-1]));
					
						if($newdate!=$olddate){
						 
							$newdatenew.=  "<b><span>" .$newdate. " " .date('Y', strtotime($wc_item['bookme']['month_date'][$ij-1])). "</span></b>";
						
						}
				}

				if($ij==0)
				{
					$newdatenew.= " (" .date('d', strtotime($wc_item['bookme']['month_date'][$ij])). ",";
				}
				else
				{
					$newdate=date('M', strtotime($wc_item['bookme']['month_date'][$ij]));
					$olddate=date('M', strtotime($wc_item['bookme']['month_date'][$ij-1]));

					if($newdate!=$olddate){
						$newdatenew.= " (" .date('d', strtotime($wc_item['bookme']['month_date'][$ij])). ",";
					}
					else
					{
						$newdatenew.= "" .date('d', strtotime($wc_item['bookme']['month_date'][$ij])). ",";
					}
				}

				++$ij;

				if($ij!=0){
				
					$newdate=date('M', strtotime($wc_item['bookme']['month_date'][$ij]));
					$olddate=date('M', strtotime($wc_item['bookme']['month_date'][$ij-1]));
					
					if($newdate!=$olddate){

						$newdatenew.= " )</span><br>";
					
					}
				
				}
		    }

			}
					//$newd = "";
					$newd = str_replace(", )",")",$newdatenew);
                    $message = str_replace("{booking_time}", $appointment_time, $message);
                    $message = str_replace("{booking_end_time}", $appointment_end_time, $message);
                    $message = str_replace("{booking_date}", $newd, $message);
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

                if(bookme_get_settings('email_admin', 'true') == 'true'){
                    $sqlblbc = "SELECT * FROM $table_enotification where email_key='admin_sub' and key_type='admin_confirm'";
                    $reslabl = $wpdb->get_results($sqlblbc);
                    $subject = $reslabl[0]->email_value;
                    $sqlblbc = "SELECT * FROM $table_enotification where email_key='admin_msg' and key_type='admin_confirm'";
                    $reslabl = $wpdb->get_results($sqlblbc);
                    $message = $reslabl[0]->email_value;
					$totalmonth=count($month_date);
					$m_testes=$month_date;
					//$m_testes=$wc_item['bookme']['month_date'];
					$ij=0;
					if(isset($wc_item)){
					foreach ($wc_item['bookme']['month_date'] as $m_date) {
						if($ij==0){
						$newdatenew.= "<span><b>" .date('M', strtotime($wc_item['bookme']['month_date'][0])). "-</b>";
						}
						
						$newdatenew.= "" .date('d', strtotime($wc_item['bookme']['month_date'][$ij])). "</span>-";
					
					   if($ij!=0){
							$newdate=date('M', strtotime($wc_item['bookme']['month_date'][$ij]));
							$olddate=date('M', strtotime($wc_item['bookme']['month_date'][$ij-1]));
					
					 if($newdate!=$olddate){
						 $newdatenew.=  "<b><span>" .date('Y', strtotime($wc_item['bookme']['month_date'][$ij-1])). "</span>-<br><span>" .$newdate. "</span>-</b>";
						
						}
					}
					++$ij;
					if($ij==$totalmonth){
						$newdatenew.=  "<b><span>" .date('Y', strtotime($wc_item['bookme']['month_date'][$totalmonth-1])). "</span></b>";
					}
					}
                }
					
                                                            
                    $message = str_replace("{booking_time}", $appointment_time, $message);
                    $message = str_replace("{booking_end_time}", $appointment_end_time, $message);
                    $message = str_replace("{booking_date}", $newdatenew, $message);
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
                    $message = str_replace("{booking_date}", $newd, $message);
                    //$message = str_replace("{booking_date}", implode(',',$month_date), $message);
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
                    $message = str_replace("{booking_date}", implode(',',$month_date), $message);
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
                        $message = str_replace("{booking_date}", implode(',',$month_date), $message);
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

                /* Google Calendar integration */
                if (bookme_get_settings('bookme_gc_client_id') != null) {
                    if($google_data) {
                        include_once plugin_dir_path(__FILE__) . '/../google.php';
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

                $data['processed'] = true;
                $data['booking_id'] = $booking_id_list;
            }
            wc_update_order_item_meta( $item_id, 'bookme', $data );
        }
    }
}

function bookme_cancelOrder( $order_id )
{
    global $wpdb;
    $order = new \WC_Order( $order_id );
    foreach ( $order->get_items() as $item_id => $order_item ) {
        $data = wc_get_order_item_meta( $item_id, 'bookme' );
        if ( isset ( $data['processed'], $data['booking_id'] ) && $data['processed'] ) {
            $table_current_booking = $wpdb->prefix . 'bookme_current_booking';
		    $table_customer_booking = $wpdb->prefix . 'bookme_customer_booking';
		    $table_payments = $wpdb->prefix . 'bookme_payments';
            foreach ($data['booking_id'] as $booking_id){
                $payment_id = $wpdb->get_results("Select payment_id from $table_customer_booking where booking_id=" . $booking_id);
                foreach ($payment_id as $p_id) {
                    $wpdb->delete($table_payments, array('id' => $p_id->payment_id), array('%d'));
                }
                $wpdb->delete($table_customer_booking, array('booking_id' => $booking_id), array('%d'));
                $wpdb->delete($table_current_booking, array('id' => $booking_id), array('%d'));
            }

	        unset($data['booking_id']);
	        unset($data['processed']);
	        wc_update_order_item_meta( $item_id, 'bookme', $data );
        }
    }
}

function bookme_quantityArgs( $args, $product )
{
	$woocommerce_product = bookme_get_settings('woocommerce_product', 0);
    if ( $product->get_id() == $woocommerce_product ) {
        $args['max_value'] = $args['input_value'];
        $args['min_value'] = $args['input_value'];
    }

    return $args;
}

function bookme_beforeCalculateTotals( $cart_object )
{
    foreach ( $cart_object->cart_contents as $wc_key => $wc_item ) {
        if ( isset ( $wc_item['bookme'] ) ) {
        	$price = $wc_item['bookme']['price'];
            $person = $wc_item['bookme']['person'];
            $month_date=count($wc_item['bookme']['month_date']);
            $wc_item['data']->set_price( $price/$month_date );
        }
    }
}

function bookme_addOrderItemMeta( $item_id, $values, $wc_key )
{
    if ( isset ( $values['bookme'] ) ) {
        wc_update_order_item_meta( $item_id, 'bookme', $values['bookme'] );
    }
}

function bookme_getItemData( $other_data, $wc_item )
{
    if ( isset ( $wc_item['bookme'] ) ) {
    	$times = date_i18n(get_option('time_format'), strtotime($wc_item['bookme']['appointstart']));
        $timee = date_i18n(get_option('time_format'), strtotime($wc_item['bookme']['appointend']));
        $appointment_time = $times. '-' . $timee;
        $date = date_i18n(get_option('date_format'), strtotime($wc_item['bookme']['dates']));
        $category = $wc_item['bookme']['category'];
        $person = $wc_item['bookme']['person'];
        $service = $wc_item['bookme']['service'];
        $price = wc_price($wc_item['bookme']['price'] * $person);
        $employee = $wc_item['bookme']['employee'];
        $cname = $wc_item['bookme']['name'];

        global $wpdb;
        $table_employee = $wpdb->prefix . 'bookme_employee';
        $table_category = $wpdb->prefix . 'bookme_category';
        $table_service = $wpdb->prefix . 'bookme_service';

        $results = $wpdb->get_results("SELECT name FROM $table_category where id=" . $category . "");
        $category = $results[0]->name;

        $results = $wpdb->get_results("SELECT name FROM $table_service where id=" . $service . "");
        $service = $results[0]->name;

        $resultE = $wpdb->get_results("SELECT name FROM $table_employee WHERE id=" . $employee . "");
        $employee = $resultE[0]->name;

        $data_text = "Employee: {employee_name}\nService: {service_name}\nDate: {booking_date}\nTime: {booking_time}";
        $message = bookme_get_settings('woocommerce_cart_data_text', $data_text);
			$totalmonth=count($wc_item['bookme']['month_date']);
					$m_testes=$wc_item['bookme']['month_date'];
					$ij=0;
					//Added by Vignesh R
					$newdatenew= "";
					foreach ($wc_item['bookme']['month_date'] as $m_date) {
						if($ij==0){
						$newdatenew.= "<b>" .date('M', strtotime($wc_item['bookme']['month_date'][0]))." ". "</b>";
						}
						
						
					
					   if($ij!=0){
							$newdate=date('M', strtotime($wc_item['bookme']['month_date'][$ij]));
							$olddate=date('M', strtotime($wc_item['bookme']['month_date'][$ij-1]));
					
					 if($newdate!=$olddate){
						 $newdatenew.=  "<b>" .date('Y', strtotime($wc_item['bookme']['month_date'][$ij-1])). " - " .$newdate." ". "</b>";
						
						}
					}
					$newdatenew.= "" .date('d', strtotime($wc_item['bookme']['month_date'][$ij])). ",";
					++$ij;
					if($ij==$totalmonth){
						$newdatenew.=  "<b>" .date('Y', strtotime($wc_item['bookme']['month_date'][$totalmonth-1])). "</b>";
					}
					}
					
			
					
        $message = str_replace("{booking_time}", $appointment_time, $message);
        $message = str_replace("{booking_date}", $newdatenew, $message);
        $message = str_replace("{category_name}", $category, $message);
        $message = str_replace("{no_of_person}", $person, $message);
        $message = str_replace("{service_name}", $service, $message);
        $message = str_replace("{service_price}", $price, $message);
        $message = str_replace("{employee_name}", $employee, $message);
        $message = str_replace("{customer_name}", $cname, $message);

        $other_data[] = array(
            'name' => bookme_get_settings('woocommerce_cart_data', '').PHP_EOL,
            'value'=>$message);
    }

    return $other_data;
}

function bookme_orderItemMeta( $item_id )
{
    $data = wc_get_order_item_meta( $item_id, 'bookme' );
    if ( $data ) {
        $other_data = bookme_getItemData( array(), array( 'bookme' => $data ) );
        echo '<br/>' . $other_data[0]['name'] . '<br/>' . nl2br( $other_data[0]['value'] );
    }
}

function bookme_getCartItemPrice( $product_price, $wc_item, $cart_item_key )
{
    if ( isset ( $wc_item['bookme'] ) ) {
        //$month_date=count($wc_item['bookme']['month_date']);
        $product_price = wc_price($wc_item['bookme']['price'] * $wc_item['bookme']['person']);
    }
    return $product_price;
}

function bookme_woo_add_to_cart()
{
	if(isset($_POST['access_token'])){
    	$access_token = $_POST['access_token'];
    	$woocommerce_product = bookme_get_settings('woocommerce_product', 0);
    	$session = WC()->session;
    	if ( $session instanceof \WC_Session_Handler && $session->get_session_cookie() === false ) {
            $session->set_customer_session_cookie( true );
        }

        if(!bookme_check_cart_exist($access_token)){
            $code = isset($_SESSION['bookme'][$access_token]['disc_code']) ? $_SESSION['bookme'][$access_token]['disc_code'] : '';
    	    $dic_price = isset($_SESSION['bookme'][$access_token]['discount']['off_price']) ? $_SESSION['bookme'][$access_token]['discount']['off_price'] : '';
    	    $appointstart = $_SESSION['bookme'][$access_token]['time_s'];
    	    $appointend = $_SESSION['bookme'][$access_token]['time_e'];
    	    $category = $_SESSION['bookme'][$access_token]['category'];
    	    $service = $_SESSION['bookme'][$access_token]['service'];
    	    $employee = $_SESSION['bookme'][$access_token]['employee'];
    	    $dates = $_SESSION['bookme'][$access_token]['date'];
    	    $person = $_SESSION['bookme'][$access_token]['person'];
    	    $student = $_SESSION['bookme'][$access_token]['student'];

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


    	    global $wpdb;
    	    $table_coupans = $wpdb->prefix . 'bookme_coupons';
    	    $table_settings = $wpdb->prefix . 'bookme_settings';
    	    $table_all_employee = $wpdb->prefix . 'bookme_employee';
    	    $table = $wpdb->prefix . 'bookme_category';
    	    $table_enotification = $wpdb->prefix . 'bookme_email_notification';
    	    $table_current_booking_fields = $wpdb->prefix . 'bookme_current_booking_fields';
    	    $table_current_booking = $wpdb->prefix . 'bookme_current_booking';
    	    $table_book_service = $wpdb->prefix . 'bookme_service';
    	    $table_customer_booking = $wpdb->prefix . 'bookme_customer_booking';
    	    $table_customers = $wpdb->prefix . 'bookme_customers';
    	    $table_payments = $wpdb->prefix . 'bookme_payments';
    	    $table_holidays = $wpdb->prefix . 'bookme_holidays';

            $dt = new DateTime($dates);
            $booking_date = $dt->format('Y-m-d');

    	    $hodiday = 0;

    		
    	    $result = $wpdb->get_results("SELECT * FROM $table_holidays WHERE staff_id = $employee");
    	    foreach ($result as $holiday) {
    	        if ($holiday->holi_date == $booking_date){
    	            $hodiday = 1;
    	        }
    	    }

    	    if ($hodiday == 0) {
    	        $rowcount = $wpdb->get_var("SELECT COUNT(cb.id) FROM $table_customer_booking cb LEFT JOIN $table_current_booking b ON cb.booking_id = b.id LEFT JOIN $table_customers c ON cb.customer_id = c.id WHERE b.cat_id='" . $category . "' and b.ser_id='" . $service . "' and b.emp_id='" . $employee . "' and b.date='" . $dates . "' and b.time = '" . $appointstart . "' and c.email='" . $email . "'");
    	        if ($rowcount >= 1) {
    	            echo '2';
    	        } else {
    	            $booked = 0;
    	            $resultSs = $wpdb->get_results("SELECT capacity,duration,name,product_id FROM $table_book_service WHERE id=$service and catId=$category");
    	            $capacity = $resultSs[0]->capacity;
    	            $duration = $resultSs[0]->duration;
    	            $servname = $resultSs[0]->name;
                    $woocommerce_product=$resultSs[0]->product_id;
    	            $countAppoint = $wpdb->get_results("SELECT SUM(cb.no_of_person) as sump FROM $table_customer_booking cb LEFT JOIN $table_current_booking b ON b.id = cb.booking_id WHERE  b.ser_id='$service' and b.emp_id='$employee' and b.date='$dates' and b.time = '$appointstart' and b.duration = '$duration'");
    	            if (empty($countAppoint[0]->sump)) {
    	                $booked = 0;
    	            } else {
    	                $booked = $countAppoint[0]->sump;
    	            }
    	            $avl = 0;
    	            $ij=0;
    	            $avl = $capacity - $booked;
    	            
    	            	$totalmonth=count($_SESSION['bookme'][$access_token]['month_date']);
    	            	$m_testes=$_SESSION['bookme'][$access_token]['month_date'];
					foreach ($m_testes as $m_date) {
						if($ij==0){
						$newdate= "<span><b>" .date('M', strtotime($m_testes[0])). ",</b>";
						}
						
						$newdate.= "" .date('d', strtotime($m_testes[$ij])). "</span>,";
					
					   if($ij!=0){
							$newdate1=date('M', strtotime($m_testes[$ij]));
							$olddate=date('M', strtotime($m_testes[$ij-1]));
					
					 if($newdate!=$olddate){
						 $newdate.=  "<b><span>" .date('Y', strtotime($m_testes[$ij-1])). "</span>,<br><span>" .$newdate1. "</span>,</b>";
						
						}
					}
					++$ij;
					if($ij==$totalmonth){
						$newdate.=  "<b><span>" .date('Y', strtotime($m_testes[$totalmonth-1])). "</span></b><br>";
					}
					}
    	            if ($person <= $avl) {
    	            	$data = array(
    	            		'code' => $code,
    	            		'dic_price' => $dic_price,
    	            		'appointstart' => $appointstart,
    	            		'appointend' => $appointend,
    	            		'category' => $category,
    	            		'service' => $service,
    	            		'employee' => $employee,
    	            		'dates' => $dates,
                            'month_date'=>$_SESSION['bookme'][$access_token]['month_date'],
    	            		'person' => $person,
    	            		'name' => $name,
    	            		'email' => $email,
    	            		'phone' => $phone,
    	            		'student' => $student,
    	            		'notes' => $notes,
    	            		'price' => $price*count($_SESSION['bookme'][$access_token]['month_date']),
    	            		'custom_text' => $custom_text,
    	            		'custom_textarea' => $custom_textarea,
    	            		'custom_content' => $custom_content,
    	            		'custom_checkbox' => $custom_checkbox,
    	            		'custom_radio' => $custom_radio,
    	            		'custom_select' => $custom_select
    	            		);
                        $qt=count($_SESSION['bookme'][$access_token]['month_date']);
                        WC()->cart->add_to_cart( $woocommerce_product, $qt, '', array(), array( 'bookme' => $data ) );
                        unset($_SESSION['bookme']);
                        echo wc_get_cart_url();
    	            } else {
    	                echo '3';
    	            }
    	        }
            } else {
    	        echo '4';
    	    }
        } else {
            echo wc_get_cart_url();
        }
	}
}

function bookme_check_cart_exist($accesstoken){
    $appointstart = $_SESSION['bookme'][$accesstoken]['time_s'];
    $service = $_SESSION['bookme'][$accesstoken]['service'];
    $employee = $_SESSION['bookme'][$accesstoken]['employee'];
    $dates = $_SESSION['bookme'][$accesstoken]['date'];
    $email = $_SESSION['bookme'][$accesstoken]['email'];

    $available = false;

    foreach ( WC()->cart->get_cart() as $wc_key => $wc_item ) {
        if ( array_key_exists( 'bookme', $wc_item ) ) {
            $cart_appointstart = $wc_item['bookme']['appointstart'];
            $cart_service = $wc_item['bookme']['service'];
            $cart_employee = $wc_item['bookme']['employee'];
            $cart_dates = $wc_item['bookme']['dates'];
            $cart_email = $wc_item['bookme']['email'];

            if($appointstart == $cart_appointstart && $service == $cart_service && $employee == $cart_employee && $dates == $cart_dates && $email == $cart_email){
                $available = true;
                break;
            }
        }
    }
    return $available;
}

?>
