<?php 
class ListaController extends Application_Model_Filter
{
	
	public function indexAction()
    {
		$modelo = new Application_Model_DbTable_Profesores();		
		$IdDatosPersonales = Zend_Auth::getInstance()->getIdentity()->N_DATOS_PERSONALES;
		$modeloArbol = new Application_Model_DbTable_Arbol();
		$modeloLista = new Application_Model_DbTable_Lista();
		$IdDatosPersonales = Zend_Auth::getInstance()->getIdentity()->N_DATOS_PERSONALES;		
		$padres = $modeloArbol->carga_arbol_grupos($IdDatosPersonales);
		$contenedor =array();
		foreach($padres as $padre){			
			$contenedor[$padre['id_grupo'].'_'.$padre['grupo']] = $modeloLista->carga_materias_grupo($IdDatosPersonales);
		}
		$this->view->datos = $contenedor;
	}	
	
	
	
	public function tabsAction()
	{
		$_GET = $this->filter->process($_GET);
		$id_grupo = isset($_GET['id_grupo']) ? addslashes($this->entityFilter->filter($this->sql_command(intval($_GET['id_grupo'])))) : '';
				
		$_GET = $this->filter->process($_GET);
		$modelo= new Application_Model_DbTable_Lista();
		$id_grupo = isset($_GET['id_grupo']) ? addslashes($this->entityFilter->filter($this->sql_command(intval($_GET['id_grupo'])))) : '1';
		$page = isset($_GET['page']) ? addslashes($this->entityFilter->filter($this->sql_command(intval($_GET['page'])))) : 1;
		$rows = isset($_GET['rows']) ? addslashes($this->entityFilter->filter($this->sql_command(intval($_GET['rows'])))) : 10;
		$offset = ($page - 1) * $rows;						
		$alumnos = Zend_Json::encode($modelo->carga_alumnos($id_grupo,$offset,$rows));			
		$alumnos = html_entity_decode($alumnos);
		$genreaJson=$modelo->genera_archivo_json($alumnos);	
		
	}
	
	public function datosjsonAction(){		
		
	}
	
}