<?php
return [
	'activeForm' => [
		'class' => 'SActiveForm',
		'id' => 'edit-form',
		'enableAjaxValidation' => true,
		'clientOptions' => [
			'validateOnSubmit' => true,
			'validateOnChange' => true,
		],
	],
	'elements' => [
		'name' => [
			'layout' => '<div class="control-group">{label}<span class="field">{input}{error}</span></div>',
			'type' => 'text',
			'data-language' => lang(),
		],
		'type' => [
			'layout' => '<div class="control-group">{label}<span class="field">{input}{error}</span></div>',
			'type' => 'text',
		],
		'code' => [
			'layout' => '<div class="control-group">{label}<span class="field">{input}{error}</span></div>',
			'type' => 'text',
			'class' => 'w50',
		]
	],
	'buttons' => []
]?>