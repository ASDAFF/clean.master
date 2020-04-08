<?php


class CCleanComponent extends TCleanMasterFunctions
{
	public $documentRoot;
	private $app;
	private $filePerStep = 200;

	static $arComponentsGetComponents = array();
	
    public function __construct()
    {
        $this->app = Bitrix\Main\Application::getInstance();
		$this->documentRoot = Bitrix\Main\Application::getDocumentRoot();
    }
    
	public function GetComponents()
    {
    	// cache
    	if (count(self::$arComponentsGetComponents) > 0) {
			return self::$arComponentsGetComponents;
	    }

	    $arComponents = array();

		$componentPath = array(
			$this->documentRoot . '/bitrix/components/',
			$this->documentRoot . '/local/components/'
		);
		foreach($componentPath as $path)
		{
			if(file_exists($path))
			{
				$parthners = scandir($path);
				$parthners = $this->PathFilter($parthners);
				$location = $path == $this->documentRoot.'/bitrix/components/' ? 'bitrix' : 'local';
				foreach ($parthners as $pName)
				{
					$components = scandir($path.$pName);
					$components = $this->PathFilter($components);
					foreach ($components as $comp)
					{
						if (! is_dir($path . $pName.'/'.$comp)) {
							continue;
						}

						$arComponents[] = array(
							'location' => $location,
							'parthner' => $pName,
							'component' => $comp
						);
					}
				}
			}
		}

	    self::$arComponentsGetComponents = $arComponents;

		return $arComponents;
    }
	public function GetRegExp($components = array(), $skipExists = false)
	{
		if(!is_array($components))
			$components = array();
		$retRegExp = array();
		foreach($components as $component)
		{
			if($skipExists)
			{
				if($_SESSION['CLEAR_COMPONENTS']['STATUS_LIST'][$component['parthner'].':'.$component['component']]['inuse'])
					continue;
			}
			$retRegExp[] = preg_quote($component['parthner'].':'.$component['component']);
		}
		$retRegExp = implode('|', $retRegExp);
		return $retRegExp;
	}
	
