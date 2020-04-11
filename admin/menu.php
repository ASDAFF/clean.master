<?php
/**
 * Copyright (c) 11/4/2020 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
IncludeModuleLangFile(__FILE__);
?><?
if ($APPLICATION->GetGroupRight("clean.master") != "D"){
	$aMenu = array(
		"parent_menu" => "global_menu_clean", //"global_menu_services",
		"section" => GetMessage("CLEAN_EXPORTPRO_SECTION"),
		"sort" => 100,
		"text" => GetMessage("CLEAN_MASTER_OCISTKA_SAYTA"),
		"title" => GetMessage("CLEAN_MASTER_OCISTKA_SAYTA"),
		"url" => "/bitrix/admin/settings.php?lang=ru&mid=clean.master&mid_menu=1",
		"icon" => "iblock_menu_icon_settings",
		"page_icon" => "",
		"items_id" => "menu_clean.master",
        "module_id" => "clean.master",
        "items" => array(
        )
	);
	return $aMenu;
}
return false;
?>