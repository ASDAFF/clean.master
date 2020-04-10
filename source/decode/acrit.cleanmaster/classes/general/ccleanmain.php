<?
IncludeModuleLangFile(__FILE__);
IncludeModuleLangFile(__DIR__ . '/../../steps.php');

if (! class_exists('CUpdateClientPartner')) {
	require_once $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/classes/general/update_client_partner.php";
}

/**
 * Class CCleanMain для выполнения общих действий (а не главного модуля ;)
 */
class CCleanMain extends TCleanMasterFunctions
{
	const MODULE_ID = 'acrit.cleanmaster';

	public static function includeAllNeedModules()
	{
		CModule::IncludeModule("iblock");
		CModule::IncludeModule("form");
		CModule::IncludeModule("forum");
		CModule::IncludeModule("subscribe");
		CModule::IncludeModule("statistic");
		CModule::IncludeModule("sale");
	}

	public static function getMainSteps()
	{
		$actionList = array(
			1 => GetMessage("CLEANMASTER_ACTION_1"),
			2 => GetMessage("CLEANMASTER_ACTION_2"),
			3 => GetMessage("CLEANMASTER_ACTION_3"),
			4 => GetMessage("CLEANMASTER_ACTION_4"),
			5 => GetMessage("CLEANMASTER_ACTION_5"),
			6 => GetMessage("CLEANMASTER_ACTION_6"),
			7 => GetMessage("CLEANMASTER_ACTION_7"),
			8 => GetMessage("CLEANMASTER_ACTION_8"),
			9 => GetMessage("CLEANMASTER_ACTION_9"),
			10 => GetMessage("CLEANMASTER_ACTION_10"),
			11 => GetMessage("CLEANMASTER_ACTION_11"),
			12 => GetMessage("CLEANMASTER_ACTION_12"),
			13 => GetMessage("CLEANMASTER_ACTION_13"),
			14 => GetMessage("CLEANMASTER_ACTION_14"),
			16 => GetMessage("CLEANMASTER_ACTION_16"),
			17 => GetMessage("CLEANMASTER_ACTION_17"),
			20 => GetMessage("CLEANMASTER_ACTION_20"),
			21 => GetMessage("CLEANMASTER_ACTION_21"),
			22 => GetMessage("CLEANMASTER_ACTION_22"),
			23 => GetMessage("CLEANMASTER_ACTION_23"),
			28 => GetMessage("CLEANMASTER_ACTION_28"),
		);
		return $actionList;
	}

	public static function getToolsSteps()
	{
		$actionList = array(
			24 => GetMessage("CLEANMASTER_ACTION_24"),
			25 => GetMessage("CLEANMASTER_ACTION_25"),
			26 => GetMessage("CLEANMASTER_ACTION_26"),
			27 => GetMessage("CLEANMASTER_ACTION_27"),
		);
		return $actionList;
	}

	public static function getDiagnosticStepCodesByStep($step)
	{
		$step = (int)$step;
		if ($step <= 0) {
			return false;
		}

		$diagSteps = array(
			1 => array('site'),
			2 => array('templates'),
			3 => array('ibelement'),
			4 => array('mailtemplate'),
			5 => array('file_index', 'upload'),
			6 => array('user'),
			7 => array('orderstat'),
			8 => array('cache'),
			9 => array('attackevent'),
			10 => array('webhist'),
			11 => array('dropbasket'),
			12 => array('unconfirmed'),
			13 => array(),
			14 => array('webform'),
			16 => array('lang'),
			17 => array('module'),
			20 => array('component'),
			21 => array(),
			22 => array('perfmon'),
			23 => array('saleviewed'),
			24 => array(),
			25 => array(),
			26 => array(),
		);

		return $diagSteps[$step];
	}

	/**
	 * Конвертирует элементы и ключи массива из UTF8 в cp1251
	 *
	 * @param      $array
	 * @param bool $orig
	 *
	 * @return mixed {array}
	 * @internal param $ {array} $array Сам массив. $array Сам массив.
	 * @internal param $ {bool} $orig=false Возврощать ли оригинальные элементы с '~'. $orig=false Возвращать ли оригинальные элементы с '~'.
	 */
	public static function convArray_r($array, $orig = false)
	{
		foreach ($array as $k => $v) {
			if (is_array($v)) {
				$o = ($orig) ? true : false;
				$res[$k] = self::convArray_r($v, $o);
			} else {
				$res[$k] = mb_convert_encoding($v, 'WINDOWS-1251', 'UTF-8');
				if ($orig) {
					$res['~' . $k] = $v;
				}
			}
		}
		return $res;
	}

