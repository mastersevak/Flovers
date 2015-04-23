<div id="<?=$this->id?>" class="image-container" 
	 data-crop-url="<?=$cropUrl?>"
	 data-upload-url="<?=$uploadUrl?>"
	 data-delete-url="<?=$deleteUrl?>"
	 data-size="<?=$size?>"
	 data-model-id="<?=$model->id?>" >
	
	<div class="droppable-image">

		<div class="profile-avatar-wrap">
			
			<div class="tools <? if(!empty($model->$thumbID)) echo "visible"; ?>">
				<a class="crop-btn fa fa-crop">&nbsp;</a>
				<a href="#" class="delete fa fa-trash-o">&nbsp;</a>
			</div>
			<a href="<?=$model->getImageUrl($bigSize)?>" class="fancybox">
					<?=$model->getThumbnail($size, $thumbWidth, $thumbHeight, $alt, 
									array('id'=>get_class($model).'_'.$thumbID, 'class'=>'image'), 
									false, true); ?></a>
		</div>
		<div <?php if($hiddenFile) echo 'class="hidden"'; ?>>
			<?if(isset($form) && !empty($form)) {
				echo $form->fileField($model, $field, ['class' => 'browse']); 
			}
			else{
				echo CHtml::fileField(get_class($model) . '_' . $field);
			}?>
		</div>
		<?if(isset($form) && !empty($form)) echo $form->error($model, $field); ?>
		<br clear="both">
		<?php if($hiddenFile && !$hiddenLink) echo CHtml::link('выбрать картинку', '#', array('class'=>'upload btn btn-success btn-mini')); ?>
	</div>

	<div class="crop-container hidden">

		<div class="sizes clearfix"></div>
		<div class="crop-area"></div>
		<div class="buttons hidden">
			<a href="#" class="apply-crop btn btn_orange"><i class="icon-crop"></i>ОБРЕЗАТЬ</a>
		</div>
	</div>

</div>