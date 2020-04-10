<?php
$cleanIBlock = new CCleanIBlock(array());
$ib_list = $cleanIBlock->GetAllIblock();
?>
<h2><?=GetMessage("CLEANMASTER_ACTION_24")?></h2>
<br/>
<form>
    <?if(count($ib_list)):?>
        <p><b><?=GetMessage('CLEANMASTER_ACTION_24_NOT_USED')?></b></p>
        <br/>
	    <?foreach ($ib_list as $key => $value):
		    $id = 'step24_' . $value['ID'];
		    ?>
		    <?echo '<input type="checkbox" name="ib['.$value['ID'].']" id="'.$id.'">'." <label for='".$id."'>{$value['NAME']} ({$value['INACTIVE_CNT']})</label>".'</input><br />';?>
	    <?endforeach?>
        <br/>
		<?if ($isDemo == 1) {?>
        	<a href="javascript:void(0)" class="action-process adm-btn adm-btn-save"><?=GetMessage('CLEANMASTER_ACTION_CLEANSTART')?></a>

			<?include __DIR__ . '/save_profile_btn.php';?>
		<?}?>
    <?else:?>
        <p><b><?=GetMessage('CLEANMASTER_ACTION_24_NOT_FOUND')?></b></p>
    <?endif?>
</form>
