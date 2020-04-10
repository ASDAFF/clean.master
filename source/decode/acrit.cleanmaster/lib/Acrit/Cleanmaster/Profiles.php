<?php
namespace Acrit\Cleanmaster;

use Bitrix\Main,
	Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

/**
 * Class ProfilesTable
 *
 * Fields:
 * <ul>
 * <li> ID int mandatory
 * <li> STEP_ID int optional
 * <li> PARAMS string optional
 * </ul>
 *
 * @package Bitrix\Cleanmaster
 **/

class ProfilesTable extends Main\Entity\DataManager
{
	/**
	 * Returns DB table name for entity.
	 *
	 * @return string
	 */
	public static function getTableName()
	{
		return 'acrit_cleanmaster_profiles';
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
				'title' => Loc::getMessage('acrit_cleanmaster_PROFILES_ENTITY_ID_FIELD'),
				'sortable' => true
			),
			'STEP_ID' => array(
				'data_type' => 'integer',
				'title' => Loc::getMessage('acrit_cleanmaster_PROFILES_ENTITY_STEP_ID_FIELD'),
				'sortable' => true
			),
			'PARAMS' => array(
				'data_type' => 'text',
				'title' => Loc::getMessage('acrit_cleanmaster_PROFILES_ENTITY_PARAMS_FIELD'),
			),
		);
	}

	public static function clearTable()
	{
		Main\Application::getConnection()->query("TRUNCATE TABLE " . self::getTableName());
	}
}
?>