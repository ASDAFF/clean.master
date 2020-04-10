<?
IncludeModuleLangFile(__FILE__);

$moduleId = "acrit.cleanmaster";

CModule::IncludeModule($moduleId);

global $DB, $isDemo, $dop_tools, $profile_table;

if ($profile_table == 'Y') {

	require __DIR__ . '/admin/profiles.php';

} else {

	if ($isDemo != 1) {
		echo BeginNote();
		echo GetMessage("ACRIT_CLEANMASTER_IS_DEMO_MESSAGE");
		echo '<br /><br /><a target="_blank" href="'. GetMessage('ACRIT_CLEANMASTER_IS_DEMO_MESSAGE_BUY_URL') . '">'.GetMessage("ACRIT_CLEANMASTER_IS_DEMO_MESSAGE_BTN").'</a>';
		echo EndNote();
	}

	if ((int)$_GET['step'] <= 0) {
		echo BeginNote();
		echo GetMessage('ACRIT_CLEANMASTER_FILESYS_WARNING');
		echo EndNote();

		include __DIR__ . '/include/update_notifier/update_notifier.php';
	}

	// save settings
	if (isset($_REQUEST['ACUpdate'])) {
		if (isset($_REQUEST["ACRITMENU_GROUPNAME"]) && (strlen(trim($_REQUEST["ACRITMENU_GROUPNAME"])) > 0)) {
			COption::SetOptionString("acrit.common", "acritmenu_groupname", trim($_REQUEST["ACRITMENU_GROUPNAME"]));
		}
		if (isset($_REQUEST["ACRIT_CLEANMASTER_PHP_PATH"]) && (strlen(trim($_REQUEST["ACRIT_CLEANMASTER_PHP_PATH"])) > 0)) {
			COption::SetOptionString($moduleId, "php_path", trim($_REQUEST["ACRIT_CLEANMASTER_PHP_PATH"]));
		}
	}

	$bLastResultTab = ($clear_menu != 'Y' && $action_start != 'Y' && $dop_tools != 'Y');

	$aTabs = array(
		array("DIV" => "edit1", "TAB" => GetMessage("ACRIT_CLEANMASTER_MAIN_TAB_SET"),
			"ICON" => "settings", "TITLE" => GetMessage("ACRIT_CLEANMASTER_MAIN_TAB_SET")),
		array("DIV" => "edit2", "TAB" => GetMessage("ACRIT_CLEANMASTER_MAIN_TAB_SUPPORT"),
			"ICON" => "settings", "TITLE" => GetMessage("ACRIT_CLEANMASTER_MAIN_TAB_TITLE_SUPPORT")),
		array("DIV" => "edit3", "TAB" => GetMessage("ACRIT_CLEANMASTER_MAIN_TAB_OPTIONS"),
			"ICON" => "settings", "TITLE" => GetMessage("ACRIT_CLEANMASTER_MAIN_TAB_OPTIONS"))
	);
	if ($bLastResultTab) {
		$aTabs[] = array("DIV" => "edit4", "TAB" => GetMessage("ACRIT_CLEANMASTER_LAST_DIAG_TAB_OPTIONS"),
			"ICON" => "settings", "TITLE" => GetMessage("ACRIT_CLEANMASTER_LAST_DIAG_TAB_OPTIONS"));
	}
	$tabControl = new CAdminTabControl("tabControlAcritClean", $aTabs);

	$tabControl->Begin();
	$tabControl->BeginNextTab();
	?>
	<tr>
		<td colspan="2">
			<?
			require_once $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.basename(__DIR__).'/steps.php';
			?>
			<?if ((int)$_GET['step'] <= 0) {?>
				<?=GetMessage('ACRIT_CLEANMASTER_FULL_DIAG_INTERRUPT_WARNING')?>
			<?}?>
		</td>
	</tr>

	<?
	$tabControl->EndTab();
	$tabControl->BeginNextTab();
	?>
	<tr>
		<td class="heading" colspan="2"><?= GetMessage('SC_FRM_1') ?></td>
	</tr>
	<tr>
		<td valign="top" class="adm-detail-content-cell-l"><span class="required">*</span><?= GetMessage('SC_FRM_2') ?><br>
			<small><?= GetMessage('SC_FRM_3') ?></small>
		</td>
		<td valign="top" class="adm-detail-content-cell-r"><textarea cols="60" rows="6" name="ticket_text_proxy" id="ticket_text_proxy"></textarea></td>
	</tr>
	<tr>
		<td class="adm-detail-content-cell-l"></td>
		<td class="adm-detail-content-cell-r">
			<input type="button" value="<?= GetMessage('SC_FRM_4') ?>" onclick="SubmitToSupport()" name="submit_button">
		</td>
	</tr>
	<tr>
		<td colspan="2">
			<?=BeginNote()?>
			<?=GetMessage('SC_TXT_1') ?> <a href="<?= GetMessage('A_SUPPORT_URL') ?>"><?= GetMessage('A_SUPPORT_URL') ?></a>
			<?=EndNote() ?>
		</td>
	</tr>
	<tr>
		<td colspan="2">
			<?=GetMessage("ACRIT_CLEANMASTER_SUPPORT_TEXT")?>
		</td>
	</tr>

	<?
	$tabControl->EndTab();
	$tabControl->BeginNextTab();
	?>
	<form method="post" action="<?=$APPLICATION->GetCurPage()?>?mid=<?=urlencode('acrit.cleanmaster')?>&amp;lang=<?=LANGUAGE_ID?>&amp;mid_menu=1&amp;tabControlAcritClean_active_tab=edit3">
		<tr>
			<td class="heading" colspan="2"><?=GetMessage( "ACRITMENU_GROUPNAME_LABEL" );?></td>
		</tr>
		<tr>
			<td colspan="2" class="adm-detail-content-cell" align="center">
				<?$v = COption::GetOptionString( "acrit.common", "acritmenu_groupname", GetMessage("ACRIT_MENU_NAME") );?>
				<input type="text" name="ACRITMENU_GROUPNAME" value="<?=htmlspecialcharsbx($v)?>"/>
			</td>
		</tr>
		<tr>
			<td class="heading" colspan="2"><?=GetMessage( "ACRIT_CLEANMASTER_PHP_PATH" )?></td>
		</tr>
		<tr>
			<td colspan="2" class="adm-detail-content-cell" align="center">
				<?$v = COption::GetOptionString($moduleId, "php_path", "php");?>
				<input type="text" name="ACRIT_CLEANMASTER_PHP_PATH" value="<?=htmlspecialcharsbx($v)?>"/>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<input class="adm-btn-save" type="submit" name="ACUpdate" value="<?=GetMessage( "MAIN_SAVE" )?>" title="<?=GetMessage( "MAIN_OPT_SAVE_TITLE" )?>">
			</td>
		</tr>
	</form>

	<?
	if ($bLastResultTab) {

		$tabControl->EndTab();
		$tabControl->BeginNextTab();
		?>

		<div id="last-diag-result"></div>
		<script type="text/javascript">
			$(function () {
				$("#last-diag-result").html("<?=GetMessage('ACRIT_CLEANMASTER_LOADING')?>");
				$.ajax({
					method: 'post',
					url: '/bitrix/admin/acrit_cleanmaster_processor.php',
					data: 'funcName=diagnostic&diagnosticStep=1&showFinded=1',
					success: function (data) {
						try {
							var obj = JSON.parse(data);
							if (obj.result == 'OK' && obj.action == 'process') {
								$("#last-diag-result").html(obj.DATA);
							}
						} catch (e) {
							console.log(e);
						}
					}
				});
			});
		</script>
		<?

	}
	$tabControl->EndTab();
	$tabControl->Buttons();
	$tabControl->End();
	?>

	<form target="_blank" name="fticket" action="<?= GetMessage('A_SUPPORT_URL') ?>" method="POST">
		<input type="hidden" name="send_ticket" value="Y">
		<input type="hidden" name="ticket_title" value="<?= GetMessage('SC_RUS_L1') . ' ' . htmlspecialcharsbx($_SERVER['HTTP_HOST']) ?>">
		<input type="hidden" name="ticket_text" value="Y">
	</form>

	<script type="text/javascript">
		BX.ready(function () {
			BX.bind(BX('cleanup_queue'), 'click', function (e) {
				BX.PreventDefault(e);
				if (confirm('<?= GetMessage('ACRIT_CLEANMASTER_OPTIONS_YOU_SURE') ?>')) {
					window.location = this.getAttribute('href');
				}
			});
		});
		function SubmitToSupport() {
			var frm = document.forms.fticket;
			frm.ticket_text.value = BX('ticket_text_proxy').value;
			if (frm.ticket_text.value == '') {
				alert('<?=GetMessage("SC_NOT_FILLED")?>');
				return;
			}
			frm.submit();
		}
	</script>

<?}?>