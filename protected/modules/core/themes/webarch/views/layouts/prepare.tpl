<!DOCTYPE html>
<html>
	<head>
		{CHtml::tag('meta', ['charset'=>Yii::app()->charset])}
		<title>{strip_tags($this->pageTitle)}</title>
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
	</head>
	<body class="{$this->bodyClass}">
		
		{$content}

	</body>	
</html>