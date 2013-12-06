<?php

class AlumnosController extends Application_Model_Filter {	
    public function indexAction() {		
		$modelo = new Application_Model_DbTable_Alumnos();
		$lista_padres = $modelo->consulta_padres();
		$this->view->lista_padres = $lista_padres;		
		
    }

    public function datosjsonAction() {
        if ($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->layout->disableLayout();
            $this->getHelper("viewRenderer")->setNoRender();
            $_POST = $this->filter->process($_POST);            
            $page = isset($_POST['page']) ? addslashes($this->entityFilter->filter($this->sql_command(intval($_POST['page'])))) : 1;
            $rows = isset($_POST['rows']) ? addslashes($this->entityFilter->filter($this->sql_command(intval($_POST['rows'])))) : 10;
            $q = isset($_POST['Snombre']) ? addslashes($this->entityFilter->filter($this->sql_command($_POST['Snombre']))) : '';
            $offset = ($page - 1) * $rows;
            $modelo = new Application_Model_DbTable_Alumnos();
            $cosulta = Zend_Json::encode($modelo->consulta_general($offset, $rows, $q));
			echo $cosulta = html_entity_decode($cosulta);
        }
    }
    public function consultapaisAction() {
        $this->_helper->layout->disableLayout();
        $this->getHelper("viewRenderer")->setNoRender();
        $modelo = new Application_Model_DbTable_Alumnos();
        echo $consulta = Zend_Json::encode($modelo->consulta_pais());
    }
    public function consultagrupoAction() {
        $this->_helper->layout->disableLayout();
        $this->getHelper("viewRenderer")->setNoRender();
        $modelo = new Application_Model_DbTable_Alumnos();
        echo $consulta = Zend_Json::encode($modelo->consulta_grupo());
    }
    public function consultagradoAction() {
        $this->_helper->layout->disableLayout();
        $this->getHelper("viewRenderer")->setNoRender();
        $modelo = new Application_Model_DbTable_Alumnos();
        echo $consulta = Zend_Json::encode($modelo->consulta_grado());
    }

    public function uploadifyAction() {
        $this->getHelper("viewRenderer")->setNoRender();
        $this->_helper->layout->disableLayout();
        $folder = '../fotos';
        $verifyToken = md5('unique_salt' . $_POST['timestamp']);
        if (!empty($_FILES) && $_POST['token'] == $verifyToken) {                     
            $imagen = $_FILES['Filedata']['name'];
            $tempFile = $_FILES['Filedata']['tmp_name'];
            $targetPath = $_SERVER['DOCUMENT_ROOT'] . $folder;
            $targetFile = rtrim($targetPath, '/') . '/' .$_POST['timestamp'].'.jpg';
            $imageinfo = @getimagesize($_FILES['Filedata']['tmp_name']);
            if ($imageinfo[2] == IMAGETYPE_JPEG && $_FILES['Filedata']['size'] < '2621440') {                
                move_uploaded_file($tempFile, $targetFile);                
                echo '1';
            } else {
               echo '2';
            }
        }
    }
    
    public function uploadexcelAction(){
        $this->getHelper("viewRenderer")->setNoRender();
        $this->_helper->layout->disableLayout();
        $folder = '/excel';
        $verifyToken = md5('unique_salt' . $_POST['timestamp']);
        if (!empty($_FILES) && $_POST['token'] == $verifyToken) {
            $name = $_FILES['Filedata']['name'];
            $temp_name = $_FILES['Filedata']['tmp_name'];
            $targetPath = $_SERVER['DOCUMENT_ROOT'] . $folder;
            $targetFile = rtrim($targetPath, '/') . '/' .$_POST['timestamp'].'.xlsx';
            if($_FILES['Filedata']['error'] == 0){              
                move_uploaded_file($temp_name, $targetFile);
                //$this->importarExcel($targetFile);
                 $excel = new Application_Model_Excel();
                 $contenedor_array = $excel->importa_excel($targetFile);
                 if($contenedor_array == 8 || $contenedor_array == '8'){
                     echo '8';
                 }else{
                    $modelo = new Application_Model_DbTable_Alumnos();
                    $modelo->importa_a_excel($contenedor_array);
                    echo '0';
                 }                  
            }elseif($_FILES['Filedata']['error'] == 1){
                echo '1';
            }elseif($_FILES['Filedata']['error'] == 2){
                echo '2';
            }elseif($_FILES['Filedata']['error'] == 3){
                echo '3';
            }elseif($_FILES['Filedata']['error'] == 4){
                echo '4';
            }elseif($_FILES['Filedata']['error'] == 5){
                echo '6';
            }           
        }
    }
    public function buscacpAction() {
        if ($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->layout->disableLayout();
            $_POST = $this->filter->process($_POST);
            $codigo_postal = addslashes($this->entityFilter->filter($this->sql_command($_POST['cp'])));
            $CP = new Application_Model_DbTable_Alumnos();
            $this->view->cargaCP = $CP->BuscaCP($codigo_postal);
        }
    }
    
