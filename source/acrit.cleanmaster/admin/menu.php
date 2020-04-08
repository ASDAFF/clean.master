<?php
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
IncludeModuleLangFile(__FILE__);
?><?
if ($APPLICATION->GetGroupRight("acrit.cleanmaster") != "D"){
	$aMenu = array(
		"parent_menu" => "global_menu_acrit", //"global_menu_services",
		"section" => GetMessage("ACRIT_EXPORTPRO_SECTION"),
		"sort" => 100,
		"text" => GetMessage("ACRIT_CLEANMASTER_OCISTKA_SAYTA"),
		"title" => GetMessage("ACRIT_CLEANMASTER_OCISTKA_SAYTA"),
		"url" => "/bitrix/admin/settings.php?lang=ru&mid=acrit.cleanmaster&mid_menu=1",
		"icon" => "iblock_menu_icon_settings",
		"page_icon" => "",
		"items_id" => "menu_acrit.cleanmaster",
        "module_id" => "acrit.cleanmaster",
        "items" => array(
        )
	);
	return $aMenu;
}
return false;
?>