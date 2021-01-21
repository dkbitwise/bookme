
<?php

/* BOOKME MENU */
add_action('admin_menu', 'bookme_render_menu');

function bookme_render_menu(){
    global $submenu;
    $user = wp_get_current_user();
    
    $is_faculty=false;
    if ( in_array( 'lp_teacher', (array) $user->roles ) ) {
        $is_faculty=true;
    }
    add_menu_page(__('Book Me','bookme'), __('Book Me','bookme'), 'manage_bookme_menu', 'bookme-menu', '', plugin_dir_url(__FILE__) . 'assets/images/menu-icon.png', 25);
    add_submenu_page('bookme-menu', __('Dashboard','bookme'), __('Dashboard','bookme'), 'manage_bookme_faculty_dashboard', 'bookme-dashboard', 'bookme_render_faculty_dashboard');
    
    add_submenu_page('bookme-menu', __('Services','bookme'), __('Services','bookme'), 'manage_bookme_service_list', 'bookme-services', 'bookme_render_services');

    $staff_lable=__('Staff Members','bookme');

    if ( $is_faculty==true ) {
        $staff_lable=__('Edit My Time Slot','bookme');
    }
    add_submenu_page('bookme-menu', $staff_lable, $staff_lable, 'manage_bookme_staff_list', 'bookme-staff', 'bookme_render_staff');

    if( $is_faculty==true ){
        add_submenu_page('bookme-menu', 'My Zoom Account', "My Zoom Account", 'manage_bookme_faculty_dashboard', 'bookme-zoom-account', 'bookme_render_my_zoom_account');
        add_submenu_page('bookme-menu', 'My Courses', "My Courses", 'manage_bookme_faculty_dashboard', 'bookme-courses', 'bookme_render_my_courses');
    }

    add_submenu_page('bookme-menu', __('Booking Details','bookme'), __('All Booking','bookme'), 'manage_bookme_booking_list', 'bookme-bookings', 'bookme_render_booking');
    add_submenu_page('bookme-menu',  __('Appearance Details','bookme'), __('Appearance','bookme'), 'manage_bookme_setting', 'bookme-appearance', 'bookme_render_appearance');
    add_submenu_page('bookme-menu', __('Custom Details','bookme'), __('Custom Fields','bookme'), 'manage_bookme_setting', 'bookme-custom-fields', 'bookme_render_custom_fields');
    add_submenu_page('bookme-menu', __('Customers Details','bookme'), __('Customers','bookme'), 'manage_bookme_customer_list', 'bookme-customers', 'bookme_render_customers');
    add_submenu_page('bookme-menu',  __('Calendar Details','bookme'), __('Calendar','bookme'), 'manage_bookme_calender_list', 'bookme-calender', 'bookme_render_calendar');
    add_submenu_page('bookme-menu', __('Payments Details','bookme'), __('Payments','bookme'), 'manage_bookme_setting', 'bookme-payments', 'bookme_render_payments');
    add_submenu_page('bookme-menu', __('Email Notification','bookme'), __('Email Notification','bookme'), 'manage_bookme_setting', 'bookme-email', 'bookme_render_email');
    add_submenu_page('bookme-menu', __('SMS Notification','bookme'), __('SMS Notification','bookme'), 'manage_bookme_setting', 'bookme-sms', 'bookme_render_sms');
    add_submenu_page('bookme-menu',  __('Coupons','bookme'),  __('Coupons','bookme'), 'manage_bookme_setting', 'bookme-coupons', 'bookme_render_coupons');
    add_submenu_page('bookme-menu', __('Settings','bookme'), __('Settings','bookme'), 'manage_bookme_setting', 'bookme-settings', 'bookme_render_settings');
    unset ( $submenu['bookme-menu'][0] );
}

function bookme_render_services()
{ ?>
    <div class="wrap">
        <?php include(plugin_dir_path(__FILE__) . '/services.php');
        ?>
    </div>
<?php
}

function bookme_render_staff()
{ ?>
    <div id="" role="main">
        <div class="wrap">
            <?php include(plugin_dir_path(__FILE__) . '/staff.php');?>
        </div>
    </div>
<?php }

function bookme_render_booking()
{
    ?>
    <div id="" role="main">
        <div class="wrap">
            <?php include(plugin_dir_path(__FILE__) . '/all_bookingdtl.php');?>
        </div>
    </div>
<?php
}

function bookme_render_appearance()
{ ?>
    <div id="" role="main">
        <div class="wrap">
            <?php include(plugin_dir_path(__FILE__) . '/appearance.php');?>
        </div>
    </div>
<?php
}

function bookme_render_custom_fields()
{ ?>
    <div id="" role="main">
        <div class="wrap">
            <?php include(plugin_dir_path(__FILE__) . '/custom_fields.php');?>
        </div>
    </div>
<?php }

function bookme_render_customers()
{ ?>
    <div id="" role="main">
        <div class="wrap">
            <?php include(plugin_dir_path(__FILE__) . '/customers.php');?>
        </div>
    </div>
<?php
}

function bookme_render_calendar()
{ ?>
    <div id="" role="main">
        <div class="wrap">
            <?php
            include(plugin_dir_path(__FILE__) . '/calendar.php');
            ?>
        </div>
    </div>
<?php
}

function bookme_render_payments()
{ ?>
    <div id="" role="main">
        <div class="wrap">
            <?php include(plugin_dir_path(__FILE__) . '/payments.php');?>
        </div>
    </div>
<?php
}

function bookme_render_email()
{ ?>
    <div id="" role="main">
        <div class="wrap">
            <?php include(plugin_dir_path(__FILE__) . '/email_notification.php');?>
        </div>
    </div>
<?php
}

function bookme_render_sms()
{ ?>
    <div id="" role="main">
        <div class="wrap">
            <?php include(plugin_dir_path(__FILE__) . '/sms_notification.php');?>
        </div>
    </div>
    <?php
}

function bookme_render_coupons()
{ ?>
    <div id="" role="main">
        <div class="wrap">
            <?php include(plugin_dir_path(__FILE__) . '/coupons.php');?>
        </div>
    </div>
<?php
}

function bookme_render_settings()
{ ?>
    <div id="" role="main">
        <div class="wrap">
            <?php include(plugin_dir_path(__FILE__) . '/setting.php');?>
        </div>
    </div>
<?php
}