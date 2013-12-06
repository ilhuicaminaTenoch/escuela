<?php 
class JerarquiasController extends Application_Model_Filter{
    
	public function indexAction(){
		$id = isset($_POST['id']) ? addslashes($this->entityFilter->filter($this->sql_command($_POST['id']))) : 0;		
		$modelo = new Application_Model_DbTable_Jerarquias();
		$tipos = $modelo->padres();
		$this->view->tipos = $tipos;
		
    }
	
	public function guardatiposAction(){
		$this->_helper->layout->disableLayout();
        $this->getHelper("viewRenderer")->setNoRender();
		$_POST = $this->filter->process($_POST);
		$tipos = isset($_POST['Tipos']) ? addslashes($this->entityFilter->filter($this->sql_command($_POST['Tipos']))) : 'NULL';		
		$id_padre = isset($_POST['id_padre']) ? addslashes($this->entityFilter->filter($this->sql_command($_POST['id_padre']))) : '';
		$id_hijo = isset($_POST['id_hijo']) ? addslashes($this->entityFilter->filter($this->sql_command($_POST['id_hijo']))) : '';
		$id_alumnos = isset($_POST['id_alumnos']) ? addslashes($this->entityFilter->filter($this->sql_command($_POST['id_alumnos']))) : '';
		$modelo = new Application_Model_DbTable_Jerarquias();
		$inserta = $modelo->guarda_tipo($id_padre,$id_hijo,$id_alumnos);
	}
	
	public function listanivelesAction(){		
		$this->_helper->layout->disableLayout();				
		if ($this->getRequest()->isXmlHttpRequest()) {
			$_POST = $this->filter->process($_POST);
			$id_padre = isset($_POST['id_actual']) ? addslashes($this->entityFilter->filter($this->sql_command($_POST['id_actual']))) : '';
			$modelo = new Application_Model_DbTable_Jerarquias();			
			$lista_maestros = $modelo->lista_maestros($id_padre);			
			$this->view->listas = $lista_maestros;
		}
	}
	
	public function listaelementosAction(){
		$this->_helper->layout->disableLayout();
        $this->getHelper("viewRenderer")->setNoRender();		
		if ($this->getRequest()->isXmlHttpRequest()) {
			$_POST = $this->filter->process($_POST);			
			$modelo = new Application_Model_DbTable_Jerarquias();
			$_POST = $this->filter->process($_POST);
			$id_actual = isset($_POST['id_actual']) ? addslashes($this->entityFilter->filter($this->sql_command($_POST['id_actual']))) : '';
			$id_padre = isset($_POST['id_padre']) ? addslashes($this->entityFilter->filter($this->sql_command($_POST['id_padre']))) : '';
			$lista_materias = $modelo->lista_elementos($id_padre,$id_actual);			
			$this->view->listas = $lista_materias;
			$this->renderScript('Jerarquias/listaniveles.phtml');
		}
	}
	public function addnivelesActions(){
	
	}
	
	public function jsonprofesoresAction(){
		$this->_helper->layout->disableLayout();
		$this->getHelper("viewRenderer")->setNoRender();
		$_POST = $this->filter->process($_POST);
		$q = isset($_POST['q']) ? $_POST['q'] : '';		
		$modelo = new Application_Model_DbTable_Jerarquias();
		$cg = $modelo->combo_grid($q);
		echo Zend_Json::encode($cg);
	}
	
	public function jsonalumnosAction(){
		$this->_helper->layout->disableLayout();
		$this->getHelper("viewRenderer")->setNoRender();
		$_POST = $this->filter->process($_POST);		
		$q= isset($_POST['q']) ? $_POST['q'] : '';
		$modelo= new Application_Model_DbTable_Jerarquias();
		$cga = Zend_Json::encode($modelo->combo_grid_almnos($q));
		echo $cga = html_entity_decode($cga);
	}
	
	public function quitaAction(){
		$this->_helper->layout->disableLayout();
		$this->getHelper("viewRenderer")->setNoRender();
		$_POST = $this->filter->process($_POST);	
		$id_alumno= isset($_POST['id_alumno']) ? $_POST['id_alumno'] : '';		
		$modelo= new Application_Model_DbTable_Jerarquias();
		$ejecuta = $modelo->quita_alumno_grupo($id_alumno);
	}
}