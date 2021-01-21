<?php
add_action('wp_ajax_nopriv_bookme_admin_action', 'bookme_admin_ajax_action');
add_action('wp_ajax_bookme_admin_action', 'bookme_admin_ajax_action');

function bookme_admin_ajax_action()
{
    /* woo */
    if (isset($_POST['call'])) {
        if ($_POST['call'] == 'save_cat') {
            bookme_save_cat();
        }
        if ($_POST['call'] == 'delete_cat') {
            bookme_delete_cat();
        }
        if ($_POST['call'] == 'edit_cat') {
            bookme_edit_cat();
        }
        if ($_POST['call'] == 'get_services_by_cat_id') {
            bookme_get_services_by_cat_id($_POST['cat_id']);
        }
        if ($_POST['call'] == 'get_emp_by_ser_id') {
            bookme_get_emp_by_ser_id($_POST['ser_id']);
        }
        if ($_POST['call'] == 'fetch_services') {
            bookme_fetch_services();
        }
        if ($_POST['call'] == 'del_services') {
            bookme_del_services();
        }
        if ($_POST['call'] == 'del_members') {
            bookme_del_members();
        }
        if ($_POST['call'] == 'get_services_by_emp_id') {
            bookme_get_services_by_emp_id();
        }
        if ($_POST['call'] == 'check_for_appointment') {
            bookme_check_for_appointment();
        }
        if ($_POST['call'] == 'del_bookings') {
            bookme_del_bookings();
        }
        if ($_POST['call'] == 'del_customers') {
            bookme_del_customers();
        }
        if ($_POST['call'] == 'del_payments') {
            bookme_del_payments();
        }
        if ($_POST['call'] == 'complete_payment') {
            bookme_complete_payment();
        }
        if ($_POST['call'] == 'del_coupon') {
            bookme_del_coupon();
        }
        if ($_POST['call'] == 'save_custom_fields') {
            bookme_save_custom_fields();
        }
        if ($_POST['call'] == 'insert_email_notification') {
            bookme_insert_email_notification();
        }
        if ($_POST['call'] == 'insert_sms_notification') {
            bookme_insert_sms_notification();
        }
        if ($_POST['call'] == 'save_appearance_bullets') {
            bookme_save_appearance_bullets();
        }
        if ($_POST['call'] == 'save_appearance_labels') {
            bookme_save_appearance_labels();
        }
        if ($_POST['call'] == 'save_appearance_colors') {
            bookme_save_appearance_colors();
        }
        if ($_POST['call'] == 'save_appearance_msg') {
            bookme_save_appearance_msg();
        }
        if ($_POST['call'] == 'save_company_data') {
            bookme_save_company_data();
        }
        if ($_POST['call'] == 'save_gen_settings') {
            bookme_save_gen_settings();
        }
        if ($_POST['call'] == 'save_payment_details') {
            bookme_save_payment_details();
        }
        if ($_POST['call'] == 'get_calender_for_emp') {
            bookme_get_calender_for_emp();
        }
        if ($_POST['call'] == 'save_dayoff') {
            bookme_save_dayoff();
        }
        if ($_POST['call'] == 'save_woocommerce') {
            bookme_save_woocommerce();
        }
        if ($_POST['call'] == 'save_cart_settings') {
            bookme_save_cart_settings();
        }
        if ($_POST['call'] == 'save_google_calendar') {
            bookme_save_google_calendar();
        }
    }
    if (isset($_GET['call'])) {
        if ($_GET['call'] == 'edit_service') {
            bookme_edit_service();
        }
        if ($_GET['call'] == 'save_emp_data') {
            bookme_save_emp_data();
        }
        if ($_GET['call'] == 'save_cust_data') {
            bookme_save_cust_data();
        }
        if ($_GET['call'] == 'save_coupon') {
            bookme_save_coupon();
        }
        if ($_GET['call'] == 'save_booking') {
            bookme_save_booking();
        }
    }
    wp_die();
}

function bookme_save_cat()
{

    global $wpdb;
    if (isset($_POST['name'])) {
        $table = $wpdb->prefix . 'bookme_category';
        $rowcount = $wpdb->get_var("SELECT COUNT(*) FROM $table WHERE name='" . $_POST['name'] . "'");
        if ($rowcount >= 1) {
            echo "exist";
        } else {
            $insert = $wpdb->insert($table, array(
                'name' => $_POST['name']
            ));
            if ($insert) {
                echo $wpdb->insert_id;
            } else {
                echo 'db_error';
            }
        }
    }

}

function bookme_delete_cat()
{

    global $wpdb;
    $table_book_category = $wpdb->prefix . 'bookme_category';
    $table_book_service = $wpdb->prefix . 'bookme_service';
    $table_current_booking = $wpdb->prefix . 'bookme_current_booking';
    $table_customer_booking = $wpdb->prefix . 'bookme_customers_booking';
    $table_payment = $wpdb->prefix . 'bookme_payments';
    if (isset($_POST['del_cat'])) {
        $wpdb->delete($table_book_category, array('id' => $_POST['del_cat']), array('%d'));
        $wpdb->delete($table_book_service, array('catId' => $_POST['del_cat']), array('%d'));

        $booking_id = $wpdb->get_results("Select id from $table_current_booking where cat_id=" . $_POST['del_cat']);
        foreach ($booking_id as $b_id) {
            $payment_id = $wpdb->get_results("Select payment_id from $table_customer_booking where booking_id=" . $b_id->id);
            foreach ($payment_id as $p_id) {
                $wpdb->delete($table_payment, array('id' => $p_id->payment_id), array('%d'));
            }
            $wpdb->delete($table_customer_booking, array('booking_id' => $b_id->id), array('%d'));
        }
        $wpdb->delete($table_current_booking, array('cat_id' => $_POST['del_cat']), array('%d'));
        echo "1";
    } else {
        echo "0";
    }

}

function bookme_edit_cat()
{

    global $wpdb;
    if (isset($_POST['cat_name'])) {
        $table = $wpdb->prefix . 'bookme_category';
        $tableS = $wpdb->prefix . 'bookme_service';
        $cat_update = $wpdb->update($table, array(
            'name' => $_POST['cat_name'],
            'status' => $_POST['catState']
        ),
            array('id' => $_POST['cat_id']),
            array(
                '%s',
                '%s',
                '%s'
            ),
            array('%d')
        );
        echo '1';
    } else {
        echo '0';
    }

}

function bookme_fetch_services()
{
    global $wpdb;
    $tableS = $wpdb->prefix . 'bookme_service';
    if (isset($_POST['cat_id'])) {
        $cat_id = $_POST['cat_id'];
        if ($cat_id == 0) {
            $condition = '';
        } else {
            $condition = "WHERE catId='" . $cat_id . "'";
        }
        $resultS = $wpdb->get_results("SELECT id,name,ser_icon,duration,price FROM $tableS $condition ORDER BY id ASC");
        if ($wpdb->num_rows > 0) {
            foreach ($resultS as $values) {
                $time = $values->duration;
                $min = '';
                if (($time / 60) < 60) {
                    $time = ($time / 60) . " M";
                }
                if (($time / 60) >= 60) {
                    if (($time / 60) % 60 > 0) {
                        $min = (($time / 60) % 60) . " M";
                    }
                    $time = ltrim(gmdate("H", $values->duration), '0') . ' H ' . $min;
                }
                $price = bookme_formatPrice($values->price);
                ?>
                <tr data-url="<?php echo admin_url('admin-ajax.php') . '?action=edit_ser&ser_id=' . $values->id; ?>"
                    data-toggle="slidePanel" data-serid="<?php echo $values->id; ?>"
                    data-search-term="<?php echo strtolower($values->name); ?>">
                    <td class="pre-cell"></td>
                    <td class="cell-30">
                <span class="checkbox-custom checkbox-primary checkbox-lg ">
                  <input type="checkbox" class="contacts-checkbox selectable-item" id="contacts_1"
                  />
                  <label for="contacts_1"></label>
                </span>
                    </td>
                    <td class="cell-300">
                        <div class="ellipsis  cell-300">
                            <span class="ser-icon fa <?php echo $values->ser_icon; ?>"></span>
                            <?php echo $values->name; ?>
                        </div>
                    </td>
                    <td class="cell-300"><?php echo $time; ?></td>
                    <td><?php echo $price; ?></td>
                    <td class="suf-cell"></td>
                </tr>
            <?php }
        } else {
            ?>
            <tr>
                <td class="pre-cell"></td>
                <td colspan="4"><h4 class="text-center"><?php _e('No services found.', 'bookme'); ?></h4></td>
                <td class="suf-cell"></td>
            </tr>
            <?php
        }
    } else {
        echo '0';
    }
}

function bookme_edit_service()
{
    global $wpdb;
    $table = $wpdb->prefix . 'bookme_service';
    $res = array();
    if (isset($_POST['ser_id'])) {
        $wpdb->update($table, array(
            'catId' => $_POST['category_id'],
            'name' => $_POST['title'],
            'ser_icon' => $_POST['serviceicon'],
            'price' => $_POST['price'],
            'description' => $_POST['info'],
            'visibility' => $_POST['visibility'],
            'capacity' => $_POST['capacity'],
            'duration' => $_POST['duration'],
            'paddingBefore' => $_POST['padding_left'],
            'staff' => implode($_POST['staff'], ','),
            'product_id'=>$_POST['product_id']
        ),
            array('id' => $_POST['ser_id']),
            array(
                '%d',
                '%s',
                '%s',
                '%d',
                '%s',
                '%d',
                '%d',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s'
            ),
            array('%d')
        );
        $res['response'] = 'edited';
        echo json_encode($res);

    } else {
        if ($wpdb->insert($table, array(
            'catId' => $_POST['category_id'],
            'name' => $_POST['title'],
            'ser_icon' => $_POST['serviceicon'],
            'price' => $_POST['price'],
            'description' => $_POST['info'],
            'visibility' => $_POST['visibility'],
            'capacity' => $_POST['capacity'],
            'duration' => $_POST['duration'],
            'paddingBefore' => $_POST['padding_left'],
            'staff' => implode($_POST['staff'], ',')
        ))
        ) {
            $res['response'] = 'added';
            $res['id'] = $wpdb->insert_id;
            echo json_encode($res);
        }

    }
}

