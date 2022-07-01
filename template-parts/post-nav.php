<?php
$next = get_next_post();
$prev = get_previous_post();
if ($next || $prev) :
?>
  <div class="wpb-posts-nav">
    <div>
      <?php if (!empty($prev)) : ?>
        <a href="<?php echo get_permalink($prev); ?>">
          <div>
            <div class="wpb-posts-nav__thumbnail wpb-posts-nav__prev">
              <?php echo get_the_post_thumbnail($prev, 'full'); ?>
            </div>
          </div>
          <div>
            <strong>
              <svg viewBox="0 0 24 24" width="24" height="24">
                <path d="M13.775,18.707,8.482,13.414a2,2,0,0,1,0-2.828l5.293-5.293,1.414,1.414L9.9,12l5.293,5.293Z" />
              </svg>
              <?php _e('Previous article', 'textdomain') ?>
            </strong>
            <h4><?php echo get_the_title($prev); ?></h4>
          </div>
        </a>
      <?php endif; ?>
    </div>
    <div>
      <?php if (!empty($next)) : ?>
        <a href="<?php echo get_permalink($next); ?>">
          <div>
            <strong>
              <?php _e('Next article', 'textdomain') ?>
              <svg viewBox="0 0 24 24" width="24" height="24">
                <path d="M10.811,18.707,9.4,17.293,14.689,12,9.4,6.707l1.415-1.414L16.1,10.586a2,2,0,0,1,0,2.828Z" />
              </svg>
            </strong>
            <h4><?php echo get_the_title($next); ?></h4>
          </div>
          <div>
            <div class="wpb-posts-nav__thumbnail wpb-posts-nav__next">
              <?php echo get_the_post_thumbnail($next, 'full'); ?>
            </div>
          </div>
        </a>
      <?php endif; ?>
    </div>
  </div>
<?php
endif;
