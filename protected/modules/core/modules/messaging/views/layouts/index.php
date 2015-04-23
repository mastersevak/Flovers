<?php 

$cId = app()->controller->id;
$aId = app()->controller->action->id;
$this->beginContent(app()->controller->getModule('messaging')->appLayout); ?>

<div class="btn-group mb20">
	<? $this->widget('UIMenu', ['buttons'=>[
			[
				'name'   => 'Информация',
				'url'    => ['/core/messaging/logs/info'],
				'active' => $cId == 'log' && $aId == 'info'
			],
			[
				'name'   => 'Предупреждения',
				'url'    => ['/core/messaging/logs/warning'],
				'active' => $cId == 'log' && $aId == 'warning'
			],
			[
				'name'   => 'Ошибки',
				'url'    => ['/core/messaging/logs/error'],
				'active' => $cId == 'log' && $aId == 'error'
			],
			[
				'name'   => 'SMS',
				'url'    => ['/core/messaging/logs/sms'],
				'active' => $cId == 'log' && $aId == 'sms'
			],
			[
				'name'   => 'E-mail',
				'url'    => ['/core/messaging/logs/email'],
				'active' => $cId == 'log' && $aId == 'email'
			],
		]]) ?>
</div>

<div>
	<?=$content?>
</div>

<?php $this->endContent(); ?>