<?if( !defined( "B_PROLOG_INCLUDED" ) || B_PROLOG_INCLUDED !== true ) die();

if( !CModule::IncludeModule( "iblock" ) || !CModule::IncludeModule( "sale" ) ){
    return false;                
}

require_once $_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/classes/general/update_client_partner.php";

if( !class_exists( "CAcritCleanmasterInformerGadget" ) ){
    class CAcritCleanmasterInformerGadget
	{
        public static $moduleId = "acrit.cleanmaster";
        public static $modulePrefix = "acrit";
        public static $timeExpire = 2592000;
        
        private function GetMarketModuleList()
		{
            $arModules = array();
            
            $arRequestedModules = CUpdateClientPartner::GetRequestedModules( self::$moduleId );
            $arUpdateList = CUpdateClientPartner::GetUpdatesList(
                $errorMessage,
                LANGUAGE_ID,
                "N",
                $arRequestedModules,
                array(
                    "fullmoduleinfo" => "Y"
                )
            );
            
            $arModules = $arUpdateList;
            
            return $arModules;
        }
        
        public static function GetMarketModulesInfo()
		{
            $result = false;
            $arModuleList = self::GetMarketModuleList();
            if( is_array( $arModuleList["MODULE"] ) && !empty( $arModuleList["MODULE"] ) ){
                foreach( $arModuleList["MODULE"] as $arModule ){
                    if( stripos( $arModule["@"]["ID"], self::$modulePrefix ) !== false ){
                        if( $arModule["@"]["ID"] == self::$moduleId ){
                            $moduleStatus = CModule::IncludeModuleEx( self::$moduleId );
                            $licenseDateTo = MakeTimeStamp( $arModule["@"]["DATE_TO"] );
                            if( ( $licenseDateTo - time() ) < self::$timeExpire ){
                                if( $licenseDateTo < time() ){
                                    $result = array(
                                        "IS_EXPIRE" => true,
                                        "NEED_BUY_UPDATE" => true,
                                        "DATE_EXPIRE" => $arModule["@"]["DATE_TO"],
                                        "IS_DEMO" => ( $moduleStatus != 1 ) ? true : false
                                    );
                                }
                                else{
                                    $result = array(
                                        "IS_EXPIRE" => false,
                                        "NEED_BUY_UPDATE" => true,
                                        "DATE_EXPIRE" => $arModule["@"]["DATE_TO"],
                                        "IS_DEMO" => ( $moduleStatus != 1 ) ? true : false
                                    );
                                }
                            }
                            else{
                                $result = array(
                                    "IS_EXPIRE" => false,
                                    "NEED_BUY_UPDATE" => false,
                                    "DATE_EXPIRE" => $arModule["@"]["DATE_TO"],
                                    "IS_DEMO" => ( $moduleStatus != 1 ) ? true : false
                                );
                            }
                        }                
                    }
                }
            }
            
            return $result;                
        }
    }
}

$arModuleNameParts = explode( ".", CAcritCleanmasterInformerGadget::$moduleId );

global $APPLICATION;
$APPLICATION->SetAdditionalCSS( "/bitrix/gadgets/".$arModuleNameParts[0]."/".$arModuleNameParts[1]."/styles.css" );

$arModuleData = CAcritCleanmasterInformerGadget::GetMarketModulesInfo();

$showInfoRow = "";
if( $arModuleData["IS_DEMO"] && ( strlen( $arModuleData["DATE_EXPIRE"] ) > 0 ) ){
    $showInfoRow = GetMessage( "GD_ACRIT_CLEANMASTER_EXPORT_DEMO_PERIOD_INFO" ).$arModuleData["DATE_EXPIRE"];
    $showBuyText = GetMessage( "GD_ACRIT_CLEANMASTER_EXPORT_BUY_LICENCE_INFO" );
}
elseif( $arModuleData["IS_DEMO"] && ( strlen( $arModuleData["DATE_EXPIRE"] ) <= 0 ) ){
    $showInfoRow = GetMessage( "GD_ACRIT_CLEANMASTER_EXPORT_DEMO_PERIOD_EXPIRED_INFO" );
    $showBuyText = GetMessage( "GD_ACRIT_CLEANMASTER_EXPORT_BUY_LICENCE_INFO" );
}
elseif( !$arModuleData["IS_DEMO"] && ( strlen( $arModuleData["DATE_EXPIRE"] ) <= 0 ) ){
    $showInfoRow = GetMessage( "GD_ACRIT_CLEANMASTER_EXPORT_LICENSE_PERIOD_EXPIRED_INFO" );
    $showBuyText = GetMessage( "GD_ACRIT_CLEANMASTER_EXPORT_PROLONG_LICENCE_INFO" );
}
elseif( !$arModuleData["IS_DEMO"] && ( strlen( $arModuleData["DATE_EXPIRE"] ) > 0 ) ){
    $showInfoRow = GetMessage( "GD_ACRIT_CLEANMASTER_EXPORT_LICENSE_PERIOD_INFO" ).$arModuleData["DATE_EXPIRE"];
    $showBuyText = false;
}

if (trim($showInfoRow) == '') {
	$showInfoRow = GetMessage('GD_ACRIT_CLEANMASTER_ELEVATOR_SPECH_INFO');
}

$byLicenceUrl = "https://www.acrit-studio.ru/market/avtomatizatsiya-rutiny/master-ochistki-sayta/?action=BUY&id=8538";
$acritUrl = "https://www.acrit-studio.ru/";?>

<div class="acrit-info-widget">
    <div class="show-gadget-title"><?=GetMessage( "GD_ACRIT_CLEANMASTER_NAME" );?></div>
    <div class="show-info-row"><?=$showInfoRow;?></div>
    <?if( $showBuyText ){?>
        <a class="acrit-info-widget-button acrit-info-widget-button-buy " href="<?=$byLicenceUrl;?>" target="_blank">
            <div class="acrit-info-widget-button-text"><?=$showBuyText;?></div>
        </a>
    <?}?>
    <a href="<?=$acritUrl;?>" target="_blank"><div class="logo"></div></a>
</div>