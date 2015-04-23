<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="language" content="<?=lang()?>" />

	<title><?=$this->pageTitle?> | Error</title>
	
    <meta name="robots" content="noindex">

	<link rel="stylesheet" href="<?=$this->assetsUrl?>/css/style.default.css" type="text/css" />
	
	<!--ICON-->
	<link rel="icon" href="<?=$this->assetsUrl?>/images/favicon.ico" type="image/x-icon" />
	<link rel="shortcut icon" href="<?=$this->assetsUrl?>/images/favicon.ico" type="image/x-icon">
	<!--[if lte IE 8]><script language="javascript" type="text/javascript" src="<?=$this->assetsUrl?>/js/plugins/excanvas.min.js"></script><![endif]-->
	<!--[if IE 9]>
	    <link rel="stylesheet" media="screen" href="<?=$this->assetsUrl?>/css/style.ie9.css"/>
	<![endif]-->
	<!--[if IE 8]>
	    <link rel="stylesheet" media="screen" href="<?=$this->assetsUrl?>/css/style.ie8.css"/>
	<![endif]-->
	<!--[if lt IE 9]>
		<script src="http://css3-mediaqueries-js.googlecode.com/svn/trunk/css3-mediaqueries.js"></script>
	<![endif]-->
</head>

<body>

<div class="bodywrapper">
	    
    <div class="contentwrapper padding10">
    	<?=$content?>
    </div>    

</div><!--bodywrapper-->	

</body>
</html>