<?php

class Common {

    public static function getRandomUniqueName($model, $field, $ext = false){
		$n=1;
		// loop until random is unqiue - which it probably is first time!
		while ($n>0) {
		    $rnd = self::makeRandomString(9); //dechex(rand()%999999999);
		    $newname = $rnd . ($ext ? '.' . $ext : '');
		    $n = $model::model()->count($field.'=:fieldname', array('fieldname'=>$newname));
		}

		return $newname;
    }

    public static function makeRandomString($max = 6){
    	$i = 0; //Reset the counter.
	    $possible_keys = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
	    $keys_length = strlen($possible_keys);
	    $str = ""; //Let's declare the string, to add later.
	    while($i<$max) {
	    	$rand = mt_rand(1, $keys_length-1);
	    	$str.= $possible_keys[$rand];
	    	$i++;
	    }
	    return $str;
    }

    public static function getPagerSize($model){
    	$pages=false;
 		$model=ucfirst($model);
 		$cookie=$model."_pagesize";
 		if($pages = Cookie::get($cookie)) {
            return $pages;
		}
		else $pages = $model::PAGE_SIZE;

        Cookie::set($cookie, $pages);

		return $pages;
    }

    /**
     * Другая фунция обратимого шифрования
     * 
     * @param  [type] $action    ['enc' / 'dec']
     * @param  [type] $string    ['входная строка']
     * @param  [type] $key       [E4HD9h4DhS23DYfhHemkS3Nf]
     * @param  [type] $iv        [fYfhHeDm]
     * @param  [type] $bit_check [8]
     */
    public static function encrypt_decrypt($action, $text, $key, $iv, $bit_check) {

        $output = false;
        
        if( $action == 'enc' ) {
            $text_num =str_split($text,$bit_check);
            $text_num = $bit_check-strlen($text_num[count($text_num)-1]);
            for ($i=0;$i<$text_num; $i++) {$text = $text . chr($text_num);}
            $cipher = mcrypt_module_open(MCRYPT_TRIPLEDES,'','cbc','');
            mcrypt_generic_init($cipher, $key, $iv);
            $decrypted = mcrypt_generic($cipher,$text);
            mcrypt_generic_deinit($cipher);
            $output = base64_encode($decrypted);
        }
        elseif($action == 'dec'){
            $cipher = mcrypt_module_open(MCRYPT_TRIPLEDES,'','cbc','');
            mcrypt_generic_init($cipher, $key, $iv);
            $decrypted = mdecrypt_generic($cipher,base64_decode($text));
            mcrypt_generic_deinit($cipher);
            $last_char=substr($decrypted,-1);
            
            for($i=0;$i<$bit_check-1; $i++){
                if(chr($i)==$last_char){
                    $decrypted=substr($decrypted,0,strlen($decrypted)-$i);
                    break;
                }
            }

            $output = $decrypted;
        }

        return $output;
    }



    public static function getYoutubeEmbedCode($source){
        
        $search = '#(https?://)?(?:www\.)?(?:youtu\.be/|youtube\.com(?:/embed/|/v/|/watch\?(.+)?v=))([\w\-]{10,12}).*$#x';
        $replace = '$3';
        $embed_code = preg_replace($search, $replace, $source);

        return $embed_code;
        
    }

    public static function object_in_array($needle, $haystack) {

        $stringArray = array_map(create_function('$in','return (string)$in;'),$haystack);
        $objectString = (string)$needle;
        return in_array($objectString, $stringArray, TRUE);

    }

    public static function og_trim($str, $limit = 150){
        return preg_replace("`^(.{".$limit."}.*?)[ .,:!?].*$`s", "$1...", $str);
    }

    public static function unserializeForm($str) {
        $returndata = array();
        $strArray = explode("&", $str);
        $i = 0;
        foreach ($strArray as $item) {
            $array = explode("=", $item);
            $returndata[$array[0]] = urldecode($array[1]); //используется urldecode, так как все строки при $('#form').serialize(), преобрауются в url-подобную строку
        }

        return $returndata;
    }

    //Удалить елемент массива по значению
    public static function removeFromArray(&$array, $value){
        $array = array_flip($array); //Меняем местами ключи и значения
        unset ($array[$value]) ; //Удаляем элемент массива
        $array = array_flip($array); //Меняем местами ключи и значения

        return $array;
    }

    public static function isCLI(){
        return substr(php_sapi_name(), 0, 3) == 'cli';
    }

    public static function widget($content){

        if(preg_match_all("%(.*?){{(youtube):\[?(.+?)\]?}}(.*?)%is", $content, $matches)){
            foreach($matches[2] as $key => $widgetName){
                $par = $matches[3][$key];
                $par = str_replace(' ', ' ', $par); //здесь другой пробел, который ставит elrte
                $par = preg_replace("#\s+#", " ", $par);

                $content = str_replace(' ', ' ', $content); //здесь другой пробел, который ставит elrte
                $content = preg_replace("#\s+#", " ", $content);

                if(strtolower($widgetName) == 'youtube')
                    $content = preg_replace("%(.*?)({{youtube:\[?".addcslashes($par, "?./")."\]?}})(.*?)%is", "$1".self::youtube($par)."$3", $content);
            }
        }
  
        return $content;
        
    }

    public static function youtube($params){
        $widgetCode = '';

        // $params приходят такого вида href:http://.... width:200 height=300
        $params = preg_replace("#(https?://)(.+)?#", "$2", $params);
        $params = explode(' ', $params);

        $newparams = array();
        if(count($params) == 1){ //для случая с параметром гсе приходит просто ссылка
            $newparams['href'] = $params[0];
        }
        else{
            foreach($params as $attr){
                list($key, $val) = explode(':', $attr);
                $newparams[$key] = $val;
            }
        }
        
        $embed_code = self::getYoutubeEmbedCode($newparams['href']);

        ob_start();
        app()->controller->widget('ext.Yiitube.Yiitube', array('v' => $embed_code, 'params'=>$newparams));
        $widgetCode = ob_get_contents();
        ob_end_clean();

        return $widgetCode;    
    }

