<?php

class PedidosPagamentos extends TPage
{
	
	public function onLoad($param) {
		parent::onLoad ( $param );
		if (! $this->IsPostBack) {
		/* Chama a lista  de produtos.*/
		$this-> itensPedidos($_GET['ped']);
		$this->populaCondicoes();
	}
	
	}
	
	public function itensPedidos($pd) {
		if (isset($_GET['it']))
		{
			$ite = ItensPedidosRecord::finder()->findbycodigo($_GET['it']);
		    if ($ite->status_item != 'C')
            {     $ite->status_item = 'C'; }
			else
            {  $ite->status_item = "F";}
            
            $ite->save();
		}
	   
		
		$it = ItensPedidosRecord::finder()->findAllbyPedidos_codigo($pd);
		$this->itenspedidos->datasource=$it;
		$this->itenspedidos->databind();
		$tot=0;
		foreach ($it as $reg){
			if ($reg->status_item <> "C")
			$tot = $tot+$reg->valor;
		}
		
		$this->total->text=$tot;
		
	}

	
	public function btCancelaItem($sender,$param){
		
		
	}
	
	public function populaCondicoes() {
		$con = CondicoesPagamentosRecord::finder()->findall();
		foreach ($con as $reg){
			$ls[$reg->codigo] = $reg->descricao;
		}
		$this->forma_pagamento->datasource=$ls;
		$this->forma_pagamento->databind();
		
	}
	
	public function bt_finaliza_pedido($sender,$param){
		
		if (((float)$this->valor_documento->text - (float)$this->total->text) < 0 ) {
		   $this->msg->setText('<div class="alert alert-danger" role="alert" > Atenção, o valor pago é menor que o devido.</div>');	
		}
		else {
		
		$pd = PedidosRecord::finder()->findByCodigo($_GET['ped']);
		$pd->status_pedido = 'F';
		$pd->save();

		$cp = CondicoesPagamentosRecord::finder()->findby_Codigo($this->forma_pagamento->data);
		
		$mv = new MovimentacoesFinanceirasRecord();
		$mv->data_documento = date("Y-m-d");
		$mv->data_vencimento= date('Y-m-d', strtotime("+".$cp->dias_credito." days"));
		$mv->documento= $pd->codigo;
		$mv->forma_pagamento = $this->forma_pagamento->data;
		$mv->tipo_movimento = "E";
		$mv->valor_documento = $this->total->text ;
		$mv->observacao = "AUT:".$this->Documento->text;
		if ($cp->dias_credito = 0){ 
			$mv->valor_pago= $this->total->text ;
			$mv->data_pagamento = date("Y-m-d");
		}
		$mv->save();
		
		$this->valor_troco->settext(((float)$this->valor_documento->text - (float)$this->total->text));
		$this->bt_calcula->enabled="false";
		$this->msg->setText(" ");
		Util::AtualizaInsumos();
		}
		//$this->Response->redirect($this->Service->constructUrl("Caixa"));
	}
	public function bt_confirma($sender,$param){
		$this->Response->redirect($this->Service->constructUrl("Caixa"));		
	}
	public function bt_imprimir_pedido($sender,$param){

	$pd = PedidosRecord::finder()->findByCodigo($_GET['ped']);
	$it = ItensPedidosRecord::finder()->findAllbyPedidos_codigo($pd->codigo);
	
	$pdf = new FPDF();
	$pdf->AddPage();
	$pdf->Header();
	//Corpo
	$pdf->Line(200,45,10,45);
	$pdf->Text(10,50 ,'Pedido:'	. $_GET['ped']);
	$xpos = 55;
	if ($pd->pessoas_codigo){
		
	$pess = PessoasRecord::finder()->findbypk($pd->pessoas_codigo);
	$pdf->Text(10,$xpos,'Cliente:'. $pd->pessoas_codigo."-".$pess->razao_social);
	$xpos+=5;
	$pdf->Text(10,$xpos,'Endereco:'. $pess->endereco);
	$xpos+=5;
	$pdf->Text(10,$xpos,'Telefone:'. $pess->telefone);
		
	}
	else 
	{
	  $pdf->Text(10,$xpos ,'Mesa:'. $pd->mesa);
	}
	$xpos+=5;
	$pdf->Text(10,$xpos ,'Atendente:'. $pd->atendente);
	$xpos+=5;
	$pdf->Text(10,$xpos,'Data Pedido:'. date($pd->data_hora));
	$xpos+=5;
	$pdf->Line(200,$xpos,10,$xpos);
	$xpos+=5;
		$pdf->Text(10,$xpos ,'Detalhes do pedido');
	$xpos+=3;
	$pdf->Line(200,$xpos,10,$xpos);
	$xpos+=5;
	$pdf->Text(10,$xpos,'Codigo');
	$pdf->Text(30,$xpos,'Descricao');
	$pdf->Text(150,$xpos,'Valor');
	$pdf->Text(180,$xpos,'Status');
	$xpos+=5;
	$total_pedido = 0;
	foreach ($it as $pr){
	    $pdf->Text(10,$xpos,$pr->produtos->codigo);
		$pdf->Text(30,$xpos,$pr->produtos->descricao);
		$pdf->Text(180,$xpos,$pr->status_item);
		$pdf->Text(150,$xpos,"R$ ".number_format($pr->valor,2,',','.'));
		
	  if ($pr->status_item != 'C'){
		$total_pedido+=$pr->valor;
		}
		$xpos+=5;
		
	}
	$xpos+=10;
	$pdf->Text(120,$xpos,'Valor Total:');
	$pdf->Text(150,$xpos,'R$ '.number_format($total_pedido,2,',','.'));
	
	
		
	$pdf->Output("tmp/pedido_".$_GET['ped'].".pdf","F");
	$this->Response->redirect("tmp/pedido_".$_GET['ped'].".pdf");
	}


}

 ?>


 
 
 
 