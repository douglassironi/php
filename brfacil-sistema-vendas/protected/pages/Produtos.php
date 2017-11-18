<?php

class Produtos extends TPage
{
	public function onLoad($param) {
		parent::onLoad ( $param );
		if (! $this->IsPostBack) {
			$this->popula_produtos();
			$this->popula_lista_dinamicas();
			$this->popula_dados();
		}
	}

	public function popula_dados(){
			if (isset($_GET['Produto'])){
				$pr = ProdutosRecord::finder()->findby_codigo($_GET['Produto']);
				$this->engenharias_produtos_codigo->data=$pr->engenharias_produtos_codigo;
				$this->tipos_produtos_codigo->data			= $pr->tipos_produtos_codigo;
				$this->descricao->settext($pr->descricao);
				$this->categorias_codigo->data=$pr->categorias_codigo;
				$this->valor_compra->settext($pr->valor_compra);
				$this->valor_venda->settext($pr->valor_venda);
				$this->quantidade_minima->settext($pr->quantidade_minima);
				$this->quantidade_estoque->settext($pr->quantidade_estoque);
				$this->cozinhas_codigo->data = $pr->cozinhas_codigo;
				$this->js->settext("<script> $('add').click(); </script>");
			}
	}
	
	public function editItem($sender,$param)
	{
		$cat = CategoriasRecord::finder()->findall();
		$this->categoria_grid->ListDataSource  = $this->fnc_gera_lista($cat);
		
		$this->produto_grid->EditItemIndex=$param->Item->ItemIndex;
		$this->produto_grid->DataSource=$this->get_data();
		$this->produto_grid->dataBind();
	}

	
	public function fileUploaded($sender,$param)
    {NULL;
    }

	public function get_data(){
		$cr = new TActiveRecordCriteria();
		$cr->condition = "data_exclusao is null and descricao like '%".$this->busca->Text."%'";
		$prd = ProdutosRecord::finder()->findall($cr);
		return $prd ;
	}


	public function popula_produtos(){

		$this->produto_grid->datasource = $this->get_data();
		$this->produto_grid->databind();

	}

	public function fnc_gera_lista($reg){
		$lst[-1] = "Escolha uma Opção";
		foreach ($reg as $it){
			$lst[$it->codigo]=$it->descricao;
		}
		return $lst;
	}

	public function bt_salvar($sender,$param) {
		//		
		if (isset($_GET['Produto']))
		{
		   $pr = ProdutosRecord::finder()->findby_codigo($_GET['Produto']);
		}
		else {	
		$pr = new ProdutosRecord();
		}
		$pr->tipos_produtos_codigo			= $this->tipos_produtos_codigo->data;		
		if (!($this->engenharias_produtos_codigo->data ==-1))
		{
		   $pr->engenharias_produtos_codigo	= $this->engenharias_produtos_codigo->data;
		}  
		$pr->descricao						=		$this->descricao->text;
		$pr->categorias_codigo				=		$this->categorias_codigo->data;
		$pr->valor_compra					=		$this->valor_compra->text;
		$pr->valor_venda					=		$this->valor_venda->text;
		$pr->quantidade_minima				=		$this->quantidade_minima->text;
		$pr->quantidade_estoque				=		$this->quantidade_estoque->text;

		if($this->imagem->HasFile){
				$pr->imagem                         =       "themes/produtos/".$this->imagem->FileName;
				$this->imagem->saveAs("themes/produtos/".$this->imagem->FileName);
		
		}
		
		if (!($this->cozinhas_codigo->data ==-1))
		{
		      $pr->cozinhas_codigo				=		$this->cozinhas_codigo->data;
		}  
		$pr->save();
		
		//
		//$this->Page->ClientScript->registerEndScript( $this->ClientID, 'location.reload()');
		$this->limpaCampos();
		$this->Response->redirect($this->Service->constructUrl("Produtos"));
	}

function limpaCampos() {
	$this->engenharias_produtos_codigo->data=-1;
	$this->tipos_produtos_codigo->data			= -1;
	$this->descricao->settext(null);
	$this->categorias_codigo->data=-1;
	$this->valor_compra->settext(null);
	$this->valor_venda->settext(null);
	$this->quantidade_minima->settext(null);
	$this->quantidade_estoque->settext(null);
	$this->cozinhas_codigo->data = -1;
	
}

	public function popula_lista_dinamicas(){
		$cat = CategoriasRecord::finder()->findall();
		$coz = CozinhasRecord::finder()->findall();
		$eng = EngenhariaProdutosRecord::finder()->findall();
		$tip = TiposProdutosRecord::finder()->findall();
		//
		
		$this->cozinhas_codigo->DataSource = $this->fnc_gera_lista($coz);
		$this->engenharias_produtos_codigo->DataSource = $this->fnc_gera_lista($eng);
		$this->categorias_codigo->DataSource = $this->fnc_gera_lista($cat);
		$this->tipos_produtos_codigo->DataSource = $this->fnc_gera_lista($tip);

		
		$this->cozinhas_codigo->databind();
		$this->engenharias_produtos_codigo->databind();
		$this->categorias_codigo->databind();
		$this->tipos_produtos_codigo->databind();

	}

	

	public function saveItem($sender,$param)
	{
		$item=$param->Item;
		$prd = ProdutosRecord::finder()->findby_codigo($this->produto_grid->DataKeys[$item->ItemIndex]);
		$prd->descricao = $item->descricao_grid->TextBox->Text;
		$prd->valor_venda = $item->valor_grid->TextBox->Text;
		$prd->quantidade_estoque = $item->quantidade_grid->TextBox->Text;
		$prd->save();

		$this->produto_grid->EditItemIndex=-1;
		$this->produto_grid->DataSource=$this->get_data();
		$this->produto_grid->dataBind();
	}

	public function cancelItem($sender,$param)
	{
		$this->produto_grid->EditItemIndex=-1;
		$this->produto_grid->DataSource=$this->get_data();
		$this->produto_grid->dataBind();
	}

	 public function deleteItem($sender,$param)
	    {
	    	 
	        $prd = ProdutosRecord::finder()->findBy_Codigo($this->produto_grid->DataKeys[$param->Item->ItemIndex]);
	        $prd->data_exclusao = date("Ymd");
	        $prd->save();
	    	$this->produto_grid->EditItemIndex=-1;
	        $this->produto_grid->DataSource=$this->get_data();
	        $this->produto_grid->dataBind();
	    }
}

?>

