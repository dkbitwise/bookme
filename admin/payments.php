<?php

global $wpdb;
$table_book_payment = $wpdb->prefix . 'bookme_payments';
$table_book_service = $wpdb->prefix . 'bookme_service';
$table_all_employee = $wpdb->prefix . 'bookme_employee';
$table_customers = $wpdb->prefix . 'bookme_customers';
$table_customer_booking = $wpdb->prefix . 'bookme_customer_booking';
$table_current_booking = $wpdb->prefix . 'bookme_current_booking';
$result = $wpdb->get_results("SELECT p.*,c.name,b.date,b.time, s.name service_name, e.name emp_name, cb.no_of_person person FROM $table_book_payment p LEFT JOIN $table_customer_booking cb ON cb.payment_id = p.id LEFT JOIN $table_customers c ON c.id = cb.customer_id LEFT JOIN $table_current_booking b ON cb.booking_id = b.id LEFT JOIN $table_book_service s ON s.id = b.ser_id LEFT JOIN $table_all_employee e ON e.id = b.emp_id");
$num_row = $wpdb->num_rows;
?>
<div class="page ">
    <div>
        <?php include(plugin_dir_path(__FILE__) . '/header.php'); ?>
        <div class="panel">
            <header class="panel-heading">
                <h3 class="panel-title"><?php _e('Payments', 'bookme'); ?></h3>
            </header>
            <div class="panel-body">
                <div class="table-responsive">
                    <table id="paymentTable" class="table table-hover dataTable table-striped width-full border-table"
                           data-tablesaw-mode="stack" data-plugin="animateList"
                           data-animate="fade" data-child="tr" data-selectable="selectable">
                        <thead>
                        <th><?php _e('S No.', 'bookme'); ?></th>
                        <th><?php _e('Date', 'bookme'); ?></th>
                        <th><?php _e('Type', 'bookme'); ?></th>
                        <th><?php _e('Customer', 'bookme'); ?></th>
                        <th><?php _e('Provider', 'bookme'); ?></th>
                        <th><?php _e('Service', 'bookme'); ?></th>
                        <th><?php _e('Appoinment Date', 'bookme'); ?></th>
                        <th><?php _e('Amount', 'bookme'); ?></th>
                        <th><?php _e('Status', 'bookme'); ?></th>
                        <th data-priority="2" class="no-sort hidden-print"><?php _e('Invoice', 'bookme'); ?></th>
                        <th data-priority="1" class="no-sort">
                <span class="checkbox-custom checkbox-primary checkbox-lg contacts-select-all">
                  <input type="checkbox" class="contacts-checkbox selectable-all" id="select_all"
                      />
                  <label for="select_all"></label>
                </span>
                        </th>
                        </thead>
                        <tbody>
                        <?php
                        if ($num_row > 0){
                            $i = 1;
                            foreach ($result as $val){
                                ?>
                                <tr id="payment_id_<?php echo $val->id; ?>">
                                    <td><?php echo $i;?></td>
                                    <td><?php echo date_i18n(get_option('date_format').' '.get_option('time_format'), strtotime($val->created)); ?></td>
                                    <td><?php echo $val->type;?></td>
                                    <td><?php echo $val->name;?></td>
                                    <td><?php echo $val->emp_name;?></td>
                                    <td><?php echo $val->service_name;?></td>
                                    <td>
                                        <?php echo date_i18n(get_option('date_format').' '.get_option('time_format'), strtotime($val->date.' '.$val->time));?>
                                    </td>
                                    <td><?php echo bookme_formatPrice(($val->discount_price == "") ? $val->price : ($val->price * $val->person) - $val->discount_price); ?></td>
                                    <td><?php echo $val->status;?></td>
                                    <td>
                                        <p title="Edit">
                                            <button type="button" class="btn btn-default btn-inverse btn-xs empEdit_id"
                                                    data-url="<?php echo admin_url('admin-ajax.php') . '?action=payment_panel&payment_id=' . $val->id; ?>"
                                                    data-toggle="slidePanel"><?php _e('Invoice', 'bookme'); ?>
                                            </button>
                                        </p>
                                    </td>
                                    <td>
                                        <span class="checkbox-custom checkbox-primary checkbox-lg del_payment_checkbox">
                                          <input type="checkbox" class="contacts-checkbox selectable-item" id="<?php echo $val->id;?>"/>
                                          <label for="contacts_1"></label>
                                        </span>
                                    </td>
                                    <?php
                                    $i++;
                            }
                        }
                        ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Site Action -->
<div class="site-action">
    <button style="display: none;"></button>
    <button type="button" class="site-action-toggle btn-raised btn btn-success btn-floating fade">
        <i class="front-icon md-plus animation-scale-up" aria-hidden="true"></i>
        <i class="back-icon md-close animation-scale-up" aria-hidden="true"></i>
    </button>
    <div class="site-action-buttons">
        <button type="button" data-action="trash"
                class="btn-raised btn btn-danger btn-floating animation-slide-bottom" id="del_payment_array">
            <i class="icon md-delete" aria-hidden="true"></i>
        </button>
    </div>
</div>
<!-- End Site Action -->