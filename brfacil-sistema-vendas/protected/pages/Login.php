<?php

class Login extends TPage
{
	public  function onLoad($param){
			
		//$this->buscaImagens();
	
	}
    public function validateUser($sender, $param)
    {
        $authManager = $this->Application->getModule('auth');
        if(!$authManager->login($this->Username->Text, $this->Senha->Text))
            $param->IsValid = false;
    }
 
    public function loginButtonClicked($sender,$param)
    {
        if($this->Page->IsValid)
        {

        	$url = $this->Application->getModule('auth')->ReturnUrl;
            if(empty($url))
                $url = $this->Service->DefaultPageUrl;
            $this->Response->redirect($url);
        }
    }
    public function buscaImagens(){
    	$param = ParametrosRecord::finder()->findAllBydescricao('IMG_LOGIN');
    	$this->Repeater->DataSource=$param;
    	$this->Repeater->dataBind();
    }
}

 ?>