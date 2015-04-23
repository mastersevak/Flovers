
<ul class="horlist">
	<?foreach($photos as $one):?>
	<li><?=$one->getThumbnail('thumb', 80)?></li>
	<?endforeach?>
</ul>

<?=CHtml::link('редактировать фотки', array('/core/photo/default/album/update', 'id'=>$photoalbum->id), array('class'=>'edit-album'))?>