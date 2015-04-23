<script src="http://api-maps.yandex.ru/2.0-stable/?load=package.standard&lang=ru-RU" type="text/javascript"> </script>
    <script type="text/javascript">
        var myMap, myPlacemark, coords;
     
        ymaps.ready(init);
     
            function init () {
     
            //Определяем начальные параметры карты
            myMap = new ymaps.Map('YMapsID', {
                center: [43.60, 39.73], 
                zoom: 13
            }); 
     
            //Определяем элемент управления поиск по карте  
            var SearchControl = new ymaps.control.SearchControl({noPlacemark:true});    
     
            //Добавляем элементы управления на карту
             myMap.controls
                .add(SearchControl)                
                .add('zoomControl')                
                .add('typeSelector')                 
                .add('mapTools');
     
            coords = [43.5929,39.7333];
            myMap.behaviors.enable('scrollZoom');
     
            //Определяем метку и добавляем ее на карту              
            myPlacemark = new ymaps.Placemark([56.326944, 44.0075],{}, {preset: "twirl#houseIcon", draggable: true}); 
            myMap.geoObjects.add(myPlacemark);          
     
            //Отслеживаем событие перемещения метки
            myPlacemark.events.add("dragend", function (e) {            
                coords = this.geometry.getCoordinates();
                if (myMap.getCenter() != coords) {     
                    recentr(coords);
                }
                savecoordinats();
            }, myPlacemark);
     
            //Отслеживаем событие щелчка по карте
            myMap.events.add('click', function (e) {        
                coords = e.get('coordPosition');
                if (myMap.getCenter() != coords) {     
                    recentr(coords);
                }
                savecoordinats();
            }); 
     
            //Отслеживаем событие выбора результата поиска
            SearchControl.events.add("resultselect", function (e) {
                coords = SearchControl.getResultsArray()[0].geometry.getCoordinates();
                savecoordinats();
            });
     
            //Ослеживаем событие изменения области просмотра карты - масштаб и центр карты
            myMap.events.add('boundschange', function (event) {
                if (event.get('newZoom') != event.get('oldZoom')) {     
                    savecoordinats();
                }
                if (event.get('newCenter') != event.get('oldCenter')) {       
                    savecoordinats();
                }
            });
        }
     
        //Функция для передачи полученных значений в форму
        function savecoordinats (){ 
            var new_coords = [coords[0].toFixed(4), coords[1].toFixed(4)]; 
            var lat = [coords[0].toFixed(4)];
            var lon = [coords[1].toFixed(4)];
            myPlacemark.getOverlay().getData().geometry.setCoordinates(new_coords);
            document.getElementById("latlongmet").value = new_coords;
            document.getElementById("lat").value = lat;
            document.getElementById("lng").value = lon;
        }

        //Функция для изменение центра карты.
        function recentr(e){
            myMap.setCenter(e,myMap.getZoom(),
                {duration: 300});
        }
 
    </script>