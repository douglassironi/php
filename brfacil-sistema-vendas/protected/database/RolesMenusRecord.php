<?php
/**
 * Auto generated by prado-cli.php on 2013-02-21 10:58:31.
 */
class RolesMenusRecord extends TActiveRecord
{
const TABLE='roles_menus';

	public $menus_codigo;
	public $roles_codigo;
	
	public static function finder($className=__CLASS__)
	{
		return parent::finder($className);
	}

	public static $RELATIONS=array
    (
        'menus' => array(self::BELONGS_TO,
            'MenusRecord', 'menus_codigo'),
        'roles' => array(self::BELONGS_TO,
            'RolesRecord', 'roles_codigo'),
    );
	
	

	
}
?>