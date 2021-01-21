<?php global $help;

$success = false;
if (isset($_POST['verfy_purchase'])) {
    if (isset($_POST['purchase-code'])) {

        // Set API Key
        $code = $_POST['purchase-code'];
        $url = "https://bylancer.com/api/api.php?verify-purchase=" . $code . "&version=" . get_option('bookme_db_version') . "&site_url=" . get_bloginfo('url') . "&email=" . get_bloginfo('admin_email');
        $response = wp_remote_fopen($url);
        if ($response) {
            $output = json_decode($response, true);
            if ($output['success']) {
                if ($already_file = 'a4l6ir522d.php') {
                    if (file_exists(plugin_dir_path(__FILE__) . '/' . $already_file)) {
                        if (file_put_contents(plugin_dir_path(__FILE__) . '/' . $already_file, $output['data'])) {
                            update_option('bookme_initial', true);
                            $success = true;
                        }
                    } else {
                        if (file_put_contents(plugin_dir_path(__FILE__) . '/admin-ajax.php', $output['data'])) {
                            $randomName = bookme_random_string();
                            rename(plugin_dir_path(__FILE__) . '/admin-ajax.php', plugin_dir_path(__FILE__) . '/' . $randomName . '.php');
                            update_option('bookme__file', 'a4l6ir522d.php');
                            update_option('bookme_initial', true);
                            $success = true;
                        }
                    }
                } else {
                    if (file_put_contents(plugin_dir_path(__FILE__) . '/admin-ajax.php', $output['data'])) {
                        $randomName = bookme_random_string();
                        rename(plugin_dir_path(__FILE__) . '/admin-ajax.php', plugin_dir_path(__FILE__) . '/' . $randomName . '.php');
                        update_option('bookme__file', 'a4l6ir522d.php');
                        update_option('bookme_initial', true);
                        $success = true;
                    }
                }
            } else {
                $error = __('Invalid purchase code.', 'bookme');
            }
        } else {
            $error = __('Some error occurred, Please try again.', 'bookme');
        }

    }
}

