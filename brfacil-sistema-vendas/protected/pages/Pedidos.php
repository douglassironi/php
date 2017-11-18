<?php

class Pedidos extends TPage {
	// public $pedido;
	public function onLoad($param) {
		parent::onLoad ( $param );
		if (! $this->IsPostBack) {
		   if (isset($_GET["cartao"]))
		   {
		   	$this->mesa->settext($_GET["cartao"]);
            $this->iniciaPedido(null);
		   	$this->tab_it_pedido->visible = true;
		   	$this->pedidos->visible = false;
		   }	
			$this->fnc_popula_cidades ();
			$this->populaCategorias ();
			$this->fnc_popula_produtos();
		}
	}
	public function serverValidate($sender, $param) {
		if ($this->cliente->text != "" and $this->endereco->text != "" and $this->fone->text != "") {
			if ($this->mesa->text == "") {
				$param->IsValid = true;
			}
		} 

		else if ($this->mesa->text == "") {
			$param->IsValid = false;
		}
		// $param->IsValid=false;
	}

	

	public function populaCategorias() {
		$cat = CategoriasRecord::finder ()->findAll ();
		foreach ( $cat as $rg ) {
			$ls [$rg->codigo] = $rg->descricao;
		}
		$this->categorias->datasource = $ls;
		$this->categorias->databind ();
		
		
		$this->fnc_popula_produtos ($cat);
	}
	public function listaProdutos($sender, $param) {
		$cat = CategoriasRecord::finder ()->findbypk($this->categorias->data);
		$this->fnc_popula_produtos($cat);

		$this->tab_pedido->render ( $param->getNewWriter () );
	}
	public function buscaDadosClientes($sender, $param) {
		$ps = PessoasRecord::finder ()->findbytelefone ( $this->fone->text );
		if (isset ( $ps )) {
			$this->endereco->settext ( $ps->endereco );
			$this->cliente->settext ( $ps->razao_social );
			$this->cidade->data = $ps->cidades_codigo;
			$this->clientesCodigo->value = $ps->codigo;
			$this->validate_campos->IsValid = true;
			$this->ClienteLBL->text = $this->cliente->text;
			$this->msgCab->text = '<div class="alert alert-success" role="alert">	Pessoa localizada com sucesso.</div>';
		}
		else{
			$this->endereco->settext ( null );
			$this->cliente->settext ( null );
			$this->fone->settext (null);
			$this->clientesCodigo->value = null;
			$this->msgCab->text = '<div class="alert alert-warning" role="alert">	Pessoa nao localizada com esse telefone, proceda o cadastro.</div>';
		}
		$this->tab_pedido->render ( $param->getNewWriter () );
		
	}
	public function addPessoas($sender, $param) {
		if ($this->Page->IsValid) {
				
			if ($this->mesa->text !="") {
				$this->iniciaPedido ( null );
			   }
			 else {
				
				$pk = null;
				$ps = null;
				$pe = PessoasRecord::finder()->findbypk($this->clientesCodigo->value);
				
				if (!$pe){
					$ps = new PessoasRecord ();
					print("nao achou");
					//if ($this->cliente->text) {
						$ps->razao_social = $this->cliente->text;
						$ps->endereco = $this->endereco->text;
						$ps->telefone = $this->fone->text;
						$ps->cidades_codigo = $this->cidade->data;
						$this->ClienteLBL->text = $this->cliente->text;
						$ps->save ();
						$this->msgCab->text = '<div class="alert alert-success" role="alert">	Pessoa adicionada com sucesso.</div>';
						$this->clientesCodigo->value = $ps->codigo;
				//}
			       }
			$this->iniciaPedido ( $this->clientesCodigo->value );
		}	
			$this->tab_it_pedido->visible = true;
			$this->pedidos->visible = false;
		}
		$this->tab_pedido->render ( $param->getNewWriter () );
	}
	public function iniciaPedido($cli) {
		$pd = new Pedido ();
		$ped = $pd->criaPedido ( $this->mesa->text, $cli );
		$this->pedido->text = $ped->codigo;
		$this->pedidoLBL->text = $ped->codigo;
		$this->ClienteLBL->text = $this->cliente->text;
	}
	public function fnc_popula_produtos() {
		$cr = new TActiveRecordCriteria();
		$where = 'and 1=1';
		if ($this->categorias->data) {
		$where = "and categorias_codigo = ".$this->categorias->data;
		}
		
		$cr->condition = "data_exclusao is null and tipos_produtos_codigo in (select codigo from tipos_produtos
				where insumo = 0)".$where;
		
		
		$pr = ProdutosRecord::finder ()->findAll($cr);
		$ls [0] = "Escolha um produto da categoria";
		foreach ( $pr as $rg ) {
			$ls [$rg->codigo] = $rg->descricao."   Disponivel:".$rg->quantidade_estoque;
		}
		$this->lst_produto->datasource = $ls;
		$this->lst_produto->databind ();
	}
	public function fnc_popula_cidades() {
		$pr = CidadesRecord::finder ()->findAll ();
		foreach ( $pr as $rg ) {
			$ls [$rg->codigo] = $rg->descricao;
		}
		$this->cidade->datasource = $ls;
		$this->cidade->databind ();
	}
	public function ExcluirItem($ip) {
		$input = $this->produtosck;
		$indices = $input->SelectedIndices;
		$result = '';
		foreach ( $indices as $index ) {
			$item = $input->Items [$index];
			$result .= "(Index: $index, Value: $item->Value, Text: $item->Text)";
			$ins = new InsumosRetiradosPedidosRecord ();
			$ins->pedidos_codigo = $this->pedidoLBL->text;
			$ins->produtos_codigo = $item->Value;
			$ins->item_pedido = $ip;
			$ins->save ();
		}
	}
	public function additem($sender, $param) {
		$pr = ProdutosRecord::finder ()->findbypk ( $this->lst_produto->data );
		if (isset($pr)){
		$pd = new Pedido ();
		$ret = $pd->addItemPedido ( $this->pedidoLBL->text, $this->lst_produto->data, 1/*$this->quantidade->text*/ );
		$this->ExcluirItem ( $ret->codigo );
		$this->msg->settext ( '<div class="alert alert-success" role="alert">	Produto ' . $this->lst_produto->text . ' adicionado com sucesso.</div>' );
		$this->lst_produto->data = 0;
		}
		$this->quantidade->text = null;
		$this->tab_insumos->visible = false;
		$this->produtosck->datasource = null;
		$this->produtosck->dataBind ();
		
		$this->tab_pedido->render ( $param->getNewWriter () );
	}
	public function mostraInsumos($sender, $param) {
		
		$pr = ProdutosRecord::finder ()->findByPk ( $this->lst_produto->data );

		$this->imagem_produto->data=$pr->imagem;
		
		$this->tab_insumos->visible = false;
		$this->tab_pedido->render ( $param->getNewWriter () );
			
		$l = null;
		if (isset ( $pr )) {
			if ($pr->engenharias_produtos_codigo != 0) {
				$cr = new TActiveRecordCriteria ();
				$cr->condition = '
						engenharias_produtos_codigo = ' . $pr->engenharias_produtos_codigo;
				$is = EngenhariasComponentesRecord::finder ()->findAll ( $cr );
				if ($is){
					$this->tab_insumos->visible = true;
					$this->tab_pedido->render ( $param->getNewWriter () );						
				}
				
				foreach ( $is as $re ) {
					$l [] = array (
							'codigo' => $re->produtos_codigo,
							'descricao' => ' ' . $re->produtos->descricao 
					);
				}
				$this->produtosck->datasource = $l;
				$this->produtosck->dataBind ();
				$this->tab_pedido->render ( $param->getNewWriter () );
			}
		}
		else {
			$this->tab_insumos->visible = false;
			$this->produtosck->datasource = $l;
			$this->produtosck->dataBind ();
			$this->tab_pedido->render ( $param->getNewWriter () );
		}
		$this->btAdd->focus();
	}
	public function finalizarPedido($sender, $param) {
		$ped = PedidosRecord::finder ()->findbypk ( $this->pedidoLBL->text );

		//$ped->forma_pagamento = $this->forma_pagamento->data;
		$ped->save();
	//	$pd = new Pedido ();
		Pedido::geraArquivoImpressora ( $ped );
		$this->msg->settext ( '<div class="alert alert-success" role="alert">	Pedido finalizado com sucesso.</div>' );
		$this->Response->redirect ( $this->Service->constructUrl ( 'Pedidos' ) );
		
	}
	
	public function getData() {
		$pr = ProdutosRecord::finder ()->findByPk ( $this->lst_produto->data );
		if (isset ( $pr )) {
			if ($pr->engenharias_produtos_codigo != 0) {
				$cr = new TActiveRecordCriteria ();
				$cr->condition = '
						engenharias_produtos_codigo = ' . $pr->engenharias_produtos_codigo . '
						and produtos_codigo not in (select produtos_codigo from insumos_retirados_pedidos 
						where pedidos_codigo = ' . $this->pedidoLBL->text . ')';
				$is = EngenhariasComponentesRecord::finder ()->findAll ( $cr );
				return $is;
			}
		}
	}
}

?>
