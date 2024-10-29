<?php

/**
* BeautyPlus Comments
*
* Manage comments
*
* @since      1.0.0
* @package    BeautyPlus
* @subpackage BeautyPlus/framework
* @author     Rajthemes
*/

if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

class BeautyPlus_Comments extends BeautyPlus {

  /**
  * Starts everything
  *
  * @return void
  */

  public static function run() {

    wp_enqueue_script("beautyplus-comments",  BeautyPlus_Public . "js/beautyplus-comments.js");

    $i18n = array(
      'approve'   => esc_attr__('Approve', 'beautyplus'),
      'approved'   => esc_attr__('APPROVED', 'beautyplus'),
      'unapprove' => esc_attr__('Unapprove', 'beautyplus'),
      'unapproved' => esc_attr__('UNAPPROVED', 'beautyplus')
    );

    wp_localize_script('beautyplus-comments', 'BeautyPlusComments', $i18n);

    self::route();
  }

  /**
  * Router for sub pages
  *
  * @return void
  */

  private static function route() {

    switch (BeautyPlus_Helpers::get('action')) {

      case 'reply':
      self::reply();
      break;

      case 'hide':
      wp_die('<script>"use strict"; if (self!==top && window.parent.BeautyPlus_Window != null && window.parent.BeautyPlus_Window != undefined) {   window.parent.trigGlobal.slideReveal("hide"); }</script> ');
      break;

      default:
      self::index();
      break;
    }
  }


  /**
  * Main function
  *
  * @param  mixed $filter   array of filter
  * @return BeautyPlus_View
  */

  public static function index($filter = false) {


    $filter = self::filter($filter);

    $comment_status = $filter['status'];

    switch ( $mode = ( !empty($filter['mode']) ? absint($filter['mode']) : BeautyPlus::option('mode-beautyplus-comments', 1 ) ) ) {

      // 99: Woocommerce Native
      case 99:
      if (!BeautyPlus_Admin::is_full()) {
        BeautyPlus_Helpers::frame( admin_url( 'edit-comments.php' ) );
      } else {
        wp_redirect( admin_url( 'edit-comments.php' ) );
      }
      break;

      // 1-2: Standart
      case 1:
      case 2:
      case 95:

      if ( !in_array( $comment_status, array( 'all', '0', '1', 'spam', 'trash' ) ) ) {
        $comment_status = 'all';
      }

      $c_status = str_replace(array('all', '0','1'), array('total_comments', 'moderated', 'approved'), $comment_status);

      $filter['status'] = $comment_status;

      $comments = self::get_comments( $filter );

      $ids = array_map(function($e) {
        return is_object($e) ? $e->comment_ID : $e['comment_ID'];
      }, $comments);

      $sub_args = array( 'parent__in' => $ids);
      $_replies = self::get_comments( $sub_args );

      $replies  = array();

      if (! empty ( $_replies )) {
        foreach ( $_replies AS $comment ) {
          $replies[ $comment->comment_parent ][] = array(
            'comment_ID'      => $comment->comment_ID,
            'comment_author'  => $comment->comment_author,
            'comment_date'    => $comment->comment_date,
            'comment_content' => $comment->comment_content,
          );
        }
      }

      $comments_count = wp_count_comments();

      $stars = self::get_stars();

      unset($filter['offset']);

      $filter['count']        = true;
      $comments_count_display = self::get_comments($filter);

      if (95 === $mode) {
        echo BeautyPlus_View::run('comments/list-95', array('stars' => $stars, 'count' => $comments_count_display, 'counts' => $comments_count, 'iframe_url' => BeautyPlus_Helpers::get_submenu_url(BeautyPlus_Helpers::get('go')) ));
      } else {
        echo BeautyPlus_View::run('comments/list-'. $mode,  array( 'stars' => $stars, 'count' => $comments_count_display, 'counts' => $comments_count, 'per_page' => $filter['number'], 'comments' => $comments, 'replies' => $replies, 'search' => isset($filter['search']) ? $filter['search'] : '', 'ajax' =>   BeautyPlus_Helpers::is_ajax() ));
      }
      break;
    }


  }

  /**
  * Reply to a comment
  *
  * @return void
  */

