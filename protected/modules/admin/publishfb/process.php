<?
// Graph API Methods

// To call a Graph API method, pass in the endpoint path you wish to retrieve as the first
// parameter, and other optional parameters as necessary:

// $ret = $facebook->api($path, $method, $params);

// Name	Description
// path	The Graph API path for the request, e.g. "/me" for the logged in user's profile.
// method	(optional) Specify the HTTP method for this request: 'GET', 'POST', or 'DELETE'.
// params	(optional) Parameters specific to the particular Graph API method you are calling.
// Passed in as an associative array of 'name' => 'value' pairs.

// posts message on page feed

// == example ==
	// $msg_body = array(
	// 		'message' 		=> $userMessage,
	// 		'name' 			=> $name,
	// 		'caption' 		=> $caption,
	// 		'link' 			=> $link,		//if is set link, post comes from user` named 
	// 		'description' 	=> $description,
	// 		'picture' 		=> $picture,
	// 		'actions' => array(
	// 						array(
	// 							'name' => 'Saaraan',
	// 							'link' => 'http://www.goodsamaritan.am'
	// 						)
	// 					),
	//		'countries'		=> array('AM','DE'),
	//		'scheduled_publish_time' => '1342047241', //unix time 
	//		'published' 	=> true //default true
	// );

?>


<?php
include_once("config.php");

if(!empty($_POST)){

	//Post variables we received from user
	$userPageId = $_POST["userpages"];
	//HTTP POST request to PAGE_ID/feed with the publish_stream
	$post_url = '/'.$userPageId.'/feed';

	$msg_body = array(
			'message' 		=> 'My Message',
			'name' 			=> 'Title',
			'caption' 		=> 'Sub title',
			'link' 			=> 'http://mega-real.ru',		//if is set link, post comes from user` named 
			'description' 	=> 'My custom description',
			'picture' 		=> 'http://mega-real.ru/upload/images/photo/96/41/big/irfOLDimk.JPG',
	);

	if ($fbuser) {
		try {
			$postResult = $facebook->api($post_url, 'post', $msg_body);
		}
		catch (FacebookApiException $e) {
			echo $e->getMessage();
		}
			
	}
	else {
		$loginUrl = $facebook->getLoginUrl(array('redirect_uri'=>$homeurl, 'scope'=>$fbPermissions));
		header('Location: ' . $loginUrl);
	}
	
	//Show sucess message
	if($postResult)
	 {
		//echo success
	 }
}
 
?>
