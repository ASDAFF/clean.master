<?php
IncludeModuleLangFile(__FILE__);


class CCleanSite
{
	private $app;

	public function __construct()
	{
		$this->app = &$GLOBALS['APPLICATION'];
	}

	public function GetActiveSites()
	{
		$dbSites = CSite::GetList(($by = "sort"), ($order = "desc"), Array("ACTIVE" => "Y"));
		$activeSites = array();
		while ($arSite = $dbSites->Fetch()) {
			$activeSites[$arSite['ID']] = $arSite;
		}
		return $activeSites;
	}

	public function GetInactiveSites()
	{
		$dbSites = CSite::GetList(($by = "sort"), ($order = "desc"), Array("ACTIVE" => "N"));
		$anactiveSites = array();
		while ($arSite = $dbSites->Fetch()) {
			$anactiveSites[$arSite['ID']] = $arSite;
		}
		return $anactiveSites;
	}

	public function GetSitesForum($sites)
	{
		$r = array();
		$rs = \CForumNew::GetList(array('sort' => 'asc'), array('SITE_ID' => $sites));
		while ($ar = $rs->Fetch()) {
			$r[] = $ar;
		}
		return $r;
	}

	public function GetSitesIBlock($sites)
	{
		$siteIDs = array_keys($sites);
		$res = CIBlock::GetList(Array(), Array('SITE_ID' => $siteIDs), true);
		$deletedIBlock = array();
		while ($ar_res = $res->Fetch()) {
			$iblockSites = array();
			$rsSites = CIBlock::GetSite($ar_res['ID']);
			while ($arSite = $rsSites->Fetch())
				$iblockSites[] = htmlspecialchars($arSite["SITE_ID"]);
			$isDiff = array_diff($iblockSites, $siteIDs);
			if (empty($isDiff)) {
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
		foreach ($arActiveSites as $site) {
			$activeSiteDir[] = $site['DIR'];
		}
		foreach ($siteList as $site_id => $site_dir) {
			$res = CIBlock::GetList(Array(), Array('SITE_ID' => $siteIds), true);
			while ($ar_res = $res->Fetch()) {
				$iblockSites = array();
				$rsSites = CIBlock::GetSite($ar_res['ID']);
				while ($arSite = $rsSites->Fetch())
					$iblockSites[] = htmlspecialchars($arSite["SITE_ID"]);
				$isDiff = array_diff($iblockSites, $siteIds);
				if (empty($isDiff)) {
					if (CIBlock::Delete($ar_res['ID'])) {
						$processReport['success'][] = GetMessage("ACRIT_CLEANMASTER_INFOBLOK", array('#IBLOCK_ID#' => $ar_res['ID'], '#IBLOCK_NAME#' => $ar_res['NAME']));
					} else {
						//if($ex = $this->app->GetException())
						//	$processReport['error'][] = $ex->GetString();
					}
				}
			}

			if (\CModule::IncludeModule('forum')) {
				$forums = $this->GetSitesForum($site_id);
				foreach ($forums as $forum) {
					if (\CForumNew::Delete($forum['ID'])) {
						$processReport['success'][] = GetMessage("ACRIT_CLEANMASTER_FORUM_DELETE", array('#FORUM_ID#' => $forum['ID'], '#FORUM_NAME#' => $forum['NAME']));
					}
				}
			}

			if (\CModule::IncludeModule('sale')) {
				$db_sales = \CSaleOrder::GetList(array("DATE_INSERT" => "ASC"), array("LID" => $site_id));
				if ($db_sales->SelectedRowsCount() > 0) {
					$processReport['success'][] = GetMessage("ACRIT_CLEANMASTER_CLEAN_SITE_ORDERS");
				}
				while ($ar_sales = $db_sales->Fetch()) {
					\CSaleOrder::Delete($ar_sales['ID']);
				}

				$db_ptype = \CSalePersonType::GetList(array("SORT" => "ASC"), array("LID" => $site_id));
				while ($ptype = $db_ptype->Fetch()) {
					\CSalePersonType::Delete($ptype['ID']);
				}
			}

			if (\CModule::IncludeModule('subscribe')) {
				$db_rub = \CRubric::GetList(array("SORT" => "ASC"), array("LID" => $site_id));
				if ($db_rub->SelectedRowsCount() > 0) {
					$processReport['success'][] = GetMessage("ACRIT_CLEANMASTER_CLEAN_SITE_SUBSCRIBE_RUBRICS");
				}
				while ($ar = $db_rub->Fetch()) {
					\CRubric::Delete($ar['ID']);
				}
			}

			if (CSite::Delete($site_id) === false) {
				if ($ex = $this->app->GetException()) {
					$processReport['error'][] = str_replace('#SITE#', $site_id, GetMessage('CLEANMASTER_ACTION_1_SITE')) . $ex->GetString();
				}
			} else {
				$processReport['success'][] = GetMessage('CLEANMASTER_ACTION_1_SITE_DELETED', array('#SITE#' => $site_id));
			}
			if (!in_array($site_dir, $activeSiteDir)) {
				if (file_exists($site_dir)) {
					DeleteDirFilesEx($site_dir);
				}
			}
		}
		return $processReport;
	}

	/**
	 * Получаем данные для диагностики
	 */
	public function GetDiagnosticData($step = false)
	{
		$anactiveSites = $this->GetInactiveSites();
		$anactiveSitesIB = $this->GetSitesIBlock($anactiveSites);
		$_SESSION['cleanmaster']['diagnostic']['site']['site'] = $anactiveSites;
		$_SESSION['cleanmaster']['diagnostic']['site']['iblock'] = $anactiveSitesIB;
		return false;
	}
}
