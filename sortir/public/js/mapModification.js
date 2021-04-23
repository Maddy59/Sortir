async function initAutocomplete() {
  var geocoder = new google.maps.Geocoder()
  var dataLatLng = document.querySelector('.mapWrapper')
  const lat = parseFloat(dataLatLng.dataset.lat)
  const lng = parseFloat(dataLatLng.dataset.lng)
  const LatLng = new google.maps.LatLng(lat, lng)

  const docId = {
    street: document.getElementById('modifier_sortie_form_lieu_rue'),
    adress: document.getElementById('modifier_sortie_form_adresse'),
    lat: document.getElementById('modifier_sortie_form_lieu_latitude'),
    lng: document.getElementById('modifier_sortie_form_lieu_longitude'),
  }

  cityZip = document.getElementById('modifier_sortie_form_ville')
  city = cityZip.selectedOptions[0].text.split(',')[0]
  zip = cityZip.selectedOptions[0].text.split(',')[1]

  cityZip.addEventListener('change', () => {
    setBorder()
  })
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

  const getPoly = async () => {
    url = `https://nominatim.openstreetmap.org/search?city=${city}&format=geojson&polygon_geojson=1&countrycodes=fr&limit=1`
    data = await fetch(url)
    data = await data.json()
    map.data.addGeoJson(data)
  }

  const setBorder = async () => {
    city = cityZip.selectedOptions[0].text.split(',')[0]
    zip = cityZip.selectedOptions[0].text.split(',')[1]
    map.data.forEach(function (feature) {
      map.data.remove(feature)
    })
    getPoly()
    zoom(map)
  }

  map.data.setStyle({
    fillColor: 'transparent',
    strokeWeight: 2,
    strokeColor: 'red',
    clickable: false,
  })

  const zoom = (map) => {
    const bounds = new google.maps.LatLngBounds()
    map.data.forEach((feature) => {
      const geometry = feature.getGeometry()

      if (geometry) {
        processPoints(geometry, bounds.extend, bounds)
      }
    })
    map.fitBounds(bounds)
  }

  const processPoints = (geometry, callback, thisArg) => {
    if (geometry instanceof google.maps.LatLng) {
      callback.call(thisArg, geometry)
    } else if (geometry instanceof google.maps.Data.Point) {
      callback.call(thisArg, geometry.get())
    } else {
      geometry.getArray().forEach((g) => {
        processPoints(g, callback, thisArg)
      })
    }
  }

  getPoly()

  const setAdress = (LatLng) => {
    geocoder.geocode(
      {
        latLng: LatLng,
      },
      (results, status) => {
        if (status == google.maps.GeocoderStatus.OK) {
          if (results[0]) {
            const adress = results[0].address_components

            let locality = adress.find((i) => i.types[0] === 'locality').short_name

            if (locality !== city) return

            let street_number = adress.find((i) => i.types[0] === 'street_number') || ''
            !!street_number && (street_number = street_number.short_name)

            let route = adress.find((i) => i.types[0] === 'route') || ''
            !!route && (route = route.short_name)
            console.log(results[0].formatted_address)

            const street = street_number + ' ' + route

            docId.adress.value = results[0].formatted_address
            docId.street.value = street
            docId.lat.value = LatLng.lat()
            docId.lng.value = LatLng.lng()
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
