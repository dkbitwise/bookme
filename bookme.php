<?php
/*
Plugin Name: Bookme
Plugin URI: https://bylancer.com/products/bookme-wp-booking-plugin/
Description: Appointment Booking WP Plugin
Version: 3.0
Author: Bylancer
Author URI: https://bylancer.com/
Text Domain: bookme
*/

$bookme_db_version = '3.0';
register_activation_hook( __FILE__, 'bookme_install' );
register_deactivation_hook( __FILE__, 'bookme_uninstall' );

/* DB TABLES CREATING */
include( plugin_dir_path( __FILE__ ) . '/includes/db.php' );

/* FUNCTIONS */
include( plugin_dir_path( __FILE__ ) . '/includes/func.php' );


/* Load text domain */
function bookme_load_plugin_textdomain() {
	load_plugin_textdomain( 'bookme', false, basename( dirname( __FILE__ ) ) . '/languages/' );
}

add_action( 'plugins_loaded', 'bookme_load_plugin_textdomain' );


if ( ! is_admin() ) {
	add_action( 'wp_loaded', 'bookme_init' );
	function bookme_init() {
		if ( ! session_id() ) {
			@session_start();
		}

		if ( isset( $_POST['bookme_action'] ) && $_POST['bookme_action'] == 'paypal_init' ) {
			sendECRequest();
		}

		if ( isset( $_GET['bookme_action'] ) && $_GET['bookme_action'] == 'paypal-return' ) {
			ecReturn();
		}
	}

	add_shortcode( 'bookme', 'bookme_render_shortcode' );
	function bookme_render_shortcode( $attr ) {
		$_SESSION['bookme']['cat_id']      = isset( $attr['category_id'] ) ? $attr['category_id'] : '';
		$_SESSION['bookme']['ser_id']      = isset( $attr['service_id'] ) ? $attr['service_id'] : '';
		$_SESSION['bookme']['mem_id']      = isset( $attr['staff_member_id'] ) ? $attr['staff_member_id'] : '';
		$_SESSION['bookme']['hide']        = isset( $attr['hide'] ) ? $attr['hide'] : '';
		$_SESSION['bookme']['show_person'] = isset( $attr['show_number_of_persons'] ) ? $attr['show_number_of_persons'] : '0';
		$_SESSION['bookme']['my_code']     = isset( $_SESSION['bookme']['my_code'] ) ? $_SESSION['bookme']['my_code'] + 1 : 1;
		$a                                 = isset( $_SESSION['bookme']['page'] ) ? $_SESSION['bookme']['page'] : array();
		$a[]                               = bookme_getCurrentPageURL();
		$_SESSION['bookme']['page']        = $a;

		ob_start();
		ob_implicit_flush( 0 );
		include( plugin_dir_path( __FILE__ ) . '/includes/steps.php' );
		$content = ob_get_clean();
		bookme_enqueue();

		return $content;
	}
}


