<?php
/**
 * Copyright (c) 11/4/2020 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

class CCleanOrderStat {
    
    function StatOrderDelete($d1,$m1,$y1, $d2,$m2,$y2, $unpayed = false) {
		if(!@CModule::IncludeModule('sale'))
			return;
		$arFilter = Array(
			"PAYED" => "N",
			">=DATE_UPDATE" => "$d1.$m1.$y1 00:00:01",
			"<=DATE_UPDATE" => "$d2.$m2.$y2 23:59:59",
		);
		$arUnpayed = array();
		if ($unpayed){
			$arUnpayed = array("PAYED" => "Y");
		}
		$arFilter = array_merge($arFilter, $arUnpayed);

		$rsSales = CSaleOrder::GetList(array("DATE_INSERT" => "ASC"), $arFilter);
		while ($arSales = $rsSales->Fetch()) {
			CSaleOrder::Delete($arSales['ID']);
		}
	}
	
	/*
		Получаем данные для диагностики
	*/
	public function GetDiagnosticData($step = false)
	{
		if(!@CModule::IncludeModule('sale'))
			return false;
		$_SESSION['master']['diagnostic']['orderstat']['order'] = 0;
		$arFilter = Array(
			"PAYED" => "N",
			"<=DATE_UPDATE" => date("d.m.Y 23:59:59", time() - 2592000),
		);
		$arUnpayed = array();
		if ($unpayed){
			$arUnpayed = array("PAYED" => "Y");
		}
		$arFilter = array_merge($arFilter, $arUnpayed);
		$rsSales = CSaleOrder::GetList(array("DATE_INSERT" => "ASC"), $arFilter);
		while ($arSales = $rsSales->Fetch()) {
			$_SESSION['master']['diagnostic']['orderstat']['order']++;
		}
		return false;
	}
}
