var Auth = {

    //перед авторизацией
    beforeLogin : function(form, data, hasError){
        $(form).find(".errorMessage").hide();
        Forms.disableBtn($(form).find(":submit"));
        $('.login-box').removeClass('shake'); //tada, flash, bounce, shake, swing, wobble, flipOutY, flipOutX, flipInY, flipOutX, pulse, flip
        return true;
    },

    //авторизация
    login : function(form, data, hasError){
        if (!hasError) {
            
            jPost(form.data("ajax-action"), form.serialize(), function(data){
                if(data.success) {

                    // form.get(0).reset();
                    
                    $('.login-box').addClass('flipOutY');
                    Forms.enableBtn($(form).find(":submit"));
                    
                    if(form.closest('#popup-login').length > 0) { //когда вход через попап
                        form.closest('#popup-login').hide();
                    }
                    else{
                        location = data.redirect;
                    }

                    return false;
                }
                else {
                    $('.login-box').addClass('shake'); //tada, flash, bounce, shake, swing, wobble, flipOutY, flipOutX, flipInY, flipOutX, pulse, flip
                    Forms.enableBtn($(form).find(":submit"));
                }
                
            }, "json");    
            
        }
        else{
            $('.login-box').addClass('shake'); //tada, flash, bounce, shake, swing, wobble, flipOutY, flipOutX, flipInY, flipOutX, pulse, flip
            Forms.enableBtn($(form).find(":submit"));
        }
        
        return false;
    },

    //перед разблокировкой
    beforeUnlock: function(form, data, hasError){
        $(form).find(".errorMessage").hide();
        Forms.disableBtn($(form).find(":submit"));
        $('.lockscreen-wrapper').removeClass('flipInX').removeClass('shake');
        return true;
    },

    //разблокировка
    unlock : function(form, data, hasError){
        if (!hasError) {
            jPost('', form.serialize(), function(data){
                if(data.success) {
                    $('.login-box').addClass('flipOutY');
                    location = data.redirect;
                    return false;
                }
                else {
                    //animate window
                    $('.lockscreen-wrapper').addClass('shake');
                    Forms.enableBtn($(form).find(":submit"));
                }
            }, "json");
        }
        else{
            //animate window
            $('.lockscreen-wrapper').addClass('shake');
            Forms.enableBtn($(form).find(":submit"));
        }
        
        return false;
    },

    //события на странице пользователей
    registerEvents : function(){
        
    },

    register : function(form, data, hasError){

    }
}