	public static function ArrayValidate($arData)
	{
		$result = false;
		if (isset($arData) && is_array($arData) && !empty($arData)) {
			$result = true;
		}
		return $result;
	}

	private static function GetMarketModuleList()
	{
		$arRequestedModules = CUpdateClientPartner::GetRequestedModules("");
		$arUpdateList = CUpdateClientPartner::GetUpdatesList(
			$errorMessage,
			LANGUAGE_ID,
			"N",
			$arRequestedModules,
			array(
				"fullmoduleinfo" => "Y"
			)
		);
		return $arUpdateList;
	}


	public static function GetModuleUpdatesInfo()
	{
		$bHasUpdates = false;

		$arModuleList = self::GetMarketModuleList();
		if (self::ArrayValidate($arModuleList["MODULE"])) {
			foreach ($arModuleList["MODULE"] as $arModule) {
				if ($arModule["@"]["ID"] != self::MODULE_ID) {
					continue;
				}
				if (self::ArrayValidate($arModule["#"]) && self::ArrayValidate($arModule["#"]["VERSION"])) {
					$bHasUpdates = true;
				}
			}
		}
		return $bHasUpdates;
	}


	/**
	 * Check module updates
	 *
	 * @param string    $strModuleID
	 * @param           $intDateTo
	 * @param bool      $sessionCache = false
	 *
	 * @return array
	 */
	public static function checkModuleUpdates($strModuleID, &$intDateTo, $sessionCache = false)
	{
		$arAvailableUpdates = array();
		if (!class_exists('CUpdateClientPartner')) {
			return $arAvailableUpdates;
		}

		// session cache
		$cacheKey = 'acrit.common.arUpdateFullList';
		if (isset($_SESSION[$cacheKey]) && $sessionCache) {
			$arUpdateList = $_SESSION[$cacheKey];
		} else {
			$arUpdateList = CUpdateClientPartner::GetUpdatesList($errorMessage, LANGUAGE_ID, 'Y', array(),
				array('fullmoduleinfo' => 'Y'));

			if ($sessionCache) {
				$_SESSION[$cacheKey] = $arUpdateList;
			}
		}

		if (is_array($arUpdateList) && is_array($arUpdateList['MODULE'])) {
			foreach ($arUpdateList['MODULE'] as $arModuleData) {
				if ($arModuleData['@']['ID'] == $strModuleID) {
					if (preg_match('#^(\d{1,2})\.(\d{1,2})\.(\d{4})$#', $arModuleData['@']['DATE_TO'], $arMatch)) {
						$intDateTo = mktime(23, 59, 59, $arMatch[2], $arMatch[1], $arMatch[3]);
					}
					if (is_array($arModuleData['#']['VERSION'])) {
						foreach ($arModuleData['#']['VERSION'] as $arVersion) {
							$arAvailableUpdates[$arVersion['@']['ID']] = $arVersion['#']['DESCRIPTION'][0]['#'];
						}
					}
				}
			}
		}

		return $arAvailableUpdates;
	}

	/**
	 * Show success
	 */
	public static function showSuccess($strMessage = null, $strDetails = null)
	{
		ob_start();
		\CAdminMessage::ShowMessage(array(
			'MESSAGE' => $strMessage,
			'DETAILS' => $strDetails,
			'HTML' => true,
			'TYPE' => 'OK',
		));
		return ob_get_clean();
	}

	/**
	 * Show note
	 */
	public static function showNote($strNote, $bCompact = false, $bCenter = false)
	{
		$arClass = array();
		if ($bCompact) {
			$arClass[] = 'acrit-exp-note-compact';
		}
		if ($bCenter) {
			$arClass[] = 'acrit-exp-note-center';
		}
		print '<div class="' . implode(' ', $arClass) . '">';
		print BeginNote();
		print $strNote;
		print EndNote();
		print '</div>';
	}

	public function GetHttpHost()
	{
		$arHttpHost = explode( ":", $_SERVER["HTTP_HOST"] );
		return $arHttpHost[0];
	}

} // end class