  private static function reply() {

    $id      = absint( BeautyPlus_Helpers::get( 'id', 0 ) ) ;
    $post_id = absint( BeautyPlus_Helpers::get( 'post', 0 ) ) ;

    $post    = get_post( $post_id );

    if ( ! $post ) {
      wp_die( -1 );
    }

    if ( !current_user_can( 'edit_post', $post_id ) ) {
      wp_die( -1 );
    }

    if ( !$comment = get_comment( $id ) ) {
      wp_die( -1 );
    }

    if ( ! current_user_can( 'edit_comment', $comment->comment_ID ) ) {
      wp_die( -2 );
    }

    if ($_POST) {

      $user = wp_get_current_user();
      if ( $user->exists() ) {

        $commentdata = array(
          'comment_post_ID'      => $post->ID,
          'comment_author'       => wp_slash( $user->display_name ),
          'comment_author_email' => wp_slash( sanitize_email($user->user_email) ),
          'comment_author_url'   => wp_slash( esc_url_raw ($user->user_url) ),
          'comment_content'      => wp_kses_data(BeautyPlus_Helpers::post('reply','')),
          'comment_type'         => '',
          'comment_parent'       => $id,
          'user_id'              => wp_slash( $user->ID ),
        );

        $comment_id = wp_new_comment( $commentdata );

        if (1 !== $comment->comment_approved) {
          wp_set_comment_status( $id, 'approve' );
        }

        // Close sidebar
        echo '<script>"use strict"; window.parent.trigGlobal.slideReveal("hide");window.parent.location.href=window.parent.location.href;</script>';

      } else
      {
        wp_die( -2 );
      }
    }

    echo BeautyPlus_View::run('comments/reply',  array( 'comment' => $comment));
  }


  /**
  * Ajax router
  *
  * @return mixed
  */

  public static function ajax() {
    $do	=	 BeautyPlus_Helpers::post('do') ;
    $id	=	 BeautyPlus_Helpers::post('id') ;

    switch ($do) {

      // Bulk operations
      case 'bulk':

      if ('' === $id)
      {
        wp_die ( -1 );
      }

      $ids = explode ( ',', $id );

      if ( !is_array( $ids ) OR ( 0 === count( $ids ) ) )
      {
        wp_die ( -2);
      }

      $success = array();

      $ids = array_map('absint', $ids);

      foreach ($ids AS $id)
      {

        if ( !$comment = get_comment( $id ) ) {
          return BeautyPlus_Ajax::error("Invalid Comment");
          break;
        }

        if ( ! current_user_can( 'edit_comment', $comment->comment_ID ) ) {
          wp_die( -1 );
        }

        if ( 'approve' === BeautyPlus_Helpers::post( 'state' ) ) {
          $result    = wp_set_comment_status( $id, 'approve' );
          $success[] = array('id' => $id, 'state' => 'approve');
        }

        if ( 'unapprove' === BeautyPlus_Helpers::post( 'state' ) ) {
          $result    = wp_set_comment_status( $id, 'hold' );
          $success[] = array('id' => $id, 'state' => 'unapprove');
        }

        if ( 'trash' === BeautyPlus_Helpers::post( 'state' ) ) {
          $result    = wp_delete_comment( $id );
          $success[] = array('id' => $id, 'state' => 'trash');
        }

        if ( 'restore' === BeautyPlus_Helpers::post( 'state' ) ) {
          $result    = wp_untrash_comment( $id );
          $success[] = array('id' => $id, 'state' => 'trash');
        }

        if ( 'deleteforever' === BeautyPlus_Helpers::post( 'state' ) ) {
          $result    = wp_delete_comment( $id, 'true');
          $success[] = array('id' => $id, 'state' => 'trash');
        }

      }

      return BeautyPlus_Ajax::success('Comments status has been changed', array('id'=> $success));

      break;

      // Search
      case 'search':

      $filter['search'] = BeautyPlus_Helpers::post('q');
      $filter['status'] = BeautyPlus_Helpers::post('status', 'all');
      $filter['number'] = 999;

      if (!$filter['search']) {
        wp_die();
      }

      echo self::index($filter);
      wp_die();

      break;


      // Change status of comment
      case 'status':

      $result = false;
      $state  = BeautyPlus_Helpers::post('state');
      $id     = absint( $id );


      if ( !$comment = get_comment( $id ) ) {
        wp_die( -1 );
      }

      if ( ! current_user_can( 'edit_comment', $comment->comment_ID ) ) {
        wp_die( -1 );
      }


      if ('approve' === $state) {
        $undostate = 'approve';
        $result    = wp_set_comment_status( $id, 'approve' );
        $message   = esc_html__('Comment has been approved', 'beautyplus');
      }

      if ('unapprove' === $state) {
        $undostate = 'unapprove';
        $result    = wp_set_comment_status( $id, 'hold' );
        $message   = esc_html__('Comment has been unapproved', 'beautyplus');
      }

      if ('restore' === $state) {
        $undostate = 'restore';
        $result    = wp_untrash_comment( $id );
        $message   = esc_html__('Comment has been restored', 'beautyplus');
      }

      if ('spam' === $state) {
        $undostate = 'spam';
        $result    = wp_spam_comment( $id );
        $message   = esc_html__( 'Comment has been flagged as spam &mdash; ', 'beautyplus' ).'<a class="__A__AjaxButton" href="javascript:;" data-id="'.$id.'" data-do="status" data-state="unspam">'.esc_html__( 'Undo', 'beautyplus' ).'</a>';
      }

      if ('unspam' === $state) {
        $undostate = 'unspam';
        $result    = wp_unspam_comment( $id );
        $message   = esc_html__('Comment has been restored', 'beautyplus');
      }

      if ('untrash' === $state) {
        $undostate = 'untrash';
        $result    = wp_untrash_comment( $id );
        $message   = esc_html__('Comment has been restored', 'beautyplus');
      }

      if ('trash' === $state) {
        $undostate = 'trash';
        $result    = wp_delete_comment( $id );
        $message   = esc_html__( 'Comment moved to the trash &mdash; ', 'beautyplus' ).'<a class="__A__AjaxButton" href="javascript:;" data-id="'.$id.'" data-do="status" data-state="untrash">'.esc_html__( 'Undo', 'beautyplus' ).'</a>';
      }

      if ('forcedelete' === $state) {
        $undostate = 'forcedelete';
        $result    = wp_delete_comment( $id, 'true');
        $message   = esc_html__('Comment has been deleted forever', 'beautyplus');
      }


      if (TRUE === $result) {
        return BeautyPlus_Ajax::success(esc_html__('Comment deleted', 'beautyplus'), array('id'=> $id, 'message' => $message, 'state' => $undostate ));
      } else {
        return BeautyPlus_Ajax::error( esc_html__('Comment can not be deleted', 'beautyplus') );
      }

      break;
    }
  }


