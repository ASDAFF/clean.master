<?php
namespace Acrit\Cleanmaster;

use Bitrix\Main,
	Bitrix\Main\Localization\Loc;
use Bitrix\Main\Web\Json;

Loc::loadMessages(__FILE__);

/**
 * Class LastDiagTable
 *
 * Fields:
 * <ul>
 * <li> ID int mandatory
 * <li> STEP_CODE string(100) optional
 * <li> PARAMS string optional
 * </ul>
 *
 * @package Acrit\Cleanmaster
 **/

class LastDiagTable extends Main\Entity\DataManager
{
	/**
	 * Returns DB table name for entity.
	 *
	 * @return string
	 */
	public static function getTableName()
	{
		return 'acrit_cleanmaster_last_diag';
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
				'title' => Loc::getMessage('acrit.cleanmaster_LAST_DIAG_ENTITY_ID_FIELD'),
			),
			'STEP_CODE' => array(
				'data_type' => 'string',
				'validation' => array(__CLASS__, 'validateStepCode'),
				'title' => Loc::getMessage('acrit.cleanmaster_LAST_DIAG_ENTITY_STEP_CODE_FIELD'),
			),
			'PARAMS' => array(
				'data_type' => 'text',
				'title' => Loc::getMessage('acrit.cleanmaster_LAST_DIAG_ENTITY_PARAMS_FIELD'),
			),
		);
	}

	/**
	 * Returns validators for STEP_CODE field.
	 *
	 * @return array
	 * @throws \Bitrix\Main\ArgumentTypeException
	 */
	public static function validateStepCode()
	{
		return array(
			new Main\Entity\Validator\Length(null, 100),
		);
	}

	public function addFinishedStep($stepCode, $proc)
	{
		global $DB;

		if ($proc['complete'] === false || trim($stepCode) == '') {
			return false;
		}

		$DB->Query('DELETE FROM ' . self::getTableName() . ' WHERE `STEP_CODE` = "' . $stepCode . '"');

		try {
			self::add([
				'STEP_CODE' => $stepCode,
				'PARAMS' => Json::encode($proc)
			]);
		} catch (Main\ArgumentException $e) {
		} catch (\Exception $e) {
		}

		return true;
	}

	public function returnSavedList()
	{
		$l = [];
		$r = self::getList([
			'order'         => ['STEP_CODE' => 'ASC'],
			'select'        => ['*']
		]);
		while ($a = $r->fetch()) {
			$a['PARAMS'] = (array)Json::decode($a['PARAMS']);
			$l[ $a['STEP_CODE'] ] = $a;
		}
		return $l;
	}

	static $lastList = [];

	public function returnSavedStep($stepCode)
	{
		if (trim($stepCode) == '') {
			return false;
		}
		if (count(self::$lastList) == 0) {
			self::$lastList = self::returnSavedList();
		}
		return self::$lastList[ $stepCode ];
	}

	public function clearAll()
	{
		global $DB;

		self::$lastList = [];
		$DB->Query('TRUNCATE TABLE ' . self::getTableName());
	}
}

?>