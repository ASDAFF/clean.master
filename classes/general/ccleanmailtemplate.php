<?php
/**
 * Copyright (c) 11/4/2020 Created By/Edited By ASDAFF asdaff.asad@yandex.ru
 */

class CCleanMailTemplate {
    public function InactiveMailTemplatesList() {
		$arFilter = Array("ACTIVE" => "N");
		$rsMess = CEventMessage::GetList($by = "site_id", $order = "desc", $arFilter);
		while($ob = $rsMess->Fetch()){
			$mas[] = $ob;
		}
        return $mas;
	}

	public function MailTemplatesDelete($list) {
		if (count($list) > 0){
			foreach ($list as $template) {
				CEventMessage::Delete($template);
			}
		}
	}
	
	/*
		Получаем данные для диагностики
	*/
	public function GetDiagnosticData($step = false)
	{
		$_SESSION['master']['diagnostic']['mailtemplate']['tempalte'] = $this->InactiveMailTemplatesList();
		return false;
	}
}