function bookme_del_services()
{

    if (isset($_POST['ser_id'])) {
        global $wpdb;
        $table_book_service = $wpdb->prefix . 'bookme_service';
        $table_current_booking = $wpdb->prefix . 'bookme_current_booking';
        $table_customer_booking = $wpdb->prefix . 'bookme_customer_booking';
        $table_payment = $wpdb->prefix . 'bookme_payments';
        for ($del = 0; $del < count($_POST['ser_id']); $del++) {
            $wpdb->delete($table_book_service, array('id' => $_POST['ser_id'][$del]), array('%d'));

            $booking_id = $wpdb->get_results("Select id from $table_current_booking where ser_id=" . $_POST['ser_id'][$del]);
            foreach ($booking_id as $b_id) {
                $payment_id = $wpdb->get_results("Select payment_id from $table_customer_booking where booking_id=" . $b_id->id);
                foreach ($payment_id as $p_id) {
                    $wpdb->delete($table_payment, array('id' => $p_id->payment_id), array('%d'));
                }
                $wpdb->delete($table_customer_booking, array('booking_id' => $b_id->id), array('%d'));
            }
            $wpdb->delete($table_current_booking, array('ser_id' => $_POST['ser_id'][$del]), array('%d'));
        }
        echo '1';
    } else {
        echo '0';
    }

}

function bookme_save_emp_data()
{
    global $wpdb;
    $table_all_employee = $wpdb->prefix . 'bookme_employee';
    $table_book_service = $wpdb->prefix . 'bookme_service';
    $table_member_schedule = $wpdb->prefix . 'bookme_member_schedule';
    $day = array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday');
    $res = array();
    $allSer = $wpdb->get_results("SELECT id, staff FROM $table_book_service");
    if (isset($_POST['emp_id'])) {
        $wpdb->update($table_all_employee, array(
            'name' => $_POST['full_name'],
            'email' => $_POST['email'],
            'phone' => $_POST['phone'],
            'info' => $_POST['info'],
            'visibility' => $_POST['visibility'],
            'img' => $_POST['emp_img_url'],
            'country'=>$_POST['country']
        ),
            array('id' => $_POST['emp_id']),
            array(
                '%s',
                '%s',
                '%s',
                '%s',
                '%d',
                '%s',
            ),
            array('%d')
        );


        foreach ($allSer as $ser) {
            $serStaff = $ser->staff;
            $serArr = explode(',', $serStaff);
            if (@in_array($ser->id, @$_POST['services'])) {
                if (!in_array($_POST['emp_id'], $serArr)) {
                    $wpdb->update($table_book_service, array(
                        'staff' => ($serStaff == '') ? $_POST['emp_id'] : $serStaff . ',' . $_POST['emp_id']
                    ),
                        array(
                            'id' => $ser->id
                        ),
                        array('%s'),
                        array('%d')
                    );
                }
            } else {
                $index = array_search($_POST['emp_id'], $serArr);
                if ($index !== false) {
                    unset($serArr[$index]);
                    $wpdb->update($table_book_service, array(
                        'staff' => implode(',', $serArr)
                    ),
                        array(
                            'id' => $ser->id
                        ),
                        array('%s'),
                        array('%d')
                    );
                }
            }
        }

        for ($i = 0; $i < count($day); $i++) {
            $result = $wpdb->get_results("SELECT COUNT(*) total FROM $table_member_schedule WHERE emp_id = " . $_POST['emp_id'] . " AND day = '" . $day[$i] . "'");
            if ($result[0]->total <= 0) {
                if (@$_POST['day_off'][$i][0] != 'on' && $_POST['schedule_start'][$i][0] != '' && $_POST['schedule_end'][$i][0] != '') {
                    $wpdb->insert($table_member_schedule, array(
                        'emp_id' => $_POST['emp_id'],
                        'schedule_start' => $_POST['schedule_start'][$i][0],
                        'schedule_end' => $_POST['schedule_end'][$i][0],
                        'break_start' => isset($_POST['break_start'][$i][0]) ? $_POST['break_start'][$i][0] : '',
                        'break_end' => isset($_POST['break_end'][$i][0]) ? $_POST['break_end'][$i][0] : '',
                        'day' => $day[$i]
                    ));
                }
            } else {
                if (@$_POST['day_off'][$i][0] != 'on' && $_POST['schedule_start'][$i][0] != '' && $_POST['schedule_end'][$i][0] != '') {
                    $wpdb->update($table_member_schedule, array(
                        'schedule_start' => $_POST['schedule_start'][$i][0],
                        'schedule_end' => $_POST['schedule_end'][$i][0],
                        'break_start' => isset($_POST['break_start'][$i][0]) ? $_POST['break_start'][$i][0] : '',
                        'break_end' => isset($_POST['break_end'][$i][0]) ? $_POST['break_end'][$i][0] : ''
                    ),
                        array('emp_id' => $_POST['emp_id'],
                            'day' => $day[$i]
                        ),
                        array(
                            '%s',
                            '%s',
                            '%s',
                            '%s',
                        ),
                        array('%d', '%s')
                    );
                } else {
                    $wpdb->delete($table_member_schedule,
                        array('emp_id' => $_POST['emp_id'],
                            'day' => $day[$i]
                        ),
                        array('%d', '%s')
                    );
                }
            }
        }

        $res['response'] = 'edited';
        echo json_encode($res);

    } else {
        $wpdb->insert($table_all_employee, array(
            'name' => $_POST['full_name'],
            'email' => $_POST['email'],
            'phone' => $_POST['phone'],
            'info' => $_POST['info'],
            'visibility' => $_POST['visibility'],
            'img' => $_POST['emp_img_url'],
            'country'=>$_POST['country']
        ));

        $emp_id = $wpdb->insert_id;

        foreach ($allSer as $ser) {
            $serStaff = $ser->staff;
            if (@in_array($ser->id, @$_POST['services'])) {
                $wpdb->update($table_book_service, array(
                    'staff' => ($serStaff == '') ? $emp_id : $serStaff . ',' . $emp_id
                ),
                    array(
                        'id' => $ser->id
                    ),
                    array('%s'),
                    array('%d')
                );

            }
        }

        for ($i = 0; $i < count($day); $i++) {
            if ($_POST['day_off'][$i][0] != 'on' && $_POST['schedule_start'][$i][0] != '' && $_POST['schedule_end'][$i][0] != '') {
                $wpdb->insert($table_member_schedule, array(
                    'emp_id' => $emp_id,
                    'schedule_start' => $_POST['schedule_start'][$i][0],
                    'schedule_end' => $_POST['schedule_end'][$i][0],
                    'break_start' => isset($_POST['break_start'][$i][0]) ? $_POST['break_start'][$i][0] : '',
                    'break_end' => isset($_POST['break_end'][$i][0]) ? $_POST['break_end'][$i][0] : '',
                    'day' => $day[$i]
                ));
            }
        }

        $res['response'] = 'added';
        $res['id'] = $emp_id;
        echo json_encode($res);

    }
}

function bookme_del_members()
{

    if (isset($_POST['emp_id'])) {
        global $wpdb;
        $table_all_employee = $wpdb->prefix . 'bookme_employee';
        $table_book_service = $wpdb->prefix . 'bookme_service';
        $table_member_schedule = $wpdb->prefix . 'bookme_member_schedule';

        foreach ($_POST['emp_id'] as $emp_id) {
            $wpdb->delete($table_all_employee,
                array('id' => $emp_id),
                array('%d')
            );

            $allSer = $wpdb->get_results("SELECT id, staff FROM $table_book_service WHERE find_in_set('" . $emp_id . "',staff) <> 0");

            foreach ($allSer as $ser) {
                $serStaff = $ser->staff;
                $serArr = explode(',', $serStaff);
                $index = array_search($emp_id, $serArr);
                if ($index !== false) {
                    unset($serArr[$index]);
                    $wpdb->update($table_book_service, array(
                        'staff' => implode(',', $serArr)
                    ),
                        array(
                            'id' => $ser->id
                        ),
                        array('%s'),
                        array('%d')
                    );
                }
            }

            $wpdb->delete($table_member_schedule,
                array('emp_id' => $emp_id),
                array('%d')
            );
        }
        echo '1';
    } else {
        echo '0';
    }

}

