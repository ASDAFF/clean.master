<?php

use \Acrit\Cleanmaster\Data\UploadBfileindexTable,
	\Acrit\Cleanmaster\Data\UploadFilelostTable;


class CCleanUpload extends TCleanMasterFunctions
{
	private $documentRoot;

	public $mediaCount;


	public function __construct()
	{
		$this->documentRoot = Bitrix\Main\Application::getDocumentRoot();
	}

	/**
	 * Сколько обрабатывать за шаг (2000 файлов)
	 *
	 * @param bool $count
	 *
	 * @return bool|int
	 */
	public function GetStepCount($count = false)
	{
		if ($count) {
			$this->mediaCount = $count;
		}

		if (!$this->mediaCount) {
			$this->mediaCount = 2000;
		}

		return $this->mediaCount;
	}

	/**
	 * получить кол-во записей в таблице b_file
	 * @return integer
	 */
	public function GetCount()
	{
		global $DB;
		$arCount = $DB->Query("SELECT count(ID) AS CNT FROM b_file")->Fetch();
		$arCount = (int)$arCount['CNT'];
		return $arCount;
	}

	/**
	 * получить кол-во записей в таблице b_file
	 * @return integer
	 * @deprecated
	 */
	public function GetCountEx()
	{
		return 0;
	}

	/**
	 * Получить строки из b_file
	 *
	 * @param $step
	 *
	 * @return array|bool
	 */
	public function GetRows($step)
	{
		global $DB;
		$count = $this->GetStepCount();
		$start = $step * $count;

		$dbFiles = $DB->Query("SELECT * FROM b_file LIMIT $count OFFSET $start");
		while ($arFile = $dbFiles->Fetch()) {
			$files[] = $arFile;
		}
		return is_array($files) ? $files : false;
	}

	/**
	 * Получить ВСЕ строки из b_file (cron)
	 * @return array|bool
	 */
	public function GetRowsFull()
	{
		global $DB;
		$files = array();

		$dbFiles = $DB->Query("SELECT * FROM b_file");
		while ($arFile = $dbFiles->Fetch()) {
			$files[] = $arFile;
		}
		return is_array($files) ? $files : false;
	}

	public function DeleteTempDir($step = false, $delete_resize_cache = 'N')
	{
		if (file_exists($this->documentRoot . '/upload/tmp')) {
			DeleteDirFilesEx('/upload/tmp/');
		}

		if ($delete_resize_cache == 'Y') {
			// its used in admin lists
			if (file_exists($this->documentRoot . '/upload/resize_cache')) {
				DeleteDirFilesEx('/upload/resize_cache/');
			}

			// drop resize_cache need drop cache
			$cleanCache = new CCleanCache;
			$cleanCache->CacheClear();
		}

		if (file_exists($this->documentRoot . '/upload/1c_catalog')) {
			DeleteDirFilesEx('/upload/1c_catalog/');
		}

		$temp_upload = '/upload/cleanmaster/';
		$this->SetPermission($this->documentRoot . $temp_upload, 0777);
		DeleteDirFilesEx($temp_upload);

		return false;
	}

	/**
	 * Откат с папки /upload/cleanmaster/
	 */
	public function RevertTmpDir()
	{
		$path = $this->documentRoot . '/upload/cleanmaster';
		$idir = new RecursiveIteratorIterator(
			new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST
		);
		foreach ($idir as $v) {
			/* @var $v \SplFileInfo */
			if ($v->isFile()) {
				$p = str_replace('/upload/cleanmaster', '', $idir->key());
				CheckDirPath($p);
				copy($idir->key(), $p);
			}
		}
		$this->ClearTmpDir();
	}

	public function ClearTmpDir()
	{
		DeleteDirFilesEx('/upload/cleanmaster/');
	}

	public function fillBfileIndex($step, $noLimit = false)
	{
		$step = (int)$step;
		if ($step == 0) {
			UploadBfileindexTable::clearTable();
		}

		$cntSteps = $this->getCountStepsBfileIndex();

		if ($noLimit === true) {
			$records = $this->GetRowsFull();
		} else {
			$records = $this->GetRows($step);
		}

		foreach ($records as $record) {
			$record['F_PATH'] = \CFile::GetFileSRC($record, false, false);
			UploadBfileindexTable::add(array(
				'F_PATH' => $record['F_PATH'],
				'FILE_ID' => $record['ID']
			));
		}

		$step++;
		if ($step > $cntSteps - 1) { // step from zero
			return false;
		} else {
			return $step;
		}
	}

