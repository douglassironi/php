<?php
Prado::using('Application.commom.Pedido');
class PedidosReport extends TPage
{
	//public $pedido;

	public  function onLoad($param){
		 
		parent::onLoad($param);
		if(!$this->IsPostBack)
		{
			$this->fnc_popula_produtos();
			$this->fnc_popula_cidades();
		}

	}
	
}

?>

