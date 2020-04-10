<?php
namespace Acrit\Cleanmaster\Steps;

use \Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

class Step28 extends BaseStep
{
	protected $tableList;

	public function __construct()
	{
		$this->tableList = array(
			array('name' => 'b_perf_sql', 'def_interface_checked' => 'Y', 'description' => Loc::getMessage('acrit.cleanmaster.STEP28_b_perf_sql')),
			array('name' => 'b_perf_sql_backtrace', 'def_interface_checked' => 'Y', 'description' => Loc::getMessage('acrit.cleanmaster.STEP28_b_perf_sql_backtrace')),
			array('name' => 'b_event', 'def_interface_checked' => 'N', 'description' => Loc::getMessage('acrit.cleanmaster.STEP28_b_event')),
			array('name' => 'b_stat_hit', 'def_interface_checked' => 'N', 'description' => Loc::getMessage('acrit.cleanmaster.STEP28_b_stat_hit')),
			array('name' => 'b_stat_path_cache', 'def_interface_checked' => 'N', 'description' => Loc::getMessage('acrit.cleanmaster.STEP28_b_stat_path_cache')),
			array('name' => 'b_xml_tree', 'def_interface_checked' => 'Y', 'description' => Loc::getMessage('acrit.cleanmaster.STEP28_b_xml_tree')),
			array('name' => 'b_sec_session', 'def_interface_checked' => 'N', 'description' => Loc::getMessage('acrit.cleanmaster.STEP28_b_sec_session'))
		);
	}

	public function getTableSizes()
	{
		$tableSizes = $this->GetDBSize();
		foreach ($this->tableList as &$table) {
			$table = array_merge((array)$table, (array)$tableSizes[ $table['name'] ]);
			unset($tableSizes[ $tableSizes['name'] ]);
		}
	}

	public function getTableList()
	{
		$this->getTableSizes();
		return $this->tableList;
	}
}