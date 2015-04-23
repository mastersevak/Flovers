<?php

$modal = $this->beginWidget('UIModal',[
		'id' => 'items-form-modal',
		'width' => 900,
		'title' => 'Редактирование меню',
		'languageSelector' => $this->languageSelector
	]);

	$form = $this->beginWidget('SActiveForm',[
		'modal' => true,
		'enableAjaxValidation' => true,
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
								'class'=>'w100p',
								'data-slug-to'=>'Menu_slug',
								'data-slugger'=>$this->createUrl('ajaxslug')] );?>
							<?=$form->error($model,'name')?>
						</div>
						
						<div class="mb10">
							<?=$form->label($model, 'slug')?>
							<?=$form->slugField($model, 'slug', ['class' => 'w100p', 'id' => 'Menu_slug', 'slugset-id' => 'items']);?>
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
						
						<div class="mb15 clearfix">
							<div class="fr checkbox mt25">
								<?=CHtml::checkBox('Menu[visible_checkbox]', true, ['id' => 'visible', 'class' => 'fl']); ?>
								<?=CHtml::label('Default','visible', ['class' => 'mr0']); ?>
							</div>
							<?=$form->label($model, 'visible')?>
							<span class="mr80 block">
								<?=$form->textField($model, 'visible', ['class' => 'w100p'])?>
							</span>
							<?=$form->error($model,'visible')?>
						</div>
						
						<div class="mb15 clearfix">
							<div class="fr checkbox mt25">
								<?=CHtml::checkBox('Menu[active_checkbox]', true, ['id' => 'active', 'class' => 'fl']); ?>
								<?=CHtml::label('Default','active', ['class' => 'mr0']); ?>
							</div>
			                <?=$form->label($model, 'active')?>
			                <span class="mr80 block">
								<?=$form->textField($model, 'active', ['class' => 'w100p'])?>
			                </span>
							<?=$form->error($model,'active')?>
						</div>
					</div>

					<div class="col-md-6">
						<!-- htmlOptions -->
						<div class="mb5 bootstrap" >
							<div class="panel panel-default panel-group-no-bg level1 mb0 internal-table w100p">
								<div class="panel-heading collapsed order-groups">
									<h4 class="panel-title clearfix">
										<?=CHtml::link($form->label($model, 'htmlOptions', ['class' => 'fl pointer']), "#l1-htmlOptionsSecond",
											['class' => 'collapsed c-dark-gray mt2', 
											'target' => '_blank', 'data-toggle' => "collapse"])?>
									</h4>
								</div>
							</div>
							
							<div id="l1-htmlOptionsSecond" class="panel-collapse collapse" style="height: 0px;">
								<div class="bg-white mt10 ml10">
									<?=CHtml::link('','#',['id' => 'addhtmlOptionsSecond','class'=> 'fl mt2 fa fa-plus-circle fsize22 c-green']);?>
									<div id="htmlOptionsSecondDiv" class=" ml30">
										<div class="mb10 ">
											<?=$form->textField($model, 'htmlOptions[0][key]', ['class' => 'w100 test'])?>
											<?=$form->textField($model, 'htmlOptions[0][value]', ['class' => 'w140 ml5 test'])?>
										</div>
									</div>
								</div>
							</div>
						</div>
						
						<!-- linkLabelWrapperHtmlOptions -->
						<div class="mb5 bootstrap" >
							<div class="panel panel-default panel-group-no-bg level1 mb0 internal-table w100p">
								<div class="panel-heading collapsed order-groups">
									<h4 class="panel-title clearfix">
										<?=CHtml::link($form->label($model, 'linkLabelWrapperHtmlOptions', ['class' => 'fl pointer']), "#l1-linkLabelWrapperHtmlOptionsSecond",
											['class' => 'collapsed c-dark-gray mt2', 
											'target' => '_blank', 'data-toggle' => "collapse"])?>
									</h4>
								</div>
							</div>
								
							<div id="l1-linkLabelWrapperHtmlOptionsSecond" class="panel-collapse collapse" style="height: 0px;">
								<div class="bg-white mt10 ml10">
									<?=CHtml::link('','#',['id' => 'addlinkLabelWrapperHtmlOptionsSecond','class' => 'fl mt2 fa fa-plus-circle fsize22 c-green']);?>
									<div id="linkLabelWrapperHtmlOptionsSecondDiv" class=" ml30">
										<div class="mb10 ">
											<?=$form->textField($model, 'linkLabelWrapperHtmlOptions[0][key]', ['class' => 'w100'])?>
											<?=$form->textField($model, 'linkLabelWrapperHtmlOptions[0][value]', ['class' => 'w140 ml5'])?>
										</div>
									</div>
								</div>
							</div>
						</div>
						
						<!-- submenuHtmlOptions -->
						<div class="mb5 bootstrap" >
							<div class="panel panel-default panel-group-no-bg level1 mb0 internal-table w100p">
								<div class="panel-heading collapsed order-groups">
									<h4 class="panel-title clearfix">
										<?=CHtml::link($form->label($model, 'submenuHtmlOptions', ['class' => 'fl pointer']), "#l1-submenuHtmlOptionsSecond",
											['class' => 'collapsed c-dark-gray mt2', 
											'target' => '_blank', 'data-toggle' => "collapse"])?>
									</h4>
								</div>
							</div>
							
							<div id="l1-submenuHtmlOptionsSecond" class="panel-collapse collapse" style="height: 0px;">
								<div class="bg-white mt10 ml10">
									<?=CHtml::link('','#',['id' => 'addsubmenuHtmlOptionsSecond','class' => 'fl mt2 fa fa-plus-circle fsize22 c-green']);?>
									<div id="submenuHtmlOptionsSecondDiv" class=" ml30">
										<div class="mb10 ">
											<?=$form->textField($model, 'submenuHtmlOptions[0][key]', ['class' => 'w100'])?>
											<?=$form->textField($model, 'submenuHtmlOptions[0][value]', ['class' => 'w140 ml5'])?>
										</div>
									</div>
								</div>
							</div>
						</div>
						
						<!-- itemOptions -->
						<div class="mb5 bootstrap" >
							<div class="panel panel-default panel-group-no-bg level1 mb0 internal-table w100p">
								<div class="panel-heading collapsed order-groups">
									<h4 class="panel-title clearfix">
										<?=CHtml::link($form->label($model, 'itemOptions', ['class' => 'fl pointer']), "#l1-itemOptions",
											['class' => 'collapsed w100p c-dark-gray mt2', 
											'target' => '_blank', 'data-toggle' => "collapse"])?>
									</h4>
								</div>
							</div>
							
							<div id="l1-itemOptions" class="panel-collapse collapse" style="height: 0px;">
								<div class="bg-white mt10 ml10">
									<?=CHtml::link('','#',['id' => 'additemOptions','class' => 'fl mt2 fa fa-plus-circle fsize22 c-green']);?>
									<div id="itemOptionsDiv" class=" ml30">
										<div class="mb10 ">
											<?=$form->textField($model, 'itemOptions[0][key]', ['class' => 'w100'])?>
											<?=$form->textField($model, 'itemOptions[0][value]', ['class' => 'w140 ml5'])?>
										</div>
									</div>
								</div>
							</div>
						</div>
						
						<!-- submenuOptions -->
						<div class="mb5 bootstrap" >
							<div class="panel panel-default panel-group-no-bg level1 mb0 internal-table w100p">
								<div class="panel-heading collapsed order-groups">
									<h4 class="panel-title clearfix">
										<?=CHtml::link($form->label($model, 'submenuOptions', ['class' => 'fl pointer']), "#l1-submenuOptions",
											['class' => 'collapsed  c-dark-gray mt2', 
											'target' => '_blank', 'data-toggle' => "collapse"])?>
											
									</h4>
								</div>
							</div>
							
							<div id="l1-submenuOptions" class="panel-collapse collapse" style="height: 0px;">
								<div class="bg-white mt10 ml10">
									<?=CHtml::link('','#',['id' => 'addsubmenuOptions','class' => 'fl mt2 fa fa-plus-circle fsize22 c-green']);?>
									<div id="submenuOptionsDiv" class=" ml30">
										<div class="mb10 ">
											<?=$form->textField($model, 'submenuOptions[0][key]', ['class' => 'w100'])?>
											<?=$form->textField($model, 'submenuOptions[0][value]', ['class' => 'w140 ml5'])?>
										</div>
									</div>
								</div>
							</div>
						</div>
						
						<!-- linkOptions -->
						<div class="mb5 bootstrap" >
							<div class="panel panel-default panel-group-no-bg level1 mb0 internal-table w100p">
								<div class="panel-heading collapsed order-groups">
									<h4 class="panel-title clearfix">
										<?=CHtml::link($form->label($model, 'linkOptions', ['class' => 'fl pointer']), "#l1-linkOptions",
											['class' => 'collapsed c-dark-gray mt2', 
											'target' => '_blank', 'data-toggle' => "collapse"])?>
									</h4>
								</div>
							</div>
							
							<div id="l1-linkOptions" class="panel-collapse collapse" style="height: 0px;">
								<div class="bg-white mt10 ml10">
									<?=CHtml::link('','#',['id' => 'addlinkOptions','class' => 'fl mt2 fa fa-plus-circle fsize22 c-green']);?>
									<div id="linkOptionsDiv" class=" ml30">
										<div class="mb10 ">
											<?=$form->textField($model, 'linkOptions[0][key]', ['class' => 'w100'])?>
											<?=$form->textField($model, 'linkOptions[0][value]', ['class' => 'w140 ml5'])?>
										</div>
									</div>
								</div>
							</div>
						</div>
						
						<div class="mb10 h20 mt20 checkbox">
			                <?=$form->checkBox($model,'activateItems', ['class' => 'mt5', 'id' => 'activateItemsItemsModal', 'value' => '1']); ?>
			                <?=$form->labelEx($model,'activateItems', ['class' => 'ml10', 'for' => 'activateItemsItemsModal']); ?>
			                <?=$form->error($model,'activateItems')?>
						</div>
						<div class="mb10 h20 checkbox">
			                <?=$form->checkBox($model,'activateParents', ['class' => 'mt5', 'id' => 'activateParentsItemsModal', 'value' => '1']); ?>
			                <?=$form->labelEx($model,'activateParents', ['class' => 'ml10', 'for' => 'activateParentsItemsModal']); ?>
			                <?=$form->error($model,'activateParents')?>
						</div>
						<div class="h20 checkbox">
			                <?=$form->checkBox($model,'encodeLabel', ['class' => 'mt5', 'id' => 'encodeLabelItemsModal', 'value' => '1']); ?>
			                <?=$form->labelEx($model,'encodeLabel', ['class' => 'ml10', 'for' => 'encodeLabelItemsModal']); ?>
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
?><?php

