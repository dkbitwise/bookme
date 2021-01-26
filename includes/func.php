<?php
/* GET APPEARANCE DATA */
function bookme_get_table_appearance( $key, $type, $default = '' ) {
	global $wpdb;
	$table_appearance = $wpdb->prefix . 'bookme_appearance';
	$sqlblb           = "SELECT * FROM " . $table_appearance . " where label_key='" . $key . "' and appearance_type='" . $type . "'";
	$reslabl          = $wpdb->get_results( $sqlblb );

	return ( isset( $reslabl[0]->label_value ) && trim( $reslabl[0]->label_value ) != '' ) ? $reslabl[0]->label_value : $default;
}

/* GET SETTINGS */
function bookme_get_settings( $key, $default = '' ) {
	global $wpdb;
	$table_settings = $wpdb->prefix . 'bookme_settings';
	$resultSetting  = $wpdb->get_results( "SELECT book_value FROM $table_settings WHERE book_key='" . $key . "'" );

	return ( isset( $resultSetting[0]->book_value ) && trim( $resultSetting[0]->book_value ) != '' ) ? $resultSetting[0]->book_value : $default;
}


function bookme_get_google_fonts() {
	$fonts_url = '';

	$encode_sans = _x( 'on', 'Encode sans: on or off', 'bookme' );
	$roboto      = _x( 'on', 'Roboto: on or off', 'bookme' );

	if ( 'off' !== $encode_sans && 'off' !== $roboto ) {
		$font_families = array();

		if ( 'off' !== $encode_sans ) {
			$font_families[] = 'Encode Sans:400,300,700';
		}

		if ( 'off' !== $encode_sans ) {
			$font_families[] = 'Roboto:400,400italic,700';
		}

		$query_args = array(
			'family' => urlencode( implode( '|', $font_families ) ),
			'subset' => urlencode( 'latin,latin-ext' ),
		);

		$fonts_url = add_query_arg( $query_args, 'https://fonts.googleapis.com/css' );
	}

	return esc_url_raw( $fonts_url );
}

function bookme_getCurrentPageURL() {
	if ( ( ! empty( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] !== 'off' ) || $_SERVER['SERVER_PORT'] == 443 ) {
		$url = 'https://';
	} else {
		$url = 'http://';
	}
	$url .= isset( $_SERVER['HTTP_X_FORWARDED_HOST'] ) ? $_SERVER['HTTP_X_FORWARDED_HOST'] : $_SERVER['HTTP_HOST'];

	return $url . $_SERVER['REQUEST_URI'];
}

