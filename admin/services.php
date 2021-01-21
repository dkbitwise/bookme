<?php
global $wpdb;
$table_book_category = $wpdb->prefix . 'bookme_category';
$table_book_service = $wpdb->prefix . 'bookme_service';
$table_all_employee = $wpdb->prefix . 'bookme_employee';
$result = $wpdb->get_results("SELECT c.*, count(s.id) num_service FROM $table_book_category c LEFT JOIN $table_book_service s ON s.catId = c.id GROUP BY c.id");


$resultS = $wpdb->get_results("SELECT *, count(*) total_services FROM $table_book_service ORDER BY id ASC");
?>

<div class="app-contacts">

    <?php include(plugin_dir_path(__FILE__) . '/header.php'); ?>


    <div class="page bg-white">
        <div class="page-aside">
            <!-- Contacts Sidebar -->
            <div class="page-aside-switch">
                <i class="icon md-chevron-left" aria-hidden="true"></i>
                <i class="icon md-chevron-right" aria-hidden="true"></i>
            </div>
            <div class="page-aside-inner">
                <div>
                    <div>
                        <div class="page-aside-section">
                            <div class="list-group">
                                <a class="list-group-item" href="javascript:void(0)" data-catid="0">
                                    <span class="item-right"><?php echo $resultS[0]->total_services; ?></span><i
                                        class="icon md-star"
                                        aria-hidden="true"></i><?php _e('All Services', 'bookme'); ?></a>
                            </div>
                        </div>
                        <div class="page-aside-section">
                            <h5 class="page-aside-title"><?php _e('CATEGORIES', 'bookme'); ?></h5>

                            <div class="list-group has-actions">
                                <div id="ajax-cat-section">
                                    <?php foreach ($result as $value) { ?>
                                        <div
                                            class="list-group-item cat-edit <?php echo ($value->status == 'invalid') ? 'bg-danger' : ''; ?>"
                                            data-catid="<?php echo $value->id; ?>">
                                            <div class="list-content">
                                                <span class="item-right"><?php echo $value->num_service; ?></span>
                                                <span class="list-text"><?php echo $value->name; ?></span>

                                                <div class="item-actions">
                                        <span class="btn btn-pure btn-icon" data-toggle="list-editable"><i
                                                class="icon md-edit" aria-hidden="true"></i></span>
                                        <span class="btn btn-pure btn-icon" data-tag="list-delete"><i
                                                class="icon md-delete" aria-hidden="true"></i></span>
                                                </div>
                                            </div>
                                            <div class="list-editable">
                                                <div class="form-group form-material">
                                                    <input type="text" class="form-control empty" id="new_name"
                                                           data-bind=".list-text"
                                                           value="<?php echo $value->name; ?>">
                                                    <select data-plugin="selectpicker" id="catState">
                                                        <option
                                                            value="valid" <?php echo ($value->status == 'valid') ? 'selected' : ''; ?>>
                                                            <?php _e('Valid','bookme'); ?>
                                                        </option>
                                                        <option
                                                            value="invalid" <?php echo ($value->status == 'invalid') ? 'selected' : ''; ?>>
                                                            <?php _e('Invalid','bookme'); ?>
                                                        </option>
                                                    </select>
                                                    <input type="hidden" class="form-control"
                                                           value="<?php echo $value->id; ?>" id="catID"/>
                                                    <button type="button"
                                                            class="input-editable-close icon md-check-circle"
                                                            data-save="save-cat" aria-label="Save"
                                                            aria-expanded="true"></button>
                                                    <button type="button" class="input-editable-close icon md-close"
                                                            data-toggle="list-editable-close" aria-label="Close"
                                                            aria-expanded="true"></button>
                                                </div>
                                            </div>
                                        </div>
                                    <?php } ?>
                                </div>
                                <a id="addcatModelToggle" class="list-group-item" href="javascript:void(0)">
                                    <i class="icon md-plus" aria-hidden="true"></i> <?php _e('Add New Category','bookme'); ?>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contacts Content -->
        <div class="page-main">

            <!-- Contacts Content Header -->
            <div class="page-header">
                <h1 class="page-title"><?php _e('Service List', 'bookme'); ?></h1>

                <div class="page-header-actions">
                    <form>
                        <div class="input-search input-search-dark">
                            <i class="input-search-icon md-search" aria-hidden="true"></i>
                            <input type="text" class="form-control live-search-box" name=""
                                   placeholder="<?php _e('Search...', 'bookme'); ?>">
                        </div>
                    </form>
                </div>
            </div>

            <!-- Contacts Content -->
            <div class="page-content page-content-table position-relative">
                <!-- Contacts -->
                <table class="table is-indent tablesaw" data-tablesaw-mode="stack" data-plugin="animateList"
                       data-animate="fade" data-child="tr" data-selectable="selectable">
                    <thead>
                    <tr>
                        <th class="pre-cell"></th>
                        <th class="cell-30" scope="col" data-tablesaw-sortable-col data-tablesaw-priority="3">
                <span class="checkbox-custom checkbox-primary checkbox-lg contacts-select-all">
                  <input type="checkbox" class="contacts-checkbox selectable-all" id="select_all"
                      />
                  <label for="select_all"></label>
                </span>
                        </th>
                        <th class="cell-300" scope="col" data-tablesaw-sortable-col data-tablesaw-priority="3">
                            <?php _e('Name', 'bookme'); ?>
                        </th>
                        <th class="cell-300" scope="col" data-tablesaw-sortable-col data-tablesaw-priority="3">
                            <?php _e('Duration', 'bookme'); ?>
                        </th>
                        <th scope="col" data-tablesaw-sortable-col
                            data-tablesaw-priority="4"><?php _e('Price', 'bookme'); ?></th>
                        <th class="suf-cell"></th>
                    </tr>
                    </thead>
                    <tbody id="ajax-services">

                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <!-- Site Action -->
    <div class="site-action">
        <button data-url="<?php echo admin_url('admin-ajax.php') . '?action=edit_ser'; ?>" data-toggle="slidePanel"
                id="slidepanel-show" style="display: none;"></button>
        <button type="button" class="site-action-toggle btn-raised btn btn-success btn-floating">
            <i class="front-icon md-plus animation-scale-up" aria-hidden="true"></i>
            <i class="back-icon md-close animation-scale-up" aria-hidden="true"></i>
        </button>
        <div class="site-action-buttons">
            <button type="button" data-action="trash" id="del_service_array"
                    class="btn-raised btn btn-danger btn-floating animation-slide-bottom">
                <i class="icon md-delete" aria-hidden="true"></i>
            </button>
        </div>
    </div>
    <!-- End Site Action -->

    <!-- Add Category Form -->
    <div class="modal fade" id="addcatmodal" aria-hidden="true" aria-labelledby="addLabelForm"
         role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" aria-hidden="true" data-dismiss="modal">&Cross;</button>
                    <h4 class="modal-title"><?php _e('Add New Category', 'bookme'); ?></h4>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="form-group">
                            <input type="text" id="name" class="form-control" name="lablename"
                                   placeholder="<?php _e('Category Name', 'bookme'); ?>" />
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary" type="submit" id="save_cat"><?php _e('Save', 'bookme'); ?></button>
                    <a class="btn btn-sm btn-white btn-pure" data-dismiss="modal"
                       href="javascript:void(0)"><?php _e('Cancel', 'bookme'); ?></a>
                </div>
            </div>
        </div>
    </div>
    <!-- End Add Category Form -->
</div>