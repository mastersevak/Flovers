<section class="container content nopad-bottom">
	<div class="row">
		<!-- Featured Products -->
		<?$featuredproducts = ShortcutGroup::getByTag('featured', 5);?>
		<?if(isset($featuredproducts[1]['shortcuts']) && $featuredproducts[1]['shortcuts']):?>
			<section class="col-sm-12 col-md-6 col-lg-6  slider-products module">
				<h3><?=t('front', 'РЕКОМЕНДУЕМЫЕ ТОВАРЫ')?></h3>
				<!-- Products list -->
				<?$this->widget('hends.widgets.slideritems.SliderItems', [
					'ids' => $featuredproducts[1]['shortcuts'],
					'sliderType' => 'big'
				]);?>
				<!-- //end Products list -->
			</section>
		<?endif;?>
		<!-- //end Featured Products -->

		<!-- On Sale -->
		<?$onsaleproducts = ShortcutGroup::getByTag('onsale', 5);?>
		<?if(isset($onsaleproducts[2]['shortcuts']) && $onsaleproducts[2]['shortcuts']):?>
			<section class="col-sm-6 col-md-3 col-lg-3  module">
				<h3><a href="#"><?=t('front', 'В ПРОДАЖЕ')?></a></h3>

				<?$this->widget('hends.widgets.slideritems.SliderItems', [
					'ids' => $onsaleproducts[2]['shortcuts'],
					'sliderType' => 'small'
				]);?>
			</section>
		<?endif;?>
		<!-- //end On Sale -->

		<!-- Blog Widget Small -->

		<?$news = DynamicList::getItems('poslednie-novosti', 'News');?>
		<?if($news):?>
			<section class="col-sm-6 col-md-3 col-lg-3 blog-widget-small module">
				<h3><?=t('front', 'Новости')?></h3>
				<?$news = $news->data;?>
				<?$this->widget('hends.widgets.slideritems.SliderItems', [
					'models' => $news,
					'sliderType' => 'news'
				]);?>
			</section>
		<?endif;?>

		<!-- //end Blog Widjet Small -->
	</div>
	<div class="row">
		<!-- Best Sellers -->
		<?$bestsellersproducts = ShortcutGroup::getByTag('bestsellers', 5);?>
		<?if(isset($bestsellersproducts[3]['shortcuts']) && $bestsellersproducts[3]['shortcuts']):?>
			<section class="col-sm-6 col-md-3 col-lg-3 module">
					<h3><a href="#"><?=t('front', 'ХИТЫ ПРОДАЖ')?></a></h3>

					<?$this->widget('hends.widgets.slideritems.SliderItems', [
						'ids' => $bestsellersproducts[3]['shortcuts'],
						'sliderType' => 'small'
					]);?>
			</section>
		<?endif;?>
		<!-- //end Best Sellers -->
		<!-- About Us -->
		<section class="col-sm-6 col-md-3 col-lg-3 module">
			<h3 class="upper"><?=t('front', 'О НАС')?></h3>
			<?=Block::getBlock('about-us')?>
		</section>
		<!-- //end  About Us -->

		<!-- New Products -->
		<?$newproducts = DynamicList::getItems('novinki', 'Product');?>
		<?if($newproducts):?>
			<section class="col-sm-12 col-md-6 col-lg-6  slider-products  module">
				<!-- Products list -->
				<?$newproducts = $newproducts->data;?>
				<h3><?=t('front', 'НОВИНКИ')?></h3>
				<?$this->widget('hends.widgets.slideritems.SliderItems', [
					'models' => $newproducts,
					'sliderType' => 'big'
				]);?>
				<!-- //end Products list -->
			</section>
		<?endif;?>
		<!-- //end New Products -->

	</div>
	<!-- Product view compact -->
	<div class="product-view-ajax">
		<div class="ajax-loader progress progress-striped active">
			<div class="progress-bar progress-bar-danger" role="progressbar"></div>
		</div>
		<div class="layar"></div>
		<div class="product-view-container"></div>
	</div>
	<!-- //end Product view compact -->
</section>