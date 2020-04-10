<?php
$cleanIBlock = new CCleanIBlock;
$ib_list = $cleanIBlock->GetAllIblock();
?>
<h2><?=GetMessage("CLEANMASTER_ACTION_3")?></h2>
<br/>
<form>
    <?if(count($ib_list)):?>
        <p><b><?=GetMessage('CLEANMASTER_ACTION_3_NOT_USED')?></b></p>
        <br/>
        <?foreach ($ib_list as $key => $value):?>
            <?echo '<input type="checkbox" name="ib['.$value['ID'].']">'."{$value['NAME']} ({$value['INACTIVE_CNT']})".'</input><br />';?>
        <?endforeach?>
        <br/>
        <a href="javascript:void(0)" class="action-process adm-btn adm-btn-save"><?=GetMessage('CLEANMASTER_ACTION_CLEANSTART')?></a>
    <?else:?>
        <p><b><?=GetMessage('CLEANMASTER_ACTION_3_NOT_FOUND')?></b></p>
    <?endif?>
</form>
