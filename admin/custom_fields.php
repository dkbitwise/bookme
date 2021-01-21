<div class="page">
    <div>
        <?php include(plugin_dir_path(__FILE__) . '/header.php'); ?>
        <div class="panel">
            <header class="panel-heading">
                <h3 class="panel-title"><?php _e('Custom Fields', 'bookme'); ?></h3>
            </header>
            <div class="panel-body custom-field">
        <div class="bookme-panel-bg">
            <div class="row">
                <div class="col-md-3 ">
                    <div class="btn-group-vertical" role="group" aria-label="Button group with nested dropdown">
                        <button class="btn btn-default add_text_field_button "><i class="fa fa-plus "></i> <?php _e('Text Field', 'bookme'); ?>
                        </button>
                        <button class="btn btn-default bookme_button_margin_top add_text_area_button"><i
                                class="fa fa-plus "></i> <?php _e('Text Area', 'bookme'); ?>
                        </button>
                        <button class="btn btn-default bookme_button_margin_top add_text_content_button"><i
                                class="fa fa-plus "></i> <?php _e('Text Content', 'bookme'); ?>
                        </button>
                        <button
                                class="btn btn-default bookme_button_margin_top add_checkbox_button"><i
                                class="fa fa-plus "></i> <?php _e('Checkbox Group', 'bookme'); ?>
                        </button>
                        <button class="btn btn-default bookme_button_margin_top add_radio_button">
                            <i class="fa fa-plus "></i> <?php _e('Radio Button Group', 'bookme'); ?>
                        </button>
                        <button class="btn btn-default bookme_button_margin_top add_dropdown_button"><i
                                class="fa fa-plus "></i> <?php _e('Drop Down', 'bookme'); ?>
                        </button>
                    </div>
                </div>
                <div class="col-md-9">
                    <ul class="input_fields_wrap">
                        <?php
                        global $wpdb;
                        $table_custom_fields = $wpdb->prefix . 'bookme_custom_field';
                        $resultField = $wpdb->get_results("SELECT * FROM $table_custom_fields ORDER BY position");
                        $ck = $rd = $dd = $tf = $ta = $tc = 0;
                        foreach ($resultField as $rowdata) {
                            if ($rowdata->field_type == 'textField') {
                                ?>
                                <li class="input_fields" id="textField">
                                    <hr>
                                    <div class="input_fields_header"><span class="fa fa-bars parent_drag"></span><span
                                            class="input_heading"><?php _e('Text Field', 'bookme'); ?></span>&nbsp;<a href="#"
                                                                                            class="remove_field"><i
                                                class="fa fa-close "></i></a></div>
                                    <div class="bookme_input_group">
                                        <label class="required_field">
                                            <input
                                                type="checkbox" <?php if (!empty($rowdata->required)) {
                                                if ($rowdata->required == 1) {
                                                    echo 'checked';
                                                }
                                            } ?> id="required-1">
                                            <span> <?php _e('Required Field', 'bookme'); ?></span>
                                        </label>
                                        <input
                                            class="ab-label form-control inputlblOne" data-xt="<?php echo $tf; ?>"
                                            type="text" value="<?php echo $rowdata->field_name ?>"
                                            placeholder="<?php _e('Enter a label', 'bookme'); ?>">
                                    </div>
                                </li>
                                <?php
                                $tf++;
                            }
                            if ($rowdata->field_type == 'textArea') {
                                ?>
                                <li class="input_fields" id="textArea">
                                    <hr>
                                    <div class="input_fields_header"><span class="fa fa-bars parent_drag"></span><span
                                            class="input_heading"><?php _e('Text Area', 'bookme'); ?></span><a href="#"
                                                                                           class="remove_field"><i
                                                class="fa fa-close "></i></a></div>
                                    <div class="bookme_input_group">
                                        <label class="required_field">
                                            <input
                                                class="ab-required"
                                                type="checkbox"  <?php if (!empty($rowdata->required)) {
                                                if ($rowdata->required == 1) {
                                                    echo 'checked';
                                                }
                                            } ?> id="required-1"><span> <?php _e('Required Field', 'bookme'); ?></span>
                                        </label>
                                        <input
                                            class="ab-label form-control inputlblOne" data-xta="<?php echo $ta; ?>"
                                            type="text" value="<?php echo $rowdata->field_name ?>"
                                            placeholder="<?php _e('Enter a label', 'bookme'); ?>">
                                    </div>
                                </li>
                                <?php
                                $ta++;
                            }
                            if ($rowdata->field_type == 'textContent') {
                                ?>
                                <li class="input_fields" id="textContent">
                                    <hr>
                                    <div class="input_fields_header"><span class="fa fa-bars parent_drag"></span><span
                                            class="input_heading"><?php _e('Text Content', 'bookme'); ?></span>&nbsp;<a href="#"
                                                                                              class="remove_field"><i
                                                class="fa fa-close "></i></a></div>
                                    <div class="bookme_input_group">
                                        <label class="required_field">
                                            <input
                                                class="ab-required"
                                                type="checkbox" <?php if (!empty($rowdata->required)) {
                                                if ($rowdata->required == 1) {
                                                    echo 'checked';
                                                }
                                            } ?> id="required-1">
                                            <span> <?php _e('Required Field', 'bookme'); ?></span>
                                        </label>
                                        <input
                                            class="ab-label form-control inputlblOne" data-xtc="<?php echo $tc; ?>"
                                            value="<?php echo $rowdata->field_name ?>" type="text"
                                            placeholder="<?php _e('Enter a label', 'bookme'); ?>">
                                    </div>
                                </li>
                                <?php
                                $tc++;
                            }
                            if ($rowdata->field_type == 'checkboxGroup') {
                                ?>
                                <li class="input_fields" id="checkboxGroup">
                                    <hr>
                                    <div class="input_fields_header"><span class="fa fa-bars parent_drag"></span><span
                                            class="input_heading"><?php _e('Checkbox Group', 'bookme'); ?></span>&nbsp;<a href="#"
                                                                                                class="remove_field"><i
                                                class="fa fa-close "></i></a></div>
                                    <div class="bookme_input_group">
                                        <label class="required_field">
                                            <input
                                                class="ab-required"
                                                type="checkbox" <?php if (!empty($rowdata->required)) {
                                                if ($rowdata->required == 1) {
                                                    echo 'checked';
                                                }
                                            } ?> id="required-1">
                                            <span> <?php _e('Required Field', 'bookme'); ?></span>
                                        </label>
                                        <input
                                            class="ab-label form-control inputlblOne"
                                            value="<?php echo $rowdata->field_name ?>" type="text"
                                            placeholder="<?php _e('Enter a label', 'bookme'); ?>">
                                    </div>
                                    <ul class="input_fields_wrap_items input_fields_wrap_itemsa<?php echo $ck; ?>">
                                        <?php
                                        $resultOption = $wpdb->get_results("SELECT * FROM $table_custom_fields where associate_with='" . $rowdata->id . "'");
                                        foreach ($resultOption as $poti) {
                                            ?>
                                            <li class="input_fields_items ">
                                                <div class=" input_fields_header">
                                                    <span class="fa fa-bars child_drag"></span>
                                                    <a href="#" class="bookme_flexcell remove_fields"><i
                                                            class="fa fa-close "></i></a>
                                                    <input
                                                        class="ab-label form-control dropOption"
                                                        value="<?php echo $poti->field_name ?>" type="text"
                                                        placeholder="<?php _e('Enter a label', 'bookme'); ?>">

                                                </div>
                                            </li>
                                        <?php
                                        }
                                        ?>

                                    </ul>
                                    <button class="add_checkbox_option_button btn btn-default add_sub_button "
                                            data-a='<?php echo $ck; ?>'><i class="fa fa-plus "></i> <?php _e('Option', 'bookme'); ?>
                                    </button>
                                </li>
                                <?php $ck++;
                            }
                            if ($rowdata->field_type == 'radioGroup') {
                                ?>
                                <li class="input_fields" id="radioGroup">
                                    <hr>
                                    <div class="input_fields_header"><span class="fa fa-bars parent_drag"></span><span
                                            class="input_heading"><?php _e('Radio Button Group', 'bookme'); ?></span>&nbsp;<a href="#"
                                                                                                    class="remove_field"><i
                                                class="fa fa-close "></i></a></div>
                                    <div class="bookme_input_group">
                                        <label class="required_field">
                                            <input
                                                class="ab-required"
                                                type="checkbox" <?php if (!empty($rowdata->required)) {
                                                if ($rowdata->required == 1) {
                                                    echo 'checked';
                                                }
                                            } ?> id="required-1">
                                            <span> <?php _e('Required Field', 'bookme'); ?></span>
                                        </label>
                                        <input
                                            class="ab-label form-control inputlblOne"
                                            value="<?php echo $rowdata->field_name ?>" type="text"
                                            placeholder="<?php _e('Enter a label', 'bookme'); ?>">

                                    </div>
                                    <ul class="input_fields_wrap_items input_fields_wrap_itemsb<?php echo $rd; ?>">

                                        <?php
                                        $resultOption = $wpdb->get_results("SELECT * FROM $table_custom_fields where associate_with='" . $rowdata->id . "'");
                                        foreach ($resultOption as $poti) {
                                            ?>
                                            <li class="input_fields_items ">

                                                <div class=" input_fields_header">
                                                    <span class="fa fa-bars child_drag"></span>
                                                    <a href="#"
                                                       class="bookme_flexcell remove_fields"><i
                                                            class="fa fa-close "></i></a>
                                                    <input
                                                        class="ab-label form-control dropOption"
                                                        value="<?php echo $poti->field_name ?>" type="text"
                                                        placeholder="<?php _e('Enter a label', 'bookme'); ?>">

                                                </div>
                                            </li>
                                        <?php
                                        }
                                        ?>

                                    </ul>
                                    <button class="add_radio_option_button btn btn-default add_sub_button "
                                            data-b='<?php echo $rd; ?>'><i class="fa fa-plus "></i> <?php _e('Option', 'bookme'); ?>
                                    </button>
                                </li>
                                <?php $rd++;
                            }
                            if ($rowdata->field_type == 'dropDown') {

                                ?>
                                <li class="input_fields" id="dropDown">
                                    <hr>
                                    <div class="input_fields_header"><span class="fa fa-bars parent_drag"></span><span
                                            class="input_heading"><?php _e('Drop Down', 'bookme'); ?></span>&nbsp;<a href="#"
                                                                                           class="remove_field"><i
                                                class="fa fa-close "></i></a></div>
                                    <div class="bookme_input_group">
                                        <label class="required_field">
                                            <input
                                                class="ab-required"
                                                type="checkbox" <?php if (!empty($rowdata->required)) {
                                                if ($rowdata->required == 1) {
                                                    echo 'checked';
                                                }
                                            } ?> id="required-1">
                                            <span> <?php _e('Required Field', 'bookme'); ?></span>
                                        </label>
                                        <input
                                            class="ab-label form-control inputlblOne"
                                            value="<?php echo $rowdata->field_name ?>" type="text"
                                            placeholder="<?php _e('Enter a label', 'bookme'); ?>">

                                    </div>
                                    <ul class="input_fields_wrap_items input_fields_wrap_itemsc<?php echo $dd; ?>">
                                        <?php
                                        $resultOption = $wpdb->get_results("SELECT * FROM $table_custom_fields where associate_with='" . $rowdata->id . "'");
                                        foreach ($resultOption as $poti) {
                                            ?>
                                            <li class="input_fields_items ">

                                                <div class=" input_fields_header">
                                                    <span class="fa fa-bars child_drag"></span>
                                                    <a href="#"
                                                       class="bookme_flexcell remove_fields"><i
                                                            class="fa fa-close "></i></a>
                                                    <input
                                                        class="ab-label form-control dropOption"
                                                        value="<?php echo $poti->field_name ?>" type="text"
                                                        placeholder="<?php _e('Enter a label', 'bookme'); ?>">

                                                </div>
                                            </li>
                                        <?php
                                        }
                                        ?>

                                    </ul>
                                    <button class="add_dropdown_option_button btn btn-default add_sub_button "
                                            data-c='<?php echo $dd; ?>'><i class="fa fa-plus "></i> <?php _e('Option', 'bookme'); ?>
                                    </button>
                                </li>
                                <?php
                                $dd++;
                            }
                        }

                        ?>

                    </ul>

                </div>
            </div>
            <hr/>
            <div class="panel-footer1 text-right">
                <button id="booking_custom_save" class="btn btn-success"><?php _e('Save', 'bookme'); ?></button>
            </div>
            <p class="text-muted"><?php _e('HTML allowed in all texts and labels.', 'bookme'); ?></p>

            <div class="clearfix"></div>
        </div>
            </div>
        </div>
    </div>
