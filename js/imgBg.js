const imgBg = () => {
	document.querySelectorAll('.js-img-bg').forEach((e) => {
		if (window.pageYOffset + window.innerHeight > e.offsetTop) {
			e.classList.remove('js-img-bg')
			const imgSrc = e.dataset.src
			if (imgSrc) e.style.backgroundImage = 'url(' + imgSrc + ')'
		}
	})
}

imgBg()

window.addEventListener('orientationChange', imgBg)
window.addEventListener('resize', imgBg)
window.addEventListener('scroll', imgBg)