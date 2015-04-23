<?php
/**
 * String
 * @author Yaroslav Pelesh aka Tokolist http://tokolist.com
 * @link https://github.com/tokolist/yii-components
 * @version 1.0
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 *
 * ниже мои доработки
 */

class String
{
	public static $unicode = true;
	public static $subjectChanged = false;

	// does the first string start with the second?
    public static function startsWith($haystack, $needle){
        return strpos($haystack, $needle) === 0;
    }

	public static function breakLongWords($subject, $maxLength=20, $break="\r\n")
	{
		$pattern = '/([^\s]{'.$maxLength.'})(?=[^\s])/m';
		if(self::$unicode)
			$pattern .= 'u';
		
		return preg_replace($pattern, "$1$break", $subject);
	}

	public static function arrayEncode($subject)
	{
		if(!is_array($subject))
			return htmlspecialchars($subject);

		return array_map(array('self','arrayEncode'), $subject);
	}

	public static function arrayTrim($subject)
	{
		if(!is_array($subject))
			return trim($subject);

		return array_map(array('self','arrayTrim'), $subject);
	}

	public static function cutLongString($subject, $maxChars, $trimChars=" \t\n\r\0\x0B")
	{
		self::$subjectChanged = false;

		if(strlen($subject) > $maxChars)
		{
			$subject = substr($subject, 0, $maxChars);
			$subject = rtrim($subject, $trimChars);

			self::$subjectChanged = true;
		}

		return $subject;
	}

	public static function cutStringToWords($subject, $maxWordsCount, $trimChars=" \t\n\r\0\x0B")
	{
		self::$subjectChanged = false;

		$pattern = '/([^\s\n\r]+[\s\n\r]+){' . $maxWordsCount . '}/s';
		if(self::$unicode)
			$pattern .= 'u';
		
		if(preg_match($pattern, $subject, $match))
		{
			$subject = rtrim($match[0], $trimChars);

			self::$subjectChanged = true;
		}

		return $subject;
	}

	public static function stringFormat($subject, $maxChars, $maxWordLength, $encode=true, $ellipsis='&hellip;', $wordBreak='&shy;')
	{
		$subject = self::cutLongString($subject, $maxChars, $trimChars=" \t\n\r\0\x0B.,");
		$stringCut = self::$subjectChanged;

		$subject = self::breakLongWords($subject, $maxWordLength);

		if($encode)
			$subject = htmlspecialchars($subject);
		
		$subject = str_replace("\r\n", $wordBreak, $subject);

		if($stringCut)
			$subject .= $ellipsis;

		return $subject;
	}

	public static function simpleTextFormat($subject)
	{
		$subject = trim($subject);
		$subject = preg_replace('~([^\r\n]+?)([\r\n]+|$)~', '<p>$1</p>', $subject);
		return $subject;
	}

	/**
	 * Генерация случайной строки из определенных смиволов
	 * @param  integer $length длина возвращаемой строки
	 * @param  string  $chars  строка, в которой указаны допустимы символы
	 * @return string
	 */
	public static function randomString($length=8, $chars=false)
	{
		if($chars === false)
			$chars = "0123456789AaBbCcDdEeFfGgHhIiJjKkLlMmNnOoPpQqRrSsTtUuVvWwXxYyZz";

			srand((double)microtime()*1000000);
			$i = 1;
			$result = '';
			while($i <= $length)
			{
				//	rand() устаревает. Рекомендую использовать mt_rand($from, $to)
				//	работает лучше чем rand()
				$num = rand() % strlen($chars);
				$tmp = substr($chars, $num, 1);
				$result = $result . $tmp;
				$i++;
			}

		return $result;
	}

	public static function getUniqueString($model, $field, $length = 8){
		$n=1;
		// loop until random is unqiue - which it probably is first time!
		while ($n>0) {
		    $string = self::randomString($length);
		    $n = $model::model()->count($field.'=:fieldname', array('fieldname'=>$string));
		}

		return $string;
    }

