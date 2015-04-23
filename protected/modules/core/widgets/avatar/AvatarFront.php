<?php
Yii::import('application.modules.core.widgets.avatar.Avatar');

class AvatarFront extends Avatar
{

	public function run(){
        $vars = parent::makeVars();
        $model = get_class($this->model);

        Yii::app()->getClientScript()->registerScript(__CLASS__.'#upload_{$model}_avatar', 
                "$('#upload-avatar').on('click', function(e){
                    e.preventDefault();

                    console.log('#{$model}_avatar');

                    $('#{$model}_{$this->field}').trigger('click');
                });", CClientScript::POS_END);

        $this->render('front', $vars);
    }

}