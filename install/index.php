<?
$GLOBALS['_____184093346'] = array(
    base64_decode('S' . 'W5' . 'jbH' . 'Vk' . 'ZU1vZHVsZUx' . 'hbmdG' . 'aWxl')
);
?>
<?
$GLOBALS['_____184093346'][0](__FILE__);
Class acrit_cleanmaster extends CModule
{
    const MODULE_ID = 'acrit.cleanmaster';
    var $MODULE_ID = 'acrit.cleanmaster';
    var $MODULE_VERSION;
    var $MODULE_VERSION_DATE;
    var $MODULE_NAME;
    var $MODULE_DESCRIPTION;
    var $MODULE_CSS;
    var $_2048130702 = '';
    function __construct()
    {
        $arModuleVersion = array();
        include(dirname(__FILE__) . '/version.php');
        $this->MODULE_VERSION      = $arModuleVersion['VERSION'];
        $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
        $this->MODULE_NAME         = GetMessage('acrit.cleanmaster_MODULE_NAME');
        $this->MODULE_DESCRIPTION  = GetMessage('acrit.cleanmaster_MODULE_DESC');
        $this->PARTNER_NAME        = GetMessage('acrit.cleanmaster_PARTNER_NAME');
        $this->PARTNER_URI         = GetMessage('acrit.cleanmaster_PARTNER_URI');
    }
    function InstallDB($_476777929 = array())
    {
        RegisterModuleDependences('main', 'OnBuildGlobalMenu', self::MODULE_ID, 'CAcritCleanMasterMenu', 'OnBuildGlobalMenu');
        return true;
    }
    function UnInstallDB($_476777929 = array())
    {
        global $DB;
        UnRegisterModuleDependences('main', 'OnBuildGlobalMenu', self::MODULE_ID, 'CAcritCleanMasterMenu', 'OnBuildGlobalMenu');
        $DB->Query("DELETE FROM b_option WHERE `MODULE_ID`='{$this->MODULE_ID}' AND `NAME`='~bsm_stop_date'");
        return true;
    }
    function InstallEvents()
    {
        return true;
    }
    function UnInstallEvents()
    {
        return true;
    }
    function InstallFiles($_476777929 = array())
    {
        if (is_dir($_1517105015 = $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/' . self::MODULE_ID . '/admin')) {
            if ($_1321396995 = opendir($_1517105015)) {
                while (false !== $_1720814425 = readdir($_1321396995)) {
                    if ($_1720814425 == '..' || $_1720814425 == '.' || $_1720814425 == 'menu.php')
                        continue;
                    file_put_contents($_1733132088 = $_SERVER['DOCUMENT_ROOT'] . '/bitrix/admin/' . self::MODULE_ID . '_' . $_1720814425, '<' . '? require($_SERVER[\"DOCUMENT_ROOT\"].\"/bitrix/modules/' . self::MODULE_ID . '/admin/' . $_1720814425 . '\");?' . '>');
                }
                closedir($_1321396995);
            }
        }
        if (is_dir($_1517105015 = $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/' . self::MODULE_ID . '/install/components')) {
            if ($_1321396995 = opendir($_1517105015)) {
                while (false !== $_1720814425 = readdir($_1321396995)) {
                    if ($_1720814425 == '..' || $_1720814425 == '.')
                        continue;
                    CopyDirFiles($_1517105015 . '/' . $_1720814425, $_SERVER['DOCUMENT_ROOT'] . '/bitrix/components/' . $_1720814425, $_1979574470 = True, $_1303146352 = True);
                }
                closedir($_1321396995);
            }
        }
        CopyDirFiles($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/' . self::MODULE_ID . '/install/admin', $_SERVER['DOCUMENT_ROOT'] . '/bitrix/admin', true, true);
        if (!is_dir($_SERVER['DOCUMENT_ROOT'] . '/bitrix/js/' . self::MODULE_ID))
            mkdir($_SERVER['DOCUMENT_ROOT'] . '/bitrix/js/' . self::MODULE_ID);
        CopyDirFiles($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/' . self::MODULE_ID . '/install/js/', $_SERVER['DOCUMENT_ROOT'] . '/bitrix/js/', true, true);
        CopyDirFiles($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/' . self::MODULE_ID . '/install/themes/', $_SERVER['DOCUMENT_ROOT'] . '/bitrix/themes/', true, true);
        CopyDirFiles($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/' . self::MODULE_ID . '/install/fonts/', $_SERVER['DOCUMENT_ROOT'] . '/bitrix/fonts/', true, true);
        CopyDirFiles($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/' . self::MODULE_ID . '/install/css/', $_SERVER['DOCUMENT_ROOT'] . '/bitrix/css/', true, true);
        CopyDirFiles($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/' . self::MODULE_ID . '/install/gadgets', $_SERVER['DOCUMENT_ROOT'] . '/bitrix/gadgets', true, true);
        return true;
    }
    function UnInstallFiles()
    {
        if (is_dir($_1517105015 = $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/' . self::MODULE_ID . '/admin')) {
            if ($_1321396995 = opendir($_1517105015)) {
                while (false !== $_1720814425 = readdir($_1321396995)) {
                    if ($_1720814425 == '..' || $_1720814425 == '.')
                        continue;
                    unlink($_SERVER['DOCUMENT_ROOT'] . '/bitrix/admin/' . self::MODULE_ID . '_' . $_1720814425);
                }
                closedir($_1321396995);
            }
        }
        if (is_dir($_1517105015 = $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/' . self::MODULE_ID . '/install/components')) {
            if ($_1321396995 = opendir($_1517105015)) {
                while (false !== $_1720814425 = readdir($_1321396995)) {
                    if ($_1720814425 == '..' || $_1720814425 == '.' || !is_dir($_912851098 = $_1517105015 . '/' . $_1720814425))
                        continue;
                    $_1812137780 = opendir($_912851098);
                    while (false !== $_963496294 = readdir($_1812137780)) {
                        if ($_963496294 == '..' || $_963496294 == '.')
                            continue;
                        DeleteDirFilesEx('/bitrix/components/' . $_1720814425 . '/' . $_963496294);
                    }
                    closedir($_1812137780);
                }
                closedir($_1321396995);
            }
        }
        unlink($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/' . self::MODULE_ID . '/admin/user_date_bsm.php');
        DeleteDirFilesEx('/bitrix/gadgets/acrit/cleanmaster/');
        return true;
    }
    function RegisterGadget()
    {
        $_2056528097 = explode('.', self::MODULE_ID);
        $_779333843  = array(
            ToUpper($_2056528097[round(0 + 0.25 + 0.25 + 0.25 + 0.25)]) . '@' . time() => array(
                'COLUMN' => (1460 / 2 - 730),
                'ROW' => (1284 / 2 - 642),
                'HIDE' => 'N'
            )
        );
        $_1646505832 = CUserOptions::GetOption('intranet', '~gadgets_admin_index');
        if (is_array($_1646505832[(846 - 2 * 423)]['GADGETS']) && !empty($_1646505832[(134 * 2 - 268)]['GADGETS'])) {
            $_1646505832[(1144 / 2 - 572)]['GADGETS'] = array_merge($_779333843, $_1646505832[(1064 / 2 - 532)]['GADGETS']);
        } else {
            $_1646505832[(1244 / 2 - 622)]['GADGETS'] = $_779333843;
        }
        CUserOptions::SetOption('intranet', '~gadgets_admin_index', $_1646505832, false, false);
    }
    function UnRegisterGadget()
    {
        $_2056528097 = explode('.', self::MODULE_ID);
        $_1875179041 = ToUpper($_2056528097[round(0 + 0.33333333333333 + 0.33333333333333 + 0.33333333333333)]);
        $_1646505832 = CUserOptions::GetOption('intranet', '~gadgets_admin_index');
        foreach ($_1646505832[(792 - 2 * 396)]['GADGETS'] as $_778069796 => $_41714797) {
            if (stripos($_778069796, $_1875179041 . '@') !== false) {
                unset($_1646505832[(145 * 2 - 290)]['GADGETS'][$_778069796]);
            }
        }
        CUserOptions::SetOption('intranet', '~gadgets_admin_index', $_1646505832, false, false);
    }
    function DoInstall()
    {
        global $APPLICATION, $DB;
        $_1166746543 = $DB->Query("SELECT * FROM b_option WHERE `MODULE_ID`='{$this->MODULE_ID}' AND `NAME`='~bsm_stop_date'");
        if ($_1166746543->Fetch()) {
            $DB->Query("DELETE FROM b_option WHERE `MODULE_ID`='{$this->MODULE_ID}' AND `NAME`='~bsm_stop_date'");
        }
        $this->InstallFiles();
        $this->InstallDB();
        $this->RegisterGadget();
        RegisterModule(self::MODULE_ID);
    }
    function DoUninstall()
    {
        global $APPLICATION;
        UnRegisterModule(self::MODULE_ID);
        $this->UnInstallDB();
        $this->UnRegisterGadget();
        $this->UnInstallFiles();
    }
}
?>