function bookme_get_services_by_emp_id()
{
    if (isset($_POST['empid'])) {
        global $wpdb;
        $tableS = $wpdb->prefix . 'bookme_service';
        $resultser = $wpdb->get_results("SELECT * FROM $tableS WHERE find_in_set('" . $_POST['empid'] . "',staff) <> 0");
        echo '<option data-capacity="0" data-duration="0" value="0">Select service</option>';
        if ($wpdb->num_rows > 0) {
            foreach ($resultser as $ser) {
                ?>
                <option data-capacity="<?php echo $ser->capacity; ?>" data-duration="<?php echo $ser->duration; ?>"
                        value="<?php echo $ser->id; ?>">
                    <?php echo $ser->name; ?>
                    <?php
                    $time = $ser->duration;
                    if (($time / 60) < 60) {
                        echo '(' . ($time / 60) . " M" . ')';
                    }
                    if (($time / 60) >= 60) {
                        echo '(' . ($time / (60 * 60)) . " H" . ')';
                    }
                    ?>
                </option>
                <?php
            }
        }
    } else {
        echo '0';
    }
}

function bookme_check_for_appointment()
{
    global $wpdb;
    $postStime = $_POST['start_t'];
    $postEtime = $_POST['end_t'];
    $table_current_booking = $wpdb->prefix . 'bookme_current_booking';
    $table_customer_booking = $wpdb->prefix . 'bookme_customer_booking';
    $booked = 0;
    $sqlt = $wpdb->get_results("SELECT b.time,b.duration FROM $table_customer_booking cb LEFT JOIN $table_current_booking b ON cb.booking_id = b.id WHERE b.emp_id='" . $_POST['emp_id'] . "' and b.date='" . $_POST['date'] . "' and cb.id != '" . $_POST['booking_id'] . "' ");
    foreach ($sqlt as $sql) {
        $time = strtotime($sql->time);
        $etime = strtotime($sql->time + $sql->duration);

        if (($time >= $postStime && $time <= $postEtime) || ($etime >= $postStime && $etime <= $postEtime)) {
            $booked = 1;
        }
    }
    echo $booked;
}

function bookme_save_booking()
{

    global $wpdb;
    add_filter('wp_mail_content_type', 'bookme_set_html_mail_content_type');

    $service = $_POST['edit_ser'];
    $employee = $_POST['provider_name'];
    $dates = $_POST['booking_date'];
    $appointstart = date('g:i A', $_POST['time_start']);

    $person = $_POST['person'];
    $person_count = array_count_values($person);
    $customers = $_POST['customers'];
    $old_customers = $_POST['old_customers'];

    $table_settings = $wpdb->prefix . 'bookme_settings';
    $table_all_employee = $wpdb->prefix . 'bookme_employee';
    $table = $wpdb->prefix . 'bookme_category';
    $table_enotification = $wpdb->prefix . 'bookme_email_notification';
    $table_current_booking = $wpdb->prefix . 'bookme_current_booking';
    $table_book_service = $wpdb->prefix . 'bookme_service';
    $table_customer_booking = $wpdb->prefix . 'bookme_customer_booking';
    $table_customers = $wpdb->prefix . 'bookme_customers';
    $table_payments = $wpdb->prefix . 'bookme_payments';

    $resultSs = $wpdb->get_results("SELECT capacity,duration,name,price,catId FROM $table_book_service WHERE id=$service");
    $capacity = $resultSs[0]->capacity;
    $duration = $resultSs[0]->duration;
    $servname = $resultSs[0]->name;
    $price = $resultSs[0]->price;
    $category = $resultSs[0]->catId;

    $resultE = $wpdb->get_results("SELECT name,email,google_data FROM $table_all_employee WHERE id=" . $employee);
    $employee_name = $resultE[0]->name;
    $employee_email = $resultE[0]->email;
    $google_data = $resultE[0]->google_data;

    $appointend = date('g:i A', strtotime($appointstart) + $duration);

    $free = 0;

    if ($free == 0) {
        $booked = 0;
        $countAppoint = $wpdb->get_results("SELECT SUM(cb.no_of_person) as sump FROM $table_customer_booking cb LEFT JOIN $table_current_booking b ON b.id = cb.booking_id WHERE  b.ser_id='$service' and b.emp_id='$employee' and b.date='$dates' and b.time = '$appointstart' and b.duration = '$duration'");
        if (empty($countAppoint[0]->sump)) {
            $booked = 0;
        } else {
            $booked = $countAppoint[0]->sump;
        }
        $avl = $capacity - $booked;
        if ($person_count[0] <= $avl) {
            $gc_event_id = null;
            $edited_staff = 0;
            if (isset($_POST['bookingId'])) {
                $booking_id = $_POST['bookingId'];

                $gc_staff_result = $wpdb->get_results("select google_event_id,emp_id from $table_current_booking where id = $booking_id ");
                if ($gc_staff_result[0]->emp_id != $employee) {
                    $edited_staff = $gc_staff_result[0]->emp_id;
                    /* Google Calendar integration */
                    if (bookme_get_settings('bookme_gc_client_id') != null) {
                        include_once plugin_dir_path(__FILE__) . '/../includes/google.php';
                        $bookme_gc_client = new Google_Client();
                        $bookme_gc_client->setClientId(bookme_get_settings('bookme_gc_client_id'));
                        $bookme_gc_client->setClientSecret(bookme_get_settings('bookme_gc_client_secret'));
                        $google_data1 = $wpdb->get_var("SELECT google_data FROM $table_all_employee WHERE id=" . $edited_staff);
                        if ($google_data1) {
                            try {
                                $bookme_gc_client->setAccessToken($google_data1);
                                if ($bookme_gc_client->isAccessTokenExpired()) {
                                    $bookme_gc_client->refreshToken($bookme_gc_client->getRefreshToken());
                                    $wpdb->update($table_all_employee, array('google_data' => $bookme_gc_client->getAccessToken()), array('id' => $edited_staff), array('%s'), array('%d'));
                                }
                                $bookme_gc_service = new Google_Service_Calendar($bookme_gc_client);
                                $google_calendar_id = 'primary';
                                $gc_calendar = $bookme_gc_service->calendarList->get($google_calendar_id);
                                $gc_access = $gc_calendar->getAccessRole();
                                if (in_array($gc_access, array('writer', 'owner'))) {
                                    $bookme_gc_service->events->delete($google_calendar_id, $gc_staff_result[0]->google_event_id);

                                }
                            } catch (Exception $e) {
                                $res['error_del'] = $e->getMessage();
                            }
                        }
                    }

                    $wpdb->update($table_current_booking, array(
                        'ser_id' => $service,
                        'emp_id' => $employee,
                        'date' => $dates,
                        'time' => $appointstart,
                        'duration' => $duration,
                        'google_event_id' => null
                    ),
                        array('id' => $booking_id),
                        array(
                            '%d',
                            '%d',
                            '%s',
                            '%s',
                            '%s',
                            '%d'
                        ),
                        array('%d')
                    );
                } else {
                    $gc_event_id = $gc_staff_result[0]->google_event_id;
                    $wpdb->update($table_current_booking, array(
                        'ser_id' => $service,
                        'emp_id' => $employee,
                        'date' => $dates,
                        'time' => $appointstart,
                        'duration' => $duration
                    ),
                        array('id' => $booking_id),
                        array(
                            '%d',
                            '%d',
                            '%s',
                            '%s',
                            '%s',
                        ),
                        array('%d')
                    );
                }

                $del_customers = array_diff($old_customers, $customers);
                foreach($del_customers as $del_customer){
                    $payment_id = $wpdb->get_results("Select payment_id from $table_customer_booking where booking_id=" . $booking_id ." and customer_id = ".$del_customer);
                    foreach ($payment_id as $p_id) {
                        $wpdb->delete($table_payments, array('id' => $p_id->payment_id), array('%d'));
                    }
                    $wpdb->delete($table_customer_booking, array('booking_id' => $booking_id,'customer_id' => $del_customer), array('%d'));
                }
                $new_customers = array_diff($customers, $old_customers);
                for($i = 0; $i < count($new_customers); $i++){
                    $wpdb->insert($table_payments, array(
                        'created' => current_time('mysql'),
                        'type' => 'locally',
                        'price' => $price,
                        'status' => 'pending'
                    ));
                    $payment_id = $wpdb->insert_id;
                    $wpdb->insert($table_customer_booking, array(
                        'customer_id' => $customers[$i],
                        'booking_id' => $booking_id,
                        'payment_id' => $payment_id,
                        'no_of_person' => 1,
                        'status' => 'Approved'
                    ));
                }

                $res['response'] = 'edited';
            } else {
                $booking_result = $wpdb->get_results("select id,google_event_id from $table_current_booking where ser_id = $service and emp_id = $employee and date = '$dates' and time = '$appointstart'");
                if ($wpdb->num_rows > 0) {
                    $booking_id = $booking_result[0]->id;
                    $gc_event_id = $booking_result[0]->google_event_id;
                } else {
                    $wpdb->insert($table_current_booking, array('cat_id' => $category, 'ser_id' => $service, 'emp_id' => $employee, 'date' => $dates, 'time' => $appointstart, 'duration' => $duration));
                    $booking_id = $wpdb->insert_id;
                }
                for ($i = 0; $i < count($customers); $i++) {
                    $wpdb->insert($table_payments, array(
                        'created' => current_time('mysql'),
                        'type' => 'locally',
                        'price' => $price,
                        'status' => 'pending'
                    ));
                    $payment_id = $wpdb->insert_id;
                    $wpdb->insert($table_customer_booking, array(
                        'customer_id' => $customers[$i],
                        'booking_id' => $booking_id,
                        'payment_id' => $payment_id,
                        'no_of_person' => 1,
                        'status' => 'Approved'
                    ));
                }
                $res['response'] = 'added';
                $res['id'] = $booking_id;
            }
            $cname = array();
            $cemail = array();
            $cphone = array();
            $cid = array();

            $resultcompanyName = $wpdb->get_results("SELECT book_value FROM $table_settings WHERE book_key='companyName'");
            $resultcompanyAddress = $wpdb->get_results("SELECT book_value FROM $table_settings WHERE book_key='companyAddress'");
            $resultcompanyPhone = $wpdb->get_results("SELECT book_value FROM $table_settings WHERE book_key='companyPhone'");
            $resultcompanyWebsite = $wpdb->get_results("SELECT book_value FROM $table_settings WHERE book_key='companyWebsite'");

            $company_name = $resultcompanyName[0]->book_value;
            $company_address = $resultcompanyAddress[0]->book_value;
            $company_phone = $resultcompanyPhone[0]->book_value;
            $company_website = $resultcompanyWebsite[0]->book_value;

            $results = $wpdb->get_results("SELECT name FROM $table where id=" . $category . "");
            $category_name = $results[0]->name;
            $service_name = $servname;

            for ($i = 0; $i < count($customers); $i++) {

                $resultC = $wpdb->get_results("SELECT name,email,phone,notes FROM $table_customers WHERE id=$customers[$i]");
                $cname[] = $resultC[0]->name;
                $cemail[] = $resultC[0]->email;
                $cphone[] = $resultC[0]->phone;
                $cid[] = $customers[$i];

                if ($_POST['notifications']) {

                    $endtime = strtotime($appointstart);
                    $appointment_time = date_i18n(get_option('time_format'), strtotime($appointstart));
                    $appointment_end_time = date_i18n(get_option('time_format'), $endtime + $duration);

                    $booking_date = $dates;
                    $ttl_person = $person;
                    $customer_name = ucfirst($resultC[0]->name);
                    $customer_email = $resultC[0]->email;
                    $customer_phone = $resultC[0]->phone;
                    $customer_note = $resultC[0]->notes;

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

                        wp_mail(get_bloginfo('admin_email'), $subject, $message, $headers);
                    }

                    remove_filter('wp_mail_content_type', 'wpdocs_set_html_mail_content_type');

                }
            }

            /* Google Calendar integration */
            if (bookme_get_settings('bookme_gc_client_id') != null) {
                if ($google_data) {
                    include_once plugin_dir_path(__FILE__) . '/../includes/google.php';
                    $bookme_gc_client1 = new Google_Client();
                    $bookme_gc_client1->setClientId(bookme_get_settings('bookme_gc_client_id'));
                    $bookme_gc_client1->setClientSecret(bookme_get_settings('bookme_gc_client_secret'));

                    try {
                        $bookme_gc_client1->setAccessToken($google_data);
                        if ($bookme_gc_client1->isAccessTokenExpired()) {
                            $bookme_gc_client1->refreshToken($bookme_gc_client1->getRefreshToken());
                            $wpdb->update($table_all_employee, array('google_data' => $bookme_gc_client1->getAccessToken()), array('id' => $employee), array('%s'), array('%d'));
                        }
                        $bookme_gc_service1 = new Google_Service_Calendar($bookme_gc_client1);
                        $google_calendar_id = 'primary';
                        $gc_calendar1 = $bookme_gc_service1->calendarList->get($google_calendar_id);
                        $gc_access1 = $gc_calendar1->getAccessRole();
                        if (in_array($gc_access1, array('writer', 'owner'))) {
                            if ($gc_event_id == null) {
                                $event_data = array(
                                    'start' => $dates . ' ' . $appointstart,
                                    'end' => $dates . ' ' . $appointend,
                                    'name' => $cname,
                                    'email' => $cemail,
                                    'phone' => $cphone,
                                    'service' => $service_name,
                                    'category' => $category_name,
                                    'employee' => $employee_name,
                                    'service_id' => $service,
                                    'customer_id' => $cid,
                                    'booking_id' => $booking_id
                                );
                                $gc_event = bookme_get_event_data($event_data);

                                $createdEvent = $bookme_gc_service1->events->insert($google_calendar_id, $gc_event);

                                $event_id = $createdEvent->getId();

                                if ($event_id) {
                                    $wpdb->update($table_current_booking, array('google_event_id' => $event_id), array('id' => $booking_id), array('%s'), array('%d'));
                                }
                            } else {
                                $customer_result = $wpdb->get_results("select customer_id from $table_customer_booking where booking_id = $booking_id");
                                $customer_ids = array();
                                $name = array();
                                $email = array();
                                $phone = array();
                                foreach ($customer_result as $customer_id) {
                                    $customer_ids[] = $customer_id->customer_id;
                                    $customer_data = $wpdb->get_results("select name,email,phone from $table_customers where id = $customer_id->customer_id");
                                    $name[] = $customer_data[0]->name;
                                    $email[] = $customer_data[0]->email;
                                    $phone[] = $customer_data[0]->phone;
                                }

                                $event_data = array(
                                    'start' => $dates . ' ' . $appointstart,
                                    'end' => $dates . ' ' . $appointend,
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
                                $bookme_gc_service1->events->update($google_calendar_id, $gc_event_id, $gc_event);
                            }
                        }
                    } catch (Exception $e) {
                        $res['error'] = $e->getMessage();
                    }
                }
            }

        } else {
            $res['response'] = 'full';
        }
    } else {
        $res['response'] = 'booked';
    }

    wp_send_json($res);
}

