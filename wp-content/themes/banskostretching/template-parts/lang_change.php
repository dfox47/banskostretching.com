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
      <li class="lang_change__item"><a class="lang_change__link lang_change__link--ru js-lang-change-link" href="/" data-lang="ru">ru</a></li>
    <?php }
    if ($langShort !== 'en') { ?>
      <li class="lang_change__item"><a class="lang_change__link lang_change__link--en js-lang-change-link" href="/en" data-lang="en">en</a></li>
    <?php }
    if ($langShort !== 'ua') { ?>
      <li class="lang_change__item"><a class="lang_change__link lang_change__link--ua js-lang-change-link" href="/ua" data-lang="ua">ua</a></li>
    <?php } ?>
  </ul>
</div>