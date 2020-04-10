<?php


class CCleanSubscribe extends TCleanMasterFunctions
{
	/**
	 * Удаляем историю рассылок
	 *
	 * @param $d1
	 * @param $m1
	 * @param $y1
	 * @param $d2
	 * @param $m2
	 * @param $y2
	 */
	public function SubscribeHistoryClear($d1,$m1,$y1, $d2,$m2,$y2)
	{
		global $DB;
		$from = strtotime($d1.'-'.$m1.'-'.$y1);
		$to = strtotime($d2.'-'.$m2.'-'.$y2);
		$DB->Query("DELETE FROM `b_event` WHERE `DATE_EXEC`<'".date("Y-m-d 23:59:59",$to)."' AND `DATE_EXEC`>'".date("Y-m-d 23:59:59",$from)."'");
	}

	public function UnconfirmedSubscriptionDelete()
    {
        Cmodule::IncludeModule('subscribe');
		$subscr = CSubscription::GetList(array("ID"=>"ASC"), array("CONFIRMED"=>"N"));
		while(($subscr_arr = $subscr->Fetch())){
			$res = CSubscription::Delete($subscr_arr['ID']);
		}
	}
	
	public function DeleteRubric($y1,$m1,$d1, $y2,$m2,$d2)
	{
		global $DB;
		$strSql = "DELETE FROM `b_list_rubric` WHERE `LAST_EXECUTED` < '".$y2."-".$m2."-".$d2." 23:59:59' AND `LAST_EXECUTED` > '".$y1."-".$m1."-".$d1." 00:00:00'";
		$res = $DB->Query($strSql, false, $err_mess.__LINE__);
	}
	
	/*
		Получаем данные для диагностики
	*/
	public function GetDiagnosticDataUnconfirmed($step = false)
	{
		$_SESSION['cleanmaster']['diagnostic']['unconfirmed']['record'] = 0;
		if(!@Cmodule::IncludeModule('subscribe'))
			return false;
		$subscr = CSubscription::GetList(array("ID"=>"ASC"), array("CONFIRMED"=>"N"));
		while(($subscr_arr = $subscr->Fetch())){
			$_SESSION['cleanmaster']['diagnostic']['unconfirmed']['record']++;
		}
		return false;
	}
	public function GetDiagnosticDataHistory($step = false)
	{
		global $DB;
		$arDBSize = $this->GetDBSize();
		$arHist = $DB->Query("SELECT COUNT(ID) FROM `b_event` WHERE `DATE_EXEC`<'".date("Y-m-d", time() - 2592000)."'")->fetch();
		$_SESSION['cleanmaster']['diagnostic']['stathistory']['record'] = $arHist['COUNT(ID)'];
		if($arDBSize['b_event']['table_rows'] <= 0)
			$_SESSION['cleanmaster']['diagnostic']['stathistory']['size'] = 0;
		else
			$_SESSION['cleanmaster']['diagnostic']['stathistory']['size'] = $arDBSize['b_event']['total_size_mb'] / $arDBSize['b_event']['table_rows'] * $arHist['COUNT(ID)'];
		return false;
	}
	public function GetDiagnosticDataRubric($step = false)
	{
		if(!@Cmodule::IncludeModule('subscribe'))
			return false;
		global $DB;
		$arDBSize = $this->GetDBSize();
		$arHist = $DB->Query("SELECT COUNT(ID) FROM `b_list_rubric` WHERE `LAST_EXECUTED`<'".date("Y-m-d", time() - 2592000)."'")->fetch();
		$_SESSION['cleanmaster']['diagnostic']['rubric']['record'] = $arHist['COUNT(ID)'];
		if($arDBSize['b_list_rubric']['table_rows'] <= 0)
			$_SESSION['cleanmaster']['diagnostic']['rubric']['size'] = 0;
		else
			$_SESSION['cleanmaster']['diagnostic']['rubric']['size'] = $arDBSize['b_list_rubric']['total_size_mb'] / $arDBSize['b_list_rubric']['table_rows'] * $arHist['COUNT(ID)'];
		return false;
	}
}
