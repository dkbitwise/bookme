<?php
add_action('wp_ajax_nopriv_edit_ser', 'bookme_edit_ser');
add_action('wp_ajax_edit_ser', 'bookme_edit_ser');
function bookme_edit_ser()
{
    global $help;
    global $wpdb;
    $table_book_category = $wpdb->prefix . 'bookme_category';
    $table_book_service = $wpdb->prefix . 'bookme_service';
    $table_all_employee = $wpdb->prefix . 'bookme_employee';

    $id = '';
    $title = '';
    $icon = '';
    $visibility = '';
    $price = '';
    $capacity = '';
    $duration = '';
    $pbefore = '';
    $info = '';
    $select = '[]';
    $product_id='';

    $resultE = $wpdb->get_results("SELECT *  FROM $table_all_employee WHERE visibility=1 ORDER BY id ASC");
    $all_member_count = $wpdb->num_rows;
    $all_staff_img = plugins_url('/assets/portraits/allstaff.png', __FILE__);
    $arr = '[';
    $arr .= '{id: "All", name: "'.__('All Staff','bookme').'", img: "' . $all_staff_img . '"},';
    foreach ($resultE as $member) {
        $arr .= '{id: "' . $member->id . '", name: "' . $member->name . '", img: "' . $member->img . '"},';
    }
    $arr .= ']';

    $resultc = $wpdb->get_results("SELECT * FROM $table_book_category ORDER BY id ASC");
    $option = '<option value="0">' . __('Select a category', 'bookme') . '</option>';
    if (isset($_GET['ser_id'])) {


        $ser_id = $_GET['ser_id'];
        $resultS = $wpdb->get_results("SELECT * FROM $table_book_service WHERE id='" . $ser_id . "' ORDER BY id ASC LIMIT 1");
        foreach ($resultc as $valuec) {
            if ($valuec->id == $resultS[0]->catId) {
                $selected = 'selected';
            } else {
                $selected = "";
            }
            $option .= '<option value="' . $valuec->id . '" ' . $selected . '>' . $valuec->name . '</option>';
        }
        foreach ($resultS as $values) {
            $id = $values->id;
            $title = $values->name;
            $icon = $values->ser_icon;
            $visibility = $values->visibility;
            $price = $values->price;
            $capacity = $values->capacity;
            $duration = $values->duration;
            $pbefore = $values->paddingBefore;
            $info = $values->description;
            $product_id=$values->product_id;
        }

        $to_be_exp = explode(',', $resultS[0]->staff);
        $select = '[';
        if (in_array('All', $to_be_exp)) {
            $select .= '{id: "All", name: "'.__('All Staff','bookme').'", img: "' . $all_staff_img . '"},';
        }
        foreach ($resultE as $value) {
            if (in_array($value->id, $to_be_exp)) {
                $select .= '{id: "' . $value->id . '", name: "' . $value->name . '", img: "' . $value->img . '"},';
            }
        }
        $select .= ']';
    } else {
        foreach ($resultc as $valuec) {
            $option .= '<option value="' . $valuec->id . '">' . $valuec->name . '</option>';
        }
    }
    ?>

    <header class="slidePanel-header overlay">
        <div class="overlay-panel overlay-background vertical-align">
            <div class="service-heading">
                <h2><?php echo (!empty($title)) ? $title : __('Add new Service','bookme'); ?></h2>
            </div>
            <div class="slidePanel-actions">
                <div class="btn-group-flat">
                    <button type="button"
                            class="btn btn-floating btn-success btn-sm waves-effect waves-float waves-light margin-right-10"
                            id="edit_service"><i class="icon md-check" aria-hidden="true"></i></button>
                    <?php if (isset($_GET['ser_id'])) { ?>
                        <button type="button" class="btn btn-pure btn-inverse icon md-delete font-size-20"
                                aria-hidden="true" id="del_service"></button>
                    <?php } ?>
                    <button type="button" class="btn btn-pure btn-inverse slidePanel-close icon md-close font-size-20"
                            aria-hidden="true"></button>
                </div>
            </div>
        </div>
    </header>
    <div class="slidePanel-inner">
        <div class="panel-body">
            <form method="get" id="edit_ser_form">
                <?php
                if (!empty($id)) {
                    ?>
                    <input type="hidden" name="ser_id" value="<?php echo $id; ?>" id="ser_id">
                <?php } ?>
                <div class="row">
                    <div class="col-md-12 col-sm-12">
                        <div class="form-group">
                            <label for="title"><?php _e('Title','bookme'); ?></label>

                            <div class="input-group">
                                <span class="input-group-btn">
                                    <button class="btn btn-default" data-iconset="fontawesome"
                                            data-icon="<?php echo (!empty($icon)) ? $icon : 'fa-user'; ?>"
                                            data-arrow-prev-icon-class="fa fa-chevron-left"
                                            data-arrow-next-icon-class="fa fa-chevron-right" id="iconpicker"></button>
                                </span>
                                <input name="serviceicon" value="<?php echo (!empty($icon)) ? $icon : 'fa-user'; ?>"
                                       id="serviceicon" type="hidden">
                                <input name="title" value="<?php echo (!empty($title)) ? $title : ''; ?>" id="title"
                                       class="form-control" type="text">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label for="visibility"><?php _e('Visibility','bookme'); ?><span class="dashicons dashicons-editor-help tooltipster" title="<?php echo $help['SER_VISIBILITY'] ?>"></span></label>

                            <select name="visibility" class="form-control" id="visibility">
                                <option
                                    value="1" <?php echo ($visibility == 1) ? 'selected' : ''; ?>><?php _e('Public'); ?></option>
                                <option
                                    value="0" <?php echo ($visibility == 0) ? 'selected' : ''; ?>><?php _e('Private'); ?></option>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label for="price"><?php _e('Price','bookme'); ?></label>
                            <input id="price" class="form-control" type="number" min="0" step="1"
                                   name="price" value="<?php echo (!empty($price)) ? $price : ''; ?>">
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label for="capacity"><?php _e('Capacity','bookme'); ?><span class="dashicons dashicons-editor-help tooltipster" title="<?php echo $help['CAPACITY']; ?>"></span></label>
                            <input id="capacity" class="form-control" type="number" min="1" step="1"
                                   name="capacity" value="<?php echo (!empty($capacity)) ? $capacity : ''; ?>">
                        </div>
                    </div>
                </div>

                <div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="duration">
                                    <?php _e('Duration','bookme'); ?>
                                </label>
                                <select id="duration" class="form-control" name="duration">
                                    <?php
                                    $h = 0;
                                    $m = 15;
                                    $mtext = '15 min';
                                    $htext = '';
                                    for ($i = 900; $i <= 43200; $i += 900) {
                                        $selected = ($i == $duration) ? 'selected' : '';
                                        echo '<option value="' . $i . '" ' . $selected . '>' . $htext . ' ' . $mtext . '</option>';
                                        if ($m == 45) {
                                            $m = 0;
                                            $h++;
                                        } else {
                                            $m += 15;
                                        }
                                        if ($h == 0) {
                                            $mtext = $m . ' min';
                                        }
                                        if ($m == 0) {
                                            $htext = $h . ' h';
                                            $mtext = '';
                                        } else {
                                            $mtext = $m . ' min';
                                        }
                                    }
                                    ?>
                                    <option value="86400" <?php echo ($duration == 86400) ? 'selected' : ''; ?>>
                                        <?php _e('All day','bookme'); ?>
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="padding_left">
                                    <?php _e('Padding time','bookme'); ?> <span class="dashicons dashicons-editor-help tooltipster" title="<?php echo $help['PADDING_TIME']; ?>"></span></label>

                                <div class="row">
                                    <div class="col-xs-12">
                                        <select id="padding_left" class="form-control" name="padding_left">
                                            <option value="0"  <?php echo ($pbefore == 0) ? 'selected' : ''; ?>>OFF
                                            </option>
                                            <?php
                                            $h = 0;
                                            $m = 15;
                                            $mtext = '15 min';
                                            $htext = '';
                                            for ($i = 900; $i <= 43200; $i += 900) {
                                                $selected = ($i == $pbefore) ? 'selected' : '';
                                                echo '<option value="' . $i . '" ' . $selected . '>' . $htext . ' ' . $mtext . '</option>';
                                                if ($m == 45) {
                                                    $m = 0;
                                                    $h++;
                                                } else {
                                                    $m += 15;
                                                }
                                                if ($h == 0) {
                                                    $mtext = $m . ' min';
                                                }
                                                if ($m == 0) {
                                                    $htext = $h . ' h';
                                                    $mtext = '';
                                                } else {
                                                    $mtext = $m . ' min';
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="category"><?php _e('Category','bookme'); ?></label>
                            <select id="category" class="form-control" name="category_id">
                                <?php echo $option; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label><?php _e('Staff members','bookme'); ?><span class="dashicons dashicons-editor-help tooltipster" title="<?php echo $help['SER_STAFF_MEMBER'] ?>"></span></label><br>
                            <select multiple="multiple" id="staff" data-plugin="jquery-selective" name="staff[]">
                            </select>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="info">
                        <?php _e('Info','bookme'); ?> <span class="dashicons dashicons-editor-help tooltipster" title="<?php echo $help['SER_INFO']; ?>"></span>
                    </label>
                    <textarea class="form-control" id="info" name="info" rows="3"
                              type="text"><?php echo (!empty($info)) ? $info : ''; ?></textarea>
                </div>
                <div class="form-group">
                    <label for="product">
                        <?php _e('Woocommerce Product','bookme'); ?> <span class="dashicons dashicons-editor-help tooltipster" title="Select Product from woocommerce"></span>
                    </label>
                    <?php
                    $products = wc_get_products( array( 'orderby'  => 'name','status' => 'publish', 'limit' => -1 ) );
                    ?>
                    <select class="form-control" id="product_id" name="product_id">
                        <?php
                        echo "<option value=''>Select Product</option>";
                        foreach ($products as $product){
                            $sel=($product_id==$product->get_id() ? 'selected' : '');
                            echo "<option $sel value='".$product->get_id()."'>".$product->get_name()."</option>";
                        }
                        ?>
                    </select>
                </div>
            </form>
        </div>
    </div>
    <script>
        /* selective */
        jQuery(document).ready(function ($) {
            $('.tooltipster').tooltipster({
                theme: 'tooltipster-borderless',
                plugins: ['follower'],
                maxWidth: 300,
                delay: 100
            });
            $("#iconpicker").iconpicker();
            $('#iconpicker').on('change', function (e) {
                $('#serviceicon').val(e.icon);
            });
            var selected_count = 0;
            var all_members_count = <?php echo $all_member_count; ?>+1;

            var members = <?php echo $arr; ?>
                ,
                selected =<?php echo $select; ?>;
            var a;
            var selector = 0;
            $('[data-plugin="jquery-selective"]').selective({
                namespace: "addMember",
                local: members,
                selected: selected,
                buildFromHtml: !1,
                tpl: {
                    optionValue: function (data) {
                        return data.id
                    },
                    frame: function () {
                        return '<div class="' + this.namespace + '">' + this.options.tpl.items.call(this) +
                            '<div class="' + this.namespace + '-trigger">' + this.options.tpl.triggerButton.call(this) + '<div class="' + this.namespace + '-trigger-dropdown">' + this.options.tpl.list.call(this) + "</div></div></div>"
                    },
                    triggerButton: function () {
                        return '<div class="' + this.namespace + '-trigger-button"><i class="md-plus"></i></div>'
                    },
                    list: function () {
                        return '<ul class="' + this.namespace + '-list"></ul>';
                    },
                    listItem: function (data) {
                        return '<li class="' + this.namespace + '-list-item" id="' + this.options.tpl.optionValue.call(this, data) + '"><img class="avatar" src="' + data.img + '">' + data.name + '</li>'
                    },
                    item: function (data) {
                        return '<li class="' + this.namespace + '-item css-tooltip" id="imgbox' + this.options.tpl.optionValue.call(this, data) + '" data-tooltip-title="' + data.name + '"><img class="avatar" src="' + data.img + '">' + this.options.tpl.itemRemove.call(this) + "</li>"
                    },
                    itemRemove: function () {
                        return '<span class="' + this.namespace + '-remove"><i class="md-close-circle"></i></span>'
                    },
                    option: function (data) {
                        return '<option value="' + this.options.tpl.optionValue.call(this, data) + '">' + data.name + "</option>"
                    }
                },
                onBeforeSelected: function () {
                    a = this;
                },
                onAfterItemAdd: function () {
                    if (selector == 'all') {
                        selector = 0;
                        $.each(members, function (index, item) {
                            if (!($('.addMember-list-item#' + item.id).hasClass('addMember-selected'))) {
                                a.select($('.addMember-list-item#' + item.id));
                            }
                            if (item.id != 'All') {
                                $('.addMember-item#imgbox' + item.id).remove();
                            }
                        });
                    }
                },
                onAfterSelected: function () {
                    selected_count++;
                },
                onAfterUnselected: function () {
                    selected_count--;
                }
            });

            if (all_members_count == selected_count) {
                $.each(members, function (index, item) {
                    if (item.id != 'All')
                        $('.addMember-item#imgbox' + item.id).remove();
                });
            }

            $('.addMember-list-item#All').on('click', function (e) {
                if ($(this).hasClass('addMember-selected')) {
                    e.stopPropagation();
                    $.each(members, function (index, item) {
                        a.unselect($('.addMember-list-item#' + item.id));
                        $('#staff option[value="' + item.id + '"]').remove();
                    });
                    $('.addMember-item').remove();
                } else {
                    selector = 'all';
                }
            });

            $('.addMember-list-item').on('click', function (e) {
                if ($(this).hasClass('addMember-selected')) {
                    var id = $(this).attr('id');
                    if (id != 'All') {
                        e.stopPropagation();
                        a.unselect($(this));
                        $('#staff option[value="' + id + '"]').remove();
                        $('.addMember-item#imgbox' + id).remove();
                        if (all_members_count - selected_count == 1) {
                            a.unselect($('.addMember-list-item#All'));
                            $('#staff option[value="All"]').remove();
                            $('.addMember-item').remove();
                            $.each(members, function (index, item) {
                                if (item.id != id && item.id != 'All')
                                    a.itemAdd(item);
                            });
                        }
                    }
                } else {
                    if (selected_count == all_members_count - 2) {
                        selector = 'all';
                    }
                }
            });

            $('.addMember-items').on('click', '#imgboxAll .addMember-remove', function (e) {
                e.stopPropagation();
                $.each(members, function (index, item) {
                    a.unselect($('.addMember-list-item#' + item.id));
                    $('#staff option[value="' + item.id + '"]').remove();
                });
                selected_count = 0;
            });
        });
    </script>
    <?php
    wp_die();
}

?>