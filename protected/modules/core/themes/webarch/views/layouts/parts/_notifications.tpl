<div style="width:300px" class="scroller" data-height="100px">
  	<div class="notification-messages info">
		<div class="user-profile">
			{Yii::app()->user->createAvatar("thumb", 35)}
		</div>
		<div class="message-wrapper">
			<div class="heading">
				David Nester - Commented on your wall
			</div>
			<div class="description">
				Meeting postponed to tomorrow
			</div>
			<div class="date pull-left">
			A min ago
			</div>										
		</div>
		<div class="clearfix"></div>									
	</div>

	<div class="notification-messages danger">
		<div class="iconholder">
			<i class="icon-warning-sign"></i>
		</div>
		<div class="message-wrapper">
			<div class="heading">
				Server load limited
			</div>
			<div class="description">
				Database server has reached its daily capicity
			</div>
			<div class="date pull-left">
			2 mins ago
			</div>
		</div>
		<div class="clearfix"></div>
	</div>	
	
	<div class="notification-messages success">
		<div class="user-profile">
			{Yii::app()->user->createAvatar("thumb", 35)}
		</div>
		<div class="message-wrapper">
			<div class="heading">
				You haveve got 150 messages
			</div>
			<div class="description">
				150 newly unread messages in your inbox
			</div>
			<div class="date pull-left">
			An hour ago
			</div>									
		</div>
		<div class="clearfix"></div>
	</div>							
</div>