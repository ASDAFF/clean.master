<?php
/**
 * Summary
 */

class CCleanTemplate extends TCleanMasterFunctions {
    //use TCleanMasterFunctions;
    
    private $templateDir;
    private $templateDirFull;
    private $tmpDir;
    private $documentRoot;
    private $tmpDirFull;
    
    public function __construct()
    {
        $this->templateDir = '/bitrix/templates/';
        $this->tmpDir = '/bitrix/templates_cleanmaster/';
        $this->documentRoot = Bitrix\Main\Application::getDocumentRoot();
        $this->templateDirFull = $this->documentRoot.$this->templateDir;
        $this->tmpDirFull = $this->documentRoot.$this->tmpDir;
    }
    
    public function isTmpEmpty()
    {
        if(!file_exists($this->documentRoot.$this->tmpDir))
            return true;
        if($this->GetFilesCount($this->tmpDirFull))
            return false;
        return true;
    }
    public function isDirExists()
	{
		return file_exists($this->tmpDirFull);
	}
    public function GetUsedTemplates()
	{
		$sites = CSite::GetList($by="sort", $order="desc", Array());
		while ($arSite = $sites->Fetch()) { // Получаем все используемые шаблоны
			$dbSiteRes = CSite::GetTemplateList($arSite['ID']);
			while($arSiteRes = $dbSiteRes->Fetch()) {
				$u_templates[] = $arSiteRes['TEMPLATE'];
			}
		}
		$u_templates[] = '.default';
		$templates = array_unique($u_templates);
		return $templates;
	}
	public function GetUnusedTemplates()
	{
		$used = $this->GetUsedTemplates();
		$unused = array();
		$allTemplates = scandir($this->templateDirFull);
		$allTemplates = $this->PathFilter($allTemplates);
		foreach($allTemplates as $template)
		{
			if(!is_dir($this->templateDirFull.$template))
				continue;
			if(!in_array($template, $used))
				$unused[] = $template;
		}
		return $unused;
	}
    public function CleanTemplates($deltemplates) {
		if ($this->isDirExists()){
			$this->SetPermission($this->tmpDirFull, 0777);
			DeleteDirFilesEx($this->tmpDir);
		}
		$templates = $this->GetUsedTemplates();
		$unusedTemplates = $this->GetUnusedTemplates();
		$unusedTemplates = array_diff($unusedTemplates, $deltemplates);
		$templates = array_merge($unusedTemplates, $templates);
		
		$this->SetPermission($this->templateDirFull, 0777);
		$this->SetPermission($this->tmpDirFull, 0777);
		
		rename($this->templateDirFull, $this->tmpDirFull);
		mkdir($this->templateDirFull);
		
		if(file_exists($this->tmpDirFull.'.default/'))
			rename($this->tmpDirFull.'.default/', $this->templateDirFull.'.default/');
		foreach ($templates as $template) {
			if(file_exists($this->tmpDirFull.$template))
				rename($this->tmpDirFull.$template, $this->templateDirFull.$template);
		}
		
		$this->SetPermission($this->templateDirFull, BX_DIR_PERMISSIONS);
		DeleteDirFilesEx($this->tmpDir);
	}
	
	/*
		Получаем данные для диагностики
	*/
	public function GetDiagnosticData($step = false)
	{
		$templates = $this->GetUnusedTemplates();
		foreach($templates as $template)
			$_SESSION['cleanmaster']['diagnostic']['templates']['templates'][$template] = $this->GetDirSize($this->templateDirFull.$template.'/');
		return false;
	}
}
