<?$form = $this->beginWidget('SActiveForm', [
	'id'	=>'edit-form', //объязательно для работы кнопок сохранения, удаления
	'htmlOptions'=>[
		'enctype'=>'multipart/form-data'
	]])?>

	<div class="grid simple mt20">
		<div class="grid-body">

			<p class="note mb20">Поля со знаком <code>*</code> объязательны для заполнения</p>

			<div class="std-form">
				<div class="control-group clearfix field_name">
					<?=$form->labelEx($model, 'name')?>
					<span class="field">
						<?=$form->multilangTextField($model, 'name')?>
						<?=$form->error($model,'name')?>
					</span>
				</div>

				<div class="control-group">
					<?=$form->labelEx($model, 'type')?>
					<span class="field">
						<?=$form->textField($model, 'type')?>
						<?=$form->error($model,'type')?>
					</span>
				</div>

				<div class="control-group">
					<?=$form->labelEx($model, 'code')?>
					<span class="field">
						<?=$form->textField($model, 'code', ['class' => 'w50'])?>
						<?=$form->error($model,'code')?>
					</span>
				</div>
			</div>
		</div>
	</div>

	<div class='fլ'>
		<?$this->widget('UIButtons', ['group'=>'update', 'form'=>'edit-form'])?>
	</div>
<? $this->endWidget(); ?>