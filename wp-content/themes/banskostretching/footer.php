<?php // vars
$themeDir   = '/wp-content/themes/banskostretching';
$i          = $themeDir . '/i';
$iconsDir   = $i . '/icons'; ?>

<?php wp_footer(); ?>

<?php if (is_front_page()) {
  // reviews
  include_once('template-parts/reviews.php');

  // instagram
  include_once('template-parts/instagram_widget.php');
} ?>

<footer class="footer">
  <div class="wrap">
    <ul class="footer_links">
      <li class="footer_links__item">
        <a class="footer_links__link" href="//www.instagram.com/stretching_nelly/" target="_blank" title="instagram"><span class="footer_links__img js-img-scroll" data-src="<?= $iconsDir; ?>/instagram.svg" title="instagram"></span></a>
      </li>

      <li>
        <a class="footer_links__link" href="//t.me/stretchingnelya" target="_blank" title="telegram"><span class="footer_links__img js-img-scroll" data-src="<?= $iconsDir; ?>/telegram.svg" title="telegram"></span></a>
      </li>
    </ul>

    <div class="copyright">
      <div class="copyright__desc"></div>

      <div class="copyright__date">© 2022 - <?= (date('Y')); ?></div>
    </div>
  </div>
</footer>

<?php get_sidebar(); ?>

<script src="<?= $themeDir; ?>/all.min.js?v=<?= (date('YmdHis')); ?>"></script>

</body>

</html>