<section class="top">
	<div class="big-baner">
		<a href="#"><img src="<?=$this->assetsUrl?>/images/placeholders/banner-carre-3.jpg" class="full">
		<!-- <div class="wrapper rel">
			<div>
				<p class="t1">Hasmik Zakaryan</p>
				<p class="t2">"Rings"</p>					
			</div>
		</div> -->
		</a>
	</div>
</section>
<!-- end top -->

<div class="wrapper">	
	<div>
		<div class="add-work mb20">
			<?=CHtml::link(t('front', 'Добавить работу'), ['productcreate'], ['class' => 'btn btn-lg yellow fright ml20'])?>
			
			<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod
			tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,
			quis nostrud exercitation ullamco laboris nisi ut aliquip.</p>
		</div>
		
		<!-- item list -->
		<ul class="titles">
			<li data-show-type="<?=Product::SCOPE_SORT_NEW?>" class="active"><a href="#"><?=t('front', 'Новые')?></a></li>
			<li data-show-type="<?=Product::SCOPE_TOP_OF_THE_WEEK?>"><a href="#"><?=t('front', 'Лучшие за неделю')?></a></li>
			<li data-show-type="<?=Product::SCOPE_BEST_SALES?>"><a href="#"><?=t('front', 'Самые продаваемые')?></a></li>
		</ul>

		<br class="clear">

		<? $this->renderPartial('products/_list', compact('model'));?>
	</div>
</div>