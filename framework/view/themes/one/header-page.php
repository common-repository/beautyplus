<?php
if (! defined('ABSPATH')) {
  exit; // Exit if accessed directly
}
?>

<?php if (1 === $type) { ?>
  <div class="beautyplus-title">
    <div class="__A__GP">
      <h3><?php echo esc_html($title) ?></h3>
      <div class="beautyplus-title--description"><?php echo esc_html($description) ?> </div>
      <?php if (!isset($no_button)) { ?>
        <div class="beautyplus-title--buttons" class="float-sm-right"><?php echo wp_kses_post($buttons) ?></div>
      <?php } ?>
      <div class="__A__Clear_Both"></div>
    </div>
  </div>
<?php } ?>
