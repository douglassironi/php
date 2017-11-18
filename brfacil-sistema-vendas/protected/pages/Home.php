<?php
class Home extends TPage
{

public function OnLoad() {
     /* Chama a lista  de produtos.*/
	$this->logo();
   }  
  
   public function logo() {
   	$this->logo->text = 'SFacil';    	
   	$pr = ParametrosRecord::finder()->findbydescricao('EMP_LOGO');
   
   	if ($pr){
   		$this->logo->settext('<img src="'.$pr->valor.'"  class="media-object" width="300"/> ');
   	}
   	 
   }
    
   
}

?>

