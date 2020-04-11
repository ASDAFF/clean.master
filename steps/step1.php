<?php
/**
 * Copyright (c) 11/4/2020 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

$cleanSite = new CCleanSite;
	$sites = $cleanSite->GetInactiveSites();
?>
<h2><?=GetMessage("MASTER_ACTION_1")?></h2>
<p><?=GetMessage('MASTER_ACTION_1_DESCRIPTION')?></p>
<br/>
<form>
	<?if(count($sites)):?>
		<p><b><?=GetMessage('MASTER_ACTION_1_NOT_USED')?></b></p>
		<br/>
		<?foreach ($sites as $arSite):?>
			<input type="checkbox" name="site_del[<?=$arSite['ID'];?>]" value="<?=$arSite['DIR'];?>" />
			<label for="site_del[<?=$arSite['ID'];?>]"><?=$arSite['ID'];?> (<?=$arSite['NAME']?>)</label><br/>
		<?endforeach?>
		<div class="deleted-iblocks"></div>
		<br/>
		<a href="javascript:void(0)" class="action-process adm-btn adm-btn-save"><?=GetMessage('MASTER_ACTION_CLEANSTART')?></a>
	<?else:?>
		<p><b><?=GetMessage('MASTER_ACTION_1_NOT_FOUND')?></b></p>
	<?endif?>
</form>
