<?php
/*
 * LangUrlManager - альтернативный менеджер урлов с поддержкой языков
 * при инициализации добавляет к существующим правилам маршрутизации
 * правила для выбора языков в началае пути.
 * Т.о. /page/<slug> к примеру становится /<language>/page/<slug>.
 * Добавленный параметр используется в LanguageBehavior
 */
class LangUrlManager extends CUrlManager
{
    public $languages;
    public $langParam = 'language';

    public function init()
    {

        if(!Common::isCLI()){
            //static PAGE rules
            $defaultController = app()->defaultController; //здесь ошибка из консоли
            
            $cacheKey = 'page.routes';
            $result = Yii::app()->cache->get($cacheKey);
            
            if($result !== false) $routes = $result;
            else{
                $criteria = 'route is not null and route <> ""';

                $routes = app()->db->createCommand()
                                ->select('route')
                                ->where($criteria)
                                ->from('{{page}}')
                                ->queryColumn();

                Yii::app()->cache->set($cacheKey, $routes, 0);    
            }

            foreach ($routes as $route)
            {
                $pattern = '<route:'.$route.'>';
                $route = $defaultController.'/page';
                $this->addRules(array($pattern => $route));
                $this->rules[$pattern] = $route;
            }    
        }
        

        // Получаем из настроек доступные языки
        $this->languages = array_keys(param('languages'));

        // Если указаны - добавляем правила для обработки, иначе ничего не трогаем вообще
        if (is_array($this->languages))
        {
            // Добавляем правила для обработки языков
            $r = array();

            foreach ( $this->rules as $rule => $p )
                $r[(($rule[0] == '/') ? ('/<' . $this->langParam . ':\w{2}>') : ('<' . $this->langParam . ':\w{2}>/')) . $rule] = $p;

            $this->rules = array_merge($r, $this->rules);

            $p = parent::init();
            $this->processRules();

            return $p;
        }
        else
            return parent::init();
    }

    public function createUrl($route, $params = array(), $ampersand = '&')
    {
        // Если указаны языки, дописываем указанный язык
        if (is_array($this->languages))
        {
            // Если язык не указан - берем текущий
            if (!isset($params[$this->langParam]))
                $params[$this->langParam] = Yii::app()->language;

            // Если указан "нативный" язык и к тому же он текущий  - делаем URL без него, т.к. он соответсвует пустому пути
            if ((param('defaultLanguage') == $params[$this->langParam]) && ($params[$this->langParam] == Yii::app()->language))
                unset($params[$this->langParam]);

        }

        return parent::createUrl($route, $params, $ampersand);
    }

    public function getCleanUrl($url)
    {
        
        if ( in_array($url, $this->languages))
            return "/";

        $r = join("|", $this->languages);
        $url = preg_replace("/^($r)\//", "", $url);

        if ( !isset($url[0]) || ($url[0] != '/') )
            $url = '/' . $url;
        return $url;
    }
}