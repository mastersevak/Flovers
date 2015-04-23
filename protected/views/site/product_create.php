<?$modal = $this->beginWidget('UIModal',[
	'id' 	=> 'product-modal',
	'width' => 600,
	'title' => 'Создание продукта',
]);
	$form = $this->beginWidget('SActiveForm', [
		'modal' => true,
		'action' => ['/site/productcreate'],
		'enableAjaxValidation'=>true,
		'clientOptions' => [
			'validateOnChange' => false,
		],
		'htmlOptions'=>[
			'enctype'=>'multipart/form-data',
		],
		'afterModalClose' => 'function(data){
			$.fn.yiiListView.update("products-list");
			if ($("#product-modal").find("#Product_status").val() == 2) {
				$("#product-modal").find("#Product_status").val(0);
			}
		}'
	]);
	$modal->header();?>
	<div class="create-product" data-delete-url="<?=$this->createUrl('/site/deleteproduct')?>">
		<div>
			<p class="note"><?=t('front', 'Fields with * are required.')?></p>
					<?=$form->hiddenField($model, 'id')?>
					<?=$form->hiddenField($model, 'status')?>
					<?=$form->hiddenField($model, 'id_owner')?>
				<div class="clearfix mb5">
					<div class="fleft mr10">
						<?=CHtml::dropDownList('ProductCollection', $model->collections, ProductCollection::listData(),[
							'title'			=> 'Коллекции',
							'data-search'	=> true,
							'multiple' => true,
							'data-width' 	=> 132,
						]);?>
						<?=$form->error($model, 'collections')?>
					</div>

					<div class="fleft mr10">
						<?=$form->dropDownList($model, 'id_brand', ProductBrand::listData(), [
							'empty'			=> t('front', 'Бренды'),
							'data-search'	=> true,
							'data-width' 	=> 132,
						])?>
						<?=$form->error($model, 'id_brand')?>
					</div>

					<div class="fleft mr10">
						<?=$form->dropDownList($model, 'id_category', ProductCategory::listData()['list'], [
							'empty'			=> t('front', 'Категория'),
							'data-search'	=> true,
							'data-width' 	=> 132,
							'options' 		=> Treelist::listOptions(ProductCategory::listData()['levels'])
						])?>
						<?=$form->error($model, 'id_category')?>
					</div>

					<div class="fleft">
						<?=$form->dropDownList($model, 'id_material', ProductMaterial::listData()['list'], [
							'empty'			=> t('front', 'Материал'),
							'data-search'	=> true,
							'data-width' 	=> 132,
							'options' 		=> Treelist::listOptions(ProductMaterial::listData()['levels'])
						])?>
						<?=$form->error($model, 'id_material')?>
					</div>
				</div>

				<div class="clearfix mb10">
					<div class="title fleft mr10">
						<?=$form->textField($model,'title',['placeholder'=>t('front', 'Название')])?>
						<?=$form->error($model,'title')?>
					</div>

					<div class="price fleft mr10">
						<?=$form->textField($model, 'price',['placeholder'=>t('front', 'Цена')])?>
						<?=$form->error($model, 'price')?>
					</div>

					<div class="size fleft">
						<?=$form->textField($model, 'size', ['placeholder'=>t('front', 'Размер')])?>
						<?=$form->error($model, 'size')?>
					</div>
				</div>
				<div>
					<?=$form->textarea($model, 'description',['placeholder'=>t('front', 'Описание')])?>
					<?=$form->error($model, 'description')?>
				</div>

				<div class="mt10 image-uploader">
					<?$showTypeLink = false;
					$this->widget('Uploader', compact('files', 'model', 'params','showTypeLink')) ?>
				</div>
		</div>
	</div>
	<?$modal->footer();
	$this->endWidget();
$this->endWidget();?>