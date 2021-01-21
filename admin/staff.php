<?php

global $wpdb;
$table_all_employee = $wpdb->prefix . 'bookme_employee';
$table_book_service = $wpdb->prefix . 'bookme_service';
$table_member_schedule = $wpdb->prefix . 'bookme_member_schedule';
$table_current_booking = $wpdb->prefix . 'bookme_current_booking';

if (isset($_GET['code'])) {
    include_once plugin_dir_path(__FILE__) . '../includes/google.php';
    $bookme_gc_client = new Google_Client();
    $bookme_gc_client->setClientId(bookme_get_settings('bookme_gc_client_id'));
    $bookme_gc_client->setClientSecret(bookme_get_settings('bookme_gc_client_secret'));
    $bookme_gc_client->setRedirectUri(admin_url('admin.php?page=bookme-staff'));
    try {
        $success_auth = $bookme_gc_client->authenticate($_GET['code']);
        if ($success_auth) {
            $staff_id = base64_decode(strtr($_GET['state'], '-_,', '+/='));
            $access_token = $bookme_gc_client->getAccessToken();
            $wpdb->update($table_all_employee, array('google_data' => $access_token), array('id' => $staff_id), array('%s'), array('%d'));
        }
    } catch (Exception $e) {
        //echo $e->getMessage();
    }
    exit ('<script>location.href="' . admin_url('admin.php?page=bookme-staff') . '";</script>');
}

if (isset($_GET['google_logout'])) {
    include_once plugin_dir_path(__FILE__) . '../includes/google.php';
    $bookme_gc_client = new Google_Client();
    $bookme_gc_client->setClientId(bookme_get_settings('bookme_gc_client_id'));
    $bookme_gc_client->setClientSecret(bookme_get_settings('bookme_gc_client_secret'));

    $google_data = $wpdb->get_var("SELECT google_data FROM $table_all_employee WHERE id=" . $_GET['google_logout']);
    try {
        $bookme_gc_client->setAccessToken($google_data);
        if ($bookme_gc_client->isAccessTokenExpired()) {
            $bookme_gc_client->refreshToken($bookme_gc_client->getRefreshToken());
            $wpdb->update($table_all_employee, array('google_data' => $bookme_gc_client->getAccessToken()), array('id' => $_GET['google_logout']), array('%s'), array('%d'));
        }
        $bookme_gc_client->revokeToken($google_data);
        $wpdb->update($table_all_employee, array('google_data' => null), array('id' => $_GET['google_logout']), array('%d'), array('%d'));
    } catch (Exception $e) {

    }
    exit ('<script>location.href="' . admin_url('admin.php?page=bookme-staff') . '";</script>');
}

/* Faculty filter */
$user = wp_get_current_user();
$filter='';
if ( !in_array( 'administrator', (array) $user->roles ) ) {
    $filter=" where e.email='".$user->user_email."' ";
}
$resultE = $wpdb->get_results("SELECT e.id,e.name,e.visibility,e.img, group_concat(s.id) service_id, group_concat(s.name) service_name, group_concat(s.ser_icon) service_icon, group_concat(s.duration) service_duration, group_concat(s.paddingBefore) service_paddingB FROM $table_all_employee e LEFT JOIN  $table_book_service s ON find_in_set(e.id,s.staff) <> 0 ".$filter." GROUP BY e.id ORDER BY e.id ");
$num_row = $wpdb->num_rows;

?>

