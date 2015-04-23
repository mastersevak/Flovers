<?
$this->breadcrumbs = array(t('admin','Manage'));
$loader = CJavaScript::encode(app()->controller->assetsUrl.'/images/loaders/loader11.gif');

$script = <<< script
	$("<div class=\'save_loader\'><img src=$loader></div>").hide().appendTo("body");

	$('.ajax').on('click', function(e){
		e.preventDefault();

		height = $(window).height();
		topX = (height/2 - 12) + $(window).scrollTop();

		$("<div class=\'white-overlay\'/>").appendTo("body");
		$(".save_loader").css("top", topX+"px").show();

		$.post($(this).attr('href'), {}, function(success){
			
			$(".save_loader").hide();
			$(".white-overlay").remove();

			jQuery.jGrowl(success, { life: 5000, position: "customtop-right"});
		});
	});



	
script;
	
cs()->registerScriptFile($this->assetsUrl.'/js/plugins/jquery.jgrowl.js');
cs()->registerCssFile($this->assetsUrl.'/css/plugins/jquery.jgrowl.css');
cs()->registerScript('manage_scripts', $script, CClientScript::POS_READY);
?>

<div id="main" class="subcontent mb20">
	
</div>

<?=CHtml::link('<span>Очистить кеш</span>', array('clearcache'), 
				array('class'=>'ajax btn btn2 mr10 btn_trash'));?>

<?=CHtml::link('<span>Сгенерировать весь кеш</span>', array('createcache'), 
				array('class'=>'ajax btn btn2 mr10 btn_refresh'));?>


<div style="width:350px; margin-top:20px">
	<?=CHtml::link('<span>Сгенерировать картинки для заведений</span>', array('leisurephotos'), 
					array('class'=>'ajax btn btn2 mr10 mb10 btn_refresh'));?>

	<?=CHtml::link('<span>Сгенерировать названия заведений</span>', array('leisurenames'), 
					array('class'=>'ajax btn btn2 mr10 mb10 btn_refresh'));?>

	<?=CHtml::link('<span>Сгенерировать банеры</span>', array('banners'), 
					array('class'=>'ajax btn btn2 mr10 mb10 btn_refresh'));?>
</div>
