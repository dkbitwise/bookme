<?php
add_action('wp_ajax_nopriv_customer_panel', 'customer_panel');
add_action('wp_ajax_customer_panel', 'customer_panel');
function customer_panel()
{
    global $wpdb;
    $table_customers = $wpdb->prefix . 'bookme_customers';

    $name = '';
    $phone = '';
    $email = '';
    $note = '';
    if (isset($_GET['customer_id'])) {
        $resultCust = $wpdb->get_results("SELECT * FROM $table_customers WHERE id=" . $_GET['customer_id']);
        $name = $resultCust[0]->name;
        $phone = $resultCust[0]->phone;
        $email = $resultCust[0]->email;
        $note = $resultCust[0]->notes;
    }
    ?>

    <header class="slidePanel-header overlay">
        <div class="overlay-panel overlay-background vertical-align">
            <div class="service-heading">
                <h2><?php echo (isset($_GET['customer_id'])) ? __('Edit customer', 'bookme') : __('Add new Customer', 'bookme'); ?></h2>
            </div>
            <div class="slidePanel-actions">
                <div class="btn-group-flat">
                    <button type="button"
                            class="btn btn-floating btn-success btn-sm waves-effect waves-float waves-light margin-right-10"
                            id="edit_customer"><i class="icon md-check" aria-hidden="true"></i></button>
                    <?php if (isset($_GET['customer_id'])) { ?>
                        <button type="button" class="btn btn-pure btn-inverse icon md-delete font-size-20"
                                aria-hidden="true" id="del_customer"></button>
                    <?php } ?>
                    <button type="button" class="btn btn-pure btn-inverse slidePanel-close icon md-close font-size-20"
                            aria-hidden="true"></button>
                </div>
            </div>
        </div>
    </header>
    <div class="slidePanel-inner">
        <div class="panel-body">
            <form method="get" id="save_cust_details">
                <?php
                if (isset($_GET['customer_id'])) {
                    ?>
                    <input type="hidden" name="custId" id="custId" value="<?php echo $_GET['customer_id']; ?>">
                <?php } ?>
                <div class="row">
                    <div class="col-md-12 col-sm-12">
                        <div class="form-group">
                            <label for="custName"><?php _e('Name', 'bookme'); ?></label>
                            <input class="form-control " type="text" name="custName" id="custName" placeholder="<?php _e('Name', 'bookme'); ?>"
                                   value="<?php echo (!empty($name)) ? $name : ''; ?>">
                        </div>
                        <div class="form-group">
                            <label for="custPhone"><?php _e('Phone', 'bookme'); ?></label>
                            <input class="form-control " name="custPhone" id="custPhone" type="text"
                                   value="<?php echo (!empty($phone)) ? $phone : ''; ?>">
                        </div>
                        <div class="form-group">
                            <label for="custEmail"><?php _e('Email', 'bookme'); ?></label>
                            <input class="form-control " name="custEmail" id="custEmail" type="text"
                                   placeholder="<?php _e('Email', 'bookme'); ?>"
                                   value="<?php echo (!empty($email)) ? $email : ''; ?>">
                        </div>
                        <div class="form-group">
                            <label for="custNote"><?php _e('Note', 'bookme'); ?></label>
                    <textarea rows="2" class="form-control" name="custNote" id="custNote"
                              placeholder="<?php _e('Note', 'bookme'); ?>"><?php echo (!empty($note)) ? $note : ''; ?></textarea>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <script>
    (function($){
        $('#custPhone').intlTelInput({
            preferredCountries: ["us", "br", "gb", "in"],
            initialCountry: "auto",
            geoIpLookup: function(callback) {
                $.get('https://ipinfo.io', function() {}, "jsonp").always(function(resp) {
                    var countryCode = (resp && resp.country) ? resp.country : "";
                    callback(countryCode);
                });
            }
        });
        })(jQuery);
    </script>
    <?php
    wp_die();
}
?>