function bookme_del_bookings()
{

    global $wpdb;
    $table_current_booking = $wpdb->prefix . 'bookme_current_booking';
    $table_customer_booking = $wpdb->prefix . 'bookme_customer_booking';
    $table_payments = $wpdb->prefix . 'bookme_payments';
    $table_all_employee = $wpdb->prefix . 'bookme_employee';
    foreach ($_POST['id'] as $id) {
        $payment_id = $wpdb->get_results("Select payment_id from $table_customer_booking where booking_id=" . $id);
        foreach ($payment_id as $p_id) {
            $wpdb->delete($table_payments, array('id' => $p_id->payment_id), array('%d'));
        }
        $wpdb->delete($table_customer_booking, array('booking_id' => $id), array('%d'));

        /* Google Calendar integration */
        if (bookme_get_settings('bookme_gc_client_id') != null) {
            $gc_staff_result = $wpdb->get_results("select google_event_id,emp_id from $table_current_booking where id = $id ");
            if ($gc_staff_result[0]->google_event_id) {
                include_once plugin_dir_path(__FILE__) . '../includes/google.php';
                $bookme_gc_client = new Google_Client();
                $bookme_gc_client->setClientId(bookme_get_settings('bookme_gc_client_id'));
                $bookme_gc_client->setClientSecret(bookme_get_settings('bookme_gc_client_secret'));
                $google_data1 = $wpdb->get_var("SELECT google_data FROM $table_all_employee WHERE id=" . $gc_staff_result[0]->emp_id);
                if ($google_data1) {
                    try {
                        $bookme_gc_client->setAccessToken($google_data1);
                        if ($bookme_gc_client->isAccessTokenExpired()) {
                            $bookme_gc_client->refreshToken($bookme_gc_client->getRefreshToken());
                            $wpdb->update($table_all_employee, array('google_data' => $bookme_gc_client->getAccessToken()), array('id' => $gc_staff_result[0]->emp_id), array('%s'), array('%d'));
                        }
                        $bookme_gc_service = new Google_Service_Calendar($bookme_gc_client);
                        $google_calendar_id = 'primary';
                        $gc_calendar = $bookme_gc_service->calendarList->get($google_calendar_id);
                        $gc_access = $gc_calendar->getAccessRole();
                        if (in_array($gc_access, array('writer', 'owner'))) {
                            $bookme_gc_service->events->delete($google_calendar_id, $gc_staff_result[0]->google_event_id);

                        }
                    } catch (Exception $e) {
                        echo $e->getMessage();
                    }
                }
            }
        }

        $wpdb->delete($table_current_booking, array('id' => $id), array('%d'));
    }
    echo 1;

}

function bookme_save_cust_data()
{
    global $wpdb;
    $table_customers = $wpdb->prefix . 'bookme_customers';
    $res = array();
    if (isset($_POST['custId'])) {
        $wpdb->update($table_customers, array(
            'name' => $_POST['custName'],
            'email' => $_POST['custEmail'],
            'phone' => $_POST['custPhone'],
            'notes' => $_POST['custNote']
        ),
            array('id' => $_POST['custId']),
            array(
                '%s',
                '%s',
                '%s',
                '%s'
            ),
            array('%d')
        );

        $res['response'] = 'edited';
        echo json_encode($res);

    } else {
        $wpdb->insert($table_customers, array(
            'name' => $_POST['custName'],
            'email' => $_POST['custEmail'],
            'phone' => $_POST['custPhone'],
            'notes' => $_POST['custNote']
        ));

        $res['response'] = 'added';
        $res['id'] = $wpdb->insert_id;
        echo json_encode($res);

    }
}

function bookme_del_customers()
{

    if (isset($_POST['cust_id'])) {
        global $wpdb;
        $table_customers = $wpdb->prefix . 'bookme_customers';
        $table_customer_booking = $wpdb->prefix . 'bookme_customer_booking';
        $table_payments = $wpdb->prefix . 'bookme_payments';
        for ($del = 0; $del < count($_POST['cust_id']); $del++) {
            $wpdb->delete($table_customers, array('id' => $_POST['cust_id'][$del]), array('%d'));
            $payment_id = $wpdb->get_results("Select payment_id from $table_customer_booking where customer_id=" . $_POST['cust_id'][$del]);
            foreach ($payment_id as $p_id) {
                $wpdb->delete($table_payments, array('id' => $p_id->payment_id), array('%d'));
            }
            $wpdb->delete($table_customer_booking, array('customer_id' => $_POST['cust_id'][$del]), array('%d'));
        }
        echo '1';
    } else {
        echo '0';
    }

}

