<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php";
IncludeModuleLangFile(__FILE__);

$MODULE_ID = "acrit.cleanmaster";

if ($APPLICATION->GetGroupRight($MODULE_ID) != "D") {
	\CModule::IncludeModule($MODULE_ID);

	$itemsWSubs = array(
		array(
			"text" => GetMessage("ACRIT_CLEANMASTER_MENU_MAIN_ACTIONS"),
			"url" => "/bitrix/admin/settings.php?clear_menu=Y&lang=ru&mid=acrit.cleanmaster&mid_menu=1",
			"more_url" => array(),
			"title" => GetMessage("ACRIT_CLEANMASTER_MENU_MAIN_ACTIONS"),
			"icon" => "form_menu_icon",
			"items_id" => "menu_acrit_cleanmaster_MAIN_ACTIONS",
		),
		array(
			"text" => GetMessage("ACRIT_CLEANMASTER_MENU_ADDONS_ACTIONS"),
			"url" => "/bitrix/admin/settings.php?dop_tools=Y&lang=ru&mid=acrit.cleanmaster&mid_menu=1",
			"more_url" => array(),
			"title" => GetMessage("ACRIT_CLEANMASTER_MENU_ADDONS_ACTIONS"),
			"icon" => "form_menu_icon",
			"items_id" => "menu_acrit_cleanmaster_ADDONS_ACTIONS",
		),
	);

	if (class_exists('CCleanMain')) {
		foreach ($itemsWSubs as &$item) {
			$items = array();
			switch ($item['items_id']) {
				case 'menu_acrit_cleanmaster_MAIN_ACTIONS': {
					$steps = CCleanMain::getMainSteps();
					break;
				}
				case 'menu_acrit_cleanmaster_ADDONS_ACTIONS': {
					$steps = CCleanMain::getToolsSteps();
					break;
				}
			}
			foreach ($steps as $stepId => $step) {
				$url = $item['url'] . '&show_steps=' . $stepId . '&action_start=Y';
				$items[] = array(
					"text" => $step,
					"url" => $url,
					"more_url" => array($url, $item['url'] . '&show_steps=SELECT' . $stepId . '&action_start=Y'),
					"title" => $step,
				);
			}

			$item['items'] = $items;
		}
		unset($item, $items);
	}

	$itemsWSubs[] = array(
		"text" => GetMessage("ACRIT_CLEANMASTER_MENU_PROFILES"),
		"url" => "/bitrix/admin/settings.php?profile_table=Y&lang=ru&mid=acrit.cleanmaster&mid_menu=1",
		"more_url" => array(),
		"title" => GetMessage("ACRIT_CLEANMASTER_MENU_PROFILES"),
		"icon" => false, //"form_menu_icon",
		"items_id" => "menu_acrit_cleanmaster_MENU_PROFILES",
	);

	$itemsWSubs[] = [
		"text" => GetMessage("ACRIT_CLEANMASTER_MENU_SUPPORT"),
		"url" => str_replace('.', '_', $MODULE_ID) . "_support.php?lang=" . LANGUAGE_ID,
		"more_url" => array(),
		"title" => GetMessage("ACRIT_CLEANMASTER_MENU_SUPPORT")
	];

	$aMenu = array(
		"parent_menu" => "global_menu_acrit",
		"section" => "acrit.cleanmaster",
		"sort" => 100,
		"text" => GetMessage("ACRIT_CLEANMASTER_OCISTKA_SAYTA"),
		"title" => GetMessage("ACRIT_CLEANMASTER_OCISTKA_SAYTA"),
		"url" => "/bitrix/admin/settings.php?lang=ru&mid=acrit.cleanmaster&mid_menu=1",
		"icon" => "util_menu_icon",
		"page_icon" => "",
		"items_id" => "menu_acrit_cleanmaster",
		"module_id" => "acrit.cleanmaster",
		"items" => $itemsWSubs
	);

	return $aMenu;
}

return false;
?>