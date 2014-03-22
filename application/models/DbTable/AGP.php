<?php

class Application_Model_DbTable_AGP extends Zend_Db_Table_Abstract {
    public function carga_materia(){
	$db = Zend_Db_Table_Abstract::getDefaultAdapter();
	$query = "SELECT * FROM tb_grupo";
	$filas = $db->fetchAll($query);
	return $filas;
    }
    
    
    public function guarda_amp($arreglo){
	$db = Zend_Db_Table_Abstract::getDefaultAdapter();
	$sql2="truncate table tb_agp";
	$ejecuta_vaciar = $db->query($sql2);	
	$sql="";
	$sql.="insert into tb_agp(N_ID_GRUPO,N_ID_PROFESOR) values";
	foreach($arreglo as $idProfesor => $materias){
	    /*
	     *valida que no se repitan los grupos
	     */
	    $quitaRepetidos = array_unique($materias);
	    $listOrdenada = array_values($quitaRepetidos);	    
	    foreach($listOrdenada as $llave => $materia){
		  $sql.="($materia,$idProfesor),";
	    }
	}	
	$cadena = trim($sql, ',');	
	$ejecuta = $db->query($cadena);
    }
    public function grupo_seleccionado($idGrupo){
    	$db = Zend_Db_Table_Abstract::getDefaultAdapter();
    	$query = "select * from tb_grupo where N_ID_GRUPO = $idGrupo";
    	$filas = $db->fetchAll($query);
    	return $filas;
    }
}