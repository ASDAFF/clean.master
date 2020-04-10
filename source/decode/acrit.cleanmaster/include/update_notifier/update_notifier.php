<?
use Bitrix\Main\Localization\Loc,
	Bitrix\Main\Loader;

Loc::loadMessages(__FILE__);

$ModuleID = 'acrit.cleanmaster';

$imRes = Loader::includeSharewareModule($ModuleID);
if (! class_exists('CCleanMain')) {
	return;
}

$intDateTo = null;
$arAvailableUpdates = CCleanMain::checkModuleUpdates($ModuleID, $intDateTo, true);

if (is_array($arAvailableUpdates) && !empty($arAvailableUpdates)) {
	$arAvailableUpdates = array_reverse($arAvailableUpdates);
	$intMaxDisplayUpdates = 10;
	$intUpdatesCount = count($arAvailableUpdates);
	$arAvailableUpdates = array_slice($arAvailableUpdates, 0, $intMaxDisplayUpdates);
	ob_start();
	?>
	<style>
		#acrit-exp-update-notifier-details-toggle {
			border-bottom: 1px dashed #2675d7;
			color: #2675d7;
			text-decoration: none;
		}
		#acrit-exp-update-notifier-details-toggle:hover {
			border-bottom: 0;
		}
		#acrit-exp-update-notifier-details-block {
			display: none;
		}
		#acrit-exp-update-notifier-details-block ul {
			margin-bottom: 4px;
			margin-left: 0;
			padding-left: 18px;
		}
	</style>
	<div>
		<a href="javascript:void(0);" id="acrit-exp-update-notifier-details-toggle">
			<?= Loc::getMessage('ACRIT_EXP_UPDATE_NOTIFIER_DETAILS'); ?>
		</a>
	</div>
	<div id="acrit-exp-update-notifier-details-block">
		<ul>
			<?
			foreach ($arAvailableUpdates as $strVersion => $strDescription):?>
				<li>
					<div><b><?= $strVersion; ?></b>.<br/><?= $strDescription; ?></div>
					<br/></li>
			<?endforeach ?>
		</ul>
		<a href="/bitrix/admin/update_system_partner.php?lang=<?= LANGUAGE_ID ?>&addmodule=<?=$ModuleID?>" target="_blank" class="adm-btn adm-btn-green">
			<?= Loc::getMessage('ACRIT_EXP_UPDATE_NOTIFIER_UPDATE'); ?>
		</a>
	</div>
	<script>
		$('#acrit-exp-update-notifier-details-toggle').bind('click', function (e) {
			e.preventDefault();
			$('#acrit-exp-update-notifier-details-block').toggle();
		});
	</script>
	<?
	$strDetails = ob_get_clean();
	print CCleanMain::showSuccess(Loc::getMessage('ACRIT_EXP_UPDATE_NOTIFIER_AVAILABLE', array(
			'#COUNT#' => $intUpdatesCount, '#MODULE_ID#' => $ModuleID)
	), $strDetails);
} elseif (is_numeric($intDateTo) && $intDateTo > 0 && $intDateTo <= time()) {
	$strRenewUrl = 'https://marketplace.1c-bitrix.ru/tobasket.php?ID=' . $ModuleID;
	if (LICENSE_KEY != 'DEMO') {
		$strLicense = md5('BITRIX' . LICENSE_KEY . 'LICENCE');
		$strRenewUrl .= '&lckey=' . $strLicense;
	}
	$strMessage = Loc::getMessage('ACRIT_EXP_UPDATE_NOTIFIER_RENEW_LICENSE', array(
		'#DATE#' => date(\CDatabase::DateFormatToPHP(FORMAT_DATE), $intDateTo),
		'#LINK#' => $strRenewUrl,
	));
	print CCleanMain::showNote($strMessage, false);
}

?>