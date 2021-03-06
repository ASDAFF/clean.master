<?php
/**
 * Copyright (c) 11/4/2020 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

class CCleanDropedBasket
{
	
	function DropedBasketDelete($d1,$m1,$y1, $d2,$m2,$y2)
	{
		$arFilter = Array(
			">=DATE_UPDATE" => "$d1.$m1.$y1 00:00:01",
			"<=DATE_UPDATE" => "$d2.$m2.$y2 23:59:59",
			"ORDER_ID"		=> 'NULL'
		);

		$basket = array();
		$dbBasketItems = CSaleBasket::GetList(array("NAME" => "ASC", "ID" => "ASC"), $arFilter, false,false, array("ID"));
		while ($arItems = $dbBasketItems->Fetch()){
			CSaleBasket::Delete($arItems['ID']);
			$basket[] = $arItems;
		}
	}
	
	/**
	 * Получаем данные для диагностики
	 *
	 * @param string $step
	 * @return boolean
	 */
	public function GetDiagnosticData($step = false)
	{
		$_SESSION['master']['diagnostic']['dropbasket']['basket'] = 0;
		$arFilter = Array(
			"<=DATE_UPDATE" => date("d.m.Y 23:59:59", time() - 2592000),
			"ORDER_ID"		=> 'NULL'
		);

		$basket = array();
		$dbBasketItems = CSaleBasket::GetList(array("NAME" => "ASC", "ID" => "ASC"), $arFilter, false,false, array("ID"));
		while ($arItems = $dbBasketItems->Fetch()){
			$_SESSION['master']['diagnostic']['dropbasket']['basket']++;
		}
		return false;
	}
}
