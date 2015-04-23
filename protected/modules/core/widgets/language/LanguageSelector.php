<?php

class LanguageSelector extends SWidget
{
	public $view = 'languageSelector';
	public $languages = null;
	public $ajax = false;
	public $type = 'tree'; //тип селектора grid, tree

	public function run() {
		$languages = ($this->languages ? $this->languages : param('languages') );
		if(count($languages) < 2) return;
		
		$currentLang = lang();
		$cp = app()->urlManager->getCleanUrl( request()->getPathInfo() );

		if($this->ajax) {

			cs()->registerScript('language-selector', " 
				$('.language-selector button').on('click', function(){
					$(this).closest('.language-selector').find('button')
						.removeClass('btn-primary')
						.removeClass('active');

					$(this).addClass('btn-primary active');

					var lang = $(this).data('language');

					if($(this).closest('.language-selector').data('type') == 'tree'){
						$('.multilang').addClass('hidden');
						$('.multilang.'+lang).removeClass('hidden');
					}
					else { //grid
						$.fn.yiiGridView.update(findGrid(), {data : {'_lang' : lang}}); 
					}					
				});");
		}

		$type = $this->type;

		$this->render($this->view, compact('currentLang', 'languages', 'cp', 'type'));
	}
}?>