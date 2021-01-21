<?php

global $wpdb;
$table_book_category = $wpdb->prefix . 'bookme_category';
$table_book_service = $wpdb->prefix . 'bookme_service';
$table_all_employee = $wpdb->prefix . 'bookme_employee';
$table_current_booking = $wpdb->prefix . 'bookme_current_booking';
$table_customers = $wpdb->prefix . 'bookme_customers';
$table_customer_booking = $wpdb->prefix . 'bookme_customer_booking';
$table_payments = $wpdb->prefix . 'bookme_payments';
$table_settings2 = $wpdb->prefix . 'bookme_settings';
$table_custom_fields = $wpdb->prefix . 'bookme_custom_field';
$table_booking_custom_fields = $wpdb->prefix . 'bookme_current_booking_fields';


$i = 1;
/* Faculty filter */
$user = wp_get_current_user();
$filters='';
if ( in_array( 'lp_teacher', (array) $user->roles ) ) {
    $filters=" where e.email='".$user->user_email."' and `b`.`date` >=date(now()) ";
}

//echo "SELECT cb.*, c.name, c.phone, c.email, c.notes, b.id booking_id, b.duration, b.date, b.time, p.id payment_id, p.price, p.type, p.status payment_status, p.discount_price, s.name ser_name, s.id ser_id, e.name emp_name, cb.no_of_person person  FROM $table_customer_booking cb LEFT JOIN $table_customers c ON cb.customer_id = c.id LEFT JOIN $table_current_booking b ON cb.booking_id = b.id LEFT JOIN $table_payments p ON cb.payment_id = p.id LEFT JOIN $table_book_service s ON b.ser_id = s.id LEFT JOIN $table_all_employee e ON b.emp_id = e.id ".$filter." ORDER BY b.id";

$result = $wpdb->get_results("SELECT cb.*, c.name, c.phone, c.email, c.notes, b.id booking_id, b.duration, b.date, b.time, p.id payment_id, p.price, p.type, p.status payment_status, p.discount_price, s.name ser_name, s.id ser_id, e.name emp_name, cb.no_of_person person  FROM $table_customer_booking cb LEFT JOIN $table_customers c ON cb.customer_id = c.id LEFT JOIN $table_current_booking b ON cb.booking_id = b.id LEFT JOIN $table_payments p ON cb.payment_id = p.id LEFT JOIN $table_book_service s ON b.ser_id = s.id LEFT JOIN $table_all_employee e ON b.emp_id = e.id ".$filters." ORDER BY b.id");
$num_row = $wpdb->num_rows;


$resultField = $wpdb->get_results("SELECT * FROM $table_custom_fields where associate_with = '' order by position asc");
$custom_row = $wpdb->num_rows;
?>

