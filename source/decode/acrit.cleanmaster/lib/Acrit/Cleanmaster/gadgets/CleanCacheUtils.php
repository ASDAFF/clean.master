<?php

namespace Acrit\Cleanmaster\gadgets;

class CleanCacheUtils
{
	public static function deleteFileCache($path)
	{
		if (strlen($path) == 0 || $path == '/')
			return false;

		$full_path = $_SERVER["DOCUMENT_ROOT"] . $path;

		$f = true;
		if (is_file($full_path) || is_link($full_path)) {
			if (@unlink($full_path))
				return true;
			return false;
		} elseif (is_dir($full_path)) {
			if ($handle = opendir($full_path)) {
				while (($file = readdir($handle)) !== false) {
					if ($file == "." || $file == "..")
						continue;

					if (!DeleteDirFilesEx($path . "/" . $file))
						$f = false;
				}
				closedir($handle);
			}
			return $f;
		}
		return false;
	}

	public static function getDirInfo($path)
	{
		$full_path = $path;
		$info = array(
			'count' => 0,
			'size' => 0
		);

		foreach (scandir($full_path) as $file) {
			if ($file != '.' && $file != '..') {
				$file_path = $full_path . DIRECTORY_SEPARATOR . $file;
				if (is_dir($file_path)) {
					$tmp = self::getDirInfo($file_path);

					$info['count'] += $tmp['count'];
					$info['size'] += $tmp['size'];
				} else {
					$info['count']++;
					$info['size'] += filesize($file_path);
				}
			}
		}

		return $info;
	}

	public static function deleteSeoCache()
	{
		$db_result = \CIBlock::GetList(
			array(),
			array(
				'SITE_ID' => SITE_ID,
				'ACTIVE' => 'Y',
			),
			false
		);
		while ($row = $db_result->Fetch()) {
			$ipropValues = new \Bitrix\Iblock\InheritedProperty\IblockValues((int)$row['ID']);
			$ipropValues->clearValues();
		}
	}

	public static function deleteManagedCache()
	{
		$cache = \Bitrix\Main\Application::getInstance()->getManagedCache();
		$cache->cleanAll();
	}

}