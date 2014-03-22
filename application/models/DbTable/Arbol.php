<?php

class Application_Model_DbTable_Arbol extends Zend_Db_Table_Abstract {
	public function cargar_arbol($id,$tabla){
		$db = Zend_Db_Table_Abstract::getDefaultAdapter();
		$query = "select * from $tabla where parentId = $id";
		$filas = $db->fetchAll($query);
		$resultado = array();
		foreach($filas as $llave => $valor){
			$nodo = array();
			$nodo['id'] = $valor['id'];
			$nodo['text'] = $valor['text'];
			$nodo['state'] = $this->has_child($valor['id'],$tabla)? 'closed' : 'open';
			array_push($resultado,$nodo);
		}
		return $resultado;
	}
	public function has_child($id,$tabla){
		$db = Zend_Db_Table_Abstract::getDefaultAdapter();
		$rs ="select count(*) as total from $tabla where parentId=$id";
		$filas = $db->fetchAll($rs);		
		return $filas[0]['total'] > 0 ? true : false;
	}
	public function carga_materia(){
		$db = Zend_Db_Table_Abstract::getDefaultAdapter();
		$query = "SELECT * FROM tb_materias";
		$filas = $db->fetchAll($query);
		return $filas;
	}
	
	public function carga_profesor_materias($tipo){
		$db = Zend_Db_Table_Abstract::getDefaultAdapter();		
		$query ="CALL amp('$tipo');";
		$filas = $db->fetchAll($query);
		return $filas;
	}
	
	public function carga_profesor($id_profesor){
		$db = Zend_Db_Table_Abstract::getDefaultAdapter();
		$query = "SELECT DISTINCT tb_datos_generales.S_NOMBRE as nombre, tb_datos_generales.N_DATOS_PERSONALES AS id_profesor
				  FROM tb_materias, tb_arbol_amp, tb_profesor, tb_datos_generales
				  WHERE tb_arbol_amp.id_materia = tb_materias.id 
				  AND tb_arbol_amp.id_profesor = tb_profesor.N_ID_PROFESOR 
				  AND tb_profesor.N_DATOS_PERSONALES = tb_datos_generales.N_DATOS_PERSONALES
		          AND tb_profesor.N_DATOS_PERSONALES = $id_profesor
				  GROUP BY tb_datos_generales.S_NOMBRE";
		$filas = $db->fetchAll($query);
		return $filas;
	}
	
	public function carga_profesores(){
		$db = Zend_Db_Table_Abstract::getDefaultAdapter();
		$query = "SELECT DISTINCT tb_datos_generales.S_NOMBRE as nombre, tb_datos_generales.N_DATOS_PERSONALES AS id_profesor
		FROM tb_materias, tb_arbol_amp, tb_profesor, tb_datos_generales
		WHERE tb_arbol_amp.id_materia = tb_materias.id
		AND tb_arbol_amp.id_profesor = tb_profesor.N_ID_PROFESOR
		AND tb_profesor.N_DATOS_PERSONALES = tb_datos_generales.N_DATOS_PERSONALES";
		$filas = $db->fetchAll($query);
		return $filas;
	}
	public function guarda_amp($arreglo){
		$db = Zend_Db_Table_Abstract::getDefaultAdapter();
		$sql2="truncate table tb_arbol_AMP";
		$ejecuta_vaciar = $db->query($sql2);
		
		$sql="";
		$sql.="insert into tb_arbol_AMP(id_profesor,id_materia) values";		
		foreach($arreglo as $idProfesor => $materias){			
			foreach($materias as $llave => $materia){				
				$sql.="('$idProfesor','$materia'),";	
			}			
		}
		$cadena = trim($sql, ',');		
		$ejecuta = $db->query($cadena);
	}
	public function carga_arbol_grupo($id_datos_personales){
		$db = Zend_Db_Table_Abstract::getDefaultAdapter();
		$queryGrupos ="SELECT
                        tb_agp.N_ID_GRUPO as id_grupo,
                        tb_datos_generales.N_DATOS_PERSONALES as id_profesor,
                        tb_grupo.S_GRUPO as grupo,
                        tb_datos_generales.S_NOMBRE as nombre_profesor
                        FROM
                        tb_agp ,
                        tb_profesor ,
                        tb_grupo ,
                        tb_datos_generales
                        WHERE tb_agp.N_ID_GRUPO = tb_grupo.N_ID_GRUPO AND tb_agp.N_ID_PROFESOR = tb_profesor.N_ID_PROFESOR
                        AND tb_profesor.N_DATOS_PERSONALES = tb_datos_generales.N_DATOS_PERSONALES AND tb_datos_generales.N_DATOS_PERSONALES = $id_datos_personales";
		$filas = $db->fetchAll($queryGrupos);
		return $filas;
	}
	
	
}