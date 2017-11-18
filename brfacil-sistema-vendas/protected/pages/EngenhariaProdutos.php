<?php

class EngenhariaProdutos extends TPage
{

	

	public function onInit ($param)
	{
		parent::onInit($param);
		if (!$this->getIsPostBack() && !$this->getIsCallBack())
		{
	
			$this->populateProductList();
			$this->populateShoppingList();
			$this->popCategoria();
		}
	}
	
	public function popCategoria(){
		foreach (CategoriasRecord::finder()->findAll() as $reg){
			
			$ls[$reg->codigo] = $reg->descricao;
		}
		$this->categoria->datasource = $ls;
		$this->categoria->databind();
		
	}
	
	private function getProductData ()
	{
		$criterio = new TActiveRecordCriteria();
		$criterio->Condition = "tipos_produtos_codigo in (select codigo from tipos_produtos t where t.insumo = '1')";
		$prod = ProdutosRecord::finder()->findAll($criterio);
		
		
		
		foreach  ($prod as $reg){
			$list[] = array('ProductId'=>$reg->codigo,'descricao'=>strtoupper($reg->descricao));			
		}
		
	
	return $list;
	}
	
	private function getProduct ($key)
	{
		foreach ($this->getProductData() as $product)
		if ($product['ProductId']==$key) return $product;
		return null;
	}
	
	protected function populateProductList ()
	{
		$this->ProductList->DataSource=$this->getProductData();
		$this->ProductList->Databind();
	}
	
	protected function populateShoppingList ()
	{
		$this->ShoppingList->DataSource=$this->getShoppingListData();
		$this->ShoppingList->Databind();
	
	}
	
	
	public function getShoppingListData ()
	{
		return $this->getViewState('ShoppingList', array ());
	}
	
	public function setShoppingListData ($value)
	{
		$this->setViewState('ShoppingList', TPropertyValue::ensureArray($value), array ());
	}
	
	public function addItemToCart ($sender, $param)
	{
		$control=$param->getDroppedControl();
		// Get the Key from the repeater item
		$item=$control->getNamingContainer();
		$key=$this->ProductList->getDataKeys()->itemAt($item->getItemIndex());
		$product=$this->getProduct($key);
		$shoppingList=$this->getShoppingListData();
		if (isset ($shoppingList[$key]))
		{
			// Already an item of this type, increment counter
			$shoppingList[$key]['ProductCount']++;
		}
		else
		{
			// Add an item to the shopping list
			$shoppingList[$key]=$product;
			$shoppingList[$key]['ProductCount']=1;
		}
		$this->setShoppingListData($shoppingList);
	
	}
	
	public function removeItemFromCart ($sender, $param)
	{
		$control=$param->getDroppedControl();
		$item=$control->getNamingContainer();
		$key=$this->ShoppingList->getDataKeys()->itemAt($item->getItemIndex());
		$shoppingList=$this->getShoppingListData();
		if (isset($shoppingList[$key]))
		{
			if ($shoppingList[$key]['ProductCount'] > 1)
				$shoppingList[$key]['ProductCount'] --;
			else
				unset($shoppingList[$key]);
		}
		$this->setShoppingListData($shoppingList);
	
	}
	
	public function redrawCart ($sender, $param)
	{
		$this->populateShoppingList();
		$this->cart->render($param->NewWriter);
	
	}
	
	public function btSalvar ($sender, $param) {
        // Cria a engenharia
		$eng = new EngenhariaProdutosRecord();		
		$eng->descricao = $this->descricao->text;
		$eng->save();
		$this->descricao->settext($eng->descricao);
		$this->codigo->text = $eng->codigo;
		// Cria o item
		$itens = $this->getShoppingListData();
		//print_r($itens);
		foreach ($itens as $reg) {
			$engComp = new EngenhariasComponentesRecord();
			$engComp->engenharias_produtos_codigo = $eng->codigo;
			$engComp->quantidade = $reg['ProductCount']; 
			$engComp->produtos_codigo = $reg['ProductId'];
			$engComp->save();
		}		
		
		// Cria o produto acabado.
		$produto = new ProdutosRecord();
		$produto->descricao = $this->descricao->text;
		$tp = TiposProdutosRecord::finder()->findbyinsumo(0);
		
		$produto->tipos_produtos_codigo = $tp->codigo;
		$produto->engenharias_produtos_codigo = $eng->codigo;
		$produto->valor_compra = $this->valor->text;
		$produto->valor_venda = $this->valor->text;		
		$produto->categorias_codigo = $this->categoria->DATA;
		$produto->save();
		//
		
		$this->eng_tab->render($param->NewWriter);
		$this->msg->text = '<div class="alert alert-success" role="alert">	Engenharia Criada com sucesso.</div>';
		
	}
}

 ?>