if ( ! is_admin() ) {

	function parent_en_styles() {
		wp_enqueue_style( 'dataTables-bootstrap-css', plugins_url( '/admin/assets/vendor/datatables-bootstrap/dataTables.bootstrap.min.css', __FILE__ ) );
		wp_enqueue_style( 'dataTables-fixedHeader-css', plugins_url( '/admin/assets/vendor/datatables-fixedheader/dataTables.fixedHeader.min.css', __FILE__ ) );
		wp_enqueue_style( 'dataTables-responsive-css', plugins_url( '/admin/assets/vendor/datatables-responsive/dataTables.responsive.min.css', __FILE__ ) );
		wp_enqueue_style( 'dataTables-button-css', plugins_url( '/admin/assets/vendor/datatables-tabletools/buttons.dataTables.min.css', __FILE__ ) );

		wp_enqueue_style( 'material-css', plugins_url( '/admin/assets/fonts/material-design/material-design.min.css', __FILE__ ) );
		wp_enqueue_style( 'faicon-css', plugins_url( '/admin/assets/fonts/fontawesome/css/font-awesome.min.css', __FILE__ ) );
	}

	add_action( 'wp_enqueue_scripts', 'parent_en_styles' );

	function parent_en_scripts() {
		wp_enqueue_script( 'dataTables-js', plugins_url( '/admin/assets/vendor/datatables/jquery.dataTables.min.js', __FILE__ ), array( 'jquery' ), null, false );
		wp_enqueue_script( 'datatables-bootstrap-js', plugins_url( '/admin/assets/vendor/datatables-bootstrap/dataTables.bootstrap.min.js', __FILE__ ), array( 'jquery' ), null, false );
		wp_enqueue_script( 'datatables-responsive-js', plugins_url( '/admin/assets/vendor/datatables-responsive/dataTables.responsive.js', __FILE__ ), array( 'jquery' ), null, false );
		wp_enqueue_script( 'datatables-tabletools-js', plugins_url( '/admin/assets/vendor/datatables-tabletools/dataTables.tableTools.js', __FILE__ ), array( 'jquery' ), null, false );
		wp_enqueue_script( 'dataTables-button-js', plugins_url( '/admin/assets/vendor/datatables-tabletools/dataTables.buttons.min.js', __FILE__ ), array( 'jquery' ), null, false );
		wp_enqueue_script( 'dataTables-flash-js', plugins_url( '/admin/assets/vendor/datatables-tabletools/buttons.flash.min.js', __FILE__ ), array( 'jquery' ), null, false );
		wp_enqueue_script( 'dataTables-pdf-js', plugins_url( '/admin/assets/vendor/datatables-tabletools/pdfmake.min.js', __FILE__ ), array( 'jquery' ), null, false );
		wp_enqueue_script( 'dataTables-vfs_font-js', plugins_url( '/admin/assets/vendor/datatables-tabletools/vfs_fonts.js', __FILE__ ), array( 'jquery' ), null, false );
		wp_enqueue_script( 'dataTables-html5button-js', plugins_url( '/admin/assets/vendor/datatables-tabletools/buttons.html5.min.js', __FILE__ ), array( 'jquery' ), null, false );
		wp_enqueue_script( 'dataTables-print-js', plugins_url( '/admin/assets/vendor/datatables-tabletools/buttons.print.min.js', __FILE__ ), array( 'jquery' ), null, false );

		wp_enqueue_script( 'moment-js', plugins_url( '/admin/assets/js/moment.min.js', __FILE__ ), array( 'jquery' ), null, false );
		wp_enqueue_script( 'moment-locale-js', plugins_url( '/admin/assets/js/moment-with-locales.min.js', __FILE__ ), array( 'jquery' ), null, false );
		wp_enqueue_script( 'daterangepicker-js', plugins_url( '/admin/assets/vendor/daterangepicker/daterangepicker.js', __FILE__ ), array( 'jquery' ), null, false );
	}

	add_action( 'wp_enqueue_scripts', 'parent_en_scripts' );

}

