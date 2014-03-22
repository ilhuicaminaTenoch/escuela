<?php
/**
 *
 * @author manuel.moreno
 * Controlador Consulta Horario por Grupo       
 */
class CHPController extends Application_Model_Filter {
    public function indexAction(){
       $modelo = new Application_Model_DbTable_CHP();
       $grupos = $modelo->carga_grupos();
       $this->view->grupos = $grupos;  
    }
    public function consultaAction(){
        $this->_helper->layout->disableLayout();        
        $modelo = new Application_Model_DbTable_CHP();
        $_POST = $this->filter->process($_POST);
        $idGrupo = isset($_POST['idGrupo']) ? addslashes($this->entityFilter->filter($this->sql_command($_POST['idGrupo']))):'';
        $horas = $modelo->carga_horas();
        $this->view->horas = $horas;
         
        $datos = $modelo->consulta_materias($idGrupo);
        $this->view->datos = $datos;
        
    }
}
?>