function bookme_del_payments()
{

    if (isset($_POST['pay_id'])) {
        global $wpdb;
        $table_book_payment = $wpdb->prefix . 'bookme_payments';
        for ($del = 0; $del < count($_POST['pay_id']); $del++) {
            $wpdb->delete($table_book_payment, array('id' => $_POST['pay_id'][$del]), array('%d'));
        }
        echo '1';
    } else {
        echo '0';
    }

}

function bookme_complete_payment()
{

    if (isset($_POST['payment_id'])) {
        global $wpdb;
        $table_book_payment = $wpdb->prefix . 'bookme_payments';
        $wpdb->update($table_book_payment, array(
            'status' => 'Completed'
        ),
            array('id' => $_POST['payment_id']),
            array(
                '%s'
            ),
            array('%d')
        );
        echo '1';
    } else {
        echo '0';
    }

}

function bookme_save_coupon()
{
    global $wpdb;
    $table_coupon = $wpdb->prefix . 'bookme_coupons';
    $services = implode(',', $_POST['services']);
    if (isset($_POST['coupon_id'])) {
        $wpdb->update($table_coupon, array(
            'coupon_code' => $_POST['coupan_code'],
            'deduction' => $_POST['coupan_deduction'],
            'discount' => $_POST['coupan_discount'],
            'ser_id' => $services,
            'usage_limit' => $_POST['coupan_limit']
        ),
            array('id' => $_POST['coupon_id']),
            array(
                '%s',
                '%d',
                '%d',
                '%s',
                '%d'
            ),
            array('%d')
        );

        $res['response'] = 'edited';
        echo json_encode($res);

    } else {
        $wpdb->insert($table_coupon, array(
                'coupon_code' => $_POST['coupan_code'],
                'deduction' => $_POST['coupan_deduction'],
                'discount' => $_POST['coupan_discount'],
                'ser_id' => $services,
                'usage_limit' => $_POST['coupan_limit'],
                'coupon_used_limit' => '0'
            )
        );
        $res['response'] = 'added';
        $res['id'] = $wpdb->insert_id;
        echo json_encode($res);

    }
}

function bookme_del_coupon()
{

    if (isset($_POST['coupon_id'])) {
        global $wpdb;
        $table_coupon = $wpdb->prefix . 'bookme_coupons';
        for ($del = 0; $del < count($_POST['coupon_id']); $del++) {
            $wpdb->delete($table_coupon, array('id' => $_POST['coupon_id'][$del]), array('%d'));
        }
        echo '1';
    } else {
        echo '0';
    }

}

function bookme_save_custom_fields()
{

    global $wpdb;
    $table_custom_fields = $wpdb->prefix . 'bookme_custom_field';

    if (isset($_POST['fields'])) {
        $wpdb->query('TRUNCATE TABLE ' . $table_custom_fields);
    } else {
        echo 0;
        wp_die();
    }
    $fields = json_decode(wp_unslash($_POST['fields']), true);
    foreach ($fields as $custom) {
        $type = $custom['type'];
        $title = $custom['label'];
        $required = $custom['required'];
        $position = $custom['position'];

        $wpdb->insert($table_custom_fields, array('field_type' => $type, 'field_name' => $title, 'position' => $position, 'required' => $required, 'status' => 'valid'));
        $lastid = $wpdb->insert_id;


        if ($type == 'checkboxGroup') {
            $fValue = 'checkOption';
        }
        if ($type == 'radioGroup') {
            $fValue = 'radioOption';
        }
        if ($type == 'dropDown') {
            $fValue = 'dropOption';
        }
        if (isset($custom['items'])) {
            for ($i = 0; $i < count($custom['items']); $i++) {
                $wpdb->insert($table_custom_fields, array('field_type' => $fValue, 'field_name' => $custom['items'][$i], 'associate_with' => $lastid, 'status' => 'valid'));
            }
        }
    }

}

function bookme_insert_email_notification()
{

    global $wpdb;
    $table_enotification = $wpdb->prefix . 'bookme_email_notification';
    $table_settings = $wpdb->prefix . 'bookme_settings';

    $resultComp = $wpdb->get_results("SELECT count(*) as comp_count FROM $table_settings WHERE book_key='bookme_email_sender_name'");
    if ($resultComp[0]->comp_count != 0) {
        $wpdb->update($table_settings, array('book_value' => $_POST['sender_name'],), array('book_key' => 'bookme_email_sender_name'), array('%s'), array('%s'));
    } else {
        $wpdb->insert($table_settings, array('book_key' => 'bookme_email_sender_name', 'book_value' => $_POST['sender_name']));
    }

    $resultComp = $wpdb->get_results("SELECT count(*) as comp_count FROM $table_settings WHERE book_key='bookme_email_sender_email'");
    if ($resultComp[0]->comp_count != 0) {
        $wpdb->update($table_settings, array('book_value' => $_POST['sender_email'],), array('book_key' => 'bookme_email_sender_email'), array('%s'), array('%s'));
    } else {
        $wpdb->insert($table_settings, array('book_key' => 'bookme_email_sender_email', 'book_value' => $_POST['sender_email']));
    }

    $resultComp = $wpdb->get_results("SELECT count(*) as comp_count FROM $table_settings WHERE book_key='email_customer'");
    if ($resultComp[0]->comp_count != 0) {
        $wpdb->update($table_settings, array('book_value' => $_POST['customer_mail'],), array('book_key' => 'email_customer'), array('%s'), array('%s'));
    } else {
        $wpdb->insert($table_settings, array('book_key' => 'email_customer', 'book_value' => $_POST['customer_mail']));
    }


    if ($_POST['customer_mail']) {
        $resultComp = $wpdb->get_results("SELECT count(*) as comp_account FROM $table_enotification WHERE key_type='customer_confirm'");
        if ($resultComp[0]->comp_account != 0) {
            $wpdb->update($table_enotification, array('email_value' => $_POST['customer_subject']), array('email_key' => 'customer_sub'), array('%s'), array('%s'));
            $wpdb->update($table_enotification, array('email_value' => stripcslashes($_POST['customer_msg'])), array('email_key' => 'customer_msg'), array('%s'), array('%s'));
        } else {
            $wpdb->insert($table_enotification, array('email_key' => 'customer_sub', 'email_value' => $_POST['customer_subject'], 'key_type' => 'customer_confirm', 'status' => 'valid'));
            $wpdb->insert($table_enotification, array('email_key' => 'customer_msg', 'email_value' => stripcslashes($_POST['customer_msg']), 'key_type' => 'customer_confirm', 'status' => 'valid'));
        }
    }

    $resultComp = $wpdb->get_results("SELECT count(*) as comp_count FROM $table_settings WHERE book_key='email_employee'");
    if ($resultComp[0]->comp_count != 0) {
        $wpdb->update($table_settings, array('book_value' => $_POST['employee_mail'],), array('book_key' => 'email_employee'), array('%s'), array('%s'));
    } else {
        $wpdb->insert($table_settings, array('book_key' => 'email_employee', 'book_value' => $_POST['employee_mail']));
    }

    if ($_POST['employee_mail']) {
        $resultComp = $wpdb->get_results("SELECT count(*) as comp_account FROM $table_enotification WHERE key_type='employee_confirm'");
        if ($resultComp[0]->comp_account != 0) {
            $wpdb->update($table_enotification, array('email_value' => $_POST['employee_subject']), array('email_key' => 'employee_sub'), array('%s'), array('%s'));
            $wpdb->update($table_enotification, array('email_value' => stripcslashes($_POST['employee_msg'])), array('email_key' => 'employee_msg'), array('%s'), array('%s'));
        } else {
            $wpdb->insert($table_enotification, array('email_key' => 'employee_sub', 'email_value' => $_POST['employee_subject'], 'key_type' => 'employee_confirm', 'status' => 'valid'));
            $wpdb->insert($table_enotification, array('email_key' => 'employee_msg', 'email_value' => stripcslashes($_POST['employee_msg']), 'key_type' => 'employee_confirm', 'status' => 'valid'));
        }
    }

    $resultComp = $wpdb->get_results("SELECT count(*) as comp_count FROM $table_settings WHERE book_key='email_admin'");
    if ($resultComp[0]->comp_count != 0) {
        $wpdb->update($table_settings, array('book_value' => $_POST['admin_mail'],), array('book_key' => 'email_admin'), array('%s'), array('%s'));
    } else {
        $wpdb->insert($table_settings, array('book_key' => 'email_admin', 'book_value' => $_POST['admin_mail']));
    }

    if ($_POST['admin_mail']) {
        $resultComp = $wpdb->get_results("SELECT count(*) as comp_account FROM $table_enotification WHERE key_type='admin_confirm'");
        if ($resultComp[0]->comp_account != 0) {
            $wpdb->update($table_enotification, array('email_value' => $_POST['admin_subject']), array('email_key' => 'admin_sub'), array('%s'), array('%s'));
            $wpdb->update($table_enotification, array('email_value' => stripcslashes($_POST['admin_msg'])), array('email_key' => 'admin_msg'), array('%s'), array('%s'));
        } else {
            $wpdb->insert($table_enotification, array('email_key' => 'admin_sub', 'email_value' => $_POST['admin_subject'], 'key_type' => 'admin_confirm', 'status' => 'valid'));
            $wpdb->insert($table_enotification, array('email_key' => 'admin_msg', 'email_value' => stripcslashes($_POST['admin_msg']), 'key_type' => 'admin_confirm', 'status' => 'valid'));
        }
    }
    echo 1;

}

