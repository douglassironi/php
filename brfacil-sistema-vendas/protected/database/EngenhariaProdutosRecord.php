<?php
/**
 * Auto generated by prado-cli.php on 2013-02-21 10:58:21.
 */
class EngenhariaProdutosRecord extends TActiveRecord
{
	const TABLE='engenharias_produtos';

	public $codigo;
	public $descricao;

	public static function finder($className=__CLASS__)
	{
		return parent::finder($className);
	}
	
	
	public static $RELATIONS=array
    (
        'produtos' => array(self::BELONGS_TO,
            'ProdutosRecord', 'engenharias_produtos_codigo'),

  
    );
	
}
?>