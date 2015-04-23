{if $this->breadcrumbs}
<div class="mb10">
{widget name='SBreadcrumbs' 
		htmlOptions=[class=>'breadcrumb'] 
		separator='<i class="fa fa-angle-right arrow"></i>'
		links=$this->breadcrumbs}	
</div>

{/if}