    /**
     * Export XML from array, with attributes
     * $doc = new DOMDocument();
     * 
     * $child = generate_xml_element( $doc, $data );
     * if ( $child )
     *   $doc->appendChild( $child );
     * $doc->formatOutput = true; // Add whitespace to make easier to read XML
     * $xml = $doc->saveXML();
     */
    public function generate_xml_element( $dom, $data ) {
        if ( empty( $data['name'] ) )
            return false;
     
        // Create the element
        $element_value = ( ! empty( $data['value'] ) ) ? $data['value'] : null;
        $element = $dom->createElement( $data['name'], $element_value );
     
        // Add any attributes
        if ( ! empty( $data['attributes'] ) && is_array( $data['attributes'] ) ) {
            foreach ( $data['attributes'] as $attribute_key => $attribute_value ) {
                $element->setAttribute( $attribute_key, $attribute_value );
            }
        }
     
        // Any other items in the data array should be child elements
        foreach ( $data as $data_key => $child_data ) {
            if ( ! is_numeric( $data_key ) )
                continue;
     
            $child = generate_xml_element( $dom, $child_data );
            if ( $child )
                $element->appendChild( $child );
        }
     
        return $element;
    }

    /**
    * Converts an array to Xml without DOM, and attributes
    *
    * @param mixed $arData The array to convert
    * @param mixed $sRootNodeName The name of the root node in the returned Xml
    * @param string $sXml The converted Xml
    */
    public function arrayToXml( $arData, $sRootNodeName = 'data', $sXml = null )
    {
        // turn off compatibility mode as simple xml doesn't like it
        if ( 1 == ini_get( 'zend.ze1_compatibility_mode' ) )
                ini_set( 'zend.ze1_compatibility_mode', 0 );

        if ( null == $sXml )
                $sXml = simplexml_load_string( "<?xml version='1.0' encoding='utf-8'?><{$sRootNodeName} />" );

        // loop through the data passed in.
        foreach ( $arData as $_sKey => $_oValue )
        {
            // no numeric keys in our xml please!
            if ( is_numeric($_sKey ) )
                    $_sKey = "unknownNode_". ( string )$_sKey;

            // replace anything not alpha numeric
            $_sKey = preg_replace( '/[^a-z]/i', '', $_sKey );

            // if there is another array found recrusively call this function
            if ( is_array( $_oValue ) )
            {
                    $_oNode = $sXml->addChild( $_sKey );
                    self::arrayToXml( $_oValue, $sRootNodeName, $_oNode );
            }
            else
            {
                // add single node.
                $_oValue = htmlentities( $_oValue );
                $sXml->addChild( $_sKey, $_oValue );
            }
        }

        return( $sXml->asXML() );
    }

    /**
     * A more inuitive way of sorting multidimensional arrays using array_msort() in just one line, you don't have to divide the original array into per-column-arrays:
	 * $arr2 = array_msort($arr1, array('name'=>SORT_DESC, 'cat'=>SORT_ASC));
	 */

    public static function arrayMsort($array, $cols) {
        $colarr = array();
        foreach ($cols as $col => $order) {
            $colarr[$col] = array();
            foreach ($array as $k => $row) { $colarr[$col]['_'.$k] = strtolower($row[$col]); }
        }
        $eval = 'array_multisort(';
        foreach ($cols as $col => $order) {
            $eval .= '$colarr[\''.$col.'\'],'.$order.',';
        }
        $eval = substr($eval,0,-1).');';
        eval($eval);
        $ret = array();
        foreach ($colarr as $col => $arr) {
            foreach ($arr as $k => $v) {
                $k = substr($k,1);
                if (!isset($ret[$k])) $ret[$k] = $array[$k];
                $ret[$k][$col] = $array[$k][$col];
            }
        }
        return $ret;

    }

    public static function jsonError($errors, $end = false, $additional = []){
        $params = ['success' => false];
        if($errors) {
            if(!is_array($errors)) $errors = [$errors];

            $params['errors'] = $errors;
        }

        $params += $additional;

        header('Content-type: application/json');
        app()->response->setStatus(HttpResponse::STATUS_OK);
        echo CJSON::encode($params);

        if($end) Yii::app()->end();
    }

    public static function jsonSuccess($end = false, $additional = []){
        $params = ['success' => true];
        $params = CMap::mergeArray($params, $additional);

        header('Content-type: application/json');
        app()->response->setStatus(HttpResponse::STATUS_OK);
        echo CJSON::encode($params);

        if($end) Yii::app()->end();
    }

    //уведомить сокет сервер о завершении процесса
    public static function nodejsEmit($params){

        $url = param('nodejsUrl').DS."queuecomplete";
        
        $curl = Yii::app()->curl;
        $curl->setOptions([
                CURLOPT_HTTPHEADER => array('Content-Type: application/json'),
                CURLOPT_RETURNTRANSFER => true]);

        $curl->post($url, CJSON::encode($params));
    }

    //Получаем отправленные постом, отмеченные checkbox-ы, и отдаем callback функции
    public static function getChecked($key = 'id'){
        if($items = request()->getPost($key)){
            if(is_string($items)) $items = explode(',', $items);

            return $items;
        }

        return [];
    }

    public static function arr2obj($array){
        return json_decode(json_encode($array), false);
    }
   
}
?>