	public function getCountStepsBfileIndex()
	{
		$cntRows = $this->GetCount();
		$cntPerStep = $this->GetStepCount();
		$cntSteps = ceil($cntRows / $cntPerStep);

		return $cntSteps;
	}

	const PER_STEP_PROCESS_FILES = 1000;

	public function getCountStepsDiagnosticData()
	{
		$cntFiles = 0;

		$directory = new \RecursiveDirectoryIterator($this->documentRoot . '/upload',
			\FilesystemIterator::SKIP_DOTS | \FilesystemIterator::FOLLOW_SYMLINKS
		);
		$filter = new UpoadFilterIterator($directory);
		$fsi = new \RecursiveIteratorIterator($filter);
		foreach ($fsi as $f) {
			$cntFiles++;
		}

		return ceil($cntFiles / self::PER_STEP_PROCESS_FILES);
	}


	public function GetDiagnosticData($step, $noLimit = false)
	{
		$step = (int)$step;
		if ($step == 0) {
			$_SESSION['cleanmaster']['diagnostic']['upload']['dbSize'] = 0;
			$_SESSION['cleanmaster']['diagnostic']['upload']['dirs'] = array();
			//$_SESSION['cleanmaster']['diagnostic']['upload']['dirSize']['.'] = $this->GetUploadSize($this->documentRoot.'/upload/');
			UploadFilelostTable::clearTable();
			$_SESSION['cleanmaster']['diagnostic']['upload']['lost_file_size'] = 0;
		}

		$directory = new \RecursiveDirectoryIterator($this->documentRoot . '/upload',
			\FilesystemIterator::SKIP_DOTS | \FilesystemIterator::FOLLOW_SYMLINKS
		);
		$filter = new UpoadFilterIterator($directory);
		$fsi = new \RecursiveIteratorIterator($filter);


		$cntPerStep = self::PER_STEP_PROCESS_FILES;
		$fileISkip = $step * $cntPerStep;
		$fileI = 0;

		$filenames = array();
		foreach ($fsi as $f) {
			/* @var $f SplFileInfo */
			if ($fileISkip > 0) {
				$fileISkip--;
				continue;
			}

			$filename = str_replace('\\', '/', $f->getRealPath());
			$filename = str_replace($this->documentRoot, '', $filename);

			$filenames[] = $filename;

			if (!$noLimit) {
				if ((++$fileI % $cntPerStep) === 0) {
					break;
				}
			}
		}

		if (count($filenames) > 0) {
			$filenamesRegistered = array();
			$rsRows = UploadBfileindexTable::getList(array(
				'select' => array('F_PATH'),
				'filter' => array('=F_PATH' => $filenames),
			));
			while ($row = $rsRows->fetch()) {
				$filenamesRegistered[] = $row['F_PATH'];
			}
			$toDelete = array_diff($filenames, $filenamesRegistered);
			foreach ($toDelete as $filename) {
				UploadFilelostTable::add(array(
					'F_PATH' => $filename,
					'SIZE' => filesize($this->documentRoot . $filename)
				));
			}
		}

		if ($fileISkip > 0) {
			$_SESSION['cleanmaster']['diagnostic']['upload']['lost_file_size'] = UploadFilelostTable::getLostSize();
		}
		return ($fileISkip == 0);
	}