	public static function wordwrap($str, $width=75, $break="\n", $cut=false)
	{
		//Fix multiple spaces
		$str = preg_replace('/ +/', ' ', $str);

		//Standardize line breaks
		$str = str_replace(array("\r\n", "\r"), "\n", $str);

		
		$lines=explode($break, $str);
		$result = array();
		if($cut)
		{
			$pattern = '/.{'.$width.'}/';

			if(self::$unicode)
				$pattern .= 'u';

			foreach($lines as $line)
			{
				if(strlen($line) > $width)
					$result[]=preg_replace($pattern, "$0$break", $line);
			}
		}
		else
		{
			foreach($lines as $line)
			{
				if(strlen($line) > $width)
				{
					$line=self::breakLongWords($line, $width, ' ');

					

					$currentLine='';
					foreach(explode(' ', $line) as $word)
					{
						if(strlen($currentLine.$word) > $width)
						{
							$result[]=rtrim($currentLine);
							$currentLine='';
						}

						$currentLine .= $word . ' ';
					}

					if($currentLine!='')
						$result[]=rtrim($currentLine);

					
				}
				else
				{
					$result[]=$line;
				}
			}
		}

		return implode($break, $result);
	}

	public static function smartReSubject($subject)
	{
		$subject=trim($subject);

		//Has Re
		$pattern='/^Re(\[(\d+)\])?:/';

		if(self::$unicode)
			$pattern .= 'u';

		if(preg_match($pattern, $subject, $matches))
		{
			$number=intval($matches[2]);

			if(empty($number))
				$number=1;

			$number++;

			return preg_replace($pattern, "Re[$number]:", $subject);
		}

		//No Re's
		return 'Re: '.$subject;
	}

	/**
	 * Seems like UTF-8?
	 * hmdker at gmail dot com {@link php.net/utf8_encode}
	 *
	 * @access	public
	 * @param	string		Raw text
	 * @return	boolean
	 */
	public static function isUTF8($str) {
	    $c=0; $b=0;
	    $bits=0;
	    $len=strlen($str);
	    for($i=0; $i<$len; $i++)
	    {
	        $c=ord($str[$i]);

	        if($c > 128)
	        {
	            if(($c >= 254)) return false;
	            elseif($c >= 252) $bits=6;
	            elseif($c >= 248) $bits=5;
	            elseif($c >= 240) $bits=4;
	            elseif($c >= 224) $bits=3;
	            elseif($c >= 192) $bits=2;
	            else return false;

	            if(($i+$bits) > $len) return false;

	            while( $bits > 1 )
	            {
	                $i++;
	                $b = ord($str[$i]);
	                if($b < 128 || $b > 191) return false;
	                $bits--;
	            }
	        }
	    }

	    return true;
	}

	/**
	 * Make an SEO title for use in the URL
	 *
	 * @access	public
	 * @param	string		Raw SEO title or text
	 * @return	string		Cleaned up SEO title
	 */
	static public function makeAlias( $text )
	{
		if ( ! $text )
		{
			return '';
		}
		
		$text = str_replace( array( '`', ' ', '+', '.', '?', '_' ), '-', $text );
		
		/* Strip all HTML tags first */
		$text = strip_tags($text);
			
		/* Preserve %data */
		$text = preg_replace('#%([a-fA-F0-9][a-fA-F0-9])#', '-xx-$1-xx-', $text);
		$text = str_replace( array( '%', '`' ), '', $text);
		$text = preg_replace('#-xx-([a-fA-F0-9][a-fA-F0-9])-xx-#', '%$1', $text);

		/* Convert accented chars */
		$text = self::convertAccents($text);
		
		/* Convert it */
		if ( self::isUTF8( $text )  )
		{
			if ( function_exists('mb_strtolower') )
			{
				$text = mb_strtolower($text, 'UTF-8');
			}

			$text = self::utf8Encode( $text, 500 );
		}

		/* Finish off */
		$text = strtolower($text);
		
		if ( strtolower( Yii::app()->charset ) == 'utf-8' )
		{
			$text = preg_replace( '#&.+?;#'        , '', $text );
			$text = preg_replace( '#[^%a-z0-9 _-]#', '', $text );
		}
		else
		{
			/* Remove &#xx; and &#xxx; but keep &#xxxx; */
			$text = preg_replace( '/&#(\d){2,3};/', '', $text );
			$text = preg_replace( '#[^%&\#;a-z0-9 _-]#', '', $text );
			$text = str_replace( array( '&quot;', '&amp;'), '', $text );
		}
		
		$text = str_replace( array( '`', ' ', '+', '.', '?', '_' ), '-', $text );
		$text = preg_replace( "#-{2,}#", '-', $text );
		$text = trim($text, '-');
		
		return ( $text ) ? $text : '-';
	}

	

