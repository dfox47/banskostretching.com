const xhr = new XMLHttpRequest()

xhr.open('GET', 'api.php', true)

xhr.onload = () => {
	if (xhr.status >= 200 && xhr.status < 300) {
		const data = JSON.parse(xhr.responseText)

		if (!data.response.entities.length) return

		const $addressCity          = document.querySelector('.js-address-city')
		const $addressLine1         = document.querySelector('.js-address-line1')
		const $addressRegion        = document.querySelector('.js-address-region')
		const $addressPostal        = document.querySelector('.js-address-postal')
		const $description          = document.querySelector('.js-description')
		const $map                  = document.querySelector('.js-map')
		const $mapClassList         = $map.className
		const $mapIframe            = document.createElement('iframe')
		const $phone                = document.querySelector('.js-phone')
		const $title                = document.querySelector('.js-title')
		const $heroLinkPrimary      = document.querySelector('.js-hero-link-primary')
		const $heroLinkSecondary    = document.querySelector('.js-hero-link-secondary')

		// copy classes from MAP span to iframe
		if ($mapClassList) {
			$mapClassList.split(' ').forEach((c) => {
				$mapIframe.classList.add(c)
			})
		}

		data.response.entities.forEach(e => {
			// meta title
			if (e.c_metaTitle) {
				const metaTitle = document.createElement('meta')
				metaTitle.setAttribute('name', 'title')
				metaTitle.setAttribute('content', e.c_metaTitle)

				const metaTitleOg = document.createElement('meta')
				metaTitleOg.setAttribute('property', 'og:title')
				metaTitleOg.setAttribute('content', 'Shop ALDI Grocery Store ' + e.address.city + ', ' + e.address.region + ' | ' + e.address.line1)
				document.head.appendChild(metaTitleOg)
			}

			// meta description
			if (e.c_metaDescription) {
				const metaDescription = document.createElement('meta')
				metaDescription.setAttribute('name', 'description')
				metaDescription.setAttribute('content', e.c_metaDescription)
				document.head.appendChild(metaDescription)

				const metaDescriptionOg = document.createElement('meta')
				metaDescriptionOg.setAttribute('property', 'og:description')
				metaDescriptionOg.setAttribute('content', e.c_metaDescription)
				document.head.appendChild(metaDescriptionOg)
			}

			if ($description !== null) {
				$description.textContent = e.description
			}

			// h1
			if ($title !== null) {
				$title.textContent = e.c_locationName
			}

			// address line1
			if ($addressLine1 !== null) {
				$addressLine1.textContent = e.address.line1
			}

			// address city
			if ($addressCity !== null) {
				$addressCity.textContent = e.address.city
			}

			// address region
			if ($addressRegion !== null) {
				$addressRegion.textContent = e.address.region
			}

			// address postal code
			if ($addressPostal !== null) {
				$addressPostal.textContent = e.address.postalCode
			}

			// phone
			if ($phone !== null) {
				$phone.textContent = formatPhoneNumber(e.c_storeLocalPhoneNumber, '(###) ###-####')
			}

			// hero link primary
			if ($heroLinkPrimary !== null) {
				$heroLinkPrimary.href               = e.c_heroPrimaryCTA.link
				$heroLinkPrimary.textContent        = e.c_heroPrimaryCTA.label
			}

			// hero link secondary
			if ($heroLinkSecondary !== null) {
				$heroLinkSecondary.href             = e.c_heroSecondaryCTA.link
				$heroLinkSecondary.textContent      = e.c_heroSecondaryCTA.label
			}

			if (e.c_latitude && e.c_longitude && $map !== null) {
				$mapIframe.src = '//maps.google.com/maps?q=' + e.c_latitude +',' + e.c_longitude + '&hl=en&z=17&output=embed'

				$mapIframe.setAttribute('referrerpolicy', 'no-referrer-when-downgrade')

				// append iframe
				$map.after($mapIframe)

				// remove span
				$map.remove()
			}



			// TODO : remove it
			// all DATA
			document.getElementById('data').textContent = JSON.stringify(e, null, 2)
		})
	}
	else {
		console.error('Request failed with status:', xhr.status)
	}
}

xhr.onerror = () => {
	console.error('Request failed')
}

xhr.send()

function formatPhoneNumber(number, template) {
	const digits = number.toString().replace(/\D/g, '')
	let formattedNumber = ''
	let digitIndex = 0

	for (let i = 0; i < template.length; i++) {
		if (template[i] === '#') {
			if (digitIndex < digits.length) {
				formattedNumber += digits[digitIndex]
				digitIndex++
			}
			else {
				break; // No more digits to append
			}
		}
		else {
			formattedNumber += template[i]
		}
	}

	return formattedNumber
}