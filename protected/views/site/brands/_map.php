<?php
Yii::import('core.extensions.EGMap.*');
$gMap = new EGMap();
$gMap->setCenter($model->lat, $model->lng);
$gMap->addMarker(new EGMapMarker($model->lat, $model->lng));
$gMap->zoom = 15;
$gMap->setWidth(545);
$gMap->setHeight(165);
$gMap->renderMap();

