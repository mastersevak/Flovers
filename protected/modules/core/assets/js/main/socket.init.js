
var nodejs;

(function($){

	var socketConnected = false;
	var socketConnecting = false;
	var options = [];
	var interval;
	var attempt = 0;

	$(function(){
		var id_user = 'id_user='+$("input#nodejs_user").val();

		var options = {
			'reconnect': true,
			'reconnectionDelay': 10000,
			'reconnectionAttempts': 10,
			'query' : id_user
		};

		nodejs = new io.connect($('#nodejs_url').val(), options);

		//если нет соединения пробовать дальше
		interval = setInterval(function(){
			socketConnecting = false;
			if(!socketConnecting && !nodejs.socket.open && attempt < 10){
				socketConnecting = true;
				console.log('соединяемся ...');
				nodejs.socket.connect();
				attempt ++;
			}
		}, 15000);

		nodejs.on('connect', onConnect);
		
		nodejs.on('disconnect', onDisconnect);
		
		nodejs.on('message', onMessage);

		nodejs.on("reconnecting", function(delay, attempt) {
			// if (attempt === max_socket_reconnects) {
			// 	setTimeout(function(){ socket.socket.reconnect(); }, 5000);
			// 	return console.log("Failed to reconnect. Lets try that again in 5 seconds.");
			// }
		});

	});
	
	//связь с сервером установлена
	var onConnect = function (socket) {

		if(!socketConnected){
			attempt = 0;

			console.log('соединение установлено');
			socketConnected = true;
			clearInterval(interval);
		}
		
	};

	//связь с сервером потеряна
	var onDisconnect = function(socket) {
		if(socketConnected){
			console.log('соединение разорвано');
			socketConnected = false;

			// nodejs.socket.reconnect();    
		}
		
	};

	//получили сообщение от сервера
	var onMessage = function(result) {
		console.log('сообщение с сервера');
		
		if(result.params == undefined)
			result.params = {};

		//обработка сообщения от сервера
		if($.fn[result.class] != undefined)
			$.fn[result.class](result.function, result.params);
		
	};

})(jQuery);

