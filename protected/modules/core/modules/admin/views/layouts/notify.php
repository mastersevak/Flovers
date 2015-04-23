<?php 

$cId = app()->controller->id;
$aId = app()->controller->action->id;
$this->beginContent(app()->controller->getModule('admin')->appLayout); ?>

<div class="btn-group mb20">
	<? $this->widget('UIMenu', ['buttons'=>[
			[
				'name'   => 'Информация',
				'url'    => ['/core/admin/notify/info'],
				'active' => $cId == 'notify' && $aId == 'info'
			],
			[
				'name'   => 'Предупреждения',
				'url'    => ['/core/admin/notify/warning'],
				'active' => $cId == 'notify' && $aId == 'warning'
			],
			[
				'name'   => 'Ошибки',
				'url'    => ['/core/admin/notify/error'],
				'active' => $cId == 'notify' && $aId == 'error'
			],
			[
				'name'   => 'SMS',
				'url'    => ['/core/admin/notify/sms'],
				'active' => $cId == 'notify' && $aId == 'sms'
			],
			[
				'name'   => 'E-mail',
				'url'    => ['/core/admin/notify/email'],
				'active' => $cId == 'notify' && $aId == 'email'
			],
		]]) ?>
</div>

<div>
	<?=$content?>
</div>

<?php $this->endContent(); ?>