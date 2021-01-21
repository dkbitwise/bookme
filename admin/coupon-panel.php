<?php
add_action('wp_ajax_nopriv_coupon_panel', 'coupon_panel');
add_action('wp_ajax_coupon_panel', 'coupon_panel');
function coupon_panel()
{
    global $help;
    global $wpdb;
    $table_book_service = $wpdb->prefix . 'bookme_service';
    $table_book_category = $wpdb->prefix . 'bookme_category';
    $table_coupon = $wpdb->prefix . 'bookme_coupons';

    $resultS = $wpdb->get_results("SELECT c.id cat_id, c.name cat_name, group_concat(s.id) service_id, group_concat(s.name) service_name, group_concat(s.ser_icon) service_icon  FROM $table_book_category c LEFT JOIN  $table_book_service s ON s.catId = c.id GROUP BY c.id ORDER BY c.id ");
    $code = "";
    $dic = "";
    $ser = array();
    $dud = "";
    $limit = "";
    if(isset($_GET['coupon_id'])){
        $query = $wpdb->get_results("SELECT * FROM $table_coupon WHERE id =".$_GET['coupon_id']);
        $code = $query[0]->coupon_code;
        $dic = $query[0]->discount;
        $ser = explode(',',$query[0]->ser_id);
        $dud = $query[0]->deduction;
        $limit = $query[0]->usage_limit;
    }
        ?>

        <header class="slidePanel-header overlay">
            <div class="overlay-panel overlay-background vertical-align">
                <div class="service-heading">
                    <h2><?php echo isset($_GET['coupon_id'])?__('Edit coupen','bookme'): __('Create coupen','bookme'); ?></h2>
                </div>
                <div class="slidePanel-actions">
                    <div class="btn-group-flat">
                        <button type="button"
                                class="btn btn-floating btn-success btn-sm waves-effect waves-float waves-light margin-right-10"
                                aria-hidden="true" id="save_coupon"><i class="icon md-check" aria-hidden="true"></i>
                        </button>
                        <?php if (isset($_GET['coupon_id'])) { ?>
                            <button type="button" class="btn btn-pure btn-inverse icon md-delete font-size-20"
                                    aria-hidden="true" id="del_coupon"></button>
                        <?php } ?>
                        <button type="button"
                                class="btn btn-pure btn-inverse slidePanel-close icon md-close font-size-20"
                                aria-hidden="true"></button>
                    </div>
                </div>
            </div>
        </header>
        <div class="slidePanel-inner">
            <div class="panel-body">
                <form id="coupon_create_form">
                    <?php if (isset($_GET['coupon_id'])) { ?>
                        <input type="hidden" value="<?php echo $_GET['coupon_id']; ?>" name="coupon_id" id="coupon_id">
                    <?php } ?>
                <div class="form-group">
                    <label for="coupan_code"><?php _e('Code','bookme'); ?><span class="dashicons dashicons-editor-help tooltipster" title="<?php echo $help['COUPON_CODE'] ?>"></span></label>
                    <input class="form-control" type="text"
                           name="coupan_code" id="coupan_code" value="<?php echo !empty($code) ? $code : ''; ?>">
                </div>
                <div class="form-group">
                    <div class="row">
                        <div class="col-md-6">
                            <label for="coupan_discount"><?php _e('Discount (%)','bookme'); ?></label>
                            <input class="form-control" min="0" name="coupan_discount"
                                   id="coupan_discount" value="<?php echo !empty($dic) ? $dic : '0'; ?>" type="number">

                        </div>
                        <div class="col-md-6">
                            <label for="coupan_deuction"><?php _e('Deduction','bookme'); ?><span class="dashicons dashicons-editor-help tooltipster" title="<?php echo $help['COUPON_DEDUCTION'] ?>"></span></label>
                            <input class="form-control" name="coupan_deduction"
                                   id="coupan_deuction" value="<?php echo !empty($dud) ? $dud : '0'; ?>" type="text">
                        </div>

                    </div>
                </div>

                <div class="form-group">
                    <label for="coupan_limit"><?php _e('Usage limit','bookme'); ?><span class="dashicons dashicons-editor-help tooltipster" title="<?php echo $help['COUPON_USE_LIMIT'] ?>"></span></label>
                    <input class="form-control" min="0" name="coupan_limit"
                           id="coupan_limit" value="<?php echo !empty($limit) ? $limit : '0'; ?>" type="number">
                </div>


                <div class="form-group">

                    <label for="service_select"><?php _e('Services','bookme'); ?><span class="dashicons dashicons-editor-help tooltipster" title="<?php echo $help['COUPON_SERVICE'] ?>"></span></label><br>
                    <select multiple="" data-live-search="true" data-plugin="selectpicker" id="service_select" name="services[]">
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
                                                value="<?php echo $ser_id[$i]; ?>" <?php echo in_array($ser_id[$i], $ser) ? 'selected' : ''; ?>><?php echo $services[$i]; ?></option>
                                    <?php
                                    }
                                    ?>
                                </optgroup>
                            <?php }
                        } ?>
                    </select>
                </div>
                </form>
            </div>
        </div>
    <script>
    (function($){
        $('.tooltipster').tooltipster({
            theme: 'tooltipster-borderless',
            plugins: ['follower'],
            maxWidth: 300,
            delay: 100
        });
        $('[data-plugin="selectpicker"]').selectpicker({
            style: "btn-select", iconBase: "icon", tickIcon: "md-check"
        });
        })(jQuery);
    </script>
    <?php
    wp_die();
}
?>