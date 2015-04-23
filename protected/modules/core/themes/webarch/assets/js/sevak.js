(function($){
    $.fn.CategoryTree = function (){ 

        var list_click = {
        
            show_lists : function(event){
                event.preventDefault();
                var element = $(this);
                var a       = element.find('a');
                var id      = a.data('id');
                var deep    = a.data('deep');
                var parent  = a.data('parent');
                var div     = element.closest('div');
                var next    = div.next();
                var divs    = element.closest('.choosevay').find('div'); // vercnum e 1,2,3 ev pahest diver@
                var pahest  = divs.filter(':last');
                var index   = div.index(); // stugum enq vor divi mej e click@ 
                var text    = element.text();
                var menuLi  = element.closest('.iblock').find('ul:first > li');                  
                var divsLength = divs.length;

                // stugum enq ete verjinin click chen qrel tox lcni hajord divi mej
                if ( div.index() != (divs.filter(':last').index() - 1) ){
                    if( element.find('span').attr('class') == 'icon' ){
                        //stugum enq active clas unecox li -in enq click arel                       
                        if( 0 != next.find('a').data('parent') ){
                            if(index == 0 && deep >= 1){// ete arajin divn e ev(&&) ev xorutyun@ 1 e ev aveli bardzr
                                
                                list_click.goToBack(next, pahest, divs, div, deep, divsLength, menuLi);
                            }
                            else{
                                var menuText = menuLi.eq( div.index()+1 );
                                // menui texti popoxvel@
                                menuText.find('a').text(text);
                                menuText.nextAll().find('a').text('');  // jnjum enq errord li-ic heto gtnvox li neri text -er@
                                
                                // diveri hamar
                                next.find('li').removeClass('active'); // aj koxqi divi meji activ class -@ jnjum e
                                next.nextAll().find('li').appendTo( pahest.find('ul') ); // clickarac divi aj kom gtnvox bolor diveri li ner@ gcum e amenaverjin divi mej(pahestum) 
                                list_click.appendNextDiv(element, div, next, pahest, deep, id); // click arac divi hajord divi mejin@ lcnum e pahest
                            }
                        }
                    }
                    else{
                        alert(text);
                    }
                }
                else{
                    if( element.find('span').attr('class') == 'icon' ){
                        // menui texti pah@
                        element.closest('.iblock').find('ul:first').find('li').each(function(){
                            tex = $(this).find('a').text();
                            $(this).prev().find('a').text(tex);
                        });
                        menuLi.eq( div.index() ).find('a').text(text);

                        list_click.deepPrev(element, divs, div, divsLength, pahest, deep, id);
                    }
                    else{
                        alert(text);
                    }
                }  
            },

            appendNextDiv : function(element, div, next, pahest, deep, id) {
                
                next.find('li').appendTo( pahest.find('ul') );// click arac li -i cnox diiv-i hajord div-@.
                div.find('li').removeClass('active');
                element.addClass('active');
                pahest.find('a[data-deep='+(deep+1)+'][data-parent='+id+']').parent().appendTo( next.find('ul') );
            },
            deepPrev : function(element, divs, div, divsLength, pahest, deep, id){
                // xorutyunnerov araj enq gnum. arajin divi mejin@ lcnum e pahest ir activ class -ov, erkrordic skasac minchev naxaverjin divi li -ner@ vercnum e lcnum e naxordi ul -i mej. nshvac click aracin avelacnum e active class, isk naxaverji divi mej pahestic e vercnum li-ner@ ev lcnum  ir ul-i mej.
                divs.filter(':first').find('li').appendTo( pahest.find('ul') );
                // aystex bolor diveri vrayov ancnum es sksac 2-ic minchev naxaverjin@ ev amen meki meji li -ner@ gcum naxord divi mej
                for (i = 1; i < divsLength - 1; i++) {
                    divs.each(function(){
                        divs.eq(i).find('li').appendTo( divs.eq(i-1).find('ul') );
                    });
                }                    
                element.addClass('active');
                pahest.find('a[data-deep='+(deep+1)+'][data-parent='+id+']').parent().appendTo( div.find('ul') );
            },
            goToBack : function(next, pahest, divs, div, deep, divsLength, menuLi){
                // menu -i texteri grancelu hatvac@
                idParent   = divs.eq(0).find('li.active').find('a').data('parent'); // gtnum e arajin divi meji activ classs unecox a -i data-id -n
                pahestAInf = pahest.find('.active').find('a[data-id="'+idParent+'"]');
                aText      = pahestAInf.text();         // pahestic gtnum e text@ vor menui mej gri
                thisDeep   = pahestAInf.data('deep');   // gtnu enq deep@ vor@ piti pahestic het qashi arajin divi mej
                thisParent = pahestAInf.data('parent'); // gtnu enq parent@ vor@ piti pahestic het qashi arajin divi mej

                // diveri hamar
                next.nextAll().find('li').appendTo( pahest.find('ul') );// erb arden 3-ic avel xorutyun bacac e linum , arajin divi mej clich aneluc urish apranqanish @ntreluc naxord bacvacner@ bolor@ lcnum e pahest
                next.find('li').removeClass('active').appendTo( next.next().find('ul') );// vorin vor click enq arel, dra hajord divi li -neri class@ jnjum e ev gcum e hajord div
                div.find('li').appendTo(  next.find('ul') );// vorin vor clichk enq arel et gcum e hajord divi mej
                pahest.find('a[data-deep='+thisDeep+'][data-parent="'+thisParent+'"]').parent().appendTo( div.find('ul') );// pahestic berum lcnum e arajin divi mej,  naxord xorutyan li-ner@
                
                // menui texti  hamar
                for(var i = divsLength - 1; i >= 0; i--){
                    var tex =  menuLi.eq(i-1).find('a').text();                                    
                    menuLi.eq(i).find('a').text(tex);
                };

                if(aText != ''){
                    // pahestum gtnvox active clas unecox apranqneric erku xorutyun het enq gnum vercnum enq tvyal text@ menui arajin li-um grelu hamar
                    pahestADeepId = pahest.find('.active').find('a[data-id="'+thisParent+'"]');
                    aPahestText = pahestADeepId.text();
                    
                    menuLi.eq(0).find('a').text(aPahestText);
                    if (deep == 1) {
                        menuLi.eq(0).find('a').text('Рубрика');
                    }
                }

                menuLi.eq( div.index()+2 ).nextAll().find('a').text('');// 4 hatic avel div linelu depqum jnjum e menui chorord textic sksac hajordner@
            }
        }
        this.on('click', list_click.show_lists);          
    }
})(jQuery);

$(function () {
    $('.categoryLi').CategoryTree();    
});