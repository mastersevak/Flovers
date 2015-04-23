<?php 

/**
* FILE - вспомогательный класс со статическими функциями, для работы с файлами и папками
*/
class File
{
	
	/**
	 * Download content as text
	 */
	public static function downloadAs($title, $name, $content, $type='text')
	{
		$types = array(
						'text' => 'text/plain',
						'pdf' => 'application/pdf',
						'word' => 'application/msword'
						);
						
		$exts = array(
						'text' => 'txt',
						'pdf' => 'pdf',
						'word' => 'doc'
						);	
						
		// Load anything?
		if( $type == 'pdf' )
		{
			$pdf = Yii::createComponent('ext.tcpdf.ETcPdf', 'P', 'cm', 'A4', true, 'UTF-8');
			$pdf->SetCreator(PDF_CREATOR);
			$pdf->SetAuthor(Yii::app()->name);
			$pdf->SetTitle($title);
			$pdf->SetSubject($title);
			$pdf->setPrintHeader(false);
			$pdf->setPrintFooter(false);
			$pdf->AliasNbPages();
			$pdf->AddPage();
			$pdf->writeHTML($content, true, 0, true, 0);
			$pdf->Output($name . '.' . $exts[ $type ], "I");
		}							
		
		
		header('Cache-Control: no-cache, no-store, max-age=0, must-revalidate');
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
		header('Pragma: no-cache');
		header("Content-Type: ".$types[ $type ]."");
		header("Content-Disposition: attachment; filename=\"".$name . '.' . $exts[ $type ] ."\";");
	    header("Content-Length: ".mb_strlen($content));
		echo $content;
		exit;
	}

	/**
	 * Convert bytes to human readable format
	 *
	 * @param integer bytes Size in bytes to convert
	 * @return string
	 */
	public static function bytesToSize($bytes, $precision = 2)
	{	
		$kilobyte = 1024;
		$megabyte = $kilobyte * 1024;
		$gigabyte = $megabyte * 1024;
		$terabyte = $gigabyte * 1024;

		if (($bytes >= 0) && ($bytes < $kilobyte)) {
			return $bytes . ' B';

		} elseif (($bytes >= $kilobyte) && ($bytes < $megabyte)) {
			return round($bytes / $kilobyte, $precision) . ' KB';

		} elseif (($bytes >= $megabyte) && ($bytes < $gigabyte)) {
			return round($bytes / $megabyte, $precision) . ' MB';

		} elseif (($bytes >= $gigabyte) && ($bytes < $terabyte)) {
			return round($bytes / $gigabyte, $precision) . ' GB';

		} elseif ($bytes >= $terabyte) {
			return round($bytes / $gigabyte, $precision) . ' TB';
		} else {
			return $bytes . ' B';
		}
	}

	/**
	 * Функция проверяет папку на существование или на запись,
	 * и в случае если не существует то создает с правами на запись,
	 * если же существует но нет прав, пробует установить, 
	 * если удается, возвращает true, если же нет false
	 * 
	 * @param  string  $folder [description]
	 * @param  integer $mode   permission mode
	 * @return bool
	 */
	public static function checkPermissions($folder, $mode = 0755){
		if(is_array($folder)){
			foreach($folder as $one)
				self::checkPermissions($one, $mode);
		}
		else{
			if(!file_exists($folder)){
				mkdir($folder, $mode, true);
				return chmod($folder, $mode);
			}
		    
		    if(is_dir($folder)) {
		    	if(!is_writable($folder)){
		    		return chmod($folder, $mode);
		    	}

		    	return true;
		    }

		    throw new Exception("У вас нет прав на запись в папку: {$folder}");
		}
	}

	/**
	 * Фукция которая создает папку со вложенностью
	 * Например в папке $folder, надо создать папку /23/ab/24
	 * если входная строка 23ab24
	 * Делается это для того, чтобы в одной папке не хранилось много файлов
	 */
	public static function getChunkedPath($folder, $string, $size = 2)
    {
        $path = $folder . DS . chunk_split($string, $size, DS);

        return self::checkPermissions($path) ? $path : false;
          
    } 

    /**
	 * Проверка существования файла либо на локальном, либо на удаленном сервере
	 * @param  string $file - путь к файлу
	 * @return boolean
	 */
	public static function fileExists($file) 
	{
		if(preg_match('#^http#', $file)){
			return self::remoteFileExists($file);
		}
		return file_exists($file);
	}

    /**
	 * Проверка существования файла на удаленном сервере
	 * @param  string $file - путь к файлу
	 * @return boolean
	 */
	public static function remoteFileExists($url, $login = false, $password = false, $authdata = false) 
	{
	    $headers = self::getHeadersRemoteFile($url, $login, $password, $authdata);
		return $headers['http_code'] === 200;
	}

