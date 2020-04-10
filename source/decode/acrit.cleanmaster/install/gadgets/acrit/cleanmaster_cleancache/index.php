<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

if (!CModule::IncludeModule('acrit.cleanmaster')) {
	return;
}

use \Acrit\Cleanmaster\gadgets\CleanCacheUtils;

IncludeModuleLangFile(__FILE__);

$cache_types = array(
	'cache' => array(
		'name' => GetMessage('GT_RM_UNGOVERNABLE_CACHE'),
		'path' => '/bitrix/cache',
	),
	'managed_cache' => array(
		'name' => GetMessage('GT_RM_MANAGED_CACHE'),
		'path' => '/bitrix/managed_cache',
	),
	'stack_cache' => array(
		'name' => GetMessage('GT_RM_STACK_CACHE'),
		'path' => '/bitrix/stack_cache',
	),
	'resize_cache' => array(
		'name' => GetMessage('GT_RM_RESIZE_CACHE'),
		'path' => '/upload/resize_cache',
	),
	'seo_cache' => array(
		'name' => GetMessage('GT_RM_SEO_CACHE'),
	),
);

if (!empty($_POST['cm_gt_rm_cache'])) {
	if (isset($_POST['cm_gt_rm_cache']['clear_all'])) {
		foreach ($cache_types as $cache_key => $cache) {
			if (isset($cache['path'])) {
				CleanCacheUtils::deleteFileCache($cache['path']);
			}
		}
		CleanCacheUtils::deleteManagedCache();
		CleanCacheUtils::deleteSeoCache();
		unset($_SESSION[basename(__DIR__)]);
	}
	if (isset($_POST['cm_gt_rm_cache']['clear_cache'])) {
		foreach (array('cache', 'managed_cache', 'stack_cache') as $key) {
			$cache = $cache_types[$key];
			CleanCacheUtils::deleteFileCache($cache['path']);
			if ('managed_cache' === $key) {
				CleanCacheUtils::deleteManagedCache();
			}
		}
		unset($_SESSION[basename(__DIR__)]);
	}
	if (isset($_POST['cm_gt_rm_cache']['clear'])) {
		$cache_key = array_keys($_POST['cm_gt_rm_cache']['clear']);
		reset($cache_key);
		$cache_key = current($cache_key);

		if (isset($cache_types[$cache_key])) {
			$cache = $cache_types[$cache_key];
			if ($cache['path']) {
				CleanCacheUtils::deleteFileCache($cache['path']);
				if ('managed_cache' === $cache_key) {
					CleanCacheUtils::deleteManagedCache();
				}
				unset($_SESSION[basename(__DIR__)][$cache_key]);
			} else if ('seo_cache' === $cache_key) {
				CleanCacheUtils::deleteSeoCache();
			}
		}
	}
	LocalRedirect('/bitrix/admin/');
}

// get sizes of cache
foreach ($cache_types as $cache_key => $cache) {
	if (isset($cache['path'])) {

		if (isset($_SESSION[basename(__DIR__)][$cache_key])) {
			$info = $_SESSION[basename(__DIR__)][$cache_key];
		} else {
			$info = CleanCacheUtils::getDirInfo($_SERVER['DOCUMENT_ROOT'] . $cache['path']);
			$_SESSION[basename(__DIR__)][$cache_key] = $info;
		}

		$cache['dir_file_count'] = $info['count'];
		$cache['dir_size'] = round($info['size'] / 1024 / 1024, 2) . ' ' . GetMessage('GT_RM_CACHE_SIZE_MB');
	} else {
		$cache['dir_file_count'] = '';
		$cache['dir_size'] = '';
	}

	$cache_types[$cache_key] = $cache;
}
unset($info);
?>

<style>
	#cm_gt_rm_cache_gadget { background: #f5f9f9; border-top: 1px solid #d7e0e8; }

	#cm_gt_rm_cache_gadget table { width: 100%; border-collapse: collapse; }

	#cm_gt_rm_cache_gadget table th,
	#cm_gt_rm_cache_gadget table td { vertical-align: top; text-align: left; padding: 10px; border-bottom: 1px solid #fff; }

	#cm_gt_rm_cache_gadget form { padding: 10px; }
</style>

<div id="cm_gt_rm_cache_gadget">
	<table>
		<tr>
			<th><?= GetMessage('GT_RM_CACHE_TYPE') ?></th>
			<th><?= GetMessage('GT_RM_CACHE_PATH') ?></th>
			<th><?= GetMessage('GT_RM_CACHE_DIR_FILES_COUNT') ?></th>
			<th><?= GetMessage('GT_RM_CACHE_DIR_SIZE') ?></th>
			<th></th>
		</tr>

		<? foreach ($cache_types as $cache_key => $cache): ?>
			<tr>
				<td><?= $cache['name'] ?></td>
				<? if ($cache_key == 'seo_cache'): ?>
					<td>
						<?= GetMessage('GT_RM_CACHE_SEO_INFO') ?>
					</td>
					<td>-</td>
					<td>-</td>
				<? else: ?>
					<td>
						<? if (!empty($cache['path'])): ?>
							<a href="/bitrix/admin/fileman_admin.php?lang=ru&path=<?= $cache['path'] ?>">
								<?= $cache['path'] ?>
							</a>
						<? endif; ?>
					</td>
					<td><?= $cache['dir_file_count'] ?></td>
					<td><?= $cache['dir_size'] ?></td>
				<? endif; ?>
				<td>
					<form method="post" action="">
						<input type="submit"
							   name="cm_gt_rm_cache[clear][<?= $cache_key ?>]"
							   value="<?= GetMessage('GT_RM_CLEAN') ?>"
							   class="adm-btn-save">
					</form>
				</td>
			</tr>
		<? endforeach; ?>

	</table>

	<form method="post" action="">
		<input type="submit"
			   name="cm_gt_rm_cache[clear_cache]"
			   value="<?= GetMessage('GT_RM_CLEAN_CACHE') ?>"
			   class="adm-btn-save"
			   title="<?= GetMessage('GT_RM_CLEAN_CACHE_TITLE') ?>">
		<input type="submit"
			   name="cm_gt_rm_cache[clear_all]"
			   value="<?= GetMessage('GT_RM_CLEAN_ALL') ?>"
			   class="adm-btn-save"
			   title="<?= GetMessage('GT_RM_CLEAN_ALL_TITLE') ?>">
	</form>
</div>