$modal = $this->beginWidget('UIModal',[
		'id' => 'items-form-modal',
		'width' => 900,
		'title' => 'Редактирование меню',
		'languageSelector' => $this->languageSelector
	]);

	$form = $this->beginWidget('SActiveForm',[
		'modal' => true,
		'enableAjaxValidation' => true,
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
								'class'=>'w100p',
								'data-slug-to'=>'Menu_slug',
								'data-slugger'=>$this->createUrl('ajaxslug')] );?>
							<?=$form->error($model,'name')?>
						</div>
						
						<div class="mb10">
							<?=$form->label($model, 'slug')?>
							<?=$form->slugField($model, 'slug', ['class' => 'w100p', 'id' => 'Menu_slug', 'slugset-id' => 'items']);?>
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
						
						<div class="mb15 clearfix">
							<div class="fr checkbox mt25">
								<?=CHtml::checkBox('Menu[visible_checkbox]', true, ['id' => 'visible', 'class' => 'fl']); ?>
								<?=CHtml::label('Default','visible', ['class' => 'mr0']); ?>
							</div>
							<?=$form->label($model, 'visible')?>
							<span class="mr80 block">
								<?=$form->textField($model, 'visible', ['class' => 'w100p'])?>
							</span>
							<?=$form->error($model,'visible')?>
						</div>
						
						<div class="mb15 clearfix">
							<div class="fr checkbox mt25">
								<?=CHtml::checkBox('Menu[active_checkbox]', true, ['id' => 'active', 'class' => 'fl']); ?>
								<?=CHtml::label('Default','active', ['class' => 'mr0']); ?>
							</div>
			                <?=$form->label($model, 'active')?>
			                <span class="mr80 block">
								<?=$form->textField($model, 'active', ['class' => 'w100p'])?>
			                </span>
							<?=$form->error($model,'active')?>
						</div>
					</div>

					<div class="col-md-6">
						<!-- htmlOptions -->
						<div class="mb5 bootstrap" >
							<div class="panel panel-default panel-group-no-bg level1 mb0 internal-table w100p">
								<div class="panel-heading collapsed order-groups">
									<h4 class="panel-title clearfix">
										<?=CHtml::link($form->label($model, 'htmlOptions', ['class' => 'fl pointer']), "#l1-htmlOptionsSecond",
											['class' => 'collapsed c-dark-gray mt2', 
											'target' => '_blank', 'data-toggle' => "collapse"])?>
									</h4>
								</div>
							</div>
							
							<div id="l1-htmlOptionsSecond" class="panel-collapse collapse" style="height: 0px;">
								<div class="bg-white mt10 ml10">
									<?=CHtml::link('','#',['id' => 'addhtmlOptionsSecond','class'=> 'fl mt2 fa fa-plus-circle fsize22 c-green']);?>
									<div id="htmlOptionsSecondDiv" class=" ml30">
										<div class="mb10 ">
											<?=$form->textField($model, 'htmlOptions[0][key]', ['class' => 'w100 test'])?>
											<?=$form->textField($model, 'htmlOptions[0][value]', ['class' => 'w140 ml5 test'])?>
										</div>
									</div>
								</div>
							</div>
						</div>
						
						<!-- linkLabelWrapperHtmlOptions -->
						<div class="mb5 bootstrap" >
							<div class="panel panel-default panel-group-no-bg level1 mb0 internal-table w100p">
								<div class="panel-heading collapsed order-groups">
									<h4 class="panel-title clearfix">
										<?=CHtml::link($form->label($model, 'linkLabelWrapperHtmlOptions', ['class' => 'fl pointer']), "#l1-linkLabelWrapperHtmlOptionsSecond",
											['class' => 'collapsed c-dark-gray mt2', 
											'target' => '_blank', 'data-toggle' => "collapse"])?>
									</h4>
								</div>
							</div>
								
							<div id="l1-linkLabelWrapperHtmlOptionsSecond" class="panel-collapse collapse" style="height: 0px;">
								<div class="bg-white mt10 ml10">
									<?=CHtml::link('','#',['id' => 'addlinkLabelWrapperHtmlOptionsSecond','class' => 'fl mt2 fa fa-plus-circle fsize22 c-green']);?>
									<div id="linkLabelWrapperHtmlOptionsSecondDiv" class=" ml30">
										<div class="mb10 ">
											<?=$form->textField($model, 'linkLabelWrapperHtmlOptions[0][key]', ['class' => 'w100'])?>
											<?=$form->textField($model, 'linkLabelWrapperHtmlOptions[0][value]', ['class' => 'w140 ml5'])?>
										</div>
									</div>
								</div>
							</div>
						</div>
						
						<!-- submenuHtmlOptions -->
						<div class="mb5 bootstrap" >
							<div class="panel panel-default panel-group-no-bg level1 mb0 internal-table w100p">
								<div class="panel-heading collapsed order-groups">
									<h4 class="panel-title clearfix">
										<?=CHtml::link($form->label($model, 'submenuHtmlOptions', ['class' => 'fl pointer']), "#l1-submenuHtmlOptionsSecond",
											['class' => 'collapsed c-dark-gray mt2', 
											'target' => '_blank', 'data-toggle' => "collapse"])?>
									</h4>
								</div>
							</div>
							
							<div id="l1-submenuHtmlOptionsSecond" class="panel-collapse collapse" style="height: 0px;">
								<div class="bg-white mt10 ml10">
									<?=CHtml::link('','#',['id' => 'addsubmenuHtmlOptionsSecond','class' => 'fl mt2 fa fa-plus-circle fsize22 c-green']);?>
									<div id="submenuHtmlOptionsSecondDiv" class=" ml30">
										<div class="mb10 ">
											<?=$form->textField($model, 'submenuHtmlOptions[0][key]', ['class' => 'w100'])?>
											<?=$form->textField($model, 'submenuHtmlOptions[0][value]', ['class' => 'w140 ml5'])?>
										</div>
									</div>
								</div>
							</div>
						</div>
						
						<!-- itemOptions -->
						<div class="mb5 bootstrap" >
							<div class="panel panel-default panel-group-no-bg level1 mb0 internal-table w100p">
								<div class="panel-heading collapsed order-groups">
									<h4 class="panel-title clearfix">
										<?=CHtml::link($form->label($model, 'itemOptions', ['class' => 'fl pointer']), "#l1-itemOptions",
											['class' => 'collapsed w100p c-dark-gray mt2', 
											'target' => '_blank', 'data-toggle' => "collapse"])?>
									</h4>
								</div>
							</div>
							
							<div id="l1-itemOptions" class="panel-collapse collapse" style="height: 0px;">
								<div class="bg-white mt10 ml10">
									<?=CHtml::link('','#',['id' => 'additemOptions','class' => 'fl mt2 fa fa-plus-circle fsize22 c-green']);?>
									<div id="itemOptionsDiv" class=" ml30">
										<div class="mb10 ">
											<?=$form->textField($model, 'itemOptions[0][key]', ['class' => 'w100'])?>
											<?=$form->textField($model, 'itemOptions[0][value]', ['class' => 'w140 ml5'])?>
										</div>
									</div>
								</div>
							</div>
						</div>
						
						<!-- submenuOptions -->
						<div class="mb5 bootstrap" >
							<div class="panel panel-default panel-group-no-bg level1 mb0 internal-table w100p">
								<div class="panel-heading collapsed order-groups">
									<h4 class="panel-title clearfix">
										<?=CHtml::link($form->label($model, 'submenuOptions', ['class' => 'fl pointer']), "#l1-submenuOptions",
											['class' => 'collapsed  c-dark-gray mt2', 
											'target' => '_blank', 'data-toggle' => "collapse"])?>
											
									</h4>
								</div>
							</div>
							
							<div id="l1-submenuOptions" class="panel-collapse collapse" style="height: 0px;">
								<div class="bg-white mt10 ml10">
									<?=CHtml::link('','#',['id' => 'addsubmenuOptions','class' => 'fl mt2 fa fa-plus-circle fsize22 c-green']);?>
									<div id="submenuOptionsDiv" class=" ml30">
										<div class="mb10 ">
											<?=$form->textField($model, 'submenuOptions[0][key]', ['class' => 'w100'])?>
											<?=$form->textField($model, 'submenuOptions[0][value]', ['class' => 'w140 ml5'])?>
										</div>
									</div>
								</div>
							</div>
						</div>
						
						<!-- linkOptions -->
						<div class="mb5 bootstrap" >
							<div class="panel panel-default panel-group-no-bg level1 mb0 internal-table w100p">
								<div class="panel-heading collapsed order-groups">
									<h4 class="panel-title clearfix">
										<?=CHtml::link($form->label($model, 'linkOptions', ['class' => 'fl pointer']), "#l1-linkOptions",
											['class' => 'collapsed c-dark-gray mt2', 
											'target' => '_blank', 'data-toggle' => "collapse"])?>
									</h4>
								</div>
							</div>
							
							<div id="l1-linkOptions" class="panel-collapse collapse" style="height: 0px;">
								<div class="bg-white mt10 ml10">
									<?=CHtml::link('','#',['id' => 'addlinkOptions','class' => 'fl mt2 fa fa-plus-circle fsize22 c-green']);?>
									<div id="linkOptionsDiv" class=" ml30">
										<div class="mb10 ">
											<?=$form->textField($model, 'linkOptions[0][key]', ['class' => 'w100'])?>
											<?=$form->textField($model, 'linkOptions[0][value]', ['class' => 'w140 ml5'])?>
										</div>
									</div>
								</div>
							</div>
						</div>
						
						<div class="mb10 h20 mt20 checkbox">
			                <?=$form->checkBox($model,'activateItems', ['class' => 'mt5', 'id' => 'activateItemsItemsModal', 'value' => '1']); ?>
			                <?=$form->labelEx($model,'activateItems', ['class' => 'ml10', 'for' => 'activateItemsItemsModal']); ?>
			                <?=$form->error($model,'activateItems')?>
						</div>
						<div class="mb10 h20 checkbox">
			                <?=$form->checkBox($model,'activateParents', ['class' => 'mt5', 'id' => 'activateParentsItemsModal', 'value' => '1']); ?>
			                <?=$form->labelEx($model,'activateParents', ['class' => 'ml10', 'for' => 'activateParentsItemsModal']); ?>
			                <?=$form->error($model,'activateParents')?>
						</div>
						<div class="h20 checkbox">
			                <?=$form->checkBox($model,'encodeLabel', ['class' => 'mt5', 'id' => 'encodeLabelItemsModal', 'value' => '1']); ?>
			                <?=$form->labelEx($model,'encodeLabel', ['class' => 'ml10', 'for' => 'encodeLabelItemsModal']); ?>
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