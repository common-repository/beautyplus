<?php
if (! defined('ABSPATH')) {
  exit; // Exit if accessed directly
}
?>

<?php echo BeautyPlus_View::run('header-in'); ?>

<div class="beautyplus-title inbrowser">
  <h3><?php esc_html_e('Reply', 'beautyplus'); ?></h3>
</div>

<form action="" method="POST">
  <div class="container" id="beautyplus-comments--new">
    <div class="row">
      <?php echo wp_kses_post($comment->comment_content); ?>
    </div>
    <div class="row __A__Info">
      <br>
      <?php esc_html( $comment->comment_author ); ?> &mdash;
      <?php echo date("d F Y, H:i", strtotime($comment->comment_date)); ?>
    </div>
    <div class="row">
      <div class="__A__Label"><hr></div>
      <div class="__A__Label"><strong><?php esc_html_e('Your Reply', 'beautyplus'); ?></strong><br></div>
      <?php wp_editor( "", "reply", $settings = array( 'teeny' => true, 'tinymce'=>false, 'quicktags' => array( 'buttons' => 'strong,em,del,ul,ol,li,close' ), 'media_buttons' => false ) ); ?>
    </div>
    <div class="row">
      <div class="__A__Label"></div>
      <?php if ("1" === $comment->comment_approved) { ?>
        <input class="btn btn-primary" type="submit" name="submit" value="<?php esc_attr_e('Submit reply', 'beautyplus'); ?>">
      <?php } else { ?>
        <input class="btn btn-primary" type="submit" name="submit" value="<?php esc_attr_e('Approve comment and submit reply', 'beautyplus'); ?>">
      <?php }  ?>
    </div>
  </div>
</form>

<p>&nbsp;</p>