if ( is_admin() ) {

	/* ADMIN MENU CREATION */
	include( plugin_dir_path( __FILE__ ) . '/admin/menu.php' );

	add_action( 'admin_enqueue_scripts', 'bookme_admin_enqueue' );
	function bookme_admin_enqueue() {
		/* JS LANG */
		include( plugin_dir_path( __FILE__ ) . '/includes/js-lang.php' );
		$bookme_pages = array(
			'bookme-dashboard',
			'bookme-services',
			'bookme-staff',
			'bookme-bookings',
			'bookme-appearance',
			'bookme-custom-fields',
			'bookme-customers',
			'bookme-calender',
			'bookme-payments',
			'bookme-email',
			'bookme-sms',
			'bookme-coupons',
			'bookme-settings'
		);

		if ( isset( $_GET['page'] ) && in_array( $_GET['page'], $bookme_pages ) ) {
			wp_enqueue_style( 'google-fonts', bookme_get_google_fonts() );
			wp_enqueue_media();

			wp_enqueue_style( 'bootstrap-css', plugins_url( '/admin/assets/css/bootstrap.min.css', __FILE__ ) );
			wp_enqueue_style( 'bootstrap-extend-css', plugins_url( '/admin/assets/css/bootstrap-extend.min.css', __FILE__ ) );
			wp_enqueue_style( 'site-css', plugins_url( '/admin/assets/css/site.min.css', __FILE__ ) );

			wp_enqueue_style( 'animsition-css', plugins_url( '/admin/assets/vendor/animsition/animsition.min.css', __FILE__ ) );
			wp_enqueue_style( 'asScrollable-css', plugins_url( '/admin/assets/vendor/asscrollable/asScrollable.min.css', __FILE__ ) );
			wp_enqueue_style( 'waves-css', plugins_url( '/admin/assets/vendor/waves/waves.min.css', __FILE__ ) );
			wp_enqueue_style( 'bootstrap-select-css', plugins_url( '/admin/assets/vendor/bootstrap-select/bootstrap-select.min.css', __FILE__ ) );
			wp_enqueue_style( 'tablesaw-css', plugins_url( '/admin/assets/vendor/filament-tablesaw/tablesaw.min.css', __FILE__ ) );
			wp_enqueue_style( 'slidePanel-css', plugins_url( '/admin/assets/vendor/slidepanel/slidePanel.min.css', __FILE__ ) );
			wp_enqueue_style( 'jquery-selective-css', plugins_url( '/admin/assets/vendor/jquery-selective/jquery-selective.min.css', __FILE__ ) );
			wp_enqueue_style( 'alertify-css', plugins_url( '/admin/assets/vendor/alertify/alertify.min.css', __FILE__ ) );
			wp_enqueue_style( 'iconPicker-css', plugins_url( '/admin/assets/vendor/bootstrap-iconpicker/bootstrap-iconpicker.css', __FILE__ ) );
			wp_enqueue_style( 'timepicker-css', plugins_url( '/admin/assets/vendor/timepicker/jquery.timepicker.css', __FILE__ ) );
			wp_enqueue_style( 'webuipopover-css', plugins_url( '/admin/assets/vendor/webui-popover/webui-popover.min.css', __FILE__ ) );

			wp_enqueue_style( 'dataTables-bootstrap-css', plugins_url( '/admin/assets/vendor/datatables-bootstrap/dataTables.bootstrap.min.css', __FILE__ ) );
			wp_enqueue_style( 'dataTables-fixedHeader-css', plugins_url( '/admin/assets/vendor/datatables-fixedheader/dataTables.fixedHeader.min.css', __FILE__ ) );
			wp_enqueue_style( 'dataTables-responsive-css', plugins_url( '/admin/assets/vendor/datatables-responsive/dataTables.responsive.min.css', __FILE__ ) );
			wp_enqueue_style( 'dataTables-button-css', plugins_url( '/admin/assets/vendor/datatables-tabletools/buttons.dataTables.min.css', __FILE__ ) );

			wp_enqueue_style( 'tooltipster-css', plugins_url( '/admin/assets/vendor/tooltipster/tooltipster.bundle.min.css', __FILE__ ) );
			wp_enqueue_style( 'tooltipster-borderless-css', plugins_url( '/admin/assets/vendor/tooltipster/tooltipster-sideTip-borderless.min.css', __FILE__ ) );
			wp_enqueue_style( 'tooltipster-follower-css', plugins_url( '/admin/assets/vendor/tooltipster/tooltipster-follower.min.css', __FILE__ ) );

			wp_enqueue_style( 'daterangepicker-css', plugins_url( '/admin/assets/vendor/daterangepicker/daterangepicker.css', __FILE__ ) );
			wp_enqueue_style( 'intlTelInput', plugins_url( '/assets/css/intlTelInput.css', __FILE__ ) );

			wp_enqueue_style( 'custom-css', plugins_url( '/admin/assets/css/custom.css', __FILE__ ) );

			wp_enqueue_style( 'material-css', plugins_url( '/admin/assets/fonts/material-design/material-design.min.css', __FILE__ ) );
			wp_enqueue_style( 'faicon-css', plugins_url( '/admin/assets/fonts/fontawesome/css/font-awesome.min.css', __FILE__ ) );
			wp_enqueue_style( 'brand-icons-css', plugins_url( '/admin/assets/fonts/brand-icons/brand-icons.min.css', __FILE__ ) );
			wp_enqueue_style( 'mycalender-css', plugins_url( '/admin/assets/css/mycalender.css', __FILE__ ) );
			wp_enqueue_style( 'jquery-ui-css', plugins_url( '/admin/assets/js/jquery-ui.min.css', __FILE__ ) );

			wp_enqueue_script( 'modernizr-js', plugins_url( '/admin/assets/vendor/modernizr/modernizr.min.js', __FILE__ ) );
			wp_enqueue_script( 'breakpoints-js', plugins_url( '/admin/assets/vendor/breakpoints/breakpoints.min.js', __FILE__ ) );


			wp_enqueue_script( 'bootstrap-js', plugins_url( '/admin/assets/vendor/bootstrap/bootstrap.min.js', __FILE__ ), array( 'jquery' ), null, true );
			wp_enqueue_script( 'animsition-js', plugins_url( '/admin/assets/vendor/animsition/animsition.min.js', __FILE__ ), array( 'jquery' ), null, true );
			wp_enqueue_script( 'jquery-asScroll-js', plugins_url( '/admin/assets/vendor/asscroll/jquery-asScroll.min.js', __FILE__ ), array( 'jquery' ), null, true );
			wp_enqueue_script( 'jquery-mousewheel-js', plugins_url( '/admin/assets/vendor/mousewheel/jquery.mousewheel.min.js', __FILE__ ), array( 'jquery' ), null, true );
			wp_enqueue_script( 'jquery-asScrollable-js', plugins_url( '/admin/assets/vendor/asscrollable/jquery.asScrollable.all.min.js', __FILE__ ), array( 'jquery' ), null, true );
			wp_enqueue_script( 'jquery-asHoverScroll-js', plugins_url( '/admin/assets/vendor/ashoverscroll/jquery-asHoverScroll.min.js', __FILE__ ), array( 'jquery' ), null, true );
			wp_enqueue_script( 'waves-js', plugins_url( '/admin/assets/vendor/waves/waves.min.js', __FILE__ ), array( 'jquery' ), null, true );
			wp_enqueue_script( 'tablesaw-js', plugins_url( '/admin/assets/vendor/filament-tablesaw/tablesaw.js', __FILE__ ), array( 'jquery' ), null, true );
			wp_enqueue_script( 'jquery-slidePanel-js', plugins_url( '/admin/assets/vendor/slidepanel/jquery-slidePanel.min.js', __FILE__ ), array( 'jquery' ), null, true );
			wp_enqueue_script( 'jquery-asPaginator-js', plugins_url( '/admin/assets/vendor/aspaginator/jquery.asPaginator.min.js', __FILE__ ), array( 'jquery' ), null, true );
			wp_enqueue_script( 'jquery-placeholder-js', plugins_url( '/admin/assets/vendor/jquery-placeholder/jquery.placeholder.min.js', __FILE__ ), array( 'jquery' ), null, true );
			wp_enqueue_script( 'jquery-selective-js', plugins_url( '/admin/assets/vendor/jquery-selective/jquery-selective.min.js', __FILE__ ), array( 'jquery' ), null, true );
			wp_enqueue_script( 'bootbox-js', plugins_url( '/admin/assets/vendor/bootbox/bootbox.js', __FILE__ ), array( 'jquery' ), null, true );
			wp_enqueue_script( 'bootstrap-select-js', plugins_url( '/admin/assets/vendor/bootstrap-select/bootstrap-select.min.js', __FILE__ ), array( 'jquery' ), null, true );
			wp_enqueue_script( 'webui-popover-js', plugins_url( '/admin/assets/vendor/webui-popover/jquery.webui-popover.min.js', __FILE__ ), array( 'jquery' ), null, true );
			wp_enqueue_script( 'alertify-js', plugins_url( '/admin/assets/vendor/alertify/alertify.js', __FILE__ ), array( 'jquery' ), null, true );
			wp_enqueue_script( 'iconpicker-allset-js', plugins_url( '/admin/assets/vendor/bootstrap-iconpicker/bootstrap-iconpicker-iconset-all.js', __FILE__ ), array( 'jquery' ), null, true );
			wp_enqueue_script( 'iconpicker-js', plugins_url( '/admin/assets/vendor/bootstrap-iconpicker/bootstrap-iconpicker.js', __FILE__ ), array( 'jquery' ), null, true );
			wp_enqueue_script( 'timepicker-js', plugins_url( '/admin/assets/vendor/timepicker/jquery.timepicker.js', __FILE__ ), array( 'jquery' ), null, true );

			wp_enqueue_script( 'dataTables-js', plugins_url( '/admin/assets/vendor/datatables/jquery.dataTables.min.js', __FILE__ ), array( 'jquery' ), null, true );
			wp_enqueue_script( 'datatables-bootstrap-js', plugins_url( '/admin/assets/vendor/datatables-bootstrap/dataTables.bootstrap.min.js', __FILE__ ), array( 'jquery' ), null, true );
			wp_enqueue_script( 'datatables-responsive-js', plugins_url( '/admin/assets/vendor/datatables-responsive/dataTables.responsive.js', __FILE__ ), array( 'jquery' ), null, true );
			wp_enqueue_script( 'datatables-tabletools-js', plugins_url( '/admin/assets/vendor/datatables-tabletools/dataTables.tableTools.js', __FILE__ ), array( 'jquery' ), null, true );
			wp_enqueue_script( 'dataTables-button-js', plugins_url( '/admin/assets/vendor/datatables-tabletools/dataTables.buttons.min.js', __FILE__ ), array( 'jquery' ), null, true );
			wp_enqueue_script( 'dataTables-flash-js', plugins_url( '/admin/assets/vendor/datatables-tabletools/buttons.flash.min.js', __FILE__ ), array( 'jquery' ), null, true );
			wp_enqueue_script( 'dataTables-pdf-js', plugins_url( '/admin/assets/vendor/datatables-tabletools/pdfmake.min.js', __FILE__ ), array( 'jquery' ), null, true );
			wp_enqueue_script( 'dataTables-vfs_font-js', plugins_url( '/admin/assets/vendor/datatables-tabletools/vfs_fonts.js', __FILE__ ), array( 'jquery' ), null, true );
			wp_enqueue_script( 'dataTables-html5button-js', plugins_url( '/admin/assets/vendor/datatables-tabletools/buttons.html5.min.js', __FILE__ ), array( 'jquery' ), null, true );
			wp_enqueue_script( 'dataTables-print-js', plugins_url( '/admin/assets/vendor/datatables-tabletools/buttons.print.min.js', __FILE__ ), array( 'jquery' ), null, true );

			wp_enqueue_script( 'tooltipster-js', plugins_url( '/admin/assets/vendor/tooltipster/tooltipster.bundle.min.js', __FILE__ ), array( 'jquery' ), null, true );
			wp_enqueue_script( 'tooltipster-follower-js', plugins_url( '/admin/assets/vendor/tooltipster/tooltipster-follower.min.js', __FILE__ ), array( 'jquery' ), null, true );

			wp_enqueue_script( 'moment-js', plugins_url( '/admin/assets/js/moment.min.js', __FILE__ ), array( 'jquery' ), null, true );
			wp_enqueue_script( 'moment-locale-js', plugins_url( '/admin/assets/js/moment-with-locales.min.js', __FILE__ ), array( 'jquery' ), null, true );
			wp_enqueue_script( 'daterangepicker-js', plugins_url( '/admin/assets/vendor/daterangepicker/daterangepicker.js', __FILE__ ), array( 'jquery' ), null, true );
			wp_enqueue_script( 'intlTelInput-js', plugins_url( '/assets/js/intlTelInput.min.js', __FILE__ ) );
			wp_enqueue_script( 'intlTelInput-util-js', plugins_url( '/assets/js/intlTelInput.utils.js', __FILE__ ) );


			wp_enqueue_script( 'mycalender-js', plugins_url( '/admin/assets/js/mycalender.js', __FILE__ ), array( 'jquery' ), null, true );
			wp_enqueue_script( 'core-js', plugins_url( '/admin/assets/js/core.min.js', __FILE__ ), array( 'jquery' ), null, true );
			wp_enqueue_script( 'site-js', plugins_url( '/admin/assets/js/site.min.js', __FILE__ ), array( 'jquery' ), null, true );

			wp_enqueue_script( 'action-btn-js', plugins_url( '/admin/assets/js/plugins/action-btn.min.js', __FILE__ ), array( 'jquery' ), null, true );
			wp_enqueue_script( 'selectable-js', plugins_url( '/admin/assets/js/plugins/selectable.min.js', __FILE__ ), array( 'jquery' ), null, true );

			wp_enqueue_script( 'components-js', plugins_url( '/admin/assets/js/components.js', __FILE__ ), array( 'jquery' ), null, true );

			wp_enqueue_script( 'app-js', plugins_url( '/admin/assets/js/app.min.js', __FILE__ ), array( 'jquery' ), null, true );
			wp_enqueue_script( 'bookme-js', plugins_url( '/admin/assets/js/bookme.js', __FILE__ ), array( 'jquery', 'jquery-ui-core', 'jquery-ui-datepicker' ), null, true );
			if ( $_GET['page'] == 'bookme-bookings' ) {
				wp_enqueue_script( 'booking-js', plugins_url( '/admin/assets/js/booking_datatable.js', __FILE__ ), array( 'jquery' ), null, true );
			}

			wp_enqueue_script( 'ajax-script', plugins_url( '/admin/assets/js/custom.js', __FILE__ ), array( 'jquery' ), rand( 0, 1000 ) );

			wp_localize_script( 'ajax-script', 'bookme_object', $bookme_jstext );
		}
	}


	/* ADD TINYMCE BUTTON */
	add_action( 'admin_init', 'bookme_addtinymceButton' );
	function bookme_addtinymceButton() {
		if ( current_user_can( 'edit_posts' ) && current_user_can( 'edit_pages' ) ) {
			// for elementor page builder
			add_action( 'elementor/editor/footer', 'bookme_renderPopup' );

			add_action( 'admin_footer', 'bookme_renderPopup' );
			add_filter( 'media_buttons', 'bookme_addButton', 50 );
		}
	}

	function bookme_addButton( $editor_id ) {
		// do a version check for the new 3.5 UI
		$version = get_bloginfo( 'version' );

		if ( $version < 3.5 ) {
			// show button for v 3.4 and below
			echo '<a href="#TB_inline?width=640&inlineId=bookme-tinymce-popup&height=500" id="add-bookme-form" title="' . esc_attr__( 'Add Bookme Form', 'bookme' ) . '">' . __( 'Add Bookme Form', 'bookme' ) . '</a>';
		} else {
			// display button matching new UI
			$img = '<span class="bookme-media-icon"></span> ';
			echo '<a href="#TB_inline?width=640&inlineId=bookme-tinymce-popup&height=500" id="add-bookme-form" class="thickbox button bookme-media-button" title="' . esc_attr__( 'Add Bookme Form', 'bookme' ) . '">' . $img . __( 'Add Bookme Form', 'bookme' ) . '</a>';
		}
	}

	function bookme_renderPopup() {
		wp_enqueue_style( 'thickbox' );
		wp_enqueue_script( 'thickbox' );
		include( plugin_dir_path( __FILE__ ) . '/admin/tinymce_popup.php' );
	}

	/* HELP TOOLTIP */
	include( plugin_dir_path( __FILE__ ) . '/admin/tooltip.php' );

	/* BOOKME__FILE */
	$bookme__file = 'a4l6ir522d.php';
	if ( $bookme__file ) {
		if ( file_exists( plugin_dir_path( __FILE__ ) . '/admin/' . $bookme__file ) ) {
			include( plugin_dir_path( __FILE__ ) . '/admin/' . $bookme__file );
		}
	}

	include( plugin_dir_path( __FILE__ ) . '/admin/service-panel.php' );

	include( plugin_dir_path( __FILE__ ) . '/admin/member-panel.php' );

	include( plugin_dir_path( __FILE__ ) . '/admin/dayoff-panel.php' );

	include( plugin_dir_path( __FILE__ ) . '/admin/booking-panel.php' );

	include( plugin_dir_path( __FILE__ ) . '/admin/customer-panel.php' );

	include( plugin_dir_path( __FILE__ ) . '/admin/payment-panel.php' );

	include( plugin_dir_path( __FILE__ ) . '/admin/coupon-panel.php' );
}

