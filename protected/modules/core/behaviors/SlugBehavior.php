<?php

class SlugBehavior extends CActiveRecordBehavior
{
    // relation of source slug
    public $sourceRelation = NULL;
    // source slug
    public $sourceAttribute = 'title';
    // result
    public $slugAttribute = 'slug';

    /**
     * @boolean needSlug - проверяет необходимость генерировать слаг при пустом sourceAttribute
     */
    public $needSlug;

    public $excludeList = array(
        ')' => '',
        ':)' => '',
        '=)' => '',
        ':' => '',
        '(' => '',
        '(:' => '',
        '(=' => '',
        ':D' => '',
        ':P' => '',
        ':3' => '',
        '!' => '',
        '?' => '',
        '#' => '',
        ',' => '',
        '№' => '',

    );

    public $replaceList = array(
        'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd', 'е' => 'e', 'ж' => 'zh', 'з' => 'z',
        'и' => 'i', 'й' => 'y', 'к' => 'k', 'л' => 'l', 'м' => 'm', 'н' => 'n', 'о' => 'o', 'п' => 'p',
        'р' => 'r', 'с' => 's', 'т' => 't', 'у' => 'u', 'ф' => 'f', 'ы' => 'i', 'э' => 'e', 'і' => 'i',
        'ї' => 'yi', 'є' => 'e', 'ґ' => 'g', 'А' => 'A', 'Б' => 'B', 'В' => 'V', 'Г' => 'G', 'Д' => 'D',
        'Е' => 'E', 'Ж' => 'ZH', 'З' => 'Z', 'И' => 'I', 'Й' => 'Y', 'К' => 'K', 'Л' => 'L', 'М' => 'M',
        'Н' => 'N', 'О' => 'O', 'П' => 'P', 'Р' => 'R', 'С' => 'S', 'Т' => 'T', 'У' => 'U', 'Ф' => 'F',
        'Ы' => 'I', 'Э' => 'E', 'ё' => "yo", 'х' => "h", 'ц' => "ts", 'ч' => "ch", 'ш' => "sh", 'щ' => "shch",
        'ъ' => "", 'ь' => "", 'ю' => "yu", 'я' => "ya", 'Ё' => "YO", 'Х' => "H", 'Ц' => "TS", 'Ч' => "CH",
        'Ш' => "SH", 'Щ' => "SHCH", 'Ъ' => "", 'Ь' => "", 'Ю' => "YU", 'Я' => "YA", 'І' => 'I',
        'Ї' => 'YI', 'Є' => 'E', 'Ґ' => 'G', ' ' => '-', '/'=>'-',
        'Ա'=>'a', 'Բ'=>'b', 'Գ'=>'g', 'Դ'=>'d', 'Ե'=>'e', 'Զ'=>'z', 'Է'=>'e', 
        'Ը'=>'y', 'Թ'=>'t', 'Ժ'=>'zh', 'Ի'=>'i', 'Լ'=>'l', 'Խ'=>'kh', 'Ծ'=>'ts', 
        'Կ'=>'k', 'Հ'=>'h', 'Ձ'=>'dz', 'Ղ'=>'gh', 'Ճ'=>'tch', 'Մ'=>'m', 'Յ'=>'y', 
        'Ն'=>'n', 'Շ'=>'sh', 'Ո'=>'o', 'Չ'=>'ch', 'Պ'=>'p', 'Ջ'=>'j', 'Ռ'=>'r', 'Ս'=>'s',
        'Վ'=>'v', 'Տ'=>'t',  'Ր'=>'r', 'Ց'=>'c', 'Ւ'=>'u', 'Փ'=>'p', 'Ք'=>'q', 'Օ'=>'o', 'Ֆ'=>'f', 'ԵՎ'=>'ev', 'ա'=>'a', 'բ'=>'b', 'գ'=>'g', 'դ'=>'d', 'ե'=>'e', 'զ'=>'z', 'է'=>'e', 'ը'=>'y', 'թ'=>'t', 'ժ'=>'zh', 'ի'=>'i', 'լ'=>'l', 'խ'=>'kh', 'ծ'=>'ts', 
        'կ'=>'k', 'հ'=>'h', 'ձ'=>'dz', 'ղ'=>'gh', 'ճ'=>'tch', 'մ'=>'m', 'յ'=>'y', 
        'ն'=>'n', 'շ'=>'sh', 'ո'=>'o', 'չ'=>'ch', 'պ'=>'p', 'ջ'=>'j', 'ռ'=>'r', 'ս'=>'s',
        'վ'=>'v', 'տ'=>'t',  'ր'=>'r', 'ց'=>'c', 'ւ'=>'u', 'փ'=>'p', 'ք'=>'q', 'օ'=>'o', 'ֆ'=>'f', 'և'=>'ev',
    );

    public function beforeValidate($event)
    {
        // нужно ли генерировать слаг при пустом значении
        if ($this->needSlug === true && !$this->getOwner()->needSlug)
            return;

        if (empty($this->getOwner()->{$this->slugAttribute}))
        {
            $this->getOwner()->{$this->slugAttribute} = $this->makeSlug($this->getSourceAttribute());
        }
    }

    private function getSourceAttribute()
    {
        if (!empty($this->sourceRelation) && !empty($this->sourceAttribute))
            return $this->getOwner()->{$this->sourceRelation}->{$this->sourceAttribute};

        if (!empty($this->sourceAttribute))
            return $this->getOwner()->{$this->sourceAttribute};

        return NULL;
    }

    public function makeSlug($source, $include = "")
    {
        preg_match_all("/([\d\wа-яА-ЯёЁіІїЇєЄґҐ\/ա-ֆև\- ".$include."]+)/ui", $source, $tmp);
        $preparedString = '';

        foreach($tmp[1] as $part)
            $preparedString .= $part;

        if(isset($tmp[1][0]))
            return strtolower(strtr(strtr(trim($preparedString), $this->replaceList), $this->excludeList));

        return $source;
    }
}
