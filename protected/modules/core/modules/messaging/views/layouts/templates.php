<?php 

$cId = app()->controller->id;
$aId = app()->controller->action->id;
$this->beginContent(app()->controller->getModule('messaging')->appLayout); ?>

<div class="btn-group mb20">
	<? $this->widget('UIMenu', ['buttons'=>[
			[
				'name'   => 'SMS',
				'url'    => ['/core/messaging/templates/sms'],
				'active' => $cId == 'templates' && $aId == 'sms'
			],
			[
				'name'   => 'E-mail',
				'url'    => ['/core/messaging/templates/email'],
				'active' => $cId == 'templates' && $aId == 'email'
			],
		]]) ?>
</div>

<div>
	<?=$content?>
</div>

<?php $this->endContent(); ?>