</div>

    <script>
        jQuery(document).ready(function ($) {

            jQuery("ul.input_fields_wrap").sortable({
                group: 'no-drop',
                handle: 'span.parent_drag',
                onDragStart: function ($item, container, _super) {
                    // Duplicate items of the no drop area
                    if (!container.options.drop)
                        $item.clone().insertAfter($item);
                    _super($item, container);
                }
            });

            jQuery("ul.input_fields_wrap_items").sortable({
                group: 'no-drop',
                handle: 'span.child_drag',
                onDragStart: function ($item, container, _super) {
                    // Duplicate items of the no drop area
                    if (!container.options.drop)
                        $item.clone().insertAfter($item);
                    _super($item, container);
                }
            });

            var wrapper = jQuery(".input_fields_wrap"); //Fields wrapper
            var wrapper1 = ".input_fields_wrap_itemsa"; //Fields wrapper
            var wrapper2 = ".input_fields_wrap_itemsb"; //Fields wrapper
            var wrapper3 = ".input_fields_wrap_itemsc"; //Fields wrapper
            var wrap = ".input_fields_wrap_items"; //Fields wrapper
            var x = 0;

            jQuery(".add_text_field_button").click(function (e) { //on add input button click
                e.preventDefault();
                    jQuery(wrapper).append('<li class="input_fields" id="textField"><hr><div class="input_fields_header"><span class="fa fa-bars parent_drag"></span><span class="input_heading"><?php _e('Text Field', 'bookme'); ?></span>&nbsp;<a href="#" class="remove_field" ><i class="fa fa-close "></i></a></div><div class="bookme_input_group"><label class="required_field"><input class="ab-required" type="checkbox" id="required-1"><span> <?php _e('Required Field', 'bookme'); ?></span></label><input class="ab-label form-control inputlblOne" type="text" placeholder="<?php _e('Enter a label', 'bookme'); ?>"></div></li>'); //add input box

            });

            jQuery(".add_text_area_button").click(function (e) { //on add input button click
                e.preventDefault();
                jQuery(wrapper).append('<li class="input_fields" id="textArea"><hr><div class="input_fields_header"><span class="fa fa-bars parent_drag"></span><span class="input_heading"><?php _e('Text Area', 'bookme'); ?></span>&nbsp;<a href="#" class="remove_field" ><i class="fa fa-close "></i></a></div><div class="bookme_input_group"><label class="required_field"><input class="ab-required" type="checkbox" id="required-1"><span> <?php _e('Required Field', 'bookme'); ?></span></label><input class="ab-label form-control inputlblOne" type="text" placeholder="<?php _e('Enter a label', 'bookme'); ?>"></div></li> '); //add input box
            });

            jQuery(".add_text_content_button").click(function (e) { //on add input button click
                e.preventDefault();
                jQuery(wrapper).append('<li class="input_fields" id="textContent"><hr><div class="input_fields_header"><span class="fa fa-bars parent_drag"></span><span class="input_heading"><?php _e('Text Content', 'bookme'); ?></span>&nbsp;<a href="#" class="remove_field" ><i class="fa fa-close "></i></a></div><div class="bookme_input_group"><label class="required_field"><input class="ab-required" type="checkbox" id="required-1"><span> <?php _e('Required Field', 'bookme'); ?></span></label><input class="ab-label form-control inputlblOne" type="text" placeholder="<?php _e('Enter a label', 'bookme'); ?>"></div></li> '); //add input box
            });

            jQuery(".add_checkbox_button").click(function (e) { //on add input button click
                e.preventDefault();
                jQuery(wrapper).append('<li class="input_fields" id="checkboxGroup"><hr><div class="input_fields_header"><span class="fa fa-bars parent_drag"></span><span class="input_heading"><?php _e('Checkbox Group', 'bookme'); ?></span>&nbsp;<a href="#" class="remove_field" ><i class="fa fa-close"></i></a></div><div class="bookme_input_group"><label class="required_field"><input class="ab-required" type="checkbox" id="required-1"><span> <?php _e('Required Field', 'bookme'); ?></span></label><input class="ab-label form-control inputlblOne" type="text" placeholder="<?php _e('Enter a label', 'bookme'); ?>"></div><ul class="input_fields_wrap_items input_fields_wrap_itemsa' + x + '"></ul><button class="add_checkbox_option_button btn btn-default add_sub_button " data-a=' + x + '><i class="fa fa-plus "></i> <?php _e('Option', 'bookme'); ?></button></li> '); //add input box
                x++;
            });

            jQuery(wrapper).on('click', ".add_checkbox_option_button", function (e) { //on add input button click
                e.preventDefault();
                jQuery(wrapper1 + jQuery(this).attr('data-a')).append('<li class="input_fields_items "><div class=" input_fields_header"><span class="fa fa-bars child_drag"></span><a href="#" class="bookme_flexcell remove_fields" ><i class="fa fa-close "></i></a> <input class="ab-label form-control dropOption" type="text" value="" placeholder="<?php _e('Enter a label', 'bookme'); ?>"></div></li>'); //add input box
            });

            jQuery(".add_radio_button").click(function (e) {
                jQuery(wrapper).append('<li class="input_fields" id="radioGroup"><hr><div class="input_fields_header"><span class="fa fa-bars parent_drag"></span><span class="input_heading"><?php _e('Radio Button Group', 'bookme'); ?></span>&nbsp;<a href="#" class="remove_field" ><i class="fa fa-close "></i></a></div><div class="bookme_input_group"><label class="required_field"><input class="ab-required" type="checkbox" id="required-1"><span> <?php _e('Required Field', 'bookme'); ?></span></label><input class="ab-label form-control inputlblOne" type="text" placeholder="<?php _e('Enter a label', 'bookme'); ?>"></div><ul class="input_fields_wrap_items input_fields_wrap_itemsb' + x + '"></ul><button class="add_radio_option_button btn btn-default add_sub_button " data-b=' + x + '><i class="fa fa-plus "></i> <?php _e('Option', 'bookme'); ?></button></li> '); //add input box
                x++;
            });

            jQuery(wrapper).on('click', ".add_radio_option_button", function (e) { //on add input button click
                e.preventDefault();
                jQuery(wrapper2 + jQuery(this).attr('data-b')).append('<li class="input_fields_items "><div class=" input_fields_header"><span class="fa fa-bars child_drag"></span><a href="#" class="bookme_flexcell remove_fields" ><i class="fa fa-close "></i></a> <input class="ab-label form-control dropOption" type="text" value="" placeholder="<?php _e('Enter a label', 'bookme'); ?>"></div></li>'); //add input box
            });

            jQuery(".add_dropdown_button").click(function (e) { //on add input button click
                e.preventDefault();
                jQuery(wrapper).append('<li class="input_fields" id="dropDown"><hr><div class="input_fields_header"><span class="fa fa-bars parent_drag"></span><span class="input_heading"><?php _e('Drop Down', 'bookme'); ?></span>&nbsp;<a href="#" class="remove_field" ><i class="fa fa-close "></i></a></div><div class="bookme_input_group"><label class="required_field"><input class="ab-required" type="checkbox" id="required-1"><span> <?php _e('Required Field', 'bookme'); ?></span></label><input class="ab-label form-control inputlblOne" type="text" placeholder="<?php _e('Enter a label', 'bookme'); ?>"></div><ul class="input_fields_wrap_items input_fields_wrap_itemsc' + x + '"></ul><button class="add_dropdown_option_button btn btn-default add_sub_button " data-c=' + x + '><i class="fa fa-plus "></i> <?php _e('Option', 'bookme'); ?></button></li>'); //add input box
                x++;
            });

            jQuery(wrapper).on('click', ".add_dropdown_option_button", function (e) { //on add input button click
                e.preventDefault();
                jQuery(wrapper3 + jQuery(this).attr('data-c')).append('<li class="input_fields_items "><div class=" input_fields_header"><span class="fa fa-bars child_drag"></span><a href="#" class="bookme_flexcell remove_fields" ><i class="fa fa-close "></i></a> <input class="ab-label form-control dropOption" type="text" value="" placeholder="<?php _e('Enter a label', 'bookme'); ?>"></div></li>'); //add input box
            });

            jQuery(wrapper).on("click", ".remove_field", function (e) { //user click on remove text
                e.preventDefault();
                jQuery(this).parent().parent('.input_fields').remove();
            })

            jQuery(wrapper).on("click", wrap + " .remove_fields", function (e) { //user click on remove text
                e.preventDefault();
                jQuery(this).parent().parent('.input_fields_items').remove();
            });
        });
    </script>