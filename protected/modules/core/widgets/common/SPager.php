<?
class SPager extends CLinkPager
{
	/**
	 * @see CLinkPager::init()
	 */
	public function init()
	{
		parent::init();

		$this->firstPageLabel = "<i class='fa fa-angle-double-left'></i>";
		$this->lastPageLabel = "<i class='fa fa-angle-double-right'></i>";

		$this->prevPageLabel = "<i class='fa fa-angle-left'></i>";
		$this->nextPageLabel = "<i class='fa fa-angle-right'></i>";

		$this->nextPageCssClass = 'next';
		$this->previousPageCssClass = 'prev';
		$this->selectedPageCssClass	= 'active';
		
		$this->htmlOptions['id'] = $this->getId();

		$assets = assets(dirname(__FILE__).'/assets', false, -1, YII_DEBUG);
 		//загружаем свои стили 
 		cs()->registerCssFile( $assets. '/css/pager.css');

	}

	/**
	 * @see CLinkPager::run()
	 */
	public function run()
	{
		$buttons = $this->createPageButtons();
		if (empty($buttons))
			return;
		echo CHtml::tag('ul', $this->htmlOptions, implode("\n", $buttons));
	}

	protected function createPageButtons()
    {
        if(($pageCount=$this->getPageCount())<=1)
            return array();

        list($beginPage,$endPage)=$this->getPageRange();
        $currentPage=$this->getCurrentPage(false); // currentPage is calculated in getPageRange()
        $buttons=array();

        // Commented out first / prev page (these added in run())
        // first page
        $buttons[]=$this->createPageButton($this->firstPageLabel,0,self::CSS_FIRST_PAGE,$beginPage<=0,false);
        // prev page
        if(($page=$currentPage-1)<0)
           $page=0;
        $buttons[]=$this->createPageButton($this->prevPageLabel, $page,self::CSS_PREVIOUS_PAGE,$currentPage<=0,false);

        // internal pages
        for($i=$beginPage;$i<=$endPage;++$i)
            $buttons[]=$this->createPageButton($i+1,$i,self::CSS_INTERNAL_PAGE,false,$i==$currentPage);

        // Commented out next/last page (these added in run()) 
        // next page
        if(($page=$currentPage+1)>=$pageCount-1)
           $page=$pageCount-1;
        $buttons[]=$this->createPageButton($this->nextPageLabel,$page, self::CSS_NEXT_PAGE,$currentPage>=$pageCount-1,false);
        // last page
        $buttons[]=$this->createPageButton($this->lastPageLabel,$pageCount-1,self::CSS_LAST_PAGE,$endPage>=$pageCount-1,false);

        return $buttons;
    }
}

?>