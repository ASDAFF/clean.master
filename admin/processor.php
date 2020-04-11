<?php
/**
 * Copyright (c) 11/4/2020 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

// turn off file cache buckets create
define('CACHED_b_file', false);
define("CACHED_b_file_bucket_size", 0);

require_once $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php";

set_time_limit(300);

IncludeModuleLangFile(__FILE__);

if ($APPLICATION->GetUserRight('clean.master') <= 'D') {
    $processorResult = array(
        'result' => 'FAIL',
        'message' => CAdminMessage::ShowMessage(array(
            'MESSAGE' => GetMessage("CLEAN_MASTER_DOSTUP_K_MODULU_ZAPR"),
            'TYPE' => 'FAIL',
            'HTML' => true
        ))
    );
    echo CUtil::PhpToJSObject($processorResult);
	die();
}

CModule::IncludeModule('clean.master');

global $action, $funcName, $isDemo;

CModule::IncludeModule('blog');
CModule::IncludeModule('forum');
CModule::IncludeModule('sale');
CModule::IncludeModule('iblock');

$funcName = !$funcName ? "clearAction$action" : $funcName;
echo $funcName();
die();


function diagnostic()
{
	global $diagnosticStep, $isDemo, $APPLICATION;
	if ($diagnosticStep == 1)
	{
		$_SESSION['master']['diagnostic'] = array(
			'upload' => array('complete' => false, 'step' => 0, 'class' => 'CCleanUpload', 'method' => 'GetDiagnosticData', 'id' => 5),
			// turn off this diagnostic. its unused TODO 2.1.7
			/*'iblock' => array('complete' => false, 'step' => 1, 'class' => 'CCleanUpload', 'method' => 'GetDiagnosticIBlockData','id' => 5),
			'section' => array('complete' => false, 'step' => 1, 'class' => 'CCleanUpload', 'method' => 'GetDiagnosticSectionData','id' => 5),
			'props' => array('complete' => false, 'step' => 1, 'class' => 'CCleanUpload', 'method' => 'GetDiagnosticPropsData','id' => 5),*/
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
			$_SESSION['master']['diagnostic']['orderstat'] = array('complete' => false, 'step' => 1, 'class' => 'CCleanOrderStat', 'method' => 'GetDiagnosticData', 'id' => 7);
			$_SESSION['master']['diagnostic']['dropbasket'] = array('complete' => false, 'step' => 1, 'class' => 'CCleanDropedBasket', 'method' => 'GetDiagnosticData', 'id' => 11);
			$_SESSION['master']['diagnostic']['saleviewed'] = array('complete' => false, 'step' => 1, 'class' => 'CCleanSaleViewed', 'method' => 'GetDiagnosticData', 'id' => 23);
		}
		if (CModule::IncludeModule('statistic')) {
			$_SESSION['master']['diagnostic']['attackevent'] = array('complete' => false, 'step' => 1, 'class' => 'CCleanAttackEvent', 'method' => 'GetDiagnosticData', 'id' => 9);
			$_SESSION['master']['diagnostic']['webhist'] = array('complete' => false, 'step' => 1, 'class' => 'CCleanWebHistory', 'method' => 'GetDiagnosticData', 'id' => 10);
		}
		if (CModule::IncludeModule('subscribe')) {
			$_SESSION['master']['diagnostic']['rubric'] = array('complete' => false, 'step' => 1, 'class' => 'CCleanSubscribe', 'method' => 'GetDiagnosticDataRubric', 'id' => 21);
			$_SESSION['master']['diagnostic']['unconfirmed'] = array('complete' => false, 'step' => 1, 'class' => 'CCleanSubscribe', 'method' => 'GetDiagnosticDataUnconfirmed', 'id' => 12);
		}
		if (CModule::IncludeModule('perfmon')) {
			$_SESSION['master']['diagnostic']['perfmon'] = array('complete' => false, 'step' => 1, 'class' => 'CCleanPerfmon', 'method' => 'GetDiagnosticData', 'id' => 22);
		}
	}

	$continue = false;
	$progressBar = '';
	$message = '';
	$stepCnt = 0;
	foreach ($_SESSION['master']['diagnostic'] as $key => $proc)
	{
		$stepCnt++;
		if ($proc['complete'] === false) {
			$progressBar = GetMessage('progress', array(
				'#NAME#' => $proc['step'] > 1 ? GetMessage($key.'_analize').GetMessage('CLEAN_MASTER_DIAGNOSTIC_STEP', array('#STEP#' => $proc['step'])) : GetMessage($key.'_analize'),
				'#PERSENT_VALUE#' => intval($stepCnt/count($_SESSION['master']['diagnostic'])*500),
				'#PERSENT#' => intval($stepCnt/count($_SESSION['master']['diagnostic'])*100),
			));

			$obj = new $proc['class'];
			if (($freeSpace = $obj->{$proc['method']}( $proc['step'] )) !== false) {
				$_SESSION['master']['diagnostic'][$key]['step']++;
			} else {
				$_SESSION['master']['diagnostic'][$key]['complete'] = true;
			}
			$continue = true;
			break;
		}
	}

	if ($continue)
	{
		ob_start();
		?>
        <script>
            $.ajax({
                method: 'POST',
                url: '/bitrix/admin/clean_master_processor.php',
                data: 'funcName=diagnostic',
                success: function(data){
        			try {
						var obj = JSON.parse(data);

        				if (obj.result == 'OK' && obj.action == 'process') {
	                        $('.master-area form').first().html(obj.DATA);
	                        $('.progress-bar').html(obj.PROGRESS);
	                    }
					} catch (e) {
        			    alert("<?=GetMessage("CLEAN_MASTER_DIAGNOSTIC_JS_ERROR")?>\n" + e.toString() + "\n\n" + data);
                        console.log( e );
        			    console.log( data );
    				}
                }
            });
        </script>
		<?
		$message .= ob_get_clean();
	}
	else
	{
		$message .= '<div class="adm-list-table-wrap" id="bx_admin_prefix"><table class="adm-list-table">';

		if($isDemo == 1)
			$message .= GetMessage('CLEAN_MASTER_DIAGNOSTIC_TABLE_HEAD');
		else
			$message .= GetMessage('CLEAN_MASTER_DIAGNOSTIC_TABLE_HEAD_DEMO');
		$summ = 0;

		foreach($_SESSION['master']['diagnostic'] as $key => $value)
		{
			if($key == 'iblock' || $key == 'props' || $key == 'section')
				continue;

			$size = 0;
			$description = '';

			switch($key)
			{
				case 'upload':
					if(count($value['dirs']) > 0 && is_array($value['dirs']))
					{
						foreach($value['dirs'] as $dk => $dir)
						{
							if($dir == 'iblock')
								continue;

							// TODO 2.1.7
							//$size += $value['dirSize'][$dk] - $dir;
						}
					}
					$size += $value['dirSize']['tmp']
						+ $value['dirSize']['resize_cache']

						// TODO 2.1.7
						//+ ($value['dirSize']['iblock'] - $value['iblock'])
					;
					break;
				case 'cache':
					foreach($value['dirs'] as $dk => $dir)
					{
						$size += $dir;
					}
					break;
				case 'templates':
					if(count($value['templates']) > 0 && is_array($value['templates']))
					{
						$description .= '<b>'.GetMessage('CLEAN_MASTER_DIAGNOSTIC_TEMPLATE').'</b>';
						$description .= '<ul>';
						foreach($value['templates'] as $dk => $dir)
						{
							$size += $dir;
							$description .= '<li><b>'.$dk.'</b> - '.round($dir, 1).'</li>';
						}
						$description .= '</ul>';
					}
					break;
				case 'ibelement':
					$size = 'N/A';
					if(count($value['iblock']) > 0 && is_array($value['iblock']))
					{
						foreach($value['iblock'] as $iblock => $elements)
						{
							$description .= '<b>'.$iblock.'</b>';
							$description .= '<ul>';
							$elCnt = 0;
							foreach($elements as $elem)
							{
								if($elCnt++ == 5)
									$description .= '<div style="display: none">';
								$description .= '<li>'.$elem.'</li>';
							}
							if($elCnt > 5)
								$description .= '</div><a onclick="$(this).siblings(\'div\').toggle()" href="javascript:void(0);">'.GetMessage('CLEAN_MASTER_SHOW_MORE').'</a>';
							$description .= '</ul>';
						}
					}
					break;
				case 'mailtemplate':
					$size = 'N/A';
					if(count($value['template']) > 0 && is_array($value['template']))
					{
						$description = '<b>'.GetMessage('CLEAN_MASTER_DIAGNOSTIC_TEMPLATE_TITLE').'</b>';
						$description .= '<ul>';
						foreach($value['template'] as $template)
							$description .= '<li>'.GetMessage('CLEAN_MASTER_DIAGNOSTIC_TEMPLATE_ITEM', array('#ID#' => $template['ID'], '#EVENT_NAME#' => $template['EVENT_NAME'])).'</li>';
						$description .= '</ul>';
					}
					break;
				case 'site':
					$size = 'N/A';
					if(count($value['site']) > 0 && is_array($value['site']))
					{
						$description .= '<b>'.GetMessage('CLEAN_MASTER_DIAGNOSTIC_SITE').'</b>';
						$description .= '<ul>';
						foreach($value['site'] as $site)
						{
							$description .= '<li>'.$site['NAME'].' ['.$site['ID'].']'.'</li>';
						}
						$description .= '</ul>';
					}
					if(count($value['iblock']) > 0 && is_array($value['iblock']))
					{
						$description .= '<b>'.GetMessage('CLEAN_MASTER_DIAGNOSTIC_SITE_IBLOCK').'</b>';
						$description .= '<ul>';
						foreach($value['iblock'] as $iblock)
						{
							$description .= '<li>'.$iblock['NAME'].' ['.$iblock['ID'].']'.'</li>';
						}
						$description .= '</ul>';
					}
					break;
				case 'user':
					$size = 'N/A';
					if(count($value['inactive']) > 0  && is_array($value['inactive']))
					{
						$description .= '<b>'.GetMessage('CLEAN_MASTER_DIAGNOSTIC_USER_TITLE').'</b>';
						$description .= '<ul>';
						$elCnt = 0;
						foreach($value['inactive'] as $user)
						{
							if($elCnt++ == 5)
								$description .= '<div style="display: none">';
							$description .= '<li>'.GetMessage('CLEAN_MASTER_DIAGNOSTIC_USER_ITEM', array('#ID#' => $user['ID'], '#LOGIN#' => $user['LOGIN'], '#EMAIL#' => $user['EMAIL'])).'</li>';
						}
						if($elCnt > 5)
							$description .= '</div><a onclick="$(this).siblings(\'div\').toggle()" href="javascript:void(0);">'.GetMessage('CLEAN_MASTER_SHOW_MORE').'</a>';
						$description .= '</ul>';
					}
					if(count($value['notauth']) > 0  && is_array($value['inactive']))
					{
						$description .= '<b>'.GetMessage('CLEAN_MASTER_DIAGNOSTIC_USER_NOTAUTH_TITLE').'</b>';
						$description .= '<ul>';
						$elCnt = 0;
						foreach($value['notauth'] as $user)
						{
							if($elCnt++ == 5)
								$description .= '<div style="display: none">';
							$description .= '<li>'.GetMessage('CLEAN_MASTER_DIAGNOSTIC_USER_ITEM', array('#ID#' => $user['ID'], '#LOGIN#' => $user['LOGIN'], '#EMAIL#' => $user['EMAIL'])).'</li>';
						}
						if($elCnt > 5)
							$description .= '</div><a onclick="$(this).siblings(\'div\').toggle()" href="javascript:void(0);">'.GetMessage('CLEAN_MASTER_SHOW_MORE').'</a>';
						$description .= '</ul>';
					}
					break;
				case 'orderstat':
					$size = 'N/A';
					if(intval($value['order'] > 0))
						$description = GetMessage('CLEAN_MASTER_DIAGNOSTIC_DELRECORD', array('#DELRECORD#' => $value['order']));
					break;
				case 'attackevent':
				case 'webhist':
				case 'stathistory':
				case 'rubric':
				case 'perfmon':
				case 'saleviewed':
					if($value['record'] > 0)
						$description = GetMessage('CLEAN_MASTER_DIAGNOSTIC_DELRECORD', array('#DELRECORD#' => $value['record']));
					$size = $value['size'];
					break;
				case 'dropbasket':
					$size = 'N/A';
					if(intval($value['basket'] > 0))
						$description = GetMessage('CLEAN_MASTER_DIAGNOSTIC_BASKET', array('#BASKET#' => $value['basket']));
					break;
				case 'unconfirmed':
					$size = 'N/A';
					if(intval($value['record'] > 0))
						$description = GetMessage('CLEAN_MASTER_DIAGNOSTIC_DELRECORD', array('#DELRECORD#' => $value['record']));
					break;
				case 'lang':
					if(count($value['langs']) > 0  && is_array($value['langs']))
					{
						$description .= GetMessage('CLEAN_MASTER_DIAGNOSTIC_LANG');
						$description .= '<ul>';
						foreach($value['langs'] as $langid => $lang)
						{
							$size += $lang;
							$description .= '<li>'.$langid.' - '.round($lang,3).' MB </li>';
						}
						$description .= '</ul>';
					}
					break;
				case 'module':
					if(count($value['modules']) > 0  && is_array($value['modules']))
					{
						$description .= GetMessage('CLEAN_MASTER_DIAGNOSTIC_MODULE');
						$description .= '<ul>';
						$elCnt = 0;
						foreach($value['modules'] as $id => $module)
						{
							$size += $module;
							if($elCnt++ == 5)
								$description .= '<div style="display: none">';
							$description .= '<li>'.$id.' - '.round($module,1).' MB </li>';
						}
						if($elCnt > 5)
							$description .= '</div><a onclick="$(this).siblings(\'div\').toggle()">'.GetMessage('CLEAN_MASTER_SHOW_MORE').'</a>';
						$description .= '</ul>';
					}
					break;
				case 'component':
					if(count($value['components']) && is_array($value['components']))
					{
						$description .= GetMessage('CLEAN_MASTER_DIAGNOSTIC_COMPONENTS');
						$description .= '<ul>';
						$elCnt = 0;
						foreach($value['components'] as $id => $component)
						{
							$size += $module;
							if($elCnt++ == 5)
								$description .= '<div style="display: none">';
							$description .= '<li>'.$component['name'].'</li>';
						}
						if($elCnt > 5)
							$description .= '</div><a onclick="$(this).siblings(\'div\').toggle()" href="javascript:void(0);">'.GetMessage('CLEAN_MASTER_SHOW_MORE').'</a>';
						$description .= '</ul>';
					}
					$size = $value['size'];
					break;

			}

			if (floatval($size)) {
				$summ += round($size, 3);
				$size = '<b>~'.round($size, 3).'</b>';
			}

			if ($isDemo == 1)
				$message .= GetMessage('ACRTT_MASTER_DIAGNOSTIC_TABLE_ROW', array(
					'#NAME#' => GetMessage($key),
					'#DESCRIPTION#' => $description,
					'#SIZE#' => $size,
					'#ID#' => $value['id']
				));
			else
				$message .= GetMessage('ACRTT_MASTER_DIAGNOSTIC_TABLE_ROW_DEMO', array(
					'#NAME#' => GetMessage($key),
					'#DESCRIPTION#' => $description,
					'#SIZE#' => $size,
					'#ID#' => $value['id']
				));

		} // \end foreach $_SESSION['master']['diagnostic']


		if($isDemo == 1)
			$message .= GetMessage('CLEAN_MASTER_DIAGNOSTIC_TABLE_FOOT', array("#SUM#" => $summ));
		else
			$message .= GetMessage('CLEAN_MASTER_DIAGNOSTIC_TABLE_FOOT_DEMO', array("#SUM#" => $summ));

		if ($isDemo == 1)
			$message .= '</table></div>'
						. '<input type="hidden" name="action_start" value="Y">'
						. '<input class="adm-btn adm-btn-save" type="submit" name="select_action" value="' . GetMessage('MASTER_ACTION_CLEANSTART')
							. '" style="float: right; margin-top: 20px"/>';

		//$message .= '<pre>'.print_r($_SESSION['master']['diagnostic'], true).'</pre><br>';
	}

	$APPLICATION->RestartBuffer();
	return \Bitrix\Main\Web\Json::encode(array(
		'result' => 'OK',
		'action' => 'process',
		'DATA' => $message,
		'PROGRESS' => $progressBar,
	));
}