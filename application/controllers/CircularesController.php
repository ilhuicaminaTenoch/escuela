<?php
/**
 * CircularesController
 * 
 * @author Jose Manuel Moreno Plaza
 * @version 1.0
 */
require_once 'Zend/Controller/Action.php';
class CircularesController extends Application_Model_Filter
{
    /**
     * The default action - show the home page
     */
    public function indexAction ()
    {
        // TODO Auto-generated CircularesController::indexAction() default action
        $IdDatosPersonales = Zend_Auth::getInstance()->getIdentity()->N_DATOS_PERSONALES;
        $sessionPerfiles = new Zend_Session_Namespace('perfiles');
        $id_perfil = $sessionPerfiles->perfil;        
        $modelo = new Application_Model_DbTable_APG();
        $modelo2 = new Application_Model_DbTable_Circulares();
        $comboGrupo = $modelo->carga_combo($IdDatosPersonales, '');
        $this->view->grupos = $comboGrupo;
        $todos = $modelo2->todos($IdDatosPersonales);
        $this->view->todos = $todos;
        if ($id_perfil == 2 || $id_perfil == '2') {
            $materias = $modelo->carga_materias($IdDatosPersonales);
            
        }elseif ($id_perfil == 4 || $id_perfil == '4'){
            $materias = $modelo2->perfiles();
        }
        $this->view->materias = $materias;
        
        $avisos = $modelo2->aviso();
        $this->view->avisos = $avisos;
        
        $array_avisos = $modelo2->aviso();
        $filter_tipo = $modelo2->filter_tipo($array_avisos);        
        $this->view->filter_tipo = $filter_tipo;
        
        $array_grupos = $modelo2->consulta_grupos();
        $filter_grupos = $modelo2->filter_tipo($array_grupos);
        $this->view->filter_grupo = $filter_grupos;        
       
       
    }
    
    public function realoadAction()
    {
        $sessionPerfiles = new Zend_Session_Namespace('perfiles');
        $id_perfil = $sessionPerfiles->perfil;
        $this->_helper->layout->disableLayout();
        $this->getHelper("viewRenderer")->setNoRender();
        $_REQUEST = $this->filter->process($_REQUEST);
        $idGrupo = isset($_REQUEST['idGrupo']) ? addslashes($this->entityFilter->filter($this->sql_command($_REQUEST['idGrupo']))):'';
        $page = isset($_REQUEST['page']) ? addslashes($this->entityFilter->filter($this->sql_command($_REQUEST['page']))) : 1;
        $rows = isset($_REQUEST['rows']) ? addslashes($this->entityFilter->filter($this->sql_command($_REQUEST['rows']))) : 10;
        $offset = ($page-1)*$rows;        
        $modelo = new Application_Model_DbTable_Jerarquias();
        $modelo2 = new Application_Model_DbTable_Circulares();        
        if ($id_perfil == 2 || $id_perfil == '2') {//profesor
            if(strlen($idGrupo) == 0){
                $alumnos = "[]";         
            }else if (strlen($idGrupo) == 1) {            	
            	$alumnos = Zend_Json::encode($modelo->lista_elementos($idGrupo));
            }else{
            	$IdDatosPersonales = Zend_Auth::getInstance()->getIdentity()->N_DATOS_PERSONALES;            	
            	$id_profesor = $modelo2->id_profesor($IdDatosPersonales);
            	$alumnos = Zend_Json::encode($modelo2->todo_los_alumnos($idGrupo, $id_profesor,$offset, $rows));
            }
            echo $alumnos = html_entity_decode($alumnos);
        }else if($id_perfil == 4 || $id_perfil == '4'){  //admon          
           $arreglo = $modelo2->contenido_perfil($idGrupo, $offset, $rows, '');          
           //echo"<pre>"; print_r($arreglo); echo"</pre>";
            $alumnos = Zend_Json::encode($arreglo);
            echo $alumnos = html_entity_decode($alumnos);
        }
        
    }
    
    public function guardaAction()
    {
        $this->_helper->layout->disableLayout();
        $this->getHelper("viewRenderer")->setNoRender();
        $id_datos_generales = Zend_Auth::getInstance()->getIdentity()->N_DATOS_PERSONALES;
        $tipo = $_POST['tipo'];
        $idMateria = $_POST['idMateria'];
        $idGrupo = isset($_POST['idGrupo']) ? $_POST['idGrupo'] : '0';
        $alumnos = $_POST['alumnos'];
        $asunto = $_POST['asunto'];        
        $nota = addslashes($_POST['nota']);        
        $modelo = new Application_Model_DbTable_Circulares(); 
        $manda_mesaje = $modelo->manda_mensaje($alumnos, 'zerocool.moreno@gmail.com', 'Jose Manuel Moreno Plaza', $asunto, $nota,'$zerocoolmane371986$');       
        $guarda = $modelo->guarda_circulares($tipo, $idMateria, $idGrupo, $alumnos, $id_datos_generales, $nota);
    }
    
    public function cargagridavisosAction()
    {
        $this->_helper->layout->disableLayout();
        $this->getHelper("viewRenderer")->setNoRender();        
        $sessionPerfiles = new Zend_Session_Namespace('perfiles');
        $id_perfil = $sessionPerfiles->perfil;
        $id_datos_generales = Zend_Auth::getInstance()->getIdentity()->N_DATOS_PERSONALES;
        $modelo = new Application_Model_DbTable_Circulares();
        $arreglo = $modelo->carga_avisos_enviados($id_perfil, $id_datos_generales);                      
        $consulta = Zend_Json::encode($arreglo);
        echo $consulta = html_entity_decode($consulta);
        $this->view->data = $consulta;                
    }
    public function cargagridavisosprofesorAction(){
        $this->_helper->layout->disableLayout();
        $this->getHelper("viewRenderer")->setNoRender();
        $sessionPerfiles = new Zend_Session_Namespace('perfiles');
        $id_perfil = $sessionPerfiles->perfil;
        $id_datos_generales = Zend_Auth::getInstance()->getIdentity()->N_DATOS_PERSONALES;
        $modelo = new Application_Model_DbTable_Circulares();
        $arreglo = $modelo->carga_avisos_enviados_profesores($id_datos_generales);
        $consulta = Zend_Json::encode($arreglo);
        echo $consulta = html_entity_decode($consulta);
    }
    public function carga_total_filas_avisos(){        
        $sessionPerfiles = new Zend_Session_Namespace('perfiles');
        $id_perfil = $sessionPerfiles->perfil;
        $id_datos_generales = Zend_Auth::getInstance()->getIdentity()->N_DATOS_PERSONALES;
        $modelo = new Application_Model_DbTable_Circulares();
        $arreglo = $modelo->carga_avisos_enviados($id_perfil, $id_datos_generales);
        //echo"<pre>"; print_r($arreglo); echo"</pre>";       
        $this->view->total = $arreglo['total'];
    }
}
