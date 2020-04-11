<?
/**
 * Copyright (c) 11/4/2020 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

class CCleanPerfmon extends TCleanMasterFunctions {
    private $documentRoot;
    
    public function __construct()
    {
        $this->documentRoot = Bitrix\Main\Application::getDocumentRoot();
    }
    
    public function Clear()
    {
        if(!Cmodule::IncludeModule('perfmon'))
			return;
        CPerfomanceError::Clear();
        CPerfomanceHit::Clear();
        CPerfomanceIndexSuggest::Clear();
        CPerfomanceComponent::Clear();
        CPerfomanceCache::Clear();
        CPerfomanceSQL::Clear();
    }
    
    /*
		Получаем данные для диагностики
	*/
	public function GetDiagnosticData($step = false)
	{
		if(!Cmodule::IncludeModule('perfmon'))
			return false;
		global $DB;
		$_SESSION['master']['diagnostic']['perfmon']['size'] = 0;
		$arrTables = array(
			"b_perf_error",
			"b_perf_hit",
			"b_perf_tab_stat",
			"b_perf_tab_column_stat",
			"b_perf_index_suggest",
			"b_perf_index_suggest_sql",
			"b_perf_component",
			"b_perf_cache",
			"b_perf_sql_backtrace",
			"b_perf_sql",
			);
        $arDBSize = $this->GetDBSize();
		foreach ($arrTables as $table_name)
		{
			$_SESSION['master']['diagnostic']['perfmon']['table'][$table_name] = $arDBSize[$table_name];
			$_SESSION['master']['diagnostic']['perfmon']['size'] += $arDBSize[$table_name]['total_size_mb'];
			$_SESSION['master']['diagnostic']['perfmon']['record'] += $arDBSize[$table_name]['table_rows'];
		}
		return false;
	}
}

?>