<?php
/**
 * hipot studio source file
 * User: <hipot AT ya DOT ru>
 * Date: 23.04.2018 12:56
 * @version pre 1.0
 */

namespace Acrit\Cleanmaster;

use Bitrix\Main\Localization\Loc,
	Bitrix\Main\Grid\Declension,
	Bitrix\Main\Loader;

Loc::loadMessages(__DIR__ . '/../../../admin/processor.php');

class DiagInterface extends \TCleanMasterFunctions
{
	public static function showHtmlFromOneDiagValue($type, $value, $isDemo = 1, $steps = [])
	{
		$size = 0;
		$description = '';

		switch ($type) {
			case 'upload':
				$cu = new \CCleanUpload();
				$cntF = 50;

				$listBIG = $cu->getDiagnosticListFiles( /*$value['dirSize']*/[], $cntF/*, $showFinded != 1*/);

				$listBIGra = $listBIG['ra'];
				foreach ($listBIGra as $f) {
					$size += $f['SIZE'] / 1024 / 1024; //mb
				}
				unset($listBIGra);
				$size += $cu->getDiagnosticListFilesFullSize() / 1024 / 1024; //mb

				if ($isDemo == 1) {
					$list = $listBIG['r'];

					if (count($list) > 0) {
						$description = '<b>' . GetMessage('ACRIT_CLEANMASTER_DIAGNOSTIC_UPLOAD_LIST_HEAD') . ' ' . GetMessage('ACRIT_CLEANMASTER_DIAGNOSTIC_UPLOAD_LIST_HEAD_PR', array('#N#' => $cntF)) . '</b>';
						$description .= '<ul>';
						$elCnt = 0;

						foreach ($list as $l) {
							if ($elCnt++ == 5) {
								$description .= '<div style="display: none">';
							}
							$description .= '<li>' . $l . '</li>';
						}
						unset($list);
						if ($elCnt > 5) {
							$description .= '</div><a style="cursor:pointer;" onclick="$(this).siblings(\'div\').toggle()" href="javascript:void(0);">' . GetMessage('ACRIT_CLEANMASTER_SHOW_MORE') . '</a>';
						}
						$description .= '</ul>';

						$description .= '<p><a href="/bitrix/admin/acrit_cleanmaster_processor.php?download_lost_files=Y">' . GetMessage('ACRIT_CLEANMASTER_DOWNLOAD_LOST_FILES') . '</a></p>';
						//'<p><a href="/bitrix/admin/acrit_cleanmaster_processor.php?delete_lost_files=Y" class="dyn-url">' . GetMessage('ACRIT_CLEANMASTER_DELETE_LOST_FILES') . '</a></p>';
					}
				}
				unset($cu);

				break;
			case 'cache':
				foreach ($value['dirs'] as $dk => $dir) {
					$size += $dir;
				}
				break;
			case 'templates':
				if (is_array($value['templates']) && count($value['templates']) > 0) {
					$description .= '<b>' . GetMessage('ACRIT_CLEANMASTER_DIAGNOSTIC_TEMPLATE') . '</b>';
					$description .= '<ul>';
					foreach ($value['templates'] as $dk => $dir) {
						$size += $dir;
						$description .= '<li><b>' . $dk . '</b> - ' . round($dir, 1) . '</li>';
					}
					$description .= '</ul>';
				}
				break;
			case 'ibelement':
				$size = 'N/A';
				if (is_array($value['iblock']) && count($value['iblock']) > 0) {
					foreach ($value['iblock'] as $iblock => $elements) {
						$description .= '<b>' . $iblock . '</b>';
						$description .= '<ul>';
						$elCnt = 0;
						foreach ($elements as $elem) {
							if ($elCnt++ == 5) {
								$description .= '<div style="display: none">';
							}
							$description .= '<li>' . $elem . '</li>';
						}
						if ($elCnt > 5) {
							$description .= '</div><a style="cursor:pointer;" onclick="$(this).siblings(\'div\').toggle()" href="javascript:void(0);">' . GetMessage('ACRIT_CLEANMASTER_SHOW_MORE') . '</a>';
						}
						$description .= '</ul>';
					}
				}
				break;
			case 'mailtemplate':
				$size = 'N/A';
				if (count($value['template']) > 0 && is_array($value['template'])) {
					$description = '<b>' . GetMessage('ACRIT_CLEANMASTER_DIAGNOSTIC_TEMPLATE_TITLE') . '</b>';
					$description .= '<ul>';
					foreach ($value['template'] as $template) {
						$description .= '<li>' . GetMessage('ACRIT_CLEANMASTER_DIAGNOSTIC_TEMPLATE_ITEM',
								array('#ID#' => $template['ID'], '#EVENT_NAME#' => $template['EVENT_NAME'])) . '</li>';
					}
					$description .= '</ul>';
				}
				break;
			case 'site':
				$size = 'N/A';
				if (count($value['site']) > 0 && is_array($value['site'])) {
					$description .= '<b>' . GetMessage('ACRIT_CLEANMASTER_DIAGNOSTIC_SITE') . '</b>';
					$description .= '<ul>';
					foreach ($value['site'] as $site) {
						$description .= '<li>' . $site['NAME'] . ' [' . $site['ID'] . ']' . '</li>';
					}
					$description .= '</ul>';
				}
				if (count($value['iblock']) > 0 && is_array($value['iblock'])) {
					$description .= '<b>' . GetMessage('ACRIT_CLEANMASTER_DIAGNOSTIC_SITE_IBLOCK') . '</b>';
					$description .= '<ul>';
					foreach ($value['iblock'] as $iblock) {
						$description .= '<li>' . $iblock['NAME'] . ' [' . $iblock['ID'] . ']' . '</li>';
					}
					$description .= '</ul>';
				}
				break;
			case 'user':
				$size = 'N/A';
				$description .=  GetMessage('CLEANMASTER_ACTION_6_DESCRIPTION') . '<br><br>';
				if (count($value['inactive']) > 0 && is_array($value['inactive'])) {
					$description .= '<b>' . GetMessage('ACRIT_CLEANMASTER_DIAGNOSTIC_USER_TITLE') . '</b>';
					$description .= '<ul>';
					$elCnt = 0;
					foreach ($value['inactive'] as $user) {
						if ($elCnt++ == 5)
							$description .= '<div style="display: none">';
						$description .= '<li>' . GetMessage('ACRIT_CLEANMASTER_DIAGNOSTIC_USER_ITEM',
								array('#ID#' => $user['ID'], '#LOGIN#' => $user['LOGIN'], '#EMAIL#' => $user['EMAIL'])) . '</li>';
					}
					if ($elCnt > 5)
						$description .= '</div><a style="cursor:pointer;" onclick="$(this).siblings(\'div\').toggle()" href="javascript:void(0);">' . GetMessage('ACRIT_CLEANMASTER_SHOW_MORE') . '</a>';
					$description .= '</ul>';
				}
				if (count($value['notauth']) > 0 && is_array($value['inactive'])) {
					$description .= '<b>' . GetMessage('ACRIT_CLEANMASTER_DIAGNOSTIC_USER_NOTAUTH_TITLE') . '</b>';
					$description .= '<ul>';
					$elCnt = 0;
					foreach ($value['notauth'] as $user) {
						if ($elCnt++ == 5)
							$description .= '<div style="display: none">';
						$description .= '<li>' . GetMessage('ACRIT_CLEANMASTER_DIAGNOSTIC_USER_ITEM', array('#ID#' => $user['ID'], '#LOGIN#' => $user['LOGIN'], '#EMAIL#' => $user['EMAIL'])) . '</li>';
					}
					if ($elCnt > 5)
						$description .= '</div><a style="cursor:pointer;" onclick="$(this).siblings(\'div\').toggle()" href="javascript:void(0);">' . GetMessage('ACRIT_CLEANMASTER_SHOW_MORE') . '</a>';
					$description .= '</ul>';
				}
				break;
			case 'orderstat':
				$size = 'N/A';
				if ((int)$value['order'] > 0)
					$description = GetMessage('ACRIT_CLEANMASTER_DIAGNOSTIC_DELRECORD', array('#DELRECORD#' => $value['order']));
				break;
			case 'attackevent':
			case 'webhist':
			case 'stathistory':
			case 'rubric':
			case 'perfmon':
			case 'saleviewed':
				if ($value['record'] > 0)
					$description = GetMessage('ACRIT_CLEANMASTER_DIAGNOSTIC_DELRECORD', array('#DELRECORD#' => $value['record']));
				$size = $value['size'];
				break;
			case 'dropbasket':
				$size = 'N/A';
				if ((int)$value['basket'] > 0)
					$description = GetMessage('ACRIT_CLEANMASTER_DIAGNOSTIC_BASKET', array('#BASKET#' => $value['basket']));
				break;
			case 'unconfirmed':
				$size = 'N/A';
				if ((int)$value['record'] > 0)
					$description = GetMessage('ACRIT_CLEANMASTER_DIAGNOSTIC_DELRECORD', array('#DELRECORD#' => $value['record']));
				break;
			case 'lang':
				if (count($value['langs']) > 0 && is_array($value['langs'])) {
					$description .= GetMessage('ACRIT_CLEANMASTER_DIAGNOSTIC_LANG');
					$description .= '<ul>';
					foreach ($value['langs'] as $langid => $lang) {
						$size += $lang;
						$description .= '<li>' . $langid . ' - ' . round($lang, 3) . ' MB </li>';
					}
					$description .= '</ul>';
				}
				break;
			case 'module':
				if (count($value['modules']) > 0 && is_array($value['modules'])) {
					$isPeformanceModule = Loader::includeModule('perfmon');

					$description .= GetMessage('ACRIT_CLEANMASTER_DIAGNOSTIC_MODULE');
					$description .= '<ul>';
					$elCnt = 0;
					foreach ($value['modules'] as $id => $module) {
						$size += $module['SIZE'];
						if ($elCnt++ == 5)
							$description .= '<div style="display: none">';
						$description .= '<li>'
							. ($module['IS_SYSTEM'] ? '<b>' : '') . $id . ($module['IS_SYSTEM'] ? '</b>' : '')
							. ' - ' . round($module['SIZE'], 1) . ' MB ';

						$ca = $module['AGENTS'];
						if ($isDemo == 1) {
							if ($ca > 0) {
								if ($isPeformanceModule) {
									$description .= '<a target="_blank" href="' . GetMessage('ACRIT_CLEANMASTER_AGENTS_URL', ['#MODULE_ID#' => $id]) . '">';
								}
								$description .= ' [ <b>' . $ca . ' ' . self::Suffix($ca, GetMessage('ACRIT_CLEANMASTER_AGENTS_WFORMS')) . '</b> ]';
								if ($isPeformanceModule) {
									$description .= '</a>';
								}
							} else {
								$description .= '<i>' . GetMessage('ACRIT_CLEANMASTER_NO_AGENTS') . '</i>';
							}
						}
						$description .= '</li>';
					}
					if ($elCnt > 5)
						$description .= '</div><a style="cursor:pointer;" onclick="$(this).siblings(\'div\').toggle()">' . GetMessage('ACRIT_CLEANMASTER_SHOW_MORE') . '</a>';
					$description .= '</ul>';
				}
				break;
			case 'component':
				if (count($value['components']) && is_array($value['components'])) {
					$description .= GetMessage('ACRIT_CLEANMASTER_DIAGNOSTIC_COMPONENTS');
					$description .= '<ul>';
					$elCnt = 0;
					foreach ($value['components'] as $id => $component) {
						$size += $component['size'];
						if ($elCnt++ == 5)
							$description .= '<div style="display: none">';
						$description .= '<li>' . $component['name'] . '</li>';
					}
					if ($elCnt > 5)
						$description .= '</div><a style="cursor:pointer;" onclick="$(this).siblings(\'div\').toggle()" href="javascript:void(0);">' . GetMessage('ACRIT_CLEANMASTER_SHOW_MORE') . '</a>';
					$description .= '</ul>';
				}
				$size = $value['size'];
				break;
		}

		if ((float)$size > 0) {
			$size_html = '<b>~' . round($size, 3) . '</b>';
		} else {
			$size_html = 0;
		}
		$message    = '';
		if ($isDemo == 1 && count($steps) <= 0) {
			$message .= GetMessage('ACRTT_CLEANMASTER_DIAGNOSTIC_TABLE_ROW', array(
				'#NAME#' => GetMessage($type),
				'#DESCRIPTION#' => $description,
				'#SIZE#' => $size_html,
				'#ID#' => $value['id']
			));
		} else {
			$message .= GetMessage('ACRTT_CLEANMASTER_DIAGNOSTIC_TABLE_ROW_DEMO', array(
				'#NAME#' => GetMessage($type),
				'#DESCRIPTION#' => $description,
				'#SIZE#' => $size_html,
				'#ID#' => $value['id']
			));
		}

		return [
			'size'              => $size,
			'size_html'         => $size_html,
			'html'              => $message,
			'html_value'        => $description
		];
	}

