<div class="success_message mt30 w570 mauto left">
	<div class="fl mr30">
		<img src="<?=$this->assetsUrl?>/images/404.png">
	</div>
	 
	<div class="fl">
		<h2 class="error-message"><?=t('front', 'Ошибка !!!');?></h2>
		<p class="w290 left fs14"><?=t('front', 'Запрашиваемая страница не найдена.<br> Вероятно, она была удалена автором с сервера, либо ее вообще не было.');?></p>
		<div class="gradient">
			<a href="<?=url('/site/index')?>"><?=t('front', 'Перейти на главную');?></a>
		</div>
	</div>
	
</div>