function bookme_formatPrice( $price ) {
	$price = (float) $price;
	switch ( bookme_get_settings( 'pmt_currency', 'USD' ) ) {
		case 'AED' :
			return number_format_i18n( $price, 2 ) . ' AED';
		case 'ARS' :
			return '$' . number_format_i18n( $price, 2 );
		case 'AUD' :
			return 'A$' . number_format_i18n( $price, 2 );
		case 'BGN' :
			return number_format_i18n( $price, 2 ) . ' лв.';
		case 'BHD' :
			return 'BHD ' . number_format_i18n( $price, 2 );
		case 'BRL' :
			return 'R$ ' . number_format_i18n( $price, 2 );
		case 'CAD' :
			return 'C$' . number_format_i18n( $price, 2 );
		case 'CHF' :
			return number_format_i18n( $price, 2 ) . ' CHF';
		case 'CLP' :
			return 'CLP $' . number_format_i18n( $price, 2 );
		case 'COP' :
			return '$' . number_format_i18n( $price ) . ' COP';
		case 'CRC' :
			return '₡' . number_format_i18n( $price, 2 );
		case 'CZK' :
			return number_format_i18n( $price, 2 ) . ' Kč';
		case 'DKK' :
			return number_format_i18n( $price, 2 ) . ' kr';
		case 'DOP' :
			return 'RD$' . number_format_i18n( $price, 2 );
		case 'DZD' :
			return number_format_i18n( $price, 2 ) . 'دج ';
		case 'EGP' :
			return 'EGP ' . number_format_i18n( $price, 2 );
		case 'EUR' :
			return number_format_i18n( $price, 2 ) . ' €';
		case 'GBP' :
			return '£' . number_format_i18n( $price, 2 );
		case 'GEL' :
			return number_format_i18n( $price, 2 ) . ' lari';
		case 'GTQ' :
			return 'Q' . number_format_i18n( $price, 2 );
		case 'HKD' :
			return 'HK$' . number_format_i18n( $price, 2 );
		case 'HRK' :
			return number_format_i18n( $price, 2 ) . ' kn';
		case 'HUF' :
			return number_format_i18n( $price, 2 ) . ' Ft';
		case 'IDR' :
			return number_format_i18n( $price, 2 ) . ' Rp';
		case 'ILS' :
			return number_format_i18n( $price, 2 ) . ' ₪';
		case 'INR' :
			return number_format_i18n( $price, 2 ) . ' ₹';
		case 'ISK' :
			return number_format_i18n( $price ) . ' kr';
		case 'JPY' :
			return '¥' . number_format_i18n( $price );
		case 'KES' :
			return 'KSh ' . number_format_i18n( $price, 2 );
		case 'KRW' :
			return number_format_i18n( $price, 2 ) . ' ₩';
		case 'KZT' :
			return number_format_i18n( $price, 2 ) . ' тг.';
		case 'KWD' :
			return number_format_i18n( $price, 2 ) . ' KD';
		case 'LAK' :
			return number_format_i18n( $price ) . ' ₭';
		case 'MUR' :
			return 'Rs' . number_format_i18n( $price, 2 );
		case 'MXN' :
			return '$' . number_format_i18n( $price, 2 );
		case 'MYR' :
			return number_format_i18n( $price, 2 ) . ' RM';
		case 'NAD' :
			return 'N$' . number_format_i18n( $price, 2 );
		case 'NGN' :
			return '₦' . number_format_i18n( $price, 2 );
		case 'NOK' :
			return 'Kr ' . number_format_i18n( $price, 2 );
		case 'NZD' :
			return '$' . number_format_i18n( $price, 2 );
		case 'OMR' :
			return number_format_i18n( $price, 3 ) . ' OMR';
		case 'PEN' :
			return 'S/.' . number_format_i18n( $price, 2 );
		case 'PHP' :
			return number_format_i18n( $price, 2 ) . ' ₱';
		case 'PKR' :
			return 'Rs. ' . number_format_i18n( $price );
		case 'PLN' :
			return number_format_i18n( $price, 2 ) . ' zł';
		case 'QAR' :
			return number_format_i18n( $price, 2 ) . ' QAR';
		case 'RMB' :
			return number_format_i18n( $price, 2 ) . ' ¥';
		case 'RON' :
			return number_format_i18n( $price, 2 ) . ' lei';
		case 'RUB' :
			return number_format_i18n( $price, 2 ) . ' руб.';
		case 'SAR' :
			return number_format_i18n( $price, 2 ) . ' SAR';
		case 'SEK' :
			return number_format_i18n( $price, 2 ) . ' kr';
		case 'SGD' :
			return '$' . number_format_i18n( $price, 2 );
		case 'THB' :
			return number_format_i18n( $price, 2 ) . ' ฿';
		case 'TRY' :
			return number_format_i18n( $price, 2 ) . ' TL';
		case 'TWD' :
			return number_format_i18n( $price, 2 ) . ' NT$';
		case 'UAH' :
			return number_format_i18n( $price, 2 ) . ' ₴';
		case 'UGX' :
			return 'UGX ' . number_format_i18n( $price );
		case 'USD' :
			return '$' . number_format_i18n( $price, 2 );
		case 'VND' :
			return number_format_i18n( $price ) . ' VNĐ';
		case 'XAF' :
			return number_format_i18n( $price ) . ' FCFA';
		case 'XOF' :
			return 'CFA ' . number_format_i18n( $price, 2 );
		case 'ZAR' :
			return 'R ' . number_format_i18n( $price, 2 );
		case 'ZMW' :
			return 'K' . number_format_i18n( $price, 2 );
	}

	return number_format_i18n( $price, 2 );
}

function bookme_set_html_mail_content_type() {
	return 'text/html';
}

function bookme_get_services_by_cat_id( $id ) {
	global $wpdb;
	$table_book_service = $wpdb->prefix . 'bookme_service';
	$result             = $wpdb->get_results( "SELECT id,name FROM $table_book_service WHERE catId='$id'and visibility = '1' ORDER BY id ASC" );
	$option             = '<option value="">' . __( 'Select', 'bookme' ) . ' ' . bookme_get_table_appearance( 'service', 'label', __( 'Service', 'bookme' ) ) . '</option>';
	foreach ( $result as $value ) {
		$option .= '<option value="' . $value->id . '">' . $value->name . '</option>';
	}
	echo $option;
}

