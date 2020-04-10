<?php
	$cleanSite = new CCleanSite;
	$sites = $cleanSite->GetInactiveSites();
?>
<h2><?=GetMessage("CLEANMASTER_ACTION_1")?></h2>
<p><?=GetMessage('CLEANMASTER_ACTION_1_DESCRIPTION')?></p>
<br/>
<form>
	<?if(count($sites)):?>
		<p><b><?=GetMessage('CLEANMASTER_ACTION_1_NOT_USED')?></b></p>
		<br/>
		<?foreach ($sites as $arSite):?>
			<input type="checkbox" name="site_del[<?=$arSite['ID'];?>]" value="<?=$arSite['DIR'];?>" />
			<label for="site_del[<?=$arSite['ID'];?>]"><?=$arSite['ID'];?> (<?=$arSite['NAME']?>)</label><br/>
		<?endforeach?>
		<div class="deleted-iblocks"></div>
		<br/>
		<a href="javascript:void(0)" class="action-process adm-btn adm-btn-save"><?=GetMessage('CLEANMASTER_ACTION_CLEANSTART')?></a>
	<?else:?>
		<p><b><?=GetMessage('CLEANMASTER_ACTION_1_NOT_FOUND')?></b></p>
	<?endif?>
</form>
