<?php

/**
* Login
*
* @since      1.1.7
* @package    BeautyPlus
* @subpackage BeautyPlus/framework/libs/widgets
* @author     Rajthemes
* */


if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}


class Reactors__login__login  {

  public static function settings() {

    wp_enqueue_media();
    wp_enqueue_style( 'wp-color-picker' );
    wp_enqueue_script( 'beautyplus-footer', BeautyPlus_Public . "js/beautyplus-footer.js", array( 'wp-color-picker' ), BeautyPlus_Version, true );

    $reactor = BeautyPlus_Reactors::reactors_list('login');

    $saved = 0;
    if($_POST) {

      if ( ! wp_verify_nonce( $_REQUEST['_wpnonce'], 'beautyplus_reactors' ) ) {
        exit;
      }

      $settings = array();

      $settings['position']   = BeautyPlus_Helpers::post('position', 'center');
      $settings['logo']       = intval(BeautyPlus_Helpers::post('logo', '0'));
      $settings['background'] = intval(BeautyPlus_Helpers::post('background', '0'));
      $settings['box']        = BeautyPlus_Helpers::post('box', '#fff');
      $settings['text']       = BeautyPlus_Helpers::post('text', '#000');
      $settings['buttontext'] = BeautyPlus_Helpers::post('buttontext', '#fff');
      $settings['button']     = BeautyPlus_Helpers::post('button', '#000');

      BeautyPlus::option('reactors-login-settings', $settings, 'set');

      $saved = 1;
    }

    $settings = BeautyPlus::option('reactors-login-settings', array());

    echo BeautyPlus_View::reactor('login/views/settings', array('reactor' => $reactor, 'settings'=>$settings, 'saved' => $saved));
  }

  public static function init() {

    add_action( 'login_enqueue_scripts', 'Reactors__login__login::styles' );
    add_action( 'login_footer',          'Reactors__login__login::footer', 99 );
  }

  public static function styles() {

    wp_enqueue_style("beautyplus-reactors-login", BeautyPlus_Public . "reactors/login/login.css", array(), BeautyPlus_Version);

    $settings = BeautyPlus::option('reactors-login-settings', array());

    $css = "";

    if (!empty($settings['logo'])) {
      $logo   = wp_get_attachment_image_src( intval($settings['logo']), 'full' );
      $width  = $logo[1]*84/$logo[2];
      $css   .= ".login h1 a {max-width:100%; background-size:".intval($width)."px; width:".intval($width)."px; background-image: none, url(". esc_url($logo[0]) .")}";
    }

    if (empty($settings['logo']) || '0' === $settings['logo']) {
      $css .= ".login h1 a {display:none} .login form { margin-top:-20px !important; }";
    }


    if ( 'left' === BeautyPlus_Helpers::clean( $settings['position'], 'center' )) {
      $css .= "
      #login {
        height:100%;
        width: 380px;
        overflow: auto;
        padding: 15vh 30px 30px 30px;
        box-shadow:0px 0px 10px 10px rgba(0,0,0,0.1);
        opacity:0.98;
        float:left;
        max-height: calc( 85vh - 30px );
      }";
    }

