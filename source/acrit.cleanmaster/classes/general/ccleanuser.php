<?php


class CCleanUser
{
    private function DeleteUserOrders($userID)
	{
		if (! CModule::IncludeModule('sale')) {
			return;
		}

		$arFilter = Array(
			"USER_ID" => $userID,
		);
		$db_sales = CSaleOrder::GetList(array("DATE_INSERT" => "ASC"), $arFilter);
		while ($ar_sales = $db_sales->Fetch())
		{
			CSaleOrder::Delete($ar_sales['ID']);
		}
	}
	private function DeleteUserBlog($userID)
	{
		if (! CModule::IncludeModule('blog')) {
			return;
		}

		$dbBlogs = CBlog::GetList(array(), array('OWNER_ID' => $userID));
		while($arBlog = $dbBlogs->Fetch())
		{
			CBlog::Delete($arBlog['ID']);
		}
		$dbBlogUser = CBlogUser::GetList(array(), array('USER_ID' => $userID));
		while($arBlogUser = $dbBlogUser->Fetch())
		{
			CBlogUser::Delete($arBlogUser['ID']);
		}
	}
	private function DeleteUserAccount($userID)
	{
		if (! CModule::IncludeModule('sale')) {
			return;
		}

		$dbUserAccount = CSaleUserAccount::GetList(array(), array('USER_ID' => $userID));
		while($arUserAccount = $dbUserAccount->Fetch())
		{
			CSaleUserAccount::Delete($arUserAccount['ID']);
		}
	}
	private function DeleteUserForum($userID)
	{
		if (! CModule::IncludeModule('forum')) {
			return;
		}

		$dbTopics = CForumTopic::GetList(array(), array('USER_START_ID' => $userID));
		while($arTopic = $dbTopics->Fetch())
		{
			CForumTopic::Delete($arTopic['ID']);
		}
		$dbForumUser = CForumUser::GetList(array(), array('USER_ID' => $userID));
		while($arForumUser = $dbForumUser->Fetch())
		{
			CForumUser::Delete($arForumUser['ID']);
		}
	}

	/**
	 * ����� �����, ����� ����� �������
	 * @param bool $not_authorized
	 * @return bool
	 */
	function InactiveUsersDelete($not_authorized = false)
	{
		$param = true;

		$arFilter = array("ACTIVE" => "N");
		$users = CUser::GetList($by="ID", $order="asc", $arFilter);
		
		while($arUsers = $users->Fetch()) {
			
			$this->DeleteUserOrders($arUsers['ID']);
			$this->DeleteUserBlog($arUsers['ID']);
			$this->DeleteUserForum($arUsers['ID']);
			$this->DeleteUserAccount($arUsers['ID']);
			
			if(!CUser::Delete($arUsers["ID"])){
				$ex = $GLOBALS['APPLICATION']->GetException();
				$param = false;
			}
		}

		if ($not_authorized) {
			$not_auth = array("LAST_LOGIN" => false);

			$users = CUser::GetList($by = "ID", $order = "asc", $not_auth);
			while ($arUsers = $users->Fetch()) {

				$this->DeleteUserOrders($arUsers['ID']);
				$this->DeleteUserBlog($arUsers['ID']);
				$this->DeleteUserForum($arUsers['ID']);
				$this->DeleteUserAccount($arUsers['ID']);

				if (!CUser::Delete($arUsers["ID"])) {
					$ex = $GLOBALS['APPLICATION']->GetException();
					$param = false;
				}
			}
		}
		return $param;
	}
	
	/*
		�������� ������ ��� �����������
	*/
	public function GetDiagnosticData($step = false)
	{
		$not_auth = array("LAST_LOGIN" => false);
		$arFilter = array("ACTIVE" => "N");
		$users = CUser::GetList($by="ID", $order="asc", $arFilter);
		while($arUsers = $users->Fetch()) {
			$_SESSION['cleanmaster']['diagnostic']['user']['inactive'][$arUsers['LOGIN']] = array('NAME' => $arUsers['NAME'], 'LOGIN' => $arUsers['LOGIN'], 'ID' => $arUsers['ID'], 'EMAIL' => $arUsers['EMAIL']);
		}
		$users2 = CUser::GetList($by="ID", $order="asc", $not_auth);
		while($arUsers = $users2->Fetch()) {
			if(!key_exists($arUsers['LOGIN'], $_SESSION['cleanmaster']['diagnostic']['user']['inactive']))
				$_SESSION['cleanmaster']['diagnostic']['user']['notauth'][$arUsers['LOGIN']] = array('NAME' => $arUsers['NAME'], 'LOGIN' => $arUsers['LOGIN'], 'ID' => $arUsers['ID'], 'EMAIL' => $arUsers['EMAIL']);
		}
		return false;
	}
}
