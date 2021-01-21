<?php
global $wpdb;

function bookme_is_gutenberg_page(){
    if (function_exists('is_gutenberg_page') &&
        is_gutenberg_page()
    ) {
        // The Gutenberg plugin is on.
        return true;
    }
    $current_screen = get_current_screen();
    if (method_exists($current_screen, 'is_block_editor') &&
        $current_screen->is_block_editor()
    ) {
        // Gutenberg page on 5+.
        return true;
    }
    return false;
}
?>
<div id="bookme-tinymce-popup" style="display: none">
    <form id="bookme-shortcode-form">
        <table>
            <tr>
                <td>
                    <label for="bookme-select-category"><?php _e('Default value for category select', 'bookme') ?></label>
                </td>
                <td>
                    <?php
                    $tableC = $wpdb->prefix . 'bookme_category';
                    $resultC = $wpdb->get_results("SELECT id,name FROM $tableC where status='valid'");
                    ?>
                    <select id="bookme-select-category">
                        <option value=""><?php _e('Select Category', 'bookme') ?></option>
                        <?php
                        foreach ($resultC as $result) { ?>
                            <option value="<?php echo $result->id; ?>"><?php echo $result->name; ?></option>
                        <?php } ?>
                    </select>
                    <div><label><input type="checkbox"
                                       id="bookme-hide-categories"/><?php _e('Hide this field', 'bookme') ?></label>
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <label for="bookme-select-service"><?php _e('Default value for service select', 'bookme') ?></label>
                </td>
                <td>
                    <select id="bookme-select-service">
                        <option value=""><?php _e('Select Service', 'bookme') ?></option>
                    </select>
                    <div><label><input type="checkbox"
                                       id="bookme-hide-services"/><?php _e('Hide this field', 'bookme') ?></label></div>
                </td>
            </tr>
            <tr>
                <td>
                    <label for="bookme-select-employee"><?php _e('Default value for employee select', 'bookme') ?></label>
                </td>
                <td>
                    <select class="bookme-select-mobile" id="bookme-select-employee">
                        <option value=""><?php _e('Select Employee', 'bookme') ?></option>
                    </select>
                    <div><label><input type="checkbox"
                                       id="bookme-hide-employee"/><?php _e('Hide this field', 'bookme') ?></label></div>
                </td>
            </tr>
            <tr>
                <td></td>
                <td>
                    <i><?php _e('Please be aware that a value in above fields is required in the frontend. If you choose to hide these fields, please be sure to select a default value for it', 'bookme') ?></i>
                </td>
            </tr>
            <tr>
                <td>
                    <label for="bookme-hide-number-of-persons"><?php _e('No of person', 'bookme'); ?></label>
                </td>
                <td>
                    <label><input type="checkbox" id="bookme-hide-number-of-persons"
                                  checked/><?php _e('Hide this field', 'bookme') ?></label>
                </td>
            </tr>
            <tr>
                <td>
                    <input class="button button-primary" id="bookme-insert-shortcode" type="submit"
                           value="<?php _e('Insert', 'bookme') ?>"/>
                </td>
                <td></td>
            </tr>
        </table>
    </form>
</div>
<style type="text/css">
    #bookme-shortcode-form {
        margin-top: 15px;
    }

    #bookme-shortcode-form table {
        width: 100%;
    }

    #bookme-shortcode-form table td {
        vertical-align: top;
        padding: 10px 0;
    }

    #bookme-shortcode-form table td select {
        width: 100%;
        margin-bottom: 5px;
    }

    .bookme-media-icon {
        display: inline-block;
        width: 16px;
        height: 16px;
        vertical-align: text-top;
        margin: 0 2px;
        background: url("<?php echo plugins_url( '/admin/assets/images/menu-icon.png', __DIR__ ) ?>") 0 0 no-repeat;
    }

    .components-button .bookme-media-icon {
        margin: 4px 6px 4px 0;
    }

    #TB_overlay {
        z-index: 100001 !important;
    }

    #TB_window {
        z-index: 100002 !important;
        overflow: hidden;
        height: 488px !important;
    }
