<?php

class CCleanModule extends TCleanMasterFunctions
{

	private $documentRoot;

	public function __construct()
	{
		$this->documentRoot = Bitrix\Main\Application::getDocumentRoot();
	}

	public function ModulesDelete()
	{
		$folders = array(
			"/local/modules",
			"/bitrix/modules",
		);
		foreach ($folders as $folder) {
			if (! is_dir($this->documentRoot.$folder)) {
				continue;
			}

			$handle = @opendir($this->documentRoot.$folder);
			if($handle){
				while (false !== ($dir = readdir($handle))){
					if(
						!isset($arModules[$dir]) &&
						is_dir($this->documentRoot.$folder."/".$dir) &&
						$dir!="." &&
						$dir!=".." &&
						strpos($dir, ".") !== false
					){
						$module_dir = $folder."/".$dir;

						ob_start();
						if ($info = CModule::CreateModuleObject($dir)) {
							$arModules[$dir]["IsInstalled"] = $info->IsInstalled();
							$arModules[$dir]["DIR"] = $module_dir;
						}
						$ignore = ob_get_clean();
					}
				}
				closedir($handle);
			}
		}
		foreach ($arModules as $module_id => $value) {
			if (!$value['IsInstalled']){
				DeleteDirFilesEx($value['DIR']);
			}
		}
	}

	/**
	 * Получаем данные для диагностики
	 *
	 * @param string $step
	 *
	 * @return bool
	 */
	public function GetDiagnosticData($step = false)
	{
		$folders = array(
			"/local/modules",
			"/bitrix/modules",
		);
		foreach($folders as $folder){
			if (! is_dir($this->documentRoot.$folder)) {
				continue;
			}

			$handle = @opendir($this->documentRoot.$folder);
			if ($handle) {
				while (false !== ($dir = readdir($handle))){
					if(
						!isset($arModules[$dir]) &&
						is_dir($this->documentRoot.$folder."/".$dir) &&
						$dir!="." &&
						$dir!=".." &&
						strpos($dir, ".") !== false
					){
						$module_dir = $folder."/".$dir;

						ob_start();
						if ($info = CModule::CreateModuleObject($dir)) {
							$arModules[$dir]["IsInstalled"] = $info->IsInstalled();
							$arModules[$dir]["DIR"] = $module_dir;
						}
						$ignore = ob_get_clean();
					}
				}
				closedir($handle);
			}
		}
		foreach ($arModules as $module_id => $value) {
			if (!$value['IsInstalled']) {
				$_SESSION['cleanmaster']['diagnostic']['module']['modules'][$module_id] = $this->GetDirSize($this->documentRoot.$value['DIR'].'/');
			}
		}
		return false;
	}
}
