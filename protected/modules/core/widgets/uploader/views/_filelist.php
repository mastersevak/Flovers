<? foreach($files as $key=>$file): ?>
<li class="qq-upload-success image image-container" 
	data-id="<?=$file->id?>" data-size="thumb">

    <div class="tools">
    	<span class="tool fa fa-rotate-left rotate-left-btn"></span>
        <span class="tool fa fa-rotate-right rotate-right-btn"></span>
        <span class="tool fa fa-crop crop-btn"></span>
        <span class="tool fa fa-trash-o delete-btn"></span>
        <span class="tool fa fa-arrows move-btn"></span>
    </div>
    
	<a href="<?=$file->getImageUrl($bigSize)?>" class="qq-upload-file fancybox" 
            data-big-size="<?=$bigSize?>" rel="gallery" title="<?=$file->title?>">
		<?=$file->getThumbnail('thumb', 120, false, '', array('class'=>'image'))?>
	</a>

</li>
<?endforeach?>