<div class="page">
    <div>
        <?php include(plugin_dir_path(__FILE__) . '/header.php'); ?>
        <div class="panel">
            <header class="panel-heading">
                <h3 class="panel-title"><?php _e('All Booking List', 'bookme'); ?></h3>
            </header>
            <div class="panel-body">
		<?php
			$user = wp_get_current_user();
		?>
                <table id="bookingTable" class="display nowrap table table-hover dataTable table-striped width-full border-table"
                       data-tablesaw-mode="stack" data-child="tr" data-selectable="selectable">
                    <thead>
                    <tr>
                        <th><?php _e('No', 'bookme'); ?></th>
                        <th><?php _e('Appointment Date', 'bookme'); ?></th>
                        <th><?php _e('Employee', 'bookme'); ?></th>
                        <th><?php _e('Customer Name', 'bookme'); ?></th>
                        <th class="none"><?php _e('Customer Phone', 'bookme'); ?></th>
                        <th><?php _e('Customer Email', 'bookme'); ?></th>
                        <th><?php _e('Student Name', 'bookme'); ?></th>
                        <th><?php _e('Student Email', 'bookme'); ?></th>
                        <th><?php _e('Service', 'bookme'); ?></th>
                        <th class="none"><?php _e('Duration', 'bookme'); ?></th>
                        <th <?php if (!user_can( $user, 'administrator' )){ ?>class="none"<?php } ?>><?php _e('Status', 'bookme'); ?></th>
                        <th <?php if (!user_can( $user, 'administrator' )){ ?>class="none"<?php } ?>><?php _e('Payment', 'bookme'); ?></th>
                        <th class="none"><?php _e('Notes', 'bookme'); ?></th>
                        <?php
                        if ($custom_row > 0) {
                            foreach ($resultField as $rowdata) {
                                ?>
                                <th class="none"><?php echo ucwords($rowdata->field_name); ?></th>
                            <?php
                            }
                        }
                        ?>
                        <th data-priority="2" class="no-sort none"><?php _e('Edit', 'bookme'); ?></th>
                        <th data-priority="1" class="no-sort none">
                            <span class="checkbox-custom checkbox-primary checkbox-lg contacts-select-all">
                              <input type="checkbox" class="contacts-checkbox selectable-all" id="select_all"
                                  />
                              <label for="select_all"></label>
                            </span>
                        </th>
                    </tr>
                    </thead>
                    <tbody id="myTable1">
                    <?php
                    if ($num_row > 0) {
                        foreach ($result as $val) {
                            $dateOrder = date('Y-m-d H:i:s', strtotime($val->date . ' ' . $val->time));
			    $parentid = get_user_by( 'email', ''.$val->email.'' )->ID;
			    $sttable = $wpdb->prefix . 'bwlive_students';
                            $stsql = "SELECT student_id,student_fname,student_lname,student_email FROM $sttable where parent_id=".$parentid;
                            $stdata = $wpdb->get_row($stsql);
                            ?>
                            <tr id="booking_id_<?php echo $val->booking_id; ?>" data-date="<?php echo date('D, d M Y h:i:s', strtotime($val->date . ' ' . $val->time)); ?>">
                                <td class=""
                                    data-id="<?php echo $val->booking_id; ?>"><?php echo $val->booking_id; ?></td>
                                <td data-order="<?php echo $dateOrder; ?>"><?php echo date_i18n(get_option('date_format').' '.get_option('time_format'), strtotime($val->date . ' ' . $val->time)); ?>
                                </td>
                                <td><?php echo $val->emp_name; ?></td>
                                <td><?php echo $val->name; ?></td>
                                <td><?php echo $val->phone; ?></td>
                                <td><?php echo $val->email; ?></td>
				<td><?php echo isset($stdata->student_fname) ? $stdata->student_fname : 'NA' ?> <?php echo isset($stdata->student_lname) ? $stdata->student_lname : '' ?></td>
                                <td><?php echo isset($stdata->student_email) ? $stdata->student_email : 'NA' ?></td>
                                <td><?php echo isset($val->ser_name) ? $val->ser_name : ''; ?>
                                </td>
                                <td>
                                    <?php
                                    $time = $val->duration;

                                    if (($time / 60) < 60) {
                                        echo ($time / 60) . " M";
                                    }
                                    if (($time / 60) >= 60) {
                                        echo ($time / (60 * 60)) . " H";
                                    }
                                    ?>
                                </td>
                                <td><?php echo $val->status; ?></td>
                                <td>
                                    <?php if(isset($val->price)){ ?>
                                        <a href="#" data-url="<?php echo admin_url('admin-ajax.php') . '?action=payment_panel&payment_id=' . $val->payment_id; ?>" data-toggle="slidePanel">
                                            <?php echo bookme_formatPrice(($val->discount_price == "") ? $val->price : ($val->price * $val->person) - $val->discount_price); ?>
                                            <div><?php echo $val->type; ?></div>
                                            <div class="<?php echo ($val->payment_status == 'pending')?'text-danger':'text-success'; ?>"><?php echo $val->payment_status; ?></div>
                                        </a>
                                    <?php } ?>
                                </td>
                                <td><?php echo $val->notes; ?></td>
                                <?php
                                if ($custom_row > 0) {
                                    foreach ($resultField as $rowdata) {
                                        $fieldVal = $wpdb->get_var("SELECT field_val FROM $table_booking_custom_fields WHERE booking_id = $val->id and key_field = '$rowdata->field_name'");
                                        ?>
                                        <td><?php echo $fieldVal; ?></td>
                                    <?php
                                    }
                                }
                                ?>
                                <td>
                                    <p title="Edit">
                                        <button class="btn btn-primary btn-xs bookEdit"
                                                data-url="<?php echo admin_url('admin-ajax.php') . '?action=booking_panel&booking_id=' . $val->booking_id; ?>"
                                                data-toggle="slidePanel"><span
                                                class="md-edit"></span></button>
                                    </p>
                                </td>
                                <td>
                                    <span class="checkbox-custom checkbox-primary checkbox-lg del_booking_checkbox">
                                      <input type="checkbox" class="contacts-checkbox selectable-item" id="<?php echo $val->booking_id; ?>"
                                          />
                                      <label for="contacts_1"></label>
                                    </span>
                                </td>
                            </tr>
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

<!-- Site Action -->
<div class="site-action">
    <button data-url="<?php echo admin_url('admin-ajax.php') . '?action=booking_panel'; ?>" data-toggle="slidePanel"
            id="slidepanel-show" style="display: none;"></button>
    <button type="button" class="site-action-toggle btn-raised btn btn-success btn-floating">
        <i class="front-icon md-plus animation-scale-up" aria-hidden="true"></i>
        <i class="back-icon md-close animation-scale-up" aria-hidden="true"></i>
    </button>
    <div class="site-action-buttons">
        <button type="button" data-action="trash" id="del_booking_array"
                class="btn-raised btn btn-danger btn-floating animation-slide-bottom">
            <i class="icon md-delete" aria-hidden="true"></i>
        </button>
    </div>
</div>
<!-- End Site Action -->
