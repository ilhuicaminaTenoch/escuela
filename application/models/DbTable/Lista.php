<?php
class Application_Model_DbTable_Lista extends Zend_Db_Table_Abstract {
	public function carga_materias_grupo($id_datos_personales){
		$db = Zend_Db_Table_Abstract::getDefaultAdapter();
		$sql="SELECT tb_apg.id_materia, tb_materias.materia 
			  FROM tb_apg ,tb_grupo ,tb_profesor ,tb_datos_generales ,tb_materias
			  WHERE tb_apg.id_grupo = tb_grupo.N_ID_GRUPO AND tb_profesor.N_DATOS_PERSONALES = tb_datos_generales.N_DATOS_PERSONALES AND 
			  tb_apg.id_materia = tb_materias.id AND tb_apg.id_grupo = tb_grupo.N_ID_GRUPO AND tb_profesor.N_DATOS_PERSONALES = $id_datos_personales AND tb_apg.id_materia != 0";
		$filas = $db->fetchAll($sql);
		return $filas;
	}
	public function carga_alumnos($id_grupo,$empieza, $termina){
		$db = Zend_Db_Table_Abstract::getDefaultAdapter();
		$sql="SELECT tb_alumno.N_DATOS_PERSONALES as id, tb_datos_generales.S_NOMBRE as nombre, tb_grupo.S_GRUPO as grupo, 
			  tb_datos_generales.S_CURP AS CURP, tb_datos_generales.D_FECHA_NACIMIENTO AS fn,  
			  CURDATE() AS fecha_actual,(YEAR(CURDATE())-YEAR(tb_datos_generales.D_FECHA_NACIMIENTO)) - (RIGHT(CURDATE(),5)<RIGHT(tb_datos_generales.D_FECHA_NACIMIENTO,5)) AS edad
			  FROM tb_alumno, tb_datos_generales, tb_grupo
			  WHERE tb_alumno.N_DATOS_PERSONALES = tb_datos_generales.N_DATOS_PERSONALES 
			  AND tb_alumno.N_ASIGNADO = tb_grupo.N_ID_GRUPO
			  AND tb_grupo.N_ID_GRUPO= '$id_grupo'
			  LIMIT $empieza,$termina";
		$filas = $db->fetchAll($sql);
		return $filas;
	}
	
	public function genera_archivo_json($contenido){		
		$nombre_archivo="lista_alumnos.json";
		$mensaje='';
		if (is_writable($nombre_archivo)) {
			if (!$gestor = fopen($nombre_archivo, 'w')) {
				$mensaje="No se puede abrir el archivo ($nombre_archivo)";
				exit;
			}
			if (fwrite($gestor, $contenido) === FALSE) {
				$mensaje= "No se puede escribir en el archivo ($nombre_archivo)";
				exit;
			}
			//$mensaje="Exito, se escribió ($contenido) en el archivo ($nombre_archivo)";
			fclose($gestor);
		}else {
			$mensaje= "El archivo $nombre_archivo no es escribible";
		}
		return $mensaje;
	
	}
}