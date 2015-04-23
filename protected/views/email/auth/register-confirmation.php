<h2>Регистрация на сайте <?=CHtml::encode(Yii::app()->name)?></h2>
<p>Ваш акаунт на сайте <?=CHtml::encode(Yii::app()->name)?> успешно создан!</p>

<p>Если вы на самом деле регистрировались на нашем сайте, 
то для активации Вашего акаунта Вам нужно перейти по <?=CHtml::link( t('front', 'ссылке'), 
		$this->createAbsoluteUrl('/auth/activate', array(
			'username'=>$username, 
			'key'=>$key
		)));?></p>
<p>В обратном случае просто проигнорируйте данное письмо.</p>