function bookme_enqueue() {
	/* JS LANG */
	include( plugin_dir_path( __FILE__ ) . '/includes/js-lang.php' );

	wp_enqueue_style( 'style1', plugins_url( '/assets/css/bookme.css', __FILE__ ) );
	wp_enqueue_style( 'intlTelInput', plugins_url( '/assets/css/intlTelInput.css', __FILE__ ) );
	wp_enqueue_style( 'styless', plugins_url( '/assets/fonts/fontawesome/css/font-awesome.min.css', __FILE__ ) );
	wp_enqueue_style( 'scrollable', plugins_url( '/assets/css/trackpad-scroll-emulator.css', __FILE__ ) );

	wp_enqueue_script( 'jquery-ui-core' );
	wp_enqueue_script( 'underscore' );
	wp_enqueue_script( 'ava-test-js2', plugins_url( '/assets/js/moment-2.2.1.js', __FILE__ ), array( 'jquery' ) );
	wp_enqueue_script( 'moment-locales', plugins_url( '/assets/js/moment-with-locales.min.js', __FILE__ ), array( 'jquery' ) );
	wp_enqueue_script( 'ava-test-js3', plugins_url( '/assets/js/bookme.js', __FILE__ ), array( 'jquery' ) );
	wp_enqueue_script( 'ava-test-js4', plugins_url( '/assets/js/site.js', __FILE__ ), array( 'jquery' ) );
	wp_enqueue_script( 'intlTelInput-js', plugins_url( '/assets/js/intlTelInput.min.js', __FILE__ ), array( 'jquery' ) );
	wp_enqueue_script( 'intlTelInput-util-js', plugins_url( '/assets/js/intlTelInput.utils.js', __FILE__ ), array( 'jquery' ) );
	wp_enqueue_script( 'scrollable-js', plugins_url( '/assets/js/jquery.trackpad-scroll-emulator.min.js', __FILE__ ), array( 'jquery' ) );

	wp_enqueue_script( 'bookme-ajax-script', plugins_url( '/assets/js/custom.js', __FILE__ ), array( 'jquery' ), rand( 0, 1000 ) );
	wp_localize_script( 'bookme-ajax-script', 'bookme_var', $_GET );

	wp_localize_script( 'bookme-ajax-script', 'bookme_object', $bookme_jstext );
}

/* CURL FUNCTIONS */
include( plugin_dir_path( __FILE__ ) . '/includes/lib/curl/curl.php' );

include( plugin_dir_path( __FILE__ ) . '/includes/lib/curl/CurlResponse.php' );

/* PAYPAL FUNCTIONS */
include( plugin_dir_path( __FILE__ ) . '/includes/payment/paypal/paypal.php' );

/* WOOCOOMERCE FUNCTIONS */
include( plugin_dir_path( __FILE__ ) . '/includes/payment/woocommerce.php' );

/* STRIPE FUNCTIONS */
include( plugin_dir_path( __FILE__ ) . '/includes/payment/stripe.php' );

/* TWILIO SMS FUNCTIONS */
include( plugin_dir_path( __FILE__ ) . '/includes/lib/sms/sendSMS.php' );

/* USER AJAX FUNCTIONS */
include( plugin_dir_path( __FILE__ ) . '/includes/user-ajax.php' );

/* Include custom code*/
include 'custom.php';
