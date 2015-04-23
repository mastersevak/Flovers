<div class="wrapper">
	<?$this->renderPartial('_breadcrumbs');?>

	<section class="brand-info white-bg">
		<div class="dc1">
			<div>
				<img class="logo" src="<?=($model->photo) ? $model->photo->getImageUrl('big') : $this->assetsUrl.'/images/placeholders/logo22.jpg'?>">

				<p class="title"><?=$model->name?></p>
				<?= $model->address ? "<p>Address: $model->address</p>" : '' ?>
				<?= $model->phone ? "<p class='mb10'>Phone.: $model->phone</p>" : ""?>

				<div class="rating mr20 fleft">
					<a href="#"></a>
					<a href="#"></a>
					<a href="#"></a>
					<a href="#"></a>
					<a href="#"></a>
				</div>
			</div>
		</div>

		
	</section>
	<section class="dc2">
		<?$this->renderPartial('brands/_map', compact('model'))?>
		
		<div class="descr mt20"><?=$model->about?></div>
	</section>

	<br class="clear">

	<div class="bg-title mt20"><h2><?=t('front', 'Работы')?></h2></div>

	<!-- item list -->
	<?$this->renderPartial('products/_list', compact('dataProvider'))?>
</div>