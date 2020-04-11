<?php
/**
 * Copyright (c) 11/4/2020 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

class CCleanCache extends TCleanMasterFunctions{
    
    private $managedCache, $stackCache, $cache, $compositeCache;
    private $documentRoot;
    public function __construct()
    {
        $this->documentRoot = Bitrix\Main\Application::getDocumentRoot();
        $this->cache = '/bitrix/cache/';
        $this->managedCache = '/bitrix/managed_cache/';
        $this->stackCache = '/bitrix/stack_cache/';
        $this->compositeCache = '/bitrix/html_pages/';
    }
    
    public function CacheClear() {
		DeleteDirFilesEx($this->managed_cache); // управляемый кэш
		DeleteDirFilesEx($this->cache); // просто кэш
		DeleteDirFilesEx($this->stackCache); // джонни кэш
		CHTMLPagesCache::cleanAll();
	}
	
	/*
		Получаем данные для диагностики
	*/
	public function GetDiagnosticData($step = false)
	{
		$_SESSION['master']['diagnostic']['cache']['size'] = 0;
		
		$dirs = array(
			$this->cache,
			$this->managedCache,
			$this->stackCache,
			$this->compositeCache,
		);
		
		foreach($dirs as $dir)
		{
			if(file_exists($this->documentRoot.$dir))
			{
				$_SESSION['master']['diagnostic']['cache']['dirs'][$dir] = $this->GetDirSize($this->documentRoot.$dir);
				$_SESSION['master']['diagnostic']['cache']['size'] += $_SESSION['master']['diagnostic']['cache']['dirs'][$dir];
			}
		}
		return false;
	}
}
