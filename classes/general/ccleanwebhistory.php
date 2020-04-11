<?php
/**
 * Copyright (c) 11/4/2020 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

class CCleanWebHistory extends TCleanMasterFunctions {
    
    function WebStatHistoryDelete($d1,$m1,$y1) {
		if (CModule::IncludeModule('statistic')){
			global $DB;
			$new_date = $DB->FormatDate("$d1.$m1.$y1", "DD.MM.YYYY", FORMAT_DATE);

			$res = CStatistics::CleanUp($new_date, $errors);
			if ($res){
				return $res;
			}
			else{
				return $errors;
			}
		}
	}
	/*
		Получаем данные для диагностики
	*/
	public function GetDiagnosticData($step = false)
	{
		if (!@CModule::IncludeModule('statistic'))
			return false;
		global $DB;
		$_SESSION['master']['diagnostic']['webhist']['size'] = 0;
		$_SESSION['master']['diagnostic']['webhist']['record'] = 0;
		$arDBSize = $this->GetDBSize();
		$cleanup_date = time() - 2592000;
		$arrTables = array(
			"b_stat_adv_guest"        => "DATE_GUEST_HIT",
			"b_stat_adv_guest"        => "DATE_HOST_HIT",
			"b_stat_adv_day"        => "DATE_STAT",
			"b_stat_adv_event_day"    => "DATE_STAT",
			"b_stat_day"            => "DATE_STAT",
			"b_stat_day_site"        => "DATE_STAT",
			"b_stat_event_day"        => "DATE_STAT",
			"b_stat_event_list"        => "DATE_ENTER",
			"b_stat_guest"            => "LAST_DATE",
			"b_stat_hit"            => "DATE_HIT",
			"b_stat_searcher_hit"    => "DATE_HIT",
			"b_stat_phrase_list"    => "DATE_HIT",
			"b_stat_referer"        => "DATE_LAST",
			"b_stat_referer_list"    => "DATE_HIT",
			"b_stat_searcher_day"    => "DATE_STAT",
			"b_stat_session"        => "DATE_LAST",
			"b_stat_page"            => "DATE_STAT",
			"b_stat_country_day"    => "DATE_STAT",
			"b_stat_path"            => "DATE_STAT"
			);
		reset($arrTables);
		while (list($table_name, $date_name) = each($arrTables))
		{
			$strSql = "SELECT COUNT(ID) FROM $table_name WHERE $date_name<FROM_UNIXTIME('$cleanup_date')";
            $arCnt = $DB->Query($strSql, false, $err_mess.__LINE__)->Fetch();
			$_SESSION['master']['diagnostic']['webhist']['tables'][$table_name]['record'] = intval($arCnt['COUNT(ID)']);
			if($arDBSize[$table_name]['table_rows'] <= 0)
				$_SESSION['master']['diagnostic']['webhist']['tables'][$table_name]['size'] = 0;
			else
				$_SESSION['master']['diagnostic']['webhist']['tables'][$table_name]['size'] = $arDBSize[$table_name]['total_size_mb'] / $arDBSize[$table_name]['table_rows'] * $arCnt['COUNT(ID)'];
			$_SESSION['master']['diagnostic']['webhist']['size'] += $_SESSION['master']['diagnostic']['webhist']['tables'][$table_name]['size'];
			$_SESSION['master']['diagnostic']['webhist']['record'] += $_SESSION['master']['diagnostic']['webhist']['tables'][$table_name]['record'];
		}
		return false;
	}
}
