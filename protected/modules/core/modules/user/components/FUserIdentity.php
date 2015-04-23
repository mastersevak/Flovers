<?php

/**
 * UserIdentity represents the data needed to identity a user.
 * It contains the authentication method that checks if the provided
 * data can identity the user.
 */
class FUserIdentity extends UserIdentity
{
    public function authenticate()
    {
        $criteria = new SDbCriteria;
        $criteria->compare(String::isEmail($this->username) ? 'email' : 'username', $this->username);

        $user = User::model()
                    ->noSocial()
                    ->active()
                    ->find($criteria);

        if($user===null)
            $this->errorCode=self::ERROR_USERNAME_INVALID;
        else if(!$user->validatePassword($this->password))
            $this->errorCode=self::ERROR_PASSWORD_INVALID;
        else
        {
            $this->_id = $user->id;
            $this->errorCode = self::ERROR_NONE;
        }
        
        return $this->errorCode == self::ERROR_NONE;
    }

}