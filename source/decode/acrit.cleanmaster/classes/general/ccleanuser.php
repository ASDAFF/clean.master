<?php
use \Bitrix\Main\Loader;

class CCleanUser
{
	private function DeleteUserOrders($userID)
	{
		if (!CModule::IncludeModule('sale')) {
			return;
		}

		$arFilter = Array(
			"USER_ID" => $userID,
		);
		$db_sales = CSaleOrder::GetList(array("DATE_INSERT" => "ASC"), $arFilter);
		while ($ar_sales = $db_sales->Fetch()) {
			CSaleOrder::Delete($ar_sales['ID']);
		}
	}

	private function DeleteUserBlog($userID)
	{
		if (!CModule::IncludeModule('blog')) {
			return;
		}

		$dbBlogs = CBlog::GetList(array(), array('OWNER_ID' => $userID));
		while ($arBlog = $dbBlogs->Fetch()) {
			CBlog::Delete($arBlog['ID']);
		}
		$dbBlogUser = CBlogUser::GetList(array(), array('USER_ID' => $userID));
		while ($arBlogUser = $dbBlogUser->Fetch()) {
			CBlogUser::Delete($arBlogUser['ID']);
		}
	}

	private function DeleteUserAccount($userID)
	{
		if (!CModule::IncludeModule('sale')) {
			return;
		}

		$dbUserAccount = CSaleUserAccount::GetList(array(), array('USER_ID' => $userID));
		while ($arUserAccount = $dbUserAccount->Fetch()) {
			CSaleUserAccount::Delete($arUserAccount['ID']);
		}
	}

	private function DeleteUserForum($userID)
	{
		if (!CModule::IncludeModule('forum')) {
			return;
		}

		$dbTopics = CForumTopic::GetList(array(), array('USER_START_ID' => $userID));
		while ($arTopic = $dbTopics->Fetch()) {
			CForumTopic::Delete($arTopic['ID']);
		}
		$dbForumUser = CForumUser::GetList(array(), array('USER_ID' => $userID));
		while ($arForumUser = $dbForumUser->Fetch()) {
			CForumUser::Delete($arForumUser['ID']);
		}
	}

	private function DeleteUserWebFormResult($userID)
	{
		if (! Loader::includeModule('form')) {
			return;
		}
		$arFilter = [
			"USER_ID"               => $userID,
			"USER_ID_EXACT_MATCH"   => 'Y'
		];
		$rsForms = CForm::GetList($by="s_id", $order="desc", [], $is_filtered);
		while ($arForm = $rsForms->Fetch()) {
			$rsResults = CFormResult::GetList($arForm['ID'], $by="s_timestamp", $order="desc", $arFilter,
				$is_filtered,"N", 10000);
			while ($r = $rsResults->Fetch()) {
				CFormResult::Delete($r['ID'], 'N');
			}
		}
	}

	private function DeleteUserTickets($userID)
	{
		if (! Loader::includeModule('support')) {
			return;
		}
		$arFilter = [
			'CREATED_BY'                => $userID,
			'CREATED_BY_EXACT_MATCH'    => "Y"
		];
		$tickets = CTicket::GetList($by="s_id", $order="asc", $arFilter, $is_filtered);
		while ($t = $tickets->Fetch()) {
			CTicket::Delete($t['ID'], "N");
		}
	}

	private function DeleteSocNetByUser($userID)
	{
		if (! Loader::includeModule('socialnetwork')) {
			return;
		}
		$rsMess = CSocNetMessages::GetList(['ID' => 'DESC'], ['FROM_USER_ID' => $userID], false, false, ['ID', 'FROM_USER_ID']);
		while ($ar = $rsMess->Fetch()) {
			CSocNetMessages::Delete($ar['ID']);
		}
		$rsMess = CSocNetMessages::GetList(['ID' => 'DESC'], ['TO_USER_ID' => $userID], false, false, ['ID', 'TO_USER_ID']);
		while ($ar = $rsMess->Fetch()) {
			CSocNetMessages::Delete($ar['ID']);
		}
		$rsGrp = CSocNetGroup::GetList(['ID' => 'DESC'], ['OWNER_ID' => $userID], false, false, ['ID', 'OWNER_ID']);
		while ($ar = $rsGrp->Fetch()) {
			CSocNetGroup::Delete($ar['ID']);
		}
	}

	/**
	 * “очка входа, общий фасад очистки
	 *
	 * @param bool $not_authorized      = false
	 * @param bool $deleteAssets        = false удалить пользователей с данными модулей
	 *
	 * @return bool
	 */
	public function InactiveUsersDelete($not_authorized = false, $deleteAssets = false)
	{
		$param = true;

		$arFilter = array("ACTIVE" => "N");
		$users = CUser::GetList($by = "ID", $order = "asc", $arFilter);

		while ($arUsers = $users->Fetch()) {

			$assets = $this->GetUserAssets($arUsers['ID']);
			$cnt = 0;
			foreach ($assets as $k => $a) {
				$cnt += $a;
			}
			if ($cnt > 0 && $deleteAssets != 'Y') {
				continue;
			}

			$this->DeleteUserAssets($arUsers['ID']);

			if (!CUser::Delete($arUsers["ID"])) {
				$ex = $GLOBALS['APPLICATION']->GetException();
				$param = false;
			}
		}

		if ($not_authorized == 'Y') {
			$not_auth = array("LAST_LOGIN" => false);

			$users = CUser::GetList($by = "ID", $order = "asc", $not_auth);
			while ($arUsers = $users->Fetch()) {

				// its be void for this users
				//$this->DeleteUserAssets($arUsers['ID']);

				if (!CUser::Delete($arUsers["ID"])) {
					$ex = $GLOBALS['APPLICATION']->GetException();
					$param = false;
				}
			}
		}
		return $param;
	}

