const sidebar = () => {
  const sidebarToggle = document.querySelectorAll('.js-sidebar-toggle')

  if (!sidebarToggle) return

  sidebarToggle.forEach(btn => {
    btn.addEventListener('click', function (e) {
      e.preventDefault()

      document.querySelector('.js-sidebar')?.classList.toggle('active')
      document.documentElement.classList.toggle('overflow-hidden')
    })
  })
}

sidebar()