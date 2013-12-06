<?php
/**
 * Description of ProfesorController
 *
 * @author manuel.moreno
 */
class AMPController extends Application_Model_Filter {
	public function indexAction() {
		$modelo =new Application_Model_DbTable_Arbol();		
		$resultado = $modelo->carga_materia('tb_arbol_materias');
		$this->view->materias = $resultado;
		
		$res_profe_materias = $modelo->carga_profesor_materias('materias');
		$this->view->profesores_materias = $res_profe_materias;
		
		$res_profe = $modelo->carga_profesor_materias('profesor');
		$this->view->profesores = $res_profe;		
	}
	public function arbolAction(){}
	public function arbolmateriasAction(){
		$this->_helper->layout->disableLayout();
        $this->getHelper("viewRenderer")->setNoRender();
		$_POST = $this->filter->process($_POST);
		$_GET = $this->filter->process($_GET);		
		$tabla = isset($_GET['nombre_tabla']) ? addslashes($this->entityFilter->filter($this->sql_command($_GET['nombre_tabla']))):'';
		$id = isset($_POST['id']) ? addslashes($this->entityFilter->filter($this->sql_command(intval($_POST['id'])))) : 0;
		$modelo =new Application_Model_DbTable_Arbol();		
		echo $resultado = Zend_Json::encode($modelo->cargar_arbol($id,$tabla));
		
	}
	public function dndAction(){
		$this->_helper->layout->disableLayout();
        $this->getHelper("viewRenderer")->setNoRender();
		$_POST = $this->filter->process($_POST);		
		$idPocision = addslashes($this->entityFilter->filter($this->sql_command(intval($_POST['targetId']))));
		$id = addslashes($this->entityFilter->filter($this->sql_command(intval($_POST['id']))));
		$punto = addslashes($this->entityFilter->filter($this->sql_command(intval($_POST['point']))));
		echo "el ID $id se movio de lugar a $idPocision";
	}
	public function guardaAction(){
		$this->_helper->layout->disableLayout();
        $this->getHelper("viewRenderer")->setNoRender();
		$_POST = $this->filter->process($_POST);
		$json=$this->sql_command($_POST['cadena']);
		
		$arreglo = Zend_Json::decode($json);
		$modelo = new Application_Model_DbTable_Arbol();
		$guarda = $modelo->guarda_amp($arreglo);
		//$this->genera_archivo_json($json);
	}
	
	public function genera_archivo_json($contenido){
		$nombre_archivo="AsignacionesProfesorMaterias.json";
		if (is_writable($nombre_archivo)) {
			if (!$gestor = fopen($nombre_archivo, 'w')) {
				echo "No se puede abrir el archivo ($nombre_archivo)";
				exit;
			}
			if (fwrite($gestor, $contenido) === FALSE) {
				echo "No se puede escribir en el archivo ($nombre_archivo)";
				exit;
			}
			echo "Exito, se escribió ($contenido) en el archivo ($nombre_archivo)";
			fclose($gestor);
		}else {
			echo "El archivo $nombre_archivo no es escribible";
		}
	}
}