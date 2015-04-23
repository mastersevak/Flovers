<?php 


/**
 * UserSearchBehavior
 */
 class UserSearchBehavior extends CActiveRecordBehavior
 {
 	
 	public function compareUser($criteria, $value, $relation = 'user'){
 	
 		if($value){
	        $parts = explode(' ', $value);
	        foreach($parts as $key => $part){
	        	$criteria->addCondition("{$relation}.firstname LIKE :part_{$key} OR {$relation}.lastname LIKE :part_{$key} OR {$relation}.middlename LIKE :part_{$key}");
	        	$criteria->params[":part_{$key}"] = "%$part%";
	        }	
        }
 	}

 	public function compareUserOld($criteria, $value, $relation = 'user'){
 		
 		if($value){
	        $parts = explode(' ', $value);
	        foreach($parts as $key => $part){
	        	$criteria->addCondition("{$relation}.name LIKE :part_{$key} OR {$relation}.last_name LIKE :part_{$key} OR {$relation}.middle_name LIKE :part_{$key}");
	        	$criteria->params[":part_{$key}"] = "%$part%";
	        }	
        }
 	}


 }