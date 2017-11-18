<?php
class Caixa extends TPage
{

public function OnLoad() {
     /* Chama a lista  de produtos.*/
	$dsn = 'mysql:host=localhost;dbname='.$_SESSION ['db'];
	$conn = new TDbConnection($dsn, 'root','master008');
	TActiveRecordManager::getInstance()->setDbConnection($conn);
	
	 $this-> mesasAbertas();
   }  
  

 public function mesasAbertas(){
 	$cr = New TActiveRecordCriteria();
 	$cr->condition = "status_pedido != 'F'";
 	$pd = PedidosRecord::finder()->findAll($cr);
 	$this->mesas->datasource = $pd;
 	$this->mesas->databind();
 }  
  
   
}

?>

