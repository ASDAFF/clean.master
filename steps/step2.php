<?
/**
 * Copyright (c) 11/4/2020 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

$cleanTemplate = new CCleanTemplate;
?>
<h2><?=GetMessage("MASTER_ACTION_2")?></h2>
<form>
    <?$templates = $cleanTemplate->GetUnusedTemplates();?>
    <?if(count($templates)):?>
        <p><b><?=GetMessage('MASTER_ACTION_2_NOT_USED')?></b></p>
        <br/>
        <?foreach($templates as $template):?>
            <input type="checkbox" name="template_del[<?=$template?>]" value="<?=$template?>" checked="checked">
            <label for="template_del[<?=$template?>]"><?=$template?></label>
            <br/>
        <?endforeach?>
        <br/>
        <a href="javascript:void(0)" class="action-process adm-btn adm-btn-save"><?=GetMessage('MASTER_ACTION_CLEANSTART')?></a>
    <?else:?>
        <p><b><?=GetMessage('MASTER_ACTION_2_NOT_FOUND')?></b></p>
    <?endif?>
</form>
