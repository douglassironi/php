<?php
/**
 * Auto generated by prado-cli.php on 2013-02-21 10:58:31.
 */
class RolesRecord extends TActiveRecord
{
	const TABLE='roles';

	public $codigo;
	public $role;
	public $descricao;

	public static function finder($className=__CLASS__)
	{
		return parent::finder($className);
	}

	public static $RELATIONS=array
    (
        'menus' => array(self::MANY_TO_MANY,
            'MenusRecord', 'roles_codigo'),
        'usuarios_roles' => array(self::MANY_TO_MANY,
            'UsuariosRolesRecord', 'roles_codigo'),
    );
	
	
}
?>