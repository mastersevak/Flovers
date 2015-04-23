<?php

Yii::import('core.extensions.yii-EClientScript.EClientScript');

class SClientScript extends EClientScript
{
	public $skipScripts = array();

	/**
	 * полностью скопировал функцию из родительского файла, 
	 * только добавил в начале добавленный кусок, и заменил $scriptFiles,
	 * на scriptFiles
	 */
	protected function combineScriptFiles($type = self::POS_HEAD)
	{

		$scripts = $this->scriptFiles;

		foreach($scripts as $key => $script){
			if(is_array($script)){
				foreach($script as $key2 => $one){
					if(in_array($one, $this->skipScripts)){
						unset($scripts[$key][$key2]);
					}
				}
			}
		}

		$scriptFiles = $scripts;

		// Check the need for combination
		if (!isset($scriptFiles[$type]) || count($scriptFiles[$type]) < 2) {
			return;
		}
		$toCombine = array();
		$indexCombine = 0;
		$scriptName = $scriptValue = array();
		foreach ($scriptFiles[$type] as $url => $value) {
			if (is_array($value) || !($file = $this->getLocalPath($url))) {
				$scriptName[] = $url;
				$scriptValue[] = $value;
			} else {
				if (count($toCombine) === 0) {
					$indexCombine = count($scriptName);
					$scriptName[] = $url;
					$scriptValue[] = $url;
				}
				$toCombine[$url] = $file;
			}
		}
		if (count($toCombine) > 1) {
			// get unique combined filename
			$fname = $this->getCombinedFileName($this->scriptFileName, array_values($toCombine), $type);
			$fpath = Yii::app()->assetManager->basePath . DIRECTORY_SEPARATOR . $fname;
			// check exists file
			if (($valid = file_exists($fpath)) === true) {
				$mtime = filemtime($fpath);
				foreach ($toCombine as $file) {
					if ($mtime < filemtime($file)) {
						$valid = false;
						break;
					}
				}
			}
			// re-generate the file
			if (!$valid) {
				$fileBuffer = '';
				foreach ($toCombine as $url => $file) {
					$contents = file_get_contents($file);
					if ($contents) {
						// Append the contents to the fileBuffer
						$fileBuffer .= "/*** Script File: {$url}";
						if ($this->optimizeScriptFiles && strpos($file, '.min.') === false && strpos($file, '.pack.') === false) {
							$fileBuffer .= ", Original size: " . number_format(strlen($contents)) . ", Compressed size: ";
							$contents = $this->optimizeScriptCode($contents);
							$fileBuffer .= number_format(strlen($contents));
						}
						$fileBuffer .= " ***/\n";
						$fileBuffer .= $contents . "\n;\n";
					}
				}
				file_put_contents($fpath, $fileBuffer);
			}
			// add the combined file into scriptFiles
			$url = Yii::app()->assetManager->baseUrl . '/' . $fname . '?' . filemtime($fpath);
			$scriptName[$indexCombine] = $url;
			$scriptValue[$indexCombine] = $url;
		}
		// use new scriptFiles list replace old ones
		$scriptFiles[$type] = array_combine($scriptName, $scriptValue);

		$this->scriptFiles[$type] = CMap::mergeArray($scriptFiles[$type], $this->skipScripts);
	}

	//заменил чтобы скрипты из пакетов тоже перекомпилировались | false, -1, YII_DEBUG
	public function getPackageBaseUrl($name)
	{
		if(!isset($this->coreScripts[$name]))
			return false;
		$package=$this->coreScripts[$name];
		if(isset($package['baseUrl']))
		{
			$baseUrl=$package['baseUrl'];
			if($baseUrl==='' || $baseUrl[0]!=='/' && strpos($baseUrl,'://')===false)
				$baseUrl=Yii::app()->getRequest()->getBaseUrl().'/'.$baseUrl;
			$baseUrl=rtrim($baseUrl,'/');
		}
		elseif(isset($package['basePath'])){
			$recreate = APPLICATION_ENV != 'testproduction' && YII_DEBUG;
			$baseUrl=Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias($package['basePath']), false, -1, $recreate);
		}
		else
			$baseUrl=$this->getCoreScriptUrl();

		return $this->coreScripts[$name]['baseUrl']=$baseUrl;
	}

	//заменил функцию чтобы в пакетах, тоже можно было указывать external links (google web fonts, ....)
	public function registerCoreScript($name){
		if(!isset($this->coreScripts[$name]) && isset($this->packages[$name])){
			//css
			if(isset($this->packages[$name]['css']) && is_array($this->packages[$name]['css'])) 
				foreach($this->packages[$name]['css'] as $index => $css){
					if(preg_match('#^(\/\\/|http)#', $css)){
						$this->registerCssFile($css);
						unset($this->packages[$name]['css'][$index]);
					}
				}

			//js
			if(isset($this->packages[$name]['js']) && is_array($this->packages[$name]['js'])) 
				foreach($this->packages[$name]['js'] as $index => $js){
					if(preg_match('#^(\/\\/|http)#', $js)){
						$this->registerScriptFile($js, CClientScript::POS_END);
						unset($this->packages[$name]['js'][$index]);
					}
				}
		}

		parent::registerCoreScript($name);
	}
}