<?php

class Relatorios extends TPage
{
	
	public function onLoad($param) {
		parent::onLoad ( $param );
		if (! $this->IsPostBack) {
		/* Chama a lista  de produtos.*/
	   $this->fnc_tipos_relatorios();
	   $this->filtros();
		}
	
	}
	
	public function fnc_tipos_relatorios() {
		$lista["produtos"] = "Produtos";
		$lista["produtos_estoque_minimo"] = "Produtos Estoque Minimo";
		
        $this->rel->datasource=$lista;
        $this->rel->databind();
                
	}
	
	
	public function filtros() {
				$this->lbl_parametro_1->setText('Fornecedor');
		$pe = PessoasRecord::finder()->findAllBy_tipo_pessoa("FOR");
		foreach ($pe as $rg){
			$ls[$rg->codigo] = $rg->razao_social;
		}
		$this->parametro_1->datasource = $ls;
		$this->parametro_1->databind();
		$this->parametro_1->setText('Fornecedor');
		;
	}
	
	public function fnc_ajusta_filtros($sender,$param){
      $this->filtros();
	}
	
	public function bt_imprimir_relatorio($sender,$param){

	
	$pdf = new FPDF();
	$pdf->AddPage();
	$pdf->Header();
	//Corpo
	$xpos = 50;
	$xpos = $this->fnc_controla_quebra($xpos, $pdf);
	
    $cr = new TActiveRecordCriteria();
	if ($this->rel->Text=="produtos"){
		$wh = "data_exclusao is null ";
	}
	else{
		$wh = "quantidade_estoque < quantidade_minima and data_exclusao is null";
	}
	
	$cr->condition = $wh." and exists (select 1 from fornecedores_produtos where fornecedores_produtos.pessoas_codigo = ".$this->parametro_1->data." and fornecedores_produtos.produtos_codigo = produtos.codigo)";
	
	$pr = ProdutosRecord::finder()->findall($cr);
    if ($pr ){
	$xpos = $this->fnc_controla_quebra($xpos, $pdf);
        
    foreach ($pr as $rg){
    	$pdf->Text(10, $xpos, $rg->codigo);
    	$pdf->Text(30, $xpos, $rg->descricao);
    	$pdf->Text(90, $xpos, $rg->quantidade_estoque);
    	$pdf->Text(140, $xpos, $rg->quantidade_minima);
    	$pdf->Text(180, $xpos, $rg->valor_compra);
	    $xpos = $this->fnc_controla_quebra($xpos, $pdf);
    	 	    	     	
    	
    }
    
    }
	$arquivo = "tmp/relatorio_".date("YmdHis").".pdf";
	$pdf->Output($arquivo,"F");
	$this->Response->redirect($arquivo);
	}

	public  function fnc_controla_quebra($xpos,$pdf){
		if ($xpos > 280){
			$pdf->AddPage();
			$xpos = 50;
		}
		if ($xpos == 50){
			$pdf->Line(200,45,10,45);
			$pdf->Text(10, 43, "Relatorio:".ucfirst($this->rel->SelectedItem->text));
			$pdf->Text(120, 43, $this->lbl_parametro_1->text." = ".$this->parametro_1->SelectedItem->text);
			$pdf->Text(10, $xpos, "Codigo");
			$pdf->Text(30, $xpos, "Descricao");
			$pdf->Text(90, $xpos, "Quantidade Atual");
			$pdf->Text(140, $xpos, "Quantidade Minima");
			$pdf->Text(180, $xpos, "Valor Compra");
			$pdf->Line(200,$xpos+2,10,$xpos+2);
			$xpos+=5;
				
			
		}
	    else {
		$xpos+=5;
	    }
	    
		return $xpos;
	}

}

 ?>


 
 
 
 