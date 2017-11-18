<?php

class Usuarios extends TPage
{
	public function onLoad($param) {
		parent::onLoad ( $param );
		if (! $this->IsPostBack) {
			$this->popula_grid();
			$this->popula_lista_dinamicas();
		}
	}

	public function editItem($sender,$param)
	{
		$this->data_grid->EditItemIndex=$param->Item->ItemIndex;
		$this->data_grid->DataSource=$this->get_data();
		$this->data_grid->dataBind();
	}


	public function get_data(){
		$cr = new TActiveRecordCriteria();
		$cr->condition = "data_exclusao is null and nome like '%".$this->busca->Text."%'";
		$reg = UsuariosRecord::finder()->findall($cr);
		return $reg ;
	}


	public function popula_grid(){

		$this->data_grid->datasource = $this->get_data();
		$this->data_grid->databind();

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
		$us = new UsuariosRecord();
		$us->nome						    =		$this->nome->text;
		$us->username				        =		$this->username->text;
		$us->passwd					        =		md5($this->passwd->text);
		$us->perc_comissao					=		$this->perc_comissao->text;
		$us->save();
		
		$rol = new UsuariosRolesRecord();
		$rol->roles_codigo = $this->roles_usuarios->data;
		$rol->usuarios_codigo = $us->codigo;
		$rol->save();
		$this->popula_grid();
		//
		//$this->Page->ClientScript->registerEndScript( $this->ClientID, 'location.reload()');
		$this->limpaCampos();
	}

function limpaCampos() {
	$this->roles_usuarios->data=-1;
	$this->nome->settext(null);
	$this->username->data=-1;
	$this->passwd->settext(null);
	$this->perc_comissao->settext(null);
	
}

	public function popula_lista_dinamicas(){
		$rol = RolesRecord::finder()->findall();
		//
		$this->roles_usuarios->DataSource = $this->fnc_gera_lista($rol);
		$this->roles_usuarios->databind();

	}


	public function saveItem($sender,$param)
	{
		$item=$param->Item;
		$prd = UsuariosRecord::finder()->findby_codigo($this->data_grid->DataKeys[$item->ItemIndex]);
		$prd->nome = $item->nome_grid->TextBox->Text;
		$prd->username= $item->username_grid->TextBox->Text;
		$prd->perc_comissao = $item->comissao_grid->TextBox->Text;
		$prd->save();

		$this->data_grid->EditItemIndex=-1;
		$this->data_grid->DataSource=$this->get_data();
		$this->data_grid->dataBind();
	}

	public function cancelItem($sender,$param)
	{
		$this->data_grid->EditItemIndex=-1;
		$this->data_grid->DataSource=$this->get_data();
		$this->data_grid->dataBind();
	}

	 public function deleteItem($sender,$param)
	    {
	    	 
	        $prd = UsuariosRecord::finder()->findBy_Codigo($this->data_grid->DataKeys[$param->Item->ItemIndex]);
	        $prd->data_exclusao = date("Ymd");
	        $prd->save();
	    	$this->data_grid->EditItemIndex=-1;
	        $this->data_grid->DataSource=$this->get_data();
	        $this->data_grid->dataBind();
	    }
}

?>

