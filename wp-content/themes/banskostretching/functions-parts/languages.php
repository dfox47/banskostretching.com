<?php function load_json_translations($lang = 'ru')
{
  static $translations = [];

  // Cache translations in memory
  if (!isset($translations[$lang])) {
    $file = get_template_directory() . "/translations/{$lang}.json";

    if (file_exists($file)) {
      $json = file_get_contents($file);
      $translations[$lang] = json_decode($json, true);
    } else {
      $translations[$lang] = [];
    }
  }

  return $translations[$lang];
}

function __t($key)
{
  $current_lang = get_locale();

  // Map WordPress locales to your file names
  $map = [
    'en_US' => 'en',
    'ru_RU' => 'ru',
    'uk' => 'ua'
  ];

  $lang = isset($map[$current_lang]) ? $map[$current_lang] : 'ru';
  $translations = load_json_translations($lang);

  return $translations[$key] ?? $key;
}
