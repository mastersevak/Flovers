<div class="btn-group buttons-radio language-selector" data-type="<?=$type?>"> 

    <? foreach($languages as $key=>$lang){ 
        $options = ['class' => 'btn', 'data-language' => $key];
        if($key == $currentLang) $options['class'] .= ' btn-primary active';

        echo CHtml::htmlButton($key, $options);
    } ?>
</div>