  /**
  * Query for get comments
  *
  * @param  array  $args Parameters for query
  * @return array
  */

  public static function get_comments ($args = array() ) {

    // The comment query
    $comments_query = new WP_Comment_Query;
    $comments       = $comments_query->query( $args );

    return $comments;

  }


  /**
  * Stars and comment count info
  *
  * @return array cnt = comment count, avarage = avarage of stars
  */

  private static function get_stars() {
    global $wpdb;

    $query = $wpdb->prepare("SELECT count(*) AS cnt, AVG(meta_value) as average FROM {$wpdb->prefix}commentmeta WHERE meta_key = %s", 'rating');
    $stars = $wpdb->get_results($query);

    return $stars;

  }


  /**
  * Prepare filter array for query
  *
  * @param  mixed $filter   array of filter or false
  * @return array           new filter array
  */

  public static function filter($filter = false) {

    if (!$filter) {
      $filter['status'] = "all";
      $filter['offset'] = 0;
      $filter['page']   = 1;
    }

    $filter['number'] = isset($filter['number']) ? absint($filter['number']) :  20;

    if (BeautyPlus_Helpers::get('go', null)) {
      $filter['mode'] = 95;
    }

    if ('' !== BeautyPlus_Helpers::get('status', '')) {
      $filter['status'] = BeautyPlus_Helpers::get('status', null);
    }

    if ('-1' === BeautyPlus_Helpers::get('status', '')) {
      $filter['status'] = 0;
    }


    if (BeautyPlus_Helpers::get('pg', null)) {
      $filter['offset'] = (intval( BeautyPlus_Helpers::get( 'pg', 1 )) - 1) *  $filter['number'];
    }

    if ('' !== BeautyPlus_Helpers::get('s', '')) {
      $filter['search'] = BeautyPlus_Helpers::get('s', '');
      $filter['number'] = 999;
    }

    if (BeautyPlus_Helpers::get('orderby'))
    {
      if (false !== strpos(BeautyPlus_Helpers::get('orderby',''), 'meta_'))
      {
        $filter['orderby']  = "meta_value_num";
        $filter['meta_key'] = sanitize_sql_orderby(str_replace ( 'meta_', '', BeautyPlus_Helpers::get('orderby','')));
      } else {
        $filter['orderby'] = sanitize_sql_orderby(BeautyPlus_Helpers::get('orderby',''));
      }

      $filter['order'] 	= 'ASC' === BeautyPlus_Helpers::get('order','ASC') ? 'ASC' : 'DESC';
    }


    if ( !in_array( $filter['status'], array( 'all', '0', '1', 'spam', 'trash' ) ) ) {
      $filter['status'] = 'all';
    }

    if ('trash' !==   $filter['status'] ) {

      // Get only main comments
      $filter['parent__in'] = array(0);
    }
    return $filter;
  }

}

?>