	public function GetComponentFiles($rootdir, $except)
	{
		$processFiles = array();
		if(file_exists($rootdir))
		{
			$rootDirs = scandir($rootdir);
			$rootDirs = $this->PathFilter($rootDirs);
	
			foreach($rootDirs as $dir)
			{
				if(in_array($dir, $except))
					continue;
					
				if(is_dir($rootdir.'/'.$dir))
				{
					$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($rootdir.'/'.$dir), RecursiveIteratorIterator::SELF_FIRST);
					foreach($iterator as $item)
					{
						$file_extension = pathinfo($item->getFilename(), PATHINFO_EXTENSION);
						
						if(!$item->isDir() && $file_extension == 'php')
						{
							$processFiles[] = $item->getRealPath();
						}
					}
				}
				else
				{
					$finfo = pathinfo($rootdir.'/'.$dir);
					if($finfo['extension'] == 'php')
					{
						$processFiles[] = $rootdir.'/'.$dir;
					}
				}
			}
		}
		return $processFiles;
	}
	public function GetComponentFilesEx($rootdir, $except)
	{
		$processFiles = array();
		if(file_exists($rootdir))
		{
			$rootDirs = scandir($rootdir);
			$rootDirs = $this->PathFilter($rootDirs);
			
			foreach($rootDirs as $dir)
			{
				if(is_dir($rootdir.'/'.$dir))
				{
					if(in_array($dir, $except))
						continue;
					$subprocessFiles = $this->GetComponentFilesEx($rootdir.'/'.$dir, $except);
					$processFiles = array_merge($processFiles, $subprocessFiles);
				}
				else
				{
					$finfo = pathinfo($rootdir.'/'.$dir);
					if($finfo['extension'] == 'php')
					{
						$processFiles[] = $rootdir.'/'.$dir;
					}
				}
			}
		}
		return $processFiles;
	}
	public function GetProcessComponent()
	{
		if(isset($_SESSION['CLEAR_COMPONENTS']['STATUS_LIST']))
			return $_SESSION['CLEAR_COMPONENTS']['STATUS_LIST'];

		$componentList = array();
		foreach($this->GetComponents() as $arComponent)
		{
			$componentList[$arComponent['parthner'].':'.$arComponent['component']] = array(
				'inuse' => false,
				'file' => array(),
				'location' => $arComponent['location'],
			);
		}
		return ($_SESSION['CLEAR_COMPONENTS']['STATUS_LIST'] = $componentList);
	}

	public function ProcessComponents($step)
	{
		if ($step == 1) {
			unset($_SESSION['CLEAR_COMPONENTS']);
		}

		if(!isset($_SESSION['CLEAR_COMPONENTS']['STATUS_LIST'])) {
			$_SESSION['CLEAR_COMPONENTS']['STATUS_LIST'] = $this->GetProcessComponent();
		}
		if(!isset($_SESSION['CLEAR_COMPONENTS']['FILE_LIST'])) {
			$filesPart1 = $this->GetComponentFiles($this->documentRoot, array('bitrix', 'upload'));
			$filesPart2 = $this->GetComponentFiles($this->documentRoot.'/bitrix', array('backup', 'modules', 'cache', 'managed_cache', 'stack_cache', 'wizards', 'css', 'js', 'tmp', 'updates'));
			$filesPart3 = $this->GetComponentFilesEx($this->documentRoot.'/bitrix/modules', array('components'));
			$_SESSION['CLEAR_COMPONENTS']['FILE_LIST'] = array_merge($filesPart1, $filesPart2, $filesPart3);
		}

		$files = &$_SESSION['CLEAR_COMPONENTS']['FILE_LIST'];
		$componentList = &$_SESSION['CLEAR_COMPONENTS']['STATUS_LIST'];
		$pattern = $this->GetRegExp($this->GetComponents(), true);

		$start = ($step - 1) * $this->filePerStep;
		$end = $start + $this->filePerStep;
		
		for($i = $start; $i < $end; $i++)
		{
			if (!file_exists($files[$i])
				|| (strpos($files[$i], '/bitrix/catalog_export/') !== false)
			) {
				continue;
			}

			$file = file_get_contents($files[$i]);
			if(preg_match_all('/'.$pattern.'/', $file, $matches))
			{
				foreach($matches[0] as $match)
				{
					if(array_key_exists($match, $componentList))
					{
						$componentList[$match]['inuse'] = true;
						if(!in_array($files[$i], $componentList[$match]['file']))
							$componentList[$match]['file'][] = $files[$i];
					}
				}
			}
			unset($file, $matches);
		}
		if($end >= count($files))
		{
			return false;
		}
		return true;
	}
	private function UnusedComponentsHandler($item, $key, &$component)
	{
		if($key == $component['name'])
		{
			$_SESSION['CLEAR_COMPONENTS']['STATUS_LIST'][$key]['title'] = $item['TITLE'];
			$_SESSION['CLEAR_COMPONENTS']['STATUS_LIST'][$key]['description'] = $item['DESCRIPTION'];
			return;
		}
		if(is_array($item))
			array_walk($item, array($this, 'UnusedComponentsHandler'), $component);
		
	}
	public function GetUnusedComponents()
	{
		$components = &$_SESSION['CLEAR_COMPONENTS']['STATUS_LIST'];
		if(!is_array($components))
			return array();
		$tree = CComponentUtil::GetComponentsTree();
		foreach($components as $key => &$comp)
		{
			if(!$comp['inuse'])
			{
				$comp['name'] = $key;
				array_walk($tree, array($this, 'UnusedComponentsHandler'), $comp);
			}
			else
			{
				unset($components[$key]);
			}
		}
		return $components;
	}
	public function DeleteComponents($components)
	{
		if(!isset($_SESSION['CLEAR_COMPONENTS']['STATUS_LIST']))
			return false;
		$arComponents = &$_SESSION['CLEAR_COMPONENTS']['STATUS_LIST'];
		foreach($components as $comp)
		{
			$arComp = explode(':', $comp);
			if(array_key_exists($comp, $arComponents))
			{
				@chmod("/{$arComponents[$comp]['location']}/components/{$arComp[0]}/{$arComp[1]}", 0777);
				DeleteDirFilesEx("/{$arComponents[$comp]['location']}/components/{$arComp[0]}/{$arComp[1]}");
			}
		}
		unlink($_SESSION['CLEAR_COMPONENTS']);
	}
	
	public function Test()
	{
		//echo '<pre>', print_r($components, true), '</pre>';
		//echo '<pre>', print_r($this->GetComponentFiles($this->documentRoot, array('bitrix', 'upload', 'upload_tmp')), true), '</pre>';
		echo '<pre>', print_r(CComponentUtil::GetComponentsTree(), true), '</pre>';
	}
	
	/**
	 * Получаем данные для диагностики
	 */
	public function GetDiagnosticData($step = false)
	{
		//return false;
		$_SESSION['cleanmaster']['diagnostic']['component']['size'] = 0;
		if ($this->ProcessComponents($step) == false)
		{
			$_SESSION['cleanmaster']['diagnostic']['component']['components'] = $this->GetUnusedComponents();
			foreach($_SESSION['cleanmaster']['diagnostic']['component']['components'] as $key => $comp)
			{
				$arComp = explode(':', $comp['name']);
				$_SESSION['cleanmaster']['diagnostic']['component']['components'][$key]['size'] = $this->GetDirSize($this->documentRoot."/{$comp['location']}/components/{$arComp[0]}/{$arComp[1]}/");
				$_SESSION['cleanmaster']['diagnostic']['component']['size'] += $_SESSION['cleanmaster']['diagnostic']['component']['components'][$key]['size'];
			}
			return false;
		}
	}
}
