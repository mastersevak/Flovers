<? $photos = $model->photos ?>

<div id="big-image" data-gal="single">
	<a href="#" <?if($photos) echo "data-id='{$photos[0]->id}' class='zoom'"; ?>>
		
		<img src="<?=($photos) ? $photos[0]->getImageUrl('big') : $this->assetsUrl.'/images/placeholders/item.jpg'?>">

		<div class="glass"></div>
	</a>
</div>
<div id="small-images" class="jThumbnailScroller">
    <div class="jTscrollerContainer">
        <div class="jTscroller">
			<? foreach ($photos as $photo) : ?>
				<a href="<?=$photo->getImageUrl('big')?>" class="zoom"
					alt="<?=$photo->title?>" data-id="<?=$photo->id?>">
				<img src="<?=$photo->getImageUrl('thumb')?>" 
					data-big="<?=$photo->getImageUrl('original')?>"
					data-title="<?=$photo->title?>"></a>
			<? endforeach ?>
		</div>
	</div>
</div>

<?  
cs()->registerScriptFile($this->assetsUrl."/js/plugins/galleria/galleria-1.2.9.min.js"); 
cs()->registerScriptFile($this->assetsUrl."/js/plugins/galleria/myplugin.js");

$galleria = <<<script
	Galleria.loadTheme('{$this->assetsUrl}' + '/js/plugins/galleria/themes/folio/galleria.folio.min.js');

	jQuery(document).ready(function($) {
		$('#small-images .jTscroller').runGalleria($('#big-image'));
	});
script;

cs()->registerScript('run_galleria', $galleria);
?>



