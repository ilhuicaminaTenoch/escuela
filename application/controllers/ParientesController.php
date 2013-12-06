<?php
/**
 * Description of ParienteController
 *
 * @author manuel.moreno
 */
class ParientesController extends Application_Model_Filter {
    public function indexAction() {
		$modelo = new Application_Model_DbTable_Alumnos();
		$lista_padres = $modelo->consulta_pariente();
		$this->view->lista_padres = $lista_padres;
    }
    public function datosjsonAction() {
        if ($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->layout->disableLayout();
            $this->getHelper("viewRenderer")->setNoRender();
            $_POST = $this->filter->process($_POST);
            $this->_helper->layout->disableLayout();
            $page = isset($_POST['page']) ? addslashes($this->entityFilter->filter($this->sql_command(intval($_POST['page'])))) : 1;
            $rows = isset($_POST['rows']) ? addslashes($this->entityFilter->filter($this->sql_command(intval($_POST['rows'])))) : 10;
            $q = isset($_POST['Snombre']) ? addslashes($this->entityFilter->filter($this->sql_command($_POST['Snombre']))) : '';
            $offset = ($page - 1) * $rows;
            $modelo = new Application_Model_DbTable_Pariente();
            $cosulta = Zend_Json::encode($modelo->consulta_general($offset, $rows, $q));
			echo $cosulta = html_entity_decode($cosulta);
        }
    }
    public function exportarexcelAction(){
        $this->_helper->layout->disableLayout();
        $this->getHelper("viewRenderer")->setNoRender();
        $array_hojas = [0=>"tb_datos_generales",1=>"tb_parientes"];
        $excel = new Application_Model_Excel();
        $titulo = 'Catalogo Parientes '.date("Y-m-d H:i:s");
        $perfil = 3;
        $excel->exportar_excel($array_hojas,$titulo,$perfil);
    }   	
	
    
}

?>