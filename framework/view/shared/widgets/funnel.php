<?php
if (! defined('ABSPATH')) {
  exit; // Exit if accessed directly
}
?>

<div class="chart-container __A__Chart_Container">
  <div id="__A__Chart_Conversation">
  </div>
</div>

<script>
jQuery(document).ready(function() {
  "use strict";

  /* CONVERSION GRAPH */

  var funnel_data = {
    labels: ['<?php esc_html_e('Home Page', 'beautyplus'); ?>', '<?php esc_html_e('Product Page', 'beautyplus'); ?>', '<?php esc_html_e('Add to Cart', 'beautyplus'); ?>', '<?php esc_html_e('Checkout', 'beautyplus'); ?>', '<?php esc_html_e('Buy', 'beautyplus'); ?>'],
    <?php if ('dark' === BeautyPlus::$theme) {?>
    colors: ['#777',  '#3f3f3f'],
    <?php } else {?>
    colors: ['#54c8a7',  '#e4f2af'],
    colors: ['#8FC0DA',  '#efefef'],
    <?php }?>
    values: [<?php echo implode(',', $data['funnel']); ?>]
  }

  var graph = new FunnelGraph({
    container: '#__A__Chart_Conversation',
    gradientDirection: 'horizontal',
    data: funnel_data,
    displayPercent: true,
    direction: 'horizontal',
    height: 180
  });
  graph.draw();
  })
</script>
