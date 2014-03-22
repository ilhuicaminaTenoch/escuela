<?php 
class ListaController extends Application_Model_Filter
{
	
	
	public function indexAction()
    {
		$modelo = new Application_Model_DbTable_Profesores();		
		$IdDatosPersonales = Zend_Auth::getInstance()->getIdentity()->N_DATOS_PERSONALES;
		$modeloArbol = new Application_Model_DbTable_Arbol();
		$modeloLista = new Application_Model_DbTable_Lista();		
		$padres = $modeloArbol->carga_arbol_grupo($IdDatosPersonales);
		
		$contenedor =array();
		foreach($padres as $padre){			
			$contenedor[$padre['id_grupo'].'_'.$padre['grupo']] = $modeloLista->carga_materias_grupo($IdDatosPersonales,$padre['id_grupo']);
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
		$id_profesor =addslashes($this->entityFilter->filter($this->sql_command($_GET['id_profesor'])));
		$session= new Zend_Session_Namespace('profesores');		
		$session->idGrupo = $id_grupo;
		$session->idMateria = $id_materia;
		$session->idProfesor = $id_profesor;
		
		
		$modelo = new Application_Model_DbTable_Lista();
		$titulo_tabla = $modelo->filas_conceptos($session->idGrupo,$session->idMateria,$session->idProfesor);
		$this->view->titulos = $titulo_tabla;
		
		$numeroDeTitulos = $modelo->numero_de_titulos($session->idGrupo,$session->idMateria,$session->idProfesor);
		$this->view->numeroTitulos = $numeroDeTitulos;
		
		$totalNotas = $modelo->cien_porciento($session->idMateria,$session->idGrupo, $session->idProfesor);
		$this->view->cienPorciento = $totalNotas;
	}
	
	public function gridpromedioAction(){
		$this->_helper->layout->disableLayout();
		$this->getHelper("viewRenderer")->setNoRender();
		$session= new Zend_Session_Namespace('profesores');		
		$modelo = new Application_Model_DbTable_Lista();		
		$numeroTitulos = $modelo->numero_de_titulos($session->idGrupo,$session->idMateria,$session->idProfesor);		
		$totalNotas = $modelo->totalnotas($session->idMateria,$session->idGrupo, $session->idProfesor);
		if($totalNotas > 0){
			$idAlumos = $modelo->data_grid_notas($session->idMateria,$session->idGrupo, $session->idProfesor);
			echo html_entity_decode($this->armaJson($idAlumos, $numeroTitulos ,$session->idMateria,$session->idGrupo, $session->idProfesor));
			
		}else{
			$idAlumos = $modelo->alumnosPorGrupo($session->idGrupo);			
			foreach($idAlumos as $idAlmno){				
				$modelo->insertaDefault($session->idMateria,$session->idGrupo, $session->idProfesor,$idAlmno['N_DATOS_PERSONALES']);
			}
			$idAlumos2 = $modelo->data_grid_notas($session->idMateria,$session->idGrupo, $session->idProfesor);
			echo html_entity_decode($this->armaJson($idAlumos2, $numeroTitulos ,$session->idMateria,$session->idGrupo, $session->idProfesor));
			
		}
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
			$guarda = $modelo->guarda_datos($arreglo, $session->idGrupo, $session->idMateria,$session->idProfesor);
		}
	}
	
	public function combomesAction(){
		$this->_helper->layout->disableLayout();
		$this->getHelper("viewRenderer")->setNoRender();
		$modelo = new Application_Model_DbTable_Lista();
		$combo = Zend_Json::encode($modelo->mes());
		echo $combo = html_entity_decode($combo);
	}
	
	public function armaJson($idAlumos,$numeroTitulos, $id_materia, $id_grupo, $id_profesor){		
		$modelo = new Application_Model_DbTable_Lista();      
		$json = '[';		
		$contadorAlumnos =1;
		$totalAlumnos = count($idAlumos);
		foreach($idAlumos as $valorAlumnos){
			$notas = $modelo->agrupa($valorAlumnos['id_alumno'],$id_materia, $id_grupo, $id_profesor);	
			$toralNotas = count($notas);
			$json .= '{';
			$json.= '"id_alumno":'.$valorAlumnos['id_alumno'].',';
			$json.=	'"nombre_alumno":'.'"'.$valorAlumnos['nombre_alumno'].'",';			
            if( $toralNotas > 1){               
				$json.=	'"matricula":'.'"'.$valorAlumnos['matricula'].'",';            
                $contadorNotas = 2;
                foreach($notas as $valorNota){                    
                    if($contadorNotas < $toralNotas+1){
                        $json.='"id_mes":'.$valorNota['id_mes'].',';
                        $json.='"mes_nota":"'.$valorNota['mes_nota'].'",';
                        $json.='"nota'.$contadorNotas.'-'.$valorNota['id_forma_calificar'].'-'.$valorNota['porcentaje'] * 0.01 .'":'.floatval($valorNota['notas']).',';    
                    }else{
                        $json.='"nota'.$contadorNotas.'-'.$valorNota['id_forma_calificar'].'-'.$valorNota['porcentaje'] * 0.01 .'":'.floatval($valorNota['notas']);
                    }
                    
                    $contadorNotas++;
                }
			}else{
				$json.='"id_mes" : 1,';
				$json.='"mes_nota":"Enero",';
				$json.=	'"matricula":'.'"'.$valorAlumnos['matricula'].'"';	
			}
			if($contadorAlumnos < $totalAlumnos ){
				$json.='},';
			}else{
				$json.='}';
			}
			$contadorAlumnos++;		
		}		
		$json.=']';
		return $json;
		
	}	
	public function formadecalificarAction(){
		$this->_helper->layout->disableLayout();
		$this->getHelper("viewRenderer")->setNoRender();
		$modelo = new Application_Model_DbTable_Lista();	
		$session= new Zend_Session_Namespace('profesores');	
		$grid_forma = Zend_Json::encode($modelo->carga_grid_forma_calificar($session->idMateria,$session->idGrupo, $session->idProfesor));
		echo $grid_forma = html_entity_decode($grid_forma);		
	}
	
	public function saveformadecalificarAction(){
		$this->_helper->layout->disableLayout();
		$this->getHelper("viewRenderer")->setNoRender();
		$concepto = $this->_request->getParam('S_CONCEPTO');
		$porcentaje = $this->_request->getParam('N_PORCENTAJE');
		$modelo = new Application_Model_DbTable_Lista();
		$session= new Zend_Session_Namespace('profesores');		
		echo $inserta = $modelo->valida_porcentaje($session->idMateria,$session->idGrupo, $session->idProfesor,$concepto,$porcentaje,1,1);
	}
	
	public function updateformadecalificarAction(){
		$this->_helper->layout->disableLayout();
		$this->getHelper("viewRenderer")->setNoRender();
		$id = $this->_request->getParam('N_ID_FORMA_CALIFICAR');
		$concepto = $this->_request->getParam('S_CONCEPTO');
		$porcentaje = $this->_request->getParam('N_PORCENTAJE');
		$modelo = new Application_Model_DbTable_Lista();
		$update = $modelo->updtae_formadecalificar($id,$concepto,$porcentaje);
	}
	
    public function deleteformadecalificarAction(){
		$this->_helper->layout->disableLayout();
		$this->getHelper("viewRenderer")->setNoRender();
		$id =$this->_request->getParam('N_ID_FORMA_CALIFICAR');	
		$modelo = new Application_Model_DbTable_Lista();
		$elimina = $modelo->delete_fromadecalificar($id);
		
	}
}