	/**
	 * Converts accented characters into their plain alphabetic counterparts
	 *
	 * @access	public
	 * @param	string		Raw text
	 * @return	string		Cleaned text
	 */
	static public function convertAccents($string)
	{
		if ( ! preg_match('/[\x80-\xff]/', $string) )
		{
			return $string;
		}

		if ( self::isUTF8( $string) )
		{
			$_chr = array(
				/* Latin-1 Supplement */
				chr(195).chr(128) => 'A', chr(195).chr(129) => 'A',
				chr(195).chr(130) => 'A', chr(195).chr(131) => 'A',
				chr(195).chr(132) => 'A', chr(195).chr(133) => 'A',
				chr(195).chr(135) => 'C', chr(195).chr(136) => 'E',
				chr(195).chr(137) => 'E', chr(195).chr(138) => 'E',
				chr(195).chr(139) => 'E', chr(195).chr(140) => 'I',
				chr(195).chr(141) => 'I', chr(195).chr(142) => 'I',
				chr(195).chr(143) => 'I', chr(195).chr(145) => 'N',
				chr(195).chr(146) => 'O', chr(195).chr(147) => 'O',
				chr(195).chr(148) => 'O', chr(195).chr(149) => 'O',
				chr(195).chr(150) => 'O', chr(195).chr(153) => 'U',
				chr(195).chr(154) => 'U', chr(195).chr(155) => 'U',
				chr(195).chr(156) => 'U', chr(195).chr(157) => 'Y',
				chr(195).chr(159) => 's', chr(195).chr(160) => 'a',
				chr(195).chr(161) => 'a', chr(195).chr(162) => 'a',
				chr(195).chr(163) => 'a', chr(195).chr(164) => 'a',
				chr(195).chr(165) => 'a', chr(195).chr(167) => 'c',
				chr(195).chr(168) => 'e', chr(195).chr(169) => 'e',
				chr(195).chr(170) => 'e', chr(195).chr(171) => 'e',
				chr(195).chr(172) => 'i', chr(195).chr(173) => 'i',
				chr(195).chr(174) => 'i', chr(195).chr(175) => 'i',
				chr(195).chr(177) => 'n', chr(195).chr(178) => 'o',
				chr(195).chr(179) => 'o', chr(195).chr(180) => 'o',
				chr(195).chr(181) => 'o', chr(195).chr(182) => 'o',
				chr(195).chr(182) => 'o', chr(195).chr(185) => 'u',
				chr(195).chr(186) => 'u', chr(195).chr(187) => 'u',
				chr(195).chr(188) => 'u', chr(195).chr(189) => 'y',
				chr(195).chr(191) => 'y',
				/* Latin Extended-A */
				chr(196).chr(128) => 'A', chr(196).chr(129) => 'a',
				chr(196).chr(130) => 'A', chr(196).chr(131) => 'a',
				chr(196).chr(132) => 'A', chr(196).chr(133) => 'a',
				chr(196).chr(134) => 'C', chr(196).chr(135) => 'c',
				chr(196).chr(136) => 'C', chr(196).chr(137) => 'c',
				chr(196).chr(138) => 'C', chr(196).chr(139) => 'c',
				chr(196).chr(140) => 'C', chr(196).chr(141) => 'c',
				chr(196).chr(142) => 'D', chr(196).chr(143) => 'd',
				chr(196).chr(144) => 'D', chr(196).chr(145) => 'd',
				chr(196).chr(146) => 'E', chr(196).chr(147) => 'e',
				chr(196).chr(148) => 'E', chr(196).chr(149) => 'e',
				chr(196).chr(150) => 'E', chr(196).chr(151) => 'e',
				chr(196).chr(152) => 'E', chr(196).chr(153) => 'e',
				chr(196).chr(154) => 'E', chr(196).chr(155) => 'e',
				chr(196).chr(156) => 'G', chr(196).chr(157) => 'g',
				chr(196).chr(158) => 'G', chr(196).chr(159) => 'g',
				chr(196).chr(160) => 'G', chr(196).chr(161) => 'g',
				chr(196).chr(162) => 'G', chr(196).chr(163) => 'g',
				chr(196).chr(164) => 'H', chr(196).chr(165) => 'h',
				chr(196).chr(166) => 'H', chr(196).chr(167) => 'h',
				chr(196).chr(168) => 'I', chr(196).chr(169) => 'i',
				chr(196).chr(170) => 'I', chr(196).chr(171) => 'i',
				chr(196).chr(172) => 'I', chr(196).chr(173) => 'i',
				chr(196).chr(174) => 'I', chr(196).chr(175) => 'i',
				chr(196).chr(176) => 'I', chr(196).chr(177) => 'i',
				chr(196).chr(178) => 'IJ',chr(196).chr(179) => 'ij',
				chr(196).chr(180) => 'J', chr(196).chr(181) => 'j',
				chr(196).chr(182) => 'K', chr(196).chr(183) => 'k',
				chr(196).chr(184) => 'k', chr(196).chr(185) => 'L',
				chr(196).chr(186) => 'l', chr(196).chr(187) => 'L',
				chr(196).chr(188) => 'l', chr(196).chr(189) => 'L',
				chr(196).chr(190) => 'l', chr(196).chr(191) => 'L',
				chr(197).chr(128) => 'l', chr(197).chr(129) => 'L',
				chr(197).chr(130) => 'l', chr(197).chr(131) => 'N',
				chr(197).chr(132) => 'n', chr(197).chr(133) => 'N',
				chr(197).chr(134) => 'n', chr(197).chr(135) => 'N',
				chr(197).chr(136) => 'n', chr(197).chr(137) => 'N',
				chr(197).chr(138) => 'n', chr(197).chr(139) => 'N',
				chr(197).chr(140) => 'O', chr(197).chr(141) => 'o',
				chr(197).chr(142) => 'O', chr(197).chr(143) => 'o',
				chr(197).chr(144) => 'O', chr(197).chr(145) => 'o',
				chr(197).chr(146) => 'OE',chr(197).chr(147) => 'oe',
				chr(197).chr(148) => 'R',chr(197).chr(149) => 'r',
				chr(197).chr(150) => 'R',chr(197).chr(151) => 'r',
				chr(197).chr(152) => 'R',chr(197).chr(153) => 'r',
				chr(197).chr(154) => 'S',chr(197).chr(155) => 's',
				chr(197).chr(156) => 'S',chr(197).chr(157) => 's',
				chr(197).chr(158) => 'S',chr(197).chr(159) => 's',
				chr(197).chr(160) => 'S', chr(197).chr(161) => 's',
				chr(197).chr(162) => 'T', chr(197).chr(163) => 't',
				chr(197).chr(164) => 'T', chr(197).chr(165) => 't',
				chr(197).chr(166) => 'T', chr(197).chr(167) => 't',
				chr(197).chr(168) => 'U', chr(197).chr(169) => 'u',
				chr(197).chr(170) => 'U', chr(197).chr(171) => 'u',
				chr(197).chr(172) => 'U', chr(197).chr(173) => 'u',
				chr(197).chr(174) => 'U', chr(197).chr(175) => 'u',
				chr(197).chr(176) => 'U', chr(197).chr(177) => 'u',
				chr(197).chr(178) => 'U', chr(197).chr(179) => 'u',
				chr(197).chr(180) => 'W', chr(197).chr(181) => 'w',
				chr(197).chr(182) => 'Y', chr(197).chr(183) => 'y',
				chr(197).chr(184) => 'Y', chr(197).chr(185) => 'Z',
				chr(197).chr(186) => 'z', chr(197).chr(187) => 'Z',
				chr(197).chr(188) => 'z', chr(197).chr(189) => 'Z',
				chr(197).chr(190) => 'z', chr(197).chr(191) => 's',
				/* Euro Sign */
				chr(226).chr(130).chr(172) => 'E',
				/* GBP (Pound) Sign */
				chr(194).chr(163) => '' );

			$string = strtr($string, $_chr);
		}
		else
		{
			$_chr      = array();
			$_dblChars = array();
			
			/* We assume ISO-8859-1 if not UTF-8 */
			$_chr['in'] =   chr(128).chr(131).chr(138).chr(142).chr(154).chr(158)
							.chr(159).chr(162).chr(165).chr(181).chr(192).chr(193).chr(194)
							.chr(195).chr(199).chr(200).chr(201).chr(202)
							.chr(203).chr(204).chr(205).chr(206).chr(207).chr(209).chr(210)
							.chr(211).chr(212).chr(213).chr(217).chr(218)
							.chr(219).chr(220).chr(221).chr(224).chr(225).chr(226).chr(227)
							.chr(231).chr(232).chr(233).chr(234).chr(235)
							.chr(236).chr(237).chr(238).chr(239).chr(241).chr(242).chr(243)
							.chr(244).chr(245).chr(249).chr(250).chr(251)
							.chr(252).chr(253).chr(255).chr(191).chr(182).chr(179).chr(166)
							.chr(230).chr(198).chr(175).chr(172).chr(188)
							.chr(163).chr(161).chr(177);

			$_chr['out'] = "EfSZszYcYuAAAACEEEEIIIINOOOOUUUUYaaaaceeeeiiiinoooouuuuyyzslScCZZzLAa";

			$string           = strtr( $string, $_chr['in'], $_chr['out'] );
			$_dblChars['in']  = array( chr(140), chr(156), chr(196), chr(197), chr(198), chr(208), chr(214), chr(216), chr(222), chr(223), chr(228), chr(229), chr(230), chr(240), chr(246), chr(248), chr(254));
			$_dblChars['out'] = array('Oe', 'oe', 'Ae', 'Aa', 'Ae', 'DH', 'Oe', 'Oe', 'TH', 'ss', 'ae', 'aa', 'ae', 'dh', 'oe', 'oe', 'th');
			$string           = str_replace($_dblChars['in'], $_dblChars['out'], $string);
		}
				
		return $string;
	}

