<div class="login-box animated clearfix">
    
    <div class="logo clearfix">
        <h1 class="mt0">{param('settings/backendHeaderSiteName')}</h1>
    </div>
    
    <hr>

    {form name="form" 
        enableClientValidation=false
        enableAjaxValidation=true
        clientOptions=[
            validateOnSubmit => true,
            beforeValidate => 'js:Auth.beforeLogin',
            afterValidate => 'js:Auth.login'
        ]
        action=$action
        htmlOptions=['data-ajax-action' => url('/core/user/back/ajaxlogin'), autocomplete=>'off']
    }
        <div class="control-group">
            {$form->label($model, 'username', ['class'=>'control-label'])}
            <div>
                {$form->textField($model, 'username', ['class'=>'w100p'])}
                {$form->error($model, 'username')}
            </div>
        </div>
        
        <div class="control-group">
            {$form->label($model, 'password', ['class'=>'control-label'])}
            <div>
                {$form->passwordField($model, 'password', ['class'=>'w100p'])}
                {$form->error($model, 'password')}
            </div>
        </div>
        
        <input type="submit" class="btn btn-success w100p mt10 mb10" value="ВОЙТИ">
        
        <div class="checkbox check-success mt20">
            {$form->checkBox($model, 'rememberMe')}
            {$form->label($model, t('user', 'rememberMe'))}
        </div>
    
    {/form}
</div>  
