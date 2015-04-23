<div class="product-preview">
	<div class="preview scale animated">
		<?=CHtml::link(
			$data->getMainPhoto('big', 270, 328, "img-responsive scaled animated"),
			['item', 'id' => $data->id], ['class' => 'preview-image']);
		?>

		<ul class="product-controls-list">
			<li><span class="label label-new"><?=t('front', 'Новый')?></span></li>
		</ul>

		<? // СКИДКА
		if($data->price && $data->new_price) : ;
			$sale = Product::getProductSale($data->price, $data->new_price);?>
			<ul class="product-controls-list right">
				<li><span class="label label-sale"><?=t('front', 'СКИДКА')?></span></li>
				<li><span class="label">-<?=$sale?>%</span></li>
			</ul>
		<?endif?>

		<ul class="product-controls-list right hide-right">
			<li class="top-out"></li>

			<?if(app()->controller->action->id != 'profile'): ?>
				<li><a href="#" class="circle"><span class="icon-heart"></span></a></li>
				<li>
					<? if(!app()->shoppingCart->itemAt(get_class($data).$data->id)): ?>
						<?=CHtml::link('<span class="icon-basket"></span>', '#', [
							'class' => 'add-to-cart card cart fleft',
							'data-id' => $data->id
						]);?>
					<? else: ?>
						<?=CHtml::link('<span class="icon-basket"></span>', '#', [
							'class' => 'add-to-cart add cart card fleft',
							'data-id' => $data->id
						]);?>
					<? endif; ?>
				</li>
			<?endif?>
		</ul>

		<? // quick-view
		if(app()->controller->action->id != 'profile'): ?>
			<a href="_ajax_view-product.html" class="quick-view hidden-xs">
				<span class="rating">
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
				</span>
				<span class="icon-zoom-in-2"></span> <?=t('front', 'Быстрый просмотр')?>
			</a>
		<?endif?>

		<div class="quick-view visible-xs">
			<span class="rating">
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
			</span>
		</div>
	</div>

	<h3 class="title">
		<a href="<?=$this->createUrl('item', ['id' => $data->id])?>"><?=CHtml::encode($data->title)?></a>
		<?if(app()->controller->action->id == 'profile'): ?>
			<a class="update" title="" data-url="<?=$this->createUrl('/site/prepareupdate')?>" data-action="<?=$this->createUrl('/site/productupdate')?>" data-title='Редактирование продукта' data-target='#product-modal' data-model="Product" data-id="<?=$data->id?>" rel="tooltip" href="#" data-original-title="Редактировать"><i class="fa fa-pencil"></i></a>
			<a class="delete" title="" data-url="<?=$this->createUrl('/site/deleteproduct')?>" data-id="<?=$data->id?>" rel="tooltip" href="#" data-original-title="Удалить"><i class="fa fa-trash-o"></i></a>
		<?endif?>
	</h3>
	<?if($data->new_price):?>
		<span class="price old"><?=app()->format->price($data->price);?></span>
		<span class="price new"><?=app()->format->price($data->new_price);?></span>
	<?else:?>
		<span class="price new"><?=app()->format->price($data->price);?></span>
	<?endif?>
</div>
