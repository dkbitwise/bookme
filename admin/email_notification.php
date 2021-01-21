<?php
global $wpdb;
$table_enotification = $wpdb->prefix . 'bookme_email_notification';
?>

<div class="page  bookme-email">
    <div>
        <?php include(plugin_dir_path(__FILE__) . '/header.php'); ?>
        <h3><?php _e('Email Notification', 'bookme'); ?></h3>
        <div class="panel col-md-12 clearfix">
            <div class="padding-top-20">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="bookme_email_sender_name"><?php _e('Sender name', 'bookme'); ?></label>
                        <input id="bookme_email_sender_name" class="form-control"
                               type="text" name="bookme_email_sender_name"
                               value="<?php echo bookme_get_settings('bookme_email_sender_name', get_bloginfo('name')); ?>">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="bookme_email_sender_email"><?php _e('Sender email', 'bookme'); ?></label>
                        <input id="bookme_email_sender_email" class="form-control"
                               type="text" name="bookme_email_sender_email"
                               value="<?php echo bookme_get_settings('bookme_email_sender_email', get_bloginfo('admin_email')); ?>">
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="panel-group" id="accordion">
                    <div class="panel panel-default">
                        <header class="panel-heading box-heading">
                            <h3 class="panel-title">
                                <input type="checkbox" id="email_customer" value="1" <?php checked( bookme_get_settings('email_customer', 'true'), 'true' ); ?>>
                                <span class="collapsed" data-toggle="collapse" data-parent="#accordion" data-target="#collapse1"><?php _e('Notification to customer about approved appointment', 'bookme'); ?></span>
                            </h3>
                        </header>
                        <div class="panel-body panel-collapse collapse" id="collapse1">
                            <div class="row">
                                <div class="col-md-12 col-sm-12">
                                    <div class="form-group">
                                        <label for="customer_subject"><?php _e('Subject', 'bookme'); ?></label>
                                        <?php
                                        $sqlblbc = "SELECT * FROM $table_enotification where email_key='customer_sub' and key_type='customer_confirm'";
                                        $reslabl = $wpdb->get_results($sqlblbc);
                                        $subj = (isset($reslabl[0])) ? $reslabl[0]->email_value : '';
                                        ?>
                                        <input class="form-control" value="<?php if (!empty($subj)) {
                                            echo $subj;
                                        } ?>" type="text" id="customer_subject">
                                    </div>
                                    <div class="form-group">
                                        <label><?php _e('Message', 'bookme'); ?></label>
                                        <?php
                                        $sqlblbc = "SELECT * FROM $table_enotification where email_key='customer_msg' and key_type='customer_confirm'";
                                        $reslabl = $wpdb->get_results($sqlblbc);
                                        $mess = (isset($reslabl[0])) ? $reslabl[0]->email_value : '';
                                        $settings = array(
                                            'textarea_name' => 'emessage_customer',
                                            'media_buttons' => false,
                                            'editor_height' => 384,
                                            'tinymce'       => array(
                                                'theme_advanced_buttons1' => 'formatselect,|,bold,italic,underline,|,'.
                                                    'bullist,blockquote,|,justifyleft,justifycenter'.
                                                    ',justifyright,justifyfull,|,link,unlink,|'.
                                                    ',spellchecker,wp_fullscreen,wp_adv'
                                            )
                                        );
                                        wp_editor($mess, 'emessage_customer', $settings ); ?>
                                    </div>
                                </div>
                                <div class="col-md-12 col-sm-12">
                                    <table class="bookme-codes form-group">
                                        <tbody>
                                        <tr>
                                            <td>
                                                <input value="{booking_time}" readonly="readonly"
                                                       onclick="this.select()"> - <?php _e('time of appointment', 'bookme'); ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <input value="{booking_end_time}" readonly="readonly"
                                                       onclick="this.select()"> - <?php _e('end time of appointment', 'bookme'); ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <input value="{booking_date}" readonly="readonly"
                                                       onclick="this.select()"> - <?php _e('booking date', 'bookme'); ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <input value="{number_of_persons}" readonly="readonly"
                                                       onclick="this.select()"> - <?php _e('number of persons', 'bookme'); ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <input value="{customer_name}" readonly="readonly"
                                                       onclick="this.select()"> - <?php _e('customer name', 'bookme'); ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <input value="{customer_email}" readonly="readonly"
                                                       onclick="this.select()"> - <?php _e('customer email', 'bookme'); ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <input value="{customer_phone}" readonly="readonly"
                                                       onclick="this.select()"> - <?php _e('customer phone', 'bookme'); ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <input value="{customer_note}" readonly="readonly"
                                                       onclick="this.select()"> - <?php _e('customer note', 'bookme'); ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <input value="{company_name}" readonly="readonly"
                                                       onclick="this.select()"> - <?php _e('company name', 'bookme'); ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <input value="{company_address}" readonly="readonly"
                                                       onclick="this.select()"> - <?php _e('company address', 'bookme'); ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <input value="{company_phone}" readonly="readonly"
                                                       onclick="this.select()"> - <?php _e('company phone', 'bookme'); ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <input value="{company_website}" readonly="readonly"
                                                       onclick="this.select()"> - <?php _e('company website', 'bookme'); ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <input value="{employee_name}" readonly="readonly"
                                                       onclick="this.select()"> - <?php _e('employee name', 'bookme'); ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <input value="{category_name}" readonly="readonly"
                                                       onclick="this.select()"> - <?php _e('category name', 'bookme'); ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <input value="{service_name}" readonly="readonly"
                                                       onclick="this.select()"> - <?php _e('service name', 'bookme'); ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <input value="{custom_field}" readonly="readonly"
                                                       onclick="this.select()"> - <?php _e('combined values of all custom fields', 'bookme'); ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <input value="{custom_field_2col}" readonly="readonly"
                                                       onclick="this.select()"> - <?php _e('combined values of all custom fields (formatted in 2 columns)', 'bookme'); ?>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="panel panel-default">
                        <header class="panel-heading box-heading">
                            <h3 class="panel-title">
                                <input type="checkbox" id="email_employee" value="1" <?php checked( bookme_get_settings('email_employee', 'true'), 'true' ); ?>>
                                <span class="collapsed" data-toggle="collapse" data-parent="#accordion" data-target="#collapse2"><?php _e('Notification to employee about approved appointment', 'bookme'); ?></span>
                            </h3>
                        </header>
                        <div class="panel-body panel-collapse collapse" id="collapse2">
                            <div class="row">
                                <div class="col-md-12 col-sm-12">
                                    <div class="form-group">
                                        <label for="employee_subject"><?php _e('Subject', 'bookme'); ?></label>
                                        <?php
                                        $sqlblbc = "SELECT * FROM $table_enotification where email_key='employee_sub' and key_type='employee_confirm'";
                                        $reslabl = $wpdb->get_results($sqlblbc);
                                        $subj = (isset($reslabl[0])) ? $reslabl[0]->email_value : '';
                                        ?>
                                        <input class="form-control" value="<?php if (!empty($subj)) {
                                            echo $subj;
                                        } ?>" type="text" id="employee_subject">
                                    </div>
                                    <div class="form-group">
                                        <label for="info_1"><?php _e('Message', 'bookme'); ?></label>
                                        <?php
                                        $sqlblbc = "SELECT * FROM $table_enotification where email_key='employee_msg' and key_type='employee_confirm'";
                                        $reslabl = $wpdb->get_results($sqlblbc);
                                        $mess = (isset($reslabl[0])) ? $reslabl[0]->email_value : '';
                                        $settings = array(
                                            'textarea_name' => 'emessage_employee',
                                            'media_buttons' => false,
                                            'editor_height' => 384,
                                            'tinymce'       => array(
                                                'theme_advanced_buttons1' => 'formatselect,|,bold,italic,underline,|,'.
                                                    'bullist,blockquote,|,justifyleft,justifycenter'.
                                                    ',justifyright,justifyfull,|,link,unlink,|'.
                                                    ',spellchecker,wp_fullscreen,wp_adv'
                                            )
                                        );
                                        wp_editor( $mess, 'emessage_employee', $settings ); ?>
                                    </div>
                                </div>
                                <div class="col-md-12 col-sm-12">
                                    <table class="bookme-codes form-group">
                                        <tbody>
                                        <tr>
                                            <td>
                                                <input value="{booking_time}" readonly="readonly"
                                                       onclick="this.select()"> - <?php _e('time of appointment', 'bookme'); ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <input value="{booking_end_time}" readonly="readonly"
                                                       onclick="this.select()"> - <?php _e('end time of appointment', 'bookme'); ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <input value="{booking_date}" readonly="readonly"
                                                       onclick="this.select()"> - <?php _e('booking date', 'bookme'); ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <input value="{number_of_persons}" readonly="readonly"
                                                       onclick="this.select()"> - <?php _e('number of persons', 'bookme'); ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <input value="{customer_name}" readonly="readonly"
                                                       onclick="this.select()"> - <?php _e('customer name', 'bookme'); ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <input value="{customer_email}" readonly="readonly"
                                                       onclick="this.select()"> - <?php _e('customer email', 'bookme'); ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <input value="{customer_phone}" readonly="readonly"
                                                       onclick="this.select()"> - <?php _e('customer phone', 'bookme'); ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <input value="{customer_note}" readonly="readonly"
                                                       onclick="this.select()"> - <?php _e('customer note', 'bookme'); ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <input value="{company_name}" readonly="readonly"
                                                       onclick="this.select()"> - <?php _e('company name', 'bookme'); ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <input value="{company_address}" readonly="readonly"
                                                       onclick="this.select()"> - <?php _e('company address', 'bookme'); ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <input value="{company_phone}" readonly="readonly"
                                                       onclick="this.select()"> - <?php _e('company phone', 'bookme'); ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <input value="{company_website}" readonly="readonly"
                                                       onclick="this.select()"> - <?php _e('company website', 'bookme'); ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <input value="{employee_name}" readonly="readonly"
                                                       onclick="this.select()"> - <?php _e('employee name', 'bookme'); ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <input value="{category_name}" readonly="readonly"
                                                       onclick="this.select()"> - <?php _e('category name', 'bookme'); ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <input value="{service_name}" readonly="readonly"
                                                       onclick="this.select()"> - <?php _e('service name', 'bookme'); ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <input value="{custom_field}" readonly="readonly"
                                                       onclick="this.select()"> - <?php _e('combined values of all custom fields', 'bookme'); ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <input value="{custom_field_2col}" readonly="readonly"
                                                       onclick="this.select()"> - <?php _e('combined values of all custom fields (formatted in 2 columns)', 'bookme'); ?>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="panel panel-default">
                        <header class="panel-heading box-heading">
                            <h3 class="panel-title">
                                <input type="checkbox" id="email_admin" value="1" <?php checked( bookme_get_settings('email_admin', 'true'), 'true' ); ?>>
                                <span class="collapsed" data-toggle="collapse" data-parent="#accordion" data-target="#collapse3"><?php _e('Notification to admin about approved appointment', 'bookme'); ?></span>
                            </h3>
                        </header>
                        <div class="panel-body panel-collapse collapse" id="collapse3">
                            <div class="row">
                                <div class="col-md-12 col-sm-12">
                                    <div class="form-group">
                                        <label for="admin_subject"><?php _e('Subject', 'bookme'); ?></label>
                                        <?php
                                        $sqlblbc = "SELECT * FROM $table_enotification where email_key='admin_sub' and key_type='admin_confirm'";
                                        $reslabl = $wpdb->get_results($sqlblbc);
                                        $subj = (isset($reslabl[0])) ? $reslabl[0]->email_value : '';
                                        ?>
                                        <input class="form-control" value="<?php if (!empty($subj)) {
                                            echo $subj;
                                        } ?>" type="text" id="admin_subject">
                                    </div>
                                    <div class="form-group">
                                        <label for="info_1"><?php _e('Message', 'bookme'); ?></label>
                                        <?php
                                        $sqlblbc = "SELECT * FROM $table_enotification where email_key='admin_msg' and key_type='admin_confirm'";
                                        $reslabl = $wpdb->get_results($sqlblbc);
                                        $mess = (isset($reslabl[0])) ? $reslabl[0]->email_value : '';
                                        $settings = array(
                                            'textarea_name' => 'emessage_admin',
                                            'media_buttons' => false,
                                            'editor_height' => 384,
                                            'tinymce'       => array(
                                                'theme_advanced_buttons1' => 'formatselect,|,bold,italic,|,'.
                                                    'bullist,blockquote,|,justifyleft,justifycenter'.
                                                    ',justifyright,|,link,|'.
                                                    ',wp_fullscreen,wp_adv'
                                            )
                                        );
                                        wp_editor( $mess, 'emessage_admin', $settings ); ?>
                                    </div>
                                </div>
                                <div class="col-md-12 col-sm-12">
                                    <table class="bookme-codes form-group">
                                        <tbody>
                                        <tr>
                                            <td>
                                                <input value="{booking_time}" readonly="readonly"
                                                       onclick="this.select()"> - <?php _e('time of appointment', 'bookme'); ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <input value="{booking_end_time}" readonly="readonly"
                                                       onclick="this.select()"> - <?php _e('end time of appointment', 'bookme'); ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <input value="{booking_date}" readonly="readonly"
                                                       onclick="this.select()"> - <?php _e('booking date', 'bookme'); ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <input value="{number_of_persons}" readonly="readonly"
                                                       onclick="this.select()"> - <?php _e('number of persons', 'bookme'); ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <input value="{customer_name}" readonly="readonly"
                                                       onclick="this.select()"> - <?php _e('customer name', 'bookme'); ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <input value="{customer_email}" readonly="readonly"
                                                       onclick="this.select()"> - <?php _e('customer email', 'bookme'); ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <input value="{customer_phone}" readonly="readonly"
                                                       onclick="this.select()"> - <?php _e('customer phone', 'bookme'); ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <input value="{customer_note}" readonly="readonly"
                                                       onclick="this.select()"> - <?php _e('customer note', 'bookme'); ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <input value="{company_name}" readonly="readonly"
                                                       onclick="this.select()"> - <?php _e('company name', 'bookme'); ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <input value="{company_address}" readonly="readonly"
                                                       onclick="this.select()"> - <?php _e('company address', 'bookme'); ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <input value="{company_phone}" readonly="readonly"
                                                       onclick="this.select()"> - <?php _e('company phone', 'bookme'); ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <input value="{company_website}" readonly="readonly"
                                                       onclick="this.select()"> - <?php _e('company website', 'bookme'); ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <input value="{employee_name}" readonly="readonly"
                                                       onclick="this.select()"> - <?php _e('employee name', 'bookme'); ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <input value="{category_name}" readonly="readonly"
                                                       onclick="this.select()"> - <?php _e('category name', 'bookme'); ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <input value="{service_name}" readonly="readonly"
                                                       onclick="this.select()"> - <?php _e('service name', 'bookme'); ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <input value="{custom_field}" readonly="readonly"
                                                       onclick="this.select()"> - <?php _e('combined values of all custom fields', 'bookme'); ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <input value="{custom_field_2col}" readonly="readonly"
                                                       onclick="this.select()"> - <?php _e('combined values of all custom fields (formatted in 2 columns)', 'bookme'); ?>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-12 col-sm-12 margin-bottom-20">
                <button type="button" class="btn btn-success" id="emailnotification"><?php _e('Save', 'bookme'); ?></button>
            </div>
        </div>
    </div>
</div>