<?php
?>
<h2><?=GetMessage("CLEANMASTER_ACTION_25")?></h2>
<br/>
<form>
    <?if (CModule::IncludeModule('forum')):?>
        <p><b><?=GetMessage('CLEANMASTER_ACTION_25_SPAM')?></b></p>
        <br/>

	    <?=GetMessage('CLEANMASTER_ACTION_25_SPAM_HELP')?>

		<p><?=GetMessage('CLEANMASTER_ACTION_25_POST_MESSAGE_HTML')?><br>
			<input type="text" name="forum[POST_MESSAGE_HTML]" style="width:60%;" value="<?=htmlspecialcharsEx($_SESSION['CLEANMASTER']['FORUM']['POST_MESSAGE_HTML'])?>"></p>

		<p><?=GetMessage("CLEANMASTER_ACTION_25_AUTHOR_NAME")?><br>
		<input type="text" name="forum[AUTHOR_NAME]" style="width:40%;" value="<?=htmlspecialcharsEx($_SESSION['CLEANMASTER']['FORUM']['AUTHOR_NAME'])?>"></p>

        <br/>
		<input type="hidden" name="analyse" class="analyse_flag" value="">
        <a href="javascript:void(0)" class="action-analys adm-btn adm-btn-save"><?=GetMessage('CLEANMASTER_ACTION_ANALYSE_START')?></a>
		<a href="javascript:void(0)" <?if ($isDemo != 1) {?> style="display:none" <?}?> class="action-process adm-btn adm-btn-save"><?=GetMessage('CLEANMASTER_ACTION_CLEANSTART')?></a>
	    <?include __DIR__ . '/save_profile_btn.php';?>
    <?else:?>
        <p><b><?=GetMessage('CLEANMASTER_ACTION_25_NOT_FOUND')?></b></p>
    <?endif?>
</form>
