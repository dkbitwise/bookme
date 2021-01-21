<?php
add_action('wp_ajax_nopriv_booking_panel', 'bookme_booking_panel');
add_action('wp_ajax_booking_panel', 'bookme_booking_panel');

function bookme_booking_panel()
{
    global $help;
    global $wpdb;
    $table_all_employee = $wpdb->prefix . 'bookme_employee';
    $table_current_booking = $wpdb->prefix . 'bookme_current_booking';
    $table_name_ser = $wpdb->prefix . 'bookme_service';
    $table_customers = $wpdb->prefix . 'bookme_customers';
    $table_customer_booking = $wpdb->prefix . 'bookme_customer_booking';
    $table_payments = $wpdb->prefix . 'bookme_payments';

    $emp_id = 0;
    $ser_id = '';
    $date = '';
    $time = '';
    $duration = 0;
    $cb_id = '';
    $cust_id = array();
    $name = array();
    $email = '';
    $phone = '';
    $notes = '';
    $no_of_person = '';
    $status = '';
    if (isset($_GET['booking_id'])) {
        $results = $wpdb->get_results("SELECT group_concat(cb.id) cb_id, group_concat(cb.customer_id) customer_id, group_concat(cb.payment_id) payment_id, group_concat(cb.no_of_person) no_of_person, group_concat(cb.status) status, group_concat(c.name) name, group_concat(c.phone) phone, group_concat(c.email) email, group_concat(c.notes) notes, b.emp_id, b.ser_id, b.duration, b.date, b.time, group_concat(p.price) price, group_concat(p.type) ptype, group_concat(p.status) pstatus FROM $table_customer_booking cb LEFT JOIN $table_customers c ON c.id = cb.customer_id LEFT JOIN $table_current_booking b ON cb.booking_id = b.id LEFT JOIN $table_payments p ON cb.payment_id = p.id WHERE cb.booking_id = '" . $_GET['booking_id'] . "' GROUP BY cb.booking_id ORDER BY cb.id");
        $emp_id = $results[0]->emp_id;
        $ser_id = $results[0]->ser_id;
        $date = $results[0]->date;
        $time = $results[0]->time;
        $duration = $results[0]->duration;
        $cust_id = explode(',', $results[0]->customer_id);
        $cb_id = explode(',', $results[0]->cb_id);
        $name = explode(',', $results[0]->name);
        $email = explode(',', $results[0]->email);
        $phone = explode(',', $results[0]->phone);
        $notes = explode(',', $results[0]->notes);
        $no_of_person = explode(',', $results[0]->no_of_person);
        $status = explode(',', $results[0]->status);
    }
    if ($emp_id != 0) {
        $where = 'WHERE find_in_set(' . $emp_id . ',staff) <> 0';
    } else {
        $where = '';
    }
    $resultser = $wpdb->get_results("SELECT id,name,duration,capacity FROM $table_name_ser " . $where);
    $capacity = 0;

    $customers = $wpdb->get_results("SELECT * FROM $table_customers");
    ?>

    <header class="slidePanel-header overlay">
        <div class="overlay-panel overlay-background vertical-align">
            <div class="service-heading">
                <h2><?php isset($_GET['booking_id']) ? _e('Edit booking', 'bookme') : _e('Add new booking', 'bookme'); ?></h2>
            </div>
            <div class="slidePanel-actions">
                <div class="btn-group-flat">
                    <button type="button"
                            class="btn btn-floating btn-success btn-sm waves-effect waves-float waves-light margin-right-10"
                            id="save_booking"><i class="icon md-check" aria-hidden="true"></i></button>
                    <?php if (isset($_GET['booking_id'])) { ?>
                        <button type="button" class="btn btn-pure btn-inverse icon md-delete font-size-20"
                                aria-hidden="true" id="del_booking"></button>
                    <?php } ?>
                    <button type="button" class="btn btn-pure btn-inverse slidePanel-close icon md-close font-size-20"
                            aria-hidden="true"></button>
                </div>
            </div>
        </div>
    </header>
    <div class="slidePanel-inner">
        <div class="panel-body">
            <form id="booking_form">
                <?php if (isset($_GET['booking_id'])) { ?>
                    <input type="hidden" name="bookingId" id="bookingId" value="<?php echo $_GET['booking_id']; ?>">
                <?php } ?>
                <div class="form-group">
                    <div class="row">
                        <div class="col-md-6">
                            <label for="provider_name"><?php _e('Provider', 'bookme') ?></label>
                            <select name="provider_name" id="provider_name" class="form-control book_select_model">
                                <?php
                                $resultemp = $wpdb->get_results("SELECT * FROM $table_all_employee");
                                foreach ($resultemp as $emp) {
                                    ?>
                                    <option
                                            value="<?php echo $emp->id; ?>" <?php echo (!empty($emp_id) && $emp->id == $emp_id) ? 'selected' : ''; ?>><?php echo $emp->name; ?></option>
                                    <?php
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="bookme_services"><?php _e('Services', 'bookme') ?></label>
                            <select name="edit_ser" id="edit_ser" class="form-control book_select_model edit_ser_id">
                                <option data-capacity="0" data-duration="0"
                                        value="0"><?php _e('Select service', 'bookme') ?></option>
                                <?php
                                foreach ($resultser as $ser) {
                                    ?>
                                    <option data-capacity="<?php echo $ser->capacity; ?>"
                                            data-duration="<?php echo $ser->duration; ?>"
                                            value="<?php echo $ser->id; ?>"
                                        <?php if ($ser->id == $ser_id) {
                                            echo 'selected';
                                            $capacity = $ser->capacity;
                                        } ?>>
                                        <?php echo $ser->name; ?>
                                        <?php
                                        $dtime = $ser->duration;
                                        if (($dtime / 60) < 60) {
                                            echo '(' . ($dtime / 60) . " M" . ')';
                                        }
                                        if (($dtime / 60) >= 60) {
                                            echo '(' . ($dtime / (60 * 60)) . " H" . ')';
                                        }
                                        ?>
                                    </option>
                                    <?php
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="row">
                        <div class="col-md-5">
                            <label for="bookme_date"><?php _e('Date', 'bookme') ?></label>
                            <input class="form-control" id="bookingEditdate" type="text" name="booking_date"
                                   value="<?php echo (!empty($date)) ? $date : date('Y-m-d'); ?>">
                        </div>

                        <div class="col-md-3">
                            <label for="bookme_date"><?php _e('Period', 'bookme') ?><span
                                        class="dashicons dashicons-editor-help tooltipster"
                                        title="<?php echo $help['BOOKING_PERIOD']; ?>"></label>
                            <select name="time_start" id="time_start" class="form-control">
                                <?php
                                $start = strtotime('00:00');
                                $end = strtotime('23:59');
                                $tcheck = $time;
                                $toTime = 0;

                                for ($j = $start; $j <= $end; $j = $j + 15 * 60) {
                                    ?>
                                    <option
                                            value="<?php echo strtotime(date('g:i A', $j)); ?>" <?php if ($tcheck == date('g:i A', $j)) {
                                        $toTime = strtotime(date('g:i A', $j));
                                        echo 'selected';
                                    } ?>><?php echo date_i18n(get_option('time_format'), $j); ?></option>
                                    <?php
                                }
                                ?>
                            </select>

                        </div>
                        <div>
                            <div class="col-md-1">
                                <label for="bookme_date" class="margin-top-30"><?php _e('to', 'bookme') ?></label>
                            </div>
                            <div class="col-md-3">
                                <?php $toTime = $toTime + $duration;
                                $stime = date("g:i A", $toTime);
                                ?>
                                <select name="time_end" id="time_end" class="form-control margin-top-25">
                                    <?php
                                    $start = strtotime('00:00');
                                    $end = strtotime('23:59');

                                    for ($j = $start; $j <= $end; $j = $j + 15 * 60) {
                                        ?>
                                        <option
                                                value="<?php echo strtotime(date('g:i A', $j)); ?>" <?php if ($stime == date('g:i A', $j)) {
                                            echo 'selected';
                                        } ?>><?php echo date_i18n(get_option('time_format'), $j); ?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="row">
                        <div class="col-md-12">
                            <label><?php _e('Customers', 'bookme') ?>
                                <span id="customer_capacity">(<span
                                            id="panel_total_customer"><?php echo count($name); ?></span>/<span
                                            id="panel_capacity"><?php echo $capacity; ?></span>)</span>
                                <span class="dashicons dashicons-editor-help tooltipster"
                                      title="<?php echo $help['BOOKING_CUST']; ?>"></label>
                            <div id="customer_list">
                                <?php for ($i = 0; $i < count($name); $i++) {
                                    ?>
                                    <div class="row margin-bottom-5">
                                        <div class="col-md-7">
                                            <span class="text-primary"><?php echo $name[$i] . ' (' . $email[$i] . ', ' . $phone[$i] . ')'; ?></span>
                                        </div>
                                        <div class="col-md-5 text-right">
                                        <span class="btn btn-inverse disabled bookme_person">
                                            <i class="fa fa-user"></i>&Cross;<?php echo $no_of_person[$i]; ?>
                                        </span>
                                            <button class="btn btn-inverse btn-default delete_customer_cb"
                                                    data-id="<?php echo $cust_id[$i]; ?>" style="color: red"
                                                    type="button">
                                                <i class="fa fa-trash font-size-15"></i>
                                            </button>
                                            <input type="hidden" name="customers[]" value="<?php echo $cust_id[$i]; ?>">
                                            <input type="hidden" name="person[]"
                                                   value="<?php echo $no_of_person[$i]; ?>">
                                        </div>
                                    </div>

                                    <?php
                                } ?>
                            </div>
                            <div class="row margin-bottom-5" id="add_customer_div">
                                <div class="col-md-12">
                                    <div class="input-group">
                                        <select data-live-search="true" data-plugin="selectpicker" data-size="8">
                                            <?php foreach ($customers as $cust) { ?>
                                                <option value="<?php echo $cust->id; ?>"
                                                    <?php echo ($cb_id != '' && in_array($cust->id, $cust_id)) ? 'disabled' : ''; ?>>
                                                    <?php echo $cust->name . ' (' . $cust->email . ', ' . $cust->phone . ')'; ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                        <span class="input-group-btn">
                                         <button type="button" class="btn btn-success"
                                                 id="add_customer_cb"><?php _e('Add Customer', 'bookme'); ?></button>
                                    </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php foreach ($cust_id as $cid) { ?>
                    <input type="hidden" name="old_customers[]" value="<?php echo $cid; ?>">
                <?php } ?>
                <div class="form-group">
                    <label for="bookme_notifications"><?php _e('Send notifications', 'bookme'); ?><span
                                class="dashicons dashicons-editor-help tooltipster"
                                title="<?php echo $help['SEND_EMAIL']; ?>"></span></label>
                    <select name="notifications" id="notifications" class="form-control book_select_model">
                        <option value="0"><?php _e('Don\'t send', 'bookme') ?></option>
                        <option value="1"><?php _e('Send', 'bookme') ?></option>
                    </select>
                </div>
            </form>
        </div>
    </div>
    <script>
        (function ($) {
            $('.tooltipster').tooltipster({
                theme: 'tooltipster-borderless',
                plugins: ['follower'],
                maxWidth: 300,
                delay: 100
            });
            $('[data-plugin="selectpicker"]').selectpicker({
                style: "btn-select"
            });

            $("#bookingEditdate").datepicker({ dateFormat: 'yy-mm-dd' });
        })(jQuery);
    </script>

    <?php
    wp_die();
}

?>