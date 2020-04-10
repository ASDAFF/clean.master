<?php

use Bitrix\Main,
	Bitrix\Main\Localization\Loc;

class CCleanBlog
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
			$arFilter['%=POST_TEXT'] = '%' . $postSubstr . '%';
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

		$cnt = \BitrixBlogCommentTable::getList(array(
			'select'    => array('CNT'),
			'runtime' => array(
				new \Bitrix\Main\Entity\ExpressionField('CNT', 'COUNT(*)')
			),
			'filter'    => $arFilter,
		))->fetch();

		return $cnt['CNT'];
	}

	/**
	 * Удаление комментариев
	 * @param $postSubstr
	 * @param $author
	 * @param bool|int $noLimit = false
	 * @return bool
	 */
	public function DeleteBlogMessageByFilter($postSubstr, $author, $noLimit = false)
	{
		$arFilter = $this->MakeFilterArray($postSubstr, $author);

		$params = array(
			'order'     => array('ID' => 'ASC'),
			'select'    => array('ID', 'AUTHOR_NAME', 'POST_TEXT'),
			'filter'    => $arFilter,
			'limit'     => self::MESSAGE_PER_STEP, // delete top items
		);
		if ($noLimit === true) {
			unset($params['limit']);
		}
		$rs = \BitrixBlogCommentTable::getList($params);

		while ($ar = $rs->fetch()) {
			\CBlogComment::Delete($ar['ID']);
		}
	}


	public function GetExampleBlogMessageByFilter($postSubstr, $author)
	{
		$arFilter = $this->MakeFilterArray($postSubstr, $author);

		$rs = \BitrixBlogCommentTable::getList(array(
			'order'     => array('ID' => 'ASC'),
			'select'    => array('ID', 'AUTHOR_NAME', 'POST_TEXT'),
			'filter'    => $arFilter,
			'limit'     => 1,
		));
		return $rs->fetch();
	}
}


// addons:


/**
 * Class BitrixBlogCommentTable
 *
 * Fields:
 * <ul>
 * <li> ID int mandatory
 * <li> BLOG_ID int mandatory
 * <li> POST_ID int mandatory
 * <li> PARENT_ID int optional
 * <li> AUTHOR_ID int optional
 * <li> ICON_ID int optional
 * <li> AUTHOR_NAME string(255) optional
 * <li> AUTHOR_EMAIL string(255) optional
 * <li> AUTHOR_IP string(20) optional
 * <li> AUTHOR_IP1 string(20) optional
 * <li> DATE_CREATE datetime mandatory
 * <li> TITLE string(255) optional
 * <li> POST_TEXT string mandatory
 * <li> PUBLISH_STATUS string(1) mandatory default 'P'
 * <li> PATH string(255) optional
 * <li> HAS_PROPS string(1) optional
 * <li> SHARE_DEST string(255) optional
 * </ul>
 *
 * @package Bitrix\Blog
 **/

class BitrixBlogCommentTable extends Main\Entity\DataManager
{
	/**
	 * Returns DB table name for entity.
	 *
	 * @return string
	 */
	public static function getTableName()
	{
		return 'b_blog_comment';
	}

	/**
	 * Returns entity map definition.
	 *
	 * @return array
	 */
	public static function getMap()
	{
		return array(
			'ID' => array(
				'data_type' => 'integer',
				'primary' => true,
				'autocomplete' => true,
				'title' => Loc::getMessage('COMMENT_ENTITY_ID_FIELD'),
			),
			'BLOG_ID' => array(
				'data_type' => 'integer',
				'required' => true,
				'title' => Loc::getMessage('COMMENT_ENTITY_BLOG_ID_FIELD'),
			),
			'POST_ID' => array(
				'data_type' => 'integer',
				'required' => true,
				'title' => Loc::getMessage('COMMENT_ENTITY_POST_ID_FIELD'),
			),
			'PARENT_ID' => array(
				'data_type' => 'integer',
				'title' => Loc::getMessage('COMMENT_ENTITY_PARENT_ID_FIELD'),
			),
			'AUTHOR_ID' => array(
				'data_type' => 'integer',
				'title' => Loc::getMessage('COMMENT_ENTITY_AUTHOR_ID_FIELD'),
			),
			'AUTHOR_NAME' => array(
				'data_type' => 'string',
				'title' => Loc::getMessage('COMMENT_ENTITY_AUTHOR_NAME_FIELD'),
			),
			'AUTHOR_EMAIL' => array(
				'data_type' => 'string',
				'title' => Loc::getMessage('COMMENT_ENTITY_AUTHOR_EMAIL_FIELD'),
			),
			'AUTHOR_IP' => array(
				'data_type' => 'string',
				'title' => Loc::getMessage('COMMENT_ENTITY_AUTHOR_IP_FIELD'),
			),
			'AUTHOR_IP1' => array(
				'data_type' => 'string',
				'title' => Loc::getMessage('COMMENT_ENTITY_AUTHOR_IP1_FIELD'),
			),
			'TITLE' => array(
				'data_type' => 'string',
				'title' => Loc::getMessage('COMMENT_ENTITY_TITLE_FIELD'),
			),
			'POST_TEXT' => array(
				'data_type' => 'text',
				'required' => true,
				'title' => Loc::getMessage('COMMENT_ENTITY_POST_TEXT_FIELD'),
			)
		);
	}
}