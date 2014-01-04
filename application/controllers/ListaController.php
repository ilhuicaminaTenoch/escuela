<?php 
class ListaController extends Application_Model_Filter
{
	
	
	public function indexAction()
    {
		$modelo = new Application_Model_DbTable_Profesores();		
		$IdDatosPersonales = Zend_Auth::getInstance()->getIdentity()->N_DATOS_PERSONALES;
		$modeloArbol = new Application_Model_DbTable_Arbol();
		$modeloLista = new Application_Model_DbTable_Lista();		
		$padres = $modeloArbol->carga_arbol_grupos($IdDatosPersonales);
		$contenedor =array();
		foreach($padres as $padre){			
			$contenedor[$padre['id_grupo'].'_'.$padre['grupo']] = $modeloLista->carga_materias_grupo($IdDatosPersonales);
		}
		$this->view->datos = $contenedor;		
	}	
	
	
	
	public function listacontrolAction()
	{
		$this->_helper->layout->disableLayout();
		$this->getHelper("viewRenderer")->setNoRender();						
		$modelo= new Application_Model_DbTable_Lista();	
		$session= new Zend_Session_Namespace('profesores');
		$page = isset($_GET['page']) ? addslashes($this->entityFilter->filter($this->sql_command(intval($_GET['page'])))) : 1;
		$rows = isset($_GET['rows']) ? addslashes($this->entityFilter->filter($this->sql_command(intval($_GET['rows'])))) : 10;
		$offset = ($page - 1) * $rows;						
		$alumnos = Zend_Json::encode($modelo->carga_alumnos($session->idGrupo,$offset,$rows));			
		echo $alumnos = html_entity_decode($alumnos);		
		
	}
	
	public function datosjsonAction(){		
		
	}
	
	public function propertygridAction(){		
		$this->_helper->layout->disableLayout();
		$this->getHelper("viewRenderer")->setNoRender();
		$modelo= new Application_Model_DbTable_Lista();
		$session= new Zend_Session_Namespace('profesores');		
		$IdDatosPersonales = Zend_Auth::getInstance()->getIdentity()->N_DATOS_PERSONALES;
		$grid_propiedad = Zend_Json::encode($modelo->forma_json_property_grid(1,$IdDatosPersonales,$session->idMateria));
		echo $grid_propiedad = html_entity_decode($grid_propiedad);
		
	}
	
	public function tabsAction(){
		$id_grupo =addslashes($this->entityFilter->filter($this->sql_command($_GET['id_grupo'])));
		$id_materia =addslashes($this->entityFilter->filter($this->sql_command($_GET['id_materia'])));
		$session= new Zend_Session_Namespace('profesores');		
		$session->idGrupo = $id_grupo;
		$session->idMateria = $id_materia;
	}
	
	public function gridpromedioAction(){
		$this->_helper->layout->disableLayout();
		$this->getHelper("viewRenderer")->setNoRender();
		$session= new Zend_Session_Namespace('profesores');		
		$modelo = new Application_Model_DbTable_Lista();		
		$grid_promedio = Zend_Json::encode($modelo->data_grid_promedio($session->idMateria,$session->idGrupo));
		echo $grid_promedio = html_entity_decode($grid_promedio);
	}
	
	public function guardaAction(){
		$this->_helper->layout->disableLayout();
		$this->getHelper("viewRenderer")->setNoRender();
		$session= new Zend_Session_Namespace('profesores');
		if ($this->getRequest()->isXmlHttpRequest()) {
			$_POST = $this->filter->process($_POST);
			$json=$this->sql_command($_POST['cadena']);
			$arreglo = Zend_Json::decode($json);
			$modelo = new Application_Model_DbTable_Lista();
			$guarda = $modelo->guarda_datos($arreglo, $session->idGrupo, $session->idMateria);
		}
	}
}