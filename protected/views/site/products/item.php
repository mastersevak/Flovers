
<div class="product-view row">
	<div class="col-sm-6 col-md-5 col-lg-5">
		<?
		$photos = $model->photos;
		$srcBigImage = $photos ? $photos[0]->getImageUrl('big') : $this->assetsUrl.'/images/placeholders/product-02-01-593x722.jpg';
		$srcBiggerImage = $photos ? $photos[0]->getImageUrl('bigger') : $this->assetsUrl.'/images/placeholders/product-02-01-888x1080.jpg'; ?>

		<div class="hidden-xs flexslider-thumb-vertical-outer">
			<div class="flexslider flexslider-thumb-vertical vertical min">
				<ul class="previews-list slides">
					<? foreach ($photos as $key => $photo) : ?>
						<?
						$dataCloudzoom  = "useZoom: '.cloudzoom', image: '".$photo->getImageUrl('big')."', zoomImage: '".$photo->getImageUrl('bigger')."'";
						$dataCloudzoom .= ($key == 0) ? ", autoInside : 991" : "" ?>
						<li>
							<?// src: 76x92, image: 593x722, zoomImage: 888x1080
							echo CHtml::image($photo->getImageUrl('small'), "#", [
								"class" => "cloudzoom-gallery",
								"data-cloudzoom" => $dataCloudzoom,
							])
							?>
						</li>
					<? endforeach ?>
					<!-- <li><a class="various fancybox.iframe" href="http://www.youtube.com/embed/L9szn1QQfas?autoplay=1"><img alt="#" class='fancybox-video' src = "images/video.png" ></a></li> -->
				</ul>
			</div>
		</div>

		<div class="large-image vertical hidden-xs">

			<? //src: 593x722, zoomImage: 888x1080
			echo CHtml::image($srcBigImage, '#', [
				"class" => "cloudzoom",
				"data-cloudzoom" => "zoomImage: '".$srcBiggerImage."', autoInside : 991",
			])
			?>
		</div>

		<div class="flexslider flexslider-large visible-xs">
			<ul class="slides">
				<? foreach ($photos as $photo) : ?>
					<li>
						<?=CHtml::image($photo->getImageUrl('big'), '', ['class' => 'img-responsive animate scale'])?>
					</li>
				<? endforeach ?>
			</ul>
		</div>

	</div>
	<div class="col-sm-6 col-md-4 col-lg-4">

		<div class="product-description">
			<div class="product-label">
				<h2><?=$model->title?></h2>

				<? // product price
				if($model->new_price || $model->sale) : ?>
					<span class="price old"><?=app()->format->price($model->price)?></span>

					<?if(!$model->new_price)
						$newPrice = Product::getProductNewPrice($model->price, $model->sale);
					$newPrice = (!$model->new_price) ? $newPrice : $model->new_price;
					?>
					<span class="price new"><?=app()->format->price($newPrice)?></span><br>
				<?else:?>
					<span class="price new"><?=app()->format->price($model->price)?></span> <br>
				<?endif?>
			</div>

			<div class="rating clearfix mt20">

				<?$this->widget('ext.DzRaty.DzRaty', array(
					'name' => $model->id,
					'value' => $model->ratingsum ? $model->ratingsum : 0,
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

			<form>
				<div class="row">
					<div class="col-lg-6">
						<?=CHtml::link('<i class="fa fa-heart mr10">&nbsp;'.'</i>'.t('front', 'Избранные'), '#', [])?>
						<!-- <div class="product-options"></div> -->
					</div>
				</div>
				<br>
				<div class="cart-position-quantity option" data-id="<?=$model->id?>"> <b><?=t('front', 'Количество:')?></b>
					<div class="input-group quantity-control">
						<?=CHtml::link('&minus;', '#', [
							'class' => 'decrease-qty input-group-addon',
							'data-update' => false
						])?>
						<?=CHtml::textField('', 1, ['class' => 'position-qty form-control'])?>
						<?=CHtml::link('+', '#', [
							'class' => 'increase-qty input-group-addon',
							'data-update' => false
						])?>
					</div>
				</div>
				<div class="clearfix visible-xs"></div>
				<? if(!app()->shoppingCart->itemAt(get_class($model).$model->id)): ?>
					<?=CHtml::link('<span>'.t('front', 'В корзину').'</span>', '#', [
						'class' => 'add-to-cart btn btn-mega btn-lg',
						'data-id' => $model->id,
						'data-update-text' => true
					]);?>
				<? else: ?>
					<?=CHtml::link('<span>'.t('front', 'Есть в корзине').'</span>', '#', [
						'class' => 'add-to-cart add btn btn-mega btn-lg',
						'data-id' => $model->id,
						'data-update-text' => true
					]);?>
				<? endif; ?>
			</form>

			<div class="panel-group accordion-simple" id="product-accordion">

				<!-- <div class="panel">
					<div id="product-size" class="panel-collapse collapse">
						<div class="panel-body">
							<p class="mb20"> #<?//=$model->id?> </p>

							<?// if($model->size) : ?>
								<p class="mb5"><b>размер: </b><?//=$model->size?></p>
							<?// endif ?>

							<?// if($model->id_material && isset(ProductMaterial::listData()['list'][$model->id_material])) : ?>
								<p class="mb5"><b>материал: </b><?//=ProductMaterial::listData()['list'][$model->id_material]?></p>
							<?// endif ?>

							<?// if($model->id_brand && isset(ProductBrand::listData()[$model->id_brand])) : ?>
								<p class="mb5"><b>бренд: </b><?//=ProductBrand::listData()[$model->id_brand]?></p>
							<?// endif ?>

							<?// if($model->id_collection && isset(ProductCollection::listData()[$model->id_collection])) : ?>
								<p class="mb5"><b>коллекция: </b><?//=ProductCollection::listData()[$model->id_collection]?></p>
							<?// endif ?>
						</div>
					</div>
				</div> -->
				<div class="panel">
					<div class="panel-heading">
						<a data-toggle="collapse" data-parent="#product-accordion" href="#product-with" class="collapsed">
							<span class="arrow-down icon-arrow-down-4"></span>
							<span class="arrow-up icon-arrow-up-4"></span>
							<?=t('front', 'Автор')?>
						</a>
					</div>
					<div id="product-with" class="panel-collapse collapse">
						<div class="panel-body clearfix">
							<?=CHtml::link($model->person->getThumbnail('custom', 110), ['/site/person', 'id' => $model->id_owner], ['style' => 'float:left; margin-right:20px'])?>

							<div>
								<a href="<?=$this->createUrl('/site/person', ['id' => $model->id_owner])?>" class="title mb5 iblock">
									<?= isset(Person::listData()[$model->id_owner]) ? Person::listData()[$model->id_owner] : ''?>
								</a>
								<p><?//=$model->person->profile->about?></p>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- Related Products -->
	<section class="col-sm-12 col-md-3 col-lg-3 slider-products module">
		<h3><?=t('front', 'Похожие')?></h3>
		<?
		$this->widget('hends.widgets.slideritems.SliderItems', [
			'models' => Product::getRelatedProducts($model),
			'sliderType' => 'small'
		]);
		?>
	</section>
	<!-- //end Related Products -->
</div>
<!-- Services -->
	<section class="services-block single small row">
		<div class="col-xs-12 col-sm-6 col-md-3 col-lg-3 divider-right">
			<a href="#" class="item">
				<span class="icon icon-tags-2"></span>
				<div class="text">
					<span class="title"><?=t('front', 'Специальное предложение 1 + 1 = 3')?></span>
					<span class="description"><?=t('front', 'Получить в подарок!')?></span>
				</div>
			</a>
		</div>

		<div class="col-xs-12 col-sm-6 col-md-3 col-lg-3 divider-right">
			<a href="#" class="item">
			<span class="icon icon-credit-card"></span>
			<div class="text">
				<span class="title"><?=t('front', 'Бесплатно награду карты')?></span>
				<span class="description"><?=t('front', 'Стоимость')?> 10$, 50$, 100$</span>
			</div>
			</a>
		</div>
		<div class="col-xs-12 col-sm-6 col-md-3 col-lg-3 divider-right">
			<a href="#" class="item">
				<span class="icon icon-users-2"></span>
				<div class="text">
					<span class="title"><?=t('front', 'Присоединяйтесь нашего клуба')?></span>
				</div>
			</a>
		</div>
		<div class="col-xs-12 col-sm-6 col-md-3 col-lg-3 divider-right">
			<a href="#" class="item">
				<span class="icon icon-truck"></span>
				<div class="text">
					<span class="title"><?=t('front', 'Бесплатная Доставка')?></span>
				</div>
			</a>
		</div>
	</section>
<!-- //end Services -->

	<? //Comments
	$this->widget('comment.widgets.SiteComments', ['dataProvider'=>$comments, 'type'=>COMMENT::TYPE_PRODUCT, 'objectId'=>$model->id]);?>

<section class="slider-products content-box">
	<?//$this->widget('hends.widgets.recentlyviewed.RecentlyViewed')?>
</section>