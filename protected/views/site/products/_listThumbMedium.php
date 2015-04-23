<div class="product-preview">
	<div class="preview animate scale animated">
		<?=CHtml::link(
			$data->getMainPhoto('medium', 190, 230, "img-responsive animate scale animated"), 
			['item', 'id' => $data->id], ['class' => 'preview-image']);
		?>
		<ul class="product-controls-list right hide-right">
			<li class="top-out-small"></li>
           	<li><a href="#" class="circle"><span class="icon-heart"></span></a></li>
			<li>
				<? if(!app()->shoppingCart->itemAt(get_class($data).$data->id)): ?>
					<?=CHtml::link('<span class="icon-basket"></span>', '#', [
						'class' => 'add-to-cart card cart fleft',
						'data-id' => $data->id,
						'data-update-text' => false
					]);?>
				<? else: ?>
					<?=CHtml::link('<span class="icon-basket"></span>', '#', [
						'class' => 'add-to-cart add cart card fleft',
						'data-id' => $data->id,
						'data-update-text' => false
					]);?>
				<? endif; ?>
			</li>
		</ul>

		<a href="#" class="quick-view">
			<div class="rating">
				<?$this->widget('ext.DzRaty.DzRaty', [
					'name' => "first_".$data->id,
					'value' => $data->ratingsum ? $data->ratingsum : 0,
					'htmlOptions' => [
						'data-model-name' => 'ProductRating',
						'data-url' => $this->createUrl('/site/saverating'),
					],
					'options' => [
						'click' => "js:function(score, evt){
							$.fn.customFrontend('saveRating', score, $(this).data('target'), $(this).data('model-name'), '{$this->createUrl('/site/saverating')}');
						}",
					]
				]);?>
			</div>
		</a>
	</div>
	<h3 class="title"><a href="<?=$this->createUrl('item', ['id' => $data->id])?>" class="preview-image"><?=CHtml::encode($data->title)?></a></h3>

	<? // pricees ?>
	<? if($data->new_price || $data->sale) : ?>
		<span class="price old"><?=app()->format->price($data->price);?></span>

		<?if(!$data->new_price)
			$newPrice = Product::getProductNewPrice($data->price, $data->sale);
		$newPrice = (!$data->new_price) ? $newPrice : $data->new_price;
		?>

		<span class="price new"><?=app()->format->price($newPrice);?></span><br>
	<?else :?>
		<span class="price new"><?=app()->format->price($data->price);?></span>
	<?endif?>

	<? // rating ?>
	<div class="list_rating">
		<span class="rating">
			<?$this->widget('ext.DzRaty.DzRaty', [
				'name' => "second_".$data->id,
				'value' => $data->ratingsum ? $data->ratingsum : 0,
				'htmlOptions' => [
					'data-model-name' => 'ProductRating',
					'data-url' => $this->createUrl('/site/saverating'),
					'data-runer' => CJavaScript::encode([
							'click' => "js:function(score, evt){
								$.fn.customFrontend('saveRating', score, $(this).data('target'), $(this).data('model-name'),  $(this).data('url'));
							}", 'score' => $data->ratingsum ? $data->ratingsum : 0, 'target' => "#". "second_".$data->id])
				],
				'options' => [
					'click' => "js:function(score, evt){
						$.fn.customFrontend('saveRating', score, $(this).data('target'), $(this).data('model-name'),  $(this).data('url'));
					}",
				]
			]);?>

		</span>
	</div>

    <div class="list_description">
    	<?=String::truncate($data->description);?>
    </div>

	<!--buttons-->
	<div class="list_buttons">
		<? if(!app()->shoppingCart->itemAt(get_class($data).$data->id)): ?>
			<?=CHtml::link('<span>В корзину</span>', '#', [
				'class' => 'add-to-cart btn btn-mega pull-left',
				'data-id' => $data->id,
				'data-update-text' => true
			]);?>
		<? else: ?>
			<?=CHtml::link('<span>Есть в корзине</span>', '#', [
				'class' => 'add-to-cart add btn btn-mega pull-left',
				'data-id' => $data->id,
				'data-update-text' => true
			]);?>
		<? endif; ?>
		<div class="add-to-links">
			<ul>
				<li> <a  href="#"><i class="icon-heart"></i></a> <a  href="#">Список пожеланий</a> </li>
			</ul>
		</div>
	</div>
	
</div>
