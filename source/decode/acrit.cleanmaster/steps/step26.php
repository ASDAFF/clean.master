<?php
?>
<h2><?=GetMessage("CLEANMASTER_ACTION_26")?></h2>
<br/>
<form>
    <?if (CModule::IncludeModule('blog')):?>
        <p><b><?=GetMessage('CLEANMASTER_ACTION_26_SPAM')?></b></p>
        <br/>

	    <?=GetMessage('CLEANMASTER_ACTION_26_SPAM_HELP')?>

		<p><?=GetMessage('CLEANMASTER_ACTION_26_POST_MESSAGE_HTML')?><br>
			<input type="text" name="blog[POST_MESSAGE_HTML]" style="width:60%;" value="<?=htmlspecialcharsEx($_SESSION['CLEANMASTER']['BLOG']['POST_MESSAGE_HTML'])?>"></p>

		<p><?=GetMessage("CLEANMASTER_ACTION_26_AUTHOR_NAME")?><br>
		<input type="text" name="blog[AUTHOR_NAME]" style="width:40%;" value="<?=htmlspecialcharsEx($_SESSION['CLEANMASTER']['BLOG']['AUTHOR_NAME'])?>"></p>

        <br/>
		<input type="hidden" name="analyse" class="analyse_flag" value="">
        <a href="javascript:void(0)" class="action-analys adm-btn adm-btn-save"><?=GetMessage('CLEANMASTER_ACTION_ANALYSE_START')?></a>
		<a href="javascript:void(0)" <?if ($isDemo != 1) {?> style="display:none" <?}?> class="action-process adm-btn adm-btn-save"><?=GetMessage('CLEANMASTER_ACTION_CLEANSTART')?></a>
	    <?include __DIR__ . '/save_profile_btn.php';?>
    <?else:?>
        <p><b><?=GetMessage('CLEANMASTER_ACTION_26_NOT_FOUND')?></b></p>
    <?endif?>
</form>