	static public function format($content, $type = 'long', $max = 1000){

		switch($type){
			case 'short':
				//remove youtube
				$content = preg_replace("%{{youtube.+?}}%is", "", $content);
        
		        if (preg_match("~(<p[^>]*>\s*)?<!-- pagebreak -->(\s*<\/p>)?~i", $content, $match, PREG_OFFSET_CAPTURE))
		        {
		            $content = substr($content, 0, $match[0][1]);
		            $content = strip_tags($content);
		        }
		        else{
		        	/**
					 * Обрезывает строку, с указанным лимитом, заканчивая слово
					 */
		        	$content = preg_replace("`^(.{".$max."}.*?)[ .,:!?].*$`s", "$1...", strip_tags($content));

		        }
				break;

			case 'long':
				//show widgets
				//для кода типа {{youtube:href=http://youtube.com/v=err934kff width=200 height=400}}
				
				$content = Common::widget($content);
				break;

		}

		return $content;

	}

	/**
	 * Manually utf8 encode to a specific length
	 * Based on notes found at php.net
	 *
	 * @access	public
	 * @param	string		Raw text
	 * @param	int			Length
	 * @return	string
	 */
	static public function utf8Encode( $string, $len=0 )
	{
		$_unicode       = '';
		$_values        = array();
		$_nOctets       = 1;
		$_unicodeLength = 0;
 		$stringLength   = strlen( $string );

		for ( $i = 0 ; $i < $stringLength ; $i++ )
		{
			$value = ord( $string[ $i ] );

			if ( $value < 128 )
			{
				if ( $len && ( $_unicodeLength >= $len ) )
				{
					break;
				}

				$_unicode .= chr($value);
				$_unicodeLength++;
			}
			else
			{
				if ( count( $_values ) == 0 )
				{
					$_nOctets = ( $value < 224 ) ? 2 : 3;
				}

				$_values[] = $value;

				if ( $len && ( $_unicodeLength + ($_nOctets * 3) ) > $len )
				{
					break;
				}

				if ( count( $_values ) == $_nOctets )
				{
					if ( $_nOctets == 3 )
					{
						$_unicode .= '%' . dechex($_values[0]) . '%' . dechex($_values[1]) . '%' . dechex($_values[2]);
						$_unicodeLength += 9;
					}
					else
					{
						$_unicode .= '%' . dechex($_values[0]) . '%' . dechex($_values[1]);
						$_unicodeLength += 6;
					}

					$_values  = array();
					$_nOctets = 1;
				}
			}
		}

		return $_unicode;
	}