function bookme_insert_sms_notification()
{

    global $wpdb;
    $table_sms_notification = $wpdb->prefix . 'bookme_sms_notification';
    $table_settings = $wpdb->prefix . 'bookme_settings';

    $resultComp = $wpdb->get_results("SELECT count(*) as comp_count FROM $table_settings WHERE book_key='bookme_sms_accountsid'");
    if ($resultComp[0]->comp_count != 0) {
        $wpdb->update($table_settings, array('book_value' => $_POST['account_sid'],), array('book_key' => 'bookme_sms_accountsid'), array('%s'), array('%s'));
    } else {
        $wpdb->insert($table_settings, array('book_key' => 'bookme_sms_accountsid', 'book_value' => $_POST['account_sid']));
    }

    $resultComp = $wpdb->get_results("SELECT count(*) as comp_count FROM $table_settings WHERE book_key='bookme_sms_authtoken'");
    if ($resultComp[0]->comp_count != 0) {
        $wpdb->update($table_settings, array('book_value' => $_POST['auth_token'],), array('book_key' => 'bookme_sms_authtoken'), array('%s'), array('%s'));
    } else {
        $wpdb->insert($table_settings, array('book_key' => 'bookme_sms_authtoken', 'book_value' => $_POST['auth_token']));
    }

    $resultComp = $wpdb->get_results("SELECT count(*) as comp_count FROM $table_settings WHERE book_key='bookme_sms_phone_no'");
    if ($resultComp[0]->comp_count != 0) {
        $wpdb->update($table_settings, array('book_value' => $_POST['phone_no'],), array('book_key' => 'bookme_sms_phone_no'), array('%s'), array('%s'));
    } else {
        $wpdb->insert($table_settings, array('book_key' => 'bookme_sms_phone_no', 'book_value' => $_POST['phone_no']));
    }

    $resultComp = $wpdb->get_results("SELECT count(*) as comp_count FROM $table_settings WHERE book_key='bookme_admin_phone_no'");
    if ($resultComp[0]->comp_count != 0) {
        $wpdb->update($table_settings, array('book_value' => $_POST['admin_phone_no'],), array('book_key' => 'bookme_admin_phone_no'), array('%s'), array('%s'));
    } else {
        $wpdb->insert($table_settings, array('book_key' => 'bookme_admin_phone_no', 'book_value' => $_POST['admin_phone_no']));
    }

    $resultComp = $wpdb->get_results("SELECT count(*) as comp_count FROM $table_settings WHERE book_key='sms_customer'");
    if ($resultComp[0]->comp_count != 0) {
        $wpdb->update($table_settings, array('book_value' => $_POST['customer_sms'],), array('book_key' => 'sms_customer'), array('%s'), array('%s'));
    } else {
        $wpdb->insert($table_settings, array('book_key' => 'sms_customer', 'book_value' => $_POST['customer_sms']));
    }

    $resultComp = $wpdb->get_results("SELECT count(*) as comp_count FROM $table_settings WHERE book_key='sms_employee'");
    if ($resultComp[0]->comp_count != 0) {
        $wpdb->update($table_settings, array('book_value' => $_POST['employee_sms'],), array('book_key' => 'sms_employee'), array('%s'), array('%s'));
    } else {
        $wpdb->insert($table_settings, array('book_key' => 'sms_employee', 'book_value' => $_POST['employee_sms']));
    }

    $resultComp = $wpdb->get_results("SELECT count(*) as comp_count FROM $table_settings WHERE book_key='sms_admin'");
    if ($resultComp[0]->comp_count != 0) {
        $wpdb->update($table_settings, array('book_value' => $_POST['admin_sms'],), array('book_key' => 'sms_admin'), array('%s'), array('%s'));
    } else {
        $wpdb->insert($table_settings, array('book_key' => 'sms_admin', 'book_value' => $_POST['admin_sms']));
    }

    if (isset($_POST['customer_sms'])) {
        $resultComp = $wpdb->get_results("SELECT count(*) as comp_account FROM $table_sms_notification WHERE key_type='customer_confirm'");
        if ($resultComp[0]->comp_account != 0) {
            $wpdb->update($table_sms_notification, array('sms_value' => $_POST['customer_msg']), array('sms_key' => 'customer_msg'), array('%s'), array('%s'));
        } else {
            $wpdb->insert($table_sms_notification, array('sms_key' => 'customer_msg', 'sms_value' => $_POST['customer_msg'], 'key_type' => 'customer_confirm', 'status' => 'valid'));
        }
    }

    if (isset($_POST['employee_sms'])) {
        $resultComp = $wpdb->get_results("SELECT count(*) as comp_account FROM $table_sms_notification WHERE key_type='employee_confirm'");
        if ($resultComp[0]->comp_account != 0) {
            $wpdb->update($table_sms_notification, array('sms_value' => $_POST['employee_msg']), array('sms_key' => 'employee_msg'), array('%s'), array('%s'));
        } else {
            $wpdb->insert($table_sms_notification, array('sms_key' => 'employee_msg', 'sms_value' => $_POST['employee_msg'], 'key_type' => 'employee_confirm', 'status' => 'valid'));
        }
    }

    if (isset($_POST['admin_sms'])) {
        $resultComp = $wpdb->get_results("SELECT count(*) as comp_account FROM $table_sms_notification WHERE key_type='admin_confirm'");
        if ($resultComp[0]->comp_account != 0) {
            $wpdb->update($table_sms_notification, array('sms_value' => $_POST['admin_msg']), array('sms_key' => 'admin_msg'), array('%s'), array('%s'));
        } else {
            $wpdb->insert($table_sms_notification, array('sms_key' => 'admin_msg', 'sms_value' => $_POST['admin_msg'], 'key_type' => 'admin_confirm', 'status' => 'valid'));
        }
    }

    echo 1;

}

function bookme_save_appearance_bullets()
{

    global $wpdb;
    $table_appearance = $wpdb->prefix . 'bookme_appearance';
    $resultComp = $wpdb->get_results("SELECT count(*) as comp_account FROM $table_appearance WHERE appearance_type='bullet'");
    if ($resultComp[0]->comp_account != 0) {
        $wpdb->update($table_appearance, array('label_value' => $_POST['bullet1']), array('label_key' => 'bullet1'), array('%s'), array('%s'));
        $wpdb->update($table_appearance, array('label_value' => $_POST['bullet2']), array('label_key' => 'bullet2'), array('%s'), array('%s'));
        $wpdb->update($table_appearance, array('label_value' => $_POST['bullet_cart']), array('label_key' => 'bullet_cart'), array('%s'), array('%s'));
        $wpdb->update($table_appearance, array('label_value' => $_POST['bullet3']), array('label_key' => 'bullet3'), array('%s'), array('%s'));
        $wpdb->update($table_appearance, array('label_value' => $_POST['bullet4']), array('label_key' => 'bullet4'), array('%s'), array('%s'));
        $wpdb->update($table_appearance, array('label_value' => $_POST['bullet5']), array('label_key' => 'bullet5'), array('%s'), array('%s'));
        echo '1';
    } else {
        $wpdb->insert($table_appearance, array('label_key' => 'bullet1', 'label_value' => $_POST['bullet1'], 'appearance_type' => 'bullet'));
        $wpdb->insert($table_appearance, array('label_key' => 'bullet2', 'label_value' => $_POST['bullet2'], 'appearance_type' => 'bullet'));
        $wpdb->insert($table_appearance, array('label_key' => 'bullet_cart', 'label_value' => $_POST['bullet_cart'], 'appearance_type' => 'bullet'));
        $wpdb->insert($table_appearance, array('label_key' => 'bullet3', 'label_value' => $_POST['bullet3'], 'appearance_type' => 'bullet'));
        $wpdb->insert($table_appearance, array('label_key' => 'bullet4', 'label_value' => $_POST['bullet4'], 'appearance_type' => 'bullet'));
        $wpdb->insert($table_appearance, array('label_key' => 'bullet5', 'label_value' => $_POST['bullet5'], 'appearance_type' => 'bullet'));
        echo '2';
    }

}

function bookme_save_appearance_labels()
{

    global $wpdb;
    $table_appearance = $wpdb->prefix . 'bookme_appearance';
    $resultComp = $wpdb->get_results("SELECT count(*) as comp_account FROM $table_appearance WHERE appearance_type='label'");
    if ($resultComp[0]->comp_account != 0) {
        $wpdb->update($table_appearance, array('label_value' => $_POST['category']), array('label_key' => 'category'), array('%s'), array('%s'));
        $wpdb->update($table_appearance, array('label_value' => $_POST['service']), array('label_key' => 'service'), array('%s'), array('%s'));
        $wpdb->update($table_appearance, array('label_value' => $_POST['employee']), array('label_key' => 'employee'), array('%s'), array('%s'));
        $wpdb->update($table_appearance, array('label_value' => $_POST['number_of_person']), array('label_key' => 'number_of_person'), array('%s'), array('%s'));
        $wpdb->update($table_appearance, array('label_value' => $_POST['availability']), array('label_key' => 'availability'), array('%s'), array('%s'));
        echo '1';
    } else {
        $wpdb->insert($table_appearance, array('label_key' => 'category', 'label_value' => $_POST['category'], 'appearance_type' => 'label'));
        $wpdb->insert($table_appearance, array('label_key' => 'service', 'label_value' => $_POST['service'], 'appearance_type' => 'label'));
        $wpdb->insert($table_appearance, array('label_key' => 'employee', 'label_value' => $_POST['employee'], 'appearance_type' => 'label'));
        $wpdb->insert($table_appearance, array('label_key' => 'number_of_person', 'label_value' => $_POST['number_of_person'], 'appearance_type' => 'label'));
        $wpdb->insert($table_appearance, array('label_key' => 'availability', 'label_value' => $_POST['availability'], 'appearance_type' => 'label'));
        echo '2';
    }

}

