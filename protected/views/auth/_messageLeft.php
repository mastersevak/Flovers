<?foreach ($data as $idUser => $chatId):?>

	<div class="clearfix mb10 form-group">
		<div class="inside">
			<?$user = Person::model()->findByPk($idUser);?>

			<?if(isset(Person::listData()[$idUser])):;?>
				<a href=<?=$this->createUrl('/site/getmessages')?> class="c-dark-gray fl mr10 getmessages"
					data-chat=<?=$chatId?> data-id=<?=$idUser?>>
					<?=$user->getThumbnail('thumb', false, false, false,['style'=>'border-radius: 15px; margin-right: 5px;']);?>
					<?=Person::listData()[$idUser]?>
				</a>
			<?endif?>
		</div>
	</div>
<?endforeach?>