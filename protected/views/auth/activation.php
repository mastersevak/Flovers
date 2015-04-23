<div class="wrap">
    <? if(user()->hasFlash('error')): ?>
    	<!-- ошибка активации -->
    	<div class="success_message">
            <?=user()->getFlash('error')?>
            <div class="gradient">
            <?=CHtml::link(t('front', 'Перейти на главную'), ['/site/index'])?>
            </div>
        </div>

    <? endif ?>

    <? if(user()->hasFlash('success')): ?>
        <!-- активация прошла успешно -->
        <div class="success_message">
            <?=user()->getFlash('success')?>
            <div class="gradient">
            <?=CHtml::link(t('front', 'Перейти на страницу входа'), ['login'])?>
        	</div>
        </div>
    <? endif ?>
</div>