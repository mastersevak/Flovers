<?php 
$criteria = new SDbCriteria;
$criteria->compare('code', $models);

$settings = Settings::model()->findAll($criteria);

if ($settings): ?>

    <?php $form=$this->beginWidget('SActiveForm', array(
        'id'=>'ajax-settings', //объязательно для работы кнопок сохранения, удаления
        'action'=>array('/core/settings/ajaxUpdate')
    )); 
    ?>
    <h4 class="mb10"><?=t('front', 'Настройки');?></h4>
    
    <div class="stdform stdform2">
        
        <?php foreach ($settings as $key => $one): ?>
        <div class="par clearfix">
            <?=CHtml::label($one->title, 'Value_'.$one->code); ?>
            <span class="field">
                <?=CHtml::textField('Value_'.$one->code, $one->value, array('data-id'=>$one->id)); ?>
            </span>
        </div>
        <?php endforeach ?>

    </div>

    <?php $this->endWidget(); ?>

<?php endif ?>

<?php 

$script = <<<script
$('#ajax-settings input:text').on('change', function(){
    $.post($(this).closest('form').prop('action') + '?id=' + $(this).data('id'), 
        {value: $(this).val()}
    )
});

script;

cs()->registerScript('settings_ajax_update', $script, CClientScript::POS_READY);

 ?>