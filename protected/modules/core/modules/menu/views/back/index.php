<?=$this->renderPartial('_formRootModal', compact('model'));?>
<?=$this->renderPartial('_formItemsModal', compact('model'));?>
<div id="buttonsDiv" class="mb10">
    <?=CHtml::link('Создать меню',"#", array('class'=>'btn btn-success btn-small', 'id' => 'createMenu'));?>
</div>

<div id="rootDiv" class="w300 posrel" data-url="<?=$this->createUrl('/core/menu/back/getInfo')?>">
    <?php 
        foreach($data as $item){ ?>
            <div data-id="<?=$item->id;?>" class="pointer getMenu">
                <a href="#"><?=$item->name;?></a>
            </div>
    <? } ?>
</div>

<?=CHtml::hiddenField('' , '', array('id' => 'getGeneralRoot'));?>
<div id="treeDiv" class="posabs tree-wrapper">
    <?=$this->renderPartial('tree', compact('treeArray'));?>   
</div>