	/**
	 * Обрезывает строку, с указанным лимитом, заканчивая слово
	 */
	public static function og_trim($str, $limit = 150){
		return preg_replace("`^(.{".$limit."}.*?)[ .,:!?].*$`s", "$1...", $str);
	}

	 /**
     * Метод для обрезания строк.
     * Взят из плагинов "Smarty 3.0.6"
     * + добавлен параметр "$charset" (Иначе косяки с кириллицей в utf-8)
     * + подправлен код, т.к. неверно обрезались строки до целых слов
     *
     * $string - Строка, которую надо обрезать
     *
     * $length - Определяет максимальную длину обрезаемой строки
     *
     * $etc - Текстовая строка, которая заменяет обрезаемый текст.
     * Её длина НЕ включена в максимальную длину обрезаемой строки.
     *
     * $break_words - Определяет, обрезать ли строку в промежутке между словами (false)
     * или строго на указанной длине (true).
     *
     * $middle - Определяет, нужно ли обрезать строку в конце (false) или в середине строки (true).
     * Обратите внимание, что при включении этой опции, промежутки между словами игнорируются.
     *
     * $exact_length - Если true, то обрезается точно по запрашиваемой длине + $etc, если false, то запрашиваемая длина - длина $etc + $etc
     *
     * $charset - Кодировка строки
     *
     * @param string $string
     * @param integer $length
     * @param string $etc
     * @param boolean $break_words
     * @param boolean $middle
     * @param string $charset
     * @return string
     *
     * @version 0.1 21.08.2011
     * @since 0.1
     * @author webmaxx <webmaxx@webmaxx.name>
     */
    public static function truncate($string, $length = 80, $etc = '...', $break_words = false, $middle = false, $exact_length = true, $charset = 'UTF-8')
    {
        if ($length == 0) return '';

        if (function_exists('mb_strlen')) {
            if (mb_detect_encoding($string, 'UTF-8, ISO-8859-1') === 'UTF-8') {
                // $string has utf-8 encoding
                if (mb_strlen($string, $charset) > $length) {
                    if (!$break_words && !$middle) {
                        $string = mb_ereg_replace('/\s+?(\S+)?$/u', '', mb_substr($string, 0, $length + 1, $charset));

                        if (mb_strlen($string, $charset) > $length)
                            return preg_replace('/\s+?(\S+)?$/u', '', $string) . $etc;

                    }

                    if (!$exact_length) $length -= min($length, mb_strlen($etc, $charset));

                    if (!$middle)
                        return mb_substr($string, 0, $length, $charset) . $etc;
                    else
                        return mb_substr($string, 0, $length / 2, $charset) . $etc . mb_substr($string, -$length / 2, $charset);

                } else {
                    return $string;
                }
            }
        }

        // $string has no utf-8 encoding
        if (strlen($string) > $length) {
            if (!$break_words && !$middle) {
                $string = preg_replace('/\s+?(\S+)?$/', '', substr($string, 0, $length + 1));

                if (mb_strlen($string, $charset) > $length)
                    return preg_replace('/\s+?(\S+)?$/', '', $string) . $etc;
            }

            if (!$exact_length) $length -= min($length, strlen($etc));

            if (!$middle)
                return substr($string, 0, $length) . $etc;
            else
                return substr($string, 0, $length / 2) . $etc . substr($string, -$length / 2);
        } else {
            return $string;
        }
    }

