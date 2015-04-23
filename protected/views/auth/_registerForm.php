<section class="col-sm-6 col-md-6 col-lg-6">
	<section class="container-with-large-icon login-form">
		<div class="large-icon" style="z-index: -1;"><img src="<?=$this->_assetsUrl?>/images/large-icon-user.png" alt=""></div>

		<?if(user()->hasFlash('success')):?>
			<div class="wrap success_message">
				<?=user()->getFlash('success');?>
			</div>

		<?elseif(user()->hasFlash('error')):?>
			<div class="wrap error_message">
				<?=user()->getFlash('error');?>
			</div>
		<?else:?>

			<div class="wrap">
				<h3><?=t('back', 'Регистрация')?></h3>
				<!-- <p>By creating an account you will be able to shop faster, be up to date on an order's status, and keep track of the orders you have previously made.</p> -->
				<p><?=t('front', 'Создав аккаунт, Вы сможете совершать покупки намного быстрее и быть всегда в курсе о статусе заказа, и отслеживать заказов, которые ранее сделали.')?></p>
			</div>

			<?$form = $this->beginWidget('SActiveForm', [
				'id' => 'register-form',
				'action' => ['login'],
				'enableAjaxValidation' => true,
			]);?>
				<?=CHtml::hiddenField('scenario', $model->scenario);?>
				<div class="form-group">
					<?=$form->label($model, 'firstname')?>
					<?=$form->textField($model, 'firstname', ['class' => 'form-control'])?>
					<?=$form->error($model, 'firstname')?>
				</div>
				<div class="form-group">
					<?=$form->label($model, 'lastname')?>
					<?=$form->textField($model, 'lastname', ['class' => 'form-control'])?>
					<?=$form->error($model, 'lastname')?>
				</div>
				<div class="form-group">
					<?=$form->label($model, 'username')?>
					<?=$form->textField($model, 'username', ['class' => 'form-control'])?>
					<?=$form->error($model, 'username')?>
				</div>
				<div class="form-group">
					<?=$form->label($model, 'email')?>
					<?=$form->textField($model, 'email', ['class' => 'form-control'])?>
					<?=$form->error($model, 'email')?>
				</div>
				<div class="form-group">
					<?=$form->label($model, 'password')?>
					<?=$form->passwordField($model, 'password', ['class' => 'form-control'])?>
					<?=$form->error($model, 'password')?>
				</div>
				<div class="form-group">
					<?=$form->label($model, 'password2')?>
					<?=$form->passwordField($model, 'password2', ['class' => 'form-control'])?>
					<?=$form->error($model, 'password2')?>
				</div>

				<?if(CCaptcha::checkRequirements('gd')):?>
					<div class="form-group clearfix">
						<?=t('front', $form->label($model, 'verifyCode', ['class' => 'show']))?>
						<div class="pull-left">
							<?=$form->textField($model, 'verifyCode', ['class' => 'form-control'])?>
							<?=$form->error($model, 'verifyCode')?>
						</div>
						<div class="captcha pull-left">
							<?php $this->widget('CCaptcha', [
								'buttonOptions' => ['class' => 'stdbtn radius50'],
								'clickableImage' => true,
								'showRefreshButton' => false
							])?>
						</div>
					</div>
				<?endif?>
				<?$this->widget('UIButtons', ['buttons' => [
					'custom' => [
						'value'		=>	t('back', 'Регистрация'),
						'icon'		=> '',
						'options'	=> [
							'class'			=> 'btn btn-mega',
							'data-form' 	=> 'register-form',
							'type'			=> 'submit',
							'onclick'		=> 'UIButtons.save(this); return false;'
						]
					]]
				]);?>
			<?$this->endWidget()?>
		<?endif?>
	</section>
</section>