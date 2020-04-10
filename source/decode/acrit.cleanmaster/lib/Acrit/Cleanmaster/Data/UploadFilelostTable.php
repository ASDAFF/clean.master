<?
namespace Acrit\Cleanmaster\Data;

use \Bitrix\Main,
	\Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

/**
 * Class UploadFilelostTable
 *
 * Fields:
 * <ul>
 * <li> ID int mandatory
 * <li> F_PATH string(255) optional
 * <li> SIZE int optional
 * </ul>
 *
 * @package Bitrix\Cleanmaster
 **/

class UploadFilelostTable extends Main\Entity\DataManager
{
	/**
	 * Returns DB table name for entity.
	 *
	 * @return string
	 */
	public static function getTableName()
	{
		return 'acrit_cleanmaster_upload_filelost';
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
				'title' => Loc::getMessage('UPLOAD_FILELOST_ENTITY_ID_FIELD'),
			),
			'F_PATH' => array(
				'data_type' => 'string',
				'validation' => array(__CLASS__, 'validateFPath'),
				'title' => Loc::getMessage('UPLOAD_FILELOST_ENTITY_F_PATH_FIELD'),
			),
			'SIZE' => array(
				'data_type' => 'integer',
				'title' => Loc::getMessage('UPLOAD_FILELOST_ENTITY_SIZE_FIELD'),
			),
		);
	}
	/**
	 * Returns validators for F_PATH field.
	 *
	 * @return array
	 */
	public static function validateFPath()
	{
		return array(
			new \Bitrix\Main\Entity\Validator\Length(null, 255),
		);
	}

	public static function clearTable()
	{
		\Bitrix\Main\Application::getConnection()->query("TRUNCATE TABLE " . self::getTableName());
	}

	public static function getLostSize()
	{
		$dm = self::getList(array(
			'select' => array('T_SIZE'),
			'runtime' => array(
				new \Bitrix\Main\Entity\ExpressionField('T_SIZE', 'SUM(SIZE)')
			)
		))->fetch();
		return $dm['T_SIZE'] / (1024 * 1024);
	}

	public static function getCntRows()
	{
		$rows = self::getList(array(
			'select' => array('CNT'),
			'runtime' => array(
				new \Bitrix\Main\Entity\ExpressionField('CNT', 'COUNT(*)')
			)
		))->fetchAll();

		return (int)$rows[0]['CNT'];
	}
}

?>