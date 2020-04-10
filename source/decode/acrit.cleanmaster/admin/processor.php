<?php
use \Acrit\Cleanmaster\ProfilesTable,
	\Acrit\Cleanmaster\LastDiagTable,
	\Acrit\Cleanmaster\DiagInterface;

// turn off file cache buckets create
define('CACHED_b_file', false);
define("CACHED_b_file_bucket_size", 0);

// performance
define("STOP_STATISTICS",       true);
define("NO_KEEP_STATISTIC",     true);
define("NO_AGENT_STATISTIC",    "Y");
define("NOT_CHECK_PERMISSIONS", true);
define("DisableEventsCheck",    true);

require_once $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php";

/* @var $delete_lost_files string */
/* @var $download_lost_files string */
/* @var $revert_tmp_dir string */
/* @var $clear_tmp_dir string */

ini_set('error_reporting', E_ALL & ~E_NOTICE & ~E_WARNING & ~E_DEPRECATED);
set_time_limit(300);

IncludeModuleLangFile(__FILE__);

if ($APPLICATION->GetUserRight('acrit.cleanmaster') <= 'D') {
	$processorResult = array(
		'result' => 'FAIL',
		'message' => CAdminMessage::ShowMessage(array(
			'MESSAGE' => GetMessage("ACRIT_CLEANMASTER_DOSTUP_K_MODULU_ZAPR"),
			'TYPE' => 'FAIL',
			'HTML' => true
		))
	);
	echo CUtil::PhpToJSObject($processorResult);
	die();
}

CModule::IncludeModule('acrit.cleanmaster');

// needed superglobals, omg, recreate to register
global $action, $funcName, $isDemo, $save_profile;

CCleanMain::includeAllNeedModules();

if ($save_profile == 'Y') {
	$request = \Bitrix\Main\Application::getInstance()->getContext()->getRequest()->toArray();
	$config = $request;
	unset($config['action'], $config['save_profile'], $config['analyse']);

	if (!defined('BX_UTF') && constant('BX_UTF') !== true) {
		$config = CCleanMain::convArray_r($config);
		if (!is_array($config)) {
			$config = array();
		}
	}

	$r = ProfilesTable::add(array(
		'STEP_ID' => $action,
		'PARAMS' => \Bitrix\Main\Web\Json::encode($config)
	));

	if ($r->isSuccess()) {
		$message = GetMessage('ACRIT_CLEANMASTER_PROFILE_ADDED', array('#PROFILE_ID#' => $r->getId()));
	} else {
		$message = GetMessage('ACRIT_CLEANMASTER_PROFILE_ADDED_ERROR') . '<br><br>' . implode('<br>', $r->getErrorMessages());
	}
	echo \Bitrix\Main\Web\Json::encode(array(
		'result' => 'OK',
		'action' => 'process',
		'DATA' => $message,
		'PROGRESS' => false
	));
	exit;
}

if ($delete_lost_files == 'Y') {
	$funcName = 'clearAction5_LostFiles';
}
if ($download_lost_files == 'Y') {
	$funcName = 'clearAction5_DownloadLostFiles';
}
if ($revert_tmp_dir == 'Y') {
	$funcName = 'clearAction5_RevertTmpDir';
}
if ($clear_tmp_dir == 'Y') {
	$funcName = 'clearAction5_ClearTmpDir';
}


// runer
$funcName = !$funcName ? "clearAction$action" : $funcName;
$APPLICATION->RestartBuffer();
echo $funcName();
die();


