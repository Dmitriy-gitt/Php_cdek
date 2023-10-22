const pointAsync = async () => {
	const response = await fetch('http://localhost:8080/php_new.php', {
		headers: {
			'Content-Type': 'application/json',
			'Accept': 'application/json'
		}
	})
		.then((result) => {
			json = result.json();
			console.log('[PHP SERVER RESPONSE]', json);
			return json;
		})
		.catch(error => new Error(error));

	return response;
}

async function init() {
	const point_arr = await pointAsync();
	let map = new ymaps.Map('map-test', {
		center: [point_arr[0].latitude, point_arr[0].longitude],
		zoom: 17
	});
	point_arr.forEach(function (elem) {
		let center = [elem.latitude, elem.longitude];

		let placemark = new ymaps.Placemark(center, {}, {

		});
		map.geoObjects.add(placemark);
	});
	/*
		map.controls.remove('geolocationControl'); // удаляем геолокацию
			map.controls.remove('searchControl'); // удаляем поиск
			map.controls.remove('trafficControl'); // удаляем контроль трафика
			map.controls.remove('typeSelector'); // удаляем тип
			map.controls.remove('fullscreenControl'); // удаляем кнопку перехода в полноэкранный режим
			map.controls.remove('zoomControl'); // удаляем контрол зуммирования
			map.controls.remove('rulerControl'); // удаляем контрол правил
			// map.behaviors.disable(['scrollZoom']); // отключаем скролл карты (опционально)
	*/

}
ymaps.ready(init);