    if ( 'left2' === BeautyPlus_Helpers::clean( $settings['position'], 'center' )) {
      $css .= "
      #login {
        width: 320px;
        overflow: auto;
        padding: 70px 32px 50px 32px;
        box-shadow: 0 4px 25px 0 rgba(0, 0, 0, 0.1) ;
        opacity:0.98;
        border-radius:15px;
        position: absolute;
        top: 50%;
        left: 10%;
        transform: translate(-0%, -50%);
        max-height: 70vh;

      }";
    }

    if ( 'right' === BeautyPlus_Helpers::clean( $settings['position'], 'center' )) {
      $css .= "
      #login {
        height:100%;
        width: 380px;
        overflow: auto;
        padding: 15vh 50px 30px 50px;
        box-shadow:0px 0px 10px 10px rgba(0,0,0,0.1);
        opacity:0.97;
        float:right;
        max-height: calc( 85vh - 30px );
      }";
    }

    if ( 'right2' === BeautyPlus_Helpers::clean( $settings['position'], 'center' )) {
      $css .= "
      #login {
        width: 320px;
        overflow: auto;
        padding: 70px 32px 50px 32px;
        box-shadow: 0 5px 10px 0px rgba(0, 0, 0, 0.1);
        opacity:0.97;
        border-radius:15px;
        position: absolute;
        top: 50%;
        right: 10%;
        transform: translate(-0%, -50%);
        max-height: 70vh;

      }";
    }

    if ( 'center' === BeautyPlus_Helpers::clean( $settings['position'], 'center' )) {
      $css .='
      #login {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 300px;
        border-radius: 12px;
        overflow: auto;
        max-height: 70vh;
        padding: 70px 32px 30px 32px;
        box-shadow: 0 5px 10px 0px rgba(0, 0, 0, 0.21);
      }';
    }


    if (!empty($settings['background'])) {
      $background = wp_get_attachment_image_src( intval($settings['background']), 'full' );

      $css  .= '
      body.login {
        background-image: url('. esc_url_raw( $background[0] ). ');
        background-repeat: no-repeat;
        background-size: cover;
        background-position: center;
        overflow:hidden;
      }';
    };

    $css .='
    ::-webkit-scrollbar-thumb {
      background: '. esc_attr(wc_hex_darker(wc_format_hex(BeautyPlus_Helpers::clean( $settings['box'], '#fff' )), 5)). ';
      -webkit-border-radius: 100px;
    }

    #login, #login a, #login input, .dashicons-visibility, ::placeholder {
      color: '. esc_attr(BeautyPlus_Helpers::clean( $settings['text'], '#555555' )). ' !important;
    }

    #login, #loginform, #login_error, .login .message, .login .success {
      background: '. esc_attr(BeautyPlus_Helpers::clean( $settings['box'], '#fff' )). ' !important;
      border:0px;
    }

    input[type=text], input[type=password],input[type=email] {
      background: transparent !important;
      border:0px;
      border-radius:0px;
      padding-left:0px !important;
      padding-bottom: 11px !important;
      border-bottom: 4px solid '. esc_attr(BeautyPlus_Helpers::clean( $settings['text'], '#555555' )). ';
    }

    #login #wp-submit {
      width: 100%;
      margin-top: 50px;
      padding: 8px;
      border-radius: 22px;
      background: '. esc_attr(BeautyPlus_Helpers::clean( $settings['button'], '#555555' )). ' !important;
      color: '. esc_attr(BeautyPlus_Helpers::clean( $settings['buttontext'], '#fff' )). ' !important;
      border:0px;
    }

    input[type=checkbox] {
      border-radius: 50%;
      border:0px;
      opacity:0.95;
      background: '. esc_attr(BeautyPlus_Helpers::clean( $settings['box'], '#fff' )). ' !important;
      border: 1px solid '. esc_attr(BeautyPlus_Helpers::clean( $settings['text'], '#555555' )). ';
    }

    @media only screen and (max-width: 650px) {
      .login, #login {
        box-shadow:none;
        padding-left:inherit;
        padding-right:inherit;
        left:inherit;
        right:inherit;
        position:relative;
        top:inherit;
        transform:none;
        border-radius:0px;
        padding-top: 5vh;
        max-height: unset !important;
        background: '. esc_attr(BeautyPlus_Helpers::clean( $settings['box'], '#fff' )). ' !important;
      }

      body.login {
        overflow: auto;
      }
    }';

    wp_add_inline_style('beautyplus-reactors-login', $css);

  }

  public static function footer() {
    echo "<script>
    if (document.getElementById('user_login')) { document.getElementById('user_login').setAttribute('placeholder','".esc_html__('Username', 'beautyplus')."'); }
    if (document.getElementById('user_pass')) { document.getElementById('user_pass').setAttribute('placeholder','".esc_html__('Password', 'beautyplus')."'); }
    if (document.getElementById('user_email')) { document.getElementById('user_email').setAttribute('placeholder','".esc_html__('Email', 'beautyplus')."'); }
    </script>";
  }

  public static function deactivate() {
    // Remove options
    delete_option('beautyplus_reactors-login-settings');

  }
}