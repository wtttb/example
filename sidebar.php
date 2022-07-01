<aside class="sidebar layout-right" id="sidebar">
  <div class="sidebar__inner">
    <?php
    if (is_single()) {
      dynamic_sidebar('single-sidebar');
    }
    ?>
  </div>
</aside>