<?php
include_once("inc/facebook.php"); //include facebook SDK
 
######### edit details ##########
$appId = '150942661753605'; //Facebook App ID 
$appSecret = 'c4f899a77f782df662223359a973fddd'; // Facebook App Secret
$return_url = 'http://goodsamaritan.am/publish_to_wall/process.php';  //return url (url to script)
$homeurl = 'http://goodsamaritan.am/publish_to_wall/';  //return to home
$fbPermissions = 'publish_stream,manage_pages';  //Required facebook permissions
##################################

//Call Facebook API
$facebook = new Facebook(array(
  'appId'  => $appId,
  'secret' => $appSecret
));

$fbuser = $facebook->getUser();
?>