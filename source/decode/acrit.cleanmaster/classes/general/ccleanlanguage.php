<?php
class CCleanLanguage extends TCleanMasterFunctions
{
	private $documentRoot;

	public function __construct()
	{
		$this->documentRoot = Bitrix\Main\Application::getDocumentRoot();
	}

	function GetLanguages()
	{
		$langs = array();
		$rsLang = CLanguage::GetList($by = "lid", $order = "desc", Array());
		while ($arLang = $rsLang->Fetch()) {
			$langs[$arLang['LID']] = $arLang['NAME'];
		}
		return $langs;
	}

	function ClearLangs(array $needLangs)
	{
		$dirs = scandir($this->documentRoot . '/bitrix/', SCANDIR_SORT_NONE);
		$dirs = $this->PathFilter($dirs);
		$except = array('backup', 'modules', 'cache', 'managed_cache', 'stack_cache', 'wizards');
		foreach ($dirs as $dir) {
			if (in_array($dir, $except) || !is_dir($this->documentRoot . '/bitrix/' . $dir))
				continue;
			$this->ClearLangFile($this->documentRoot . '/bitrix/' . $dir, $needLangs);
		}
		if (file_exists($this->documentRoot . '/local/'))
			$this->ClearLangFile($this->documentRoot . '/local/', $needLangs);

		$langs = $this->GetLanguages();
		foreach ($langs as $lid => $name) {
			if (!in_array($lid, $needLangs)) {
				CLanguage::Delete($lid);
			}
		}
	}

	function ClearLangFile($rootDir, array $needLangs)
	{
		$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($rootDir), RecursiveIteratorIterator::SELF_FIRST);
		$dirToDelete = array();
		foreach ($iterator as $item) {
			if ($item->isDir()) {
				$dirName = $item->getBasename();
				if ($dirName == '.' || $dirName == '..')
					continue;
				$parentName = basename($item->getPath());
				if ($parentName == 'lang') {
					$relatedPath = str_replace(['\\', $this->documentRoot], ['/', ''], $item->getRealPath());
					if (! in_array($dirName, $needLangs)) {
						$dirToDelete[] = $relatedPath;
					}
				}
			}
		}

		foreach ($dirToDelete as $dir) {
			DeleteDirFilesEx($dir);
		}
	}

	/**
	 * Получаем данные для диагностики
	 */
	public function GetDiagnosticData($step = false)
	{
		$dirs = scandir($this->documentRoot . '/bitrix/', SCANDIR_SORT_NONE);
		$this->DiagnosticGetDirSize($this->documentRoot . '/bitrix/');
		$dirs = $this->PathFilter($dirs);

		$except = array('backup', 'modules', 'cache', 'managed_cache', 'stack_cache', 'wizards');
		foreach ($dirs as $dir) {
			if (in_array($dir, $except) || !is_dir($this->documentRoot . '/bitrix/' . $dir)) {
				continue;
			}
			$this->DiagnosticFindDir($this->documentRoot . '/bitrix/' . $dir);
		}
		if (file_exists($this->documentRoot . '/local/')) {
			$this->DiagnosticFindDir($this->documentRoot . '/local/');
		}

		foreach ($_SESSION['cleanmaster']['diagnostic']['lang']['lang_dirs'] as $dir) {
			$arDir = explode('/', $dir);

			if (!isset($_SESSION['cleanmaster']['diagnostic']['lang']['langs'][ $arDir[count($arDir) - 1] ])) {
				$_SESSION['cleanmaster']['diagnostic']['lang']['langs'][ $arDir[count($arDir) - 1] ] = 0;
			}
			$_SESSION['cleanmaster']['diagnostic']['lang']['langs'][ $arDir[count($arDir) - 1] ]
				+= $_SESSION['cleanmaster']['diagnostic']['lang']['dirs'][$this->documentRoot . $dir];
		}
		unset($_SESSION['cleanmaster']['diagnostic']['lang']['lang_dirs'], $_SESSION['cleanmaster']['diagnostic']['lang']['dirs']);

		return false;
	}

	function DiagnosticFindDir($rootDir)
	{
		$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($rootDir), RecursiveIteratorIterator::SELF_FIRST);
		$dirToDelete = array();
		foreach ($iterator as $item) {
			if ($item->isDir()) {
				$dirName = $item->getBasename();

				if ($dirName == '.' || $dirName == '..') {
					continue;
				}

				$parentName = basename($item->getPath());
				if ($parentName == 'lang') {
					$relatedPath = str_replace(['\\', $this->documentRoot], ['/', ''], $item->getRealPath());
					$dirToDelete[] = $relatedPath;
				}
			}
		}

		foreach ($dirToDelete as $dir) {
			$_SESSION['cleanmaster']['diagnostic']['lang']['lang_dirs'][] = $dir;
		}
	}

	protected function DiagnosticGetDirSize($path)
	{
		$files = scandir($path, SCANDIR_SORT_NONE);
		$size = 0;
		if (is_array($files)) {
			foreach ($files as $file) {
				if ($file == '.' || $file == '..')
					continue;
				if (is_dir($path . $file)) {
					$dirSize = $this->DiagnosticGetDirSize($path . $file . '/');
					$_SESSION['cleanmaster']['diagnostic']['lang']['dirs'][$path . $file] = $dirSize;
					$size += $dirSize;
				} else
					$size += filesize($path . $file) / 1024 / 1024;
			}
		}
		return $size;
	}
}
