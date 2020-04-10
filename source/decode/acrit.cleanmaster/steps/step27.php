<h2><?=GetMessage("CLEANMASTER_ACTION_27")?></h2>
<p><?=GetMessage('CLEANMASTER_ACTION_27_DESCRIPTION')?></p>
<form>
	<?if ($isDemo == 1) {?>
		<a href="javascript:void(0)" class="action-process adm-btn adm-btn-save"><?=GetMessage('CLEANMASTER_ACTION_CLEANSTART_REPAIR')?></a>
	<?}?>
</form>