<li>
	<div>
		<?=CHtml::link($data->getMainPhoto('small', 280, 204), ['item', 'id' => $data->id]);?>
		<div class="title-wrapper">
			<a href="<?=$this->createUrl('item', ['id' => $data->id])?>" class="title"><?=CHtml::encode($data->title)?></a>
			<a href="<?=$this->createUrl('person', ['id' => $data->id_owner])?>" class="author"><?=Person::listData()[$data->id_owner]?></a>
		</div>

		<div class="bottom">
			<div class="options clearfix">
				<div class="rating">
					<?$this->widget('ext.DzRaty.DzRaty', array(
						'name' => $data->id,
						'value' => $data->ratingsum ? $data->ratingsum : 0,
						'htmlOptions'		=> [
							'data-model-name' => 'ProductRating',
						],
						'options' => array(
							'click' => "js:function(score, evt){
								$.fn.customFrontend('saveRating', score, $(this).data('target'), $(this).data('model-name'), '{$this->createUrl('/site/saverating')}');
							}",
						)
					));?>
				</div>


				<div class="right">
					<? if(!app()->shoppingCart->itemAt(get_class($data).$data->id)): ?>
						<? $linkText = '<i class="fa fa-shopping-cart mr10"></i><span>'.t('front', 'В корзину').'</span>';?>
						<?=CHtml::link($linkText, '#', [
							'class' => 'add-to-cart card fleft',
							'data-id' => $data->id
						]);?>
					<? else: ?>
						<? $linkText = '<i class="fa fa-shopping-cart mr10"></i><span>'.t('front', 'Есть в корзине').'</span>';?>
						<?=CHtml::link($linkText, '#', [
							'class' => 'add-to-cart add card fleft',
							'data-id' => $data->id
						]);?>
					<? endif; ?>
					<a href="#" class="fleft"><i class="fa fa-heart"></i></a>
				</div>
			</div>

			<div class="price">
				<p class="mb0"><?=app()->format->price($data->price);?></p>
			</div>
		</div>
	</div>
</li>