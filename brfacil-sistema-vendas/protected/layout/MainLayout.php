<?php
class MainLayout extends TTemplateControl
{
public  function onLoad($param){
		 
			$this->logo();
			$this->montaMenuUsuario();

	}
	
	public function montaMenuUsuario(){
		$cr = new TActiveRecordCriteria();
		$in = "in(";
		foreach ( $this->User->roles as $role) {
			$in.= "'".$role."',";
		}
		$in.="'1')";
		$cr->condition = "codigo in (select menus_codigo
 from roles_menus rm  
	, usuarios_roles ur
    , roles rl
where rm.roles_codigo = ur.roles_codigo
 and rl.codigo = ur.roles_codigo
 and rl.role ".$in." )";
		$menu = MenusRecord::finder()->findAll($cr);
		$_SESSION ['atendente'] = $this->user->name;
		$this->Repeater->DataSource=$menu;
		$this->Repeater->dataBind();
	}
	
    public function logoutButtonClicked($sender, $param)
    {
        $this->Application->getModule('auth')->logout();
        $url=$this->Service->constructUrl($this->Service->DefaultPage);
        $this->Response->redirect($url);
    }
    
    public function logo() {
    	$this->logo->text = 'SFacil';
    	$pr = ParametrosRecord::finder()->findbydescricao('EMP_LOGO');
    	if ($pr){
    	$this->logo->settext('<img src="'.$pr->valor.'"  class="media-object" width="60"/> ');
    	}
    		 
    }
}
?>
