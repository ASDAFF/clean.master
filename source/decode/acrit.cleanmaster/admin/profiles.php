<?php
/**
 * hipot studio source file
 * User: <hipot AT ya DOT ru>
 * Date: 08.06.2017 23:25
 * @version pre 1.0
 */

require_once $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php";

global $APPLICATION, $USER, $USER_FIELD_MANAGER;

use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

$moduleId = "acrit.cleanmaster";
$phpPath = COption::GetOptionString($moduleId, "php_path", "php");

\Bitrix\Main\Loader::includeModule('acrit.cleanmaster');
$adminTableName     = "tbl_acrit_cleanmaster_profiles";
$ormDataClass       = '\\Acrit\\Cleanmaster\\ProfilesTable';

$oAdminList = new \Hipot\Admin\ListWrapper($adminTableName, 'ID', 'ASC');
$oAdminList->postGroupActions($ormDataClass);

/** @var \UploadBfileindexTable $ormDataClass */
$rsData = $ormDataClass::getList(array(
	'select'    => array('*'),
	'filter'    => array(),
	'order'     => array($by => $order),
));

$arHeaders = (array)$ormDataClass::getMap();

// custom field
$arHeaders['CRON_CMD'] = array(
	'data_type' => 'string',
	'title' => Loc::getMessage('CRON_CMD_TITLE'),
	'default' => true,
	'sort' => 1
);

$oAdminList->addHeaders($arHeaders);
$oAdminList->collectAdminResultAndNav($rsData, $ormDataClass, function (&$arFieldsTable, &$arData) use ($phpPath) {
	$arFieldsTable['CRON_CMD'] = 'html';
	$arData['CRON_CMD'] = '<b><code>'. $phpPath .' -f ' . Bitrix\Main\Application::getDocumentRoot() . '/bitrix/modules/acrit.cleanmaster/cron/profile_run.php ' . $arData['ID'] . '</code></b>';
});
$oAdminList->addAdminContextMenuAndCheckXls();

$APPLICATION->SetTitle(GetMessage("acrit_cleanmaster_PROFILES_LIST"));
require $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php";

if ($isDemo != 1) {
	echo BeginNote();
	echo GetMessage("ACRIT_CLEANMASTER_IS_DEMO_MESSAGE");
	echo '<br /><br /><a target="_blank" href="'. GetMessage('ACRIT_CLEANMASTER_IS_DEMO_MESSAGE_BUY_URL') . '">'.GetMessage("ACRIT_CLEANMASTER_IS_DEMO_MESSAGE_BTN").'</a>';
	echo EndNote();
} else {
	echo BeginNote();
	echo GetMessage("ACRIT_CLEANMASTER_CRONTAB_HELP_HTML");
	echo EndNote();

	echo BeginNote();
	echo GetMessage("ACRIT_CLEANMASTER_CRONTAB_PHP_PATH_HTML", ["#PHP_PATH#" => $phpPath]);
	echo EndNote();
}

include __DIR__ . '/../include/update_notifier/update_notifier.php';

$oAdminList->displayList();
unset($oAdminList);

require $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php";

?>