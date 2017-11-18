<?php
class PedidosCompras extends TPage {
	private $_data = null;
	private $_demanda = false;
	
	
	public function onInit($param) {
		parent::onLoad ( $param );
		if (! $this->getIsPostBack () && ! $this->getIsCallBack ()) {
			$this->fornecedores->datasource = Util::fnc_gera_lista ( Util::fnc_busca_fornecedores () );
			$this->fornecedores->databind ();
		}
	}
	public function cancelItem($sender, $param) {
		$this->produto_grid->EditItemIndex = - 1;
		$this->produto_grid->DataSource = $this->get_data ();
		$this->produto_grid->dataBind ();
	}
	
	public function deleteItem($sender, $param) {
	       unset($this->_data[$this->produto_grid->EditItemIndex]);
	       $this->saveData();
	       $this->produto_grid->DataSource = $this->get_data();
	       $this->produto_grid->dataBind ();
	}
	       
		   
	
	public function editItem($sender, $param) {
		$cat = CategoriasRecord::finder ()->findall ();
		$this->categoria_grid->ListDataSource = Util::fnc_gera_lista ( $cat );
		
		$this->produto_grid->EditItemIndex = $param->Item->ItemIndex;
		$this->produto_grid->DataSource = $this->get_data ();
		$this->produto_grid->dataBind ();
	}
	
	
	public function btVerificaDemanda($sender, $param) {
		$_demanda = TRUE;
		$this->popula_produtos ();
		$this->tab_dados->render ( $param->getNewWriter () );
	}
	
	public function btVerificaEstoqueMinimo($sender, $param) {
		$this->popula_produtos ();
		$this->tab_dados->render ( $param->getNewWriter () );
	}
	
	public function get_data() {
		if ($this->_data === null)
			$this->loadData ();
		return $this->_data;
	}
	
	protected function loadData() {
		$cr = new TActiveRecordCriteria ();
		if ($this->_demanda)
		   $cr->condition = "data_exclusao is null and exists (select 1 from fornecedores_produtos r where r.pessoas_codigo = " . $this->fornecedores->data . " and r.produtos_codigo = codigo)";
		if (!$this->_demanda)
			$cr->condition = "quantidade_minima > 0 and data_exclusao is null and exists (select 1 from fornecedores_produtos r where r.pessoas_codigo = " . $this->fornecedores->data . " and r.produtos_codigo = codigo)";
		
		$prd = ProdutosRecord::finder ()->findall ( $cr );
		$this->_data = $prd;
		return $this->_data;
	}
	
	public function popula_produtos() {
		$this->produto_grid->datasource = $this->get_data ();
		$this->produto_grid->databind ();
	}
	protected function saveData() {
		$this->setViewState ( 'Data', $this->_data );
	}
	public function saveItem($sender, $param) {
		$item = $param->Item;
		
		$this->produto_grid->EditItemIndex = - 1;
		$this->produto_grid->DataSource = $this->get_data ();
		$this->produto_grid->dataBind ();
	}
}

?>

