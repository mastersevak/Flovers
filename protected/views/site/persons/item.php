<?$this->renderPartial('//layouts/parts/_breadcrumbs');?>
<?$this->renderPartial('persons/_sendMessageModal', ['model' => $model]);?>

<section class="container">
	<div class="member-info col-sm-6 col-md-6 col-lg-6">
		<div class="row">
			<div class="col-sm-12 col-md-6 col-lg-6">
				<div class="photo">
					<?=$model->getThumbnail('custom', 175, false);?>
				</div>
			</div>
			<div class="col-sm-12 col-md-6 col-lg-6">
				<div class="name"><strong><?=Person::listData()[$model->id];?></strong></div>
				<div class="about"><?=String::truncate($model->profile->about, 200)?></div>
				<a class="contact-icon open-message-modal" data-id=<?=$model->id?> href="#"><span class="icon-envelop"></span></a> 
			</div>
		</div>
	</div>
</section>
<section>
	<div class="grey-container">
		<div class="container">
			<div class="col-lg-9"><?=$model->profile->about?></div>
		</div>
	</div>
</section>
<?if($dataProvider->totalItemCount):?>
<section>
	<div class="container content">
		<h3><?=t('front', 'Работы мастера')?></h3>
		<div class="">
			<?$this->renderPartial('products/_list', ['dataProvider' => $dataProvider, 'itemView' => '_listThumbMedium'])?>
		</div>
	</div>
</section>
<?endif?>




