<?php
return [
	'activeForm' => [
		'id' => 'edit-form',
        'class' => 'SActiveForm',
        'enableAjaxValidation' => true,
        'clientOptions' => [
            'validateOnSubmit' => true,
            'validateOnChange' => true,
        ],
    ],
	'elements' => [
	    'title' => [
	    	'layout' => '<div class="control-group">{label}<span class="field">{input}{error}</span></div>',
	        'type' => 'text',
	    ],
	    'code' => [
	    	'layout' => '<div class="control-group">{label}<span class="field">{input}{error}</span></div>',
	        'type' => 'text',
	    ],
	    'value' => [
	    	'layout' => '<div class="control-group">{label}<span class="field">{input}{error}</span></div>',
	        'type' => 'text',
	    ],
	    'category' => [
	    	'layout' => '<div class="control-group">{label}<span class="field">{input}{error}</span></div>',
	        'type' => 'text',
	    ],
	    
	],
	'buttons' => []
];	

?>