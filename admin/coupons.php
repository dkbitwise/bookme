<?php
global $wpdb;
$table_coupon = $wpdb->prefix . 'bookme_coupons';
$table_book_service = $wpdb->prefix . 'bookme_service';

?>

<div class="page">
    <div>
        <?php include(plugin_dir_path(__FILE__) . '/header.php'); ?>
        <div class="panel">
            <header class="panel-heading">
                <h3 class="panel-title"><?php _e('Coupons', 'bookme'); ?></h3>
            </header>
            <div class="panel-body">

                <div class="table-responsive">
                    <table id="coupon_table" class="table table-hover dataTable table-striped width-full border-table"
                           data-tablesaw-mode="stack" data-plugin="animateList"
                           data-animate="fade" data-child="tr" data-selectable="selectable">
                        <thead>
                        <th><?php _e('Sr. No', 'bookme'); ?></th>
                        <th><?php _e('Code', 'bookme'); ?></th>
                        <th><?php _e('Discount', 'bookme'); ?></th>
                        <th><?php _e('Deduction', 'bookme'); ?></th>
                        <th><?php _e('Services', 'bookme'); ?></th>
                        <th><?php _e('Usage limit', 'bookme'); ?></th>
                        <th data-priority="2" class="no-sort hidden-print"><?php _e('Edit', 'bookme'); ?></th>
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
                        $resultcoupan = $wpdb->get_results("SELECT c.*, group_concat(s.name) sname, group_concat(s.ser_icon) sicon FROM $table_coupon c LEFT JOIN $table_book_service s ON find_in_set(s.id,c.ser_id) <> 0 GROUP BY c.id ORDER BY c.id DESC");
                        $a = 1;
                        foreach ($resultcoupan as $code) {
                            ?>
                            <tr id="coupon_id_<?php echo $code->id; ?>">
                                <td><b><?php echo $a++; ?></b></td>
                                <td><?php echo $code->coupon_code; ?>  </td>

                                <td><?php echo $code->discount; ?>  </td>
                                <td><?php echo $code->deduction; ?>  </td>
                                <td>
                                    <?php
                                    $services = explode(',', $code->sname);
                                    $ser_icon = explode(',', $code->sicon);
                                    for ($i = 0; $i < count($services); $i++) {
                                        if ($services[$i] != "") {
                                            ?>
                                            <span class="label label-info"><i
                                                    class="fa <?php echo $ser_icon[$i]; ?>"></i> <?php echo $services[$i]; ?></span>
                                        <?php } else {
                                            ?>
                                            <span><?php _e('No services.', 'bookme'); ?></span>
                                        <?php
                                        }
                                    } ?>
                                </td>
                                <td><?php echo $code->usage_limit; ?>  </td>
                                <td>
                                    <p title="<?php _e('Edit', 'bookme'); ?>">
                                        <button type="button" class="btn btn-primary btn-xs icon"
                                                data-url="<?php echo admin_url('admin-ajax.php') . '?action=coupon_panel&coupon_id='.$code->id;; ?>"
                                                data-toggle="slidePanel"><i class="md-edit"></i>
                                        </button>
                                    </p>
                                </td>
                                <td><span class="checkbox-custom checkbox-primary checkbox-lg del_coupon_checkbox" >
              <input type="checkbox" class="contacts-checkbox selectable-item" id="<?php echo $code->id;?>"/>
              <label for="contacts_1"></label>
            </span>
                                </td>

                            </tr>
                        <?php } ?>

                        </tbody>
                    </table>
                    <br/>

                    <!-- Site Action -->
                    <div class="site-action">
                        <button data-url="<?php echo admin_url('admin-ajax.php') . '?action=coupon_panel'; ?>"
                                data-toggle="slidePanel" id="slidepanel-show" style="display: none;"></button>
                        <button type="button" class="site-action-toggle btn-raised btn btn-success btn-floating">
                            <i class="front-icon md-plus animation-scale-up" aria-hidden="true"></i>
                            <i class="back-icon md-close animation-scale-up" aria-hidden="true"></i>
                        </button>
                        <div class="site-action-buttons">
                            <button type="button" data-action="trash"
                                    class="btn-raised btn btn-danger btn-floating animation-slide-bottom" id="del_coupon_array">
                                <i class="icon md-delete" aria-hidden="true"></i>
                            </button>
                        </div>
                    </div>
                    <!-- End Site Action -->

                </div>
            </div>
        </div>
    </div>
</div>