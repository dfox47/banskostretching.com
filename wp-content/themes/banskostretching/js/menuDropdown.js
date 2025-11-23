const menuDropdown = () => {
  const menuToggle = document.querySelectorAll('.js-menu-toggle')

  if (!menuToggle) return

  menuToggle.forEach(btn => {
    btn.addEventListener('click', function (e) {
      e.preventDefault()

      document.documentElement.classList.toggle('overflow-hidden')
      document.documentElement.classList.toggle('header-user-menu-active')
    })
  })
}

menuDropdown()