	/**
	 * used in processor.php
	 *
	 * @param       $diagnostic
	 * @param int   $isDemo
	 * @param array $steps
	 *
	 * @return array
	 */
	static function showHtmlFromDiag($diagnostic, $isDemo = 1, $steps = [])
	{
		$summ = 0;
		$description = '';

		foreach ($diagnostic as $key => $value) {
			if (in_array($key, ['iblock', 'props', 'section', 'file_index'])) {
				continue;
			}

			$step = self::showHtmlFromOneDiagValue($key, $value, $isDemo, $steps);
			$size           = $step['size'];
			$description    .= $step['html'];

			if ((float)$size > 0) {
				$summ += round($size, 3);
			}
		} // \end foreach

		return [
			'summ'      => $summ,
			'html'      => $description
		];
	}

	/**
	 * ¬озвращает слово с правильным суффиксом
	 *
	 * @param (int) $n - количество
	 * @param (array|string) $str - строка 'один|два|несколько' или 'слово|слова|слов'
	 *      или массив с такой же историей
	 * @return string
	 */
	public static function Suffix($n, $forms)
	{
		if (is_string($forms)) {
			$forms = explode('|', $forms);
		}
		$declens = new Declension($forms[0], $forms[1], $forms[2]);
		return $declens->get($n);
	}
}