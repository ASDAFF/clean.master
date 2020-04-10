<?
$step28 = new \Acrit\Cleanmaster\Steps\Step28();
$tableList = $step28->getTableList();
?>

<h2><?=GetMessage("CLEANMASTER_ACTION_28")?></h2>
<p><?=GetMessage('CLEANMASTER_ACTION_28_DESCRIPTION')?></p>
<form>
	<?
	foreach ($tableList as $t) {
		?>
		<div><input name="t[]" type="checkbox" id="t-<?=$t['name']?>" <?if ($t['def_interface_checked'] == 'Y') {?> checked <?}?> value="<?=$t['name']?>">&nbsp;
			<label for="t-<?=$t['name']?>"><b <?if ($t['def_interface_checked'] != 'Y') {?> style="color:red;" <?}?>><?=$t['name']?> - <?=$t['total_size_mb']?>Mb</b> (<?=GetMessage("CLEANMASTER_ROWS")?> - <?=$t['table_rows']?>)
				<?=$t['description']?></label></div>
		<?
	}
	?>

	<p>&nbsp;</p>

	<?if ($isDemo == 1) {?>
		<a href="javascript:void(0)" class="action-process adm-btn adm-btn-save"><?=GetMessage('CLEANMASTER_ACTION_CLEANSTART')?></a>

		<?include __DIR__ . '/save_profile_btn.php';?>
	<?} else {?>
		<?=GetMessage('CLEANMASTER_ACTION_DEMO')?>
	<?}?>
</form>