<?
/**
 * Copyright (c) 11/4/2020 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

class CCleanSaleViewed extends TCleanMasterFunctions {
    private $documentRoot;
    
    public function __construct()
    {
        $this->documentRoot = Bitrix\Main\Application::getDocumentRoot();
    }

    // TODO сделать пошаговую очистку
    public function Clear()
    {
		if(!Cmodule::IncludeModule('sale'))
			return;
        global $DB;
        $viewed_time = COption::GetOptionString("sale", "viewed_time", "90");
        $viewed_time = IntVal($viewed_time);
		$strSql =
            "DELETE ".
            "FROM b_sale_viewed_product ".
            "WHERE TO_DAYS(DATE_VISIT) < (TO_DAYS(NOW()) - ".$viewed_time.")";
        $db_res = $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__);
    }

    /*
		Получаем данные для диагностики
	*/
	public function GetDiagnosticData($step = false)
	{
		if(!Cmodule::IncludeModule('sale'))
			return false;
		global $DB;
        $viewed_time = COption::GetOptionString("sale", "viewed_time", "90");
        $viewed_time = IntVal($viewed_time);
        $strSql =
            "SELECT COUNT(ID) ".
            "FROM b_sale_viewed_product ".
            "WHERE TO_DAYS(DATE_VISIT) < (TO_DAYS(NOW()) - ".$viewed_time.")";
        $arCnt = $DB->Query($strSql, false, "File: ".__FILE__."<br>Line: ".__LINE__)->fetch();
        $arDBSize = $this->GetDBSize();
        if($arDBSize['b_sale_viewed_product']['table_rows'] <= 0)
            $_SESSION['master']['diagnostic']['saleviewed']['size'] = 0;
		else
            $_SESSION['master']['diagnostic']['saleviewed']['size'] = $arDBSize['b_sale_viewed_product']['total_size_mb'] / $arDBSize['b_sale_viewed_product']['table_rows'] * $arCnt['COUNT(ID)'];
		$_SESSION['master']['diagnostic']['saleviewed']['record'] = $arCnt['COUNT(ID)'];
		return false;
	}
}
?>