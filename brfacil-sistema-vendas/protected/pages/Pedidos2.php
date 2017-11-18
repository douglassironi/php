<?php
class Pedidos2 extends TPage
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
	
	public function serverValidate($sender,$param)
	{
		
		if ($this->cliente->text !="" and $this->endereco->text != "" and $this->fone->text != "" ){
			if ($this->mesa->text =="" ){ 
			$param->IsValid=true;
			}
		}	
		 
		else if ($this->mesa->text =="" ){
				$param->IsValid=false;
			}
			//$param->IsValid=false;
				
		
			
		
	}
	
	
		
	public function  buscaPessoa($sender,$param){
	  $ps = PessoasRecord::finder()->findbytelefone($this->fone->text);
	  if (isset($ps)){
	  	$this->endereco->settext($ps->endereco);
	  	$this->cliente->settext($ps->razao_social);
	  	$this->cidade->data = $ps->cidades_codigo;
	    $this->clientesCodigo->value = $ps->codigo;
	    $this->tab_pedido->render($param->getNewWriter());
	  	
	}
	
	}
	public function addPessoas($sender,$param){
		
		if ($this->Page->IsValid) {
		
		  if ($this->mesa->text !=""){
		  	$this->iniciaPedido(null);
		  }
		  else {
		  	$pk = null;
		  	$ps = null;
		   $pk = $this->clientesCodigo->value;
		   if (isset($pk)){
			$ps = new PessoasRecord();
			if ($this->cliente->text = ""){
			}
				$ps->razao_social = $this->cliente->text;
				$ps->endereco = $this->endereco->text;
				$ps->telefone = $this->fone->text;
				$ps->cidades_codigo = $this->cidade->data;
				$ps->save();
				$this->clientesCodigo->value = $ps->codigo;
			   	}	
		   	$this->iniciaPedido($this->clientesCodigo->value);
		  }	
			$this->tab_it_pedido->visible = true;	
			$this->pedidos->visible = false;			
		}
		$this->tab_pedido->render($param->getNewWriter());
	}
	
	public function iniciaPedido($cli){
		
		$pd = New Pedido();
		$ped = $pd->criaPedido($this->mesa->text, $cli);	
		$this->pedido->text = 	$ped->codigo;
		$this->pedidoLBL->text = $ped->codigo;		
		$this->ClienteLBL->text = $this->cliente->text;
		 
	}

	public function fnc_popula_produtos(){
		$pr = ProdutosRecord::finder()->findAll();
		foreach ($pr as $rg)
		{
			$ls[$rg->codigo] = $rg->descricao;
		}
		$this->lst_produto->datasource = $ls;
		$this->lst_produto->databind();
	}

	public function fnc_popula_cidades(){
		$pr = CidadesRecord::finder()->findAll();
		foreach ($pr as $rg)
		{
			$ls[$rg->codigo] = $rg->descricao;
		}
		$this->cidade->datasource = $ls;
		$this->cidade->databind();
	}
	
	public function ExcluirItem($ip) {
		$input=$this->produtosck;
		$indices=$input->SelectedIndices;
		$result='';
		foreach($indices as $index)
		{
			$item=$input->Items[$index];
			$result.="(Index: $index, Value: $item->Value, Text: $item->Text)";
			$ins = new InsumosRetiradosPedidosRecord();
			$ins->pedidos_codigo = $this->pedidoLBL->text;
			$ins->produtos_codigo = $item->Value;
			$ins->item_pedido     =$ip;
			$ins->save();
		}
	}

    public function additem($sender,$param) {
    	$pr = ProdutosRecord::finder()->findbypk($this->lst_produto->data);
    	$pd = new Pedido();
    	$ret = $pd->addItemPedido($this->pedidoLBL->text,$this->lst_produto->data,$this->quantidade->text);
    	$this->ExcluirItem($ret->codigo);
    	$this->lst_produto->data = 1;
    	$this->quantidade->text = null;
    	$this->produtosck->datasource=null;
    	$this->produtosck->dataBind();    	
    	$this->tab_pedido->render($param->getNewWriter());
    	
    }
public function mostraInsumos($sender,$param) {
		$pr = ProdutosRecord::finder()->findByPk($this->lst_produto->data);
		if (isset($pr)) {
			if ($pr->engenharias_produtos_codigo != 0 ){
				$cr = new TActiveRecordCriteria();
				$cr->condition = '
						engenharias_produtos_codigo = '.$pr->engenharias_produtos_codigo.'
						and produtos_codigo not in (select produtos_codigo from insumos_retirados_pedidos 
						where pedidos_codigo = '.$this->pedidoLBL->text.')';
				$is = EngenhariasComponentesRecord::finder()->findAll($cr);
				$this->tab_it_pedido->render($param->getNewWriter());
				foreach ($is as $re){
					$l[]=array('codigo'=>$re->produtos_codigo,'descricao'=>' '.$re->produtos->descricao);
				}
				$this->produtosck->datasource=$l;
				$this->produtosck->dataBind();
				

			}
		}
	}
	public function getData() {
			$pr = ProdutosRecord::finder()->findByPk($this->lst_produto->data);
		if (isset($pr)) {
			if ($pr->engenharias_produtos_codigo != 0 ){
				$cr = new TActiveRecordCriteria();
				$cr->condition = '
						engenharias_produtos_codigo = '.$pr->engenharias_produtos_codigo.'
						and produtos_codigo not in (select produtos_codigo from insumos_retirados_pedidos 
						where pedidos_codigo = '.$this->pedidoLBL->text.')';
				$is = EngenhariasComponentesRecord::finder()->findAll($cr);
				return $is;
			}
		}
		
	}
}

?>

