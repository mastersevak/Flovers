<?php

/**
* CyrillicUrlValidator
*/
class CyrillicUrlValidator extends CUrlValidator
{
	
	public $pattern='/^{schemes}:\/\/(([A-Zа-яёЁцЦА-Я0-9][A-Zа-яёЁцЦА-Я0-9_-]*)(\.[A-Zа-яёЁцЦА-Я0-9][A-ZА-Яа-яёЁцЦ0-9_-]*)+)/i'; //параметр u необходим для кириллицы
}