<?php
global $wpdb;
?>
<div class="page" id="bookme_calender_page">
    <div>
        <?php include(plugin_dir_path(__FILE__) . '/header.php'); ?>
        <div class="panel">
            <header class="panel-heading">
                <h3 class="panel-title"><?php _e('Calendar', 'bookme'); ?></h3>
            </header>
            <div class="panel-body bookme-calender">
                <div class="page">
                    <div id='wrap' class="dasdasdasdas">
                        <div id="content">
                            <ul id="tabs" class="nav nav-tabs" data-tabs="tabs">
                                <?php
                                /* Faculty filter */
                                $user = wp_get_current_user();
                                if ( in_array( 'administrator', (array) $user->roles ) ) {
                                    ?>
                                    <li class="active">
                                        <a href="#empid_0" class="empClickevent" id="empClickevent_all"
                                           data-id="0" data-toggle="tab"><?php _e('All', 'bookme'); ?></a>
                                    </li>
                                    <?php
                                }
                                ?>

                                <?php
                                $table_all_emp = $wpdb->prefix . 'bookme_employee';
                                /* Faculty filter */
                                $filter='';
                                if ( !in_array( 'administrator', (array) $user->roles ) ) {
                                    $filter=" where email='".$user->user_email."' ";
                                }
                                $resultE = $wpdb->get_results("SELECT * FROM $table_all_emp ".$filter." ORDER BY id ASC");
                                $liActive = 0;
                                foreach ($resultE as $value) {
                                    ?>
                                    <li class="<?= $filter!='' ? 'active' : ''?>">
                                        <a href="#empid_<?php echo $value->id; ?>" class="empClickevent"
                                              data-id="<?php echo $value->id; ?>" data-toggle="tab"
                                              id="title"><?php echo $value->name; ?></a>
                                    </li>
                                    <?php
                                    $liActive = 1;
                                }
                                ?>
                            </ul>
                            <div id="my-tab-content" class="tab-content" style="position: relative">
                                <div class='bookme-booking-calendar'></div>
                                <div class="preloader" style="display: none;"><div class="cssload-speeding-wheel"></div></div>
                                <div style='clear:both'></div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- If is faculty select first tab default -->
<?php
if( $filter!='' ){
    ?>
    <script>
        jQuery(document).ready(function($) {
            $(".empClickevent").trigger('click');
        });

    </script>
    <?php
}
?>
