<?php 


/**
*
*
* использование 
* 
* $cbr = new CBRAgent();
* if ($cbr->load()){ //один раз загружаетс данные 
*     $usd_curs = $cbr->get('USD'); // а здесь берет значения уже из массива
* }
*/
class CBRAgent extends BaseComponent
{
    protected $list = array();
 
    public function load()
    {
        $xml = new DOMDocument();
        $url = 'http://www.cbr.ru/scripts/XML_daily.asp?date_req=' . date('d.m.Y');
 
        if (@$xml->load($url))
        {
            $this->list = array(); 
 
            $root = $xml->documentElement;
            $items = $root->getElementsByTagName('Valute');
 
            foreach ($items as $item)
            {
                $code = $item->getElementsByTagName('CharCode')->item(0)->nodeValue;
                $curs = $item->getElementsByTagName('Value')->item(0)->nodeValue;
                $this->list[$code] = floatval(str_replace(',', '.', $curs));
            }
 
            return true;
        } 
        else
            return false;
    }
 
    public function get($cur)
    {
        return isset($this->list[$cur]) ? $this->list[$cur] : 0;
    }
}