function bookme_get_emp_by_ser_id( $id ) {
	global $wpdb;
	$table_book_service = $wpdb->prefix . 'bookme_service';
	$table_all_employee = $wpdb->prefix . 'bookme_employee';
	$result             = $wpdb->get_results( "SELECT s.price, group_concat(e.name) ename, group_concat(e.id) eid FROM $table_book_service s LEFT JOIN $table_all_employee e ON find_in_set(e.id,s.staff)<>0 WHERE s.id='$id' and e.visibility = '1' ORDER BY e.id ASC" );

	$option = '<option value="">' . __( 'Select', 'bookme' ) . ' ' . bookme_get_table_appearance( 'employee', 'label', __( 'Employee', 'bookme' ) ) . '</option>';
	$emp    = explode( ',', $result[0]->ename );
	$eid    = explode( ',', $result[0]->eid );
	for ( $i = 0; $i < count( $emp ); $i ++ ) {
		$price = ( $result[0]->price > 0 ) ? '(' . bookme_formatPrice( $result[0]->price ) . ')' : '';
		if ( $emp[ $i ] != '' ) {
			$option .= '<option value="' . $eid[ $i ] . '">' . $emp[ $i ] . $price . '</option>';
		}
	}
	echo $option;
}

function bookme_get_capacity_by_ser_id( $id ) {
	global $wpdb;
	$table_book_service = $wpdb->prefix . 'bookme_service';
	$result             = $wpdb->get_var( "select capacity from $table_book_service where id = $id" );
	$option             = '';
	for ( $i = 1; $i <= $result; $i ++ ) {
		$option .= '<option value="' . $i . '">' . $i . '</option>';
	}
	echo $option;
}

function bookme_get_daysoff_by_staff_id( $id ) {
	global $wpdb;
	$table_holidays = $wpdb->prefix . 'bookme_holidays';
	$result         = $wpdb->get_results( "SELECT * FROM $table_holidays WHERE staff_id = $id" );
	$holidays       = array();
	foreach ( $result as $holiday ) {
		list ( $Y, $m, $d ) = explode( '-', $holiday->holi_date );
		$holidays[ $holiday->id ] = array(
			'm' => (int) $m,
			'd' => (int) $d,
		);
		if ( ! $holiday->repeat_day ) {
			$holidays[ $holiday->id ]['y'] = (int) $Y;
		}
	}

	return $holidays;
}

function bookme_random_string( $length = 10 ) {
	$key  = '';
	$keys = array_merge( range( 0, 9 ), range( 'a', 'z' ) );

	for ( $i = 0; $i < $length; $i ++ ) {
		$key .= $keys[ array_rand( $keys ) ];
	}

	return $key;
}

/**
 * Finding next employee for round robin algo - Dinesh
 *
 * @param $service_id
 * @param $prev_employee
 *
 * @return mixed
 */
function get_next_bookme_emp_for_service_id( $service_id, $prev_employee ) {
	global $wpdb;
	$next_empl_id = $prev_employee;

	if ( $service_id > 0 ) {
		$table_book_service    = $wpdb->prefix . 'bookme_service';
		$table_all_employee    = $wpdb->prefix . 'bookme_employee';
		$table_current_booking = $wpdb->prefix . 'bookme_current_booking';

		$resultS           = $wpdb->get_results( "SELECT staff FROM $table_book_service WHERE id=$service_id" );
		$resultS[0]->staff = str_replace( 'All,', '', $resultS[0]->staff );
		$resultemployee    = $wpdb->get_results( "SELECT id,name, google_data FROM $table_all_employee WHERE id IN (" . $resultS[0]->staff . ") " );

		$employee = [];
		foreach ( $resultemployee as $key => $resulte ) {
			$employee[] = $resulte->id;
		}

		$last_emp = $wpdb->get_var( "SELECT emp_id FROM $table_current_booking ORDER BY id DESC LIMIT 1" );
		sort( $employee );
		$last_emp_index = array_search( $last_emp, $employee );
		$next_emp_index = ( count( $employee ) > $last_emp_index + 1 ) ? $last_emp_index + 1 : 0;

		$next_empl_id = $employee[ $next_emp_index ];
	}

	return $next_empl_id;
}

/**
 * @param $service_name
 *
 * @return int
 */
function get_bookme_service_id_service_name( $service_name ) {
	global $wpdb;
	$service_id = 0;
	if ( ! empty( $service_name ) ) {
		$table_book_service = $wpdb->prefix . 'bookme_service';
		$service_id         = $wpdb->get_var( "SELECT `id` FROM $table_book_service WHERE `name`='$service_name'" );
	}

	return $service_id;
}