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
				$this->_helper->redirector('home', 'index');
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

