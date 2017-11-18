<?php
//Prado::using('Application.commom.Pedido');
class PedidosCozinha extends TPage
{

public function OnLoad() {
     /* Chama a lista  de produtos.*/
	
	 $this-> PedidosAbertos();
	 $this->populaCozinhas();
   }  
  
   
   public  function populaCozinhas() {
   	$co = CozinhasRecord::finder()->findall();
   	$sl = null;
   	foreach ($co as $rg){
   		$list[$rg->codigo] = $rg->descricao;
   		if (!$sl)
   			$sl = $rg->codigo;
   	}
   	$this->cozinha->datasource=$list;
   	$this->cozinha->databind();
   	$this->cozinha->data = $sl; 
   }

 public function PedidosAbertos(){
 	$item = null;
 	if (isset($_GET['it'])){
 	$item = $_GET['it'];
 	}
 	if (isset($item)){
 	  $item = ItensPedidosRecord::finder()->findByPk($item);
 	  if ($item->status_item == "E")
 	  	$item->status_item = 'F';
 	  if ($item->status_item == "I")
 	  	$item->status_item = 'E';
 	  if ($item->status_item == "A")
 	  	$item->status_item = 'E';
 	  
 	  
 	  
 	  $item->save();
 	}
 	
 	$cr = New TActiveRecordCriteria();
 	$cr->condition = "status_pedido = 'I' and mesa is not null";
 	$pd = PedidosRecord::finder()->findAll($cr);
 	$pdi = "(";
 	foreach ($pd as $reg ){
 		$pdi.=$reg->codigo.",";
 	}
 	$pdi.="0)"; 	
 	if ($this->cozinha->data){
 		$cr->condition = "produtos_codigo in (select codigo from produtos where cozinhas_codigo=".$this->cozinha->data.") and status_item in ('A','E','I') and pedidos_codigo in".$pdi;
 	}else 
 	{
 		$cr->condition = "produtos_codigo in (select codigo from produtos where cozinhas_codigo=cozinhas_codigo) and status_item in ('A','E','I') and pedidos_codigo in".$pdi;
 	}
 		$it = ItensPedidosRecord::finder()->findAll($cr);
 	
 	 
 	$this->itpd->datasource = $it;
 	$this->itpd->databind();
 }  
  
   
}

?>

