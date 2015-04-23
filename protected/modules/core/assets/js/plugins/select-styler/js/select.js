
(function($){

    $.expr[':'].containsIgnoreCase = function (n, i, m) {
        return jQuery(n).text().toUpperCase().indexOf(m[3].toUpperCase()) >= 0;
    };

    $.expr[':'].startsWith = function(obj, index, meta, stack){
        return ($(obj).text().toUpperCase().indexOf(meta[3].toUpperCase()) == 0);
    };

    $.isEmpty = function(str){
        str = str.replace(' ', '');
        return $.isEmptyObject(str);
    }

    var selects = 0; //счетчик

    $.fn.selectStyler = function(options){


        this.each(function(){
            var index = selects++;

            //параметры нового select - a по умолчанию
            var params = {
                // size: 10,
                empty: false,
                search: false,
                filter: false,
                url: false,
            };

            //парамтры пользователя
            $.extend(params, $(this).data());

            var oldSelect = $(this); //старый select
            // var height; //высота всего списка
            var listWidth; //ширина списка
            var wrapper; //самый внешний div
            var selectOptions; //список option старого select-а
            var view; //текст select-а
            var list; //список select - a
            var listUl; //список select - a UL
            var selectText; //поле текста
            var resetSelect; //стрелка и сброс
            var selectHeight; //высота select-а
            var viewText; //default текст при multiselect
            var viewWidth; //ширина select - а
            var searchBlock; //поле поиска
            var empty; //первый элемент в списке
            var viewButton; //button
            var xhr; //обьект jq post
            var keyCodeArr = [13, 16, 17, 18, 19, 20, 27, 33, 34, 35, 36, 37, 38, 39, 40, 45, 46, 91, 92, 93, 112, 113, 114, 115, 116, 117, 118, 119, 120, 121, 122, 123, 144, 145];



            //эта фунцкция должна быть впереди
            var createListItems = function(){
                /**
                 * //так делаем, потому что при новом вызове selectStyler('update'),
                 * на этапе вызова некоторые переменные еще не проинициализированы
                 */
                selectOptions = oldSelect.find('option:not(.hidden)');

                listUl = oldSelect.closest('.selectStyler-wrap').find('.selectStyler-listWrap .selectStyler-list');

                listUl.empty(); //очистить список
                // console.log(selectOptions);
                selectOptions.each(function(){
                    var optionText = $(this).text();
                    var optionValue = $(this).val();
                    if($(this).data('content')){
                        optionText = $(this).data('content');
                    }

                    if(!$.trim(optionText) || $.trim(optionText) == undefined){
                        optionText = '&nbsp';
                    }

                    var li = $('<li data-val="'+optionValue+'"></li>').html(optionText).addClass($(this).attr('class')).appendTo(listUl);

                    var attributes = this.attributes;
                    for (var i = 0; i < attributes.length; i++) {
                        if(li.length > 0)
                        li.get(0).setAttribute(attributes[i].name, attributes[i].value);
                    };

                });

                if(params.filter && !oldSelect.prop('multiple')){
                    listUl.find('li[data-val=""]').remove();
                }


            };

            if(options == 'refresh'){ //если нужно обновить показ, после измемения select val
                createListItems();
                oldSelect.trigger('change');
                return true;
            }

            if(options == 'update'){ //если нужно обновить показ, после измемения select val без trigger('change')
                createListItems();
                var t = '';
                if(oldSelect.val() != "" && oldSelect.val() != null){
                    if($.isArray(oldSelect.val()) && (oldSelect.val().length > 1)){
                        var textArr = [];

                        for (var i = 0; i < oldSelect.val().length; i++) {
                            var option = selectOptions.filter('[value="'+oldSelect.val()[i]+'"]');
                            textArr.push(option.html());
                            t = textArr.join(', ');
                        };

                    }else{
                        t = selectOptions.filter('[value="'+oldSelect.val()+'"]').html();
                    }
                    oldSelect.closest('.selectStyler-wrap').find('.selectStyler-view span').removeClass('data-empty-attr');
                }
                else if(oldSelect.prop('multiple')){
                    t = oldSelect.attr('title');
                    oldSelect.closest('.selectStyler-wrap').find('.selectStyler-view span').addClass('data-empty-attr');
                }else {
                    t = selectOptions.first().html(); //первый елемент
                    oldSelect.closest('.selectStyler-wrap').find('.selectStyler-view span').addClass('data-empty-attr');
                }
                oldSelect.closest('.selectStyler-wrap').find('.selectStyler-view span').text(t);

                return true;
            }

            if(oldSelect.hasClass('selectStyler'))
                return true;

            $.extend(params, options); //это должно быть сдесь

            //при изменении нового select-а изменяется и старый
            var onSelect = function(event){
                var datavalue = $(event.target).attr('data-val');
                oldSelect.val(datavalue).trigger('change');
            };

            //при изменении нового select-а изменяется и старый
            var onMultiselect = function(event){
                var datavalue = $(this).attr('data-val');
                var val = selectOptions.filter('[value="'+datavalue+'"]');

                if(val.prop('selected') == true){
                    val.prop('selected', false)
                }
                else{
                    val.prop('selected', true)
                }

                oldSelect.trigger('change');
            };

            //при изменении старого select-а изменяется и новый
            var onChange = function(event){
                // event.preventDefault();
                // event.stopPropagation();

                var optionValue = event.target.value;

                reset(optionValue);
                list.hide();
                list.find('li').removeClass('selectStyler-blue');

                if (optionValue!=null && optionValue != '') {
                    var item = list.find('li[data-val="'+optionValue+'"]');
                    item.addClass('active');
                    selectText.html(list.find('.active').html());
                    selectText.removeClass('data-empty-attr');
                    resetSelect.show();
                }
                else{
                    selectText.html(empty.html());
                    selectText.addClass('data-empty-attr');
                    if(params.filter) resetSelect.hide();
                }
            };

            //при изменении старого select-а изменяется и новый
            var onMultichange = function(event){
                var optionValue = $(event.target).val();
                var textArr = [];

                reset(optionValue);

                if (optionValue!=null && optionValue != '') {
                    for (var i = 0; i < optionValue.length; i++) {
                        var item = list.find('li[data-val="'+optionValue[i]+'"]');
                        item.toggleClass('active');
                        if(item.hasClass('active')) textArr.push(item.html());
                    };

                    selectText.html(textArr.join(', '));
                    selectText.removeClass('data-empty-attr');
                    resetSelect.show();
                }
                else{
                    selectText.html(viewText);
                    selectText.addClass('data-empty-attr');
                    if(params.filter) resetSelect.hide();
                }
            };

            //поиск в списке
            var onSearch = function(event){

                var val = event.target.value;
                if($.inArray(event.keyCode, keyCodeArr) == -1){
                    list.find('li:not(.data-empty-attr)').show();
                    list.find('li').removeClass('selectStyler-blue');
                    if(val){
                        list.find('li').not(list.find('li:containsIgnoreCase('+val+')')).not('.active').hide();
                    }
                    list.find('li:visible').not('.active, .noRemove').first().addClass('selectStyler-blue');
                    listSize();
                }

                if(list.find('.selectStyler-blue').length!=0){
                    if(list.find('.selectStyler-blue').position().top<0 || list.find('.selectStyler-blue').position().top > listUl.outerHeight())
                        listUl.scrollTop((list.find('.selectStyler-blue').index()-list.find('.selectStyler-blue').prevAll('li:hidden').length)*list.find('.selectStyler-blue').outerHeight());
                }
            };

            //поиск $.post
            var onBigSearch = function(event){
                var val = event.target.value;

                if($.inArray(event.keyCode, keyCodeArr) == -1){
                    if(val.length >=2){
                        if(xhr) xhr.abort();

                        xhr = jPost(oldSelect.data('url'), {searchStr:val}, function(data){
                            if(data.success){
                                selectOptions.not(':selected, .noRemove').remove();
                                $.each(data.items, function(value, label){
                                    if($.inArray(value, oldSelect.val()) == -1){
                                        var option = $('<option/>').val(value).text(label).appendTo(oldSelect);
                                    }
                                });

                                createListItems();
                                listSize();

                                if(oldSelect.val()!=null && oldSelect.val() != ''){
                                    for (var i = 0; i < oldSelect.val().length; i++) {
                                        list.find('li[data-val="'+oldSelect.val()[i]+'"]').addClass('active');
                                    };
                                }
                                list.find('li:visible').not('.active, .noRemove').first().addClass('selectStyler-blue');

                            }
                        }, 'json');
                    }else{
                        selectOptions.not(':selected, .noRemove').remove();
                        createListItems();
                        listSize();

                        if(oldSelect.val()!=null && oldSelect.val() != ''){
                            for (var i = 0; i < oldSelect.val().length; i++) {
                                list.find('li[data-val="'+oldSelect.val()[i]+'"]').addClass('active');
                            };
                        }
                        list.find('li:visible').not('.active, .noRemove').first().addClass('selectStyler-blue');
                    }
                }

            };

            //сброс
            var reset = function(optionValue){
                if(params.filter){
                    if(optionValue){
                        resetSelect.addClass('resetSelect')
                    }else{
                        resetSelect.removeClass('resetSelect');
                    }
                }
                list.find('li').removeClass('active');
            };

            //размеры списка
            var listSize = function(){
                list.show();
                if(params.search) searchBlock.parent().prependTo(list);
                var height;
                var windowHeight = $(window).height();
                var windowWidth = $(window).width();
                var scrollTop = $(window).scrollTop();
                var selectTop = view.offset().top;
                var selectLeft = view.offset().left;
                var footer = windowHeight + scrollTop - selectTop - selectHeight;
                var header = selectTop - scrollTop;
                var bottom = windowHeight + scrollTop;

                var liHeight = listUl.find('li').not(':hidden').actual('outerHeight');
                var liCount = listUl.find('li').not(':hidden').length;

                if(params.size){
                    height = params.size * liHeight;
                }else{
                    height = liCount * liHeight;
                }


                if(footer >= header){
                    if(footer <= (height + 40)){
                        listUl.height(footer - selectHeight - 30);
                    }else{

                        listUl.height(height);
                    }
                    list.offset({top:(selectTop + selectHeight+3), left:selectLeft});
                }

                if(footer < header){
                    if((height + 40) >= footer){
                        if(height >= header){
                            if(params.search){
                                listUl.height(header - searchBlock.actual('outerHeight') - 14);
                                list.offset({top:(scrollTop + 5), left:selectLeft});
                                searchBlock.parent().appendTo(list);
                            }else{
                                listUl.height(header-14);
                                list.offset({top:(scrollTop + 10), left:selectLeft});
                            }

                        }else{
                            listUl.height(height);
                            if(params.search){
                                list.offset({top:(selectTop - height - searchBlock.actual('outerHeight') -10), left:selectLeft});
                                searchBlock.parent().appendTo(list);
                            }else{
                                list.offset({top:(selectTop - height-5), left:selectLeft});
                            }

                        }
                    }
                    else{
                        listUl.height(height);
                        list.offset({top:(selectTop + selectHeight+3), left:selectLeft});
                    }
                }

                if((selectLeft+list.actual('outerWidth')+15)>(windowWidth+$(window).scrollLeft())){
                    list.offset({top:list.offset().top, left:((windowWidth+$(window).scrollLeft())-list.actual('outerWidth')-15)});
                }

                if(params.search){
                    // listUl.height(listUl.outerHeight() - searchBlock.outerHeight()-1);
                    list.height(listUl.outerHeight() + searchBlock.outerHeight()+8);
                    searchBlock.trigger('focus');
                }else{
                    list.height(listUl.outerHeight()+1);
                }

                //клик вне нового select - a скрывает все списки
                $(document).on('click.hideSelectStylerList' + index, function(event){
                    event.stopPropagation();

                    var target = $(event.target);
                    if(target.closest(wrapper).length == 0){
                        list.hide();
                        list.find('li').removeClass('selectStyler-blue');
                        view.trigger('blur');
                        $(document).unbind('click.hideSelectStylerList' + index);
                    }
                });
            };


            //toggle список нового select - a
            var togglelist = function(event){
                event.preventDefault();

                if(wrapper.hasClass('disabled')) return;

                if(list.is(':hidden')){
                    listSize();
                }else{
                    list.hide();
                    list.find('li').removeClass('selectStyler-blue');
                    view.trigger('blur');
                }

            };

            //выделение в списке
            var mark = function(event, keypressVal){

                list.find('li').removeClass('selectStyler-blue');

                list.find('li:startsWith("'+keypressVal+'")').first().addClass('selectStyler-blue');
            };


            var keypressSearch = function(){
                var keypressVal = '';

                function clear(){
                    keypressVal = '';
                }

                wrapper.on('keydown', function(event){

                    //при нажатие на enter отметить
                    if(event.keyCode == 13){
                        event.preventDefault();
                        event.stopPropagation();
                        list.find('.selectStyler-blue').trigger('click');
                        view.trigger('blur');
                    }

                    //нажатие на стрелку вниз
                    if(event.keyCode == 40){
                        event.preventDefault();

                        var downArrow = list.find('.selectStyler-blue');
                        downArrow.nextAll('li:visible').first().addClass('selectStyler-blue');
                        downArrow.removeClass('selectStyler-blue');
                        if(downArrow.nextAll('li:visible').first().index()==-1){
                            list.find('li:visible').first().addClass('selectStyler-blue');
                        }
                        if(list.find('.selectStyler-blue').length!=0){
                            if((list.find('.selectStyler-blue').position().top+30) > listUl.outerHeight() || list.find('.selectStyler-blue').position().top<=0)
                                listUl.scrollTop((list.find('.selectStyler-blue').index()-list.find('.selectStyler-blue').prevAll('li:hidden').length)*list.find('.selectStyler-blue').outerHeight()-listUl.outerHeight()+30);
                        }
                    }

                    //нажатие на стрелку вверх
                    if(event.keyCode == 38){
                        event.preventDefault();

                        var upArrow = list.find('.selectStyler-blue');
                        upArrow.prevAll('li:visible').first().addClass('selectStyler-blue');
                        upArrow.removeClass('selectStyler-blue');
                        if(upArrow.prevAll('li:visible').first().index()==-1){
                            list.find('li:visible').last().addClass('selectStyler-blue');
                        }
                        if(list.find('.selectStyler-blue').length!=0){
                            if(list.find('.selectStyler-blue').position().top<0 || list.find('.selectStyler-blue').position().top > listUl.outerHeight())
                                listUl.scrollTop((list.find('.selectStyler-blue').index()-list.find('.selectStyler-blue').prevAll('li:hidden').length)*list.find('.selectStyler-blue').outerHeight());
                        }
                    }

                });

                //поиск без поля
                wrapper.on('keypress', function(event){

                    if(keypressVal==''){
                        keypressVal = keypressVal + String.fromCharCode(event.which);
                        mark(event, keypressVal);
                        setTimeout(clear, 1000);

                    }else{
                        keypressVal = keypressVal + String.fromCharCode(event.which);
                        mark(event, keypressVal);
                    }
                    if(list.find('.selectStyler-blue').length!=0)
                        listUl.scrollTop((list.find('.selectStyler-blue').index()-list.find('.selectStyler-blue').prevAll('li:hidden').length)*list.find('.selectStyler-blue').outerHeight()-listUl.outerHeight()+30);
                });

            };

            /**
             * Инициализация
             * -------------------------------
             */

            $(this).addClass('selectStyler'); //используется для дальнейшего определения, применялся ли selectStyler

            /**
             * создание нового select-а
             * -----------------------------
             */

            //wrapper
            $(this).wrap('<div class="selectStyler-wrap"/>');
            wrapper = $(this).closest('.selectStyler-wrap');

            if(params.width == '100%'){
                wrapper.addClass('block');
            }

            wrapper.addClass(oldSelect.attr('class'));//добавляем классы старого select - a новому

            selectOptions = $(this).find('option:not(.hidden)');

            if(params.url) selectOptions.addClass('noRemove');

            //текст select - a
            view = $('<div/>').addClass('selectStyler-view').appendTo(wrapper);

            //список select - a
            listUl = $('<ul/>').addClass('selectStyler-list').appendTo(wrapper).wrap('<div/>');
            list = listUl.parent().addClass('selectStyler-listWrap');
            //add button
            viewButton = $('<button type="button"/>').appendTo(view);

            //поле текста
            selectText = $('<span/>').addClass('selectText').appendTo(viewButton);

            //знак стрелка и сброс
            resetSelect = $('<div/>').appendTo(view);

            if(oldSelect.val() == null || oldSelect.val() == ''){
                selectText.addClass('data-empty-attr');
                if(params.filter) resetSelect.hide();
            }

            if(!params.filter){
                resetSelect.addClass('selectArrow');
            }

            //высота select - a
            selectHeight = view.outerHeight();

            //ширина select - a
            if(params.width){
                viewWidth = params.width;
            }else{
                viewWidth = $(this).outerWidth();
            }

            if(params.width == 'auto') viewWidth = view.actual('outerWidth');
            view.outerWidth(viewWidth);

            //создание option-ов для нового select - а,
            createListItems();

            //ширина списка
            listWidth = 100;
            if((listUl.actual('outerWidth')+20) >= listWidth){
                listWidth = listUl.actual('outerWidth')+40;
            }
            if(view.actual('outerWidth') >= listWidth){
                listWidth = view.actual('outerWidth');
            }

            list.width(listWidth);
            listUl.width('100%');


            // добавляем класс 'data-empty-attr' первому элементу списка
            // list.find('ul li:first').addClass('data-empty-attr');

            //EVENTS

            //если select multiple
            if(oldSelect.prop('multiple')){

                wrapper.addClass('multiple');

                if(params.empty){
                    oldSelect.attr('title', params.empty)
                }

                viewText = oldSelect.attr('title');
                selectText.text(viewText);

                //при изменении нового select-а изменяется и старый
                list.on('click', 'li', onMultiselect);

                //при изменении старого select-а изменяется и новый
                oldSelect.on('change', onMultichange);

                if (oldSelect.val()!=null && oldSelect.val() != '') {
                    var val = oldSelect.val();
                    var arr = Array();
                    reset(val);

                    for (var i = 0; i < val.length; i++) {
                        var item = list.find('li[data-val="'+val[i]+'"]');
                        item.toggleClass('active');
                        if(item.hasClass('active')) arr.push(item.html());
                    };

                    selectText.html(arr.join(', '));
                    selectText.removeClass('data-empty-attr');
                }
                else{
                    selectText.html(viewText);
                    selectText.addClass('data-empty-attr');
                }

            }
            //если обычный select
            else{

                if(params.empty){
                    empty = $('<option/>').text(params.empty).prependTo(oldSelect);
                    // list.find('ul li:first').addClass('data-empty-attr');
                }
                else{
                    empty = selectOptions.filter('[value=""]');
                    var val = oldSelect.val();
                    if(!$.isEmpty(val)){
                        reset(val);

                        list.find('li[data-val="'+val+'"]').addClass('active');
                        selectText.html(selectOptions.filter('[value="'+val+'"]').html());

                    }else{
                        selectText.text(selectOptions.first().text());
                    }
                }

                // list.find('ul li:first').addClass('data-empty-attr');

                //при изменении нового select-а изменяется и старый
                list.on('click', 'li', onSelect);

                //при изменении старого select-а изменяется и новый
                oldSelect.on('change', onChange);
            }

            //поле поиска
            if(params.search){
                searchBlock = $('<input type=text>').prependTo(list);
                searchBlock.wrap('<div/>').parent().addClass('selectStyler-searchWrap');

                list.height(listUl.outerHeight() + searchBlock.outerHeight());

                if(params.placeholder){
                    searchBlock.attr('placeholder', params.placeholder);
                    if(params.focusplaceholder){
                        searchBlock.on('focusin', function(){
                            searchBlock.attr('placeholder', params.focusplaceholder);
                        });

                        searchBlock.on('focusout', function(){
                            searchBlock.attr('placeholder', params.placeholder);
                        })
                    }
                }

                //поиск в списке (в поле для поиска)
                if(params.url){
                    searchBlock.on('keyup', onBigSearch);
                }else{
                    searchBlock.on('keyup', onSearch);
                }
            }
            else{
                list.height(listUl.outerHeight());
            }


            //скрываем список
            list.hide();

            // toggle список нового select - a
            view.on('click', togglelist);

            //скрыть при нажатии на escape
            $(document).on('keyup', function(event){
                if(event.which == 27){
                    list.hide();
                    list.find('li').removeClass('selectStyler-blue');
                }
            })

            //сброс select - a
            wrapper.on('click', '.resetSelect', function(event){
                event.stopPropagation();
                event.preventDefault();

                if(wrapper.hasClass('disabled')) return;

                $(this).removeClass('resetSelect');
                list.toggle();
                oldSelect.val('').trigger('change');
                selectText.addClass('data-empty-attr');
                list.hide();
                list.find('li').removeClass('selectStyler-blue');
                view.trigger('blur');
            });


            //поиск без поля
            keypressSearch();

            $(this).css({'display':'none'});
        });
    };
})(jQuery);


