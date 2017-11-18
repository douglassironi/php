<?php
class FornecedoresProdutos extends TPage {
	
	public function onLoad($param) {
		parent::onLoad ( $param );
		if (! $this->IsPostBack) {
			$this->popula_fornecedores();
		}
	}
	
	public  function popula_produtos($sender,$param) {
		$cr = new TActiveRecordCriteria();
		$cr->condition = "data_exclusao is null";
		$pr = ProdutosRecord::finder()->findall($cr);
		$this->produtos->datasource = Util::fnc_gera_lista($pr);
		$this->produtos->databind();
				
		
		$cr->condition = "pessoas_codigo = ".$this->fornecedores->data;
		$fr =  FornecedoresProdutosRecord::finder()->findall($cr);
		
		foreach ($fr as $rg)
		{
			$in[]=$rg->produtos_codigo;
		}	
		if (isset($rg))
		$this->produtos->SelectedValues = $in;
		
		 $this->pn_2->render($param->getNewWriter());
		
		
		
	}

	public  function popula_fornecedores() {
		$fo = PessoasRecord::finder()->findallby_tipo_pessoa('FOR');
		$this->fornecedores->datasource = Util::fnc_gera_lista($fo);
		$this->fornecedores->databind();		
	}
	
	protected function btSalvar($sender,$param)
	{
		FornecedoresProdutosRecord::finder()->DeleteAllBy_pessoas_codigo($this->fornecedores->data);
		
		$indices=$this->produtos->SelectedIndices;
		$result='';
		foreach($indices as $index)
		{
			try {
				$item=$this->produtos->Items[$index];
				$fo = new FornecedoresProdutosRecord();
				$fo->pessoas_codigo = $this->fornecedores->data;
				$fo->produtos_codigo = $item->Value;
				$fo->save();
				
			} catch (Exception $e) {
			  null;
			}
		}
		
	}
	
	
}

?>

