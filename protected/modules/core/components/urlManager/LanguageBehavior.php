<?php
class LanguageBehavior extends CBehavior
{

    public function attach($owner)
    {
        $owner->attachEventHandler('onBeginRequest', 
            array($this, 'handleLanguageBehavior'));
    }

    public function handleLanguageBehavior($event)
    {
        $app  = app();
        $lm   = app()->urlManager;
        $l    = null;
        $defLang = param('defaultLanguage');

        if ( !is_array($lm->languages)) return;

        // Если указан язык известный нам
        if ((
                isset($_GET[$lm->langParam]) && 
                in_array($_GET[$lm->langParam], $lm->languages) && 
                ($l = $_GET[$lm->langParam])
        ) || (
                ($l = substr(request()->getPathInfo(), 0, 2)) &&
                (2 == strlen($l)) &&
                in_array($l, $lm->languages)
        ))
        {

            // Если текущий язык у нас не тот же, что указан - поставим куку и все дела
            if ($app->language != $l)
                $this->setLanguage($l);

            // Если указанный язык в URL в виде пути или параметра - нативный для приложения
            if ( $l == $defLang)
            {
                // Если указан в пути, редиректим на "чистый URL"
                $l = substr(request()->getPathInfo(), 0, 2);
                if ( (2 == strlen($l)) && ($l == $defLang))
                {
                    $this->setLanguage($l);
                    if(!request()->isAjaxRequest)
                        request()->redirect( ((substr(app()->homeUrl,-1,1)=="/")?substr(app()->homeUrl,0,strlen(app()->homeUrl)-1):app()->homeUrl ) . $lm->getCleanUrl(substr(request()->getPathInfo(), 2)));
                }
            }
        }
        else {
            $l = null;

            /*
            
            // Пытаемся определить язык из сессии
            if ($user->hasState($lm->langParam))
                $l = $user->getState($lm->langParam);

            // Если в сессии нет - пробуем получить из кук
            else if (isset(request()->cookies[$lm->langParam]) && in_array(request()->cookies[$lm->langParam]->value, $lm->languages))
                $l = request()->cookies[$lm->langParam]->value;

            */

            // Если и в куках не нашлось языка - получаем код языка из предпочтительной локали, указанной в браузере у клиента
            
            /*else if ( $l = app()->getRequest()->getPreferredLanguage())
                $l = app()->locale->getLanguageID($l);*/
            
            // иначе по-умолчанию
            if(!$l || !in_array($l, $lm->languages))
                $l = $app->language = $defLang;

            // Сделаем редирект на нужн ый url с указанием языка, если он не нативен
            if ($l != $defLang)
            {
                $this->setLanguage($l);

                if(!request()->isAjaxRequest)
                    request()->redirect((app()->homeUrl . (substr(app()->homeUrl,-1,1)!="/"?"/":"") . $l) . $lm->getCleanUrl(request()->getPathInfo()));
            } else
                Yii::app()->language = $l;
        }

    }

    protected function setLanguage($language)
    {
        $lp  = app()->urlManager->langParam;
        user()->setState($lp, $language);
        $cookie = new CHttpCookie($lp, $language);
        $cookie->expire = time() + (60 * 60 * 24 * 365); // (1 year)
        Yii::app()->request->cookies[$lp] = $cookie;
        Yii::app()->language = $language;
    }
}