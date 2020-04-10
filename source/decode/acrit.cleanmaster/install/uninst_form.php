<?
IncludeModuleLangFile(__FILE__);
$APPLICATION->SetTitle(GetMessage("acrit.uninst_fb_PAGE_TITLE", array('#MODULE_NAME#' => $GLOBALS['ACRIT_MODULE_NAME'])));
if(!isset($_REQUEST['reason'])){
	$_REQUEST['reason'] = '7';
}
if(!isset($_REQUEST['support'])){
	$_REQUEST['support'] = '0';
}
if(!isset($_REQUEST['agree'])){
	$_REQUEST['agree'] = '1';
}
if(!isset($_REQUEST['callback'])){
	$_REQUEST['callback'] = '1';
}
?>
<form action="<?=$APPLICATION->GetCurPage()?>" method="get" name="acrit_uninst_feedback" class="acrit-uninst-feedback" id="acrit_uninst_feedback" style="max-width: 500px; background: #fff; padding: 10px 20px 20px 20px;">
	<?=bitrix_sessid_post()?>
	<input type="hidden" name="lang" value="<?=LANG?>">
	<input type="hidden" name="id" value="<?=$GLOBALS['ACRIT_MODULE_ID'];?>">
	<input type="hidden" name="uninstall" value="Y">
	<input type="hidden" name="step" value="2">
	<input type="hidden" name="prevstep" value="1">
	<p><b><?=GetMessage("acrit.uninst_fb_TEXT_1")?></b></p>
	<p class="subtitle"><b><?=GetMessage("acrit.uninst_fb_REASON")?></b></p>
	<input type="radio" name="reason" id="reason_1" value="1"<?=($_REQUEST['reason']==1)?' checked':'';?>> <label for="reason_1"><?=GetMessage("acrit.uninst_fb_REASON_SETTINGS")?></label><br>
	<input type="radio" name="reason" id="reason_2" value="2"<?=($_REQUEST['reason']==2)?' checked':'';?>> <label for="reason_2"><?=GetMessage("acrit.uninst_fb_REASON_FUNCTIONAL")?></label><br>
	<input type="radio" name="reason" id="reason_3" value="3"<?=($_REQUEST['reason']==3)?' checked':'';?>> <label for="reason_3"><?=GetMessage("acrit.uninst_fb_REASON_DEMOEND")?></label><br>
	<input type="radio" name="reason" id="reason_4" value="4"<?=($_REQUEST['reason']==4)?' checked':'';?>> <label for="reason_4"><?=GetMessage("acrit.uninst_fb_REASON_ANOTHER_MODULE")?></label><br>
	<input type="radio" name="reason" id="reason_5" value="5"<?=($_REQUEST['reason']==5)?' checked':'';?>> <label for="reason_5"><?=GetMessage("acrit.uninst_fb_REASON_ERRORS")?></label><br>
	<input type="radio" name="reason" id="reason_8" value="8"<?=($_REQUEST['reason']==8)?' checked':'';?>> <label for="reason_8"><?=GetMessage("acrit.uninst_fb_REASON_REINSTALL")?></label><br>
	<input type="radio" name="reason" id="reason_6" value="6"<?=($_REQUEST['reason']==6)?' checked':'';?>> <label for="reason_6"><?=GetMessage("acrit.uninst_fb_REASON_NOTWORK")?></label><br>
	<input type="radio" name="reason" id="reason_7" value="7"<?=($_REQUEST['reason']==7)?' checked':'';?>> <label for="reason_7"><?=GetMessage("acrit.uninst_fb_REASON_ANOTHER")?></label><br>
	<textarea name="reason_other" id="reason_other" style="display: <?=($_REQUEST['reason']==7)?' block':'none';?>; width: 100%; min-height: 80px; max-height:1000px; margin-top: 10px; resize:vertical;" placeholder="<?=GetMessage("acrit.uninst_fb_REASON_NOTE")?>"></textarea>
	<p class="subtitle"><b><?=GetMessage("acrit.uninst_fb_SUPPORT")?></b></p>
	<input type="radio" name="support" id="support_1" value="1"<?=($_REQUEST['support']==1)?' checked':'';?>> <label for="support_1"><?=GetMessage("acrit.uninst_fb_SUPPORT_YES")?></label><br>
	<input type="radio" name="support" id="support_0" value="0"<?=($_REQUEST['support']==0)?' checked':'';?>> <label for="support_0"><?=GetMessage("acrit.uninst_fb_SUPPORT_NO")?></label><br>
	<p class="subtitle"><b><?=GetMessage("acrit.uninst_fb_CALLBACK")?></b></p>
	<input type="radio" name="callback" id="callback_1" value="1"<?=($_REQUEST['callback']==1)?' checked':'';?>> <label for="callback_1"><?=GetMessage("acrit.uninst_fb_CALLBACK_YES")?></label><br>
	<input type="radio" name="callback" id="callback_0" value="0"<?=($_REQUEST['callback']==0)?' checked':'';?>> <label for="callback_0"><?=GetMessage("acrit.uninst_fb_CALLBACK_NO")?></label><br>
	<p><b><?=GetMessage("acrit.uninst_fb_PERSONAL", array('#EMAIL#'=>COption::GetOptionString('main','email_from'), '#SITE#'=>$_SERVER['SERVER_NAME']))?></b></p>
	<p class="subtitle"><b><?=GetMessage("acrit.uninst_fb_AGREE")?></b></p>
	<input type="radio" name="agree" id="agree_1" value="1"<?=($_REQUEST['agree']==1)?' checked':'';?>> <label for="agree_1"><?=GetMessage("acrit.uninst_fb_AGREE_YES")?></label><br>
	<input type="radio" name="agree" id="agree_0" value="0"<?=($_REQUEST['agree']==0)?' checked':'';?>> <label for="agree_0"><?=GetMessage("acrit.uninst_fb_AGREE_NO")?></label><br>
	<br>
	<p><?=GetMessage("acrit.uninst_fb_TEXT_2")?></p>
	<?// Custom ?>
	<?/*<p>
		<input type="checkbox" name="savedata" id="savedata" value="Y" checked="checked" />
		<label for="savedata"><?=GetMessage("MOD_UNINST_SAVE_TABLES")?></label><br />
	</p>*/?>
	<input type="submit" name="inst" value="<?=GetMessage("acrit.uninst_fb_DEL")?>">
</form>
<script>
	var reasons = document.forms["acrit_uninst_feedback"].elements["reason"];
	for(var i = 0, max = reasons.length; i < max; i++) {
		reasons[i].onclick = function() {
			if (this.value == 7) {
				document.getElementById("reason_other").style.display = "block";
			}
			else {
				document.getElementById("reason_other").style.display = "none";
			}
		}
	}
</script>