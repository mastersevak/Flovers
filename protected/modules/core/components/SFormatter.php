<?php 

Yii::import('yiiwheels.widgets.timeago.WhTimeAgoFormatter');

/**
* SFormatter
*
* @author Alexander Manukian
*/
class SFormatter extends WhTimeAgoFormatter
{

	public $datePattern = 'd-m-Y H:i:s';
    public $phonePattern = '+# (###) ###-##-##';

	public function price($value, $iso = 'RUB'){
		return Yii::app()->numberFormatter->formatCurrency($value, $iso/*Yii::app()->currency->active->iso*/);
	}
	
	public function custom($format, $string){
		switch($format){

			case 'normal<semibold>': {
				return preg_replace("/([a-zа-я0-9_\-\.]+)(\s.*)/iu", 
                    "$1<span class='semi-bold'>$2</span>", $string);
			}

		}

		return '';
	}
  
    public function date($value, $pattern='date'){
        switch($pattern){
            case 'datetime':
                return app()->dateformatter->format('dd MMM yyyy г. HH:mm', strtotime($value));
            case 'date':
                return app()->dateformatter->format('dd MMM yyyy г.', strtotime($value));
            case 'dateshort':
                return app()->dateformatter->format('dd-MM-yyyy', strtotime($value));
            case 'time':
                return app()->dateformatter->format('HH:mm:ss', strtotime($value)); 
            case 'timeshort':
                return app()->dateformatter->format('HH:mm', strtotime($value));      
            case 'timeago':
                return $this->formatTimeago($value);
                break;
            default:
                return date($pattern, strtotime($value));
                break;
        }
    }

    /**
     * Возвращает сумму прописью
     * @author runcore
     * @uses morph(...)
     */
    public function num2str($num) {
        $nul='ноль';
        $ten=array(
            array('','один','два','три','четыре','пять','шесть','семь', 'восемь','девять'),
            array('','одна','две','три','четыре','пять','шесть','семь', 'восемь','девять'),
        );
        $a20=array('десять','одиннадцать','двенадцать','тринадцать','четырнадцать' ,'пятнадцать','шестнадцать','семнадцать','восемнадцать','девятнадцать');
        $tens=array(2=>'двадцать','тридцать','сорок','пятьдесят','шестьдесят','семьдесят' ,'восемьдесят','девяносто');
        $hundred=array('','сто','двести','триста','четыреста','пятьсот','шестьсот', 'семьсот','восемьсот','девятьсот');
        $unit=array( // Units
            array('копейка' ,'копейки' ,'копеек',    1),
            array('рубль'   ,'рубля'   ,'рублей'    ,0),
            array('тысяча'  ,'тысячи'  ,'тысяч'     ,1),
            array('миллион' ,'миллиона','миллионов' ,0),
            array('миллиард','милиарда','миллиардов',0),
        );
        //
        list($rub,$kop) = explode('.',sprintf("%015.2f", floatval($num)));
        $out = array();
        if (intval($rub)>0) {
            foreach(str_split($rub,3) as $uk=>$v) { // by 3 symbols
                if (!intval($v)) continue;
                $uk = sizeof($unit)-$uk-1; // unit key
                $gender = $unit[$uk][3];
                list($i1,$i2,$i3) = array_map('intval',str_split($v,1));
                // mega-logic
                $out[] = $hundred[$i1]; # 1xx-9xx
                if ($i2>1) $out[]= $tens[$i2].' '.$ten[$gender][$i3]; # 20-99
                else $out[]= $i2>0 ? $a20[$i3] : $ten[$gender][$i3]; # 10-19 | 1-9
                // units without rub & kop
                if ($uk>1) $out[]= self::morph($v,$unit[$uk][0],$unit[$uk][1],$unit[$uk][2]);
            } //foreach
        }
        else $out[] = $nul;
        $out[] = self::morph(intval($rub), $unit[1][0],$unit[1][1],$unit[1][2]); // rub
        $out[] = $kop.' '.self::morph($kop,$unit[0][0],$unit[0][1],$unit[0][2]); // kop
        return trim(preg_replace('/ {2,}/', ' ', join(' ',$out)));
    }

    /**
     * Склоняем словоформу
     * @ author runcore
     */
    private function morph($n, $f1, $f2, $f5) {
        $n = abs(intval($n)) % 100;
        if ($n>10 && $n<20) return $f5;
        $n = $n % 10;
        if ($n>1 && $n<5) return $f2;
        if ($n==1) return $f1;
        return $f5;
    }

    /*public function formatTimeagoCell($value)
    {
        return '<div data-original-title="'.$this->formatTimeago($value).'" rel="tooltip">'.$this->formatTimeCell($value).'</div>';
    }

	public function formatTimeCell($value)
    {
        return date($this->datePattern, strtotime($value));
    }

    public function formatTimeagoText($value)
    {
        return $this->formatTimeCell($value).'<br /><i>('.$this->formatTimeago($value).')</i>';
    }

    public function formatPhoneNumber($value){
        return preg_replace('/(\d)(\d{3})(\d{3})(\d{2})(\d{2})/', '+$1 ($2) $3-$4-$5', $value);
    }*/
}