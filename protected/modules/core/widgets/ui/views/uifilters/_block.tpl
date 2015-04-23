<div class="filter-block grid simple {$name} {$float} {$hidden}" id="{$id}">
	<div class="grid-title">
		{if isset($fill) && $fill}
			<a href="#" class="fill-block c-black" data-type="check" data-exceptions="{$fillExceptions}" >{$title}</a>
		{else}
			<h4>{$title}</h4>
		{/if}
		
		<div class="tools"> 
			<a href="#" class="reload" rel="tooltip" title="Сброс полей"></a> 
			<a href="#" class="remove" rel="tooltip" title="Скрыть блок"></a> 
		</div>
	</div>
	<div class="grid-body">
		{$content}
	</div>

</div>