function bookme_save_appearance_colors()
{

    global $wpdb;
    $table_appearance = $wpdb->prefix . 'bookme_appearance';
    $resultComp = $wpdb->get_results("SELECT count(*) as comp_account FROM $table_appearance WHERE appearance_type='color'");
    if ($resultComp[0]->comp_account != 0) {
        $wpdb->update($table_appearance, array('label_value' => $_POST['booking_color']), array('label_key' => 'booking_color'), array('%s'), array('%s'));
        $wpdb->update($table_appearance, array('label_value' => $_POST['booking_colortxt']), array('label_key' => 'booking_colortxt'), array('%s'), array('%s'));
        echo '1';
    } else {
        $wpdb->insert($table_appearance, array('label_key' => 'booking_color', 'label_value' => $_POST['booking_color'], 'appearance_type' => 'color'));
        $wpdb->insert($table_appearance, array('label_key' => 'booking_colortxt', 'label_value' => $_POST['booking_colortxt'], 'appearance_type' => 'color'));
        echo '2';
    }

}

function bookme_save_appearance_msg()
{

    global $wpdb;
    $table_appearance = $wpdb->prefix . 'bookme_appearance';
    $resultComp = $wpdb->get_results("SELECT count(*) as comp_account FROM $table_appearance WHERE appearance_type='message'");
    if ($resultComp[0]->comp_account != 0) {
        $wpdb->update($table_appearance, array('label_value' => $_POST['booking_mes']), array('label_key' => 'booking_message'), array('%s'), array('%s'));

        echo '1';
    } else {
        $wpdb->insert($table_appearance, array('label_key' => 'booking_message', 'label_value' => $_POST['booking_mes'], 'appearance_type' => 'message'));
        echo '2';
    }

}

function bookme_save_company_data()
{

    global $wpdb;
    $table_settings = $wpdb->prefix . 'bookme_settings';
    $resultComp = $wpdb->get_results("SELECT count(*) as comp_count FROM $table_settings WHERE book_key='companyName'");
    if ($resultComp[0]->comp_count != 0) {
        $wpdb->update($table_settings, array('book_value' => $_POST['companyName'],), array('book_key' => 'companyName'), array('%s'), array('%s'));
        $wpdb->update($table_settings, array('book_value' => $_POST['companyAddress'],), array('book_key' => 'companyAddress'), array('%s'), array('%s'));
        $wpdb->update($table_settings, array('book_value' => $_POST['companyPhone'],), array('book_key' => 'companyPhone'), array('%s'), array('%s'));
        $wpdb->update($table_settings, array('book_value' => $_POST['companyWebsite'],), array('book_key' => 'companyWebsite'), array('%s'), array('%s'));
        echo '1';
    } else {
        $wpdb->insert($table_settings, array('book_key' => 'companyName', 'book_value' => $_POST['companyName']));
        $wpdb->insert($table_settings, array('book_key' => 'companyAddress', 'book_value' => $_POST['companyAddress']));
        $wpdb->insert($table_settings, array('book_key' => 'companyPhone', 'book_value' => $_POST['companyPhone']));
        $wpdb->insert($table_settings, array('book_key' => 'companyWebsite', 'book_value' => $_POST['companyWebsite']));
        echo '2';
    }

}

function bookme_save_gen_settings()
{

    global $wpdb;
    $table_settings = $wpdb->prefix . 'bookme_settings';
    $resultE = $wpdb->get_results("SELECT count(*) as book_count FROM $table_settings WHERE book_key='enable_coupan'");
    if ($resultE[0]->book_count != 0) {
        $wpdb->update($table_settings, array('book_value' => $_POST['coupan'],), array('book_key' => 'enable_coupan'), array('%s'), array('%s'));
    } else {
        $wpdb->insert($table_settings, array(
            'book_key' => 'enable_coupan',
            'book_value' => $_POST['coupan']));
    }
    $resultE = $wpdb->get_results("SELECT count(*) as book_count FROM $table_settings WHERE book_key='bookmeDayLimit'");
    $day_limit = $_POST['day_limit'];
    if ($resultE[0]->book_count != 0) {
        $wpdb->update($table_settings, array('book_value' => $day_limit,), array('book_key' => 'bookmeDayLimit'), array('%s'), array('%s'));
    } else {
        $wpdb->insert($table_settings, array(
            'book_key' => 'bookmeDayLimit',
            'book_value' => $day_limit));
    }
    echo 1;

}

function bookme_save_payment_details()
{
    global $wpdb;
    $table_settings = $wpdb->prefix . 'bookme_settings';
    $resultComp = $wpdb->get_results("SELECT count(*) as pay_account FROM $table_settings WHERE book_key='payment_pmt'");
    if ($resultComp[0]->pay_account != 0) {
        /*----delete when update-------------*/
        $wpdb->delete($table_settings, array('book_key' => 'pmt_currency'), array('%s'));
        $wpdb->delete($table_settings, array('book_key' => 'pmt_local'), array('%s'));
        $wpdb->delete($table_settings, array('book_key' => 'pmt_paypal'), array('%s'));
        $wpdb->delete($table_settings, array('book_key' => 'pmt_paypal_api_username'), array('%s'));
        $wpdb->delete($table_settings, array('book_key' => 'pmt_paypal_api_password'), array('%s'));
        $wpdb->delete($table_settings, array('book_key' => 'pmt_paypal_api_signature'), array('%s'));
        $wpdb->delete($table_settings, array('book_key' => 'pmt_paypal_sandbox'), array('%s'));
        $wpdb->delete($table_settings, array('book_key' => 'pmt_stripe'), array('%s'));
        $wpdb->delete($table_settings, array('book_key' => 'pmt_stripe_secret_key'), array('%s'));
        /*----insert when update-------------*/
        $wpdb->insert($table_settings, array('book_key' => 'pmt_currency', 'book_value' => $_POST['pmt_currency']));
        $wpdb->insert($table_settings, array('book_key' => 'pmt_local', 'book_value' => $_POST['pmt_local']));

        $wpdb->insert($table_settings, array('book_key' => 'pmt_paypal', 'book_value' => $_POST['pmt_paypal']));
        if ($_POST['pmt_paypal'] == 'ec') {
            $wpdb->insert($table_settings, array('book_key' => 'pmt_paypal', 'book_value' => $_POST['pmt_paypal']));
            $wpdb->insert($table_settings, array('book_key' => 'pmt_paypal_api_username', 'book_value' => $_POST['pmt_paypal_api_username']));
            $wpdb->insert($table_settings, array('book_key' => 'pmt_paypal_api_password', 'book_value' => $_POST['pmt_paypal_api_password']));
            $wpdb->insert($table_settings, array('book_key' => 'pmt_paypal_api_signature', 'book_value' => $_POST['pmt_paypal_api_signature']));
            $wpdb->insert($table_settings, array('book_key' => 'pmt_paypal_sandbox', 'book_value' => $_POST['pmt_paypal_sandbox']));
        }

        $wpdb->insert($table_settings, array('book_key' => 'pmt_stripe', 'book_value' => $_POST['pmt_stripe']));
        if ($_POST['pmt_stripe'] == 'enabled') {
            $wpdb->insert($table_settings, array('book_key' => 'pmt_stripe_secret_key', 'book_value' => $_POST['pmt_stripe_secret_key']));
        }
        echo '1';
    } else {
        $wpdb->insert($table_settings, array('book_key' => 'payment_pmt', 'book_value' => $_POST['payment_pmt']));
        $wpdb->insert($table_settings, array('book_key' => 'pmt_currency', 'book_value' => $_POST['pmt_currency']));
        $wpdb->insert($table_settings, array('book_key' => 'pmt_local', 'book_value' => $_POST['pmt_local']));
        if ($_POST['pmt_paypal'] == 'disabled') {
            $wpdb->insert($table_settings, array('book_key' => 'pmt_paypal', 'book_value' => $_POST['pmt_paypal']));
        } else {
            $wpdb->insert($table_settings, array('book_key' => 'pmt_paypal', 'book_value' => $_POST['pmt_paypal']));
            $wpdb->insert($table_settings, array('book_key' => 'pmt_paypal_api_username', 'book_value' => $_POST['pmt_paypal_api_username']));
            $wpdb->insert($table_settings, array('book_key' => 'pmt_paypal_api_password', 'book_value' => $_POST['pmt_paypal_api_password']));
            $wpdb->insert($table_settings, array('book_key' => 'pmt_paypal_api_signature', 'book_value' => $_POST['pmt_paypal_api_signature']));
            $wpdb->insert($table_settings, array('book_key' => 'pmt_paypal_sandbox', 'book_value' => $_POST['pmt_paypal_sandbox']));
        }

        $wpdb->insert($table_settings, array('book_key' => 'pmt_stripe', 'book_value' => $_POST['pmt_stripe']));
        if ($_POST['pmt_stripe'] == 'enabled') {
            $wpdb->insert($table_settings, array('book_key' => 'pmt_stripe_secret_key', 'book_value' => $_POST['pmt_stripe_secret_key']));
        }
        echo '2';
    }

}

