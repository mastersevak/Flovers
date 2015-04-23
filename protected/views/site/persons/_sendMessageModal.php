<?php
$modal = $this->beginWidget('UIModal',[
 	'id' => 'send-message-modal',
	'width' => 345,
	'title' => 'Отправить сообщение',
	'backdrop' => true,
	'footerButtons' => [
			'submit' => [ 'value' => 'Отправить', 'icon' => false, 
						'htmlOptions' => [
							'type'	=>'submit',
							'id' 	=> 'send-message-modal-submit',
							'class'	=>'btn btn-mega w100',
							'data-url' => $this->createUrl('savenewmessage')
					]
			]
		]
 	]);
	?>
	<?$modal->header();?>
	<div class="contacts-form">
		<div class="form-group p15">
			<span class="icon icon-bubbles-2"></span>
			<?=CHtml::textArea('emailText', '',['style' => 'width:300px; height:150px', 'class' => 'form-control']);?>
		</div>
	</div>
	<?$modal->footer();?>
<?$this->endWidget();?>