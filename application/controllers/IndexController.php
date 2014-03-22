<?php

class IndexController extends Application_Model_Filter
{
    public function indexAction()
    {		
		if ($this->getRequest()->isPost())
        {	
			$usuario = isset($_POST['usuario']) ? addslashes($this->entityFilter->filter($this->sql_command($_POST['usuario']))) : '';
			$contrasena = isset($_POST['contrasena']) ? addslashes($this->entityFilter->filter($this->sql_command($_POST['contrasena']))) : '';

			if( $usuario == '' || $contrasena == '')
			{
				$this->view->message = "Nombre de usurio o contraseña estan vacios";
			}
			elseif ( !$this->_process( $usuario, $contrasena ) )
			{
				$this->view->message = 'Nombre de usuario o contraseña incorrecta.';			
			}
			else			    
			{
			    $modelo = new Application_Model_DbTable_Index();
			    $perfiles = $modelo->obtiene_perfil($usuario, md5($contrasena));
			    $perfil = intval($perfiles[0]['perfil']);
			    $sessionPerfiles = new Zend_Session_Namespace('perfiles');
			    $sessionPerfiles->perfil = $perfil;
			    switch ($perfil) {
			    	case 1://Alumno
			    	    
			    	break;
			    	
			    	case 2://Profesores			    	    
			    	    echo $this->render('menu-profesor');			    	    
			    	break;
			    	
			    	case 3://Pariente
			    	    echo $this->render('menu-pariente');
			    	break;
			    	
			    	case 4://Administrador
			    	    echo $this->render('menu-admon');
			    	break;
			    	
			    }
				
			}
		}
	}
	
	public function homeAction(){}
	public function descargaexcelAction(){
		$this->_helper->_layout->setLayout('layout_APG');
		$this->getHelper("viewRenderer")->setNoRender();
		$modelo =new Application_Model_Excel();
		$prueba = $modelo->descarga_excel();
	}
	
	


}

