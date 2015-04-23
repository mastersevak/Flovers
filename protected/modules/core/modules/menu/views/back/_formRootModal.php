<?php

$modal = $this->beginWidget('UIModal',[
		'id' => 'root-form-modal',
		'width' => 900,
		'title' => 'Редактирование меню',
		'languageSelector' => $this->languageSelector
	]);

	$form = $this->beginWidget('SActiveForm',[
		'modal' => true,
		'enableAjaxValidation' => true,
		'action' => ['/core/menu/back/updateMenu'],
		'clientOptions' => [
			'validateOnChange' => false,
		],
		'afterModalClose' => 'function(form, data){
			if(data.idRootParent)
				$.fn.tree("refreshTree", "#root-form-modal", data.idRootParent);
			else
				$.fn.tree("refreshTree", "#root-form-modal");
		}'
	]);

	$modal->header();

	?>
		<div class="mt20">
			<div class="grid simple">
				<div class="grid-body border-none pt0 pb0 clearfix row" style="margin:auto">
					<?=$form->hiddenField($model, 'id')?>
					<?=CHtml::hiddenField('Menu[id_parent]', '');?>
					<?=CHtml::hiddenField('Menu[idRootParent]', '');?>
					<div class="col-md-6">
						<div class="mb10">
							<?=$form->label($model, 'name')?>
							<?=$form->multilangTextField($model,'name', [
								'class' => 'w100p',
								'data-slug-to' => 'Menu_slug',
								'data-slugger' => $this->createUrl('ajaxslug')] );?>
							<?=$form->error($model,'name')?>
						</div>

						<div class="mb10">
							<?=$form->label($model, 'slug')?>
							<?=$form->slugField($model, 'slug', ['class' => 'w100p', 'id' => 'Menu_slug']);?>
							<?=$form->error($model,'slug')?>
						</div>

						<div class="mb10">
							<?=$form->label($model, 'url')?>
							<?=$form->textField($model, 'url', ['class' => 'w100p'])?>
							<?=$form->error($model,'url')?>
						</div>

						<div class="mb10">
							<?=$form->label($model, 'items')?>
							<?=$form->textField($model, 'items', ['class' => 'w100p'])?>
							<?=$form->error($model,'items')?>
							<div class="hint">Только статическая функция. Пример: Menu::test(false) </div>
						</div>

						<div class="mb10">
							<?=$form->label($model, 'containerTag')?>
							<?=$form->textField($model, 'containerTag', ['class' => 'w100p'])?>
							<?=$form->error($model,'containerTag')?>
						</div>

						<div class="mb10">
							<?=$form->label($model, 'itemTag')?>
							<?=$form->textField($model, 'itemTag', ['class' => 'w100p'])?>
							<?=$form->error($model,'itemTag')?>
						</div>
						
						<div class="mb10">
							<?=$form->label($model, 'activeCssClass')?>
							<?=$form->textField($model, 'activeCssClass', ['class' => 'w100p'])?>
							<?=$form->error($model,'activeCssClass')?>
							<div class="hint">CSS класс, который будет добавлен к активного меню элемента.</div>
						</div>

						<div class="mb10">
							<?=$form->label($model, 'itemCssClass')?>
							<?=$form->textField($model, 'itemCssClass', ['class' => 'w100p'])?>
							<?=$form->error($model,'itemCssClass')?>
							<div class="hint">CSS класс, который будет назначен к каждому элементу.</div>
						</div>

						<div class="mb10">
							<?=$form->label($model, 'itemTemplate')?>
							<?=$form->textField($model, 'itemTemplate', ['class' => 'w100p'])?>
							<?=$form->error($model,'itemTemplate')?>
							<div class="hint">шаблон, используемый для отображения отдельного элемента меню.</div>
						</div>

						<div class="mb15">
							<?=$form->label($model, 'linkLabelWrapper')?>
							<?=$form->textField($model, 'linkLabelWrapper', ['class' => 'w100p'])?>
							<?=$form->error($model,'linkLabelWrapper')?>
							<div class="hint">the HTML element name that will be used to wrap the label of all menu links.</div>
						</div>

						<div class="mb15">
							<?=$form->label($model, 'submenuWrapper')?>
							<?=$form->textField($model, 'submenuWrapper', ['class' => 'w100p'])?>
							<?=$form->error($model,'submenuWrapper')?>
						</div>
					</div>

					<div class="col-md-6">
						<div class="mb5 bootstrap" >
							<div class="panel panel-default panel-group-no-bg level1 mb0 internal-table w100p">
								<div class="panel-heading collapsed order-groups">
									<h4 class="panel-title clearfix">
										<?=CHtml::link($form->label($model, 'htmlOptions', ['class' => 'fl pointer']), "#l1-htmlOptions",
											['class' => 'collapsed c-dark-gray mt2',
											'target' => '_blank', 'data-toggle' => "collapse"])?>
											
									</h4>
								</div>
							</div>

							<div id="l1-htmlOptions" class="panel-collapse collapse" style="height: 0px;">
								<div class="bg-white mt10 ml10">
									<?=CHtml::link('','#',['id' => 'addhtmlOptions','class' => 'fl mt2 fa fa-plus-circle fsize22 c-green mr5']);?>
									<div id="htmlOptionsDiv" class=" ml30">
										<div class="mb10 ">
											<?=$form->textField($model, 'htmlOptions[0][key]', ['class' => 'w100 small'])?>
											<?=$form->textField($model, 'htmlOptions[0][value]', ['class' => 'w140 ml5 small'])?>
										</div>
									</div>
								</div>
							</div>
						</div>

						<div class="mb5 bootstrap" >
							<div class="panel panel-default panel-group-no-bg level1 mb0 internal-table w100p">
								<div class="panel-heading collapsed order-groups">
									<h4 class="panel-title clearfix">
										<?=CHtml::link($form->label($model, 'linkLabelWrapperHtmlOptions', ['class' => 'fl pointer']), "#l1-linkLabelWrapperHtmlOptions",
											['class' => 'collapsed c-dark-gray mt2',
											'target' => '_blank', 'data-toggle' => "collapse"])?>
									</h4>
								</div>
							</div>

							<div id="l1-linkLabelWrapperHtmlOptions" class="panel-collapse collapse" style="height: 0px;">
								<div class="bg-white mt10 ml10">
									<?=CHtml::link('','#',['id' => 'addlinkLabelWrapperHtmlOptions','class'=> 'fl mt2 fa fa-plus-circle fsize22 c-green mr5']);?>
									<div id="linkLabelWrapperHtmlOptionsDiv" class=" ml30">
										<div class="mb10 ">
											<?=$form->textField($model, 'linkLabelWrapperHtmlOptions[0][key]', ['class' => 'w100 small'])?>
											<?=$form->textField($model, 'linkLabelWrapperHtmlOptions[0][value]', ['class' => 'w140 ml5 small'])?>
										</div>
									</div>
								</div>
							</div>
						</div>

						<div class="mb10 bootstrap" >
							<div class="panel panel-default panel-group-no-bg level1 mb0 internal-table w100p">
								<div class="panel-heading collapsed order-groups">
									<h4 class="panel-title clearfix">
										<?=CHtml::link($form->label($model, 'submenuHtmlOptions', ['class' => 'fl pointer']), "#l1-submenuHtmlOptions",
											['class' => 'collapsed c-dark-gray mt2',
											'target' => '_blank', 'data-toggle' => "collapse"])?>
									</h4>
								</div>
							</div>

							<div id="l1-submenuHtmlOptions" class="panel-collapse collapse" style="height: 0px;">
								<div class="bg-white mt10 ml10">
									<?=CHtml::link('','#',['id' => 'addsubmenuHtmlOptions','class' => 'fl mt2 fa fa-plus-circle fsize22 c-green mr5']);?>
									<div id="submenuHtmlOptionsDiv" class="ml30">
										<div class="mb10 ">
											<?=$form->textField($model, 'submenuHtmlOptions[0][key]', ['class' => 'w100 small'])?>
											<?=$form->textField($model, 'submenuHtmlOptions[0][value]', ['class' => 'w140 ml5 small'])?>
										</div>
									</div>
								</div>
							</div>
						</div>

						<div class="mb10 h20 mt20 checkbox">
			                <?=$form->checkBox($model,'activateItems', ['class' => 'mt5', 'value' => 1]); ?>
			                <?=$form->labelEx($model,'activateItems', ['class' => 'ml10']); ?>
			                <?=$form->error($model,'activateItems')?>
						</div>
						<div class="mb10 h20 checkbox">
			                <?=$form->checkBox($model,'activateParents', ['class' =>'mt5']); ?>
			                <?=$form->labelEx($model,'activateParents', ['class' =>'ml10']); ?>
			                 <?=$form->error($model,'activateParents')?>
						</div>
						<div class="h20 checkbox">
			                <?=$form->checkBox($model,'encodeLabel', ['class' =>'mt5']); ?>
			                <?=$form->labelEx($model,'encodeLabel', ['class' =>'ml10']); ?>
			                <?=$form->error($model,'encodeLabel')?>
						</div>
					</div>
				</div>
			</div>
		</div>
<?
	$modal->footer();
	$this->endWidget();
$this->endWidget();
?>