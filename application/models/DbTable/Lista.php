<?php
class Application_Model_DbTable_Lista extends Zend_Db_Table_Abstract {
	public function carga_materias_grupo($id_datos_personales,$id_grupo){
		$db = Zend_Db_Table_Abstract::getDefaultAdapter();
		$sql="SELECT
tb_grupo.S_GRUPO,
tb_apg.id_grupo,
tb_apg.id_materia,
tb_apg.id_profesor,
tb_materias.materia,
tb_datos_generales.S_NOMBRE
FROM
tb_apg ,
tb_grupo ,
tb_materias ,
tb_profesor ,
tb_datos_generales
WHERE
tb_apg.id_grupo = tb_grupo.N_ID_GRUPO
AND tb_apg.id_materia = tb_materias.id
AND tb_apg.id_profesor = tb_profesor.N_DATOS_PERSONALES
AND tb_profesor.N_DATOS_PERSONALES = tb_datos_generales.N_DATOS_PERSONALES
AND tb_profesor.N_DATOS_PERSONALES = $id_datos_personales
AND tb_apg.id_grupo = $id_grupo
GROUP BY id_profesor";
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
	
	public function forma_json_property_grid($id_escuela,$id_datos_personales, $id_materia){
		$db = Zend_Db_Table_Abstract::getDefaultAdapter();
		$query_datos_escuela="SELECT 
								tbl_escuela.nombre_escuela,
								tbl_escuela.clave,
								tbl_escuela.zona,
								tbl_escuela.sector,
								tbl_escuela.calle,
								tbl_codigo_postal.colonia,
								tbl_codigo_postal.localidad,
								tbl_codigo_postal.municipio,
								tbl_codigo_postal.codigo_postal as cp
								FROM
									tbl_escuela,tbl_codigo_postal
								WHERE
									id_escuela = '$id_escuela' AND 
									tbl_codigo_postal.id_codigo_postal= tbl_escuela.id_codigo_postal";
		$query_datos_grupo="SELECT tb_apg.id_materia, tb_materias.materia, tb_grupo.S_GRUPO
							FROM tb_apg ,tb_grupo ,tb_profesor ,tb_datos_generales ,tb_materias
							WHERE tb_apg.id_grupo = tb_grupo.N_ID_GRUPO AND tb_profesor.N_DATOS_PERSONALES = tb_datos_generales.N_DATOS_PERSONALES AND 
							tb_apg.id_materia = tb_materias.id AND tb_apg.id_grupo = tb_grupo.N_ID_GRUPO AND tb_profesor.N_DATOS_PERSONALES =$id_datos_personales
							AND tb_materias.id = '$id_materia'";
		$query_datos_profesor="SELECT tb_datos_generales.S_NOMBRE FROM tb_datos_generales , tb_profesor
							   WHERE tb_datos_generales.N_DATOS_PERSONALES = tb_profesor.N_DATOS_PERSONALES
								AND tb_datos_generales.N_DATOS_PERSONALES= $id_datos_personales";
		
		$filas_escuela = $db->fetchAll($query_datos_escuela);
		$filas_grupo = $db->fetchAll($query_datos_grupo);
		$filas_profesor =$db->fetchAll($query_datos_profesor);
		$array = array(	"rows"=>array(
								array(
									"name"=>"Nombrela escuela",
									"value"=>ucwords($filas_escuela[0]['nombre_escuela']),
									"group"=>"Escuela"
								),
								array(
									"name"=>"Clave",
									"value"=>strtoupper($filas_escuela[0]['clave']),
									"group"=>"Escuela"
								),
								array(
									"name"=>"Zona",
									"value"=>ucwords($filas_escuela[0]['zona']),
									"group"=>"Escuela"
								),								
								array(
									"name"=>"Sector",
									"value"=>$filas_escuela[0]['sector'],
									"group"=>"Escuela"
								),
								array(
									"name"=>"Calle",
									"value"=>ucwords($filas_escuela[0]['calle']),
									"group"=>"Direccion"
								),
								array(
									"name"=>"Colonia",
									"value"=>ucwords($filas_escuela[0]['colonia']),
									"group"=>"Direccion"
								),
								array(
									"name"=>"Localidad",
									"value"=>ucwords($filas_escuela[0]['localidad']),
									"group"=>"Direccion"
								),
								array(
									"name"=>"Municipio",
									"value"=>ucwords($filas_escuela[0]['municipio']),
									"group"=>"Direccion"
								),
								array(
									"name"=>"Codigo postal",
									"value"=>$filas_escuela[0]['cp'],
									"group"=>"Direccion"
								),
								array(
									"name"=>"Nombre del profesor",
									"value"=>ucwords($filas_profesor[0]['S_NOMBRE']),
									"group"=>"Profesor"
								),
								array(
									"name"=>"Grado y Grupo",
									"value"=>$filas_grupo[0]['S_GRUPO'],
									"group"=>"Profesor"
								),
								array(
									"name"=>"Materia",
									"value"=>$filas_grupo[0]['materia'],
									"group"=>"Profesor"
								)
							)
				);
		return $array;
		
	}
	
	public function insertaDefault($id_materia,$id_grupo,$id_profesor,$id_alumno){
		$db = Zend_Db_Table_Abstract::getDefaultAdapter();
		$query ="INSERT INTO tb_notas (id_materia, id_grupo, id_profesor, id_datos_personales, id_mes, notas,N_ID_FORMA_CALIFICAR)
			SELECT $id_materia,$id_grupo,$id_profesor, $id_alumno, '1' as id_mes, '0' as nota, tb_forma_calificar.N_ID_FORMA_CALIFICAR
			FROM
			tb_forma_calificar ,
			tb_materias ,
			tb_grupo ,
			tb_datos_generales
			WHERE
			tb_forma_calificar.N_ID_GRUPO = tb_grupo.N_ID_GRUPO
			AND tb_forma_calificar.N_ID_MATERIA = tb_materias.id
			AND tb_forma_calificar.N_ID_PROFESOR = tb_datos_generales.N_DATOS_PERSONALES
			AND tb_forma_calificar.N_ID_MATERIA = $id_materia
			AND tb_forma_calificar.N_ID_GRUPO = $id_grupo
			AND tb_forma_calificar.N_ID_PROFESOR = $id_profesor";		
		$ejecuta = $db->query($query);		
		
	}
	
	public function guarda_datos($json, $id_grupo, $id_materia,$id_profesor){
		$db = Zend_Db_Table_Abstract::getDefaultAdapter();
		$borraDatos = "delete from tb_notas where id_materia='$id_materia' and id_grupo = '$id_grupo' and id_profesor='$id_profesor'";
		$ejecuta_elimina = $db->query($borraDatos);
		$inserta="";
		$inserta.= "INSERT INTO tb_notas(id_materia,id_grupo,id_profesor,id_datos_personales,id_mes,notas,N_ID_FORMA_CALIFICAR) VALUES";
		$contadorNotas = 1;		
		$numeroTitulo = $this->numero_de_titulos($id_grupo, $id_materia,$id_profesor)+1;
		foreach($json as $valor_principal){
			for ($i=2; $i <= $numeroTitulo; $i++) { 
				$nota = 'nota'.$i;
				$idFormaCalificar = 'id_forma_calificar'.$i;
				$inserta.="('$id_materia','$id_grupo','$id_profesor','{$valor_principal['id_alumno']}','{$valor_principal['id_mes']}','{$valor_principal[$nota]}','{$valor_principal[$idFormaCalificar]}'),";	
			}					
		}		
		$cadena = trim($inserta, ',');		
		$ejecuta_inserta = $db->query($cadena);
	}
	
	public function filas_conceptos($id_grupo,$id_materia,$id_profesor){
		$db = Zend_Db_Table_Abstract::getDefaultAdapter();
		$query = "SELECT S_CONCEPTO as concepto, N_PORCENTAJE as porcentaje, N_ID_FORMA_CALIFICAR AS id_formaCalificar
				  FROM tb_forma_calificar 
				  WHERE N_ID_MATERIA = $id_materia AND N_ID_GRUPO = $id_grupo AND N_ID_PROFESOR = $id_profesor ORDER BY N_PORCENTAJE DESC, N_ID_FORMA_CALIFICAR";
		$ejecuta = $db->fetchAll($query);
		return $ejecuta;
	}
	
	public function numero_de_titulos($id_grupo,$id_materia,$id_profesor){
		$db = Zend_Db_Table_Abstract::getDefaultAdapter();
		$query = "SELECT COUNT(*) AS total
				  FROM tb_forma_calificar 
				  WHERE N_ID_MATERIA = $id_materia AND N_ID_GRUPO = $id_grupo AND N_ID_PROFESOR = $id_profesor";
		$ejecuta = $db->fetchAll($query);
		return $ejecuta[0]['total'];
	}
	
	public function mes(){
		$db = Zend_Db_Table_Abstract::getDefaultAdapter();
		$query = "SELECT id_mes, mes as mes_nota FROM tb_meses";
		$ejecuta = $db->fetchAll($query);
		return $ejecuta;
	}
	
	public function data_grid_notas($id_materia, $id_grupo, $id_profesor){
		$db = Zend_Db_Table_Abstract::getDefaultAdapter();
		$queryAlumnos = "SELECT
			tb_datos_generales.N_DATOS_PERSONALES as id_alumno, 
			int_cap(tb_datos_generales.S_NOMBRE) AS nombre_alumno,
			tb_datos_generales.S_MATRICULA AS matricula,
			tb_meses.id_mes,
			tb_meses.mes AS mes_nota	
			FROM
			tb_notas ,
			tb_materias ,
			tb_grupo ,
			tb_datos_generales,
			tb_meses
			WHERE
			tb_notas.id_materia = tb_materias.id AND
			tb_notas.id_grupo = tb_grupo.N_ID_GRUPO AND
			tb_notas.id_datos_personales = tb_datos_generales.N_DATOS_PERSONALES AND			
			tb_notas.id_mes = tb_meses.id_mes AND
			tb_notas.id_materia = $id_materia AND
			tb_notas.id_grupo = $id_grupo AND
			tb_notas.id_profesor = $id_profesor
			GROUP BY S_NOMBRE
			ORDER BY S_NOMBRE";		
					
		$ejecuta = $db->fetchAll($queryAlumnos);
		return $ejecuta;
	}
	
	public function agrupa($id_alumno, $id_materia, $id_grupo, $id_profesor){
		$db = Zend_Db_Table_Abstract::getDefaultAdapter();
		$sql = "SELECT
            	tb_notas.notas,
            	tb_notas.id_mes,
            	tb_meses.mes AS mes_nota,
            	tb_forma_calificar.N_PORCENTAJE AS porcentaje,
            	tb_forma_calificar.N_ID_FORMA_CALIFICAR AS id_forma_calificar
            FROM
            	tb_notas ,
            	tb_materias ,
            	tb_grupo ,
            	tb_datos_generales ,
            	tb_meses,
            	tb_forma_calificar
            WHERE
            	tb_notas.id_grupo = tb_grupo.N_ID_GRUPO AND
            	tb_notas.id_materia = tb_materias.id AND
            	tb_notas.id_profesor = tb_datos_generales.N_DATOS_PERSONALES AND
            	tb_notas.id_mes = tb_meses.id_mes AND
            	tb_notas.N_ID_FORMA_CALIFICAR = tb_forma_calificar.N_ID_FORMA_CALIFICAR AND
            	tb_notas.id_grupo = $id_grupo AND
            	tb_notas.id_materia = $id_materia AND
            	tb_notas.id_profesor = $id_profesor AND
            	tb_notas.id_datos_personales = $id_alumno
           ORDER BY tb_forma_calificar.N_PORCENTAJE DESC, tb_forma_calificar.N_ID_FORMA_CALIFICAR";
		$ejecuta = $db->fetchAll($sql);
		return $ejecuta;
	}
	
	function totalnotas($id_materia, $id_grupo, $id_profesor){
		$db = Zend_Db_Table_Abstract::getDefaultAdapter();
		$sql="SELECT COUNT(*) as total FROM tb_notas WHERE id_grupo = $id_grupo AND id_materia = $id_materia AND id_profesor = $id_profesor";
		$ejecuta = $db->fetchAll($sql);
		return $ejecuta[0]['total'];
	}
	
	public function alumnosPorGrupo($id_grupo){
		$db = Zend_Db_Table_Abstract::getDefaultAdapter();
		$sql = "SELECT
				tb_alumno.N_DATOS_PERSONALES,
				tb_datos_generales.S_NOMBRE
				FROM
				tb_alumno ,
				tb_datos_generales ,
				tb_grupo
				WHERE tb_alumno.N_DATOS_PERSONALES = tb_datos_generales.N_DATOS_PERSONALES
				AND tb_alumno.N_ASIGNADO = tb_grupo.N_ID_GRUPO
				AND tb_grupo.N_ID_GRUPO = $id_grupo";
		$ejecuta = $db->fetchAll($sql);
		return $ejecuta;
	}
	
	public function carga_grid_forma_calificar($id_materia, $id_grupo, $id_profesor){
		$db = Zend_Db_Table_Abstract::getDefaultAdapter();
		$query = "select * from tb_forma_calificar where N_ID_MATERIA = '$id_materia' and N_ID_GRUPO = '$id_grupo' and N_ID_PROFESOR = '$id_profesor' ORDER BY N_PORCENTAJE DESC";
		$filas = $db->fetchAll($query);
		return $filas;
	}
	
	public function guarda_formadecalificar($id_materia, $id_grupo, $id_profesor, $concepto, $porcentaje,$id_mes,$id_ciclo_escolar){
		$db = Zend_Db_Table_Abstract::getDefaultAdapter();
		echo $query = "insert into tb_forma_calificar(N_ID_MATERIA,N_ID_GRUPO,N_ID_PROFESOR,S_CONCEPTO,N_PORCENTAJE,N_MES,N_ID_CICLOESCOLAR)
			  VALUES('$id_materia', '$id_grupo', '$id_profesor', '$concepto', '$porcentaje',$id_mes,$id_ciclo_escolar)";
		$ejecuta = $db->query($query);
	}
	
	public function updtae_formadecalificar($id,$concepto,$porcentaje){
		$db = Zend_Db_Table_Abstract::getDefaultAdapter();
		$query = "update tb_forma_calificar set S_CONCEPTO = '$concepto',N_PORCENTAJE = '$porcentaje' where N_ID_FORMA_CALIFICAR = '$id'";
		$ejecuta = $db->query($query);
	}
	public function delete_fromadecalificar($id){
		$db = Zend_Db_Table_Abstract::getDefaultAdapter();
		$query = "delete from tb_forma_calificar where N_ID_FORMA_CALIFICAR = '$id'";
		$ejecuta = $db->query($query);
	}
	
	public function valida_porcentaje($id_materia,$id_grupo,$id_profesor,$concepto, $porcentaje,$id_mes,$id_ciclo_escolar){
	    $db = Zend_Db_Table_Abstract::getDefaultAdapter();
		$total = $this->cien_porciento($id_materia, $id_grupo, $id_profesor);
		
		$queryTotalNotal = "SELECT COUNT(*) AS totalNotas FROM tb_forma_calificar WHERE N_ID_MATERIA = $id_materia AND N_ID_GRUPO = $id_grupo AND N_ID_PROFESOR = $id_profesor";
		$ejecutaTotalNotal = $db->fetchAll($queryTotalNotal);
		
		$totalNotas = $ejecutaTotalNotal[0]['totalNotas'];
		if($totalNotas == 0){
			$queryInsertaPromedio = "INSERT INTO tb_forma_calificar(N_ID_MATERIA,N_ID_GRUPO,N_ID_PROFESOR,S_CONCEPTO,N_PORCENTAJE,N_MES,N_ID_CICLOESCOLAR)
			                         VALUES($id_materia,$id_grupo,$id_profesor,'Promedio',0.00,$id_mes,$id_ciclo_escolar)";
			$ejecutaInsertaPromedio = $db->query($queryInsertaPromedio);
		}
		if ($total > 100) {
		    json_encode(array(
		    		'isError' => true,
		    		'msg' => 'Has sobrepasado el 100% para la evaluacacion.'
		    ));
			
		}else{
			$this->guarda_formadecalificar($id_materia, $id_grupo, $id_profesor, $concepto, $porcentaje, $id_mes, $id_ciclo_escolar);			
		}
	}
	
	public function cien_porciento($id_materia,$id_grupo,$id_profesor){
	    $db = Zend_Db_Table_Abstract::getDefaultAdapter();
	    $query = "select sum(N_PORCENTAJE) as total from tb_forma_calificar where N_ID_MATERIA = $id_materia and N_ID_GRUPO = $id_grupo and N_ID_PROFESOR = $id_profesor";
	    $ejecuta = $db->fetchAll($query);
	    $total = $ejecuta[0]['total'];
	    return intval($total);
	}
}