	/**
	 * Возвращает расширение файла
	 * @param  string $file 	путь к файлу (или имя файла) 
	 * @return string
	 */
	public static function getFileExtension($file) {
		return strtolower(substr(strrchr($file,'.'), 1));
	}

	/**
	 * Возвращает имя файла без расширения
	 * @param  string $file 	путь к файлу (или имя файла)
	 * @return string
	 */
	public static function getFileName($file) {
		// return basename($filename, '.'.$ext); //не используем basename, так как не работает с utf8
		$info = self::mbPathinfo($file);
		return $info['filename']; //pathinfo($filename, PATHINFO_EXTENSION);;
	}

	/**
	 * Возвращает имя файла с расширением
	 * используется в случае, если в файле могут быть русские буквы
	 * @param  string $file 	путь к файлу (или имя файла)
	 * @return string
	 */
	public static function getBaseName($file) {
		// return basename($filename, '.'.$ext); //не используем basename, так как не работает с utf8
		$info = self::mbPathinfo($file);
		return $info['basename']; //pathinfo($filename, PATHINFO_EXTENSION);;
	}

	/**
	 * Решение для utf8
	 */
	public static function mbPathinfo($filepath) {
	    preg_match('%^(.*?)[\\\\/]*(([^/\\\\]*?)(\.([^\.\\\\/]+?)|))[\\\\/\.]*$%im',$filepath,$m);
	    if(actual($m[1])) $ret['dirname']=$m[1];
	    if(actual($m[2])) $ret['basename']=$m[2];
	    if(actual($m[5])) $ret['extension']=$m[5];
	    if(actual($m[3])) $ret['filename']=$m[3];
	    
	    return $ret;
	}


	/**
	* Get Headers function
	* @param str #url
	* @return array
	*/
	public static function getHeadersRemoteFile($url, $login = false, $password = false, $authdata = false)
	{

		$curl = Yii::app()->curl;
		$curl->setOptions([
			// CURLOPT_NOBODY => true,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_HEADER => false,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_MAXREDIRS => 5,
			CURLOPT_POST => ($authdata !== false),
			CURLOPT_COOKIEJAR => Yii::getPathOfAlias('app.runtime').DS.'cookie.txt',
			CURLOPT_COOKIEFILE => Yii::getPathOfAlias('app.runtime').DS.'cookie.txt'
			]);

		if($authdata) {
			$curl->setOption(CURLOPT_POSTFIELDS,  $authdata);
		}

		# если нужна http авторизация перескачиванием
		if($login && $password) {
			$curl->setOption(CURLOPT_USERPWD,  $login . ":" . $password);
		}

		$curl->exec($url);

		return Yii::app()->curl->getInfo();
	}


	/**
	 * Скачивание файла
	 * @param  [type]  $url      [description]
	 * @param  [type]  $path     [description]
	 * @param  boolean $login    [description]
	 * @param  boolean $password [description]
	 * @return [type]            [description]
	 */
	public static function downloadFile($file, $downloadTo = false, $limit = false, $login = false, $password = false, $authdata = false){
		if(self::isRemoteFile($file)){ //файл на удаленном сервере
			if(!self::remoteFileExists($file, $login, $password, $authdata)){
				app()->response->setStatus(HttpResponse::STATUS_NOT_FOUND);
				return false;
			}
			else{
				return self::downloadRemoteFile($file, $downloadTo, $limit, $login, $password, $authdata);
			}
		}
		else{ //файл локальный
			if(!self::fileExists($file))
				app()->response->setStatus(HttpResponse::STATUS_NOT_FOUND);

			return self::forceDownload($file);
		}

	}


