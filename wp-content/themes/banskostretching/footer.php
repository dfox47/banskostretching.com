<?php // vars
$themeDir   = '/wp-content/themes/banskostretching';
$i          = $themeDir . '/i';
$iconsDir   = $i . '/icons'; ?>

<?php wp_footer(); ?>

<!--<script src="--><? //= $js;
                    ?><!--/imgBg.js"></script>-->
<!--<script src="--><? //= $js;
                    ?><!--/imgScroll.js"></script>-->
<!--<script src="--><? //= $js;
                    ?><!--/menuToggle.js"></script>-->
<!--<script src="--><? //= $js;
                    ?><!--/owl.carousel.min.js"></script>-->
<!--<script src="--><? //= $js;
                    ?><!--/phone.js"></script>-->

<?php // should be last
?>
<!--<script defer src="--><? //= $js;
                          ?><!--/main.js?v--><? //= (date("YmdH"));
                                              ?><!--"></script>-->

<footer class="footer">
  <div class="wrap">
    <ul class="footer_links">
      <li class="footer_links__item">
        <a class="footer_links__link" href="//www.instagram.com/banskostretching/" target="_blank" title="instagram"><span class="footer_links__img js-img-scroll" data-src="<?= $iconsDir; ?>/instagram.svg" title="instagram"></span></a>
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

<script src="<?= $themeDir; ?>/all.min.js?v=<?= (date('YmdH')); ?>"></script>

</body>

</html>