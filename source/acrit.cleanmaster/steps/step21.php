<h2><?=GetMessage("CLEANMASTER_ACTION_21")?></h2>
<p><?=GetMessage('CLEANMASTER_ACTION_21_DESCRIPTION')?></p>
<form>
    <?=GetMessage("CLEANMASTER_ACTION_FROM")?><br/>
    <input type="text" name="d1" style="width:40px;" placeholder="<?=GetMessage("CLEANMASTER_ACTION_DAY")?>"/>
    <input type="text" name="m1" style="width:40px;" placeholder="<?=GetMessage("CLEANMASTER_ACTION_MONTH")?>"/>
    <input type="text" name="y1" style="width:40px;" placeholder="<?=GetMessage("CLEANMASTER_ACTION_YEAR")?>"/><br/><br/>
    <?=GetMessage("CLEANMASTER_ACTION_TO")?><br/>
    <input type="text" name="d2" style="width:40px;" placeholder="<?=GetMessage("CLEANMASTER_ACTION_DAY")?>"/>
    <input type="text" name="m2" style="width:40px;" placeholder="<?=GetMessage("CLEANMASTER_ACTION_MONTH")?>"/>
    <input type="text" name="y2" style="width:40px;" placeholder="<?=GetMessage("CLEANMASTER_ACTION_YEAR")?>"/><br/><br/>
    <br/>
    <a href="javascript:void(0)" class="action-process adm-btn adm-btn-save"><?=GetMessage('CLEANMASTER_ACTION_CLEANSTART')?></a>
</form>
