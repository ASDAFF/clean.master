<?php

// inputs:
// clear_menu Y/N - показывать выбор действия (меню с чекерами)
// dop_tools Y/N - дополнительные действия
// show_steps - int[] шаги из гета инициировать
// $_REQUEST['action'] - массив ID действий, которые отобразить

use \Acrit\Cleanmaster\LastDiagTable;

IncludeModuleLangFile(__FILE__);

CJSCore::Init(array("jquery"));
$APPLICATION->oAsset->addString('<script type="text/javascript" src="/bitrix/js/acrit.cleanmaster/main.js"></script>');
$APPLICATION->oAsset->addString('<link rel="stylesheet" href="/bitrix/css/acrit.cleanmaster/main.css">');

$actionList = CCleanMain::getMainSteps();

if ($_REQUEST['dop_tools'] == 'Y') {
	$actionList = CCleanMain::getToolsSteps();
}

global $action_start, $clear_menu, $dop_tools, $profile_table, $show_steps, $isDemo;

/* @var $show_steps int[] */
if (is_numeric($show_steps)) {
	$show_steps = array($show_steps);
	$show_steps = array_filter($show_steps);
	if ($action_start == 'Y' && count($show_steps) > 0) {
		// emulate selects
		$_SERVER['REQUEST_METHOD'] = 'POST';
		$_REQUEST['action'] = $show_steps;
	}
}

if ($action_start != 'Y') {
	unset($_SESSION['cleanmaster']['action']);
}
?>

<script>
	var action_list = <?=CUtil::PhpToJSObject($actionList);?>;
	var selected_action_list = <?=CUtil::PhpToJSObject($_SESSION['cleanmaster']['action']);?>;
</script>
<?/*if($isDemo != 1):?>
    <?=GetMessage('ACRIT_CLEANMASTER_DEMO')?>
<?endif*/?>
<br>

<?if($clear_menu != 'Y' && $action_start != 'Y' && $dop_tools != 'Y'):?>
    <a class="adm-btn adm-btn-save" id="diagnostic"><?=GetMessage('ACRIT_CLEANMASTER_RUN_DIAGNOSTIC_FULL')?></a>
    <?if($isDemo == 1):?>
        &nbsp;&nbsp;<?=GetMessage('ACRIT_CLEANMASTER_OR')?>&nbsp;&nbsp;
        <a class="adm-btn" href="<?=$APPLICATION->GetCurPageParam('clear_menu=Y', array('dop_tools'))?>"><?=GetMessage('ACRIT_CLEANMASTER_TO_CLEAR')?></a>
    <?endif?>

	&nbsp;&nbsp;|&nbsp;&nbsp;
	<a class="adm-btn" href="<?=$APPLICATION->GetCurPageParam('dop_tools=Y', array('clear_menu'))?>"><?=GetMessage('ACRIT_CLEANMASTER_DOP_TOOLS')?></a>

    <br>
    <div class="progress-bar"></div><br>
<?elseif ($clear_menu == 'Y' && $isDemo == 1 && $action_start == 'Y' && count($_SESSION['cleanmaster']['action']) >= 1):?>

	<?
	$steps = array_keys($_SESSION['cleanmaster']['action']);

	foreach ($steps as $step) {
		if (count(CCleanMain::getDiagnosticStepCodesByStep($step)) > 0) { ?>
			<a class="adm-btn adm-btn-save" id="diagnostic"><?= GetMessage('ACRIT_CLEANMASTER_RUN_DIAGNOSTIC') ?></a>
			<br>
			<div class="progress-bar"></div><br>
		<?
		break;
		}
	}?>

<?else:?>
	<a class="adm-btn adm-btn-save" href="<?=$APPLICATION->GetCurPageParam('', array('clear_menu', 'action_start', 'dop_tools', 'show_steps'))?>"><?=GetMessage('ACRIT_CLEANMASTER_BACKTO_DIAGNOSTIC_FULL')?></a><br>

	<?if ($isDemo != 1) {?>
		<p>&nbsp;</p>
		<?=BeginNote()?>
		<p><?=GetMessage('CLEANMASTER_ACTION_DEMO')?></p>
		<?=EndNote()?>
	<?}?>

	<div class="progress-bar"></div><br>
<?endif?>

