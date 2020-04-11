<?php
/**
 * Copyright (c) 11/4/2020 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

IncludeModuleLangFile(__FILE__);

$APPLICATION->AddHeadString('<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>');
$APPLICATION->AddHeadString('<script type="text/javascript" src="/bitrix/js/clean.master/main.js"></script>');
$APPLICATION->AddHeadString('<link rel="stylesheet" href="/bitrix/css/clean.master/main.css">');

$actionList = array(
    1 => GetMessage("MASTER_ACTION_1"),
    2 => GetMessage("MASTER_ACTION_2"),
    3 => GetMessage("MASTER_ACTION_3"),
    4 => GetMessage("MASTER_ACTION_4"),
    5 => GetMessage("MASTER_ACTION_5"),
    6 => GetMessage("MASTER_ACTION_6"),
    7 => GetMessage("MASTER_ACTION_7"),
    8 => GetMessage("MASTER_ACTION_8"),
    9 => GetMessage("MASTER_ACTION_9"),
    10 => GetMessage("MASTER_ACTION_10"),
    11 => GetMessage("MASTER_ACTION_11"),
    12 => GetMessage("MASTER_ACTION_12"),
    13 => GetMessage("MASTER_ACTION_13"),
    14 => GetMessage("MASTER_ACTION_14"),
    16 => GetMessage("MASTER_ACTION_16"),
    17 => GetMessage("MASTER_ACTION_17"),
    20 => GetMessage("MASTER_ACTION_20"),
    21 => GetMessage("MASTER_ACTION_21"),
    22 => GetMessage("MASTER_ACTION_22"),
    23 => GetMessage("MASTER_ACTION_23"),
);

global $action_start, $clear_menu, $isDemo;
?>

<script>
    var action_list = <?=CUtil::PhpToJSObject($actionList);?>;
    var selected_action_list = <?=CUtil::PhpToJSObject($_SESSION['master']['action']);?>;
</script>
<?/*if($isDemo != 1):?>
    <?=GetMessage('CLEAN_MASTER_DEMO')?>
<?endif*/?>
<br/>

<?if($clear_menu != 'Y' && $action_start != 'Y'):?>
    <a class="adm-btn adm-btn-save" id="diagnostic"><?=GetMessage('CLEAN_MASTER_RUN_DIAGNOSTIC')?></a>
    <?if($isDemo == 1):?>
        &nbsp;&nbsp;<?=GetMessage('CLEAN_MASTER_OR')?>&nbsp;&nbsp;
        <a class="adm-btn" href="<?=$APPLICATION->GetCurPageParam('clear_menu=Y', array(''))?>"><?=GetMessage('CLEAN_MASTER_TO_CLEAR')?></a>
    <?endif?>
    </br>
    <div class="progress-bar"></div></br>
<?else:?>
    <a class="adm-btn adm-btn-save" href="<?=$APPLICATION->GetCurPageParam('', array('clear_menu', 'action_start'))?>"><?=GetMessage('CLEAN_MASTER_BACKTO_DIAGNOSTIC')?></a></br>
    <div class="progress-bar"></div></br>
<?endif?>

<div class="master-area">
    <?if($action_start != 'Y'):?>
        <form action="<?=$_SERVER['REQUEST_URI']?>" method="POST">
            <?if($clear_menu == 'Y' && $isDemo == 1):?>
                    <input type="hidden" name="action_start" value="Y">
                    <?foreach($actionList as $key => $action):?>
                        <div>
                            <input type="checkbox" name="action[<?=$key?>]" value="<?=$key?>" id="steps_<?=$key?>">
                            <label for="steps_<?=$key?>"><?=$actionList[$key]?></label>
                        </div>
                    <?endforeach?>
                    <br/>
                    <input type="submit" name="select_action" value="<?=GetMessage('MASTER_ACTION_SELECT')?>">
            <?endif;?>
        </form>
    <?elseif($isDemo == 1):?>
    <?
        if($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            if(!is_array($_REQUEST['action']))
                return;
            $postActions = $_REQUEST['action'];
            sort($postActions);
            unset($_SESSION['master']);
            $actionCnt = 0;
            foreach($postActions as $key => $action)
            {
                if($actionCnt == 0)
                    $_SESSION['master']['action'][$action]['active'] = true;
                $_SESSION['master']['action'][$action]['prev'] = $postActions[$key - 1];
                $_SESSION['master']['action'][$action]['next'] = $postActions[$key + 1];
                $actionCnt++;
            }
            parse_str($_SERVER['QUERY_STRING'], $arQuery);
            $arQuery['action_start'] = 'Y';
            LocalRedirect($_SERVER['SCRIPT_NAME'].'?'.http_build_query($arQuery));
        }
    ?>
    <div class="clean-action-menu">
        <h2><a href="<?=$_SERVER['SCRIPT_NAME']?>?lang=ru&mid=clean.master&mid_menu=1"><?=GetMessage('MASTER_ACTION_MENU')?></a></h2>
        <ul>
            <?foreach($_SESSION['master']['action'] as $key => $action):?>
                <li><a href="javascript:void(0)" data-action-id="<?=$key?>"><?=$actionList[$key]?></a></li>
            <?endforeach?>
        </ul>
    </div>
    <div class="clean-action-wrapper">
        <?
            foreach($_SESSION['master']['action'] as $key => $action)
            {
                if(file_exists(__DIR__."/steps/step$key.php"))
                {
                    echo '<div class="action-container';
                    if($action['active'])
                        echo ' active';
                    echo '" data-action-id="', $key, '">';
                    require_once(__DIR__."/steps/step$key.php");
                    echo '</div>';
                }
            }
            reset($_SESSION['master']['action']);
            $firstAction = current($_SESSION['master']['action']);
        ?>
        <div class="nav-buttons">
            <?/*<div><a href="javascript:void(0)" class="action-process adm-btn adm-btn-save"><?=GetMessage('MASTER_ACTION_CLEANSTART')?></a></div>*/?>
            <div class="action-container-prev<? if(!$firstAction['prev']) echo ' hide'; ?>"><span>&#212;</span><a class="action-container-prev adm-btn" data-action-id="<?=$firstAction['prev']?>"><?=$actionList[$firstAction['prev']]?></a></div>
            <div class="action-container-next<? if(!$firstAction['next']) echo ' hide'; ?>"><a class="action-container-next adm-btn" data-action-id="<?=$firstAction['next']?>"><?=$actionList[$firstAction['next']]?></a><span>&#215;</span></div>
        </div>
    </div>
<?endif?>
</div>
