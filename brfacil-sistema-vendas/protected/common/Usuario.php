<?php

Prado::using('System.Security.TDbUserManager');
 
class Usuario extends TDbUser
{  
   public $codigo;	
   public function  getCodigo(){
   	 return $this->codigo;
   }
    public function createUser($username)
    {
        $userRecord = UsuariosRecord::finder()->findBy_username($username);
        if($userRecord instanceof UsuariosRecord) // if found
        {
            $user = new Usuario($this->Manager);
            $user->Name = $userRecord->nome;
            $user->codigo = $userRecord->codigo;

		$rolesUsuariosRecords =UsuariosRolesRecord::finder()->findallbyusuarios_codigo($userRecord->codigo);

		$roles = array();
		foreach($rolesUsuariosRecords as $rolesUsuariosRecord)
		{
			$roles[] = $rolesUsuariosRecord->roles->role;
		}
		$user->Roles = $roles;

            $user->IsGuest = false;
            return $user;
        }
        else
     	{
            return null;
	    }
    }
 
    public function validateUser($username, $password)
    { 
    	return UsuariosRecord::finder()->findBy_username_AND_passwd($username, md5($password)) !== null;
    }
 
    public function getIsAdmin()
    {
        return $this->isInRole('ADM');
    }
}
?>
