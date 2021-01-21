<?php
add_action('wp_ajax_nopriv_member_panel', 'bookme_member_panel');
add_action('wp_ajax_member_panel', 'bookme_member_panel');
function bookme_member_panel()
{
    global $wpdb, $help;
    $table_all_employee = $wpdb->prefix . 'bookme_employee';
    $table_book_service = $wpdb->prefix . 'bookme_service';
    $table_member_schedule = $wpdb->prefix . 'bookme_member_schedule';
    $table_book_category = $wpdb->prefix . 'bookme_category';

    $id = '';
    $name = '';
    $img = '';
    $country='';
    $visibility = '';
    $email = '';
    $phone = '';
    $info = '';
    $google_data = '';
    $service_id = array();
    $service_name = array();

    $resultS = $wpdb->get_results("SELECT c.id cat_id, c.name cat_name, group_concat(s.id) service_id, group_concat(s.name) service_name, group_concat(s.ser_icon) service_icon  FROM $table_book_category c LEFT JOIN  $table_book_service s ON s.catId = c.id GROUP BY c.id ORDER BY c.id ");


    if (isset($_GET['member_id'])) {
        $resultE = $wpdb->get_results("SELECT e.*, group_concat(s.id) service_id, group_concat(s.name) service_name  FROM $table_all_employee e LEFT JOIN  $table_book_service s ON find_in_set(e.id,s.staff) <> 0  WHERE e.id = '" . $_GET['member_id'] . "' GROUP BY e.id ORDER BY e.id ");

        foreach ($resultE as $values) {
            $id = $values->id;
            $name = $values->name;
            $img = $values->img;
            $visibility = $values->visibility;
            $email = $values->email;
            $phone = $values->phone;
            $info = $values->info;
            $google_data = $values->google_data;
            $service_id = explode(',', $values->service_id);
            $service_name = explode(',', $values->service_name);
            $country=$values->country;
        }
    }
    ?>
    <div class="app-contacts">
        <header class="slidePanel-header overlay"
                style="background-image: url(<?php echo plugins_url('assets/photos/city-8-960x640.jpg', __FILE__); ?>)">
            <div class="overlay-panel overlay-background vertical-align">
                <div class="vertical-align-middle">
                    <a class="avatar" href="javascript:void(0)" id="emp_img_uploader">
                        <img
                                src="<?php echo !empty($img) ? $img : plugins_url('assets/images/user-default.png', __FILE__); ?>"
                                alt="<?php echo !empty($name) ? $name : ''; ?>">
                        <span class="icon md-camera upload_pic"></span>
                    </a>

                    <h3 class="name"><?php echo !empty($name) ? $name : __('Add new employee'); ?></h3>

                    <div class="tags">
                        <?php foreach ($service_name as $ser) {
                            if ($ser != '') {
                                ?>
                                <span class="label label-default"><?php echo $ser; ?></span>
                            <?php }
                        } ?>
                    </div>
                </div>
            </div>
        </header>
        <div class="slidePanel-actions">
            <div class="btn-group-flat">
                <button type="button"
                        class="btn btn-floating btn-success btn-sm waves-effect waves-float waves-light margin-right-10"
                        id="save_emp_data"><i class="icon md-check" aria-hidden="true"></i></button>
                <?php if (isset($_GET['member_id'])) { ?>
                    <button type="button" class="btn btn-pure btn-inverse icon md-delete font-size-20"
                            aria-hidden="true" id="del_member"></button>
                <?php } ?>
                <button type="button" class="btn btn-pure btn-inverse slidePanel-close icon md-close font-size-20"
                        aria-hidden="true"></button>
            </div>
        </div>
        <div class="slidePanel-inner">
            <div class="panel-body staff-detail">
                <form id="emp_data_form">
                    <?php if (isset($_GET['member_id'])) { ?>
                        <input type="hidden" id="schedule_emp" value="<?php echo $_GET['member_id']; ?>" name="emp_id">
                    <?php } ?>
                    <input type="hidden"
                           value="<?php echo !empty($img) ? $img : plugins_url('assets/images/user-default.png', __FILE__); ?>"
                           id="emp_img_url" name="emp_img_url">

                    <h3 class="heading"><?php _e('Details', 'bookme'); ?></h3>

                    <div class="memberSpin">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <input type="hidden" id="emp_id" value="1">
                                    <label for="bookme-full-name"><?php _e('Full name', 'bookme'); ?></label>
                                    <input type="text" class="form-control" id="bookme-full-name" name="full_name"
                                           value="<?php echo !empty($name) ? $name : ''; ?>">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="bookme-visibility"><?php _e('Visibility', 'bookme'); ?><span
                                                class="dashicons dashicons-editor-help tooltipster"
                                                title="<?php echo $help['STAFF_VISIBILITY'] ?>"></span></label>
                                    <select name="visibility" class="form-control" id="bookme-visibility">
                                        <option
                                                value="1" <?php echo (!empty($visibility) && $visibility == '1') ? 'selected' : ''; ?>>
                                            <?php _e('Public', 'bookme'); ?>
                                        </option>
                                        <option
                                                value="0" <?php echo (!empty($visibility) && $visibility == '0') ? 'selected' : ''; ?>>
                                            <?php _e('Private', 'bookme'); ?>
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="bookme-email"><?php _e('Email', 'bookme'); ?></label>
                                    <input class="form-control" id="bookme-email" name="email"
                                           value="<?php echo !empty($email) ? $email : ''; ?>" type="text">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="bookme-phone"><?php _e('Phone', 'bookme'); ?></label>
                                    <input class="form-control" id="bookme-phone"
                                           value="<?php echo !empty($phone) ? $phone : ''; ?>" type="text"
                                           name="phone">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="bookme-info"><?php _e('Info', 'bookme'); ?><span
                                        class="dashicons dashicons-editor-help tooltipster"
                                        title="<?php echo $help['STAFF_INFO'] ?>"></span></label>

                            <textarea id="bookme-info" name="info" rows="3"
                                      class="form-control"><?php echo !empty($info) ? $info : ''; ?></textarea>
                        </div>
                    </div>
                    <h3 class="heading"><?php _e('Services', 'bookme'); ?><span
                                class="dashicons dashicons-editor-help tooltipster"
                                title="<?php echo $help['STAFF_SERVICE'] ?>"></span></h3>

                    <div class="form-group">
                        <label for="bookme-info"><?php _e('Services', 'bookme'); ?></label>
                        <select multiple="" data-live-search="true" data-plugin="selectpicker" name="services[]">
                            <?php foreach ($resultS as $cat) {
                                $services = explode(',', $cat->service_name);
                                $ser_id = explode(',', $cat->service_id);
                                $ser_icon = explode(',', $cat->service_icon);
                                if ($ser_id[0] != '') {
                                    ?>
                                    <optgroup label="<?php echo $cat->cat_name; ?>">
                                        <?php
                                        for ($i = 0; $i < count($services); $i++) {
                                            ?>
                                            <option data-icon="fa <?php echo $ser_icon[$i]; ?>"
                                                    value="<?php echo $ser_id[$i]; ?>" <?php echo in_array($ser_id[$i], $service_id) ? 'selected' : ''; ?>><?php echo $services[$i]; ?></option>
                                            <?php
                                        }
                                        ?>
                                    </optgroup>
                                <?php }
                            } ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="bookme-info"><?php _e('Country', 'bookme'); ?></label>
                        <select name="country" class="form-control" style="width: 100%">
                            <?php
                            foreach ( get_timezone_list() as $key=>$value ){
                                $sel=$country==$key ? 'selected' : '' ;
                                echo '<option '.$sel.' value="'.$key.'">'.$value.'</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <h3 class="heading"><?php _e('Schedule ( UTC Time )', 'bookme'); ?></h3>

                    <div class="row ">
                        <div class="col-md-2 col-sm-12 padding-top-10"><h4><?php _e('Day off', 'bookme'); ?></h4></div>
                        <div class="col-md-6 col-sm-12 padding-top-10">
                            <h4><?php _e('Time (Start - End)', 'bookme'); ?></h4></div>
                        <div class="col-md-4 col-sm-12 padding-top-10"><h4><?php _e('Break', 'bookme'); ?></h4></div>
                    </div>
                    <?php
                    $day = array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday');

                    for ($i = 0; $i <= 6; $i++) {
                        $empSer = $wpdb->get_results("SELECT * FROM $table_member_schedule WHERE emp_id='" . @$_GET['member_id'] . "' and day='" . $day[$i] . "'");
                        ?>
                        <div class="row">
                            <input type="hidden" name="days[]" id="<?php echo 'schedule_day_' . $i; ?>"
                                   value="<?php echo $day[$i]; ?>">

                            <div class="col-md-2 col-sm-12 padding-top-10">
                                <input type="checkbox" id="id-name--<?php echo $i; ?>"
                                       name="day_off[<?php echo $i; ?>][]"
                                       class="switch-input" <?php echo (empty($empSer[0]->schedule_start) && empty($empSer[0]->schedule_end)) ? 'checked' : ''; ?>><label
                                        for="id-name--<?php echo $i; ?>" class="switch-label"> </label>
                            </div>
                            <div
                                    class="col-md-6 col-sm-12 padding-top-10 <?php echo (empty($empSer[0]->schedule_start) && empty($empSer[0]->schedule_end)) ? 'box-disable' : ''; ?>"
                                    id="schedule-box">
                                <div class="input-daterange" data-plugin="datepicker">
                                    <div class="input-group">
                                        <span class="input-group-addon"><?php echo substr($day[$i], 0, 3); ?></span>
                                        <input id="<?php echo 'schedule_start_' . $i; ?>" type="text"
                                               class="form-control ui-timepicker-input schedule-bookme-time-picker"
                                               autocomplete="off" name="schedule_start[<?php echo $i; ?>][]"
                                               value="<?php echo @$empSer[0]->schedule_start; ?>">
                                    </div>
                                    <div class="input-group">
                                        <span class="input-group-addon">-</span>
                                        <input id="<?php echo 'schedule_end_' . $i; ?>" type="text"
                                               class="form-control ui-timepicker-input schedule-bookme-time-picker"
                                               autocomplete="off" name="schedule_end[<?php echo $i; ?>][]"
                                               value="<?php echo @$empSer[0]->schedule_end; ?>">
                                    </div>
                                </div>
                            </div>
                            <div
                                    class="col-md-4 col-sm-12 padding-top-10 <?php echo (empty($empSer[0]->schedule_start) && empty($empSer[0]->schedule_end)) ? 'box-disable' : ''; ?>"
                                    id="break-box">
                                <div class="btn-group btn-group-sm" id="<?php echo 'break_btn_' . $i; ?>">
                                    <?php if (!empty($empSer[0]->break_start) && !empty($empSer[0]->break_end)) { ?>
                                        <button type="button" class="btn btn-info">
                                            <?php echo $empSer[0]->break_start . " - " . $empSer[0]->break_end; ?>
                                        </button>
                                        <button title="Delete break" type="button" id="delete_break"
                                                class="btn btn-info" data-key="<?php echo $i; ?>"><span>&Cross;</span>
                                        </button>
                                        <input type="hidden" name="break_start[<?php echo $i; ?>][]"
                                               value="<?php echo $empSer[0]->break_start; ?>">
                                        <input type="hidden" name="break_end[<?php echo $i; ?>][]"
                                               value="<?php echo $empSer[0]->break_end; ?>">
                                    <?php } else { ?>
                                        <button type="button" class="btn btn-info webUiPopover" data-trigger="click"
                                                data-style="info" data-animation="fade"
                                                data-title="<?php _e('Add break', 'bookme'); ?>" data-width="300"
                                                data-height="auto">
                                            <?php _e('Add break', 'bookme'); ?>
                                        </button>
                                        <div class="webui-popover-content">
                                            <div class="input-daterange">
                                                <div class="input-box">
                                                    <div class="input-group">
                                                        <input id="<?php echo 'break_start_' . $i; ?>" type="text"
                                                               class="form-control ui-timepicker-input schedule-bookme-time-picker"
                                                               autocomplete="off"
                                                               placeholder="<?php _e('Start', 'bookme'); ?>">
                                                    </div>
                                                    <div class="input-group">
                                                        <span class="input-group-addon">-</span>
                                                        <input id="<?php echo 'break_end_' . $i; ?>" type="text"
                                                               class="form-control ui-timepicker-input schedule-bookme-time-picker"
                                                               autocomplete="off"
                                                               placeholder="<?php _e('End', 'bookme'); ?>">
                                                    </div>
                                                </div>
                                                <div class="input-btn">
                                                    <button type="button"
                                                            class="btn btn-floating btn-success btn-xs waves-effect waves-float waves-light"
                                                            data-key="<?php echo $i; ?>" id="addBreak"><i
                                                                class="icon md-check" aria-hidden="true"></i></button>
                                                </div>
                                            </div>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                    ?>
                </form>
                <?php
                if (isset($_GET['member_id'])) {
                    ?>
                    <h3 class="heading"><?php _e('Google Calendar integration', 'bookme'); ?></h3>
                    <p>

                    <?php
                    if (bookme_get_settings('bookme_gc_client_id') != null) {
                        include_once plugin_dir_path(__FILE__) . '../includes/google.php';
                        $bookme_gc_client = new Google_Client();
                        $bookme_gc_client->setClientId(bookme_get_settings('bookme_gc_client_id'));
                        $bookme_gc_client->setClientSecret(bookme_get_settings('bookme_gc_client_secret'));
                        if ($google_data == '' || $google_data == null) {
                            ?><a
                            href="<?php echo bookme_create_auth_url($_GET['member_id'], $bookme_gc_client); ?>"><?php _e('Click here to connect', 'bookme') ?></a><?php
                        } else {
                            ?>
                            <p style="font-weight: 600;"><?php _e('Connected', 'bookme'); ?> (<a
                                        href="<?php echo admin_url('admin.php?page=bookme-staff&google_logout=' . $_GET['member_id']); ?>"><?php _e('Disconnect', 'bookme'); ?></a>)
                            </p>
                            <?php
                            try {
                                $bookme_gc_client->setAccessToken($google_data);
                                if ($bookme_gc_client->isAccessTokenExpired()) {
                                    $bookme_gc_client->refreshToken($bookme_gc_client->getRefreshToken());
                                    $wpdb->update($table_all_employee, array('google_data' => $bookme_gc_client->getAccessToken()), array('id' => $_GET['member_id']), array('%s'), array('%d'));
                                }
                                $bookme_gc_service = new Google_Service_Calendar($bookme_gc_client);
                                $google_calendars = bookme_get_calendar_list($bookme_gc_service);
                                ?>
                                <label for="google_calendar_id"><?php _e('Calendar', 'bookme') ?></label>
                                <select class="form-control" name="google_calendar_id" id="google_calendar_id">
                                    <?php foreach ($google_calendars as $id => $calendar) : ?>
                                        <option
                                                value="<?php echo esc_attr($id) ?>">
                                            <?php echo esc_html($calendar['summary']) ?>
                                        </option>
                                    <?php endforeach ?>
                                </select>
                                <?php
                            } catch (Exception $e) {

                            }
                        }
                    } else {
                        printf(__('Please configure Google Calendar <a href="%s">settings</a> first', 'bookme'), esc_url(admin_url('admin.php?page=bookme-settings')));
                    } ?>
                    </p>
                <?php } ?>
            </div>
        </div>
    </div>
    <script>
    (function($){
    var timeformat = '<?php echo get_option('time_format'); ?>';

        $('.tooltipster').tooltipster({
            theme: 'tooltipster-borderless',
            plugins: ['follower'],
            maxWidth: 300,
            delay: 100
        });
        $('#bookme-phone').intlTelInput({
            preferredCountries: ["us", "br", "gb", "in"],
            initialCountry: "auto",
            geoIpLookup: function (callback) {
                $.get('https://ipinfo.io', function () {
                }, "jsonp").always(function (resp) {
                    var countryCode = (resp && resp.country) ? resp.country : "";
                    callback(countryCode);
                });
            }
        });

        $('[data-plugin="selectpicker"]').selectpicker({
            style: "btn-select", iconBase: "icon", tickIcon: "md-check"
        });

        $('.switch-input').on('click', function () {
            $(this).parent().parent().find('#schedule-box').toggleClass('box-disable');
            $(this).parent().parent().find('#break-box').toggleClass('box-disable');
        });

        $('.schedule-bookme-time-picker').timepicker({'step': 15, 'timeFormat': timeformat,'show2400':true});


        $('.webUiPopover').webuiPopover({closeable: true, dismissible: false});

        $('.scrollable-container').on('scroll', function () {
            $(window).scroll();
            $('.ui-timepicker-wrapper').fadeOut();
            $('.webui-popover').fadeOut();
            $('.webUiPopover').webuiPopover('hide');
        });

        $(document).on('slidePanel::beforeHide', function (e) {
            $('.ui-timepicker-wrapper').fadeOut();
            $('.webui-popover').fadeOut();
            $('.webUiPopover').webuiPopover('hide');
        });
        })(jQuery);
    </script>
    <?php
    wp_die();
}