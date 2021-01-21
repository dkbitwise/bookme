<?php
global $wpdb, $help;
$table_sms_notification = $wpdb->prefix . 'bookme_sms_notification';
?>

<div class="page  bookme-email">
    <div>
        <?php include(plugin_dir_path(__FILE__) . '/header.php'); ?>
        <h3><?php _e('SMS Notification', 'bookme'); ?></h3>
        <div class="panel col-md-12 clearfix">
            <div class="padding-top-20">
                <div class="col-md-12">
                    <div class="alert alert-info">
                        <?php _e('Bookme uses <a href="https://www.twilio.com/" target="_blank" class="alert-link">Twilio SMS API</a> for sending SMS. Just follow the below steps and enjoy the SMS service.'); ?>
                    </div>
                    <ol>
                        <li><?php _e('Visit to','bookme'); ?> <a href="https://www.twilio.com/" target="_blank"><?php _e('Twilio website','bookme'); ?></a> <?php _e('and sign up or log in.'); ?></li>
                        <li><?php _e('After that go to '); ?><a href="https://www.twilio.com/console" target="_blank"><?php _e('Console page.'); ?></a></li>
                        <li><?php _e('And now copy ACCOUNT SID and AUTH TOKEN and paste below.'); ?></li>
                        <li><?php _e('After that create Twilio phone number and paste below.'); ?></li>
                        <li><?php _e("That's it. Now enjoy the SMS service."); ?></li>
                    </ol>
                    <hr>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="bookme_sms_accountsid"><?php _e('Account SID', 'bookme'); ?>
                            <span
                                    class="dashicons dashicons-editor-help tooltipster"
                                    title="<?php echo $help['SMS_ACCOUNTSID'] ?>"></span>
                        </label>
                        <input id="bookme_sms_accountsid" class="form-control"
                               type="text" name="bookme_sms_accountsid"
                               value="<?php echo bookme_get_settings('bookme_sms_accountsid'); ?>">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="bookme_sms_authtoken"><?php _e('Auth Token', 'bookme'); ?>
                            <span
                                    class="dashicons dashicons-editor-help tooltipster"
                                    title="<?php echo $help['SMS_AUTHTOKEN'] ?>"></span>
                        </label>
                        <input id="bookme_sms_authtoken" class="form-control"
                               type="text" name="bookme_sms_authtoken"
                               value="<?php echo bookme_get_settings('bookme_sms_authtoken'); ?>">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="bookme_sms_phone_no"><?php _e('Twilio Phone Number', 'bookme'); ?>
                            <span
                                    class="dashicons dashicons-editor-help tooltipster"
                                    title="<?php echo $help['SMS_PHONENO'] ?>"></span>
                        </label>
                        <input id="bookme_sms_phone_no" class="form-control"
                               type="tel" name="bookme_sms_phone_no"
                               value="<?php echo bookme_get_settings('bookme_sms_phone_no'); ?>">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="bookme_admin_phone_no"><?php _e('Admin Phone Number', 'bookme'); ?>
                            <span
                                    class="dashicons dashicons-editor-help tooltipster"
                                    title="<?php echo $help['ADMIN_PHONENO'] ?>"></span>
                        </label>
                        <input id="bookme_admin_phone_no" class="form-control"
                               type="tel" name="bookme_admin_phone_no"
                               value="<?php echo bookme_get_settings('bookme_admin_phone_no'); ?>">
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="panel-group" id="accordion">
                    <div class="panel panel-default">
                        <header class="panel-heading box-heading">
                            <h3 class="panel-title">
                                <input type="checkbox" id="sms_customer" value="1" <?php checked( bookme_get_settings('sms_customer', 'true'), 'true' ); ?>>
                                <span class="collapsed" data-toggle="collapse" data-parent="#accordion" data-target="#collapse1"><?php _e('SMS to customer about approved appointment', 'bookme'); ?></span>
                            </h3>
                        </header>
                        <div class="panel-body panel-collapse collapse" id="collapse1">
                            <div class="row">
                                <div class="col-md-12 col-sm-12">
                                    <div class="form-group">
                                        <label for="message_customer"><?php _e('Message', 'bookme'); ?></label>
                                        <?php
                                        $sqlblbc = "SELECT * FROM $table_sms_notification where sms_key='customer_msg' and key_type='customer_confirm'";
                                        $reslabl = $wpdb->get_results($sqlblbc);
                                        $mess = (isset($reslabl[0])) ? $reslabl[0]->sms_value : '';?>
                                        <textarea id="message_customer" name="message_customer" class="form-control" rows="10"><?php echo $mess; ?></textarea>
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
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="panel panel-default">
                        <header class="panel-heading box-heading">
                            <h3 class="panel-title">
                                <input type="checkbox" id="sms_employee" value="1" <?php checked( bookme_get_settings('sms_employee', 'true'), 'true' ); ?>>
                                <span class="collapsed" data-toggle="collapse" data-parent="#accordion" data-target="#collapse2"><?php _e('SMS to employee about approved appointment', 'bookme'); ?></span>
                            </h3>
                        </header>
                        <div class="panel-body panel-collapse collapse" id="collapse2">
                            <div class="row">
                                <div class="col-md-12 col-sm-12">
                                    <div class="form-group">
                                        <label for="message_employee"><?php _e('Message', 'bookme'); ?></label>
                                        <?php
                                        $sqlblbc = "SELECT * FROM $table_sms_notification where sms_key='employee_msg' and key_type='employee_confirm'";
                                        $reslabl = $wpdb->get_results($sqlblbc);
                                        $mess = (isset($reslabl[0])) ? $reslabl[0]->sms_value : ''; ?>
                                        <textarea id="message_employee" name="message_employee" class="form-control" rows="10"><?php echo $mess; ?></textarea>
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
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="panel panel-default">
                        <header class="panel-heading box-heading">
                            <h3 class="panel-title">
                                <input type="checkbox" id="sms_admin" value="1" <?php checked( bookme_get_settings('sms_admin', 'true'), 'true' ); ?>>
                                <span class="collapsed" data-toggle="collapse" data-parent="#accordion" data-target="#collapse3"><?php _e('SMS to admin about approved appointment', 'bookme'); ?></span>
                            </h3>
                        </header>
                        <div class="panel-body panel-collapse collapse" id="collapse3">
                            <div class="row">
                                <div class="col-md-12 col-sm-12">
                                    <div class="form-group">
                                        <label for="message_admin"><?php _e('Message', 'bookme'); ?></label>
                                        <?php
                                        $sqlblbc = "SELECT * FROM $table_sms_notification where sms_key='admin_msg' and key_type='admin_confirm'";
                                        $reslabl = $wpdb->get_results($sqlblbc);
                                        $mess = (isset($reslabl[0])) ? $reslabl[0]->sms_value : ''; ?>
                                        <textarea id="message_admin" name="message_admin" class="form-control" rows="10"><?php echo $mess; ?></textarea>
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
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-12 col-sm-12 margin-bottom-20">
                <button type="button" class="btn btn-success" id="smsnotification"><?php _e('Save', 'bookme'); ?></button>
            </div>
        </div>
    </div>
</div>