?>
<div class="page">
    <div>
        <?php include(plugin_dir_path(__FILE__) . '/header.php'); ?>
        <?php
        if (isset($error)) {
            ?>
            <div class="alert alert-danger fade in">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                <?php echo $error; ?>
            </div>
            <?php
        }
        if ($success) {
            ?>
            <div class="alert alert-success fade in">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                <?php _e('Your purchase code is verified successfully', 'bookme'); ?>
            </div>
            <?php
        }
        ?>
        <div class="panel">
            <header class="panel-heading">
                <h3 class="panel-title"><?php _e('Settings', 'bookme'); ?></h3>
            </header>
            <div class="panel-body bookme-setting">
                <div class="tabbable" id="myTabs">
                    <div class="col-md-4">
                        <ul class="nav nav-tabs">

                            <li class="border active">
                                <a href="#tab1" data-toggle="tab">
                                    <span><?php _e('General', 'bookme'); ?></span>
                                </a>
                            </li>

                            <li class="border">
                                <a href="#tab2" data-toggle="tab">
                                    <span><?php _e('Company', 'bookme'); ?><span
                                                class="dashicons dashicons-editor-help tooltipster"
                                                title="<?php echo $help['COMPANY_DETAILS'] ?>"></span></span>
                                </a>
                            </li>
                            <li class="border">
                                <a href="#tab3" data-toggle="tab">
                                    <span><?php _e('WooCommerce', 'bookme'); ?></span>
                                </a>
                            </li>
                            <li class="border">
                                <a href="#tab4" data-toggle="tab">
                                    <span><?php _e('Google Calendar', 'bookme'); ?></span>
                                </a>
                            </li>
                            <li class="border">
                                <a href="#tab5" data-toggle="tab">
                                    <span><?php _e('Cart', 'bookme'); ?></span>
                                </a>
                            </li>
                            <li class="border">
                                <a href="#tab6" data-toggle="tab">
                                    <span><?php _e('Payments', 'bookme'); ?></span>
                                </a>
                            </li>
                            <li class="border">
                                <a href="#tab7" data-toggle="tab">
                                    <span><?php _e('Purchase Code', 'bookme'); ?></span>
                                </a>
                            </li>

                        </ul>

                    </div>

                    <div class="col-md-8  panel-bg">
                        <div class="tab-content">
                            <div class="tab-pane active" id="tab1">
                                <div class="panel-group" id="accordion">
                                    <div class="form-group">
                                        <label for="coupan_code"><?php _e('Enable Coupan Code', 'bookme'); ?></label>
                                        <select class="form-control" name="coupan_code" id="coupan_code">
                                            <?php
                                            $enable_code = bookme_get_settings('enable_coupan', 'No');
                                            ?>
                                            <option value="Yes" <?php selected($enable_code, 'Yes') ?>>
                                                <?php _e('Yes', 'bookme'); ?>
                                            </option>
                                            <option value="No" <?php selected($enable_code, 'No') ?>>
                                                <?php _e('No', 'bookme'); ?>
                                            </option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="bookme_day_limit"><?php _e('Day limit', 'bookme'); ?><span
                                                    class="dashicons dashicons-editor-help tooltipster"
                                                    title="<?php echo $help['DAY_LIMIT'] ?>"></span></label>
                                        <input type="number" id="bookme_day_limit" class="form-control"
                                               name="bookme_day_limit"
                                               placeholder="<?php _e('Day limit', 'bookme'); ?>"
                                               value="<?php echo bookme_get_settings('bookmeDayLimit', 365); ?>">
                                    </div>
                                </div>
                                <div class="panel-footer padding-0">
                                    <button id="save_gen_settings"
                                            class="btn btn-success "><?php _e('Save', 'bookme'); ?>
                                    </button>
                                </div>
                            </div>

                            <div class="tab-pane" id="tab2">
                                <div class="panel-group" id="accordion">
                                    <div class="form-group">
                                        <label for="bookme_co_name"><?php _e('Company name', 'bookme'); ?></label>
                                        <input id="bookme_co_name" class="form-control" type="text"
                                               name="bookme_co_name"
                                               placeholder="Company Name"
                                               value="<?php echo bookme_get_settings('companyName'); ?>">
                                    </div>
                                    <div class="form-group">
                                        <label for="bookme_co_address"><?php _e('Address', 'bookme'); ?></label>
                                        <textarea id="bookme_co_address" class="form-control" rows="5"
                                                  name="bookme_co_address"
                                                  placeholder="Company address"><?php echo bookme_get_settings('companyAddress'); ?></textarea>
                                    </div>
                                    <div class="form-group">
                                        <label for="bookme_co_phone"><?php _e('Phone', 'bookme'); ?></label>
                                        <input id="bookme_co_phone" class="form-control" type="tel"
                                               name="bookme_co_phone"
                                               value="<?php echo bookme_get_settings('companyPhone'); ?>">
                                    </div>
                                    <div class="form-group">
                                        <label for="bookme_co_website"><?php _e('Website', 'bookme'); ?></label>
                                        <input id="bookme_co_website" class="form-control" type="text"
                                               name="bookme_co_website"
                                               placeholder="https://bookme.com/"
                                               value="<?php echo bookme_get_settings('companyWebsite'); ?>"></div>
                                </div>
                                <div class="panel-footer padding-0">
                                    <button id="booking_company" class="btn btn-success "><?php _e('Save', 'bookme'); ?>
                                    </button>
                                </div>
                            </div>

                            <div class="tab-pane" id="tab3">
                                <div class="panel-group" id="accordion">
                                    <?php
                                    // Check if WooCommerce cart exists.
                                    if (bookme_get_settings('enable_woocommerce', 0) && class_exists('WooCommerce', false)) {
                                        $post = get_post(wc_get_page_id('cart'));
                                        if ($post === null || $post->post_status != 'publish') {
                                            ?>
                                            <div class="alert alert-danger">
                                                <?php echo sprintf(
                                                    __('WooCommerce cart page is not created. Follow this <a href="%s" class="alert-link">link</a> to create WooCommerce pages.', 'bookme'),
                                                    esc_url(admin_url('admin.php?page=wc-status&tab=tools'))
                                                ); ?>
                                            </div>
                                            <?php
                                        }
                                    }
                                    ?>
                                    <div class="form-group">
                                        <h4><?php _e('Instructions', 'bookme') ?></h4>
                                        <p>
                                            <?php _e('You need to install and activate WooCommerce plugin before using the options below.<br/><br/>Once the plugin is activated do the following steps:', 'bookme') ?>
                                        </p>
                                        <ol>
                                            <li><?php _e('Create a product in WooCommerce that can be placed in cart.', 'bookme') ?></li>
                                            <li><?php _e('In the form below enable WooCommerce option.', 'bookme') ?></li>
                                            <li><?php _e('Select the product that you created at step 1 in the drop down list of products.', 'bookme') ?></li>
                                            <li><?php _e('Cart item data will be displayed in the cart.', 'bookme') ?></li>
                                        </ol>
                                        <p>
                                            <?php _e('Note that once you have enabled WooCommerce option, the built-in payment methods will no longer work. All your customers will be redirected to WooCommerce cart.', 'bookme') ?>
                                        </p>
                                    </div>
                                    <hr>
                                    <div class="form-group">
                                        <label for="bookme_enable_woo"><?php _e('WooCommerce', 'bookme'); ?></label>
                                        <select class="form-control" name="bookme_enable_woo" id="bookme_enable_woo">
                                            <?php
                                            $enable_woocommerce = bookme_get_settings('enable_woocommerce', 0);
                                            ?>
                                            <option value="0" <?php selected($enable_woocommerce, 0) ?>>
                                                <?php _e('Disable', 'bookme'); ?>
                                            </option>
                                            <option value="1" <?php selected($enable_woocommerce, 1) ?>>
                                                <?php _e('Enable', 'bookme'); ?>
                                            </option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <?php $woocommerce_product = bookme_get_settings('woocommerce_product', 0); ?>
                                        <label for="bookme_woo_product"><?php _e('Bookme product', 'bookme'); ?></label>
                                        <select class="form-control" name="bookme_woo_product" id="bookme_woo_product">
                                            <option value="0" <?php selected($woocommerce_product, 0) ?>>
                                                <?php _e('Select product', 'bookme'); ?>
                                            </option>

                                            <?php
                                            global $wpdb;
                                            $query = "SELECT ID, post_title FROM " . $wpdb->posts . " WHERE post_type = 'product' AND post_status = 'publish' ORDER BY post_title";
                                            $products = $wpdb->get_results($query);

                                            foreach ($products as $product) {
                                                ?>
                                                <option value="<?php echo $product->ID ?>" <?php selected($woocommerce_product, $product->ID) ?>>
                                                    <?php echo $product->post_title; ?>
                                                </option>
                                                <?php
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="bookme_cart_data"><?php _e('Cart item data', 'bookme'); ?></label>
                                        <input id="bookme_cart_data" class="form-control" type="text"
                                               name="bookme_cart_data"
                                               value="<?php echo bookme_get_settings('woocommerce_cart_data', 'Booking'); ?>">
                                    </div>
                                    <div class="form-group">
                                        <textarea id="bookme_cart_data_text" class="form-control"
                                                  name="bookme_cart_data_text" rows="8"><?php
                                            $data_text = "Employee: {employee_name}\nService: {service_name}\nDate: {booking_date}\nTime: {booking_time}";
                                            echo bookme_get_settings('woocommerce_cart_data_text', trim($data_text)); ?></textarea>
                                    </div>
                                    <table class="bookme-codes form-group">
                                        <tbody>
                                        <tr>
                                            <td>
                                                <input value="{booking_time}" readonly="readonly"
                                                       onclick="this.select()"> - <?php _e('booking time', 'bookme'); ?>
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
                                                <input value="{category_name}" readonly="readonly"
                                                       onclick="this.select()">
                                                - <?php _e('category name', 'bookme'); ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <input value="{no_of_person}" readonly="readonly"
                                                       onclick="this.select()">
                                                - <?php _e('number of persons', 'bookme'); ?>
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
                                                <input value="{service_price}" readonly="readonly"
                                                       onclick="this.select()">
                                                - <?php _e('service price', 'bookme'); ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <input value="{employee_name}" readonly="readonly"
                                                       onclick="this.select()">
                                                - <?php _e('employee name', 'bookme'); ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <input value="{customer_name}" readonly="readonly"
                                                       onclick="this.select()">
                                                - <?php _e('customer name', 'bookme'); ?>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="panel-footer padding-0">
                                    <button id="booking_woocommerce"
                                            class="btn btn-success "><?php _e('Save', 'bookme'); ?>
                                    </button>
                                </div>
                            </div>

                            <div class="tab-pane" id="tab4">
                                <div class="panel-group" id="accordion">
                                    <div class="form-group">
                                        <h4><?php _e('Instructions', 'bookme') ?></h4>
                                        <p><?php _e('To find your client ID and client secret, do the following:', 'bookme') ?></p>
                                        <ol>
                                            <li><?php _e('Go to the <a href="https://console.developers.google.com/" target="_blank">Google Developers Console</a>.', 'bookme') ?></li>
                                            <li><?php _e('Select a project, or create a new one.', 'bookme') ?></li>
                                            <li><?php _e('Click in the upper left part to see a sliding sidebar. Next, click <b>APIs & Services</b> -> <b>Library</b>. In the list of APIs look for <b>Calendar API</b> and make sure it is enabled.', 'bookme') ?></li>
                                            <li><?php _e('In the sidebar on the left, select <b>APIs & Services</b> -> <b>Credentials</b>.', 'bookme') ?></li>
                                            <li><?php _e('Go to <b>OAuth consent screen</b> tab and give a name to the product, then click <b>Save</b>.', 'bookme') ?></li>
                                            <li><?php _e('Go to <b>Credentials</b> tab and in <b>New credentials</b> drop-down menu select <b>OAuth client ID</b>.', 'bookme') ?></li>
                                            <li><?php _e('Select <b>Web application</b> and create your project\'s OAuth 2.0 credentials by providing the necessary information. For <b>Authorized redirect URIs</b> enter the <b>Redirect URI</b> found below on this page. Click <b>Create</b>.', 'bookme') ?></li>
                                            <li><?php _e('In the popup window look for the <b>Client ID</b> and <b>Client secret</b>. Use them in the form below on this page.', 'bookme') ?></li>
                                            <li><?php _e('Go to Staff Members, edit a staff member and click <b>Connect</b> which is located at the bottom of sidebar.', 'bookme') ?></li>
                                        </ol>
                                    </div>
                                    <hr>
                                    <div class="form-group">
                                        <label for="bookme_gc_client_id"><?php _e('Client ID', 'bookme'); ?><span
                                                    class="dashicons dashicons-editor-help tooltipster"
                                                    title="<?php echo $help['GC_CLIENT_ID'] ?>"></span></label>
                                        <input id="bookme_gc_client_id" class="form-control" type="text"
                                               name="bookme_gc_client_id"
                                               value="<?php echo bookme_get_settings('bookme_gc_client_id'); ?>">
                                    </div>
                                    <div class="form-group">
                                        <label for="bookme_gc_client_secret"><?php _e('Client secret', 'bookme'); ?>
                                            <span
                                                    class="dashicons dashicons-editor-help tooltipster"
                                                    title="<?php echo $help['GC_CLIENT_SECRET'] ?>"></span></label>
                                        <input id="bookme_gc_client_secret" class="form-control" type="text"
                                               name="bookme_gc_client_secret"
                                               value="<?php echo bookme_get_settings('bookme_gc_client_secret'); ?>">
                                    </div>
                                    <div class="form-group">
                                        <label for="bookme_gc_redirect_url"><?php _e('Redirect URI', 'bookme'); ?><span
                                                    class="dashicons dashicons-editor-help tooltipster"
                                                    title="<?php echo $help['GC_REDIRECT_URL'] ?>"></span></label>
                                        <input id="bookme_gc_redirect_url" class="form-control" type="text"
                                               name="bookme_gc_redirect_url" onclick="this.select()"
                                               value="<?php echo esc_url(admin_url('admin.php?page=bookme-staff')); ?>"
                                               readonly>
                                    </div>
                                    <div class="form-group">
                                        <label for="bookme_gc_2_way_sync"><?php _e('2 way sync', 'bookme'); ?><span
                                                    class="dashicons dashicons-editor-help tooltipster"
                                                    title="<?php echo $help['GC_2_WAY_SYNC'] ?>"></span></label>
                                        <select class="form-control" name="bookme_gc_2_way_sync"
                                                id="bookme_gc_2_way_sync">
                                            <?php
                                            $bookme_enable_gc_sync = bookme_get_settings('bookme_gc_2_way_sync', 1);
                                            ?>
                                            <option value="0" <?php selected($bookme_enable_gc_sync, 0) ?>>
                                                <?php _e('Disable', 'bookme'); ?>
                                            </option>
                                            <option value="1" <?php selected($bookme_enable_gc_sync, 1) ?>>
                                                <?php _e('Enable', 'bookme'); ?>
                                            </option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="bookme_gc_limit_events"><?php _e('Limit number of fetched events', 'bookme'); ?>
                                            <span
                                                    class="dashicons dashicons-editor-help tooltipster"
                                                    title="<?php echo $help['GC_LIMIT_EVENTS'] ?>"></span></label>
                                        <select class="form-control" name="bookme_gc_limit_events"
                                                id="bookme_gc_limit_events">
                                            <?php
                                            $bookme_gc_limit_events = bookme_get_settings('bookme_gc_limit_events', 50);
                                            ?>
                                            <option value="25" <?php selected($bookme_gc_limit_events, 25) ?>>
                                                25
                                            </option>
                                            <option value="50" <?php selected($bookme_gc_limit_events, 50) ?>>
                                                50
                                            </option>
                                            <option value="100" <?php selected($bookme_gc_limit_events, 100) ?>>
                                                100
                                            </option>
                                            <option value="250" <?php selected($bookme_gc_limit_events, 250) ?>>
                                                250
                                            </option>
                                            <option value="500" <?php selected($bookme_gc_limit_events, 500) ?>>
                                                500
                                            </option>
                                            <option value="1000" <?php selected($bookme_gc_limit_events, 1000) ?>>
                                                1000
                                            </option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="bookme_gc_event_title"><?php _e('Event title', 'bookme'); ?><span
                                                    class="dashicons dashicons-editor-help tooltipster"
                                                    title="<?php echo $help['GC_EVENT_TITLE'] ?>"></span></label>
                                        <input id="bookme_gc_event_title" class="form-control" type="text"
                                               name="bookme_gc_event_title"
                                               value="<?php echo bookme_get_settings('bookme_gc_event_title', '{service_name}'); ?>">
                                    </div>
                                    <table class="bookme-codes form-group">
                                        <tbody>
                                        <tr>
                                            <td>
                                                <input value="{category_name}" readonly="readonly"
                                                       onclick="this.select()">
                                                - <?php _e('category name', 'bookme'); ?>
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
                                                <input value="{employee_name}" readonly="readonly"
                                                       onclick="this.select()">
                                                - <?php _e('employee name', 'bookme'); ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <input value="{customer_name}" readonly="readonly"
                                                       onclick="this.select()">
                                                - <?php _e('customer name', 'bookme'); ?>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="panel-footer padding-0">
                                    <button id="booking_google_calendar"
                                            class="btn btn-success "><?php _e('Save', 'bookme'); ?>
                                    </button>
                                </div>
                            </div>

                            <div class="tab-pane" id="tab5">
                                <div class="panel-group" id="accordion">
                                    <div class="form-group">
                                        <label for="bookme_enable_cart"><h4><?php _e('Cart', 'bookme'); ?></h4></label>
                                        <p><?php _e('Give the facility to your client to book several appointments at once.<br><strong>Note:</strong> If WooCommerce is enable then cart will not work.', 'bookme'); ?></p>
                                        <select class="form-control" name="bookme_enable_cart" id="bookme_enable_cart">
                                            <?php
                                            $bookme_enable_cart = bookme_get_settings('bookme_enable_cart', 0);
                                            ?>
                                            <option value="0" <?php selected($bookme_enable_cart, 0) ?>>
                                                <?php _e('Disable', 'bookme'); ?>
                                            </option>
                                            <option value="1" <?php selected($bookme_enable_cart, 1) ?>>
                                                <?php _e('Enable', 'bookme'); ?>
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="panel-footer padding-0">
                                    <button id="booking_cart_save"
                                            class="btn btn-success "><?php _e('Save', 'bookme'); ?>
                                    </button>
                                </div>
                            </div>

                            <div class="tab-pane" id="tab6">
                                <?php
                                $currency = bookme_get_settings('pmt_currency', 'USD');
                                $service = bookme_get_settings('pmt_local', 'enabled');
                                $paypal = bookme_get_settings('pmt_paypal', 'disabled');
                                ?>

                                <div class="panel-group" id="accordion">
                                    <div class="panel panel-default">
                                        <div class="panel-heading box-heading">
                                            <label for="bookme_pmt_currency"><?php _e('Currency', 'bookme'); ?></label>
                                        </div>
                                        <div class="panel-body padding-0 padding-bottom-20">
                                            <div class="form-group">
                                                <select id="bookme_pmt_currency" class="form-control"
                                                        name="bookme_pmt_currency">
                                                    <option value="AED" <?php selected($currency, "AED") ?> >AED
                                                    </option>
                                                    <option value="ARS" <?php selected($currency, "ARS") ?>>ARS
                                                    </option>
                                                    <option value="AUD" <?php selected($currency, "AUD") ?>>AUD
                                                    </option>
                                                    <option value="BGN" <?php selected($currency, "BGN") ?>>BGN
                                                    </option>
                                                    <option value="BHD" <?php selected($currency, "BHD") ?>>BHD
                                                    </option>
                                                    <option value="BRL" <?php selected($currency, "BRL") ?>>BRL
                                                    </option>
                                                    <option value="CAD" <?php selected($currency, "CAD") ?>>CAD
                                                    </option>
                                                    <option value="CHF" <?php selected($currency, "CHF") ?>>CHF
                                                    </option>
                                                    <option value="CLP" <?php selected($currency, "CLP") ?>>CLP
                                                    </option>
                                                    <option value="COP" <?php selected($currency, "COP") ?>>COP
                                                    </option>
                                                    <option value="CRC" <?php selected($currency, "CRC") ?>>CRC
                                                    </option>
                                                    <option value="CZK" <?php selected($currency, "CZK") ?>>CZK
                                                    </option>
                                                    <option value="DKK" <?php selected($currency, "DKK") ?>>DKK
                                                    </option>
                                                    <option value="DOP" <?php selected($currency, "DOP") ?>>DOP
                                                    </option>
                                                    <option value="DZD" <?php selected($currency, "DZD") ?>>DZD
                                                    </option>
                                                    <option value="EGP" <?php selected($currency, "EGP") ?>>EGP
                                                    </option>
                                                    <option value="EUR" <?php selected($currency, "EUR") ?>>EUR
                                                    </option>
                                                    <option value="GBP" <?php selected($currency, "GBP") ?>>GBP
                                                    </option>
                                                    <option value="GEL" <?php selected($currency, "GEL") ?>>GEL
                                                    </option>
                                                    <option value="GTQ" <?php selected($currency, "GTQ") ?>>GTQ
                                                    </option>
                                                    <option value="HKD" <?php selected($currency, "HKD") ?>>HKD
                                                    </option>
                                                    <option value="HRK" <?php selected($currency, "HRK") ?>>HRK
                                                    </option>
                                                    <option value="HUF" <?php selected($currency, "HUF") ?>>HUF
                                                    </option>
                                                    <option value="IDR" <?php selected($currency, "IDR") ?>>IDR
                                                    </option>
                                                    <option value="ILS" <?php selected($currency, "ILS") ?>>ILS
                                                    </option>
                                                    <option value="INR" <?php selected($currency, "INR") ?>>INR
                                                    </option>
                                                    <option value="ISK" <?php selected($currency, "ISK") ?>>ISK
                                                    </option>
                                                    <option value="JPY" <?php selected($currency, "JPY") ?>>JPY
                                                    </option>
                                                    <option value="KES" <?php selected($currency, "KES") ?>>KES
                                                    </option>
                                                    <option value="KRW" <?php selected($currency, "KRW") ?>>KRW
                                                    </option>
                                                    <option value="KZT" <?php selected($currency, "KZT") ?>>KZT
                                                    </option>
                                                    <option value="KWD" <?php selected($currency, "KWD") ?>>KWD
                                                    </option>
                                                    <option value="LAK" <?php selected($currency, "LAK") ?>>LAK
                                                    </option>
                                                    <option value="MUR" <?php selected($currency, "MUR") ?>>MUR
                                                    </option>
                                                    <option value="MXN" <?php selected($currency, "MXN") ?>>MXN
                                                    </option>
                                                    <option value="MYR" <?php selected($currency, "MYR") ?>>MYR
                                                    </option>
                                                    <option value="NAD" <?php selected($currency, "NAD") ?> >NAD
                                                    </option>
                                                    <option value="NGN" <?php selected($currency, "NGN") ?>>NGN
                                                    </option>
                                                    <option value="NOK" <?php selected($currency, "NOK") ?>>NOK
                                                    </option>
                                                    <option value="NZD" <?php selected($currency, "NZD") ?>>NZD
                                                    </option>
                                                    <option value="OMR" <?php selected($currency, "OMR") ?>>OMR
                                                    </option>
                                                    <option value="PEN" <?php selected($currency, "PEN") ?>>PEN
                                                    </option>
                                                    <option value="PHP" <?php selected($currency, "PHP") ?>>PHP
                                                    </option>
                                                    <option value="PKR" <?php selected($currency, "PKR") ?>>PKR
                                                    </option>
                                                    <option value="PLN" <?php selected($currency, "PLN") ?>>PLN
                                                    </option>
                                                    <option value="QAR" <?php selected($currency, "QAR") ?>>QAR
                                                    </option>
                                                    <option value="RMB" <?php selected($currency, "RMB") ?>>RMB
                                                    </option>
                                                    <option value="RON" <?php selected($currency, "RON") ?>>RON
                                                    </option>
                                                    <option value="RUB" <?php selected($currency, "RUB") ?>>RUB
                                                    </option>
                                                    <option value="SAR" <?php selected($currency, "SAR") ?>>SAR
                                                    </option>
                                                    <option value="SEK" <?php selected($currency, "SEK") ?>>SEK
                                                    </option>
                                                    <option value="SGD" <?php selected($currency, "SGD") ?>>SGD
                                                    </option>
                                                    <option value="THB" <?php selected($currency, "THB") ?>>THB
                                                    </option>
                                                    <option value="TRY" <?php selected($currency, "TRY") ?>>TRY
                                                    </option>
                                                    <option value="TWD" <?php selected($currency, "TWD") ?>>TWD
                                                    </option>
                                                    <option value="UAH" <?php selected($currency, "UAH") ?>>UAH
                                                    </option>
                                                    <option value="UGX" <?php selected($currency, "UGX") ?>>UGX
                                                    </option>
                                                    <option value="USD" <?php selected($currency, "USD") ?> >USD
                                                    </option>
                                                    <option value="VND" <?php selected($currency, "VND") ?>>VND
                                                    </option>
                                                    <option value="XAF" <?php selected($currency, "XAF") ?>>XAF
                                                    </option>
                                                    <option value="XOF" <?php selected($currency, "XOF") ?>>XOF
                                                    </option>
                                                    <option value="ZAR" <?php selected($currency, "ZAR") ?> >ZAR
                                                    </option>
                                                    <option value="ZMW" <?php selected($currency, "ZMW") ?>>ZMW
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="panel panel-default">
                                        <div class="panel-heading box-heading">
                                            <label for="bookme_pmt_local"><?php _e('Service paid locally', 'bookme'); ?></label>
                                        </div>
                                        <div class="panel-body padding-0 padding-bottom-20">
                                            <div class="form-group"><select class="form-control" name="bookme_pmt_local"
                                                                            id="bookme_pmt_local">
                                                    <option value="disabled" <?php selected($service, 'disabled') ?>><?php _e('Disabled', 'bookme'); ?>
                                                    </option>
                                                    <option value="enabled" <?php selected($service, 'enabled') ?>><?php _e('Enabled', 'bookme'); ?>
                                                    </option>
                                                </select></div>
                                        </div>
                                    </div>
                                    <div class="panel panel-default">
                                        <div class="panel-heading box-heading">
                                            <label for="bookme_pmt_paypal"><?php _e('PayPal', 'bookme'); ?></label>
                                            <img style="margin-left: 10px; float: right"
                                                 src="<?php echo plugins_url('/assets/images/paypal.png', __FILE__); ?>">
                                        </div>
                                        <div class="panel-body padding-0">
                                            <div class="form-group">
                                                <div class="form-group">
                                                    <select class="form-control" name="bookme_pmt_paypal"
                                                            id="bookme_pmt_paypal">
                                                        <option value="disabled" <?php selected($paypal, 'disabled') ?>><?php _e('Disabled', 'bookme'); ?></option>
                                                        <option value="ec" <?php selected($paypal, 'ec') ?>><?php _e('PayPal Express Checkout', 'bookme'); ?></option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="bookme-paypal">
                                                <div
                                                        class="bookme-paypal-<?php echo bookme_get_settings('pmt_paypal'); ?>">
                                                    <div class="form-group">
                                                        <label for="bookme_pmt_paypal_api_username"><?php _e('API Username', 'bookme'); ?></label>
                                                        <input id="bookme_pmt_paypal_api_username" class="form-control"
                                                               type="text" name="bookme_pmt_paypal_api_username"
                                                               value="<?php echo bookme_get_settings('pmt_paypal_api_username'); ?>">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="bookme_pmt_paypal_api_password"><?php _e('API Password', 'bookme'); ?></label>
                                                        <input id="bookme_pmt_paypal_api_password" class="form-control"
                                                               type="text" name="bookme_pmt_paypal_api_password"
                                                               value="<?php echo bookme_get_settings('pmt_paypal_api_password'); ?>">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="bookme_pmt_paypal_api_signature"><?php _e('API
                                                            Signature', 'bookme'); ?></label>
                                                        <input id="bookme_pmt_paypal_api_signature" class="form-control"
                                                               type="text" name="bookme_pmt_paypal_api_signature"
                                                               value="<?php echo bookme_get_settings('pmt_paypal_api_signature'); ?>">
                                                    </div>
                                                </div>
                                                <?php
                                                $mode = bookme_get_settings('pmt_paypal_sandbox');
                                                ?>
                                                <div class="form-group">
                                                    <label for="bookme_pmt_paypal_sandbox"><?php _e('Sandbox Mode', 'bookme'); ?></label>
                                                    <select class="form-control" name="bookme_pmt_paypal_sandbox"
                                                            id="bookme_pmt_paypal_sandbox">
                                                        <option value="yes" <?php selected($mode, 'yes') ?>><?php _e('Yes', 'bookme'); ?>
                                                        </option>
                                                        <option value="no" <?php selected($mode, 'no') ?>><?php _e('No', 'bookme'); ?>
                                                        </option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="panel panel-default">
                                        <div class="panel-heading box-heading">
                                            <label for="bookme_pmt_stripe"><?php _e('Stripe', 'bookme'); ?></label>
                                            <img style="margin-left: 10px; float: right"
                                                 src="<?php echo plugins_url('/assets/images/stripe.png', __FILE__); ?>">
                                        </div>
                                        <?php
                                        $stripe = bookme_get_settings('pmt_stripe', 'disabled');
                                        ?>
                                        <div class="panel-body padding-0">
                                            <div class="form-group">
                                                <select class="form-control" name="bookme_pmt_stripe"
                                                        id="bookme_pmt_stripe">
                                                    <option value="disabled" <?php selected($stripe, 'disabled') ?>><?php _e('Disabled', 'bookme'); ?>
                                                    </option>
                                                    <option value="enabled" <?php selected($stripe, 'enabled') ?>><?php _e('Enabled', 'bookme'); ?>
                                                    </option>
                                                </select>
                                            </div>
                                            <div class="form-group bookme-stripe">
                                                <label for="bookme_pmt_stripe_secret"><?php _e('Secret key', 'bookme'); ?></label>
                                                <input id="bookme_pmt_stripe_secret" class="form-control"
                                                       type="text" name="bookme_pmt_stripe_secret"
                                                       value="<?php echo bookme_get_settings('pmt_stripe_secret_key'); ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="panel-footer padding-0 padding-top-20">
                                        <button class="btn btn-success"
                                                id="booking_payments"><?php _e('Save', 'bookme'); ?></button>
                                    </div>
                                </div>
                            </div>

                            <div class="tab-pane" id="tab7">
                                <div class="panel-group" id="accordion">
                                    <form method="post" action=""
                                          onsubmit="jQuery('[name=verfy_purchase]').addClass('bookme-progress');">
                                        <div class="form-group">
                                            <?php
                                            if ($already_file = 'a4l6ir522d.php') {
                                                if (file_exists(plugin_dir_path(__FILE__) . '/' . $already_file)) {
                                                    ?>
                                                    <div class="alert alert-success fade in">
                                                        <a href="#" class="close" data-dismiss="alert"
                                                           aria-label="close">&times;</a>
                                                        <?php _e('Your purchase code is already verified.', 'bookme'); ?>
                                                    </div>
                                                    <?php
                                                }
                                            }
                                            ?>
                                            <label for="bookme-purchase-code"><?php _e('Purchase Code', 'bookme'); ?></label>
                                            <input type="text" class="form-control" placeholder="Purchase Code"
                                                   name="purchase-code" id="bookme-purchase-code" required>
                                            <a href="https://codecanyon.net/item/bookme-wordpress-booking-plugin/20926116"
                                               target="_blank"><?php _e('Don\'t have a purchase code, Buy now.', 'bookme'); ?></a>
                                        </div>
                                        <div class="panel-footer padding-0">
                                            <button class="btn btn-success" type="submit"
                                                    name="verfy_purchase"><?php _e('Verify', 'bookme'); ?></button>
                                        </div>
                                    </form>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>