<li>
	<div class="product">

		<?=CHtml::link(
			$data->getMainPhoto('small', 200, 150, "img-responsive animate scale"),
			['item', 'id' => $data->id], ['class' => 'preview-image']);
		?>
		<p class="name">
			<a href="<?=$this->createUrl('item', ['id' => $data->id])?>" class="preview-image"><?=CHtml::encode($data->title)?></a>
		</p>
		<?$this->widget('ext.DzRaty.DzRaty', array(
			'name' => $data->id,
			'value' => $data->ratingsum ? $data->ratingsum : 0,
			'options' => array(
				'click' => "js:function(score, evt){
					$.fn.main('saveRating', score, $(this).data('target'), '{$this->createUrl('saverating')}');
				}",
			)
		));?>
		<span class="price"><?=app()->format->price($data->price);?></span>
	</div>
</li>
