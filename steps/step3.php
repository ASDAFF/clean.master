<?php
/**
 * Copyright (c) 11/4/2020 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

$cleanIBlock = new CCleanIBlock;
$ib_list = $cleanIBlock->GetAllIblock();
?>
<h2><?=GetMessage("MASTER_ACTION_3")?></h2>
<br/>
<form>
    <?if(count($ib_list)):?>
        <p><b><?=GetMessage('MASTER_ACTION_3_NOT_USED')?></b></p>
        <br/>
        <?foreach ($ib_list as $key => $value):?>
            <?echo '<input type="checkbox" name="ib['.$value['ID'].']">'."{$value['NAME']} ({$value['INACTIVE_CNT']})".'</input><br />';?>
        <?endforeach?>
        <br/>
        <a href="javascript:void(0)" class="action-process adm-btn adm-btn-save"><?=GetMessage('MASTER_ACTION_CLEANSTART')?></a>
    <?else:?>
        <p><b><?=GetMessage('MASTER_ACTION_3_NOT_FOUND')?></b></p>
    <?endif?>
</form>
