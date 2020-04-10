<?php

class CCleanUpload extends TCleanMasterFunctions
{
    private $documentRoot;

	public $mediaCount;

	const IBLOCK_ELEMENTS_PER_STEP = 2000;

    public function __construct()
    {
        $this->documentRoot = Bitrix\Main\Application::getDocumentRoot();
    }

	/**
	 * —колько обрабатывать за шаг (2000 файлов)
	 * @param bool $count
	 * @return bool|int
	 */
	public function GetStepCount($count = false)
	{
		if ($count) {
			$this->mediaCount = $count;
		}

		if (! $this->mediaCount) {
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
		$arCount = $DB->Query("SELECT count(ID) FROM b_file")->Fetch();
		$arCount = $arCount['count(ID)'];
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
	 * ѕолучить строки из b_file
	 * @param $step
	 * @return array|bool
	 */
	public function GetRows($step)
	{
		global $DB;
		$count = $this->GetStepCount();
		$start = $step * $count;
		$end = $start + $count;

		$dbFiles = $DB->Query("SELECT * FROM b_file LIMIT $count OFFSET $start");
		while($arFile = $dbFiles->Fetch())
		{
			$files[] = $arFile;
		}
		return is_array($files) ? $files : false;
	}

	public function DeleteUnusedIBlockFiles($step)
	{
		// FIXME delete used items
		return false;

		global $DB;
		if (
			!is_array($_SESSION['cleanmaster']['diagnostic']['iblock']['files'])
			|| empty($_SESSION['cleanmaster']['diagnostic']['iblock']['files'])
			|| (count($_SESSION['cleanmaster']['diagnostic']['iblock']['files']) == 0)
		) {
			return false;
		}

		$count = 1000;
		$start = $step * $count;
		$end = $start + $count;
		$dbFiles = $DB->Query("SELECT ID FROM b_file WHERE `MODULE_ID`='iblock' AND `ID` NOT IN ("
									.implode(',', $_SESSION['cleanmaster']['diagnostic']['iblock']['files'])
								.") LIMIT $count OFFSET $start");
		$continue = false;
		while ($arFile = $dbFiles->Fetch()) {
			$continue = true;
			CFile::Delete($arFile['ID']);
		}
		return $continue;
	}

	/**
	 * возвращает файлы из папки /upload/cleanmaster/ в /upload/
	 * @deprecated
	 */
	public function RemoveAndRename()
	{
		// TODO
		return;

		$temp_upload    = $this->documentRoot.'/upload/cleanmaster/';

		$temp_upload2   = $this->documentRoot.'/upload/cleanmaster_tmp/';
		if (!file_exists($temp_upload2)) {
			mkdir($temp_upload2);
            $this->SetPermission($temp_upload2, 0777);
		}

		$upload         = $this->documentRoot.'/upload/';

		$dirs = scandir($temp_upload);
		foreach($dirs as $dir)
		{
			if($dir == '.' || $dir == '..')
				continue;

			rename($upload.$dir, $temp_upload2.$dir); // upload --> cleanmaster_tmp
			rename($temp_upload.$dir, $upload.$dir);  // cleanmaster --> upload

			$this->SetPermission($upload.$dir, BX_DIR_PERMISSIONS);
		}

		rename($temp_upload2, $temp_upload);
	}

	/**
	 * ѕереносит все зарегистрированные файлы из b_file в /upload/cleanmaster/
	 * @param $step
	 * @deprecated
	 */
	public function ClearUpload($step)
	{
		// TODO без завершающего вызова RemoveAndRename() удал€ет файлы
		return;

		$upload = $this->documentRoot.'/upload/';

		$temp_upload = $this->documentRoot.'/upload/cleanmaster/';
		if (!file_exists($temp_upload)) {
			mkdir($temp_upload);
			$this->SetPermission($temp_upload, 0777);
		}

		$records = $this->GetRows($step);

		foreach ($records as $record)
		{
			if (! file_exists($temp_upload.$record['SUBDIR'])) {
				$arSubDir = explode('/', $record['SUBDIR']);
				if ($this->GetPermission($upload.$arSubDir[0]) != '0777') {
					$this->SetPermission($upload.$arSubDir[0], 0777);
				}
				$arSubDirName = array($temp_upload);
				foreach ($arSubDir as $subdir) {
					$curCount = count($arSubDirName) - 1;
					$arSubDirName[] = $arSubDirName[$curCount++].$subdir.'/';
					if (!file_exists($arSubDirName[$curCount])) {
						mkdir($arSubDirName[$curCount]);
                        $this->SetPermission($arSubDirName[$curCount], 0777);
					}
				}
			}

			if (file_exists($upload.$record['SUBDIR'].'/'.$record['FILE_NAME'])) {
				rename($upload . $record['SUBDIR'] . '/' . $record['FILE_NAME'], $temp_upload . $record['SUBDIR'] . '/' . $record['FILE_NAME']);
			}
		}
	}
    public function DeleteTempDir()
    {
        $temp_upload = '/upload/cleanmaster/';
        $this->SetPermission($this->documentRoot.$temp_upload, 0777);
	    DeleteDirFilesEx($temp_upload);

	    if(file_exists($this->documentRoot.'/upload/tmp'))
			DeleteDirFilesEx('/upload/tmp/');

	    // its used in admin lists
		if(file_exists($this->documentRoot.'/upload/resize_cache'))
			DeleteDirFilesEx('/upload/resize_cache/');

		return false;
    }

	/**
	 * ѕолучаем размер папок и сохран€ем в сессию дл€ диагностики
	 *
	 * @param     $path
	 * @param int $level
	 *
	 * @return float|int
	 */
	private function GetUploadSize($path, $level = 1)
	{
		$files = scandir($path);
		$size = 0;
		if(is_array($files))
		{
			foreach($files as $file)
			{
				if($file == '.' || $file == '..')
					continue;
				if(is_dir($path.$file))
				{
					$dirSize = $this->GetUploadSize($path.$file.'/', $level+1);
					if($level == 1)
					{
						$_SESSION['cleanmaster']['diagnostic']['upload']['dirSize'][$file] = $dirSize;
					}
					$size += $dirSize;
				}
				else
					$size += filesize($path.$file) / 1024 / 1024;
			}
		}
		return $size;
	}

	/**
	 * @param $step
	 * @return bool false отстановить текст, ничего - продолжить
	 */
	public function GetDiagnosticIBlockData($step)
	{
		if(!$step || $step == 1)
		{
			$_SESSION['cleanmaster']['diagnostic']['upload']['iblock'] = array();
			$step = 1;
		}
		$dbRes = CIBlockElement::GetList(array('id' => 'asc'), array(), false, array('nPageSize' => self::IBLOCK_ELEMENTS_PER_STEP, 'iNumPage' => $step), array('ID', 'PREVIEW_PICTURE', 'DETAIL_PICTURE'));
		while($arRes = $dbRes->GetNext())
		{
			if(intval($arRes['PREVIEW_PICTURE']) > 0)
				$_SESSION['cleanmaster']['diagnostic']['iblock']['files'][] = $arRes['PREVIEW_PICTURE'];
			if(intval($arRes['DETAIL_PICTURE']) > 0)
				$_SESSION['cleanmaster']['diagnostic']['iblock']['files'][] = $arRes['DETAIL_PICTURE'];
		}
		$_SESSION['cleanmaster']['diagnostic']['iblock']['files'] = array_unique($_SESSION['cleanmaster']['diagnostic']['iblock']['files']);
		if($dbRes->NavPageCount <= $step)
		{
			// its unused TODO 2.1.7
			/*$size = 0;
			foreach($_SESSION['cleanmaster']['diagnostic']['iblock']['files'] as $file)
			{
				$path = CFile::GetPath($file);
				$arPath = explode('/', $path);
				if($arPath[2] == 'iblock')
					$size += filesize($this->documentRoot.$path) / 1024 / 1024;
			}
			$_SESSION['cleanmaster']['diagnostic']['upload']['iblock'] = $size;*/
			return false;
		}
	}

	/**
	 * @param $step
	 * @return bool false отстановить текст, ничего - продолжить
	 */
	public function GetDiagnosticSectionData($step)
	{
		if (!$step || $step == 1) {
			$step = 1;
		}
		$dbRes = CIBlockSection::GetList(array('id' => 'asc'), array(), false, array('ID', 'PICTURE', 'DETAIL_PICTURE'), array('nPageSize' => self::IBLOCK_ELEMENTS_PER_STEP, 'iNumPage' => $step));
		while($arRes = $dbRes->GetNext())
		{
			if(intval($arRes['PICTURE']) > 0)
				$_SESSION['cleanmaster']['diagnostic']['iblock']['files'][] = $arRes['PICTURE'];
			if(intval($arRes['DETAIL_PICTURE']) > 0)
				$_SESSION['cleanmaster']['diagnostic']['iblock']['files'][] = $arRes['DETAIL_PICTURE'];
		}
		$_SESSION['cleanmaster']['diagnostic']['iblock']['files'] = array_unique($_SESSION['cleanmaster']['diagnostic']['iblock']['files']);
		if($dbRes->NavPageCount <= $step)
		{
			// its unused TODO 2.1.7
			/*$size = 0;
			foreach($_SESSION['cleanmaster']['diagnostic']['iblock']['files'] as $file)
			{
				$path = CFile::GetPath($file);
				$arPath = explode('/', $path);
				if($arPath[2] == 'iblock')
					$size += filesize($this->documentRoot.$path) / 1024 / 1024;
			}
			$_SESSION['cleanmaster']['diagnostic']['upload']['iblock'] = $size;*/
			return false;
		}
	}

	public function GetDiagnosticPropsData($step)
	{
		if (!$step || $step == 1) {
			$step = 1;
		}

		$dbRes = CIBlockProperty::GetList(array('id' => 'asc'), array('PROPERTY_TYPE' => 'F'));
		$iblockList = $filter = $propertyList = array();
		while ($arRes = $dbRes->GetNext()) {
			$iblockList[]								= $arRes['IBLOCK_ID'];
			$propertyList[ $arRes['IBLOCK_ID'] ][]		= $arRes['ID'];
		}

		if (empty($propertyList)) {
			return false;
		}

		$filter['IBLOCK_ID'] = $iblockList;

		$dbElem = \CIBlockElement::GetList(array('id' => 'asc'), $filter, false, array('nPageSize' => self::IBLOCK_ELEMENTS_PER_STEP, 'iNumPage' => $step), array('ID', 'IBLOCK_ID'));

		while ($fields = $dbElem->Fetch())
		{
			foreach ($propertyList[ $fields['IBLOCK_ID'] ] as $propId)
			{
				$prop = false;

				$db_props = CIBlockElement::GetProperty(
					$fields["IBLOCK_ID"],
					$fields['ID'],
					array("sort" => "asc"),
					array("EMPTY" => "N", 'ID' => $propId)
				);
				while ($ar_props = $db_props->GetNext()) {
					if (intval($ar_props['VALUE']) <= 0) {
						continue;
					}

					if ($ar_props['MULTIPLE'] == "Y") {
						$prop[] = $ar_props['VALUE'];
					} else {
						$prop = $ar_props['VALUE'];
					}
				}
				if ($prop !== false) {
					if (is_array($prop)) {
						foreach ($prop as $val) {
							$_SESSION['cleanmaster']['diagnostic']['iblock']['files'][] = $val;
						}
					} else {
						if (intval($prop) > 0) {
							$_SESSION['cleanmaster']['diagnostic']['iblock']['files'][] = $prop;
						}
					}
				}
			}
		}

		$_SESSION['cleanmaster']['diagnostic']['iblock']['files'] = array_unique($_SESSION['cleanmaster']['diagnostic']['iblock']['files']);
		if($dbElem->NavPageCount <= $step)
		{
			// its unused TODO 2.1.7
			/*$size = 0;
			foreach($_SESSION['cleanmaster']['diagnostic']['iblock']['files'] as $file)
			{
				$path = CFile::GetPath($file);
				$arPath = explode('/', $path);
				if($arPath[2] == 'iblock')
					$size += filesize($this->documentRoot.$path) / 1024 / 1024;
			}
			$_SESSION['cleanmaster']['diagnostic']['upload']['iblock'] = $size;*/
			return false;
		}
	}

	public function GetDiagnosticData($step)
	{
		$upload = $this->documentRoot.'/upload/';
		$records = $this->GetRows(intval($step));

		if($records === false)
			return false;

		if(!$step)
		{
			$_SESSION['cleanmaster']['diagnostic']['upload']['dbSize'] = 0;
			$_SESSION['cleanmaster']['diagnostic']['upload']['dirs'] = array();
			//exec("du -h --max-depth=1 $upload", $output);
			$_SESSION['cleanmaster']['diagnostic']['upload']['dirSize']['.'] = $this->GetUploadSize($this->documentRoot.'/upload/');

			//
			// files data dont need yet
			//
			return false;
		}

		foreach($records as $record)
		{
			$arPath = explode('/', $record['SUBDIR']);
			if(is_array($arPath) && !empty($arPath)) {
				if (!array_key_exists($arPath[0], $_SESSION['cleanmaster']['diagnostic']['upload']['dirs'])) {
					$_SESSION['cleanmaster']['diagnostic']['upload']['dirs'][$arPath[0]] = 0;
				}
			}
			if(file_exists($upload.$record['SUBDIR'].'/'.$record['FILE_NAME']))
			{
				$fileSize = filesize($upload.$record['SUBDIR'].'/'.$record['FILE_NAME']) / 1024 / 1024;
				$_SESSION['cleanmaster']['diagnostic']['upload']['dirs'][$arPath[0]] += $fileSize;
				$_SESSION['cleanmaster']['diagnostic']['upload']['dbSize'] += $fileSize;
			}
		}
	}

} // end class


