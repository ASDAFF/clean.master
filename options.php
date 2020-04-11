<?
/**
 * Copyright (c) 11/4/2020 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

IncludeModuleLangFile(__FILE__);
CModule::IncludeModule("iblock");
CModule::IncludeModule("form");
CModule::IncludeModule("subscribe");
CModule::IncludeModule("statistic");
CModule::IncludeModule("sale");
CModule::IncludeModule("clean.master");

global $DB, $isDemo;

if ($isDemo != 1) {
	echo BeginNote();
	echo GetMessage("CLEAN_MASTER_IS_DEMO_MESSAGE");
	echo '<br /><br /><input type="button" value="'.GetMessage("CLEAN_MASTER_IS_DEMO_MESSAGE_BTN").'" onclick="location.href = \''.GetMessage('CLEAN_MASTER_IS_DEMO_MESSAGE_BUY_URL').'\'">';
	echo EndNote();
}

if(intval($_GET['step']) <= 0) {
	echo BeginNote();
	echo GetMessage('CLEAN_MASTER_FILESYS_WARNING');

	echo '<br/><h1 style="color:red;">'.GetMessage("CLEAN_MASTER_PERED_NACALOM_OCISTK").'</h1>';
	echo '<a href="/bitrix/admin/dump.php" style="font-size:24px;text-decoration:underline;">'.GetMessage("CLEAN_MASTER_REZERVNOE_KOPIROVANI").'</a><br/>';

	echo EndNote();
}

require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.basename(dirname(__FILE__)).'/steps.php');

?>