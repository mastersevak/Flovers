<div class="container">
	<div class="lockscreen-wrapper animated flipInX">
		<div class="row ">
			<div class="col-md-8 col-md-offset-4 col-sm-6 col-sm-offset-4 col-xs-offset-2">
				<div class="profile-wrapper"> 
					{Yii::app()->user->createAvatar('thumb', 69)} 
				</div>
				
				{form name="form" 
			        enableAjaxValidation=true
			        clientOptions=[
			            validateOnSubmit => true,
			            beforeValidate => 'js:Auth.beforeUnlock',
			            afterValidate => 'js:Auth.unlock'
			        ]
			        htmlOptions=['class' => 'user-form']
			    }
					<h2 class="user">{Yii::app()->format->custom('normal<semibold>', Yii::app()->user->fullname)}</h2>
					
					<div>
						<div class="control-group">
							{$form->passwordField($model, 'password', [class=>'w260'])}
						</div>
						
						<button type="submit" class="btn btn-primary without-value">
							<i class="fa fa-unlock"></i></button>

						<br clear="both">
					</div>
					

						{$form->error($model, 'password')}
				{/form}
			</div>
		</div>
	</div>
	<div id="push"></div>
</div>