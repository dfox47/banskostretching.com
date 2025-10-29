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

// Sidebar toggle
$(document).ready(function() {
  // Open sidebar
  $('.js-sidebar-open').on('click', function(e) {
    e.preventDefault()
    $('.js-sidebar').addClass('is-open')
    $('html').addClass('overflow-hidden')
  })

  // Close sidebar
  $('.js-sidebar-close').on('click', function(e) {
    e.preventDefault()
    $('.js-sidebar').removeClass('is-open')
    $('html').removeClass('overflow-hidden')
  })

  $('.js-header-user-menu-avatar').on('click', function(e) {
    e.preventDefault()
    $('.js-header-user-menu-dropdown').toggleClass('is-active').fadeToggle(300)
  })

  $('.js-header-user-menu-dropdown').on('click', function(e) {
    $(this).toggleClass('is-active').fadeToggle(300)
  })
})
