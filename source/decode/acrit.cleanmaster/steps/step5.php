<?php
$cleanUpload = new CCleanUpload;

$sizeCleanTmpDir = $cleanUpload->GetDirSize($_SERVER['DOCUMENT_ROOT'] . '/upload/cleanmaster/');
?>
<h2><?=GetMessage("CLEANMASTER_ACTION_5")?></h2>
<p><?=GetMessage('CLEANMASTER_ACTION_5_DESCRIPTION')?></p>

<?=BeginNote()?>
<?=GetMessage('CLEANMASTER_ACTION_5_TMP_DIR_DESCRIPTION', array('#SIZE_MB#' => $sizeCleanTmpDir))?>
<?if ($sizeCleanTmpDir > 0) {?>
	<?=GetMessage('CLEANMASTER_ACTION_5_TMP_DIR_DESCRIPTION_DOING', array('#SIZE_MB#' => $sizeCleanTmpDir))?>
<?}?>
<?=EndNote()?>

<br/>
<form>
	<input type="checkbox" name="delete_resize_cache" value="Y" /> <?=GetMessage("CLEANMASTER_ACTION_5_DELETE_RESIZE_CACHE")?><br/><br/>

	<?if ($isDemo == 1) {?>
		<a href="javascript:void(0)" class="action-process adm-btn adm-btn-save"><?=GetMessage('CLEANMASTER_ACTION_CLEANSTART')?></a>
	<?}?>
	<?include __DIR__ . '/save_profile_btn.php';?>
</form>
<?

// restore finded data
if ($cleanUpload->getCountStepsDeleteLostFiles() > 0) {
	?>
	<script type="text/javascript">
	$(function(){
		$('#diagnostic').data('restore', 1).click().data('restore', false);
	});
	</script>
	<?
}

?>
