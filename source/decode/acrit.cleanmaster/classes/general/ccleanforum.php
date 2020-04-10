<?php


class CCleanForum
{
	/**
	 * удалять элементов за шаг
	 */
	const MESSAGE_PER_STEP = 10;

	public function GetStepCount()
	{
		return self::MESSAGE_PER_STEP;
	}

	public function MakeFilterArray($postSubstr, $author)
	{
		$arFilter = array();
		$postSubstr = trim($postSubstr);
		if ($postSubstr != '') {
			$arFilter['%=POST_MESSAGE_HTML'] = '%' . $postSubstr . '%';
		}
		$author = trim($author);
		if ($author != '') {
			$arFilter['%=AUTHOR_NAME'] = '%' . $author . '%';
		}
		return $arFilter;
	}

	/**
	 * Получить кол-во спам-сообщений
	 *
	 * @param $postSubstr
	 * @param $author
	 * @return int
	 */
	public function GetCount($postSubstr, $author)
	{
		$arFilter = $this->MakeFilterArray($postSubstr, $author);

		$cnt = \Bitrix\Forum\MessageTable::getList(array(
			'select'    => array('CNT'),
			'runtime' => array(
				new \Bitrix\Main\Entity\ExpressionField('CNT', 'COUNT(*)')
			),
			'filter'    => $arFilter,
		))->fetch();

		return $cnt['CNT'];
	}

	/**
	 * Удаление сообщений форума
	 * @param $postSubstr
	 * @param $author
	 * @param bool|int $noLimit = false
	 * @return bool
	 */
	public function DeleteForumMessageByFilter($postSubstr, $author, $noLimit = false)
	{
		$arFilter = $this->MakeFilterArray($postSubstr, $author);

		$params = array(
			'order'     => array('ID' => 'ASC'),
			'select'    => array('ID', 'AUTHOR_NAME', 'POST_MESSAGE_HTML', 'AUTHOR_REAL_IP'),
			'filter'    => $arFilter,
			'limit'     => self::MESSAGE_PER_STEP,
		);
		if ($noLimit === true) {
			unset($params['limit']);
		}
		$rs = \Bitrix\Forum\MessageTable::getList($params);

		while ($ar = $rs->fetch()) {
			\CForumMessage::Delete($ar['ID']);
		}
	}


	public function GetExampleForumMessageByFilter($postSubstr, $author)
	{
		$arFilter = $this->MakeFilterArray($postSubstr, $author);

		$rs = \Bitrix\Forum\MessageTable::getList(array(
			'order'     => array('ID' => 'ASC'),
			'select'    => array('ID', 'AUTHOR_NAME', 'POST_MESSAGE_HTML', 'AUTHOR_REAL_IP'),
			'filter'    => $arFilter,
			'limit'     => 1,
		));
		return $rs->fetch();
	}
}
