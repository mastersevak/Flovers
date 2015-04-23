<?$url = $this->createUrl('brand', ['id'=>$data->id]);?>

<li>
	<a href="<?=$url?>"><?=$data->getThumbnail('big', 285, 165)?></a>

	<div class="bottom">
		<p class="title"><?=$data->name?></p>
		<a href="<?=$url?>" class="more title"><?=t('front', 'Подробнее')?></a> 
	</div>
</li>