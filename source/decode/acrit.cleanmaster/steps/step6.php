<h2><?=GetMessage("CLEANMASTER_ACTION_6")?></h2>
<p><?=GetMessage('CLEANMASTER_ACTION_6_DESCRIPTION')?></p>
<br/>
<form>
    <input type="checkbox" name="not_auth" id="not_auth" value="Y"/>
	<label for="not_auth"><?=GetMessage("CLEANMASTER_ACTION_6_NOT_AUTH")?></label><br/>
	<br/>
	<input type="checkbox" name="deleteAssets" id="deleteAssets" value="Y"/>
	<label for="deleteAssets"><?=GetMessage("CLEANMASTER_ACTION_6_DELETE_ASSETS")?></label><br/>
	<br/>
	<a href="javascript:void(0)" class="action-process adm-btn adm-btn-save"><?=GetMessage('CLEANMASTER_ACTION_CLEANSTART')?></a>
	<?include __DIR__ . '/save_profile_btn.php';?>
</form>