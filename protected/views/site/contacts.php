<section class="container contacts">
	<div class="row">
		<section class="col-md-4 col-lg-4">

			<h3><?=t('front', 'Связаться с нами')?></h3>

			<ul class="simple-list compressed-list address">
				<li><? // address ?>
					<span class="icon icon-house"></span>
					<?$address = Lookup::items('frontend-address');?>
					<?=isset($address[1])? $address[1] : ''?>
				</li>
				<li><? // phone number ?>
					<span class="icon icon-phone-4"></span>
					<?=isset(param('settings')['phone']) ? param('settings')['phone'] : ''?>
				</li>
				<li><? // emaile ?>
					<span class="icon icon-envelop"></span>
					<a href="mailto:<?=isset(param('settings')['clientEmail']) ? param('settings')['clientEmail'] : '#'?>">
						<?=isset(param('settings')['clientEmail']) ? param('settings')['clientEmail'] : ''?>
					</a>
				</li>
				<li><? // skype login ?>
					<span class="icon icon-skype-2"></span>
					<a href="#"><?=isset(param('settings')['skype']) ? param('settings')['skype'] : ''?></a>
				</li>
			</ul>

			<div class="map">
				<iframe src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d6115.684863819771!2d-82.9719195443651!3d39.96727545833253!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0xf3846176f3dff5ed!2sLa+Aurora!5e0!3m2!1sen!2sus!4v1416911994304" class="google-map-big"></iframe>
			</div>
		</section>


		<section class="col-md-8 col-lg-8">
			<div class="contacts-form">
				<div class="wrap-paper">
					<div class="paper">
						<h3><?=t('front', 'Контактная форма')?></h3>

						<?if(user()->hasFlash('ContactForm') && user()->hasFlash('ContactForm') == 'succecss'):?>
							<div class="success_message">
								<h3><?=t('front', 'Спасибо! Ваше сообщение отправлено.');?></h3>
								<p><?=t('front', 'Мы постараемся ответить Вам в ближайшее время.');?></p>
							</div>
						<?else:?>

							<?$form = $this->beginWidget('SActiveForm', [
								'id' => 'contacts-form', //объязательно для работы кнопок сохранения, удаления
								'enableAjaxValidation' => true,
								])?>
								<div class="form-group">
									<span class="icon icon-user"></span>
									<?=$form->textField($model, 'firstname',['class'=>"form-control", 'placeholder'=>t('front', 'Имя')])?>
									<?=$form->error($model,'firstname')?>
								</div>

								<div class="form-group">
									<span class="icon icon-phone-4"></span>
									<?=$form->textField($model, 'phone', ['class'=>"form-control", 'placeholder'=>t('front', 'Телефон')])?>
									<?=$form->error($model,'phone')?>
								</div>

								<div class="form-group">
									<span class="icon icon-envelop"></span>
									<?=$form->textField($model, 'email', ['class'=>"form-control", 'placeholder'=>t('front', 'Эл. почта')])?>
									<?=$form->error($model,'email')?>
								</div>

								<div class="form-group">
									<span class="icon icon-bubbles-2"></span>
									<?=$form->textArea($model, 'message', ['class'=>'form-control', 'placeholder'=>t('back', 'Информация')])?>
									<?=$form->error($model,'message')?>
								</div>

								<?$this->widget('UIButtons', ['buttons' => [
									'custom' => [
										'value'		=> t('back', 'ОТПРАВИТЬ СООБЩЕНИЕ'),
										'icon'		=> '',
										'options'	=> [
											'class'		=> 'btn btn-mega',
											'data-form' => 'contacts-form',
											'type'		=> 'submit',
											'onclick'	=> 'UIButtons.save(this); return false;'
										]
									]]
								]);?>
							<?$this->endWidget()?>
						<?endif?>
					</div>

					<div class="clearfix"></div>
				</div>
				<img class="back" src="<?=$this->assetsUrl?>/images/contacts-back.png" alt="">
			</div>
		</section>
	</div>
</section>
