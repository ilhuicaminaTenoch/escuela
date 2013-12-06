<?php
/**
 * Descripcion de Asignacion de Profesores a Grupos
 *
 * @author manuel.moreno
 */
class APGController extends Application_Model_Filter {
	public function  indexAction(){			
		$this->_helper->_layout->setLayout('layout_APG');
		$modelo = new Application_Model_DbTable_APG();		
		$modelo2 = new Application_Model_DbTable_Arbol();		
		$_GET = $this->filter->process($_GET);
		$id_grupo = isset($_GET['grupo']) ? addslashes($this->entityFilter->filter($this->sql_command($_GET['grupo']))):'';
		
		$profesores = $modelo2->carga_profesor();
		$contenedor = array();		
		foreach($profesores as $hijos){			
			$contenedor[$hijos['nombre'].'_'.$hijos['id_profesor']] = $modelo->carga_materias($hijos['nombre']);			
		}
		$this->view->arreglo = $contenedor;		
		$modelo3 = new Application_Model_DbTable_Jerarquias();
		$grupos = $modelo3->lista_maestros(2);
		$this->view->grupos = $grupos;
		if($id_grupo == ''){								
			$horas = $modelo->carga_horas();
			$this->view->datos = $horas;
			$this->view->validacion = 1;
		}else{			
			$datos = $modelo->carga_datos($id_grupo);
			$this->view->datos = $datos;			
			$horas = $modelo->carga_horas();
			$this->view->horas = $horas;
			$this->view->validacion = 2;
			$this->view->grupo=$id_grupo;
			
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
	
	
}
