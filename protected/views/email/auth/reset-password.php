<h2>Сброс пароля</h2>
<p>Для сброса пароля на сайте <?=CHtml::encode(app()->name)?> нажмите на следующей ссылке!</p>
<p>
<?=CHtml::link(t('front', "Нажмите для сброса пароля"), 
	$this->createAbsoluteUrl('/auth/resetpassword', array('username'=>$username, 'key'=>$key)));?>!</p>
