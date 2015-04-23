<?php 
$cId = app()->controller->id;
$aId = app()->controller->action->id;
$this->beginContent(Rights::module()->appLayout); ?>

<div class="btn-group mb20">
    <? $this->widget('UIMenu', ['buttons'=>[
            [
                'name'   => 'Привязки',
                'url'    => ['/core/rights/assignment/view'],
                'active' => $cId == 'assignment'
            ],
            [
                'name'   => 'Разрешения',
                'url'    => ['/core/rights/authItem/permissions'],
                'active' => $cId == 'authitem' && ($aId == 'permissions' || $aId == 'generate' || $aId == 'update')
            ],
            [
                'name'   => 'Роли',
                'url'    => ['/core/rights/authItem/roles'],
                'active' => $cId == 'authitem' && ($aId == 'roles' || $aId == 'create')
            ],
            [
                'name'   => 'Задачи',
                'url'    => ['/core/rights/authItem/tasks']
            ],
            [
                'name'   => 'Операции',
                'url'    => ['/core/rights/authItem/operations']
            ]
        ]]) ?>
</div>

<div>
	<?=$content?>
</div>

<?php $this->endContent(); ?>