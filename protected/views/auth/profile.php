<?$this->renderPartial('/auth/_changePassword', ['model'=> $changePasswordModel]);?>
<?$this->renderPartial('/site/product_create', ['model'=> new Product]);?>

<section class="producttab">

	<div class="tabs-left">
		<?$this->beginWidget('UITabs', ['tabs' => $this->tabs]);?>

			<div class="tab-content-outer">
				<div class="tab-content">
					<div id="about" class="tab-pane fade active in" style="margin-top: 20px">
						<?$form = $this->beginWidget('SActiveForm', [
							'id' => 'profile-form', //объязательно для работы кнопок сохранения, удаления
							'enableAjaxValidation' => true,
							'htmlOptions' => ['enctype'=>'multipart/form-data']
							]);?>

							<div class="col-md-6">
								<div class="contacts-form">
									<div class="wrap-paper" style="min-height: 500px">
										<div class="paper">
											<div class="form-group">
												<span class="icon icon-user"></span>
												<?=$form->textField($model, 'firstname',['class'=>"form-control", 'placeholder'=>t('back', 'Имя')])?>
												<?=$form->error($model,'firstname')?>
											</div>

											<div class="form-group">
												<span class="icon icon-user"></span>
												<?=$form->textField($model, 'lastname',['class'=>"form-control", 'placeholder'=>t('back', 'Фамилия')])?>
												<?=$form->error($model,'lastname')?>
											</div>

											<div class="form-group">
												<span class="icon icon-user"></span>
												<?=$form->textField($model, 'middlename',['class'=>"form-control", 'placeholder'=>t('back', 'Отчество')])?>
												<?=$form->error($model,'middlename')?>
											</div>

											<div class="form-group">
												<?=$form->datePicker($model->profile, 'passport_birthday', [
													'htmlOptions'	=>	[
														'class'=>'form-control',
														'style'=>'width:270px; float: left; margin-right: 8px;  margin-bottom: 7px;',
														]
													]
												)?>
												<?=$form->error($model->profile,'passport_birthday')?>
											</div>

											<div class="form-group">
												<?=$form->dropDownList($model->profile, 'passport_gender',
													Lookup::items('UserGender'), ['empty'=>t('front', 'Выберите пол'),'data-width'=>'300px'])?>
												<?=$form->error($model->profile,'passport_gender')?>
											</div>

											<div class="form-group">
												<span class="icon icon-phone-4"></span>
												<?=$form->textField($model->profile, 'phone', ['class'=>"form-control", 'placeholder'=>t('back', 'Телефон')])?>
												<?=$form->error($model->profile,'phone')?>
											</div>

											<div class="form-group">
												<span class="icon icon-skype-3"></span>
												<?=$form->textField($model->profile, 'skype_name', ['class'=>"form-control", 'placeholder'=>t('back', 'Логин Skype')])?>
												<?=$form->error($model->profile,'skype_name')?>
											</div>

											<div class="form-group">
												<span class="icon icon-bubbles-2"></span>
												<?=$form->textArea($model->profile, 'about', ['class'=>'form-control', 'placeholder'=>t('back', 'Информация')])?>
												<?=$form->error($model->profile,'about')?>
											</div>
										</div>
									</div>
								</div>
							</div>	<!-- class="col-md-6" -->

							<div class="col-md-6">
								<div class="contacts-form">
									<div class="wrap-paper" style="min-height: 500px">
										<div class="paper">
											<!-- avatar -->
											<div class="control-group fleft">
												<span class="field">
													<?$this->widget('Avatar', [
														'form' 			=> $form,
														'model' 		=> $model,
														'field' 		=> 'image',
														'image' 		=> 'avatar',
														'size'			=> 'big',
														'hiddenLink'	=> true,
														'hiddenFile' 	=> true,
														'thumbWidth' 	=> param('images/user/sizes/big/width'),
														'thumbHeight' 	=> param('images/user/sizes/big/height'),
														'bigSize'		=> 'big',
														'alt' 			=> $model->fullname,
													]);?>
												</span>
											</div>

											<a class="btn btn-success btn-mini" href="#" onClick = "$(this).closest('.paper').find('.upload').trigger('click');">
												<i class='fa fa-camera mr5'></i>&nbsp;<?=t('back', 'выбрать картинку')?>
											</a>

											<div>
												<p class="fsize18 c-gray"><?=t('front', "Загрузить фотографию")?></p>
												<p class="fsize13 c-gray"><?=t('front', "Вы можете загрузить изображение в формате JPG, GIF или PNG.")?></p>
											</div>
										</div>
									</div>
								</div>
							</div> <!-- class="col-md-6" -->

						<?$this->endWidget()?>

						<span class="field" style="float: left; margin-right: 20px;  margin-left: 25px;">
							<?=CHtml::htmlButton(CHtml::tag('i', ['class'=>'fa fa-key mr5'], '').t('back', "Сменить пароль"), [
								'class'=>'btn yellow',
								'data-toggle'=>"domodal",
								'data-target'=>"#change-password-modal"
							]);?>
						</span>

						<div class='fright mr20 save'>
							<?$this->widget('UIButtons', [
								'group'	=> 'save',
								'form'	=> 'profile-form',
								'id'	=>	$model->id
							])?>
						</div>
					</div>

					<div id="products" class="tab-pane fade">
						<?=CHtml::button(t('front', "Создать продукт"), [
							'class' => 'btn btn-mega create-product',
							'data-target' => '#product-modal',
							'data-title' => 'Создание продукта',
							'data-model' => 'Product',
							'data-create' => '/site/productcreate',
							'data-update' => '/site/productupdate'
						])?>
						<!-- item list -->
						<?$model = new product;
						$itemView = '_listThumbBig';
						$viewMode = true; ?>
						<?$this->renderPartial('products/_list', compact('model', 'itemView', 'viewMode', 'criteria'))?>
					</div>

					<div id="message" class="tab-pane fade clearfix">
						<?$this->renderPartial('/auth/_messages')?>
					</div>

					<div id="orders" class="tab-pane fade">
						<p>orders</p>
						<p>orders</p>
						<p>orders</p>
						<p>orders</p>
						<p>orders</p>
						<p>orders</p>
						<p>orders</p>
					</div>
				</div>
			</div>

		<?$this->endWidget();?>
	</div>
</section>