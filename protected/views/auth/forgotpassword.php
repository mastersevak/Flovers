
<div class="wrap success_message mt50">
	<h1 class="headers hl bb"><?=$this->pageTitle?></h1>
	
	<?if(user()->hasFlash('email-sent')):?>
		<?= user()->getFlash('email-sent'); ?>
	<?elseif(user()->hasFlash('password-changed')):?>
		<p><?=user()->getFlash('password-changed');?></p>
	<?else:?>
		<!-- показ формы -->
		<?php $form=$this->beginWidget('SActiveForm', [
			'id' => 'forgot-password',
			'htmlOptions' => ['class' => 'changepassword']
		]);?>
			<?if(!request()->getParam('key')): //указание почтового ящика?>
				<div id="email" class="clearfix mt10">
					<h2><?=t('front', 'Укажите свой email')?></h2>	
					<div class="left w300 mauto">
						<?=$form->textField($model, 'email'); ?>
						<?=$form->error($model, 'email'); ?>
					</div>

					<?= CHtml::submitButton(t('front', 'Сменить пароль'), ['class' => 'mt20 border5']);?>
				</div>
			<?else: //установка пароля?>
			<div id="password" class="clearfix mt10">
				<h2 class="mb10"><?=t('front', 'Укажите новый пароль')?></h2>
				<div class="left w300 mauto mb20">
					<?=$form->passwordField($model, 'password', ['placeholder' => 'Password']); ?>
					<?=$form->error($model, 'password'); ?>
				</div>
				
				<div class="left w300 mauto mb20">
					<?=$form->passwordField($model, 'password2', ['placeholder' => 'Repeat passwod']);?>
					<?=$form->error($model, 'password2'); ?>
				</div>

				<?=CHtml::submitButton(t('front', 'Поменять'), ['class' => 'border5']);?>
			</div>
			<?endif?>
		<?$this->endWidget();?>
	<?endif?>
</div>