<div class="app-work">
    <div class="page ">
        <div class="page-content">
            <?php include(plugin_dir_path(__FILE__) . '/header.php'); ?>
            <div class="panel">
                <div class="panel-heading">
                    <h1 class="panel-title"><?php _e('Staff members', 'bookme'); ?></h1>

                    <div class="pull-right panel-search-form">
                        <div class="input-search">
                            <i class="input-search-icon md-search" aria-hidden="true"></i>
                            <input type="text" class="form-control" id="member-live-search" name="search"
                                   placeholder="<?php _e('Search List', 'bookme'); ?>">
                        </div>
                    </div>
                </div>
                <div class="panel-body">
                    <table class="table is-indent tablesaw" data-tablesaw-mode="stack" data-plugin="animateList"
                           data-animate="fade" data-child="tr" data-selectable="selectable">
                        <thead>
                        <tr>
                            <th class="cell-50">
                             <span class="checkbox-custom checkbox-primary checkbox-lg contacts-select-all">
                              <input type="checkbox" class="contacts-checkbox selectable-all" id="select_all"
                              />
                              <label for="select_all"></label>
                            </span>
                            </th>
                            <th><?php _e('Name & Visibility', 'bookme'); ?></th>
                            <th><?php _e('Services', 'bookme'); ?></th>
                            <th><?php _e('Schedule', 'bookme'); ?></th>
                            <th><?php _e('Actions', 'bookme'); ?></th>
                        </tr>
                        </thead>
                        <tbody id="ajax_members">
                        <?php
                        if ($num_row > 0) {
                            foreach ($resultE as $value) { ?>
                                <tr id="mem_row_<?php echo $value->id; ?>"
                                    data-search-term="<?php echo strtolower($value->name . ' ' . $value->service_name); ?>">
                                    <td class="cell-50">
                            <span class="checkbox-custom checkbox-primary checkbox-lg del_emp_checkbox">
                              <input type="checkbox" class="contacts-checkbox selectable-item"
                                     id="<?php echo $value->id; ?>"
                              />
                              <label for="contacts_1"></label>
                            </span>
                                    </td>
                                    <td class="subject">
                                        <div class="table-content">
                                <span class="avatar">
                                    <img class="img-responsive" src="<?php echo $value->img; ?>"
                                         alt="<?php echo $value->name; ?>">
                                </span>

                                            <div class="avatar-name"><?php echo $value->name; ?><br>

                                                <?php
                                                if ($value->visibility == 1) {
                                                    echo '<label class="label label-warning">' . __('Public', 'bookme') . '</label>';
                                                } else {
                                                    echo '<label class="label label-danger">' . __('Private', 'bookme') . '</label>';
                                                } ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="staff-services">
                                        <div class="table-content">
                                            <?php
                                            $services = explode(',', $value->service_name);
                                            $ser_icon = explode(',', $value->service_icon);
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
                                        </div>
                                    </td>
                                    <td class="schedule">
                                        <div class="table-content schedule-box">
                                            <?php
                                            $day = array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday');
                                            $color = array('success', 'warning', 'info', 'primary', 'info', 'warning', 'success');

                                            for ($i = 0; $i <= 6; $i++) {
                                                $empSer = $wpdb->get_results("SELECT * FROM $table_member_schedule WHERE emp_id='" . $value->id . "' and day='" . $day[$i] . "'");
                                                ?>
                                                <div class="label label-<?php echo $color[$i]; ?> webUiPopover"
                                                     data-trigger="hover" data-style="<?php echo $color[$i]; ?>"
                                                     data-title="<?php echo $day[$i]; ?>" data-width="200px"
                                                     data-height="auto"
                                                     data-animation="fade"><?php echo substr($day[$i], 0, 1); ?>
                                                </div>
                                                <div class="webui-popover-content">
                                                    <p><strong><?php _e('Timing', 'bookme'); ?></strong> : <label
                                                                class="label label-info">
                                                            <?php if (!empty($empSer[0]->schedule_start) && !empty($empSer[0]->schedule_end)) {
                                                                echo $empSer[0]->schedule_start . " - " . $empSer[0]->schedule_end;
                                                            } else {
                                                                _e('No available', 'bookme');
                                                            } ?></label>
                                                    </p>

                                                    <p class="break"><strong><?php _e('Break', 'bookme'); ?></strong> :
                                                        <label class="label label-warning">
                                                            <?php if (!empty($empSer[0]->break_start) && !empty($empSer[0]->break_end)) {
                                                                echo $empSer[0]->break_start . " - " . $empSer[0]->break_end;
                                                            } else {
                                                                _e('No available', 'bookme');
                                                            } ?></label></p>
                                                </div>
                                                <?php
                                            }
                                            ?>
                                        </div>
                                    </td>
                                    <td class="actions">
                                        <div class="table-content">
                                            <button class="btn btn-xs btn-info margin-top-5"
                                                    data-url="<?php echo admin_url('admin-ajax.php') . '?action=member_panel&member_id=' . $value->id; ?>"
                                                    data-toggle="slidePanel"><span class="icon md-edit"
                                                ></span> <?php _e('Edit', 'bookme'); ?></button>
                                            <button class="btn btn-xs btn-warning margin-top-5"
                                                    data-url="<?php echo admin_url('admin-ajax.php') . '?action=dayoff_panel&member_id=' . $value->id; ?>"
                                                    data-toggle="slidePanel"><span
                                                        class="icon md-case"></span> <?php _e('Day off', 'bookme'); ?>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <?php
                            }
                        } else {
                            ?>
                            <tr>
                                <td colspan="5"><?php _e('No members created.', 'bookme'); ?></td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Site Action -->
    <!-- Only for admin -->
    <?php
    if( $user->has_cap('manage_bookme_service_add') ){
        ?>
        <div class="site-action">
            <button data-url="<?php echo admin_url('admin-ajax.php') . '?action=member_panel'; ?>" data-toggle="slidePanel"
                    id="slidepanel-show" style="display: none;"></button>
            <button type="button" class="site-action-toggle btn-raised btn btn-success btn-floating">
                <i class="front-icon md-plus animation-scale-up" aria-hidden="true"></i>
                <i class="back-icon md-close animation-scale-up" aria-hidden="true"></i>
            </button>
            <div class="site-action-buttons">
                <button type="button" data-action="trash"
                        class="btn-raised btn btn-danger btn-floating animation-slide-bottom" id="del_member_array">
                    <i class="icon md-delete" aria-hidden="true"></i>
                </button>
            </div>
        </div>
        <?php
    }
    ?>

    <!-- End Site Action -->
</div>
