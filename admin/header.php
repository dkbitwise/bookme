<div class="mka-cp-header">
    <div class="mka-cp-branding">
        <div class="mka-cp-bookme-logo">
            <img src="<?php echo plugins_url( 'assets/images/logo.png', __FILE__); ?>">
        </div>
        <strong>
            <span><?php _e('Bookme Control Panel', 'bookme'); ?></span>
        </strong>
    </div>
    <?php global $bookme_db_version; ?>
    <div class="mka-cp-theme-version"><?php echo __('Version', 'bookme') . ' ' . $bookme_db_version; ?></div>
</div>
<?php
if ( !get_option( 'bookme_initial' ) && $_GET['page'] != 'bookme-settings') {
    // Display Message
    echo '<div class="alert alert-warning fade in">';
    echo '<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>';
    echo '<strong style="font-weight:700">'.__('Bookme','bookme').':</strong> ';
    printf(__( 'Please verify your purchase code for using Bookme plugin. <a href="%s" class="alert-link">Click here to verify.</a>', 'bookme' ),admin_url('admin.php?page=bookme-settings'));
    echo '</div>';
}
?>