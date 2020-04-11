<?php
/**
 * Copyright (c) 11/4/2020 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

IncludeModuleLangFile(__FILE__);


class CCleanSite {
	private $app;
	
	public function __construct()
	{
		$this->app = &$GLOBALS['APPLICATION'];
	}
	
    public function GetActiveSites()
    {
        $dbSites = CSite::GetList(($by="sort"), ($order="desc"), Array("ACTIVE" => "Y"));
        $activeSites = array();
        while($arSite = $dbSites->Fetch())
        {
            $activeSites[$arSite['ID']] = $arSite;
        }
        return $activeSites;
    }
    public function GetInactiveSites()
    {
        $dbSites = CSite::GetList(($by="sort"), ($order="desc"), Array("ACTIVE" => "N"));
        $anactiveSites = array();
        while($arSite = $dbSites->Fetch())
        {
            $anactiveSites[$arSite['ID']] = $arSite;
        }
        return $anactiveSites;
    }
	public function GetSitesIBlock($sites)
	{
		$siteIDs = array_keys($sites);
		$res = CIBlock::GetList(Array(), Array('SITE_ID' => $siteIDs), true);
		$deletedIBlock = array();
		while($ar_res = $res->Fetch())
		{
			$iblockSites = array();
			$rsSites = CIBlock::GetSite($ar_res['ID']);
			while($arSite = $rsSites->Fetch())
				$iblockSites[] = htmlspecialchars($arSite["SITE_ID"]);
			$isDiff = array_diff($iblockSites, $siteIDs);
			if(empty($isDiff))
			{
				$deletedIBlock[$ar_res['ID']] = $ar_res;
			}
		}
		return $deletedIBlock;
	}
    
    public function CleanSites(array $siteList)
    {
		$siteIds = array_keys($siteList);
		$arActiveSites = $this->GetActiveSites();
		$activeSiteDir = array();
		$processReport = array(
			'error' => array(),
			'success' => array(),
		);
		foreach($arActiveSites as $site)
		{
			$activeSiteDir[] = $site['DIR'];
		}
		foreach($siteList as $site_id => $site_dir) {
			$res = CIBlock::GetList(Array(), Array('SITE_ID' => $siteIds), true);
			while($ar_res = $res->Fetch()){
				$iblockSites = array();
				$rsSites = CIBlock::GetSite($ar_res['ID']);
				while($arSite = $rsSites->Fetch())
					$iblockSites[] = htmlspecialchars($arSite["SITE_ID"]);
				$isDiff = array_diff($iblockSites, $siteIds);
				if(empty($isDiff))
				{
					if(CIBlock::Delete($ar_res['ID']))
					{
						$processReport['success'][] = GetMessage("CLEAN_MASTER_INFOBLOK", array('#IBLOCK_ID#' => $ar_res['ID'], '#IBLOCK_NAME#' => $ar_res['NAME']));
					}
					else
					{
						//if($ex = $this->app->GetException())
						//	$processReport['error'][] = $ex->GetString();
					}
				}
			}

			if (CSite::Delete($site_id) === false){
				if($ex = $this->app->GetException())
					$processReport['error'][] = str_replace('#SITE#', $site_id, GetMessage('MASTER_ACTION_1_SITE')).$ex->GetString();
			}
			else{
				$processReport['success'][] = GetMessage('MASTER_ACTION_1_SITE_DELETED', array('#SITE#' => $site_id));
			}
			if(!in_array($site_dir, $activeSiteDir))
			{
				if(file_exists($site_dir))
					DeleteDirFilesEx($site_dir);
			}
		}
		return $processReport;
    }
	
	/*
		Получаем данные для диагностики
	*/
	public function GetDiagnosticData($step = false)
	{
		$anactiveSites = $this->GetInactiveSites();
		$anactiveSitesIB = $this->GetSitesIBlock($anactiveSites);
		$_SESSION['master']['diagnostic']['site']['site'] = $anactiveSites;
		$_SESSION['master']['diagnostic']['site']['iblock'] = $anactiveSitesIB;
		return false;
	}
}