<div class="cleanmaster-area">
	<div class="diagnostic-steps"></div>

	<?if ($action_start != 'Y'):?>
		<form action="<?=$_SERVER['REQUEST_URI']?>" method="POST">
			<?if (($clear_menu == 'Y' && $isDemo == 1) || $dop_tools == 'Y'):?>
					<input type="hidden" name="action_start" value="Y">

					<?
					// main list only
					if ($dop_tools != 'Y') {
						$obLastDiag = new LastDiagTable();
						$actionListlastDiag = [];
						foreach ($actionList as $key => $action) {
							$stepCodes = CCleanMain::getDiagnosticStepCodesByStep($key);
							$stepCodes = array_reverse($stepCodes);
							$stepCodes = $stepCodes[0];
							$actionListlastDiag[$key] = $obLastDiag->returnSavedStep($stepCodes);
						}
					}
					?>
					<div class="action_page_list_checks">
						<?foreach ($actionList as $key => $action):?>
							<div class="li">
								<div class="l">
									<input type="checkbox" name="action[<?=$key?>]" value="<?=$key?>" id="steps_<?=$key?>">
									<label for="steps_<?=$key?>"><?=$actionList[$key]?></label>
								</div>
								<div class="r">
									<?
									if (isset($actionListlastDiag[$key])) {
										$htmlDiag = Acrit\Cleanmaster\DiagInterface::showHtmlFromOneDiagValue(
											$actionListlastDiag[$key]['STEP_CODE'],
											$actionListlastDiag[$key]['PARAMS'],
											$isDemo,
											$steps
										);
										echo $htmlDiag['html_value'];
										if ((float)$htmlDiag['size'] > 0) {
											echo '<br>' . $htmlDiag['size_html'] . ' ' . GetMessage('CLEANMASTER_MB');
										}
									}
									?>
								</div>
							</div>
						<?endforeach?>
						<?=BeginNote() . GetMessage('acrit.cleanmaster_ACTION_LIST_SELECT_PAGE_INFO') . EndNote()?>
					</div>
					<br/>
					<input type="submit" name="select_action" value="<?=GetMessage('CLEANMASTER_ACTION_SELECT')?>">
			<?endif;?>
		</form>
	<?elseif ($isDemo == 1 || $dop_tools == 'Y'):?>
		<?
			if ($_SERVER['REQUEST_METHOD'] == 'POST')
			{
				if (!is_array($_REQUEST['action']))
					return;
				$postActions = $_REQUEST['action'];
				sort($postActions);
				unset($_SESSION['cleanmaster']);

				$actionCnt = 0;
				foreach ($postActions as $key => $action) {
					if ($actionCnt == 0) {
						$_SESSION['cleanmaster']['action'][$action]['active'] = true;
					}
					$_SESSION['cleanmaster']['action'][$action]['prev'] = $postActions[$key - 1];
					$_SESSION['cleanmaster']['action'][$action]['next'] = $postActions[$key + 1];
					$actionCnt++;
				}
				parse_str($_SERVER['QUERY_STRING'], $arQuery);
				$arQuery['action_start'] = 'Y';
				if ($arQuery['show_steps']) {
					$arQuery['show_steps'] = 'SELECT' . $arQuery['show_steps'];
				}
				LocalRedirect($_SERVER['SCRIPT_NAME'].'?'.http_build_query($arQuery));
			}
		?>
		<div class="clean-action-menu">
			<h2>
				<?
				$menuUrl = $_SERVER['SCRIPT_NAME'] . "?lang=ru&clear_menu=Y&mid=acrit.cleanmaster&mid_menu=1";
				if ($dop_tools == 'Y') {
					$menuUrl = $_SERVER['SCRIPT_NAME'] . "?lang=ru&dop_tools=Y&mid=acrit.cleanmaster&mid_menu=1";
				}
				?>
				<a href="<?=$menuUrl?>"><?=GetMessage('CLEANMASTER_ACTION_MENU')?></a></h2>
			<ul>
				<?
				if (count($_SESSION['cleanmaster']['action']) > 1) {
					foreach($_SESSION['cleanmaster']['action'] as $key => $action):?>
						<li><a href="javascript:void(0)" data-action-id="<?=$key?>"><?=$actionList[$key]?></a></li>
					<?endforeach?>
				<?}?>
			</ul>
		</div>
		<div class="clean-action-wrapper">
			<?
				foreach ($_SESSION['cleanmaster']['action'] as $key => $action)
				{
					$key = (int)$key;
					if (file_exists(__DIR__."/steps/step$key.php"))
					{
						echo '<div class="action-container';
						if ($action['active'])
							echo ' active';
						echo '" data-action-id="', $key, '">';

						///////////////////////////////// step include HERE!!!! ///////////////////

						require_once __DIR__ . "/steps/step$key.php";

						////////////////////////////////////////////////////////////////////////////

						echo '</div>' . "\n\n";
					}
				}


				reset($_SESSION['cleanmaster']['action']);
				$firstAction = current($_SESSION['cleanmaster']['action']);
			?>
			<div class="nav-buttons">
				<?/*<div><a href="javascript:void(0)" class="action-process adm-btn adm-btn-save"><?=GetMessage('CLEANMASTER_ACTION_CLEANSTART')?></a></div>*/?>
				<div class="action-container-prev<? if(!$firstAction['prev']) echo ' hide'; ?>"><span>&#212;</span><a class="action-container-prev adm-btn" data-action-id="<?=$firstAction['prev']?>"><?=$actionList[$firstAction['prev']]?></a></div>
				<div class="action-container-next<? if(!$firstAction['next']) echo ' hide'; ?>"><a class="action-container-next adm-btn" data-action-id="<?=$firstAction['next']?>"><?=$actionList[$firstAction['next']]?></a><span>&#215;</span></div>
			</div>
		</div>
	<?endif?>
</div>
<?

// fast iterators CSS and JS
?>
<style type="text/css">
	<? echo file_get_contents(__DIR__ . '/steps/fast_css_style.css') ?>
</style>
<script type="text/javascript">
	<? echo file_get_contents(__DIR__ . '/steps/fast_js_script.js') ?>
</script>
