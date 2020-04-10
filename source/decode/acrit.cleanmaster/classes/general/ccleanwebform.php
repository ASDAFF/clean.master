<?php


class CCleanWebForm {
    
    function WebformHistoryClear($d1,$m1,$y1, $d2,$m2,$y2) { // Èñòîðèÿ âåá-ôîðì, óäàëÿåì
		if(!@CModule::IncludeModule("form"))
			return false;
        $arFilter = array(
			"TIME_CREATE_1"        => "$d1-$m1-$y1 00:00:01",      // ñîçäàí "ñ"
			"TIME_CREATE_2"        => "$d2-$m2-$y2 23:59:59",      // ñîçäàí "äî"
		);
		$rsForms = CForm::GetList($by="s_id", $order="desc", array(), $is_filtered); // ÏÅÐÅÌÅÍÍÀß ÍÅÏÎÍßÒÍÀß ÊÀÊÀß-ÒÎ
		while ($arForm = $rsForms->Fetch()) {
			$rsResults = CFormResult::GetList($arForm['ID'], $by="s_id", $order="desc", $arFilter, $is_filtered);
			while ($arResult = $rsResults->Fetch())	{
				CFormResult::Delete($arResult['ID']);
			}
		}
	}
	
	/*
		Ïîëó÷àåì äàííûå äëÿ äèàãíîñòèêè
	*/
	public function GetDiagnosticData($step = false)
	{
		if(!@CModule::IncludeModule("form"))
			return false;
		$_SESSION['cleanmaster']['diagnostic']['webform']['record'] = 0;
		CModule::IncludeModule("form");
        $arFilter = array(
			"TIME_CREATE_2"        => date("d-m-Y 23:59:59", time() - 2592000),      // ñîçäàí "äî"
		);
		$rsForms = CForm::GetList($by="s_id", $order="desc", array(), $is_filtered); // ÏÅÐÅÌÅÍÍÀß ÍÅÏÎÍßÒÍÀß ÊÀÊÀß-ÒÎ
		while ($arForm = $rsForms->Fetch()) {
			$rsResults = CFormResult::GetList($arForm['ID'], $by="s_id", $order="desc", $arFilter, $is_filtered);
			while ($arResult = $rsResults->Fetch())	{
				$_SESSION['cleanmaster']['diagnostic']['webform']['record']++;
			}
		}
		return false;
	}
}