<?php

class Application_Model_DbTable_APG extends Zend_Db_Table_Abstract {
	public function carga_materias($nombre_profesor){
		$db = Zend_Db_Table_Abstract::getDefaultAdapter();
		$query = "SELECT tb_arbol_amp.id_materia, tb_arbol_amp.id_profesor, tb_datos_generales.S_NOMBRE AS nombre_profesor,tb_materias.materia AS nombre_materia
				  FROM tb_arbol_amp, tb_datos_generales, tb_materias, tb_profesor
				  WHERE tb_arbol_amp.id_materia = tb_materias.id 
				  AND tb_arbol_amp.id_profesor = tb_profesor.N_ID_PROFESOR
				  AND tb_datos_generales.N_DATOS_PERSONALES = tb_profesor.N_DATOS_PERSONALES
				  AND tb_datos_generales.S_NOMBRE = '$nombre_profesor'";
		$filas = $db->fetchAll($query);
		return $filas;
	}
	
	public function carga_horas(){
		$db = Zend_Db_Table_Abstract::getDefaultAdapter();
		$query="select id_hora as id_hora, hora from tb_horas";
		$filas = $db->fetchAll($query);
		return $filas;
	}
	
	public function carga_datos($id_grupo){
		$db = Zend_Db_Table_Abstract::getDefaultAdapter();
		$query ="SELECT
	tb_apg.id_grupo,
	tb_grupo.S_GRUPO AS nombre_grupo,
	tb_horas.hora,
	tb_apg.id_hora,
	tb_materias.materia,
	tb_apg.id_materia,
	tb_apg.id_profesor,
	tb_apg.dia,
	tb_datos_generales.S_NOMBRE AS nombre_profesor	
FROM
tb_apg ,
tb_grupo ,
tb_horas ,
tb_materias ,
tb_profesor ,
tb_datos_generales
WHERE
tb_apg.id_grupo = tb_grupo.N_ID_GRUPO AND
tb_apg.id_hora = tb_horas.id_hora AND
tb_apg.id_materia = tb_materias.id AND
tb_apg.id_profesor = tb_profesor.N_ID_PROFESOR AND
tb_datos_generales.N_DATOS_PERSONALES = tb_profesor.N_DATOS_PERSONALES AND
tb_grupo.N_ID_GRUPO = $id_grupo
UNION
SELECT
	tb_apg.id_grupo,
	tb_grupo.S_GRUPO AS nombre_grupo,
	tb_horas.hora,
	tb_apg.id_hora,
	case tb_apg.id_materia WHEN 0 THEN '' END as materia,
	case tb_apg.id_materia WHEN 0 THEN '' END as id_materia,
	case tb_apg.id_profesor WHEN 0 THEN '' END as id_profesor,
	tb_apg.dia,
	case tb_apg.id_profesor WHEN 0 THEN '' END as nombre_profesor
FROM tb_apg,tb_horas,tb_grupo
WHERE tb_apg.id_materia = 0 AND tb_grupo.N_ID_GRUPO = $id_grupo AND tb_apg.id_hora = tb_horas.id_hora AND tb_apg.id_grupo = tb_grupo.N_ID_GRUPO
ORDER BY id_hora,dia";
		$filas = $db->fetchAll($query);
		return $filas;
	}
	
	public function guarda_apg($arreglo,$id_grupo){
		$db = Zend_Db_Table_Abstract::getDefaultAdapter();		
		$sql2="DELETE FROM tb_apg WHERE id_grupo = '$id_grupo'";
		$ejecuta_vaciar = $db->query($sql2);		
		$sql="";
		$sql.="insert into tb_apg(id_grupo,id_hora,id_materia,id_profesor,dia) values";
		foreach($arreglo as $llave_principal => $valor_principal){
			foreach($valor_principal as $llave_grupos => $valor_grupo){
				$total_fila = count($valor_grupo['horario']);			
				foreach($valor_grupo['horario'] as $llave_horarios => $valor_horario){
					$id_grupo = $valor_grupo['grupo'];																			
					$id_hora = $valor_horario['hora'];					
					foreach($valor_horario['dias'] as $llave_dias => $valor_dia){
						if(count($llave_dias) > 0){
							$sql.="('$id_grupo',";	
							$sql.="'$id_hora',";
						}						
						$dia = $valor_dia['dia'];
						$id_materia = $valor_dia['materia'];
						$id_profesor = $valor_dia['profesor'];
						$sql.="'$id_materia','$id_profesor','$dia'),";
					}
					
				}				
			}			
		}				
		$cadena = trim($sql, ',');				
		$ejecuta = $db->query($cadena);
	}
	
	public function checa_disponibilidad($id_profesor, $id_hora, $dia){
		$db = Zend_Db_Table_Abstract::getDefaultAdapter();
		$pa= "CALL consulta_APG('$id_hora','$id_profesor','$dia');";
		$ejecuta = $db->fetchAll($pa);				
		return $ejecuta;
	}
	
}