	/**
	 * Чтение и скачка удаленного файла
	 * @param  [type]  $url      [description]
	 * @param  [type]  $path     [description]
	 * @param  boolean $login    [description]
	 * @param  boolean $password [description]
	 * @return [type]            [description]
	 */
	public static function downloadRemoteFile($url, $downloadTo = false, $limit = false, $login = false, $password = false, $authdata = false)
	{
		if(!$limit || $headers['download_content_length'] < $limit){
			
			$ch = curl_init();
		    curl_setopt($ch, CURLOPT_URL, $url);

			//в случае если нужно вернуть прочитанную строку
			if($downloadTo == 'content') {
				$r = curl_exec($ch);
				curl_close($ch);
				return $r;
			}

			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		    curl_setopt($ch, CURLOPT_POST, $authdata !== false);

		    if($authdata) {
		    	curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($authdata));
			}

		    // curl_setopt($ch, CURLOPT_HTTPGET, 1);
		    curl_setopt($ch, CURLOPT_NOBODY, 0);
		    curl_setopt($ch, CURLOPT_REFERER, $url);
			//отсылаем серверу COOKIE полученные от него при авторизации
			//сохранять полученные COOKIE в файл
			curl_setopt($ch, CURLOPT_COOKIEJAR, Yii::getPathOfAlias('app.runtime').DS.'cookie.txt');
			curl_setopt($ch, CURLOPT_COOKIEFILE, Yii::getPathOfAlias('app.runtime').DS.'cookie.txt');

			# если нужна http авторизация перескачиванием
			if($login && $password) {
				curl_setopt($ch, CURLOPT_USERPWD,  $login . ":" . $password);
			}

			if($downloadTo){
				if(strpos($downloadTo, '.') === false) {//downloadTo - is folder
					if(substr($downloadTo, -1) != DS) $downloadTo .= DS; //добавляем в конец /
					self::checkPermissions($downloadTo);
					$downloadTo .= basename($url); // добавляем имя файла
				}
				else { //downloadTo - is file
					self::checkPermissions(dirname($downloadTo));
				}

				# open file to write
				$fp = fopen ($downloadTo, 'w+');
				# write data to local file (если нужно скачивание)
				curl_setopt($ch, CURLOPT_FILE, $fp);
				// curl_setopt($ch, CURLOPT_FILE, STDOUT); //возвращает поток, так как файл потом закрывается
				$r = curl_exec($ch);
				// # close local file
				fclose( $fp );	
				curl_close($ch);

				if (filesize($downloadTo) > 0) return true;
			}
			else {
				$r = curl_exec($ch);
				curl_close($ch);

				Yii::app()->getRequest()->sendFile( basename($url), $r);
				return true;
			}
			
		}
		else{
			throw new Exception('Размер скачиваемого файла больше разрешимого: ' . $limit);
		}

	
	}

	private static function isRemoteFile($file){
		return preg_match("/^(http|ftp)/", $file);
	}

	private static function forceDownload($file, $newFileName = false, $mimeType = NULL){
		if(!$newFileName)
			$newFileName = basename($file);

        Yii::app()->getRequest()->sendFile( $newFileName , file_get_contents( $file ), $mimeType);
	}

	//читаем содержимое удаленной страницы, при надобности отправляем пост данные 
	//для авторизации
	public static function readRemoteContent($url, $post = false, $login = false, $password = false){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		
		curl_setopt($ch, CURLOPT_HEADER, 0);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // возвратить то что вернул сервер
	    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); // следовать за редиректами
	    curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
	    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);// таймаут4
	    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_POST, $post !== false ); // использовать данные в post
		curl_setopt($ch, CURLOPT_NOBODY, 0); 
		// curl_setopt($ch, CURLOPT_HEADER, array('Content-Length: ' . ($post ? strlen(http_build_query($post)) : 1) )); // пустые заголовки
	    //сохранять полученные COOKIE в файл
		curl_setopt($ch, CURLOPT_COOKIEJAR, Yii::getPathOfAlias('app.runtime').DS.'cookie.txt');
		curl_setopt($ch, CURLOPT_COOKIEFILE, Yii::getPathOfAlias('app.runtime').DS.'cookie.txt');

		# если нужна http авторизация перескачиванием
		if($login && $password) {
			curl_setopt($ch, CURLOPT_USERPWD,  $login . ":" . $password);
		}
		
		if($post) curl_setopt($ch, CURLOPT_POSTFIELDS,  http_build_query($post));
		curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (Windows; U; Windows NT 5.0; En; rv:1.8.0.2) Gecko/20070306 Firefox/1.0.0.4");
		
		$result = curl_exec($ch);
		curl_close($ch);
		return $result;
	}

	/**
	 * Функция для распаковки zip архивов
	 * @param  string   $from                [путь к файлу]
	 * @param  string   $to                  [путь для распаковки, (если не указан пойдет в runtime)]
	 * @param  function $callback            [callback функция после распаковки]
	 * @param  function $purifier            [функция если нужно както изменять имена расспаковываемых файлов]
	 * @param  boolean  $autodelete          [нужно ли удалять сам архивный файл после распаковки]
	 * @param  boolean  $allowedExtensions   [массив с расширениями, которые можно распаковывать, по умолчанию можно все]
	 * @return array                         [список распакованных файлов]
	 */
	public static function unzip($from, $to = false, $callback = false, $purifier = false, $autodelete = false, $allowedExtensions = false){

		/*
		не совсем удобный вариант, но тоже пойдет
		$zipfile = new ZipArchive;
		if($zipfile->open($from) === true){
			if(!$to) $to = app()->getRuntimePath();

			self::checkPermissions($to);

			//распаковываем архив
			$zipfile->extractTo($to);
			$zipfile->close();

			if($callback) call_user_func($callback);

			if($autodelete) 
				@unlink($from); //удаление архивного файла

			return true;
		}*/

		/**
		 * Второй вариант использования
		 */
		
		$zip = new ZipArchive;

		$result = [];

		if ($zip->open($from) === true) {
			if(!$to) $to = app()->getRuntimePath();

			self::checkPermissions($to);

		    for($i = 0; $i < $zip->numFiles; $i++) {
		        $filename = $zip->getNameIndex($i);
		        $fileinfo = pathinfo($filename);
		        $nameInArchive = $filename; //$fileinfo['basename'];
		        $filename = $nameInArchive;

 				$ext = File::getFileExtension($filename);

		        if($allowedExtensions){
		        	if(!in_array('zip', $allowedExtensions)) $allowedExtensions[] = 'zip';
		        	if(!in_array('rar', $allowedExtensions)) $allowedExtensions[] = 'rar';
		        	if(!in_array($ext, $allowedExtensions)) continue;
		        } 

		        if($purifier) $filename = call_user_func($purifier, $filename);

		        if(!$filename) continue;		       

		        if(copy("zip://".$from."#".$nameInArchive, $to.DS.$filename)){
		        	//рекурсивное распаковывание файлов
		        	if($ext == 'zip') 
			    		$result = array_merge($result, self::unzip($to.DS.$filename, $to, false, $purifier, true, $allowedExtensions));
		        	elseif($ext == 'rar')
		        		$result = array_merge($result, self::unrar($to.DS.$filename, $to, false, $purifier, true, $allowedExtensions));
					else $result[] = $to.DS.$filename;
		        }
		    }   

		    $zip->close(); 

		    if($callback) call_user_func($callback, $result);

		    if($autodelete) 
				@unlink($from); //удаление архивного файла                
		}

		return $result;
	}

	/**
	 * Функция для распаковки rar архивов
	 * Необходимо расширение rar.so
	 *
	 * download http://pecl.php.net/package/rar
	 * 
	 * gunzip rar-xxx.tgz
	 * tar -xvf rar-xxx.tar
	 * cd rar-xxx
	 * phpize
	 * ./configure && make && make install
	 *
	 * Вы также можете воспользоваться установщиком PECL, чтобы установить расширение Rar. Для этого необходимо использовать команду: pecl -v install rar
	 * 
	 * @param  string   $from                [путь к файлу]
	 * @param  string   $to                  [путь для распаковки, (если не указан пойдет в runtime)]
	 * @param  function $callback            [callback функция после распаковки]
	 * @param  function $purifier            [функция если нужно както изменять имена расспаковываемых файлов]
	 * @param  boolean  $autodelete          [нужно ли удалять сам архивный файл после распаковки]
	 * @param  boolean  $allowedExtensions   [массив с расширениями, которые можно распаковывать, по умолчанию можно все]
	 * @return array                         [список распакованных файлов]
	 */
	public static function unrar($from, $to = false, $callback = false, $purifier = false, $autodelete = false, $allowedExtensions = false){

		$result = [];

		if($rar_file = rar_open($from)){
			if(!$to) $to = app()->getRuntimePath();

			self::checkPermissions($to);

			$list = rar_list($rar_file);
			
			foreach($list as $file) {
				$filename = preg_replace("/RarEntry for file [\"\'](.+)[\"\']\s\(.+\)/", "$1", $file);
			    $entry = rar_entry_get($rar_file, $filename);

		        $ext = File::getFileExtension($filename);

		        if($allowedExtensions){
		        	if(!in_array('zip', $allowedExtensions)) $allowedExtensions[] = 'zip';
		        	if(!in_array('rar', $allowedExtensions)) $allowedExtensions[] = 'rar';
		        	if(!in_array($ext, $allowedExtensions)) continue;
		        } 
			    
			    if($purifier) $filename = call_user_func($purifier, $filename);

		        if(!$filename) continue;

			    if($entry->extract(false, $to.DS.$filename)){ 
			    	//рекурсивное распаковывание файлов
			    	if($ext == 'zip') 
			    		$result = array_merge($result, self::unzip($to.DS.$filename, $to, false, $purifier, true, $allowedExtensions));
		        	elseif($ext == 'rar')
		        		$result = array_merge($result, self::unrar($to.DS.$filename, $to, false, $purifier, true, $allowedExtensions));
					else $result[] = $to.DS.$filename;
			    }

			    
			}
			
			rar_close($rar_file);	

			if($callback) call_user_func($callback, $result);

		    if($autodelete) 
				@unlink($from); //удаление архивного файла      
		}

		return $result;
		
	}

}