<?php

 
class Pedido 
{
 
  public function criaPedido($mesa, $cliente){
 	if ($mesa !=""){
 	 $cr = new TActiveRecordCriteria();
 	 $cr->condition = "status_pedido != 'F' and mesa = ".$mesa; 
 	 $pd = PedidosRecord::finder()->find($cr);
 	  if ($pd !=""){
 	  	return $pd;
 	  }
 	}   
 	$pedido = new PedidosRecord();
 	$pedido->mesa = $mesa;
 	$pedido->status_pedido = 'A'; 	
 	
 	if (isset($cliente)) {
 	   $pedido->pessoas_codigo = $cliente; //Pode ser um cliente ou apenas o cartao.
 	}
  	
 	$pedido->save();
 	
 	return $pedido;
 	
 }

 public function addItemPedido($pedido,$item, $quantidade)
 {
 	$it = ProdutosRecord::finder()->findBycodigo($item); 	
 	$ip = new ItensPedidosRecord();
 	$ip->pedidos_codigo = $pedido;
 	$ip->produtos_codigo = $item;
 	$ip->quantidade = $quantidade;
 	$ip->status_item = 'A';
 	
 	if (!isset($it->valor_venda))
 	{
 		$it->valor_venda = 0;
 	}
 	$ip->valor = ($quantidade * $it->valor_venda);
 	$ip->save();
 	$it->quantidade_estoque = $it->quantidade_estoque - $quantidade;
 	$it->save(); 	
 	
 	Pedido::fnc_gera_consumo($ip);
 	
 	return $ip;
 }
 
 
 
 public function fnc_gera_consumo($it){
 	$cr = new TActiveRecordCriteria();
 	$cr->condition = ' engenharias_produtos_codigo is not null
 			 and codigo = '.$it->produtos_codigo;
 	$prd = ProdutosRecord::finder()->findAll($cr);
 	if ($prd != null){
	 	foreach ($prd as $reg){
	 		$cr->condition = 'engenharias_produtos_codigo = '.$reg->engenharias_produtos_codigo;
	 		
	 		$eng = EngenhariasComponentesRecord::finder()->findAll($cr);

	 		foreach ($eng as $ic) {
	 		$csm = new MovimentacoesConsumosPedidosRecord();
	 		$csm->itens_pedidos_codigo = $it->codigo;
	 		$csm->produtos_codigo = $ic->produtos_codigo;
	 		$csm->quantidade_consumida = $ic->quantidade;
	 		$csm->save();
	 		}
	 		
	 	}
 	}		
 	
    else 
    {
    	$csm = new MovimentacoesConsumosPedidosRecord();
    	$csm->itens_pedidos_codigo = $it->codigo;
    	$csm->produtos_codigo = $it->produtos_codigo;
    	$csm->quantidade_consumida = $it->quantidade;
    	$csm->save();    	 
    }
 	
 }

 public function geraArquivoImpressora($ped)
 {
 	$co = CozinhasRecord::finder()->findall();
 	foreach ($co as $rg){

 		$cr = new TActiveRecordCriteria();
 		$cr->condition = 'pedidos_codigo = '.$ped->codigo.
 		" and status_item = 'A' ". 
 		" and exists(select 1 from produtos p
 				where p.codigo = produtos_codigo
 				  and p.cozinhas_codigo = ".$rg->codigo.")";
 		$itens = ItensPedidosRecord::finder()->findAll($cr);
 			
 		if ($itens) {
 		$arquivo = "tmp/PED_".$ped->codigo."_IMP"."COZ".$rg->codigo.date('Ymdhis').".TXT";
	 	$ar = fopen($arquivo, "w");
	 	$wr = fwrite($ar, 'Pedido Nr:'.$ped->codigo."\r\n");
	 	$wr = fwrite($ar, 'Cozinha:'.$rg->descricao."\r\n");
	 	$wr = fwrite($ar, str_pad('Produto',20,' ').str_pad('Qtde',5,' ').str_pad('Valor',5,' ')."\r\n");
	 	
	 	$wr = fwrite($ar, '------------------------------------------------------'."\r\n");
	 	foreach ($itens as $it ){
	 		$wr = fwrite($ar, str_pad(substr($it->produtos->descricao,0,20),20,' ').'  '.str_pad($it->quantidade,5,' ').'  '.str_pad($it->valor,5,' ')."\r\n");
	 		$rt = null;
	 		$ir = InsumosRetiradosPedidosRecord::finder()->findallBy_item_pedido($it->codigo);	
	 		foreach ($ir as $rt ){	
	 		    $pr = ProdutosRecord::finder()->findBy_Codigo($rt->produtos_codigo);
	 		    $wr = fwrite($ar,'###### Sem => '. str_pad(substr($pr->descricao,0,20),20,' ')."\r\n");
	        	}  
	 		}          
	 	
	 	$wr = fwrite($ar, '-------------------------------------------------------'."\r\n");
	 	$wr = fwrite($ar, 'Numero Mesa:'.$ped->mesa."\r\n");
	 	$wr = fwrite($ar,'Data:'.$ped->data_hora."\r\n");
	 	$wr = fwrite($ar,'Atendente:'.$this->user->name."\r\n");
	 	fclose($ar);
	 	
	 	//$this->imprimePedido($arquivo,"teste");
	 	
	 	$ped->status_pedido = 'I';
	 	$ped->save();
 		}
 	}	 	
 	
 }
 
 public function imprimePedido($arquivo, $printer) {
 	//configure o nome da sua impressora
 	
 	if($ph = printer_open($printer))
 	{
 		// Get file contents
 		//abrindo o arquivo de texto
 		$fh = fopen($arquivo, "rb");
 		//llendo o arquivo de texto
 		$content = fread($fh, filesize($arquivo));
 		fclose($fh);
 	
 		// Set print mode to RAW and send PDF to printer
 		printer_set_option($ph, PRINTER_MODE, "RAW");
 		printer_write($ph, $content);
 		printer_close($ph);
 	} 
 
}
}
?>
