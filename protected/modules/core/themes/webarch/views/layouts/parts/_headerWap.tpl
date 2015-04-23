<div id="main-header" class="header wap navbar navbar-inverse "> 
	{* BEGIN TOP NAVIGATION BAR *}
	<div class="navbar-inner">

		{* BEGIN TOP NAVIGATION MENU *}
	    <div> 
			<div class="pull-left"> 

				{* LOGO 2 *}
				<ul class="logo2 nav quick-section {if !UIHelpers::hideSideBar()}hidden{/if}">
					<li class="quicklinks">
						<a href="{$this->createUrl('/market/wap/index')}">
							<img src="{$this->assetsUrl}/img/logo-blue.png" 
								class="logo pull-left no-margin" 
								data-src="{$this->assetsUrl}/img/logo-blue.png"
								height="19px" />
						</a>
					</li> 
				</ul>

			</div>
			<!-- END TOP NAVIGATION MENU -->
			
			<!-- BEGIN CHAT TOGGLER -->
		    <div class="pull-right"> 

				<div class="chat-toggler">	
					<a href="#" class="dropdown-toggle" id="my-task-list" data-placement="bottom" data-content="" data-toggle="dropdown" data-original-title="Notifications">
						<div class="user-details"> 
							<div class="username">
								{*username*}
								{Yii::app()->format->custom('normal<semibold>', Yii::app()->user->fullname)}</span>									
							</div>						
						</div> 
						<div class="iconset top-down-arrow"></div>
						<div class="profile-pic"> 
							{Yii::app()->user->avatar}
						</div>
					</a>

					{* NOTIFICATIONS *}
					<div id="notification-list" style="display:none">
						<div style="width:300px">
							
							<div class="notification-messages info">
								<div class="user-profile">
									<!-- <img src="assets/img/profiles/d.jpg" alt="" data-src="assets/img/profiles/d.jpg" data-src-retina="assets/img/profiles/d2x.jpg" width="35" height="35"> -->
								</div>
								<div class="message-wrapper">
									<div class="heading">Title of Notification</div>
									<div class="description">Description...</div>
									<div class="date pull-left">A min ago</div>										
								</div>
								<div class="clearfix"></div>									
							</div>
	
						</div>				
					</div>

					
				</div>

				<ul class="nav quick-section pull-right">
					<li class="quicklinks"> 
						<a data-toggle="dropdown" class="dropdown-toggle pull-right" href="#" id="user-options">						
							<div class="iconset top-settings-dark"></div> 	
						</a>
						{* USER DROPDOWN *}
						<ul class="dropdown-menu pull-right" role="menu" aria-labelledby="user-options">
							<li><a href="{url('/core/user/back/profile')}">Мой профиль</a></li>
							<li>
								<a href="#">Мои сообщения&nbsp;&nbsp;
									<span class="badge badge-important animated bounceIn">2</span>
								</a>
							</li>
							
							<li class="divider"></li>   

							<li><a href="{url('/core/user/back/lock')}"><i class="icon-lock"></i>&nbsp;&nbsp;Заблокировать</a></li>
							<li><a href="{url('/core/user/back/logout')}"><i class="icon-off"></i>&nbsp;&nbsp;Выйти</a></li>
						</ul>		
					</li>
				</ul>
		    </div>
			<!-- END CHAT TOGGLER -->
	    </div> 
	    <!-- END TOP NAVIGATION MENU --> 
	   
	</div>
	<!-- END TOP NAVIGATION BAR --> 
</div>