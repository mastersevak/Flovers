yii-facebook-opengrpah
======================

It includes the method for extending the short lived token to 60 days valid token.

example to extend toekn<br/> <br/> 
           
           try{
                $facebook_uid = $facebook->getUser();
                $facebook->setExtendedAccessToken();
                $accessToken = $facebook->getAccessToken();
                $fbuser = $facebook->api('/me');
        }catch (Exception $e) {
                facebook_uid =null;
        }
        
  setExtendedAccessToken(); will replace the 1 hour valid token to 60 dayas valid token .
  this extention includes token handling according  recent facebook migration.
  
