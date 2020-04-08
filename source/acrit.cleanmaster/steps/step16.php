<?php
$cleanLanguages = new CCleanLanguage();
$not_used_langs = $cleanLanguages->GetLanguages();
?>
<h2><?=GetMessage("CLEANMASTER_ACTION_16")?></h2>
<p><?=GetMessage('CLEANMASTER_ACTION_16_DESCRIPTION')?></p>
<br/>
<form>
    <?foreach ($not_used_langs as $lid => $name):?>
        <input type="checkbox" name="lang_del[<?=$lid;?>]" value="<?=$lid;?>"/>
        <label for="lang_del[<?=$lid;?>]"><?=$name;?></label><br/>
    <?endforeach?>
    <br/>
    <a href="javascript:void(0)" class="action-process adm-btn adm-btn-save"><?=GetMessage('CLEANMASTER_ACTION_CLEANSTART')?></a>
</form>
