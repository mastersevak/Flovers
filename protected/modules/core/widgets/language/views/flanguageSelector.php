<ul id="language-select" class="language_select box clearfix">
<?
$cp = app()->urlManager->getCleanUrl( request()->getPathInfo() ); 
$i = 1; 

    if(sizeof($languages) < 4) { 
        
        echo "<ul class='clearfix'>";
        $lastElement = end($languages);
        foreach($languages as $key=>$lang) {
            $params = array('class'=>'trans02');
            if($key == $currentLang) $params['class'] .= ' active';
            
            echo CHtml::openTag('li', $params);
            echo CHtml::link($lang, bu() . $key . ($cp != '/'? $cp : ''), array('lang'=>$key));
            echo CHtml::closeTag('li');
        }
        echo "</ul>";
        
    }
?>
</ul>

