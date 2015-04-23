<?$data = ChatUser::getAllUsersInCHat();?>

<div class="col-md-3 pl0 pr0">
	<div class="hends-search">
		<input type="text" placeholder=<?=t('front','Поиск')?> class="small" style="width: 90%; margin-bottom:15px; padding-left: 5px;">
		<a href="#" class="fa fa-search iblock c-gray search-user-message" data-url=<?=$this->createUrl('searchmessageuser')?>></a>
	</div>

	<div class="left-block">
		<?$this->renderPartial('/auth/_messageLeft', compact('data'));?>
	</div>
</div>

<div class="col-md-9 pr0 pl0 allmessages" style="height:500px; overflow-y:scroll;"></div>