<div class="buttons mb20">
    <?=CHtml::htmlButton('Сгенерировать элементы для действий контроллера', [
        'class'=>'btn btn-cons btn-success btn-small',
        'onclick'=>'UIButtons.gotoUrl(this)',
        'data-url'=>$this->createUrl('authItem/generate')]); 

    $this->widget('UIButtons', ['buttons'=>['deleteselected'=>[
            'onclick'=>'deleteSelectedOperations(this)',
            'data-url'=>$this->createUrl('authItem/deleteSelected')]]]);?>
</div>

<div class="mb20">
        <?php echo Rights::t('core', 'Here you can view and manage the permissions assigned to each role.'); ?><br />
        <?php echo Rights::t('core', 'Authorization items can be managed under {roleLink}, {taskLink} and {operationLink}.', array(
                '{roleLink}'=>CHtml::link(Rights::t('core', 'Roles'), array('authItem/roles')),
                '{taskLink}'=>CHtml::link(Rights::t('core', 'Tasks'), array('authItem/tasks')),
                '{operationLink}'=>CHtml::link(Rights::t('core', 'Operations'), array('authItem/operations')),
        )); ?>
</div>

<div style="overflow:scroll">
    <?php $this->widget('SGridView', array(
        'id'=>'permissions-table',
        'dataProvider'=>$dataProvider,
        'template'=>'{items}{pager}',
        'emptyText'=>Rights::t('core', 'No authorization items found.'),
        'htmlOptions'=>array('class'=>'grid-view permission-table'),
        'showButtonsColumn' => false,
        'columns'=>$columns,
    )); ?>
</div>


<div class="hint"><?php echo Rights::t('core', 'Hover to see from where the permission is inherited.'); ?></div>