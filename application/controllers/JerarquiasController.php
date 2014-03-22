<?php 
class JerarquiasController extends Application_Model_Filter{
    
	public function indexAction(){		
		$modelo = new Application_Model_DbTable_Jerarquias();			
		$lista_grupos = $modelo->lista_grupos();		
		$this->view->listas = $lista_grupos;
		
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
	public function listaelementosAction(){
		$this->_helper->layout->disableLayout();
        $this->getHelper("viewRenderer")->setNoRender();		
		if ($this->getRequest()->isXmlHttpRequest()) {			
			$_POST = $this->filter->process($_POST);			
			$modelo = new Application_Model_DbTable_Jerarquias();
			$_POST = $this->filter->process($_POST);
			$id_grupo = isset($_POST['id_grupo']) ? addslashes($this->entityFilter->filter($this->sql_command($_POST['id_grupo']))) : '';			
			$lista_materias = $modelo->lista_elementos($id_grupo);			
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
	
	public function guardagruposAction(){
		$this->_helper->layout->disableLayout();
        $this->getHelper("viewRenderer")->setNoRender();
		$_POST = $this->filter->process($_POST);		
		$sGrupo = isset($_POST['grupo']) ? addslashes($this->entityFilter->filter($this->sql_command($_POST['grupo']))) : '';
		$modelo = new Application_Model_DbTable_Jerarquias();
		echo $modelo->guarda_grupo($sGrupo,1,1);
	}
}