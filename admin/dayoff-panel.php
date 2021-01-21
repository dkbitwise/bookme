<?php
add_action('wp_ajax_nopriv_dayoff_panel', 'bookme_dayoff');
add_action('wp_ajax_dayoff_panel', 'bookme_dayoff');
function bookme_dayoff()
{

    ?>

    <header class="slidePanel-header overlay">
        <div class="overlay-panel overlay-background vertical-align">
            <div class="service-heading">
                <h2><i class="icon md-case"></i> <?php _e('Days off', 'bookme'); ?></h2>
            </div>
            <div class="slidePanel-actions">
                <div class="btn-group-flat">
                    <button type="button" class="btn btn-pure btn-inverse slidePanel-close icon md-close font-size-20"
                            aria-hidden="true"></button>
                </div>
            </div>
        </div>
    </header>
    <div class="slidePanel-inner">
        <div class="panel-body padding-horizontal-0">
            <div class="bookme-dayoff-nav">
                <div class="input-group input-group-lg">
                    <div class="input-group-btn">
                        <button class="btn btn-default" type="button"
                                id="bookme_dayoff_nav_year_back">
                            <i class="fa fa-chevron-left"></i>
                        </button>
                    </div>
                    <input class="form-control text-center" readonly="" type="text"
                           id="bookme_dayoff_nav_year"
                           value="">
                    <div class="input-group-btn">
                        <button class="btn btn-default" type="button"
                                id="bookme_dayoff_nav_year_next">
                            <i class="fa fa-chevron-right"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="bookme-cal-wrap clearfix">
            </div>
            <?php if (isset($_GET['member_id'])) { ?>
                <input type="hidden" id="emp_id" value="<?php echo $_GET['member_id']; ?>">
            <?php } ?>
        </div>
    </div>
    <script>
        <?php $dates = bookme_get_daysoff_by_staff_id($_GET['member_id']); ?>

        var days_off = JSON.parse(JSON.stringify(<?php echo json_encode($dates); ?>));

        jQuery(document).ready(function ($) {
            bookme_print_cal(new Date().getYear());

            $('.scrollable-container').on('scroll', function () {
                $(window).scroll();
                $('.bookme-cal-webUiPopover').webuiPopover('hide');
            });

            $(document).on('slidePanel::beforeHide', function () {
                $('.bookme-cal-webUiPopover').webuiPopover('hide');
            });
        });
    </script>
    <?php
    wp_die();
}

?>