<section class="col-sm-6 col-md-6 col-lg-6">
	<section class="container-with-large-icon login-form">
		<div class="large-icon"><img src="<?=$this->_assetsUrl?>/images/large-icon-lock.png" alt=""></div>
		<div class="wrap">
			<h3><?=t('front', 'Вход')?></h3>
			<!-- <p>By creating an account you will be able to shop faster, be up to date on an order's status, and keep track of the orders you have previously made.</p> -->
			<p><?=t('front', 'Создав аккаунт, Вы сможете совершать покупки намного быстрее и быть всегда в курсе о статусе заказа, и отслеживать заказов, которые ранее сделали.')?></p>
		</div>

		<?$form = $this->beginWidget('SActiveForm', [
			'id' => 'login-form',
			'action' => ['login'],
			'focus' => [$model, 'username'],
			'enableAjaxValidation' => true,
		]);?>
			<?=CHtml::hiddenField('scenario', $model->scenario);?>
			<div class="form-group">
				<?=$form->label($model, 'username')?>
				<?=$form->textField($model, 'username', ['class' => 'form-control'])?>
				<?=$form->error($model, 'username')?>
			</div>
			<div class="form-group">
				<?=$form->label($model, 'password')?>
				<?=$form->passwordField($model, 'password', ['class' => 'form-control'])?>
				<?=$form->error($model, 'password')?>
			</div>
			<div class="form-link"><a href="#"><?=t('back', 'Забыли пароль?')?></a></div>
			<?$this->widget('UIButtons', ['buttons' => [
				'custom' => [
					'value'		=> t('back', 'Войти'),
					'icon'		=> '',
					'options'	=> [
						'class'			=> 'btn btn-mega',
						'data-form' 	=> 'login-form',
						'type'			=> 'submit',
						'onclick'		=> 'UIButtons.save(this); return false;'
					]
				]]
			]);?>
		<?$this->endWidget()?>
	</section>
</section>