	/**
	 * Для интерфейса, отчет какие файлы будут удалены
	 *
	 * @param $dirSizes = array() заранее известные размеры папок (если известны)
	 * @param $limit = 0 сколько строк выбрать сверху
	 * @param $subselectTmps = true
	 *
	 * @return array
	 * @throws \Bitrix\Main\ArgumentException
	 */
	public function getDiagnosticListFiles($dirSizes = array(), $limit = 0, $subselectTmps = true)
	{
		$r = $ra = array();

		$arS = array(
			'select' => array('*'),
			'order' => array('ID' => 'asc')
		);
		if ((int)$limit > 0) {
			$arS['limit'] = $limit;
		}
		$rows = UploadFilelostTable::getList($arS);
		while ($row = $rows->fetch()) {
			$r[] = $row['F_PATH'] . ' (' . \CFile::FormatSize($row['SIZE']) . ')';
			$row['SIZE'] = 0; // recalc with getDiagnosticListFilesFullSize() method
			$ra[] = $row;
		}

		if ($subselectTmps) {
			$tmpDirsAll = array();
			$tmpDirs = array(
				'/upload/tmp/',
				'/upload/resize_cache/',
				'/upload/1c_catalog/',
				'/upload/cleanmaster/'
			);
			foreach ($tmpDirs as &$d) {
				$dt = str_replace(array('/upload/', '/'), '', $d);
				if (isset($dirSizes[$dt])) {
					$size = $dirSizes[$dt];
				} else {
					$size = $this->GetDirSize($this->documentRoot . $d) * 1024 * 1024;
				}
				$tmpDirsAll[] = array(
					'F_PATH' => $d,
					'SIZE' => $size
				);
				$d = $d . ' (' . \CFile::FormatSize($size) . ')';
			}
			unset($d);

			$r = array_merge($r, $tmpDirs);
			$ra = array_merge($ra, $tmpDirsAll);
			unset($tmpDirs, $tmpDirsAll);
		}

		$returMod = array(
			'r' => $r,
			'ra' => $ra
		);

		return $returMod;
	}

	public function getDiagnosticListFilesFullSize()
	{
		global $DB;
		$r = $DB->Query('SELECT SUM(SIZE) AS `SUM` FROM ' . UploadFilelostTable::getTableName())->Fetch();
		return (float)$r['SUM'];
	}

	public function DeleteLostFiles($step, $noLimit = false)
	{
		$temp_upload = $this->documentRoot . '/upload/cleanmaster/';
		if (!file_exists($temp_upload)) {
			/** @noinspection MkdirRaceConditionInspection */
			mkdir($temp_upload);
			$this->SetPermission($temp_upload, 0777);
		}

		$params = array(
			'select' => array('*'),
			'order' => array('ID' => 'asc'),
			'limit' => 200,
		);
		if ($noLimit === true) {
			unset($params['limit']);
		}
		$rows = UploadFilelostTable::getList($params);
		$bFinded = false;

		while ($row = $rows->fetch()) {
			$bFinded = true;

			CheckDirPath($temp_upload . $row['F_PATH']);
			copy($this->documentRoot . $row['F_PATH'], $temp_upload . $row['F_PATH']);
			unlink($this->documentRoot . $row['F_PATH']);
			UploadFilelostTable::delete($row['ID']);
		}

		return $bFinded;
	}

	public function getCountStepsDeleteLostFiles()
	{
		return ceil(UploadFilelostTable::getCntRows() / 200);
	}


	/**
	 * Получаем размер папок и сохраняем в сессию для диагностики
	 * (только временных папок tmp + resize_cache)
	 *
	 * @param     $path
	 * @param int $level
	 *
	 * @return float|int
	 * @deprecated
	 */
	private function GetUploadSize($path, $level = 1)
	{
		$files = scandir($path, SCANDIR_SORT_NONE);
		$size = 0;
		if (is_array($files)) {
			foreach ($files as $file) {
				if ($file == '.' || $file == '..' || ($level == 1 && !in_array($file, array('tmp', 'cleanmaster', 'resize_cache', '1c_catalog')))) {
					continue;
				}
				if (is_dir($path . $file)) {
					$dirSize = $this->GetUploadSize($path . $file . '/', $level + 1);
					if ($level == 1) {
						$_SESSION['cleanmaster']['diagnostic']['upload']['dirSize'][$file] = $dirSize;
					}
					$size += $dirSize;
				} else
					$size += filesize($path . $file) / 1024 / 1024;
			}
		}
		return $size;
	}


} // end class


class UpoadFilterIterator extends \RecursiveFilterIterator
{
	public function accept()
	{
		$filename = $this->current()->getRealPath();
		$filename = str_replace('\\', '/', $filename); //win

		$fileInUploadRoot = is_file($filename) && (dirname($filename) == Bitrix\Main\Application::getDocumentRoot() . '/upload');
		return preg_match('#/upload/((iblock)|(main)|(uf)|(medialibrary)|(form)|(blog)|(support)|(forum))#', $filename) && !$fileInUploadRoot;
	}
}