</style>
<script>
    jQuery(function ($) {
        function openFormModal() {
            window.parent.tb_show(<?php echo json_encode(__('Add Bookme Form', 'bookme')) ?>, '#TB_inline?width=640&inlineId=bookme-tinymce-popup&height=650');
            window.setTimeout(function () {
                $('#TB_window').css({
                    'overflow-x': 'auto',
                    'overflow-y': 'hidden'
                });
            }, 100);
        }

        function getShortcode() {
            var insert = '[bookme';
            var hide = [];
            if ($("#bookme-select-category").val()) {
                insert += ' category_id="' + $("#bookme-select-category").val() + '"';
            }
            if ($("#bookme-hide-categories").is(':checked')) {
                hide.push('categories');
            }
            if ($('#bookme-select-service').val()) {
                insert += ' service_id="' + $('#bookme-select-service').val() + '"';
            }
            if ($('#bookme-hide-services').is(':checked')) {
                hide.push('services');
            }
            if ($("#bookme-select-employee").val()) {
                insert += ' staff_member_id="' + $("#bookme-select-employee").val() + '"';
            }
            if ($('#bookme-hide-employee').is(':checked')) {
                hide.push('employees');
            }
            if ($("#bookme-hide-number-of-persons").is(':not(:checked)')) {
                insert += ' show_number_of_persons="1"';
            }
            if (hide.length > 0) {
                insert += ' hide="' + hide.join() + '"';
            }
            return insert += ']';
        }

        function clearFields() {
            $("#bookme-select-category").val('');
            $('#bookme-select-service').val('');
            $("#bookme-select-employee").val('');
            $("#bookme-hide-categories").prop('checked', false);
            $('#bookme-hide-services').prop('checked', false);
            $('#bookme-hide-employee').prop('checked', false);
            $("#bookme-hide-number-of-persons").prop('checked', true);
        }

        $('#bookme-shortcode-form').on('change', '#bookme-select-category', function () {
            var cat_id = $(this).val();
            if (cat_id) {
                var data = {
                    'action': 'bookme_admin_action',
                    'call': 'get_services_by_cat_id',
                    'cat_id': cat_id
                };
                $.post('<?php echo admin_url('admin-ajax.php'); ?>', data, function (response) {
                    if (response != 0) {
                        $('#bookme-select-service').html(response);
                    }
                });
            }
        }).on('change', '#bookme-select-service', function () {
            var ser_id = $(this).val();
            if (ser_id) {
                var data = {
                    'action': 'bookme_admin_action',
                    'call': 'get_emp_by_ser_id',
                    'ser_id': ser_id
                };
                $.post('<?php echo admin_url('admin-ajax.php'); ?>', data, function (response) {
                    $("#bookme-select-employee").html(response);
                });
            }
        });

        $('#add-bookme-form').on('click', function (e) {
            e.preventDefault();
            openFormModal();
        });

        <?php if (bookme_is_gutenberg_page()){ ?>
        var properties = null,
            el = wp.element.createElement;
        var withInspectorControls = wp.compose.createHigherOrderComponent(function (BlockEdit) {
            return function (props) {
                properties = props;
                if (props.name != 'core/shortcode')
                    return el(
                        wp.element.Fragment,
                        null,
                        el(
                            BlockEdit,
                            props
                        )
                    );

                return el(
                    wp.element.Fragment,
                    null,
                    el(
                        BlockEdit,
                        props
                    ),
                    el(
                        wp.editor.InspectorControls,
                        null,
                        el(
                            wp.components.PanelBody,
                            {
                                title: '<?php _e('Bookme Shortcode', 'bookme') ?>',
                                className: 'block-social-links',
                                initialOpen: true
                            },
                            el(
                                wp.components.Button,
                                {
                                    id: 'add-bookme-form',
                                    className: 'is-button is-default bookme-media-button',
                                    onClick: function () {
                                        openFormModal();
                                    }
                                },
                                el(
                                    'span',
                                    {
                                        className: 'bookme-media-icon'
                                    }
                                ),
                                '<?php _e('Add Bookme booking form', 'bookme') ?>'
                            )
                        )
                    )
                );
            };
        }, 'withInspectorControls');
        wp.hooks.addFilter('editor.BlockEdit', 'Bookme', withInspectorControls);

        $('#bookme-insert-shortcode').on('click', function (e) {
            e.preventDefault();
            properties.setAttributes({text: getShortcode()});
            clearFields();
            window.parent.tb_remove();
            return false;
        });
        <?php } ?>


        $('#bookme-insert-shortcode').on('click', function (e) {
            e.preventDefault();
            window.send_to_editor(getShortcode());
            clearFields();
            window.parent.tb_remove();
            return false;
        });
    });
</script>