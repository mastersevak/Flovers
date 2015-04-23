<?php 


/**
 * HttpRequest
 */
 class HttpRequest extends CHttpRequest
 {
 	
 	public $noCsrfValidationRoutes=array();

    public function init(){
        parent::init();

        //Для более надежной защиты от XSS
        /*if(isset($_GET))
            $_GET=$this->defenderXss($_GET, ["(", ")"]);
        if(isset($_COOKIE))
            $_COOKIE=$this->defenderXss($_COOKIE, []);
        if(isset($_POST))
            $_POST=$this->defenderXss($_POST, []);
        if(isset($_REQUEST))
            $_REQUEST=$this->defenderXss($_REQUEST, []); */
    }

    protected function normalizeRequest()
    {
        //attach event handlers for CSRFin the parent
        parent::normalizeRequest();
            //remove the event handler CSRF if this is a route we want skipped
        if(!Common::isCli() && $this->enableCsrfValidation) //если не проверять !Common::isCli(), то при createUrl, из консоли будет ошибка
        {
            $url=Yii::app()->getUrlManager()->parseUrl($this);
            foreach($this->noCsrfValidationRoutes as $route)
            {
                if(strpos($url, $route)===0)
                    Yii::app()->detachEventHandler('onBeginRequest', array($this, 'validateCsrfToken'));
            }
        }
    }

    //Для защиты от XSS
    private function defenderXss($arr, $filter = false ){
        if(!$filter) $filter = array("(", ")");  
        
        foreach($arr as $num=>$xss){
            $arr[$num] = str_replace ($filter, "|", $xss);
            
            if(is_string($arr[$num]))
                $arr[$num] = strip_tags ($arr[$num]);
            elseif(is_array($arr[$num])){
                foreach($arr[$num] as &$one){
                    $one = strip_tags ($one);
                }
            }
        }
        
        return $arr;
    } 

    /**
     * Переделываем функцию проверки ip, 
     * так как на некоторых серверах ip храниться не в $_SERVER['REMOTE_ADDR'] a в $_SERVER['HTTP_X_FORWARDED_FOR']
     * @return [type] [description]
     */
    public function getUserHostAddress()
    {
        $ip = parent::getUserHostAddress();
        
        if($ip == '127.0.0.1'){
            $newip = isset($_SERVER['HTTP_X_FORWARDED_FOR'])? $_SERVER['HTTP_X_FORWARDED_FOR']: false;
            $ip = $newip && $newip != '127.0.0.1' ? $newip : $ip;
        }
        
        return $ip;
    }

}