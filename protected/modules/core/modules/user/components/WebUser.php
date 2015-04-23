<?php
class WebUser extends CWebUser
{
    private $_model;
    public $userModel = 'User';
    public $frontUserIdentity = 'FUserIdentity';
    public $backUserIdentity = 'BUserIdentity';

    // Переход к данным пользователя
    public function getModel()
    {
        if ( $this->_model === null ){
            $this->_model = CActiveRecord::model($this->userModel)->findByPk($this->id);
        }

        return $this->_model;
    }

    public function UpdateInfo($user){

        $this->id = $user->id;
        $this->setState('email', $user->email);
        $this->setState('logintime', time());
        $this->setState('fullname', $user->getFullName());
        $this->setState('is_social', $user->is_social_user);
        $this->setState('avatar', $this->createAvatar('thumb', 35));
        $this->setState('username', $user->username);
    }

    public function createAvatar($size = 'thumb', $width = false){
        return user()->model->getThumbnail($size, $width, false, $this->getState('fullname'));
    }

    public function isRole($role){ 
        if($this->isGuest) return false;

        $item = app()->authManager->getAuthItem($role);
        if(!$item || $item->type != CAuthItem::TYPE_ROLE) return false;

        if(app()->authManager->isAssigned($role, $this->id))
            return true;
        
        
        $roles = Yii::app()->authManager->getRoles($this->id);
        foreach($roles as $roleName=>$roleInfo){
            if(app()->authManager->hasItemChild($roleName, $role))
                return true;
        }

        return false;
    }

    public function isJob($idJob){
        return user()->getState('job') == $idJob;
    }

    public function getGridIndex(){
        if($this->getState('gridIndex'))
            return $this->getState('gridIndex');
        else Yii::app()->controller->createUrl('index');
    }


    //эта функция для того чтобы данные getState не стирались после allowAutoLogin
    public function afterLogin($fromCookie){
        $user = CActiveRecord::model($this->userModel)->active()->findByPk($this->id);
        if($user){
            // Mark the user as a superuser if necessary.
            if( Rights::getAuthorizer()->isSuperuser($this->getId())===true ){
                $this->isSuperuser = true;
            }

            $this->UpdateInfo($user);

            // зафиксируем время входа
            $user->last_visit = time();
            $user->update(array('last_visit'));

            //создаем хеш ключ для сравнения при следущем входе
            $hash = $user->generateHashKey();
            
            if(CActiveRecord::model($this->userModel)->updateByPk($this->id, array('hash'=>$hash)))        
                $this->setHashKey($hash, $fromCookie);

        }
        else {
            $this->logout();
        }

        parent::afterLogin($fromCookie);
    }


    //проверка соответствия хеша в базе и в куки перед входом
    public function beforeLogin($id, $states, $fromCookie){

        if(parent::beforeLogin($id, $states, $fromCookie)){

            $user = CActiveRecord::model($this->userModel)->active()->findByPk($id);
            if($user){
                $this->id = $id;

                // $hashKey = $this->getHashKey($fromCookie);
                // dump([$this->getHashKey($fromCookie), $fromCookie], true);
                // $result = $fromCookie || $hashKey === null || $hashKey == $user->hash;
            }
            else{
                $this->logout();
            }
            return true;
        }

        return false;
    }


    /**
     * Вход от имени другого пользователя
     * @param  [type] $model - либо id, либо модель пользователя
     */
    public function loginAs($model){
        if(is_numeric($model)){
            $model = CActiveRecord::model($this->userModel)->findByPk($model);
        }

        if($model){

            $identity = $this->backUserIdentity;
            $identity = new $identity($model->username, $model->password);

            $identity->authenticate(true);
            
            if($identity->errorCode == CBaseUserIdentity::ERROR_NONE){
                $this->login($identity);

                return true;
            }
        }

    }

    public function setHashKey($hash, $fromCookie){
        
        $this->setState('hash_key', $hash);

        //get cookie
        $data = [];
        $app=Yii::app();
        $request=$app->getRequest();
        $cookie=$request->getCookies()->itemAt($this->getStateKeyPrefix());
        if($cookie && !empty($cookie->value) 
                    && is_string($cookie->value) 
                    && ($data=$app->getSecurityManager()->validateData($cookie->value))!==false)
        {
            $data=@unserialize($data);
        }

        // //set cookie
        // $cookie=$this->createIdentityCookie($this->getStateKeyPrefix());
        // $cookie->expire = time()+$data[2];
        // $data[] = $hash; // добавил hash
        // $cookie->value=$app->getSecurityManager()->hashData(serialize($data));
        // $app->getRequest()->getCookies()->add($cookie->name, $cookie);

        //set session hash
        
    }

    public function getHashKey($fromCookie){
        if($fromCookie){
            $app=Yii::app();
            $request=$app->getRequest();
            $cookie=$request->getCookies()->itemAt($this->getStateKeyPrefix());
            if($cookie && !empty($cookie->value) 
                        && is_string($cookie->value) 
                        && ($data=$app->getSecurityManager()->validateData($cookie->value))!==false)
            {
                $data=@unserialize($data);
                return isset($data[4]) ? $data[4] : $data;
            }

            return null;
        }
        else
            $this->getState('hash_key');
    }
    /**
    * Performs access check for this user.
    * Overloads the parent method in order to allow superusers access implicitly.
    * @param string $operation the name of the operation that need access check.
    * @param array $params name-value pairs that would be passed to business rules associated
    * with the tasks and roles assigned to the user.
    * @param boolean $allowCaching whether to allow caching the result of access checki.
    * This parameter has been available since version 1.0.5. When this parameter
    * is true (default), if the access check of an operation was performed before,
    * its result will be directly returned when calling this method to check the same operation.
    * If this parameter is false, this method will always call {@link CAuthManager::checkAccess}
    * to obtain the up-to-date access result. Note that this caching is effective
    * only within the same request.
    * @return boolean whether the operations can be performed by this user.
    */
    public function checkAccess($operation, $params=array(), $allowCaching=true)
    {
        // Allow superusers access implicitly and do CWebUser::checkAccess for others.
        return $this->isSuperuser===true ? true : parent::checkAccess($operation, $params, $allowCaching);
    }

    /**
    * @param boolean $value whether the user is a superuser.
    */
    public function setIsSuperuser($value)
    {
        $this->setState('Rights_isSuperuser', $value);
    }

    /**
    * @return boolean whether the user is a superuser.
    */
    public function getIsSuperuser()
    {
        return $this->getState('Rights_isSuperuser');
    }
    
    /**
     * @param array $value return url.
     */
    public function setRightsReturnUrl($value)
    {
        $this->setState('Rights_returnUrl', $value);
    }
    
    /**
     * Returns the URL that the user should be redirected to 
     * after updating an authorization item.
     * @param string $defaultUrl the default return URL in case it was not set previously. If this is null,
     * the application entry URL will be considered as the default return URL.
     * @return string the URL that the user should be redirected to 
     * after updating an authorization item.
     */
    public function getRightsReturnUrl($defaultUrl=null)
    {
        if( ($returnUrl = $this->getState('Rights_returnUrl'))!==null )
            $this->returnUrl = null;
        
        return $returnUrl!==null ? CHtml::normalizeUrl($returnUrl) : CHtml::normalizeUrl($defaultUrl);
    }
}
