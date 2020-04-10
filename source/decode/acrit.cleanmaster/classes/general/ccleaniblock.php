<?php


class CCleanIBlock
{
	/**
	 * удалять элементов за шаг
	 */
	const IBLOCK_ELEMENTS_PER_STEP = 50;

	protected $baseFilter = array();

	/**
	 * @param bool $baseFilter = array("ACTIVE" => "N")
	 */
	public function __construct($baseFilter = false)
	{
		if ($baseFilter === false) {
			$this->baseFilter = array("ACTIVE" => "N");
		}
	}

	public function GetStepCount()
	{
		return self::IBLOCK_ELEMENTS_PER_STEP;
	}

	/**
	 * получить кол-во неактивных элементов
	 * @param array $ibs_test
	 * @return integer
	 */
	public function GetCount($ibs_test)
	{
		$f = array("IBLOCK_ID" => $ibs_test);
		foreach ($this->baseFilter as $k => $v) {
			$f[$k] = $v;
		}

		$cnt = \CIBlockElement::GetList(array("id"=>"ASC"), $f, array());
		return $cnt;
	}

    public function GetAllIblock()
    {
		$res = \CIBlock::GetList(Array('name' => 'asc'), Array(), false);
		while($ar_res = $res->Fetch()){
			$f = array("IBLOCK_ID" => $ar_res['ID']);
			foreach ($this->baseFilter as $k => $v) {
				$f[$k] = $v;
			}

			$ibCnt = \CIBlockElement::GetList(array('id' => 'asc'), $f, array());
		    $ar_res['INACTIVE_CNT'] = $ibCnt;
			if($ibCnt <= 0)
				continue;
			$iblock_list[] = $ar_res;
		}
		return $iblock_list;
	}

	/**
	 * Удаление неактивных элементов инфоблока
	 * @param array $ibs_test
	 * @param bool|int $step
	 * @return bool
	 */
	public function InactiveIBlockDelete($ibs_test, $step = false)
	{
		if ((int)$step > 0) {
			//$navParams = array("iNumPage" => $step, "nPageSize" => self::IBLOCK_ELEMENTS_PER_STEP);
			$navParams = array("nTopCount" => self::IBLOCK_ELEMENTS_PER_STEP);
		} else {
			$navParams = false;
		}

		$f = array("IBLOCK_ID" => $ibs_test);
		foreach ($this->baseFilter as $k => $v) {
			$f[$k] = $v;
		}

		$param = true;
		$iblocks = \CIBlockElement::GetList(Array("id"=>"ASC"), $f, false, $navParams, array('ID'));
		while ($iblock = $iblocks->Fetch()){
			if (! CIBlockElement::Delete($iblock['ID'])) {
				$param = false;
			}
		}
		return $param;
	}

	/**
	 * Получаем данные для диагностики
	 * @param bool|int $step игнорируется
	 * @return bool
	 */
	public function GetDiagnosticData($step = false)
	{
		$iblocks = $this->GetAllIblock();
		if (is_array($iblocks)) {
			foreach ($iblocks as $iblock) {
				$cntElem = $this->GetCount($iblock['ID']);
				$_SESSION['cleanmaster']['diagnostic']['ibelement']['iblock']["{$iblock['NAME']} [{$iblock['ID']}]"][]
					= $cntElem . ' ' . GetMessage('ACRIT_CM_PIECES');
			}
		}
		return false;
	}
}
