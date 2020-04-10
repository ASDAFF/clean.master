<?php
/**
 * hipot studio source file
 * User: <hipot AT ya DOT ru>
 * Date: 09.06.2017 3:25
 * @version pre 1.0
 */
$_SERVER["DOCUMENT_ROOT"] = str_replace(
	array('/bitrix/modules/acrit.cleanmaster/cron', '/local/modules/acrit.cleanmaster/cron'),
	'',
	__DIR__
);

// для производительности и прочие фиксы на крон
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
require $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php";

use \Acrit\Cleanmaster\ProfilesTable;
use Bitrix\Main\Diag\Debug;

set_time_limit(0);
while (ob_get_level()) {
	ob_end_flush();
}

CModule::IncludeModule('acrit.cleanmaster');
if ($isDemo != 1) {
	echo 'module acrit.cleanmaster: license expired.' . PHP_EOL;
	exit;
}

$profileId = (int)$argv[1];
if ($profileId <= 0) {
	echo 'module acrit.cleanmaster: no profile in argv.' . PHP_EOL;
	exit;
}

$profile = ProfilesTable::getById($profileId)->fetch();
if ($profile['ID'] != $profileId) {
	echo 'module acrit.cleanmaster: DB error, index mismatch.' . PHP_EOL;
	exit;
}

$profile['AR_PARAMS'] = \Bitrix\Main\Web\Json::decode($profile['PARAMS']);

$funcName = "clearAction" . (int)$profile['STEP_ID'] . "Cron";
if (! function_exists($funcName)) {
	echo 'module acrit.cleanmaster: no defined ' . $funcName . '.' . PHP_EOL;
	exit;
}

$APPLICATION->RestartBuffer();
Debug::startTimeLabel($funcName);

echo $funcName( $profile['AR_PARAMS'] );

Debug::endTimeLabel($funcName);
$lab = Debug::getTimeLabels();
echo 'DONE on ' . round($lab[$funcName]['time'], 3);


require $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_after.php";