    public function guardaAction(){    	    		
        $this->_helper->layout->disableLayout();
        $this->getHelper("viewRenderer")->setNoRender();
        $_POST = $this->filter->process($_POST);
		$BANDERA = addslashes($this->entityFilter->filter($this->sql_command($_POST['BANDERA'])));
        $S_NOMBRE = isset($_POST['S_NOMBRE']) ? addslashes($this->entityFilter->filter($this->sql_command($_POST['S_NOMBRE']))) : '';  
        $N_ID_PERIFL = isset($_POST['N_ID_PERIFL']) ? addslashes($this->entityFilter->filter($this->sql_command($_POST['N_ID_PERIFL']))) : '';
        $S_CALLE = isset($_POST['S_CALLE']) ? addslashes($this->entityFilter->filter($this->sql_command($_POST['S_CALLE']))) : '';
        $S_NUMERO_INTERIOR = isset($_POST['S_NUMERO_INTERIOR']) ? addslashes($this->entityFilter->filter($this->sql_command($_POST['S_NUMERO_INTERIOR']))) : '';
        $S_NUMERO_EXTERIOR = isset($_POST['S_NUMERO_EXTERIOR']) ? addslashes($this->entityFilter->filter($this->sql_command($_POST['S_NUMERO_EXTERIOR']))) : '';
        $N_ID_CP= isset($_POST['id_codigo_postal']) ? addslashes($this->entityFilter->filter($this->sql_command($_POST['id_codigo_postal']))) : '';
        $N_ID_PAIS = isset($_POST['N_ID_PAIS']) ? addslashes($this->entityFilter->filter($this->sql_command($_POST['N_ID_PAIS']))) : '';
        $N_ID_ESTATUS = isset($_POST['N_ID_ESTATUS']) ? addslashes($this->entityFilter->filter($this->sql_command($_POST['N_ID_ESTATUS']))) : '';
        if($BANDERA == 1){
			$D_FECHA_NACIMIENTO = isset($_POST['D_FECHA_NACIMIENTO']) ? addslashes($this->entityFilter->filter($this->sql_command($this->normaliza_date($_POST['D_FECHA_NACIMIENTO'],'-')))) : '';
			$D_FECHA_INGRESO= isset($_POST['D_FECHA_INGRESO']) ? addslashes($this->entityFilter->filter($this->sql_command($this->normaliza_date($_POST['D_FECHA_INGRESO'],'-')))) : '';
		}else{
			$D_FECHA_NACIMIENTO = isset($_POST['D_FECHA_NACIMIENTO']) ? addslashes($this->entityFilter->filter($this->sql_command($_POST['D_FECHA_NACIMIENTO']))) : '';
			$D_FECHA_INGRESO= isset($_POST['D_FECHA_INGRESO']) ? addslashes($this->entityFilter->filter($this->sql_command($_POST['D_FECHA_INGRESO']))) : '';
		}
        $S_MATRICULA = isset($_POST['S_MATRICULA']) ? addslashes($this->entityFilter->filter($this->sql_command($_POST['S_MATRICULA']))) : '';
        $S_CURP = isset($_POST['S_CURP']) ? addslashes($this->entityFilter->filter($this->sql_command($_POST['S_CURP']))) : '';
        $S_FOTO = isset($_POST['S_FOTO']) ? addslashes($this->entityFilter->filter($this->sql_command($_POST['S_FOTO']))) : '';
        $S_CORREO = isset($_POST['S_CORREO']) ? addslashes($this->entityFilter->filter($this->sql_command($_POST['S_CORREO']))) : '';
        $S_USUARIO = isset($_POST['S_USUARIO']) ? addslashes($this->entityFilter->filter($this->sql_command($_POST['S_USUARIO']))) : '';
        $S_CONTRASENA = isset($_POST['S_CONTRASENA']) ? addslashes($this->entityFilter->filter($this->sql_command($_POST['S_CONTRASENA']))) : '';
        $S_SEXO = isset($_POST['S_SEXO']) ? addslashes($this->entityFilter->filter($this->sql_command($_POST['S_SEXO']))) : '';        
        $N_ID_GRUPO = isset($_POST['N_ID_GRUPO']) ? addslashes($this->entityFilter->filter($this->sql_command($_POST['N_ID_GRUPO']))) : '';
        $N_ID_GRADO = isset($_POST['N_ID_GRADO']) ? addslashes($this->entityFilter->filter($this->sql_command($_POST['N_ID_GRADO']))) : '';
        $S_NOTA= isset( $_POST['S_NOTA']) ? addslashes($this->entityFilter->filter($this->sql_command($_POST['S_NOTA']))) : '';
        $N_TURNO = isset($_POST['N_TURNO']) ? addslashes($this->entityFilter->filter($this->sql_command($_POST['N_TURNO']))) : '';	
		$ID_DATOS_GENERALES = addslashes($this->entityFilter->filter($this->sql_command($_POST['N_DATOS_PERSONALES'])));
		$TIPO =addslashes($this->entityFilter->filter($this->sql_command($_POST['TIPO'])));
		$GRADO_ESTUDIO= isset($_POST['N_ID_GRADO_ESTUDIO']) ? addslashes($this->entityFilter->filter($this->sql_command($_POST['N_ID_GRADO_ESTUDIO']))) : '';
		$SUELDO= isset($_POST['N_SUELDO']) ? addslashes($this->entityFilter->filter($this->sql_command($_POST['N_SUELDO']))) : '';
		$NIVEL_PUESTO= isset($_POST['N_ID_NIVEL_PUESTO']) ? addslashes($this->entityFilter->filter($this->sql_command($_POST['N_ID_NIVEL_PUESTO']))) : '';
		$LUGAR_TRABAJO= isset($_POST['S_LUGAR_TRABAJO']) ? addslashes($this->entityFilter->filter($this->sql_command($_POST['S_LUGAR_TRABAJO']))) : '';
		$RANGO_PUESTO= isset($_POST['RANGO_PUESTO']) ? addslashes($this->entityFilter->filter($this->sql_command($_POST['RANGO_PUESTO']))) : '';
		$PUESTO=isset($_POST['S_PUESTO']) ? addslashes($this->entityFilter->filter($this->sql_command($_POST['S_PUESTO']))) : '';
		$ID_PARIENTE= isset($_POST['ID_PARIENTE']) ? addslashes($this->entityFilter->filter($this->sql_command($_POST['ID_PARIENTE']))) : '';
        $ID_PADRE = isset($_POST['N_ID_PARIENTE']) ? addslashes($this->entityFilter->filter($this->sql_command($_POST['N_ID_PARIENTE']))) : '';
		$modelo = new Application_Model_DbTable_Alumnos();		
        echo $modelo->guarda($N_ID_PERIFL, $S_NOMBRE, $S_CALLE, $S_NUMERO_INTERIOR, $S_NUMERO_EXTERIOR, $N_ID_CP, $N_ID_PAIS, $N_ID_ESTATUS, $D_FECHA_NACIMIENTO, $D_FECHA_INGRESO, $S_MATRICULA, $S_CURP, $S_FOTO, $S_CORREO, $S_USUARIO, md5($S_CONTRASENA), $S_SEXO, $S_NOTA, $BANDERA,$ID_DATOS_GENERALES,$TIPO,$GRADO_ESTUDIO,$SUELDO,$NIVEL_PUESTO,$LUGAR_TRABAJO,$SUELDO,$PUESTO,$ID_PARIENTE,$ID_PADRE); 
    }  
    
    public function eliminaAction() {
        $this->_helper->layout->disableLayout();
        $this->getHelper("viewRenderer")->setNoRender();
        $_POST = $this->filter->process($_POST);
        $N_DATOS_PERSONALES= addslashes($this->entityFilter->filter($this->sql_command($_POST['N_DATOS_PERSONALES'])));
        $NOMBRE_TABLA = addslashes($this->entityFilter->filter($this->sql_command($_POST['TIPO'])));
		$modelo = new Application_Model_DbTable_Alumnos();
        echo $modelo->elimina($N_DATOS_PERSONALES,$NOMBRE_TABLA);
    }    
    
    function exportarexcelAction(){
        $this->_helper->layout->disableLayout();
        $this->getHelper("viewRenderer")->setNoRender();
        $array_hojas = [0=>"tb_datos_generales",1=>"tb_alumno"];
        $excel = new Application_Model_Excel();
        $titulo = 'Catalogo Alumnos '.date("Y-m-d H:i:s");
        $perfil = 1;
        $excel->exportar_excel($array_hojas,$titulo,$perfil);
    }	
}