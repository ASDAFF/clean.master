<h2><?=GetMessage("CLEANMASTER_ACTION_17")?></h2>
<p><?=GetMessage('CLEANMASTER_ACTION_17_DESCRIPTION')?></p>
<form>
	<input type="checkbox" name="deleteFiles" value="Y" id="deleteFiles" checked/>
	<label for="deleteFiles"><?=GetMessage("CLEANMASTER_ACTION_17_deleteFiles")?></label><br/>
	<br/>
	<div style="padding-left:40px;">
		<input type="checkbox" name="deleteFilesSys" id="deleteFilesSys" value="Y"/>
		<label for="deleteFilesSys"><?=GetMessage("CLEANMASTER_ACTION_17_deleteFilesSys")?></label><br/>
		<br/>
	</div>
	<input type="checkbox" name="deleteAgents" id="deleteAgents" value="Y" checked/>
	<label for="deleteAgents"><?=GetMessage("CLEANMASTER_ACTION_17_deleteAgents")?></label><br/>
	<br/>
    <a href="javascript:void(0)" class="action-process adm-btn adm-btn-save"><?=GetMessage('CLEANMASTER_ACTION_CLEANSTART')?></a>
</form>
