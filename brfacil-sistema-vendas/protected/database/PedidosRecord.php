<?php
/**
 * Auto generated by prado-cli.php on 2013-02-21 10:58:21.
 */
class PedidosRecord extends TActiveRecord
{
	const TABLE='pedidos';

	public $codigo;
	public $pessoas_codigo;
	public $mesa;
	public $data_hora;
	public $status_pedido;
	public $atendente;
    public $forma_pagamento;
    public $valor_servico;
	
	public static function finder($className=__CLASS__)
	{
		return parent::finder($className);
	}
	
   function OnInsert($param) {
		// parent method should be called to raise the event
		parent::OnInsert ( $param );
		if (isset($_SESSION ['atendente']))
		$this->atendente = $_SESSION ['atendente'];
	}
	
	public static $RELATIONS=array
    (
        'pessoas' => array(self::BELONGS_TO,
            'PessoasRecord', 'pessoas_codigo'),
        'itens' => array(self::MANY_TO_MANY,
            'ItensPedidosRecord', 'pedidos_codigo'),
  
    );
	
}
?>