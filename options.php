<?
IncludeModuleLangFile(__FILE__);
CModule::IncludeModule("iblock");
CModule::IncludeModule("form");
CModule::IncludeModule("subscribe");
CModule::IncludeModule("statistic");
CModule::IncludeModule("sale");
CModule::IncludeModule("acrit.cleanmaster");

global $DB, $isDemo;

if ($isDemo != 1) {
	echo BeginNote();
	echo GetMessage("ACRIT_CLEANMASTER_IS_DEMO_MESSAGE");
	echo '<br /><br /><input type="button" value="'.GetMessage("ACRIT_CLEANMASTER_IS_DEMO_MESSAGE_BTN").'" onclick="location.href = \''.GetMessage('ACRIT_CLEANMASTER_IS_DEMO_MESSAGE_BUY_URL').'\'">';
	echo EndNote();
}

if(intval($_GET['step']) <= 0) {
	echo BeginNote();
	echo GetMessage('ACRIT_CLEANMASTER_FILESYS_WARNING');

	echo '<br/><h1 style="color:red;">'.GetMessage("ACRIT_CLEANMASTER_PERED_NACALOM_OCISTK").'</h1>';
	echo '<a href="/bitrix/admin/dump.php" style="font-size:24px;text-decoration:underline;">'.GetMessage("ACRIT_CLEANMASTER_REZERVNOE_KOPIROVANI").'</a><br/>';

	echo EndNote();
}

require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.basename(dirname(__FILE__)).'/steps.php');

?>