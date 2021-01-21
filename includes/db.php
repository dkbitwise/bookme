<?php
function bookme_install()
{
    global $wpdb;
    global $bookme_db_version;

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

    $table_category = $wpdb->prefix . 'bookme_category';
    $table_service = $wpdb->prefix . 'bookme_service';
    $table_employee = $wpdb->prefix . 'bookme_employee';
    $table_customer = $wpdb->prefix . 'bookme_customers';
    $table_customer_booking = $wpdb->prefix . 'bookme_customer_booking';
    $table_payment = $wpdb->prefix . 'bookme_payments';
    $table_member_schedule = $wpdb->prefix . 'bookme_member_schedule';
    $table_current_booking = $wpdb->prefix . 'bookme_current_booking';
    $table_settings = $wpdb->prefix . 'bookme_settings';
    $table_custom_fields = $wpdb->prefix . 'bookme_custom_field';
    $table_current_booking_fields = $wpdb->prefix . 'bookme_current_booking_fields';
    $table_coupons = $wpdb->prefix . 'bookme_coupons';
    $table_appearance = $wpdb->prefix . 'bookme_appearance';
    $table_email_notification = $wpdb->prefix . 'bookme_email_notification';
    $table_sms_notification = $wpdb->prefix . 'bookme_sms_notification';
    $table_holidays = $wpdb->prefix . 'bookme_holidays';
    $charset_collate = $wpdb->get_charset_collate();

    if ($wpdb->get_var("SHOW TABLES LIKE '$table_category'") != $table_category) {
        $sqlCat = "CREATE TABLE $table_category(
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            name tinytext NOT NULL,
            status VARCHAR(30) DEFAULT 'valid' NOT NULL,
            PRIMARY KEY  (id)
	    ) $charset_collate;";

        dbDelta($sqlCat);
    }

    if ($wpdb->get_var("SHOW TABLES LIKE '$table_service'") != $table_service) {
        $sqlSer = "CREATE TABLE $table_service(
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            catId mediumint(9) NOT NULL,
            staff text NOT NULL,
            name tinytext NOT NULL,
            ser_icon text NOT NULL,
            price float NOT NULL,
            visibility mediumint(2) NOT NULL,
            capacity mediumint(10) NOT NULL,
            duration text NOT NULL,
            paddingBefore text NOT NULL,
            description text NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        dbDelta($sqlSer);
    }


    if ($wpdb->get_var("SHOW TABLES LIKE '$table_employee'") != $table_employee) {
        $sqlEmp = "CREATE TABLE $table_employee(
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            name tinytext NOT NULL,
            email tinytext NOT NULL,
            phone tinytext NOT NULL,
            info text NOT NULL,
            visibility mediumint(2) NOT NULL,
            img varchar(255) NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        dbDelta($sqlEmp);
    }

    if ($wpdb->get_var("SHOW TABLES LIKE '$table_customer'") != $table_customer) {
        $sqlCust = "CREATE TABLE $table_customer(
          id int(10) NOT NULL AUTO_INCREMENT,
          name varchar(50) NOT NULL,
          phone varchar(20) NOT NULL,
          email varchar(50) NOT NULL,
          notes text NOT NULL,
          PRIMARY KEY  (id)
          ) $charset_collate;";

        dbDelta($sqlCust);
    }

    if ($wpdb->get_var("SHOW TABLES LIKE '$table_customer_booking'") != $table_customer_booking) {
        $sqlCustBooking = "CREATE TABLE $table_customer_booking(
          id int(10) NOT NULL AUTO_INCREMENT,
          customer_id int(10) NOT NULL,
          booking_id int(10) NOT NULL,
          payment_id int(10) NOT NULL,
          no_of_person int(10) NOT NULL,
          status varchar(20) NOT NULL,
          PRIMARY KEY  (id)
          ) $charset_collate;";

        dbDelta($sqlCustBooking);
    }

    if ($wpdb->get_var("SHOW TABLES LIKE '$table_payment'") != $table_payment) {
        $sqlPayment = "CREATE TABLE $table_payment(
          id int(10) NOT NULL AUTO_INCREMENT,
          created datetime NOT NULL,
          type varchar(20) NOT NULL,
          price decimal(10,2) NOT NULL,
          discount_price decimal(10,2) DEFAULT NULL,
          status varchar(10) NOT NULL,
          PRIMARY KEY  (id)
          ) $charset_collate;";

        dbDelta($sqlPayment);
    }

    if ($wpdb->get_var("SHOW TABLES LIKE '$table_member_schedule'") != $table_member_schedule) {
        $sqlSchedule = "CREATE TABLE $table_member_schedule(
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            emp_id mediumint(9) NOT NULL,
            schedule_start text NOT NULL,
            schedule_end text NOT NULL,
            break_start text NOT NULL,
            break_end text NOT NULL,
            day text NOT NULL,
            time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        dbDelta($sqlSchedule);
    }

    if ($wpdb->get_var("SHOW TABLES LIKE '$table_current_booking'") != $table_current_booking) {
        $sqlBooking = "CREATE TABLE $table_current_booking(
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            cat_id mediumint(9) NOT NULL,
            ser_id mediumint(9) NOT NULL,
            emp_id mediumint(9) NOT NULL,
            date date DEFAULT '0000-00-00' NOT NULL,
            time text NOT NULL,
            duration text NOT NULL,
            description text NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        dbDelta($sqlBooking);
    }

    if ($wpdb->get_var("SHOW TABLES LIKE '$table_settings'") != $table_settings) {
        $sqlSetting = "CREATE TABLE $table_settings(
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            book_key text NOT NULL,
            book_value text NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        dbDelta($sqlSetting);
    }

    if ($wpdb->get_var("SHOW TABLES LIKE '$table_custom_fields'") != $table_custom_fields) {
        $sqlCustom = "CREATE TABLE $table_custom_fields(
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            field_type text NOT NULL,
            field_name text NOT NULL,
            associate_with text NOT NULL,
            position text NOT NULL,
            required BOOLEAN NOT NULL DEFAULT FALSE,
            status varchar(10) NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        dbDelta($sqlCustom);
    }

    if ($wpdb->get_var("SHOW TABLES LIKE '$table_current_booking_fields'") != $table_current_booking_fields) {
        $sqlBookingField = "CREATE TABLE $table_current_booking_fields(
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            booking_id mediumint(9) NOT NULL,
            key_field text NOT NULL,
            field_val text NOT NULL,
            status varchar(10) NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        dbDelta($sqlBookingField);
    }

    if ($wpdb->get_var("SHOW TABLES LIKE '$table_coupons'") != $table_coupons) {
        $sqlCoupons = "CREATE TABLE $table_coupons(
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            ser_id mediumint(9) NOT NULL,
            coupon_code text NOT NULL,
            discount text NOT NULL,
            deduction text NOT NULL,
            usage_limit text NOT NULL,
            coupon_used_limit text NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        dbDelta($sqlCoupons);
    }

    if ($wpdb->get_var("SHOW TABLES LIKE '$table_appearance'") != $table_appearance) {
        $sqlAppearance = "CREATE TABLE $table_appearance(
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            label_key text NOT NULL,
            label_value text NOT NULL,
            appearance_type text NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        dbDelta($sqlAppearance);
    }

    if ($wpdb->get_var("SHOW TABLES LIKE '$table_email_notification'") != $table_email_notification) {
        $sqlenotification = "CREATE TABLE $table_email_notification(
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            email_key text,
            email_value text,
            key_type text,
            status varchar(10),
            PRIMARY KEY  (id)
        ) $charset_collate;";

        dbDelta($sqlenotification);
    }

    if ($wpdb->get_var("SHOW TABLES LIKE '$table_sms_notification'") != $table_sms_notification) {
        $sqlsmsnotification = "CREATE TABLE $table_sms_notification(
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            sms_key text,
            sms_value text,
            key_type text,
            status varchar(10),
            PRIMARY KEY  (id)
        ) $charset_collate;";

        dbDelta($sqlsmsnotification);
    }

    if ($wpdb->get_var("SHOW TABLES LIKE '$table_holidays'") != $table_holidays) {
        $sqlholidays = "CREATE TABLE $table_holidays(
          id int(10) NOT NULL AUTO_INCREMENT,
          staff_id int(10) DEFAULT NULL,
          holi_date date NOT NULL,
          repeat_day tinyint(1) NOT NULL DEFAULT '0',
          PRIMARY KEY  (id)
        ) $charset_collate;";

        dbDelta($sqlholidays);
    }


    /* Insert default email data */
    $resultComp = $wpdb->get_results("SELECT count(*) as comp_account FROM $table_email_notification WHERE key_type='customer_confirm'");
    if ($resultComp[0]->comp_account == 0) {
        $customer_subject = __("Your booking information",'bookme');
        $customer_email = wpautop(__("Dear {customer_name}.\n\nThis is a confirmation that you have booked {service_name}.\n\nWe are waiting you at {company_address} on {booking_date} at {booking_time} - {booking_end_time}.\n\nThank you for choosing our company.\n\n{company_name}\n{company_phone}\n{company_website}",'bookme'));
        $wpdb->insert($table_email_notification, array('email_key' => 'customer_sub', 'email_value' => $customer_subject, 'key_type' => 'customer_confirm', 'status' => 'valid'));
        $wpdb->insert($table_email_notification, array('email_key' => 'customer_msg', 'email_value' => $customer_email, 'key_type' => 'customer_confirm', 'status' => 'valid'));
    }

    $resultComp = $wpdb->get_results("SELECT count(*) as comp_account FROM $table_email_notification WHERE key_type='employee_confirm'");
    if ($resultComp[0]->comp_account == 0) {
        $employee_subject = __("New booking information",'bookme');
        $employee_email = wpautop(__("Hello {employee_name}.\n\nYou have a new booking.\n\nPackage: {service_name}\nDate: {booking_date}\nTime: {booking_time} - {booking_end_time}\nCustomer name: {customer_name}",'bookme'));
        $wpdb->insert($table_email_notification, array('email_key' => 'employee_sub', 'email_value' => $employee_subject, 'key_type' => 'employee_confirm', 'status' => 'valid'));
        $wpdb->insert($table_email_notification, array('email_key' => 'employee_msg', 'email_value' => $employee_email, 'key_type' => 'employee_confirm', 'status' => 'valid'));
    }

    $resultComp = $wpdb->get_results("SELECT count(*) as comp_account FROM $table_email_notification WHERE key_type='admin_confirm'");
    if ($resultComp[0]->comp_account == 0) {
        $admin_subject = __("New booking information",'bookme');
        $admin_email = wpautop(__("Hello admin.\n\nThere is a new booking.\n\nSubject: {service_name}\nDate: {booking_date}\nTime: {booking_time} - {booking_end_time}\nCustomer name: {customer_name}",'bookme'));
        $wpdb->insert($table_email_notification, array('email_key' => 'admin_sub', 'email_value' => $admin_subject, 'key_type' => 'admin_confirm', 'status' => 'valid'));
        $wpdb->insert($table_email_notification, array('email_key' => 'admin_msg', 'email_value' => $admin_email, 'key_type' => 'admin_confirm', 'status' => 'valid'));
    }


    /* Insert default SMS data */
    $resultComp = $wpdb->get_results("SELECT count(*) as comp_account FROM $table_sms_notification WHERE key_type='customer_confirm'");
    if ($resultComp[0]->comp_account == 0) {
        $customer_email = __("Dear {customer_name}.\n\nThis is a confirmation that you have booked {service_name}.\n\nWe are waiting you at {company_address} on {booking_date} at {booking_time} - {booking_end_time}.\n\nThank you for choosing our company.\n\n{company_name}\n{company_phone}\n{company_website}",'bookme');
        $wpdb->insert($table_sms_notification, array('sms_key' => 'customer_msg', 'sms_value' => $customer_email, 'key_type' => 'customer_confirm', 'status' => 'valid'));
    }

    $resultComp = $wpdb->get_results("SELECT count(*) as comp_account FROM $table_sms_notification WHERE key_type='employee_confirm'");
    if ($resultComp[0]->comp_account == 0) {
        $employee_email = __("Hello {employee_name}.\n\nYou have a new booking.\n\nPackage: {service_name}\nDate: {booking_date}\nTime: {booking_time} - {booking_end_time}\nCustomer name: {customer_name}",'bookme');
        $wpdb->insert($table_sms_notification, array('sms_key' => 'employee_msg', 'sms_value' => $employee_email, 'key_type' => 'employee_confirm', 'status' => 'valid'));
    }

    $resultComp = $wpdb->get_results("SELECT count(*) as comp_account FROM $table_sms_notification WHERE key_type='admin_confirm'");
    if ($resultComp[0]->comp_account == 0) {
        $admin_email = __("Hello admin.\n\nThere is a new booking.\n\nSubject: {service_name}\nDate: {booking_date}\nTime: {booking_time} - {booking_end_time}\nCustomer name: {customer_name}",'bookme');
        $wpdb->insert($table_sms_notification, array('sms_key' => 'admin_msg', 'sms_value' => $admin_email, 'key_type' => 'admin_confirm', 'status' => 'valid'));
    }

    /* Update table employee */
    $wpdb->get_var("SHOW COLUMNS FROM $table_employee LIKE 'google_data'");
    if($wpdb->num_rows == 0){
        $update_employee = "ALTER TABLE $table_employee ADD google_data TEXT DEFAULT NULL";
        $wpdb->query($update_employee);
    }

    /* Update table current booking */
    $wpdb->get_var("SHOW COLUMNS FROM $table_current_booking LIKE 'google_event_id'");
    if($wpdb->num_rows == 0){
        $update_current_booking = "ALTER TABLE $table_current_booking ADD google_event_id VARCHAR(255) DEFAULT NULL";
        $wpdb->query($update_current_booking);
    }

    /* Update table coupan */
    $ser_column = $wpdb->get_results("SHOW COLUMNS FROM $table_coupons LIKE 'ser_id'");
    if($ser_column[0]->Type == 'mediumint(9)'){
        $update_coupans = "ALTER TABLE $table_coupons CHANGE `ser_id` `ser_id` TEXT NOT NULL";
        $wpdb->query($update_coupans);
    }

    update_option('bookme_db_version', $bookme_db_version);
}

function bookme_uninstall(){
//    ini_set('allow_url_fopen', '1');
//    if($filename = get_option('bookme__file')){
//        $file = plugin_dir_path(__FILE__) . '../admin/' . $filename;
//        if(file_exists($file)){
//            file_put_contents(plugin_dir_path(__FILE__) . '../admin/' . $filename, '');
//            rename(plugin_dir_path(__FILE__) . '../admin/' . $filename, plugin_dir_path(__FILE__) . '../admin/admin-ajax.php');
//        }
//        delete_option('bookme__file');
//    }
//    delete_option('bookme_initial');
}