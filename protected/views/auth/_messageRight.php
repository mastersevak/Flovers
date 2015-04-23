<div class="right-block">
	<?
	foreach ($models as $model):?>

		<?if($model->id_user == $idUser):;?>
			<div class="">
				<div class="inside">
					<?$user = Person::model()->findByPk($idUser);
					echo $user->getThumbnail('thumb', false, false, false,['style'=>'float:left; border-radius: 15px; margin-right: 5px;']);?>
					<p>
						<?=Person::listData()[$idUser]?>
						<br>
						<?=date('g:i a', strtotime($model->created));?>
					</p>
					<p class="commenttext"><?=$model->message?></p>
				</div>
			</div>
		<?else:?>
			<div class="">
				<div class="inside">
					<?$user = Person::model()->findByPk(user()->id);?>
					<?=$user->getThumbnail('thumb', false, false, false,['style'=>'float: left; border-radius: 15px; margin-right: 5px;']);?>
					<p>
						<strong><?=Person::listData()[user()->id]?></strong>
						<br>
						<?=date('g:i a', strtotime($model->created));?>
					</p>
					<p class="commenttext message"><?=$model->message?></p>
				</div>
			</div>

		<?endif?>
	<?endforeach?>
</div>

<div id="commentForm" class="contacts-form row print-hends form-group">
	<div class="col-md-12">
		<div class="form-group">
			<span class="icon icon-bubbles-2"></span>
			<textarea class="form-control message-input"
				id="message-input" rows="2" cols="111" placeholder=<?=t('front', 'Комментарий:')?>
				data-user=<?=$idUser?> data-chat=<?=$idChat?>
				data-url=<?=$this->createUrl('/site/sendmessage')?>></textarea>
		</div>
	</div>
</div>