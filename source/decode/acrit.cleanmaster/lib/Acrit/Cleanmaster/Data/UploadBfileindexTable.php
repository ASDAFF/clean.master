<?
namespace Acrit\Cleanmaster\Data;

use \Bitrix\Main,
	\Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

/**
 * Class UploadBfileindexTable
 *
 * Fields:
 * <ul>
 * <li> ID int mandatory
 * <li> F_PATH string(255) optional
 * <li> FILE_ID int optional
 * </ul>
 *
 * @package Bitrix\Cleanmaster
 **/
class UploadBfileindexTable extends Main\Entity\DataManager
{
	/**
	 * Returns DB table name for entity.
	 *
	 * @return string
	 */
	public static function getTableName()
	{
		return 'acrit_cleanmaster_upload_bfileindex';
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
				'title' => Loc::getMessage('UPLOAD_BFILEINDEX_ENTITY_ID_FIELD'),
				'sortable' => true
			),
			'F_PATH' => array(
				'data_type' => 'string',
				'validation' => array(__CLASS__, 'validateFPath'),
				'title' => Loc::getMessage('UPLOAD_BFILEINDEX_ENTITY_F_PATH_FIELD'),
				'sortable' => true
			),
			'FILE_ID' => array(
				'data_type' => 'integer',
				'title' => Loc::getMessage('UPLOAD_BFILEINDEX_ENTITY_FILE_ID_FIELD'),
				'sortable' => true
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

	public static function checkFileExists($filename)
	{
		$row = self::getList(array(
			'select' => array('ID'),
			'filter' => array('F_PATH' => $filename),
			'limit' => 1
		))->fetch();

		return ($row['ID'] > 0);
	}

	public static function selectFileExists($filename)
	{
		$rows = self::getList(array(
			'select' => array('F_PATH'),
			'filter' => array('F_PATH' => $filename),
		))->fetchAll();

		$rowsEx = array();
		foreach ($rows as $row) {
			$rowsEx[$row['F_PATH']] = 1;
		}

		return $rowsEx;
	}
}
?>