<div id="main-header" class="header navbar navbar-inverse "> 
	<?// BEGIN TOP NAVIGATION BAR ?>
	<div class="navbar-inner">

		<?// BEGIN TOP NAVIGATION MENU ?>
	    <div class="header-quick-nav"> 
			<div class="pull-left"> 

				<?// LOGO 2 ?>
				<ul class="logo2 nav quick-section no-margin <?if(!UIHelpers::hideSideBar()) echo 'hidden'?>">
					<li class="quicklinks">
						<a href="<?=$this->createUrl(param('adminHome'))?>" class="p0">
							<img src="<?=$this->rootAssetsUrl?>/backend/img/header-logo.png" 
								class="logo pull-left no-margin" 
								data-src="<?=$this->rootAssetsUrl?>/backend/img/header-logo.png"/>
						</a>
					</li> 
				</ul>

				<?// TOP MENU ?>
				<nav class="menu-top" >
					<?=Menu::renderMenu('backendMenu')?> 
					<?=Menu::renderMenu('administrator')?> 
				</nav>
			</div>
			<!-- END TOP NAVIGATION MENU -->
			
			<!-- BEGIN CHAT TOGGLER -->
		    <div class="pull-right"> 
		    	<?$this->renderPartial('app.views.globals.backHeaderRightSide')?>

		    	<div class="chat-toggler">	
					<a href="#" class="dropdown-toggle" id="my-task-list" data-placement="bottom" data-toggle="dropdown">
						<div class="user-details"> 
							<div class="username">
								<?//username?>
								<?=Yii::app()->format->custom('normal<semibold>', Yii::app()->user->fullname)?>
							</div>						
						</div> 
						<div class="iconset top-down-arrow"></div>
						<div class="profile-pic"> 
							<?=Yii::app()->user->getState('avatar')?>
						</div>
					</a>

					<?// NOTIFICATIONS ?>
					<!-- <div id="notification-list">
						<div style="width:300px">
							
							<div class="notification-messages info">
								<div class="user-profile">
									<img src="assets/img/profiles/d.jpg" alt="" data-src="assets/img/profiles/d.jpg" data-src-retina="assets/img/profiles/d2x.jpg" width="35" height="35">
								</div>
								<div class="message-wrapper">
									<div class="heading">Title of Notification</div>
									<div class="description">Description...</div>
									<div class="date pull-left">A min ago</div>
								</div>
								<div class="clearfix"></div>
							</div>

						</div>
					</div> -->
				</div>

				<ul class="nav quick-section pull-right">
					<li class="quicklinks"> 
						<a data-toggle="dropdown" class="dropdown-toggle pull-right" href="#" id="user-options">						
							<div class="iconset top-settings-dark"></div> 	
						</a>
						<?// USER DROPDOWN ?>

						<ul class="dropdown-menu pull-right" role="menu" aria-labelledby="user-options">
							<li><a href="/<?=ADMIN_PATH?>/profile">Мой профиль</a></li>
							<li>
								<a href="#">Мои сообщения&nbsp;&nbsp;
									<span class="badge badge-important animated bounceIn">2</span>
								</a>
							</li>
							
							<li class="divider"></li>   

							<li><a href="<?=$this->createUrl('/core/user/back/lock')?>"><i class="icon-lock"></i>&nbsp;&nbsp;Заблокировать</a></li>
							<li><a href="<?=$this->createUrl('/core/user/back/logout')?>"><i class="icon-off"></i>&nbsp;&nbsp;Выйти</a></li>
						</ul>		
					</li>

					<li class="quicklinks"><span class="h-seperate"></span></li> 

					<li class="quicklinks"> 	
						<a id="chat-menu-toggle" href="#sidr" class="chat-menu-toggle">
							<div class="iconset top-chat-dark"><span class="badge badge-important hide" id="chat-message-count">1</span></div>
						</a> 
					</li>	
				</ul>
		    </div>
			<!-- END CHAT TOGGLER -->
	    </div> 
	    <!-- END TOP NAVIGATION MENU --> 
	   
	</div>

	<? if($this->languageSelector) 
		$this->widget('core.widgets.language.LanguageSelector', ['ajax' => true, 'type' => $this->languageSelector]) ?>
	<!-- END TOP NAVIGATION BAR --> 
</div>
