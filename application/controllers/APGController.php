<?php
/**
 * Descripcion de Asignacion de Profesores a Grupos
 *
 * @author manuel.moreno
 */
class APGController extends Application_Model_Filter {
	public function  indexAction(){
	    $IdProfesor = Zend_Auth::getInstance()->getIdentity()->N_DATOS_PERSONALES;
		$this->_helper->_layout->setLayout('layout_APG');
		$modelo = new Application_Model_DbTable_APG();		
		$modelo2 = new Application_Model_DbTable_Arbol();
		$modelo3 = new Application_Model_DbTable_AGP();		
		$_GET = $this->filter->process($_GET);
		$idCombo = isset($_GET['idCombo']) ? addslashes($this->entityFilter->filter($this->sql_command($_GET['idCombo']))) : '';
		$idProfesor = isset($_GET['idProfesor']) ? addslashes($this->entityFilter->filter($this->sql_command($_GET['idProfesor']))) : '';
		$sessionPerfiles = new Zend_Session_Namespace('perfiles');
		$perfil = $sessionPerfiles->perfil;		
	    if($perfil == 3 && $idProfesor !=''){	        
	        $profesores = $modelo2->carga_profesores();
	        $cargaCombo =  $modelo->carga_combo('', '');
	        
	    }elseif ($perfil == 3) {
		  	$profesores = $modelo2->carga_profesores();
		  	$cargaCombo = $modelo2->carga_profesores();		  		  	
		}else{
		    $profesores = $modelo2->carga_profesor($IdProfesor);
		    $cargaCombo = $modelo->carga_combo($IdProfesor, '');
		   
		}
		$contenedor = array();
		foreach($profesores as $hijos){
			$contenedor[$hijos['nombre'].'_'.$hijos['id_profesor']] = $modelo->carga_materias($hijos['id_profesor']);
		}
		$this->view->arreglo = $contenedor;
		
		
		$this->view->listaCombo = $cargaCombo;
		if ($idCombo == '') {
		    $horas = $modelo->carga_horas();
		    $this->view->datos = $horas;
		    $this->view->validacion = 1;
		}else{       		    
    	    $grupoSeleccionado = $modelo->carga_combo($idCombo, '');
    		$datos = $modelo->carga_datos($idCombo);
    		$this->view->datos = $datos;
    		$this->view->sgrupo = $grupoSeleccionado;
    		
    		//die();
    		$horas = $modelo->carga_horas();
    		
    		$this->view->horas = $horas;
    		$this->view->validacion = 2;
    		$this->view->grupo=$idCombo;
		}
		
	}
	
	public function guardaAction(){
		$this->_helper->layout->disableLayout();
        $this->getHelper("viewRenderer")->setNoRender();
		$_POST = $this->filter->process($_POST);
		$json=$this->sql_command($_POST['cadena']);
		$id_grupo = isset($_POST['grupo']) ? addslashes($this->entityFilter->filter($this->sql_command($_POST['grupo']))):'';
		$arreglo = Zend_Json::decode($json);
		$modelo = new Application_Model_DbTable_APG();
		$guarda = $modelo->guarda_apg($arreglo,$id_grupo);
		//$this->view->datos = $guarda;		
	}
	
	public function checaAction(){
		if ($this->getRequest()->isXmlHttpRequest()){
			$this->_helper->layout->disableLayout();
            $this->getHelper("viewRenderer")->setNoRender();
			$_POST = $this->filter->process($_POST);
			$id_profesor = isset($_POST['id_profesor']) ? addslashes($this->entityFilter->filter($this->sql_command(intval($_POST['id_profesor'])))) : '';
			$id_grupo = isset($_POST['id_grupo']) ? addslashes($this->entityFilter->filter($this->sql_command(intval($_POST['id_grupo'])))) : '';
			$id_hora = isset($_POST['id_hora']) ? addslashes($this->entityFilter->filter($this->sql_command(intval($_POST['id_hora'])))) : '';
			$dia = isset($_POST['dia']) ? addslashes($this->entityFilter->filter($this->sql_command(intval($_POST['dia'])))) : '';
			$modelo = new Application_Model_DbTable_APG();
			$respuesta = $modelo->checa_disponibilidad($id_profesor, $id_hora, $dia);
			echo $json = Zend_Json::encode($respuesta);
			
		}
	}
	
	public function cargacomboAction(){
	    if ($this->getRequest()->isXmlHttpRequest()){
	        $this->_helper->layout->disableLayout();
	       // $this->getHelper("viewRenderer")->setNoRender();
	        $_POST = $this->filter->process($_POST);
	        $idCombo = isset($_POST['idCombo']) ? addslashes($this->entityFilter->filter($this->sql_command($_POST['idCombo']))) : '';
	        $combo_a_cargar = isset($_POST['combo_a_cargar']) ? addslashes($this->entityFilter->filter($this->sql_command($_POST['combo_a_cargar']))) : '';
	        $idProfesor = isset($_POST['idProfesor']) ? addslashes($this->entityFilter->filter($this->sql_command($_POST['idProfesor']))) : '';
	        $modelo = new Application_Model_DbTable_APG();
	        $combo = $modelo->carga_combo($idCombo, $combo_a_cargar);
	        $this->view->listaCombo = $combo;
	        
	    }
	    
	}	
}
