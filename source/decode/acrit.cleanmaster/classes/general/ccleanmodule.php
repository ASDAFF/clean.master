<?php

class CCleanModule extends TCleanMasterFunctions
{
	private $documentRoot;

	public function __construct()
	{
		$this->documentRoot = Bitrix\Main\Application::getDocumentRoot();
	}

	private function getUninstalledModules($onlyMarketplace = true)
	{
		$arModules = [];

		$folders = array(
			"/local/modules",
			"/bitrix/modules",
		);
		foreach ($folders as $folder) {
			if (!is_dir($this->documentRoot . $folder)) {
				continue;
			}
			$handle = @opendir($this->documentRoot . $folder);
			if ($handle) {
				while (false !== ($dir = readdir($handle))) {
					if (
						!isset($arModules[$dir]) &&
						is_dir($this->documentRoot . $folder . "/" . $dir) &&
						$dir != "." && $dir != ".."
					) {
						if ($onlyMarketplace && strpos($dir, ".") === false) {
							continue;
						}
						$module_dir = $folder . "/" . $dir;
						ob_start();
						if ($info = CModule::CreateModuleObject($dir)) {
							if (! $info->IsInstalled()) {
								$arModules[$dir]["IsInstalled"] = $info->IsInstalled();
								$arModules[$dir]["IsSystem"] = strpos($dir, ".") === false;
								$arModules[$dir]["DIR"] = $module_dir;
								$arModules[$dir]['AGENTS'] = $this->GetModuleAgents($info->MODULE_ID);
							}
						}
						$ignore = ob_get_clean();
					}
				}
				closedir($handle);
			}
		}
		return $arModules;
	}

	public function ModulesDelete($deleteFiles = 'Y', $deleteFilesSys = 'N', $deleteAgents = 'Y')
	{
		$arModules = $this->getUninstalledModules($deleteFilesSys != 'Y');

		foreach ($arModules as $module_id => $value) {
			if ($deleteFiles == 'Y') {
				DeleteDirFilesEx($value['DIR']);
			}
			if ($deleteAgents == 'Y') {
				$this->DeleteModuleAgents($module_id);
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
		$arModules = $this->getUninstalledModules(false);

		foreach ($arModules as $module_id => $value) {
			$_SESSION['cleanmaster']['diagnostic']['module']['modules'][$module_id] = [
				'SIZE' => $this->GetDirSize($this->documentRoot . $value['DIR'] . '/'),
				'AGENTS' => count($value['AGENTS']),
				'IS_SYSTEM' => $value["IsSystem"]
			];
		}
		return false;
	}
}
