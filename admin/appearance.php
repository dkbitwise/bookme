<?php
global $wpdb, $help;
?>

<div class="page">
    <div>
        <?php include(plugin_dir_path(__FILE__) . '/header.php'); ?>
        <div class="panel">
            <header class="panel-heading">
                <h3 class="panel-title"><?php _e('Appearance', 'bookme'); ?></h3>
            </header>
            <div class="panel-body">

                <?php
                $table_appearance = $wpdb->prefix . 'bookme_appearance';
                ?>

                <div class="tabbable" id="myTabs">
                    <div class="col-md-4">
                        <ul class="nav nav-tabs">

                            <li class="border active">
                                <a href="#tab1" data-toggle="tab">
                                    <span><?php _e('Appearance Bullets', 'bookme'); ?><span class="dashicons dashicons-editor-help tooltipster" title="<?php echo $help['APPEARANCE_BULLETS']; ?>"></span>
                                </a>
                            </li>
                            <li class="border ">
                                <a href="#tab2" data-toggle="tab">
                                    <span><?php _e('Appearance Labels', 'bookme'); ?><span class="dashicons dashicons-editor-help tooltipster" title="<?php echo $help['APPEARANCE_LABELS']; ?>"></span>
                                </a>
                            </li>
                            <li class="border ">
                                <a href="#tab3" data-toggle="tab">
                                    <span><?php _e('Appearance Colour', 'bookme'); ?><span class="dashicons dashicons-editor-help tooltipster" title="<?php echo $help['APPEARANCE_COLOR']; ?>"></span>
                                </a>
                            </li>
                            <li class="border ">
                                <a href="#tab4" data-toggle="tab">
                                    <span><?php _e('Booking Message', 'bookme'); ?><span class="dashicons dashicons-editor-help tooltipster" title="<?php echo $help['BOOKING_MSG']; ?>"></span>
                                </a>
                            </li>


                        </ul>

                    </div>

                    <div class="col-md-8  panel-bg">


                        <div class="tab-content">
                            <div class="tab-pane active" id="tab1">
                                <div class="panel-group" id="accordion">
                                    <h4><?php _e('Appearance settings', 'bookme'); ?></h4>
                                    <hr/>
                                    <div class="row  ghgh">
                                        <div class="col-lg-12">
                                            <div class="form-group">
                                                <p class="text-muted"><?php _e("Change 'Service' Label", 'bookme'); ?></p>
                                                <input type="text" name="bullet1" class="form-control"
                                                       value="<?php echo bookme_get_table_appearance('bullet1', 'bullet'); ?>">
                                            </div>
                                        </div>
                                        <div class="col-lg-12">
                                            <div class="form-group">
                                                <p class="text-muted"><?php _e("Change 'Time' Label", 'bookme'); ?></p>
                                                <input type="text" name="bullet2" class="form-control"
                                                       value="<?php echo bookme_get_table_appearance('bullet2', 'bullet'); ?>">
                                            </div>
                                        </div>
                                        <div class="col-lg-12">
                                            <div class="form-group">
                                                <p class="text-muted"><?php _e("Change 'Cart' Label", 'bookme'); ?></p>
                                                <input type="text" name="bullet_cart" class="form-control"
                                                       value="<?php echo bookme_get_table_appearance('bullet_cart', 'bullet'); ?>">
                                            </div>
                                        </div>
                                        <div class="col-lg-12">
                                            <div class="form-group">
                                                <p class="text-muted"><?php _e("Change 'Detail' Label", 'bookme'); ?></p>
                                                <input type="text" name="bullet3" class="form-control"
                                                       value="<?php echo bookme_get_table_appearance('bullet3', 'bullet'); ?>">
                                            </div>
                                        </div>
                                        <div class="col-lg-12">
                                            <div class="form-group">
                                                <p class="text-muted"><?php _e("Change 'Payment' Label", 'bookme'); ?></p>
                                                <input type="text" name="bullet4" class="form-control"
                                                       value="<?php echo bookme_get_table_appearance('bullet4', 'bullet'); ?>">
                                            </div>
                                        </div>
                                        <div class="col-lg-12">
                                            <div class="form-group">
                                                <p class="text-muted"><?php _e("Change 'Done' Label", 'bookme'); ?></p>
                                                <input type="text" name="bullet5" class="form-control"
                                                       value="<?php echo bookme_get_table_appearance('bullet5', 'bullet'); ?>">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="panel-footer">
                                        <button class="btn btn-success" id="appearance_btn_bullet"><?php _e('Save', 'bookme'); ?></button>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane" id="tab2">
                                <div class="panel-group" id="accordion">
                                    <h4><?php _e('Appearance settings', 'bookme'); ?></h4>
                                    <hr/>
                                    <div class="row  ghgh">
                                        <div class="col-lg-12">
                                            <div class="form-group">
                                                <p class="text-muted"><?php _e("Change 'Category' Label", 'bookme'); ?></p>
                                                <input type="text" name="category" class="form-control"
                                                       value="<?php echo bookme_get_table_appearance('category', 'label'); ?>">
                                            </div>
                                        </div>
                                        <div class="col-lg-12">
                                            <div class="form-group">
                                                <p class="text-muted"><?php _e("Change 'Service' Label", 'bookme'); ?></p>
                                                <input type="text" name="service" class="form-control"
                                                       value="<?php echo bookme_get_table_appearance('service', 'label'); ?>">
                                            </div>
                                        </div>
                                        <div class="col-lg-12">
                                            <div class="form-group">
                                                <p class="text-muted"><?php _e("Change 'Employee' Label", 'bookme'); ?></p>
                                                <input type="text" name="employee" class="form-control"
                                                       value="<?php echo bookme_get_table_appearance('employee', 'label'); ?>">
                                            </div>
                                        </div>
                                        <div class="col-lg-12">
                                            <div class="form-group">
                                                <p class="text-muted"><?php _e("Change 'Number of person' Label", 'bookme'); ?></p>
                                                <input type="text" name="number_of_person" class="form-control"
                                                       value="<?php echo bookme_get_table_appearance('number_of_person', 'label'); ?>">
                                            </div>
                                        </div>
                                        <div class="col-lg-12">
                                            <div class="form-group">
                                                <p class="text-muted"><?php _e("Change 'Availability' Label", 'bookme'); ?></p>
                                                <input type="text" name="availability" class="form-control"
                                                       value="<?php echo bookme_get_table_appearance('availability', 'label'); ?>">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="panel-footer">
                                        <button class="btn btn-success" id="appearance_btn"><?php _e("Save", 'bookme'); ?></button>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane" id="tab3">
                                <div class="panel-group" id="accordion">
                                    <h4><?php _e("Appearance settings", 'bookme'); ?></h4>
                                    <hr/>
                                    <div class="row  ghgh">
                                        <div class="col-lg-12">
                                            <div class="form-group">
                                                <p class="text-muted"><?php _e("BackGround Colour", 'bookme'); ?></p>
                                                <input type="color" name="appearance_color" class="form-control"
                                                       value="<?php echo bookme_get_table_appearance('booking_color', 'color','#1abc9c'); ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row  ghgh">
                                        <div class="col-lg-12">
                                            <div class="form-group">
                                                <p class="text-muted"><?php _e("Text Colour", 'bookme'); ?></p>
                                                <input type="color" name="text_color" class="form-control"
                                                       value="<?php echo bookme_get_table_appearance('booking_colortxt', 'color','#ffffff'); ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="panel-footer">
                                        <button class="btn btn-success" id="appearance_color_btn"><?php _e("Save", 'bookme'); ?></button>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane" id="tab4">
                                <div class="panel-group" id="accordion">
                                    <h4><?php _e("Appearance settings", 'bookme'); ?></h4>
                                    <hr/>
                                    <div class="row  ghgh">
                                        <div class="col-lg-12">
                                            <div class="form-group">
                                                <p class="text-muted"><?php _e("Booking Message", 'bookme'); ?></p>
                                                <input type="text" name="booking_msg" class="form-control"
                                                       value="<?php echo bookme_get_table_appearance('booking_message', 'message'); ?>">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="panel-footer">
                                        <button class="btn btn-success" id="message_btn"><?php _e("Save", 'bookme'); ?></button>
                                    </div>
                                </div>
                            </div>


                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>