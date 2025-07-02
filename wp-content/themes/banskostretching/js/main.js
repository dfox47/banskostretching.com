var $ = jQuery

$(window).bind('load', function() {
  $('.js-carousel-simple').owlCarousel({
    // autoplay:           true,
    // autoplaySpeed:      1000,
    // autoplayTimeout:    5000,
    dots:               true,
    items:              1,
    loop:               true,
    margin:             0,
    nav:                true,
    navText:            ['', '']
  })
})