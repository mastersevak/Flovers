var notificationAudio = new Audio();

function playNotificationSound(source) {
	if (source == "") {
		return;
	} else if (!source) {
		source = 'noway.mp3';
	}

	try {
		notificationAudio.src = "/storage/sounds/" + source;
	   	notificationAudio.volume = 1;
	   	notificationAudio.play();
	} catch (e) {
		alert('error');
	}
}

function showErrorMessage(msg, position, hideAfter){
	showCustomMessage(msg, 'error', position, hideAfter);

	playNotificationSound();
}

function showSuccessMessage(msg, position, hideAfter){
	showCustomMessage(msg, 'success', position, hideAfter);

	playNotificationSound();
}

function showCustomMessage(msg, type, position, hideAfter){
	var opts = {};
	var params = {
	 	message: msg,
		type: type,
    	showCloseButton: true
	};

	if(position != undefined)
		opts.extraClasses  = 'messenger-fixed messenger-on-' + position;


	if(hideAfter != undefined) 
		params.hideAfter = hideAfter;

	Messenger(opts).post(params);

	playNotificationSound();
}

//поиск активного grid-а
function findGrid(element){
	//обновление таблицы
	var gridId;

	if(element != undefined)
		gridId = $(element).data('grid-id');

	if(gridId == undefined) {
		if($('.grid-view:visible').length > 0){//find grid
			gridId = $('.grid-view:visible').prop('id');
		}
		else
			return false;
	}

	return gridId;
}

function getCsrf() {
	return {
		name: $('#csrf_name').val(),
		value: $('#csrf_value').val()
	};
}


//переопределяем $.post, с ечеом отправки csrf
function jPost(url, data, callback, dataType, options){
	if(data == undefined) data = {};
	
	var type = Object.prototype.toString.call(data);

	if(type === '[object FormData]')
		data.append(getCsrf().name, getCsrf().value);
	else if(type === '[object String]'){
		data += "&" + getCsrf().name + '=' + getCsrf().value;
	}else{
		data[getCsrf().name] = getCsrf().value;
	}

	var params = {
		type: "POST",
		url: url,
		data: data,
		success: callback,
		error: function(xhr, textStatus, errorThrown){
			// console.log(textStatus);
			/*var response = $.parseJSON(xhr.responseText);	
			if(response.success != undefined && !response.success){
				if(response.message != undefined) message = response.message;
				if(response.messages != undefined) message = response.messages;
				if(response.error != undefined) message = response.error;
				if(response.errors != undefined) message = response.errors;
				console.log(response);
				if(message != undefined){
					if(message instanceof Array){
						$.each(message, function(index, msg){
							showErrorMessage(msg);
						});
					}
					else{
						showErrorMessage(message);
					}
				}
			}*/
		},
	};

	if(dataType != undefined)
		params.dataType = dataType;


	if(options != undefined) 
		$.extend(params, options);
	
	return $.ajax(params);
}

/*
как вариант можно использовать ajaxPrefilter, чтобы с любым аякс 
запросом отправлять csrf
$.ajaxPrefilter(function(opts) {
    if (opts.data) {
        opts.data += "&";
    }
    else{
        opts.data = "";
    }
    opts.data += getCsrf().name + "=" + getCsrf().value;
});*/

//конвертируем js url в json объект
var parseQueryString = function( queryString ) {
    var params = {}, queries, temp, i, l;
 
    // Split into key/value pairs
    queries = queryString.split("&");
 
    // Convert the array of strings into an object
    for ( i = 0, l = queries.length; i < l; i++ ) {
        temp = queries[i].split('=');
        params[temp[0]] = temp[1];
    }
 
    return params;
};

//удаляем рекурсивно все ключи с пустыми значениями в nested json
function removeEmpty(JsonObj) {
    $.each(JsonObj, function(key, value) {

        if (value === "" || value === null) {
            delete JsonObj[key];
        } else if (typeof(value) === "object") {
            JsonObj[key] = removeEmpty(value);
        }
    });
    return JsonObj;
}

/**
 * Для показа и скрытия пустой строки в зависимости от текущих переключателей
 * @param  jQuery Object	table - таблица к которой применяется функция
 */
function toggleNoResult(table){
	if(table.find('tbody tr:not(.empty-tr):visible').length == 0){
		var colspan = table.find('th').length;

		var emptyTr = "<tr class='empty-tr'><td colspan='" + colspan + "' class='empty'><span class='empty'>Нет результатов.</span></td></tr>";

		table.find('tbody').append(emptyTr);
	}else if(table.find('tbody tr.empty-tr'))
		table.find('tbody tr.empty-tr').remove();
}

function parseYoutube(source){
	var search = new RegExp(/(https?:\/\/)?(?:www\.)?(?:youtu\.be\/|youtube\.com(?:\/embed\/|\/v\/|\/watch\?(.+)?v=))([\w\-]{10,12}).*$/);
    var replace = "$3";
    var embed_code = source.replace(search, replace);
    return embed_code;
}


(function($) {

    $(function(){

        $.xhrPool = []; // array of uncompleted requests
        $.xhrPool.abortAll = function(){ // our abort function
            $(this).each(function(idx, jqXHR){ 
                jqXHR.abort();
            });
            $.xhrPool.length = 0
        };

        $.ajaxSetup({
            beforeSend: function(jqXHR) { // before jQuery send the request we will push it to our array
                $.xhrPool.push(jqXHR);
            },
            complete: function(jqXHR) { // when some of the requests completed it will splice from the array

                var index = $.xhrPool.indexOf(jqXHR);
                if (index > -1) {
                    $.xhrPool.splice(index, 1);
                }
            }
        });


        /**
		 * Нужно для проверки входа для ajax действий
		 *
		 * если сессия истекла, то принесет окошко для повторного входа
		 */
        $( document ).ajaxComplete(function( event, xhr, settings ) {
        	
        	if(xhr.responseText !== undefined && xhr.responseText.length > 0 && xhr.responseText.indexOf('{') == 0){
	        	var response = $.parseJSON(xhr.responseText);
				
				if(typeof response =='object' && response != null)
				{
					if(response.logout != undefined) //для случая когда пришел результат как logout
				  		$('#popup-login').show().find('.login-box').removeClass('flipOutY');
				  	if(response.validateFilterRequired)//для случая когда есть Фильтр валидация
				  		showErrorMessage(response.validateFilterRequired.id);
				}
			}
		});

    });

})( jQuery );