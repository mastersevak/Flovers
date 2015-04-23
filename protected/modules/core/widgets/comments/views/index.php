
<div id="<?=$this->id?>" class="popup comments grid simple posfix"
		data-owner-modelname="<?=$this->ownerModel?>"
		data-comments-relation="<?=$this->commentsRelation?>"
		data-add-url="<?=$this->controller->createUrl($this->addComment)?>" 
		data-get-url="<?=$this->controller->createUrl($this->getComments)?>">
	<div class="grid-title">
		<h4>Комментарии <span class="semi-bold payment-id"></span></h4>
		<div class="tools">
			<a href="#" class="config"></a>
			<a onclick="$.fn.comments('hide', $(this).closest('.popup').attr('id')); " class="remove"></a>
		</div>
	</div>
	<div class="grid-body">
		<div class="scroller" data-height="230">
			
		</div>
	</div>

	<?
	$form = $this->beginWidget('SActiveForm', [
			'action' => [$this->addComment],
			'enableAjaxValidation' => true,
			'clientOptions' => [
				'validateOnSubmit' => true, 
				'validateOnChange' => false,
				'afterValidate' => 'js:function(form, data, hasError){
					if(!hasError){
						$.fn.comments("add", form.closest(".popup").attr("id"), {
							idOwner: form.closest(".popup").data("id-owner"),
							commentsModelName: "'.get_class($model).'",
							commentsForeignKey: "'.$commentsForeignKey.'",
							additionalFields: form.closest(".popup").attr("data-additional-fields"),
							comment: form.find("textarea").val()
						}, '.$this->afterAdd.' );
						Forms.enableFormSubmit(form);
					}
					else{
						Forms.enableFormSubmit(form);
						$(form).find("textarea").focus();
					}
					return false;
				}'
			]
	])?>

		<div class="input-block">
			<?=$form->textarea($model, 'comment', [
					'placeholder'=>"Ваш комментарий ...", 
					'class'=>'form-control no-border no-resize'])?>
			<?=$form->error($model, 'comment', ['hideErrorMessage'=>true])?>
			<?=CHtml::hiddenField('commentsModelName', get_class($model))?>
		</div>

		<div class="grid-footer">
			<button class="btn btn-primary btn-small m10 pull-right" type="submit">ОТПРАВИТЬ</button>
		</div>	
	<?$this->endWidget()?>
	
</div>