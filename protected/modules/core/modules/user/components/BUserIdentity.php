<?php

/**
 * UserIdentity represents the data needed to identity a user.
 * It contains the authentication method that checks if the provided
 * data can identity the user.
 */
class BUserIdentity extends UserIdentity
{
    const ERROR_USER_BLOCKED = 3;

    public $user;

    public function authenticate($hashedPassword = false)
    {
        $criteria = new SDbCriteria;

        $blockedUsers = Yii::app()->db
                                ->createCommand("SELECT DISTINCT username from {{user_block}}")
                                ->queryColumn();
        //заблокированные ip, это те которые указаны без имени пользователя, 
        //чтобы не блокировать всех пользователей, из которого один пользователь заблокировался
        $blockedIps = Yii::app()
                        ->db->createCommand("SELECT DISTINCT ip from {{user_block}} where username is null or username = ''")
                        ->queryColumn();

        if(in_array(Yii::app()->request->userHostAddress, $blockedIps) || 
            in_array($this->username, $blockedUsers)){
           
            $this->errorCode=self::ERROR_USER_BLOCKED;
            return false;
        }
        
        $criteria->compare('username', $this->username);
        $criteria->addNotInCondition('username', $blockedUsers);

        $user = User::model()->noSocial()->active()->find($criteria);

        //если пользователя  нет, либо его роли не разрешен доступ к dashboard
        if($user===null || 
            (!$user->isRole(app()->getModule('core')->getModule('rights')->superuserName) && 
                !Yii::app()->getAuthManager()->checkAccess('Core.Admin.Back.Index', $user->id)) ){
            $this->errorCode=self::ERROR_USERNAME_INVALID;
            $this->blockUser($user);
        }
        else if(!$user->validatePassword($this->password, $hashedPassword)){
            $this->errorCode=self::ERROR_PASSWORD_INVALID;
            $this->blockUser($user);
        }
        else{
            $this->_id = $user->id;
            $this->errorCode = self::ERROR_NONE;
           
            app()->user->setState('webuserModel', 'User');
        }

        if($this->errorCode == self::ERROR_NONE) $this->user  = $user;
        
        return $this->errorCode == self::ERROR_NONE;
    }

    private function blockUser($user){
        //TODO: в случае более чем 5ти неправильных попыток блокировать пользователя
        if($this->username && $this->password){
            $cnt = (int)Yii::app()->user->getState('loginfailed_'.$this->username, 0);

            if($cnt >= 5) {
                //block user
                $blockuser = new UserBlock;
                $blockuser->id_user = $user->id;
                $blockuser->username = $this->username;
                $blockuser->ip = Yii::app()->request->userHostAddress;
                $blockuser->date = date('Y-m-d H:i:s');

                $blockuser->save();

                Yii::app()->user->setState('loginfailed_'.$this->username, NULL);
            }
            else 
                Yii::app()->user->setState('loginfailed_'.$this->username, ++$cnt);
        }
    }

    
}