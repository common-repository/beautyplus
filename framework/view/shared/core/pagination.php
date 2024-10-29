<?php
if (! defined('ABSPATH')) {
  exit; // Exit if accessed directly
} ?>
<?php if (0 === $page) {
  $page = 1;
}
if (!isset($url)) {
  $url = filter_input(INPUT_SERVER, 'REQUEST_URI');
}
?>
<?php if ( 0 < intval ( $count/$per_page ) ) {  ?>
  <nav aria-label="Page navigation">
    <ul class="__A__Pagination pagination justify-content-center">
      <li class="page-item<?php if ($page === 0 OR $page === 1) { echo " disabled"; }?>">
        <a class="page-link" href="<?php echo remove_query_arg( 'pg', $url); ?>&pg=<?php echo intval($page)-1;?>" aria-label="Previous">
          <span aria-hidden="true">&laquo;</span>
          <span class="sr-only">Previous</span>
        </a>
      </li>

      <li class="page-item d-flex align-items-center"><a class="page-link" href="<?php echo remove_query_arg( 'pg', $url); ?>&pg=1"><?php echo esc_attr($page);  ?></a>  &nbsp;/&nbsp; <a class="page-link" href="<?php echo remove_query_arg( 'pg', $url); ?>&pg=<?php echo ceil($count/$per_page); ?>"><?php echo ceil($count/$per_page); ?></a> </li>

      <li class="page-item<?php if ( $page === ceil($count/$per_page) ) { echo " disabled"; }?>">
        <a class="page-link" href="<?php echo remove_query_arg( 'pg', $url)?>&pg=<?php echo intval($page)+1;?>" aria-label="Next">
          <span aria-hidden="true">&raquo;</span>
          <span class="sr-only">Next</span>
        </a>
      </li>
    </ul>
  </nav>
<?php }?>
