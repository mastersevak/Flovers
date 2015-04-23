<?php 


/**
* FileCloud
*/
class FileCloud extends CApplicationComponent
{
	public $url;
	public $login;
	public $password;
	public $key;

	public function init(){

		if(!$this->url) throw new Exception('Не указан путь для работы FileCloud');
		if(!$this->key) throw new Exception('Не указан ключ для работы FileCloud');

		parent::init();
	}
	
	/**
	 * Загрузка файла на cloud сервер
	 * @param  [type] $file   файл для загрузки (это может быть экзампляр типа CUploadedFile, 
	 * путь файлу, либо буфер с содержимым файла)
	 * @param  [type] $target путь для загрузки (если не указан, то берется такой же путь 
	 * который указан в источнике, с условием что первый параметр, является строкой, т.е путем к файлу)
	 * 
	 */
	public function upload($file, $target, $deleteTempFile = true){

		if(strpos(basename($target), '.') !== false){
			$filename = basename($target);
			$target = dirname($target);
		}
		else{
			$filename = basename(is_string($file) ? $file : $file->name) ;
		}

		if($file instanceof CUploadedFile){ 
			File::checkPermissions($target);

			if($file->saveAs($target.DS.$filename))
				$filepath = $target.DS.$filename;
		}
		elseif(is_string($file) && ($file[0] == '/' || $file[0] == '.')){ //$file - это путь к файлу
			$filepath = $file;
		}
		else{ //$file - это буфер с текстом
			$filepath = $target.DS.$filename;
			file_put_contents($filepath, $file);
		}

		if($filepath){

			if($this->isProductionMode()){

				$params = "http://".$this->key."@".str_replace("http://", "", $this->getUrl());
				shell_exec("curl -T {$filepath} '{$params}".$this->getPath($target, false)."/{$filename}'");
				
			}
			else{
				File::checkPermissions($target);
				copy($filepath, $target.DS.$filename);
			}

			if(($file instanceof CUploadedFile || (is_string($file) && $file[0] == '/')) && $deleteTempFile){
				@unlink($filepath);
			}

			return true;
		}

		return false;
	}


	public function download($from, $to = false){
		File::downloadFile($this->getPath($from), $to, false, $this->login, $this->password);
	}

	public function delete($filename){
		if($this->isProductionMode()){
			$params = "http://".$this->key."@".str_replace("http://", "", $this->getUrl());
			shell_exec("curl -X DELETE '{$params}".$this->getPath($filename, false)."'");
		}
		else{
			@unlink($filename);
		}
	}

	public function getUrl(){
		return $this->url;
	}

	//меняем локальный путь к онлайновскому
	private function getPath($path, $withPrefix = true){
		if(!$this->isProductionMode()) return $path;

		$target = str_replace(Yii::getPathOfAlias('webroot').DS, "", $path);
		if($withPrefix) $target = $this->getUrl(). $target;

		return $target;
	}

	public function isProductionMode(){
		return APPLICATION_ENV != 'devel';
	}

}