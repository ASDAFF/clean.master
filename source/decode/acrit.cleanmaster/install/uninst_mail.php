<?
if(!check_bitrix_sessid()) return;
IncludeModuleLangFile(__FILE__);
$APPLICATION->SetTitle(GetMessage("acrit.uninst_fb_PAGE_TITLE", array('#MODULE_NAME#' => $GLOBALS['ACRIT_MODULE_NAME'])));

if ($_REQUEST['agree'] == 1) {
	$charset = defined('BX_UTF') && BX_UTF === true ? 'UTF-8' : 'windows-1251';
	$email_def = COption::GetOptionString('main','email_from');
	$from = $email_def ? $email_def : GetMessage("acrit.uninst_fb_MAIL_SUPPORT_EMAIL");
	$to = GetMessage("acrit.uninst_fb_MAIL_SUPPORT_EMAIL");
	$subject = GetMessage("acrit.uninst_fb_MAIL_SUBJECT", array('#MODULE_NAME#' => $GLOBALS['ACRIT_MODULE_NAME']));
	$mail_msg = GetMessage("acrit.uninst_fb_MAIL_MODULE", array('#MODULE_NAME#' => $GLOBALS['ACRIT_MODULE_NAME'])) . "<br><br />\r\n";
	$mail_msg .= GetMessage("acrit.uninst_fb_MAIL_REASON") . GetMessage("acrit.uninst_fb_MAIL_REASON_" . $_REQUEST['reason'] . "") . "<br>\r\n";
	if ($_REQUEST['reason_other']) {
		$mail_msg .= GetMessage("acrit.uninst_fb_MAIL_REASON_TEXT") . nl2br($_REQUEST['reason_other']) . "<br>\r\n";
	}
	$mail_msg .= GetMessage("acrit.uninst_fb_MAIL_SUPPORT") . GetMessage("acrit.uninst_fb_MAIL_SUPPORT_" . $_REQUEST['support'] . "") . "<br>\r\n";
	$mail_msg .= GetMessage("acrit.uninst_fb_MAIL_EMAIL_TIT") . $email_def . "<br>\r\n";
	$mail_msg .= GetMessage("acrit.uninst_fb_MAIL_SITE_TIT") . $_SERVER['SERVER_NAME'] . "<br>\r\n";
	$mail_msg .= "<br>" . GetMessage("acrit.uninst_fb_MAIL_CALLBACK") . GetMessage("acrit.uninst_fb_MAIL_CALLBACK_" . $_REQUEST['callback'] . "") . "<br>\r\n";
	#
	\Bitrix\Main\Mail\Mail::send(array(
		'TO' => $to,
		'SUBJECT' => $subject,
		'BODY' => $mail_msg,
		'HEADER' => array(
			'From' => $from,
		),
		'CHARSET' => $charset,
		'CONTENT_TYPE' => 'html',
	));
}

CAdminMessage::ShowNote(GetMessage('acrit.uninst_fb_MAIL_MOD_UNINST_OK'));
?>
<form action="<?=$APPLICATION->GetCurPage()?>" method="get">
	<p>
		<input type="hidden" name="lang" value="<?=LANG?>" />
		<input type="submit" value="<?=GetMessage( "MOD_BACK" )?>" />
	</p>
</form>