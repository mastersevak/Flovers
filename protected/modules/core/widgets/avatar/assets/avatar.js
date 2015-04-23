// Required for drag and drop file access
jQuery.event.props.push('dataTransfer');


(function ( $ ) {
 
    $.fn.avatar = function( options ) {
 
        return this.each(function() {

            var body = $(this);
            var img = body.find('.image');
            var fileInput = body.find('input[type=file]');
           
            var settings = $.extend({
                autoSave: false,
                allowed: ['jpg', 'jpeg', 'gif', 'png']
            }, options );
 

            function bindUIActions() {

                var timer;

                $('body').on("dragover", function(event) {
                    clearTimeout(timer);
                    if ($(this).find(body).filter(':visible').length > 0) {
                        showDroppableArea();
                    }

                    // Required for drop to work
                    return false;
                });

                $('body').on('dragleave', function(event) {
                    if (event.currentTarget /*== body[0]*/) {
                        // Flicker protection
                        timer = setTimeout(function() {
                            hideDroppableArea();
                        }, 200);
                    }
                });

                $('body').on('drop', function(event){
                    hideDroppableArea();
                    return false;
                });

                body.on('drop', function(event) {

                    if(event.currentTarget == body[0]){
                        // Or else the browser will open the file
                        event.preventDefault();

                        handleDrop(event.dataTransfer.files);
                    }
                    
                });

                fileInput.on('change', function(event) {
                    handleDrop(event.target.files);
                });

                fileInput.parent().parent().find('a.upload').on('click', function(e){
                    e.preventDefault();
                    fileInput.trigger('click');
                });
            } // bindUIActions

            function showDroppableArea() {
                //create overlay
                if($('body > .avatar-overlay').length == 0){
                    $('body').append($('<div class="avatar-overlay" />'));
                }

                $('body').addClass("droppable");
                body.addClass("droppable");
            }

            function hideDroppableArea() {
                //remove overlay
                $('body > .avatar-overlay').remove();
                $('body').removeClass("droppable");
                body.removeClass("droppable");
            }

            function handleDrop(files) {

                hideDroppableArea();

                // Multiple files can be dropped. Lets only deal with the "first" one.
                var file = files[0];

                /**
                * для проверки типа используем проверку
                * if(file.type.match('image.*')) или 
                * if(file.type == 'image/png' или что нибудь другое)
                *
                * для проверки размера изображения, используем
                * if(file.size >= bytes)
                */

                if (typeof file !== 'undefined' && file.type.match('image.*')) {
                    //проверка на соответствие по типу
                    var validExt = false;
                    $.each(settings.allowed, function(index, allowedExt) {
                        
                        var extRegex = new RegExp('\\.' + allowedExt + "$", 'i');

                        if (file.name.match(extRegex) != null) {
                            validExt = true;
                        }
                    });

                    if(!validExt){
                        alert("Не разрешенный формат");
                        return false;
                    }

                    resizeImage(file, img.width(), img.height(), function(data) {
                        placeImage(data);

                        //автозагрузка изображения
                        if(body.data('model-id') && settings.autoSave){
                            
                            var formData = new FormData();

                            formData.append(fileInput.attr('name'), file);
                            formData.append(getCsrf().name, getCsrf().value);
                            
                            $.ajax({
                                url: body.data('upload-url'), 
                                data: formData, 
                                type: 'POST',
                                dataType: 'json',
                                cache: false,
                                contentType: false,
                                processData: false,
                                success: function(data){

                                    if(data.success){
                                        fileInput.val('');
                                        placeImage(data.src);

                                        img.prev().addClass('visible');
                                    }
                                    
                                } 
                            });
                        }
                    });

                } else if(file.type == 'application/x-shockwave-flash'){
                    //TODO: отобразить swf
                    return false;
                }
                else{
                    alert("Загружаемый файл не является картинкой");
                    return false;
                }

            } // handleDrop

            function resizeImage(file, width, height, callback) {

                var fileTracker = new FileReader;
                
                fileTracker.onload = function() {
                    Resample(this.result, width, height, callback);
                }
            
                fileTracker.readAsDataURL(file);

                fileTracker.onabort = function() {
                    alert("The upload was aborted.");
                }

                fileTracker.onerror = function() {
                    alert("An error occured while reading the file.");
                }

            }

            function placeImage(data) {
                img.attr("src", data);
            }

            //инициализация
            bindUIActions();

        });

    }
 
}( jQuery ));