{$this->beginContent('//layouts/prepare')}
	{*HEADER*}
	{$this->renderPartial('//layouts/parts/_header')}
	
	<div class="page-container row-fluid"> 

		<div class="page-content {if UIHelpers::hideSideBar()}condensed{/if}">

			<div class="content mt70">
			
				{$content}

			</div>
		</div>
	</div>

{$this->endContent()}