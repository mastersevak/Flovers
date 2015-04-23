<?
if(!isset($type)) $type = 'grid'; 
$alias = 'application.modules.core.widgets.uploader.views';
?>
<?if($showTypeLink):?>
	<ul data-id="<?=$model->id?>" class="style_chooser clearfix">

		<li><?=CHtml::link('&nbsp;', '#photos_grid', array('class'=>'btn btn_grid2 btn_orange', 
			'title'=>t('property','Show Grid'), 'data-item'=>'photos_grid'));?></li>
		<li><?=CHtml::link('&nbsp;', '#photos_list', array('class'=>'btn btn_list2', 
			'title'=>t('property','Show List'), 'data-item'=>'photos_list'));?></li>
	</ul>
<?endif?>

<?php $this->controller->renderPartial("$alias._photos", compact('files', 'model', 'params', 'css', 'bigSize')) ?>


<?php $this->controller->renderPartial("$alias._editfiles", compact('files', 'model', 'bigSize')) ?>

