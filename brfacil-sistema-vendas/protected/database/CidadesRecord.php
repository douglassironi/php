<?php
/**
 * Auto generated by prado-cli.php on 2013-02-21 10:58:21.
 */
class CidadesRecord extends TActiveRecord
{
	const TABLE='cidades';

	public $codigo;
	public $UF;
	public $descricao;

	public static function finder($className=__CLASS__)
	{
		return parent::finder($className);
	}
	
	
	public static $RELATIONS=array
    (
        'pessoas' => array(self::MANY_TO_MANY,
            'PessoasRecord', 'cidades_codigo'),

  
    );
	
}
?>