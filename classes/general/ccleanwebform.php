<?php
/**
 * Copyright (c) 11/4/2020 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

class CCleanWebForm {
    
    function WebformHistoryClear($d1,$m1,$y1, $d2,$m2,$y2) { // История веб-форм, удаляем
		if(!@CModule::IncludeModule("form"))
			return false;
        $arFilter = array(
			"TIME_CREATE_1"        => "$d1-$m1-$y1 00:00:01",      // создан "с"
			"TIME_CREATE_2"        => "$d2-$m2-$y2 23:59:59",      // создан "до"
		);
		$rsForms = CForm::GetList($by="s_id", $order="desc", array(), $is_filtered); // ПЕРЕМЕННАЯ НЕПОНЯТНАЯ КАКАЯ-ТО
		while ($arForm = $rsForms->Fetch()) {
			$rsResults = CFormResult::GetList($arForm['ID'], $by="s_id", $order="desc", $arFilter, $is_filtered);
			while ($arResult = $rsResults->Fetch())	{
				CFormResult::Delete($arResult['ID']);
			}
		}
	}
	
	/*
		Получаем данные для диагностики
	*/
	public function GetDiagnosticData($step = false)
	{
		if(!@CModule::IncludeModule("form"))
			return false;
		$_SESSION['master']['diagnostic']['webform']['record'] = 0;
		CModule::IncludeModule("form");
        $arFilter = array(
			"TIME_CREATE_2"        => date("d-m-Y 23:59:59", time() - 2592000),      // создан "до"
		);
		$rsForms = CForm::GetList($by="s_id", $order="desc", array(), $is_filtered); // ПЕРЕМЕННАЯ НЕПОНЯТНАЯ КАКАЯ-ТО
		while ($arForm = $rsForms->Fetch()) {
			$rsResults = CFormResult::GetList($arForm['ID'], $by="s_id", $order="desc", $arFilter, $is_filtered);
			while ($arResult = $rsResults->Fetch())	{
				$_SESSION['master']['diagnostic']['webform']['record']++;
			}
		}
		return false;
	}
}