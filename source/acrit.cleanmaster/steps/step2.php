<?
    $cleanTemplate = new CCleanTemplate;
?>
<h2><?=GetMessage("CLEANMASTER_ACTION_2")?></h2>
<form>
    <?$templates = $cleanTemplate->GetUnusedTemplates();?>
    <?if(count($templates)):?>
        <p><b><?=GetMessage('CLEANMASTER_ACTION_2_NOT_USED')?></b></p>
        <br/>
        <?foreach($templates as $template):?>
            <input type="checkbox" name="template_del[<?=$template?>]" value="<?=$template?>" checked="checked">
            <label for="template_del[<?=$template?>]"><?=$template?></label>
            <br/>
        <?endforeach?>
        <br/>
        <a href="javascript:void(0)" class="action-process adm-btn adm-btn-save"><?=GetMessage('CLEANMASTER_ACTION_CLEANSTART')?></a>
    <?else:?>
        <p><b><?=GetMessage('CLEANMASTER_ACTION_2_NOT_FOUND')?></b></p>
    <?endif?>
</form>
