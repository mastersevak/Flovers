<? //Резултаты поиска?>
<div class="bg-title">
	<div class="wrapper">
		<p class="fleft mb0"><span class="fbold c-blue"><?=t('front', 'Результаты поиска:')?> </span> 
			<?=t('front', '{n} объект|{n} объектa|{n} объектов|{n} объекта', $model->frontSearch()->totalItemCount)?></p>
	</div>
</div>