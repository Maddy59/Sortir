function initAutocomplete() {
  var geocoder = new google.maps.Geocoder()

  var dataLatLng = document.querySelector('.mapWrapper')

  const lat = parseFloat(dataLatLng.dataset.lat)
  const lng = parseFloat(dataLatLng.dataset.lng)
  console.log(lat)

  const LatLng = new google.maps.LatLng(lat, lng)

  const map = new google.maps.Map(document.getElementById('map'), {
    center: LatLng,
    zoom: 16,
    mapTypeId: 'roadmap',
  })

  var marker = new google.maps.Marker({
    position: LatLng,
    map,
    title: 'Lieu de la sortie.',
  })

  const setAdress = (LatLng) => {
    geocoder.geocode(
      {
        latLng: LatLng,
      },
      function (results, status) {
        if (status == google.maps.GeocoderStatus.OK) {
          if (results[0]) {
            const adress = results[0].address_components

            document.getElementById('modifier_sortie_form_adresse').value =
              results[0].formatted_address

            let street_number = adress.find((i) => i.types[0] === 'street_number') || ''
            !!street_number && (street_number = street_number.short_name)
            let route = adress.find((i) => i.types[0] === 'route') || ''
            !!route && (route = route.short_name)

            const street = street_number + ' ' + route
            const zip = adress.find((i) => i.types[0] === 'postal_code').short_name
            const city = adress.find((i) => i.types[0] === 'locality').short_name

            document.getElementById('modifier_sortie_form_lieu_rue').value = street
            document.getElementById(
              'modifier_sortie_form_lieu_latitude'
            ).value = LatLng.lat()
            document.getElementById(
              'modifier_sortie_form_lieu_longitude'
            ).value = LatLng.lng()
            document.getElementById('modifier_sortie_form_lieu_ville_nom').value = city
            document.getElementById(
              'modifier_sortie_form_lieu_ville_codePostal'
            ).value = zip
          }
        }
      }
    )
  }

  setAdress(LatLng)

  map.addListener('click', function (e) {
    setAdress(e.latLng)
    marker.setPosition(e.latLng, map)
  })

  const input = document.getElementById('modifier_sortie_form_adresse')
  const searchBox = new google.maps.places.SearchBox(input)

  map.addListener('bounds_changed', () => {
    searchBox.setBounds(map.getBounds())
  })

  searchBox.addListener('places_changed', () => {
    const places = searchBox.getPlaces()

    if (places.length == 0) {
      return
    }

    const bounds = new google.maps.LatLngBounds()

    places.forEach((place) => {
      if (!place.geometry || !place.geometry.location) {
        console.log('Returned place contains no geometry')
        return
      }
      marker.setPosition(place.geometry.location, map)

      setAdress(place.geometry.location)

      if (place.geometry.viewport) {
        bounds.union(place.geometry.viewport)
      } else {
        bounds.extend(place.geometry.location)
      }
    })
    map.fitBounds(bounds)
  })
}
