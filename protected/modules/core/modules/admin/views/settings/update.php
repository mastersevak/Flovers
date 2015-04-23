<div class="grid simple mt20">
	<div class="grid-body">
		<p class="note mb20">Поля со знаком <code>*</code> объязательны для заполнения</p>
		<div class="std-form">
			<?php echo $form?>
		</div>
	</div>
</div>

<?$this->widget('UIButtons', ['group'=>'update', 'form'=>'edit-form'])?>