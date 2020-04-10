<?php

class CCleanAttackEvent extends TCleanMasterFunctions
{
    function AtackEventsDelete()
    {
		if(!@CModule::IncludeModule('statistic'))
			return;
		$arFilter = Array(
			'TIMESTAMP_X_1' => '',
			'TIMESTAMP_X_2' => '',
			'SEVERITY' => '',
			'=AUDIT_TYPE_ID' => Array(
				'SECURITY_VIRUS',
				'SECURITY_FILTER_SQL',
				'SECURITY_FILTER_XSS',
				'SECURITY_FILTER_XSS2',
				'SECURITY_FILTER_PHP',
				'SECURITY_REDIRECT',
			),
			'MODULE_ID' => '',
			'ITEM_ID' => '',
			'SITE_ID' => '',
			'USER_ID' => '',
			'GUEST_ID' => '',
			'REMOTE_ADDR' => '',
			'REQUEST_URI' => '',
			'USER_AGENT' => '',
		);
		$rsData = CEventLog::GetList(array($by => $order), $arFilter, $arNavParams);
		while($ob = $rsData->Fetch()){
			$list[] = $ob['ID'];
		}

		if (count($list) > 0){
			$sql = "DELETE FROM `b_event_log` WHERE id IN (".implode(',',$list).");";
			global $DB;
			$DB->Query($sql);
		}
	}
	
	/*
		�������� ������ ��� �����������
	*/
	public function GetDiagnosticData($step = false)
	{
		if(!@CModule::IncludeModule('statistic'))
			return false;
		$arDBSize = $this->GetDBSize();
		$_SESSION['cleanmaster']['diagnostic']['attackevent']['record'] = 0;
		$arFilter = Array(
			'TIMESTAMP_X_1' => '',
			'TIMESTAMP_X_2' => '',
			'SEVERITY' => '',
			'=AUDIT_TYPE_ID' => Array(
				'SECURITY_VIRUS',
				'SECURITY_FILTER_SQL',
				'SECURITY_FILTER_XSS',
				'SECURITY_FILTER_XSS2',
				'SECURITY_FILTER_PHP',
				'SECURITY_REDIRECT',
			),
			'MODULE_ID' => '',
			'ITEM_ID' => '',
			'SITE_ID' => '',
			'USER_ID' => '',
			'GUEST_ID' => '',
			'REMOTE_ADDR' => '',
			'REQUEST_URI' => '',
			'USER_AGENT' => '',
		);
		$rsData = CEventLog::GetList(array($by => $order), $arFilter, false);
		while($ob = $rsData->Fetch()){
			$_SESSION['cleanmaster']['diagnostic']['attackevent']['record']++;
		}
		if($arDBSize['b_event_log']['table_rows'] <= 0)
			$_SESSION['cleanmaster']['diagnostic']['attackevent']['size'] = 0;
		else
			$_SESSION['cleanmaster']['diagnostic']['attackevent']['size'] = $arDBSize['b_event_log']['total_size_mb'] / $arDBSize['b_event_log']['table_rows'] * $_SESSION['cleanmaster']['diagnostic']['attackevent']['record'];
		
		return false;
	}
}
