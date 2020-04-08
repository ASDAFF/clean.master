<?php


class CCleanIBlock
{
    public function GetAllIblock()
    {
		$res = CIBlock::GetList(Array('name' => 'asc'), Array(), false);
		while($ar_res = $res->Fetch()){
			$ibCnt = CIBlockElement::GetList(array('id' => 'asc'), array('IBLOCK_ID' => $ar_res['ID'], 'ACTIVE' => 'N'), array());
		    $ar_res['INACTIVE_CNT'] = $ibCnt;
			if($ibCnt <= 0)
				continue;
			$iblock_list[] = $ar_res;
		}
		return $iblock_list;
	}

	/**
	 * Удаление неактивных элементов инфоблока
	 * @param $ibs_test
	 * @return bool
	 */
	public function InactiveIBlockDelete($ibs_test)
	{
		$param = true;

		$iblocks = CIBlockElement::GetList(Array("SORT"=>"ASC"), Array("IBLOCK_ID" => $ibs_test, "ACTIVE"=>"N"));
		while ($iblock = $iblocks->Fetch()){
			if (!CIBlockElement::Delete($iblock['ID'])) {
				$param = false;
			}
		}
		return $param;
	}
	
	/*
		Получаем данные для диагностики
	*/
	public function GetDiagnosticData($step = false)
	{
		$iblocks = $this->GetAllIblock();
		if(is_array($iblocks))
		{
			foreach($iblocks as $iblock)
			{
				$dbElem = CIBlockElement::GetList(Array("SORT"=>"ASC"), Array("IBLOCK_ID" => $iblock['ID'], "ACTIVE"=>"N"), false, false, array('ID', 'NAME'));
				while($arElem = $dbElem->GetNext())
				{
					$_SESSION['cleanmaster']['diagnostic']['ibelement']['iblock']["{$iblock['NAME']} [{$iblock['ID']}]"][] = "{$arElem['NAME']} [{$arElem['ID']}]";
				}
			}
		}
		return false;
	}
}
