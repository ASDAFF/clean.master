<?php
$cleanMailTemplate = new CCleanMailTemplate;
$temp_list = $cleanMailTemplate->InactiveMailTemplatesList();
?>
<h2><?=GetMessage("CLEANMASTER_ACTION_4")?></h2>
<form>
    <?if (count($temp_list) > 0):?>
    <p><b><?=GetMessage('CLEANMASTER_ACTION_4_NOT_USED')?></b></p>
    <br/>
    <?
        foreach ($temp_list as $template)
        {
            echo '<input type="checkbox" name="temp_to_del[', $template['ID'], ']" value="', $template['ID'], '">' , $template['EVENT_NAME'];
            if($template['EVENT_TYPE'])
                echo '(', $template['EVENT_TYPE'], ')';
            echo '</input><br />';
        }
    ?>
    <br/>
    <a href="javascript:void(0)" class="action-process adm-btn adm-btn-save"><?=GetMessage('CLEANMASTER_ACTION_CLEANSTART')?></a>
    <?else:?>
        <p><b><?=GetMessage("CLEANMASTER_ACTION_4_NOT_FOUND");?></b></p>
    <?endif?>
</form>