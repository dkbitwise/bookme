<?php
global $wpdb;
$table_current_booking = $wpdb->prefix . 'bookme_current_booking';
$table_customers = $wpdb->prefix . 'bookme_customers';
$table_customer_booking = $wpdb->prefix . 'bookme_customer_booking';

$resultCust = $wpdb->get_results("SELECT c.*, max(b.date) date, count(cb.customer_id) total_booking FROM $table_customers c LEFT JOIN $table_customer_booking cb ON c.id = cb.customer_id LEFT JOIN $table_current_booking b ON cb.booking_id = b.id GROUP BY c.id ORDER BY c.id");

?>
<div class="page">
    <div>
        <?php include(plugin_dir_path(__FILE__) . '/header.php'); ?>
        <div class="panel">
            <header class="panel-heading">
                <h3 class="panel-title"><?php _e('Customers List', 'bookme'); ?></h3>
            </header>
            <div class="panel-body">

                <table id="customerTable" class="table table-hover dataTable table-striped width-full border-table"
                       data-tablesaw-mode="stack" data-plugin="animateList"
                       data-animate="fade" data-child="tr" data-selectable="selectable">
                    <thead>
                    <th><?php _e('No.', 'bookme'); ?></th>
                    <th><?php _e('Name', 'bookme'); ?></th>
                    <th><?php _e('Phone', 'bookme'); ?></th>
                    <th><?php _e('Email', 'bookme'); ?></th>
                    <th><?php _e('Notes', 'bookme'); ?></th>
                    <th><?php _e('Last Appointment', 'bookme'); ?></th>
                    <th><?php _e('Total Appointments', 'bookme'); ?></th>
                    <th data-priority="2" class="no-sort"><?php _e('Edit', 'bookme'); ?></th>
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
                    foreach ($resultCust as $customer) {
                        ?>
                        <tr id="cust_id_<?php echo $customer->id; ?>">
                            <td><?php echo $customer->id; ?></td>
                            <td><?php echo $customer->name;?></td>
                            <td><?php echo $customer->phone;?></td>
                            <td><?php echo $customer->email;?></td>
                            <td><?php echo $customer->notes;?></td>
                            <td><?php echo date_i18n(get_option('date_format'), strtotime($customer->date))?></td>
                            <td><?php echo $customer->total_booking;?></td>
                            <td>
                                <p>
                                    <button class="btn btn-primary btn-xs"
                                            data-url="<?php echo admin_url('admin-ajax.php') . '?action=customer_panel&customer_id=' . $customer->id; ?>"
                                            data-toggle="slidePanel"><span class="md-edit"></span></button>
                                </p>
                            </td>
                            <td>
              <span class="checkbox-custom checkbox-primary checkbox-lg del_cust_checkbox">
              <input type="checkbox" class="contacts-checkbox selectable-item" id="<?php echo $customer->id;?>"/>
              <label for="contacts_1"></label>
            </span>
                        </tr>
                    <?php
                    }
                    ?>
                    </tbody>
                </table>

            </div>
        </div>
    </div>
</div>
<!-- Site Action -->
<div class="site-action">
    <button data-url="<?php echo admin_url('admin-ajax.php') . '?action=customer_panel'; ?>" data-toggle="slidePanel"
            id="slidepanel-show" style="display: none;"></button>
    <button type="button" class="site-action-toggle btn-raised btn btn-success btn-floating">
        <i class="front-icon md-plus animation-scale-up" aria-hidden="true"></i>
        <i class="back-icon md-close animation-scale-up" aria-hidden="true"></i>
    </button>
    <div class="site-action-buttons">
        <button type="button" data-action="trash"
                class="btn-raised btn btn-danger btn-floating animation-slide-bottom" id="del_customer_array">
            <i class="icon md-delete" aria-hidden="true"></i>
        </button>
    </div>
</div>
<!-- End Site Action -->