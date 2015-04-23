<div id="photos_list_<?=$model->id?>" class="photos_tabs photos_list" style="display:none" 
		data-url="<?php echo Yii::app()->createUrl('/core/photo/default/changetitle');?>">
	<ul class="photos editable clearfix">
	<?foreach ($files as $key=>$file):?>
		<li>
			<a href="<?=$file->getImageUrl($bigSize)?>" rel="gallery_small" class="fancybox"
				title="<?=$file->title?>" data-big-size="<?=$bigSize?>">
				<?php echo $file->getThumbnail('thumb') ?>
			</a>
			<div><textarea type="text" data-id="<?=$file->id?>" data-type="title"
				placeholder="Название"><?=$file->title?></textarea></div>
		</li>
	<?endforeach?>
	</ul>
</div>