    public static function isEmail($string){
        return preg_match("%^[-a-z0-9!#$&'*+/=?^_`{|}~]+(?:\.[-a-z0-9!#$&'*+/=?^_`{|}~]+)*@(?:[a-z0-9]([-a-z0-9]{0,61}[a-z0-9])?\.)*(?:aero|arpa|asia|biz|cat|com|coop|edu|gov|info|int|jobs|mil|mobi|museum|name|net|org|pro|tel|travel|[a-z][a-z])$%i", $string);
    }

    
    public static function prepareRegExp($regexp, $ignorecase = false, $escape = false, $unicode = true){
    	if($escape)
    		$regexp = str_replace("\\", "\\\\", $regexp); //экранируем символ \ если нужно
        
        return "#" . str_replace("#", "\#", $regexp) . "#" . ($ignorecase ? "i" : "") . ($unicode ? "u" : "");
    }

    //подготавливает регулярку для выполнения условия, если выражение не содержит указанну подстроку
    public static function prepareRegExpNegative($regexp, $ignorecase = false, $escape = false, $unicode = true){
    	if($escape)
    		$regexp = str_replace("\\", "\\\\", $regexp); //экранируем символ \ если нужно
        
        return "#" . str_replace("#", "\#", "^((?!({$regexp})).)*$") . "#" . ($ignorecase ? "i" : "") . ($unicode ? "u" : "");
    }


