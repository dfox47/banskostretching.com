<!DOCTYPE html>

<html lang="ru">

<head>
	<title>Bansko stretching by Nelya</title>

	<?php include_once "template-parts/favicon.php"; ?>

	<meta charset="utf-8">
	<meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible">
	<meta content="Default page" name="description">
	<meta content="width=device-width, initial-scale=1" name="viewport">

	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<!--	<link href="https://fonts.googleapis.com/css2?family=Lilita+One&display=swap" rel="stylesheet">-->
	<link href="https://fonts.googleapis.com/css2?family=Lilita+One&family=Ubuntu+Condensed&display=swap" rel="stylesheet">

	<link rel="stylesheet" href="/styles.min.css?v=<?= (date("YmdHis")); ?>">
</head>

<body>
<header class="header"></header>

<div class="hero js-img-bg" data-src="/i/hero.jpg"></div>

<div class="wrap">
	<h1>Bansko stretching</h1>

	<div class="greetings">
		<p>Привет!<br>Меня зовут Неля и я готова стать твоим проводником в прекрасный мир стретчинга</p>

		<p>
			<span class="js-img-scroll" data-src="/i/icons/favicon/favicon.svg"></span>
		</p>
	</div>
</div>

<footer class="footer">
	<div class="wrap">
		<ul class="footer_links">
			<li class="footer_links__item">
				<a class="footer_links__link" href="//www.instagram.com/banskostretching/" target="_blank" title="instagram"><span class="footer_links__img js-img-scroll" data-src="/i/icons/instagram.svg" title="instagram"></span></a>
			</li>

			<li>
				<a class="footer_links__link" href="//t.me/stretchingnelya" target="_blank" title="telegram"><span class="footer_links__img js-img-scroll" data-src="/i/icons/telegram.svg" title="telegram"></span></a>
			</li>
		</ul>

		<div class="copyright">
			<div class="copyright__desc"></div>

			<div class="copyright__date">© 2022 - <?= (date("Y")); ?></div>
		</div>
	</div>
</footer>

<script src="/all.min.js?v=<?= (date("YmdHis")); ?>"></script>
</body>
</html>