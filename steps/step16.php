<?php
/**
 * Copyright (c) 11/4/2020 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

$cleanLanguages = new CCleanLanguage();
$not_used_langs = $cleanLanguages->GetLanguages();
?>
<h2><?=GetMessage("MASTER_ACTION_16")?></h2>
<p><?=GetMessage('MASTER_ACTION_16_DESCRIPTION')?></p>
<br/>
<form>
    <?foreach ($not_used_langs as $lid => $name):?>
        <input type="checkbox" name="lang_del[<?=$lid;?>]" value="<?=$lid;?>"/>
        <label for="lang_del[<?=$lid;?>]"><?=$name;?></label><br/>
    <?endforeach?>
    <br/>
    <a href="javascript:void(0)" class="action-process adm-btn adm-btn-save"><?=GetMessage('MASTER_ACTION_CLEANSTART')?></a>
</form>
