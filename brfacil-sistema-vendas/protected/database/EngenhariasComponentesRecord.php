<?php
/**
 * Auto generated by prado-cli.php on 2013-02-21 10:58:21.
 */
class EngenhariasComponentesRecord extends TActiveRecord
{
	const TABLE='engenharias_componentes';

	public $produtos_codigo;
	public $engenharias_produtos_codigo;
	public $quantidade;

	public static function finder($className=__CLASS__)
	{
		return parent::finder($className);
	}
	
	
	public static $RELATIONS=array
    (
        'produtos' => array(self::BELONGS_TO,
            'ProdutosRecord', 'produtos_codigo'),
        'engenharia' => array(self::MANY_TO_MANY,
            'EngenhariaRecord', 'engenharias_produtos_codigo'),
  
    );
	
}
?>