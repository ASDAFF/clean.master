<?
/**
 * @module acrit.cleanmaster
 * iblock diagmostic script
 * @version 2.9
 */
require $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php";

CModule::IncludeModule('iblock');

function GetDiagnosticIBlockData()
{
	$files = array();

	$dbRes = CIBlockElement::GetList(array('id' => 'asc'), array(), false, false, array('ID', 'PREVIEW_PICTURE', 'DETAIL_PICTURE'));
	while ($arRes = $dbRes->Fetch()) {
		if(intval($arRes['PREVIEW_PICTURE']) > 0)
			$files[] = $arRes['PREVIEW_PICTURE'];
		if(intval($arRes['DETAIL_PICTURE']) > 0)
			$files[] = $arRes['DETAIL_PICTURE'];
	}

	return $files;
}

function GetDiagnosticSectionData()
{
	$files = array();

	$dbRes = CIBlockSection::GetList(array('id' => 'asc'), array(), false, array('ID', 'PICTURE', 'DETAIL_PICTURE'));
	while ($arRes = $dbRes->GetNext()) {
		if(intval($arRes['PICTURE']) > 0)
			$files[] =  $arRes['PICTURE'];
		if(intval($arRes['DETAIL_PICTURE']) > 0)
			$files[] =  $arRes['DETAIL_PICTURE'];
	}

	return $files;
}

function GetDiagnosticPropsData()
{
	$files = array();

	$dbRes = CIBlockProperty::GetList(array('id' => 'asc'), array('PROPERTY_TYPE' => 'F'));
	$iblockList = $filter = $propertyList = array();
	while ($arRes = $dbRes->GetNext()) {
		$iblockList[]								= $arRes['IBLOCK_ID'];
		$propertyList[ $arRes['IBLOCK_ID'] ][]		= $arRes['ID'];
	}

	if (empty($propertyList)) {
		return false;
	}

	$filter['IBLOCK_ID'] = $iblockList;

	$dbElem = \CIBlockElement::GetList(array('id' => 'asc'), $filter, false, false, array('ID', 'IBLOCK_ID'));

	while ($fields = $dbElem->Fetch())
	{
		foreach ($propertyList[ $fields['IBLOCK_ID'] ] as $propId)
		{
			$prop = false;

			$db_props = CIBlockElement::GetProperty(
				$fields["IBLOCK_ID"],
				$fields['ID'],
				array("sort" => "asc"),
				array("EMPTY" => "N", 'ID' => $propId)
			);
			while ($ar_props = $db_props->GetNext()) {
				if (intval($ar_props['VALUE']) <= 0) {
					continue;
				}

				if ($ar_props['MULTIPLE'] == "Y") {
					$prop[] = $ar_props['VALUE'];
				} else {
					$prop = $ar_props['VALUE'];
				}
			}
			if ($prop !== false) {
				if (is_array($prop)) {
					foreach ($prop as $val) {
						$files[] = $val;
					}
				} else {
					if (intval($prop) > 0) {
						$files[] = $prop;
					}
				}
			}
		}
	}

	return $files;
}

$files1 = GetDiagnosticIBlockData();
$files2 = GetDiagnosticSectionData();
$files3 = GetDiagnosticPropsData();

$files = array_merge($files1, $files2, $files3);
unset($files1, $files2, $files3);

echo '<pre>';
var_dump($files);

$dbFiles = $DB->Query("SELECT ID,MODULE_ID,SUBDIR FROM b_file WHERE SUBDIR LIKE 'iblock/%' AND `ID` NOT IN ("
	.implode(',', $files)
	.")");


var_dump($dbFiles->SelectedRowsCount());
while ($arFile = $dbFiles->Fetch()) {
	//CFile::Delete($arFile['ID']);
	var_dump($arFile);
}


?>