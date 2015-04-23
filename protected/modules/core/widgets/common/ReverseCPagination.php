<?php
class ReverseCPagination extends CPagination {

	public function createPageUrl($controller,$page){
	    $params=$this->params===null ? $_GET : $this->params;
	    if($page<$this->getPageCount()-1)
	        $params[$this->pageVar]=$page+1;
	    else
	        unset($params[$this->pageVar]);
	    return $controller->createUrl($this->route,$params);
	}

	public function getOffset() {
	    if ($this->getCurrentPage() == 0 && !isset($_GET[$this->pageVar])) {
	        $this->setCurrentPage($this->getPageCount() - 1);
	    }

	    if ($this->getCurrentPage() == 0 
	        && isset($_GET[$this->pageVar]) 
	        && ($_GET[$this->pageVar] < 1 
	            || $_GET[$this->pageVar] > $this->getPageCount())) {
	        $this->setCurrentPage($this->getPageCount() - 1);
	    }

	    return ($this->getPageCount() - $this->getCurrentPage() - 1) 
	        * $this->getPageSize();
	}
}