	public function GetDiagnosticData($step = false)
	{
		$not_auth = array("LAST_LOGIN" => false);
		$arFilter = array("ACTIVE" => "N");
		$users = CUser::GetList($by = "ID", $order = "asc", $arFilter);
		while ($arUsers = $users->Fetch()) {
			$_SESSION['cleanmaster']['diagnostic']['user']['inactive'][$arUsers['LOGIN']] = array(
				'NAME' => $arUsers['NAME'], 'LOGIN' => $arUsers['LOGIN'], 'ID' => $arUsers['ID'], 'EMAIL' => $arUsers['EMAIL']
			);
		}
		$users2 = CUser::GetList($by = "ID", $order = "asc", $not_auth);
		while ($arUsers = $users2->Fetch()) {
			if (!array_key_exists($arUsers['LOGIN'], $_SESSION['cleanmaster']['diagnostic']['user']['inactive']))
				$_SESSION['cleanmaster']['diagnostic']['user']['notauth'][$arUsers['LOGIN']] = array(
					'NAME' => $arUsers['NAME'], 'LOGIN' => $arUsers['LOGIN'], 'ID' => $arUsers['ID'], 'EMAIL' => $arUsers['EMAIL']
				);
		}
		return false;
	}

	/////////////////////////// v2 /////////////////////////////////////

	private function GetUserAssets($userID)
	{
		$userID = (int)$userID;

		$arAssetsCnt = [
			'Blog'      => 0,
			'Forum'     => 0,
			'Form'      => 0,
			'Tickets'   => 0,
			'Orders'    => 0,
			'Account'   => 0,
			'SocNetMessages'   => 0,
			'SocNetGroup'      => 0
		];

		if (Loader::includeModule('blog')) {
			$dbBlogs = CBlog::GetList(array(), array('OWNER_ID' => $userID));
			$arAssetsCnt['Blog'] = (int)$dbBlogs->SelectedRowsCount();
		}
		if (Loader::includeModule('forum')) {
			$dbTopics = CForumTopic::GetList(array(), array('USER_START_ID' => $userID));
			$arAssetsCnt['Forum'] = (int)$dbTopics->SelectedRowsCount();
		}
		if (Loader::includeModule('form')) {
			$arFilter = [
				"USER_ID"               => $userID,
				"USER_ID_EXACT_MATCH"   => 'Y'
			];
			$rsForms = CForm::GetList($by="s_id", $order="desc", [], $is_filtered);
			while ($arForm = $rsForms->Fetch()) {
				$rsResults = CFormResult::GetList($arForm['ID'], $by="s_timestamp", $order="desc", $arFilter,
					$is_filtered,"N", 100);
				$arAssetsCnt['Form'] += (int)$rsResults->SelectedRowsCount();
			}
		}
		if (Loader::includeModule('support')) {
			$arFilter = [
				'CREATED_BY'                => $userID,
				'CREATED_BY_EXACT_MATCH'    => "Y"
			];
			$tickets = CTicket::GetList($by="s_id", $order="asc", $arFilter, $is_filtered);
			$arAssetsCnt['Tickets'] = (int)$tickets->SelectedRowsCount();
		}
		if (Loader::includeModule('sale')) {
			$db_sales = CSaleOrder::GetList(["DATE_INSERT" => "ASC"], ["USER_ID" => $userID]);
			$arAssetsCnt['Orders'] = (int)$db_sales->SelectedRowsCount();

			$dbUserAccount = CSaleUserAccount::GetList([], ['USER_ID' => $userID]);
			$arAssetsCnt['Account'] = (int)$dbUserAccount->SelectedRowsCount();
		}
		if (! Loader::includeModule('socialnetwork')) {
			$rsMess = CSocNetMessages::GetList(['ID' => 'DESC'], ['FROM_USER_ID' => $userID], false, false, ['ID', 'FROM_USER_ID']);
			$arAssetsCnt['SocNetMessages'] += (int)$rsMess->SelectedRowsCount();

			$rsMess = CSocNetMessages::GetList(['ID' => 'DESC'], ['TO_USER_ID' => $userID], false, false, ['ID', 'TO_USER_ID']);
			$arAssetsCnt['SocNetMessages'] += (int)$rsMess->SelectedRowsCount();

			$rsGrp = CSocNetGroup::GetList(['ID' => 'DESC'], ['OWNER_ID' => $userID], false, false, ['ID', 'OWNER_ID']);
			$arAssetsCnt['SocNetGroup'] += (int)$rsGrp->SelectedRowsCount();
		}

		return $arAssetsCnt;
	}

	private function DeleteUserAssets($userID)
	{
		$this->DeleteUserOrders($userID);
		$this->DeleteUserBlog($userID);
		$this->DeleteUserForum($userID);
		$this->DeleteUserAccount($userID);
		$this->DeleteUserWebFormResult($userID);
		$this->DeleteUserTickets($userID);
		$this->DeleteSocNetByUser($userID);
	}

}