    /**
	 * Функция для изменения ссылку на <a href="" target="_blank">текст ссылки</a>
	 */
	public static function convertToLink($url, $text = '[ссылка]', $target = '_blank', $title = ''){
		$regex = "/((([a-zа-я]{3,9}:(?:\/\/)?)(?:[-;:&=\+\$,\w]+@)?[a-zа-я0-9.-\[\]]+|(?:www.|[-;:&=\+\$,\w]+@)[a-zа-я0-9.-]+)((?:\/[\+~%\/.\w-_]*)?\??(?:[-\+=&;%@.\w_]*)#?(?:[.\!\/\\w]*))?)/iu";
		return preg_replace($regex, CHtml::link(($text ? $text : '$0'), '$0', ['target' => $target, 'rel' => 'tooltip', 'title' => $title]), $url);
	}


	public static function convertToUtf8($s, $inEncoding) {
		$inEncoding = strtolower($inEncoding);

		if($inEncoding == 'koi8-r'){
			$s = @iconv('utf-8', 'cp1252//IGNORE', $s);
			$s = @iconv('KOI8-R', 'utf-8', $s);	
		}
		elseif($inEncoding == 'cp1251-1'){
			$s = @iconv('utf-8', 'cp1252//IGNORE', $s);
			$s = @iconv('cp1251', 'utf-8', $s);
		}
		elseif($inEncoding == 'ibm866-1'){
			$s = @iconv('utf-8', 'cp1252//IGNORE', $s);
			$s = @iconv('ibm866', 'utf-8', $s);
		}
		elseif($inEncoding == 'koi8-u'){
			$s = @iconv("KOI8-U", 'UTF-8', $s);
		}
		elseif($inEncoding == 'cp1251-2'){
			$s = @iconv('cp1251', 'utf-8', $s);
		}
		elseif($inEncoding == 'ibm866-2'){
			$s = @iconv('ibm866', 'utf-8', $s);
		}
		elseif($inEncoding == 'cp1251-3'){
			$s = @iconv('utf-8', 'cp1251', $s);
			$s = @iconv('cp1251', 'KOI8-R', $s);
			$s = @iconv('cp1251', 'utf-8', $s);
		}

		return $s;
	}

}