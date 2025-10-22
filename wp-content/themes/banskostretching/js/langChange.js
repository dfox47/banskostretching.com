const langStore = () => {
  const langChangeLinks = document.querySelectorAll('.js-lang-change-link')
  const cachedLang = localStorage.getItem('siteLang')
  const currentPath = window.location.pathname

  const pathParts = currentPath.split('/').filter(Boolean)
  const currentLang = pathParts[0] && ['en', 'bg'].includes(pathParts[0]) ? pathParts[0] : 'ru'

  if (cachedLang && cachedLang !== currentLang) {
    let newPath

    if (cachedLang === 'ru') {
      newPath = '/' + pathParts.slice(1).join('/')
    } else {
      newPath = '/' + cachedLang + '/' + pathParts.slice(currentLang === 'ru' ? 0 : 1).join('/')
    }

    newPath = newPath.replace(/\/+/g, '/')

    if (newPath !== currentPath) {
      window.location.href = newPath
      return
    }
  }

  if (!langChangeLinks.length) return

  langChangeLinks.forEach(link => {
    link.addEventListener('click', function () {
      const lang = this.dataset.lang

      if (!lang) return

      localStorage.setItem('siteLang', lang)
    })
  })
}

document.addEventListener('DOMContentLoaded', langStore)
