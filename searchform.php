<form class="search__form" method="get" action="<?= esc_url(home_url()); ?>">
  <input type="search" class="search__form--input" name="s" value="" placeholder="<?= esc_attr(get_theme_mod('etb_search_placeholder', '搜索词')); ?>">
  <input type="submit" hidden value="">
  <button class="heading__btn seatoggle">
    <svg width="24" height="24" stroke-width="1.5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
      <path d="M6.75827 17.2426L12.0009 12M17.2435 6.75736L12.0009 12M12.0009 12L6.75827 6.75736M12.0009 12L17.2435 17.2426" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" />
    </svg>
  </button>
</form>