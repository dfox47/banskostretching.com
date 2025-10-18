<?php
$lang       = get_bloginfo('language');
$langShort  = 'en';

if ($lang == 'ru-RU') {
  $langShort = 'ru';
} else if ($lang == 'uk') {
  $langShort = 'ua';
} ?>

<div class="lang_change">
  <div class="lang_change__link lang_change__link--current lang_change__link--<?= $langShort; ?>"><?= $langShort; ?></div>

  <ul class="lang_change__list">
    <?php if ($langShort !== 'ru') { ?>
      <li class="lang_change__item"><a class="lang_change__link lang_change__link--ru" href="/">ru</a></li>
    <?php }
    if ($langShort !== 'en') { ?>
      <li class="lang_change__item"><a class="lang_change__link lang_change__link--en" href="/en">en</a></li>
    <?php }
    if ($langShort !== 'ua') { ?>
      <li class="lang_change__item"><a class="lang_change__link lang_change__link--ua" href="/ua">ua</a></li>
    <?php } ?>
  </ul>
</div>