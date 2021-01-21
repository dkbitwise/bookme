<?php
add_action('wp_ajax_nopriv_payment_panel', 'payment_panel');
add_action('wp_ajax_payment_panel', 'payment_panel');
function payment_panel()
{
    if (isset($_GET['payment_id'])) {
        global $wpdb;
        $table_book_payment = $wpdb->prefix . 'bookme_payments';
        $table_book_service = $wpdb->prefix . 'bookme_service';
        $table_all_employee = $wpdb->prefix . 'bookme_employee';
        $table_customers = $wpdb->prefix . 'bookme_customers';
        $table_customer_booking = $wpdb->prefix . 'bookme_customer_booking';
        $table_current_booking = $wpdb->prefix . 'bookme_current_booking';
        $result = $wpdb->get_results("SELECT p.*,c.name,b.date,b.time, s.name service_name, e.name emp_name, cb.no_of_person person FROM $table_book_payment p LEFT JOIN $table_customer_booking cb ON cb.payment_id = p.id LEFT JOIN $table_customers c ON c.id = cb.customer_id LEFT JOIN $table_current_booking b ON cb.booking_id = b.id LEFT JOIN $table_book_service s ON s.id = b.ser_id LEFT JOIN $table_all_employee e ON e.id = b.emp_id WHERE p.id = " . $_GET['payment_id']);

        ?>

        <header class="slidePanel-header overlay">
            <div class="overlay-panel overlay-background vertical-align">
                <div class="service-heading">
                    <h2><?php _e('Invoice', 'bookme'); ?></h2>
                </div>
                <div class="slidePanel-actions">
                    <div class="btn-group-flat">
                        <button type="button"
                                class="btn btn-floating btn-success btn-sm waves-effect waves-float waves-light margin-right-10"
                                aria-hidden="true" id="print_invoice"><i class="icon md-print" aria-hidden="true"></i>
                        </button>
                        <button type="button"
                                class="btn btn-pure btn-inverse slidePanel-close icon md-close font-size-20"
                                aria-hidden="true"></button>
                    </div>
                </div>
            </div>
        </header>
        <div class="slidePanel-inner">
            <div class="panel-body">
                <div id="invoice_for_print">
                    <div class="table-responsive">
                        <table class="table table-bordered margin-bottom-20">
                            <thead>
                            <tr>
                                <th width="50%"><?php _e('Customer', 'bookme'); ?></th>
                                <th width="50%"><?php _e('Payment', 'bookme'); ?></th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td><?php echo $result[0]->name; ?></td>
                                <td>
                                    <div><?php _e('Created:', 'bookme'); ?> <?php echo date_i18n(get_option('date_format').' '.get_option('time_format'), strtotime($result[0]->created)); ?></div>
                                    <div><?php _e('Type:', 'bookme'); ?> <?php echo $result[0]->type; ?></div>
                                    <div><?php _e('Status:', 'bookme'); ?> <?php echo $result[0]->status; ?></div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered margin-bottom-20">
                            <thead>
                            <tr>
                                <th><?php _e('Service', 'bookme'); ?></th>
                                <th><?php _e('Date', 'bookme'); ?></th>
                                <th><?php _e('Provider', 'bookme'); ?></th>
                                <th class="text-right"><?php _e('Price', 'bookme'); ?></th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>
                                    <?php
                                    echo $result[0]->service_name;

                                    ?>  </td>
                                <td><?php echo date_i18n(get_option('date_format').' '.get_option('time_format'), strtotime($result[0]->date . ' ' . $result[0]->time)); ?> </td>
                                <td><?php
                                    echo $result[0]->emp_name;
                                    ?></td>
                                <td class="text-right">
                                    <?php echo bookme_formatPrice($result[0]->price); ?>
                                    <ul class="bookme-list">
                                    </ul>
                                </td>
                            </tr>
                            </tbody>
                            <tfoot>
                            <?php if($result[0]->person != 1){ ?>
                                <tr>
                                    <th rowspan="4"></th>
                                    <th colspan="2">
                                        <?php _e('No of person', 'bookme'); ?>
                                    </th>
                                    <th class="text-right">
                                        <?php echo $result[0]->person; ?>
                                    </th>
                                </tr>
                            <?php } ?>
                            <tr>
                                <?php if($result[0]->person == 1){ ?>
                                    <th rowspan="3"></th>
                                <?php } ?>
                                <th colspan="2"><?php _e('Subtotal', 'bookme'); ?></th>
                                <th class="text-right"><?php echo bookme_formatPrice($result[0]->price * $result[0]->person); ?></th>
                            </tr>
                            <tr>
                                <th colspan="2">
                                    <?php _e('Discount', 'bookme'); ?>
                                </th>
                                <th class="text-right">
                                    <?php echo bookme_formatPrice($result[0]->discount_price == ''?0:$result[0]->discount_price); ?>
                                </th>
                            </tr>
                            <tr>
                                <th colspan="2"><?php _e('Total', 'bookme'); ?></th>
                                <th class="text-right"><?php echo bookme_formatPrice($result[0]->discount_price == '' ? $result[0]->price : ($result[0]->price * $result[0]->person) - $result[0]->discount_price); ?></th>
                            </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <?php if($result[0]->status == "pending"){ ?>
                <div class="text-right" id="comp_payment">
                    <button type="button" class="btn btn-success" id="comp_payment_btn" data-id="<?php echo $_GET['payment_id']; ?>"><?php _e('Complete payment', 'bookme'); ?></button>
                </div>
                <?php } ?>
            </div>
        </div>
    <?php
    }
    wp_die();
}
?>