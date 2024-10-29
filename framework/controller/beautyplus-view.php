<?php

/**
* BeautyPlus View
*
*
* @since      1.0.0
* @package    BeautyPlus
* @subpackage BeautyPlus/framework
* @author     Rajthemes
* */


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class BeautyPlus_View extends BeautyPlus {

	public static function run( $file_original, $data = array() ) {

		$theme = sanitize_key(BeautyPlus::$theme);

		$file_original_c = explode('/', $file_original);

		$view_file = "";

		foreach ($file_original_c as $file) {
			$sanitize_file_name = sanitize_file_name($file);
			if ($sanitize_file_name !== '') {
				$view_file .='/'.$sanitize_file_name;
			}
		}

		$file = BeautyPlus_Framework . "view/themes/" . $theme . "/" . $view_file . '.php';

		if (! file_exists ( $file )) {
			$file = BeautyPlus_Framework . "view/shared/" . $view_file . '.php';
		}

		extract($data, EXTR_REFS);
		ob_start();
		include($file);
		return ob_get_clean();
	}

	public static function reactor( $file_original, $data = array() ) {

		$theme = sanitize_key(BeautyPlus::$theme);

		$file_original_c = explode('/', $file_original);

		$view_file = "";

		foreach ($file_original_c as $file) {
			$sanitize_file_name = sanitize_file_name($file);
			if ($sanitize_file_name !== '') {
				$view_file .='/'.$sanitize_file_name;
			}
		}

		$file = BeautyPlus_Framework . "libs/reactors/" . $theme . "" . $view_file . '.php';

		if (! file_exists ( $file )) {
			$file = BeautyPlus_Framework . "libs/reactors" . $view_file . '.php';
		}

		extract($data, EXTR_REFS);
		ob_start();
		include($file);
		return ob_get_clean();
	}

}

?>