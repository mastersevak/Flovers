<?$modal = $this->beginWidget('UIModal',[
	'id' 	=> 'product-update-modal',
	'width' => 400,
	'title' => 'РЕДАКТИРОВАНИЕ ПРОДУКТА',
	]);?>

	<?$files = $model->photos;
	$params  = 'product';?>

	<div class="create-product">
		<h1 class="mt0 mb0"><?=t('front','Редактирование продукта')?></h1>
		<div>
			<?$form = $this->beginWidget('SActiveForm', [ // form
				'id'=>'product-form',
				'enableAjaxValidation'=>true,
				'enableClientValidation'=>true,
				'action' => $this->createUrl('editProduct',['id' => $model->id]),
				'clientOptions' => [
					'validateOnSubmit'=>true,
				],
				'htmlOptions'=>[
					'enctype'=>'multipart/form-data'
				]
				])?>

				<div class="clearfix mb5">
					<div class="fleft mr10">
						<?=$form->dropDownList($model, 'collectionsFilter', ProductCollection::listData(), [
							'empty'			=> 'Коллекции',
							'data-search'	=> true,
							'data-width' 	=> 132,
						])?>
						<?=$form->error($model, 'collectionsFilter')?>
					</div>

					<div class="fleft mr10">
						<?=$form->dropDownList($model, 'id_brand', ProductBrand::listData(), [
							'empty'			=> 'Бренды',
							'data-search'	=> true,
							'data-width' 	=> 132,
						])?>
						<?=$form->error($model, 'id_brand')?>
					</div>

					<div class="fleft mr10">
						<?=$form->dropDownList($model, 'id_category', ProductCategory::listData()['list'], [
							'empty'			=> 'Категория',
							'data-search'	=> true,
							'data-width' 	=> 132,
							'options' 		=> Treelist::listOptions(ProductCategory::listData()['levels'])
						])?>
						<?=$form->error($model, 'id_category')?>
					</div>

					<div class="fleft">
						<?=$form->dropDownList($model, 'id_material', ProductMaterial::listData(), [
							'empty'			=> 'Материал',
							'data-search'	=> true,
							'data-width' 	=> 132,
						])?>
						<?=$form->error($model, 'id_material')?>
					</div>
				</div>

				<div class="clearfix mb10">
					<div class="title fleft mr10">
						<?=$form->textField($model,'title',['placeholder'=>"Название"])?>
						<?=$form->error($model,'title')?>
					</div>

					<div class="price fleft mr10">
						<?=$form->textField($model, 'price',['placeholder'=>"Цена"])?>
						<?=$form->error($model, 'price')?>
					</div>

					<div class="size fleft">
						<?=$form->textField($model, 'size', ['placeholder'=>"Размер"])?>
						<?=$form->error($model, 'size')?>
					</div>
				</div>
				<div>
					<?=$form->textarea($model, 'description',['placeholder'=>"Описабие"])?>
					<?=$form->error($model, 'description')?>
				</div>

				<div class="mt10">
					<?$showTypeLink = false;
					$this->widget('Uploader', compact('files', 'model', 'params','showTypeLink')) ?>
				</div>

				<div>
				<?$this->widget('UIButtons', ['group'=>'save', 'form'=>'product-form', 'id'=>$model->id])?>
					<?//=CHtml::submitButton('Сохранить',['class' =>'btn btn-lg blue']);?>
				</div>
			<?$this->endWidget(); // form ?>
		</div>
	</div>

<?$this->endWidget(); // modal?>