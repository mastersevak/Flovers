<!-- <div class="clearfix fr ml5 mb20">
	<div class="fr mr10  user-2 users">
		<div class="message"><?//=$model->message?></div>
	</div>

	<div class="message-date fl mr5"><?//=date('g:i a', strtotime($model->created));?></div>
</div> -->

<div class="">
	<div class="inside">
		<?$user = Person::model()->findByPk(user()->id);?>
		<?=$user->getThumbnail('thumb', false, false, false,['style'=>'float:left; border-radius: 15px; margin-right: 5px;']);?>

		<p>
			<?=date('g:i a', strtotime($model->created));?>
		</p>
		<p class="commenttext"><?=$model->message?></p>
	</div>
</div>