<?php

/**
 * UserIdentity represents the data needed to identity a user.
 * It contains the authentication method that checks if the provided
 * data can identity the user.
 */
class UserIdentity extends CUserIdentity
{
    protected $_id;
 
    public function getId()
    {
        return (int) $this->_id;
    }

}