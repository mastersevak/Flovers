<?php
/**
 * @author EnChikiben
 */
class YandexMap extends CWidget
{
	private $cs = null;

	public $htmlOptions = array();

	public $protocol = "http://";
	public $lang = "ru-RU";
	public $load = 'package.full';
	public $key = null;
	public $coordorder = 'latlong';
	public $mode = 'release';


	public $id = 'yandexmap';
	public $width;
	public $height;
	public $zoom = 7;
	public $center = array("ymaps.geolocation.latitude", "ymaps.geolocation.longitude");

	public $options = array();

	public $bounds;

	public $controls = array(
		'zoomControl' => true,
		'searchControl' => true,
		'typeSelector' => true,
		'fullscreenControl' => true
	);


	public $placemark = array();
	public $polyline = array();

	protected function connectYandexJsFile(){
		$url = array();
		$url[] = "load=".$this->load;
		$url[] = "lang=".$this->lang;

		$this->cs->registerScriptFile($this->protocol."api-maps.yandex.ru/2.1/?".  implode("&", $url) );
	}

	protected function initMapJsObject(){
		$state = array();

		if (is_array($this->center) && !empty($this->center) )
			$state[] = "center:[{$this->center[0]},{$this->center[1]}]";
		else
			throw new Exception('Error center map coordinat.');

		if ( $this->zoom )
			$state[] = 'zoom:'.$this->zoom;

		if ( $this->bounds )
			$state[] = "bounds:[[{$this->bounds[0][0]}, {$this->bounds[0][1]}], [{$this->bounds[1][0]}, {$this->bounds[1][1]}]]";


		if($this->controls) {
			$controls = $this->initMapControls();
			$state[] = "controls: [{$controls}]";
		}

		$state = implode(",",$state);

		$options = $this->generateOptions($this->options);
		return "map = new ymaps.Map ('{$this->id}',{".$state."},{".$options."});";
	}

	protected function initMapControls(){
		$controls = array();

		if ( is_array($this->controls) && !empty($this->controls) )
			foreach ($this->controls as $key => $value ) {
				if ( $value )
					$controls[] = CJavaScript::encode($key);
			}

		return implode(',', $controls);
	}


	protected function registerClientScript(){

		$this->connectYandexJsFile();

		$map = $this->initMapJsObject();

		$placemark = $this->placemarks();
		$polyline = $this->polylines();

		$js = <<<EQF

ymaps.ready(function(){
	$map
	$placemark
	$polyline
});

EQF;

		$this->cs->registerScript($this->id,$js,CClientScript::POS_END);
	}

	protected function is_array(&$array){
		list($key) = array_keys($array);
		return is_array($array[$key]);
	}

	protected function generateOptions(&$array){
		$options = array();

		if ( !empty($array) && is_array($array) )
			foreach ($array as $key => $value ) {
				$options[] = CJavaScript::encode($key).":".CJavaScript::encode($value)."";
			}
		else
			return null;

		return implode(',', $options);
	}


	protected function placemarks(){

		$placemark = '';
		if (is_array($this->placemark) && !empty($this->placemark) ){
			if ( $this->is_array($this->placemark) ){
				foreach ($this->placemark as $key => $value) {
					$placemark .= $this->placemark('placemark_'.$key, $value);
				}
			} else {
				$placemark .= $this->placemark('placemark', $this->placemark);
			}
		}

		return $placemark;
	}

	protected function placemark($name,&$value){

		if ( !isset($value['lat'],$value['lng']) ) return;

		$placemark = '';

		$properties = '';
		if ( isset($value['properties']) ){
			$properties = $this->generateOptions($value['properties']);
		}

		$options = '';
		if ( isset($value['options']) ){
			$options = $this->generateOptions($value['options']);
		}

		$events = '';
		if( isset($value['event']) ){
			$eventName = $value['event']['name'];
			$callback = $value['event']['callback'];

			$events = "{$name}.events.add('{$eventName}', {$callback});";

			//for using placemark in event use e.originalEvent.target
		}

		$placemark .= "{$name} = new ymaps.Placemark([{$value['lat']},{$value['lng']}],{".$properties."},{".$options."});";
		if(isset($value['name'])) $placemark .= ";{$name}.name = " . $value['name'] . ";";

		if($events) $placemark .= $events;


		$placemark .= "map.geoObjects.add({$name});";

		return $placemark;
	}


	protected function polylines(){
		$polylines = '';
		if ( is_array($this->polyline) && !empty($this->polyline) ){

			list($key) = array_keys($this->polyline);
			if ( $this->is_array($this->polyline) && !isset($this->polyline[$key]['lat']) ){
				foreach ($this->polyline as $key => $value)
					$polylines .= $this->polyline('polyline_'.$key,$value);
			} else {
				$polylines .= $this->polyline('polyline',$this->polyline);
			}

		}
		return $polylines;
	}

	protected function polyline($name,&$value){

		$polyline = '';

		$coordinates = array();
		foreach ($value as $coordinate) {
			if ( isset($coordinate['lat'],$coordinate['lng']) )
				$coordinates[] = "[".$coordinate['lat'].",".$coordinate['lng']."]";
		}

		if ( empty($coordinates) ) return;

		$properties = '';
		if ( isset($value['properties']) ){
			$properties = $this->generateOptions($value['properties']);
		}

		$options = '';
		if ( isset($value['options']) ){
			$options = $this->generateOptions($value['options']);
		}

		$polyline .= "var $name = new ymaps.Polyline([".implode(',', $coordinates)."], {".$properties."}, {".$options."});";
		$polyline .= "map.geoObjects.add($name);";

		return $polyline;
	}


	public function init(){
		$this->cs = Yii::app()->clientScript;

		$this->registerClientScript();
	}

	public function run(){

		$this->htmlOptions['id'] = $this->id;

		if ( !isset($this->htmlOptions['style']) ){
			$styles = [];
			if($this->width) $styles[] = "width:{$this->width}px";
			if($this->height) $styles[] = "height:{$this->height}px";

			if($styles) $this->htmlOptions['style'] = implode(";", $styles);
		}

		echo CHtml::tag('div',$this->htmlOptions,'');
	}

	/**
	 * @todo Написать функцию, которая будет принимать координаты
	 * и получать ближайшее к ним метро
	 * @param  integer		$lat - широта
	 * @param  integer		$lng - долгота
	 * @return string		- Название ближайшего метро
	 */
	public static function getNearestMetro($lat, $lng){

	}
}
?>