function bookme_get_calender_for_emp()
{
    global $wpdb;
    $table_book_service = $wpdb->prefix . 'bookme_service';
    $table_all_employee = $wpdb->prefix . 'bookme_employee';
    $table_current_booking = $wpdb->prefix . 'bookme_current_booking';
    $table_customers = $wpdb->prefix . 'bookme_customers';
    $table_customer_booking = $wpdb->prefix . 'bookme_customer_booking';

    if (isset($_POST['emp_id'])) {
        $where = 'where b.emp_id=' . $_POST['emp_id'];
    } else {
        $where = '';
    }

    $res = $wpdb->get_results("SELECT group_concat(cb.status) status, group_concat(c.name) name, group_concat(c.phone) phone, group_concat(c.email) email, group_concat(c.notes) notes, b.duration, b.date, b.time, s.name ser_name, s.capacity ser_capacity FROM $table_customer_booking cb LEFT JOIN $table_customers c ON c.id = cb.customer_id LEFT JOIN $table_current_booking b ON cb.booking_id = b.id LEFT JOIN $table_book_service s ON b.ser_id = s.id $where GROUP BY cb.booking_id ORDER BY b.id");
    $result = array();
    $i = 0;
    foreach ($res as $val) {
        $name = explode(',', $val->name);
        $email = explode(',', $val->email);
        $phone = explode(',', $val->phone);
        $notes = explode(',', $val->notes);
        $status = explode(',', $val->status);
        $result[$i]['title'] = '<div><strong>' . __('Service:', 'bookme') . '</strong>' . $val->ser_name . '</div><br>';
        if (count($name) == 1) {
            $result[$i]['title'] .= '<div><strong>' . __('Customer Name:', 'bookme') . '</strong>' . $name[0] . ' </div> <div> <strong>' . __('Email:', 'bookme') . '</strong> ' . $email[0] . '</div><div><strong>' . __('Phone:', 'bookme') . '</strong> ' . $phone[0] . '</div><div><strong>' . __('Status:', 'bookme') . '</strong> ' . $status[0] . '</div><div><strong>' . __('Notes:', 'bookme') . '</strong> ' . $notes[0] . '</div><br>';
        }
        $result[$i]['title'] .= '<div><strong>' . __('No of booking:', 'bookme') . '</strong> ' . count($name) . '</div><div><strong>' . __('Capacity:', 'bookme') . '</strong> ' . $val->ser_capacity . '</div>';

        $result[$i]['start'] = date('D, d M Y H:i:s', strtotime($val->date . ' ' . $val->time));
        $result[$i]['end'] = date('D, d M Y H:i:s', strtotime(($val->date . ' ' . $val->time)) + $val->duration);
        $i++;
    }
    wp_send_json($result);
}

function bookme_save_dayoff()
{

    global $wpdb;
    $table_holidays = $wpdb->prefix . 'bookme_holidays';

    $id = $_POST['id'];
    $holiday = $_POST['day_off'] == 'true';
    $repeat = $_POST['day_off_repeat'] == 'true';
    $day = $_POST['date'];
    $staff_id = $_POST['staff_id'];
    if ($staff_id) {
        if ($id) {
            if ($holiday) {
                $wpdb->update($table_holidays, array('repeat_day' => (int)$repeat), array('id' => $id), array('%d'));
            } else {
                $wpdb->delete($table_holidays, array('id' => $id), array('%d'));
            }
        } elseif ($holiday && $day) {
            $time = strtotime($day);
            $newformat = date('Y-m-d', $time);

            $wpdb->insert($table_holidays, array('holi_date' => $newformat, 'repeat_day' => (int)$repeat, 'staff_id' => $staff_id), array('%s', '%d', '%d'));
        }

        echo json_encode(bookme_get_daysoff_by_staff_id($staff_id));
    } else {
        echo 0;
    }

}

function bookme_save_woocommerce()
{

    global $wpdb;
    $table_settings = $wpdb->prefix . 'bookme_settings';
    $resultE = $wpdb->get_results("SELECT count(*) as book_count FROM $table_settings WHERE book_key='enable_woocommerce'");
    if ($resultE[0]->book_count != 0) {
        $wpdb->update($table_settings, array('book_value' => $_POST['enable']), array('book_key' => 'enable_woocommerce'), array('%s'), array('%s'));
    } else {
        $wpdb->insert($table_settings, array(
            'book_key' => 'enable_woocommerce',
            'book_value' => $_POST['enable']));
    }

    $resultS = $wpdb->get_results("SELECT count(*) as book_count FROM $table_settings WHERE book_key='woocommerce_product'");
    if ($resultS[0]->book_count != 0) {
        $wpdb->update($table_settings, array('book_value' => $_POST['product']), array('book_key' => 'woocommerce_product'), array('%s'), array('%s'));
    } else {
        $wpdb->insert($table_settings, array(
            'book_key' => 'woocommerce_product',
            'book_value' => $_POST['product']));
    }

    $resultS = $wpdb->get_results("SELECT count(*) as book_count FROM $table_settings WHERE book_key='woocommerce_cart_data'");
    if ($resultS[0]->book_count != 0) {
        $wpdb->update($table_settings, array('book_value' => $_POST['cart_data']), array('book_key' => 'woocommerce_cart_data'), array('%s'), array('%s'));
    } else {
        $wpdb->insert($table_settings, array(
            'book_key' => 'woocommerce_cart_data',
            'book_value' => $_POST['cart_data']));
    }

    $resultS = $wpdb->get_results("SELECT count(*) as book_count FROM $table_settings WHERE book_key='woocommerce_cart_data_text'");
    if ($resultS[0]->book_count != 0) {
        $wpdb->update($table_settings, array('book_value' => $_POST['cart_data_text']), array('book_key' => 'woocommerce_cart_data_text'), array('%s'), array('%s'));
    } else {
        $wpdb->insert($table_settings, array(
            'book_key' => 'woocommerce_cart_data_text',
            'book_value' => $_POST['cart_data_text']));
    }

    echo 1;

}

function bookme_save_cart_settings()
{

    global $wpdb;
    $table_settings = $wpdb->prefix . 'bookme_settings';

    $resultS = $wpdb->get_results("SELECT count(*) as book_count FROM $table_settings WHERE book_key='bookme_enable_cart'");
    if ($resultS[0]->book_count != 0) {
        $wpdb->update($table_settings, array('book_value' => $_POST['cart']), array('book_key' => 'bookme_enable_cart'), array('%s'), array('%s'));
    } else {
        $wpdb->insert($table_settings, array(
            'book_key' => 'bookme_enable_cart',
            'book_value' => $_POST['cart']));
    }
    echo 1;

}

function bookme_save_google_calendar()
{

    global $wpdb;
    $table_settings = $wpdb->prefix . 'bookme_settings';

    $resultS = $wpdb->get_results("SELECT count(*) as book_count FROM $table_settings WHERE book_key='bookme_gc_client_id'");
    if ($resultS[0]->book_count != 0) {
        $wpdb->update($table_settings, array('book_value' => $_POST['gc_client_id']), array('book_key' => 'bookme_gc_client_id'), array('%s'), array('%s'));
    } else {
        $wpdb->insert($table_settings, array(
            'book_key' => 'bookme_gc_client_id',
            'book_value' => $_POST['gc_client_id']));
    }

    $resultS = $wpdb->get_results("SELECT count(*) as book_count FROM $table_settings WHERE book_key='bookme_gc_client_secret'");
    if ($resultS[0]->book_count != 0) {
        $wpdb->update($table_settings, array('book_value' => $_POST['gc_client_secret']), array('book_key' => 'bookme_gc_client_secret'), array('%s'), array('%s'));
    } else {
        $wpdb->insert($table_settings, array(
            'book_key' => 'bookme_gc_client_secret',
            'book_value' => $_POST['gc_client_secret']));
    }

    $resultS = $wpdb->get_results("SELECT count(*) as book_count FROM $table_settings WHERE book_key='bookme_gc_2_way_sync'");
    if ($resultS[0]->book_count != 0) {
        $wpdb->update($table_settings, array('book_value' => $_POST['gc_2_way_sync']), array('book_key' => 'bookme_gc_2_way_sync'), array('%s'), array('%s'));
    } else {
        $wpdb->insert($table_settings, array(
            'book_key' => 'bookme_gc_2_way_sync',
            'book_value' => $_POST['gc_2_way_sync']));
    }

    $resultS = $wpdb->get_results("SELECT count(*) as book_count FROM $table_settings WHERE book_key='bookme_gc_limit_events'");
    if ($resultS[0]->book_count != 0) {
        $wpdb->update($table_settings, array('book_value' => $_POST['gc_limit_events']), array('book_key' => 'bookme_gc_limit_events'), array('%s'), array('%s'));
    } else {
        $wpdb->insert($table_settings, array(
            'book_key' => 'bookme_gc_limit_events',
            'book_value' => $_POST['gc_limit_events']));
    }

    $resultS = $wpdb->get_results("SELECT count(*) as book_count FROM $table_settings WHERE book_key='bookme_gc_event_title'");
    if ($resultS[0]->book_count != 0) {
        $wpdb->update($table_settings, array('book_value' => $_POST['gc_event_title']), array('book_key' => 'bookme_gc_event_title'), array('%s'), array('%s'));
    } else {
        $wpdb->insert($table_settings, array(
            'book_key' => 'bookme_gc_event_title',
            'book_value' => $_POST['gc_event_title']));
    }
    echo 1;

}