<p class="text-center"><span class="js-img-scroll" data-src="<?= $iconsDir; ?>/favicon/favicon.svg"></span></p>

<?php
$lang = get_bloginfo('language');
$langShort = 'en';

if ($lang == 'ru-RU') {
  $langShort = 'ru';
} else if ($lang == 'uk') {
  $langShort = 'ua';
}

$reviews = [
  'ru' => [
    [
      'name' => 'Елена',
      'text' => 'Очень понравилась утренняя тренировка "на четвереньках", очень хорошо тянется всё тело, но я неустойчива в упражнении, когда на одной ноге и ноги подтягиваются, держалась за стул, но очень понравилось по ощущениям. Спасибо.'
    ],
    [
      'name' => 'Татьяна',
      'text' => 'Делала замер до начала занятий и после месяца тренировок по вашей программе, с помощью весов/прибора, измеряющего процент жира / мышц в руках, ногах, в общем в теле. Снижение жира на 2% и в руках, и в ногах, и в теле. Снижение болей в пояснице при постоянных тренировках. Стрейчинг. В общем, очень эффективный вид тренировок, если настойчиво делать 3 раза в неделю.'
    ],
    [
      'name' => 'Оксана',
      'text' => 'Нелечка, это чудесная тренировка 😊. Проанализировала, как и сказали. Основная часть прекрасно, систематично, увеличивая амплитуду скруток и раскруток, прорабатывает позвоночник. Очень хорошо раскрылись позвонки в пояснице и выше. И такая необходимая релаксация-медитация 🙏🙏🙏. Спасибо.'
    ],
  ],

  'en' => [
    [
      'name' => 'Elena',
      'text' => 'I really enjoyed the morning "on all fours" workout. It stretches the whole body so well! I’m still a bit unstable in the single-leg exercise, so I held onto a chair — but it felt amazing. Thank you!'
    ],
    [
      'name' => 'Tatiana',
      'text' => 'I measured my body before starting and again after a month of your program using a body composition scale. Fat percentage dropped by 2% in arms, legs, and overall. My lower back pain also decreased. Stretching is very effective if done three times a week consistently.'
    ],
    [
      'name' => 'Oksana',
      'text' => 'Nelechka, this workout is wonderful 😊. The main part systematically works the spine, increasing range of motion in twists. My lower back feels so much freer now. And the relaxation-meditation at the end is just what I needed 🙏🙏🙏. Thank you.'
    ],
  ],

  'ua' => [
    [
      'name' => 'Олена',
      'text' => 'Дуже сподобалося ранкове тренування "на четвереньках", чудово тягнеться все тіло. У вправі на одній нозі я ще не дуже стійка, трималася за стілець, але відчуття просто чудові. Дякую!'
    ],
    [
      'name' => 'Тетяна',
      'text' => 'Робила заміри до початку занять і після місяця тренувань за вашою програмою — за допомогою ваг, що вимірюють відсоток жиру та м’язів у руках, ногах і загалом по тілу. Зменшення жиру на 2% у всіх частинах тіла. Менше болить поперек при регулярних тренуваннях. Стретчинг — дуже ефективний вид занять, якщо наполегливо робити тричі на тиждень.'
    ],
    [
      'name' => 'Оксана',
      'text' => 'Нелічко, це чудове тренування 😊. Проаналізувала, як ви радили. Основна частина чудово, систематично пропрацьовує хребет, збільшуючи амплітуду скруток і розкруток. Відчуваю, як добре розкрилися хребці в попереку. І така необхідна релаксація-медитація наприкінці 🙏🙏🙏. Дякую!'
    ],
  ],
];

$reviewTitle = [
  'en' => 'Reviews',
  'ru' => 'Отзывы',
  'ua' => 'Відгуки'
];

$current_reviews = $reviews[$langShort] ?? $reviews['en']; ?>

<h3><?= $reviewTitle[$langShort]; ?></h3>

<div class="reviews js-carousel-simple">
  <?php foreach ($current_reviews as $review): ?>
    <div class="reviews__item">
      <p><?= esc_html($review['text']); ?></p>

      <div class="reviews__name"><?= esc_html($review['name']); ?></div>
    </div>
  <?php endforeach; ?>
</div>