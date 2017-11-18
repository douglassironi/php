<?php
   class Util{
  	
  	
   	public static  function fnc_busca_fornecedores(){
   		
   		$ps = PessoasRecord::finder()->findallby_tipo_pessoa("FOR");
   		return $ps;
   		
   	}
   		 
   	
   	
  	public static  function fnc_gera_lista($reg){
  		foreach ($reg as $it){
  			if (isset($it->descricao))
  			$lst[$it->codigo]=$it->descricao;
  			if (isset($it->nome))
  			$lst[$it->codigo]=$it->nome;
  			if (isset($it->razao_social))
  			$lst[$it->codigo]=$it->razao_social;
  		}
  		return $lst;
  	}
  	
  	
  	public function AtualizaInsumos() {
  		$cr = new TActiveRecordCriteria();
  		$cr->condition = "data_integracao is null";
  		$consumo = MovimentacoesConsumosPedidosRecord::finder()->Findall($cr);
  		$rcon = 0;
  		foreach ($consumo as $reg){
  			 
  			$rcon+=1;
  	
  		}
  		$rg = 0;
  		if($consumo) {
  			foreach ($consumo as $reg){
  				$rg+=$rg;
  	
  				$pr = ProdutosRecord::finder()->findByCodigo($reg->produtos_codigo);
  				if ($pr){
  					$pr->quantidade_estoque = $pr->quantidade_estoque - $reg->quantidade_consumida;
  					$pr->save();
  				}
  				$reg->data_integracao = date("Ymd");
  				$reg->save();
  	
  			}
  		}
  	
  	
  	}
  	 
  	
  	
  }



?>