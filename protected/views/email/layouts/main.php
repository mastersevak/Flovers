<?$this->renderPartial('//email/layouts/_header'); ?>
	<div style="">
		
		<?php echo $content;?>

		<hr style="background: #d8d8d8;
				margin-top: 24px;"/>
		
		<div>
			<p>Спасибо за пользование нашим ресурсом!</p>
			<p>С уважением,<br />
				<strong><?=CHtml::encode(Yii::app()->name)?></strong> </p>
		</div>
	</div>
<?$this->renderPartial('//email/layouts/_footer');?>