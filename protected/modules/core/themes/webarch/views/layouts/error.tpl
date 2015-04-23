<!DOCTYPE html>
<html>
<head>
	{CHtml::tag('meta', ['charset'=>Yii::app()->charset])}
	<title>{$this->pageTitle}</title>
	
</head>
<body class="error-body no-top">
	
	<div class="error-wrapper container">
		{$content}
	</div>

</body>	
</html>