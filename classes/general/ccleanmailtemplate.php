<?php


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
		$_SESSION['cleanmaster']['diagnostic']['mailtemplate']['tempalte'] = $this->InactiveMailTemplatesList();
		return false;
	}
}
