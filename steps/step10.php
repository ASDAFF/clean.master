<h2><?=GetMessage("MASTER_ACTION_10")?></h2>
<p><?=GetMessage('MASTER_ACTION_10_DESCRIPTION')?></p>
<form>
    <?=GetMessage("MASTER_ACTION_TO")?><br/>
    <input type="text" name="d1" style="width:40px;" placeholder="<?=GetMessage("MASTER_ACTION_DAY")?>"/>
    <input type="text" name="m1" style="width:40px;" placeholder="<?=GetMessage("MASTER_ACTION_MONTH")?>"/>
    <input type="text" name="y1" style="width:40px;" placeholder="<?=GetMessage("MASTER_ACTION_YEAR")?>"/><br/><br/>
    <br/>
    <a href="javascript:void(0)" class="action-process adm-btn adm-btn-save"><?=GetMessage('MASTER_ACTION_CLEANSTART')?></a>
</form>
