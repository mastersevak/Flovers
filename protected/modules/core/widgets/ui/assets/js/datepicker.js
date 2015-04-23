(function($) {

$.fn.customDatePicker = function( options ) {
	// Establish our default settings
	// var settings = $.extend({
	// 	min 		: null,
	// 	max			: null,
	// }, options);

	return this.each( function() {
		var self = $(this);
		var now  = dateParser(new Date());

		// double Click
		self.on('dblclick', function(e){
			e.preventDefault();
			e.stopPropagation();

			// close datepicker and set date to current
			$('body div.datepicker:visible').hide(0, function(){self.val(now);});
		});

		// Next Prev Click
		self.closest('div').on('click', 'a.prev, a.next', function(e){
			e.preventDefault();
			var date = now;
			// if is empty date, set date to current
			
			if(self.val().length > 0){
				date = new Date($.datepicker.parseDate( "dd-mm-yy", self.val()));
				if($(this).hasClass('prev')){
					//Если проставлена начальная дата то проверяем, чтобы дата не была меньше проставленой
					if(self.data().datepicker._o.startDate !== -Infinity){
						var startDate = new Date($.datepicker.parseDate( "dd-mm-yy", self.data().datepicker._o.startDate));
						if((date.getDate() - 1 - startDate.getDate()) >= 0)
							date.setDate(date.getDate() - 1);
					}else
						date.setDate(date.getDate() - 1);
				}
				if($(this).hasClass('next'))
					date.setDate(date.getDate() + 1);

				date = dateParser(date);
			}
			
			self.val(date);
			self.trigger('changeDate');
		});

		function dateParser(date){
			var year  = date.getFullYear();
			var month = date.getMonth() + 1;
			var day   = date.getDate();

			if(month < 10) month = "0" + month;
			if(day < 10) day = "0" + day;

			return day+'-'+month+'-'+year;
		}

	});

}

}(jQuery));