function diagnostic()
{
	global $diagnosticStep, $steps, $showFinded, $isDemo, $APPLICATION;

	$obLastDiag = new LastDiagTable();

	if (strpos($steps, '|') !== false) {
		$steps = explode('|', $steps);
		$steps = array_filter($steps);
	} else if (is_numeric($steps)) {
		$steps = array($steps);
	} else {
		$steps = false;
	}

	if ($diagnosticStep == 1 || $showFinded == 1) {
		$_SESSION['cleanmaster']['diagnostic'] = array(
			'file_index' => array('complete' => false, 'step' => 0, 'class' => 'CCleanUpload', 'method' => 'fillBfileIndex', 'method_cnt_steps' => 'getCountStepsBfileIndex', 'id' => 5),
			'upload' => array('complete' => false, 'step' => 0, 'class' => 'CCleanUpload', 'method' => 'GetDiagnosticData', 'method_cnt_steps' => 'getCountStepsDiagnosticData', 'id' => 5),
			'cache' => array('complete' => false, 'step' => 1, 'class' => 'CCleanCache', 'method' => 'GetDiagnosticData', 'id' => 8),
			'site' => array('complete' => false, 'step' => 1, 'class' => 'CCleanSite', 'method' => 'GetDiagnosticData', 'id' => 1),
			'templates' => array('complete' => false, 'step' => 1, 'class' => 'CCleanTemplate', 'method' => 'GetDiagnosticData', 'id' => 2),
			'ibelement' => array('complete' => false, 'step' => 1, 'class' => 'CCleanIBlock', 'method' => 'GetDiagnosticData', 'id' => 3),
			'mailtemplate' => array('complete' => false, 'step' => 1, 'class' => 'CCleanMailTemplate', 'method' => 'GetDiagnosticData', 'id' => 4),
			'user' => array('complete' => false, 'step' => 1, 'class' => 'CCleanUser', 'method' => 'GetDiagnosticData', 'id' => 6),
			'stathistory' => array('complete' => false, 'step' => 1, 'class' => 'CCleanSubscribe', 'method' => 'GetDiagnosticDataHistory', 'id' => 13),
			'webform' => array('complete' => false, 'step' => 1, 'class' => 'CCleanWebForm', 'method' => 'GetDiagnosticData', 'id' => 14),
			'lang' => array('complete' => false, 'step' => 1, 'class' => 'CCleanLanguage', 'method' => 'GetDiagnosticData', 'id' => 16),
			'module' => array('complete' => false, 'step' => 1, 'class' => 'CCleanModule', 'method' => 'GetDiagnosticData', 'id' => 17),
			'component' => array('complete' => false, 'step' => 1, 'class' => 'CCleanComponent', 'method' => 'GetDiagnosticData', 'id' => 20),
		);
		if (CModule::IncludeModule('sale')) {
			$_SESSION['cleanmaster']['diagnostic']['orderstat'] = array('complete' => false, 'step' => 1, 'class' => 'CCleanOrderStat', 'method' => 'GetDiagnosticData', 'id' => 7);
			$_SESSION['cleanmaster']['diagnostic']['dropbasket'] = array('complete' => false, 'step' => 1, 'class' => 'CCleanDropedBasket', 'method' => 'GetDiagnosticData', 'id' => 11);
			$_SESSION['cleanmaster']['diagnostic']['saleviewed'] = array('complete' => false, 'step' => 1, 'class' => 'CCleanSaleViewed', 'method' => 'GetDiagnosticData', 'id' => 23);
		}
		if (CModule::IncludeModule('statistic')) {
			$_SESSION['cleanmaster']['diagnostic']['attackevent'] = array('complete' => false, 'step' => 1, 'class' => 'CCleanAttackEvent', 'method' => 'GetDiagnosticData', 'id' => 9);
			$_SESSION['cleanmaster']['diagnostic']['webhist'] = array('complete' => false, 'step' => 1, 'class' => 'CCleanWebHistory', 'method' => 'GetDiagnosticData', 'id' => 10);
		}
		if (CModule::IncludeModule('subscribe')) {
			$_SESSION['cleanmaster']['diagnostic']['rubric'] = array('complete' => false, 'step' => 1, 'class' => 'CCleanSubscribe', 'method' => 'GetDiagnosticDataRubric', 'id' => 21);
			$_SESSION['cleanmaster']['diagnostic']['unconfirmed'] = array('complete' => false, 'step' => 1, 'class' => 'CCleanSubscribe', 'method' => 'GetDiagnosticDataUnconfirmed', 'id' => 12);
		}
		if (CModule::IncludeModule('perfmon')) {
			$_SESSION['cleanmaster']['diagnostic']['perfmon'] = array('complete' => false, 'step' => 1, 'class' => 'CCleanPerfmon', 'method' => 'GetDiagnosticData', 'id' => 22);
		}

		// rewrite test
		/*$_SESSION['cleanmaster']['diagnostic'] = array(
			'ibelement' => array('complete' => false, 'step' => 1, 'class' => 'CCleanIBlock', 'method' => 'GetDiagnosticData', 'id' => 3),
		);*/

		if (is_array($steps) && count($steps) > 0) {
			$needSteps = array();
			foreach ($steps as $st) {
				/** @noinspection SlowArrayOperationsInLoopInspection */
				$needSteps = array_merge($needSteps, (array)CCleanMain::getDiagnosticStepCodesByStep($st));
			}
			foreach ($_SESSION['cleanmaster']['diagnostic'] as $diagStepCode => $data) {
				if (!in_array($diagStepCode, $needSteps)) {
					unset($_SESSION['cleanmaster']['diagnostic'][$diagStepCode]);
				}
			}
		}
	}

	if ($diagnosticStep == 1 && $showFinded != 1) {
		$obLastDiag->clearAll();
	}

	// restore session from DB
	foreach ($_SESSION['cleanmaster']['diagnostic'] as $key => &$proc) {
		$procDb = $obLastDiag->returnSavedStep($key);
		if ($procDb['PARAMS']['complete']) {
			$proc = $procDb['PARAMS'];
		}

		if ($showFinded == 1 && !$proc['complete']) {
			unset($_SESSION['cleanmaster']['diagnostic'][$key]);
		}
	}
	unset($proc);

	$continue = false;
	$progressBar = '';
	$message = '';
	$stepCnt = 0;
	foreach ($_SESSION['cleanmaster']['diagnostic'] as $key => $proc) {
		$stepCnt++;
		if ($proc['complete'] === false) {
			$obj = new $proc['class'];

			if (!isset($proc['cnt_steps']) && is_callable(array($obj, $proc['method_cnt_steps']))) {
				$_SESSION['cleanmaster']['diagnostic'][$key]['cnt_steps'] = $proc['cnt_steps'] = $obj->{$proc['method_cnt_steps']}();
			}

			$barName = GetMessage($key . '_analize');
			if ($proc['step'] > 1) {
				$barName = GetMessage($key . '_analize') . GetMessage('ACRIT_CLEANMASTER_DIAGNOSTIC_STEP', array('#STEP#' => $proc['step']));
				if ($proc['cnt_steps'] > 0) {
					$barName .= GetMessage('ACRIT_CLEANMASTER_DIAGNOSTIC_STEP_CNT', array('#CNT_STEPS#' => $proc['cnt_steps']));
				}
			}

			$progressBar = GetMessage('progress', array(
				'#NAME#' => $barName,
				'#PERSENT_VALUE#' => intval($stepCnt / count($_SESSION['cleanmaster']['diagnostic']) * 500),
				'#PERSENT#' => intval($stepCnt / count($_SESSION['cleanmaster']['diagnostic']) * 100),
			));

			if (($freeSpace = $obj->{$proc['method']}($proc['step'])) !== false) {
				$_SESSION['cleanmaster']['diagnostic'][$key]['step']++;
			} else {
				$_SESSION['cleanmaster']['diagnostic'][$key]['complete'] = true;
			}
			$continue = true;
			break;
		} else {
			$procDb = $obLastDiag->returnSavedStep($key);
			if (! $procDb['PARAMS']['complete']) {
				$obLastDiag->addFinishedStep($key, $proc);
			}
		}
	}

	if ($continue) {
		ob_start();
		?>
		<script>
			$.ajax({
				method: 'POST',
				url: '/bitrix/admin/acrit_cleanmaster_processor.php',
				data: 'funcName=diagnostic&steps=<?=implode('|', (array)$steps)?>',
				success: function (data) {
					try {
						var obj = JSON.parse(data);
						if (obj.result == 'OK' && obj.action == 'process') {

							<?if (count($steps) <= 0) {?>
								$('.cleanmaster-area form').first().html(obj.DATA);
							<?} else {?>
								$('.cleanmaster-area .diagnostic-steps').html(obj.DATA);
							<?}?>

							$('.progress-bar').html(obj.PROGRESS);
						}
					} catch (e) {
						alert("<?=GetMessage("ACRIT_CLEANMASTER_DIAGNOSTIC_JS_ERROR")?>\n" + e.toString() + "\n\n" + data);
						console.log(e);
						console.log(data);
					}
				}
			});
		</script>
		<?
		$message .= ob_get_clean();
	} else {
		$message .= '<div class="adm-list-table-wrap" id="bx_admin_prefix"><table class="adm-list-table">';

		if ($isDemo == 1 && count($steps) <= 0) {
			$message .= GetMessage('ACRIT_CLEANMASTER_DIAGNOSTIC_TABLE_HEAD');
		} else {
			$message .= GetMessage('ACRIT_CLEANMASTER_DIAGNOSTIC_TABLE_HEAD_DEMO');
		}

		$fullDiagHtml = DiagInterface::showHtmlFromDiag($_SESSION['cleanmaster']['diagnostic'], $isDemo, $steps);

		$summ = (float)$fullDiagHtml['summ'];
		$message .= $fullDiagHtml['html'];
		unset($fullDiagHtml);

		if ($isDemo == 1 && count($steps) <= 0) {
			$message .= GetMessage('ACRIT_CLEANMASTER_DIAGNOSTIC_TABLE_FOOT', array("#SUM#" => $summ));
		} else {
			$message .= GetMessage('ACRIT_CLEANMASTER_DIAGNOSTIC_TABLE_FOOT_DEMO', array("#SUM#" => $summ));
		}

		$message .= '</table></div>';

		if ($isDemo == 1 && count($steps) <= 0) {
			$message .= '<input type="hidden" name="action_start" value="Y">'
				. '<input class="adm-btn adm-btn-save" type="submit" name="select_action" value="' . GetMessage('CLEANMASTER_ACTION_CLEANSTART')
				. '" style="float: right; margin-top: 20px"/>';
		}

		$message .= '<br clear="all"><br>';

		/*$message .= '<pre>'.print_r($_SESSION['cleanmaster']['diagnostic'], true).'</pre><br>';
		$message .= '<pre>'.print_r($proc, true).'</pre><br>';*/
	}


	$APPLICATION->RestartBuffer();
	return \Bitrix\Main\Web\Json::encode(array(
		'result' => 'OK',
		'action' => 'process',
		'DATA' => $message,
		'PROGRESS' => $progressBar,
	));

}