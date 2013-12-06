<?php
class Application_Model_DbTable_Jerarquias extends Zend_Db_Table_Abstract {
	public function padres(){
		$db = Zend_Db_Table_Abstract::getDefaultAdapter();		
		$sql ="select id_jerarquia as id, padres as nombre from tbl_jerarquias";
		$filas = $db->fetchAll($sql);
		return $filas;
	}
	
	public function lista_maestros($id_padre){
		$db = Zend_Db_Table_Abstract::getDefaultAdapter();
		if($id_padre == 1 || $id_padre == '1'){//MAESTROS
			$sql="select S_NOMBRE as nombre, N_DATOS_PERSONALES as id from tb_datos_generales where N_ID_PERIFL = 2 order by S_NOMBRE";
		}else if($id_padre == 2 || $id_padre == '2'){//GRUPOS
			$sql="select N_ID_GRUPO as id, S_GRUPO as nombre from tb_grupo ORDER BY S_GRUPO";
		}
		$filas = $db->fetchAll($sql);
		return $filas;
	}
	
	public function lista_materias(){
		$db = Zend_Db_Table_Abstract::getDefaultAdapter();
		$sql="select id, materia as nombre from tb_materias";
		$filas = $db->fetchAll($sql);
		return $filas;
	}
	
	public function lista_elementos($id_padre,$id_profesor){
		$db = Zend_Db_Table_Abstract::getDefaultAdapter();
		if($id_padre == 1 || $id_padre == '1'){//MAESTROS
			$sql="select N_MATERIA_ASIGNADA as id_materia FROM tb_profesor where N_ID_PROFESOR = $id_profesor";
		}else if($id_padre == 2 || $id_padre == '2'){//GRUPOS
			$sql="SELECT tb_alumno.N_DATOS_PERSONALES as id, tb_datos_generales.S_NOMBRE as nombre, tb_grupo.S_GRUPO as grupo, tb_datos_generales.S_CURP AS CURP,
				  tb_datos_generales.D_FECHA_NACIMIENTO AS fn
				 FROM tb_alumno, tb_datos_generales, tb_grupo
				 WHERE tb_alumno.N_DATOS_PERSONALES = tb_datos_generales.N_DATOS_PERSONALES 
				 AND tb_alumno.N_ASIGNADO = tb_grupo.N_ID_GRUPO
				 AND tb_grupo.N_ID_GRUPO= '$id_profesor'";
		}
		$filas = $db->fetchAll($sql);
		return $filas;
	}
	
	public function guarda_tipo($id_padre,$id_hijo,$id_alumnos){
		$db = Zend_Db_Table_Abstract::getDefaultAdapter();
		$separa = explode(',',$id_alumnos);		
		$cuenta = count($separa);
		for($i = 0; $i<$cuenta; $i++){
			$sql="UPDATE tb_alumno SET N_ASIGNADO ='$id_hijo' WHERE N_DATOS_PERSONALES ='{$separa[$i]}'";
			$db->query($sql);
		}		
		echo '0';
	}
	
	public function combo_grid($q){
		$db = Zend_Db_Table_Abstract::getDefaultAdapter();
		$sql="SELECT N_ID_GRUPO as id, S_GRUPO as grupo, case N_ID_ESTATUS when 1 then 'Activo' when 0 then 'Inactivo' end as estatus
			FROM tb_grupo 
			WHERE S_GRUPO LIKE '%$q'  AND N_ID_ESTATUS = 1 ORDER BY S_GRUPO";
		$filas = $db->fetchAll($sql);		
		return $filas;
	}
	
	public function combo_grid_almnos($q){
		$db = Zend_Db_Table_Abstract::getDefaultAdapter();
		$sql="SELECT
			tb_datos_generales.S_NOMBRE as nombre,
			DATE_FORMAT(tb_datos_generales.D_FECHA_NACIMIENTO, '%d-%m-%Y') AS fecha,
			tb_datos_generales.S_MATRICULA as matricula,
			case tb_datos_generales.S_SEXO WHEN 1 THEN 'Masculino' WHEN 2 THEN 'Femenino' END as sexo,
			tb_datos_generales.N_DATOS_PERSONALES AS id
			FROM
			tb_datos_generales, tb_alumno
			WHERE
			tb_datos_generales.N_ID_PERIFL = 1
			AND tb_alumno.N_DATOS_PERSONALES = tb_datos_generales.N_DATOS_PERSONALES
			AND tb_datos_generales.S_NOMBRE LIKE '$q%' 
			AND tb_alumno.N_ASIGNADO = 0
			ORDER BY S_NOMBRE";
		$filas = $db->fetchAll($sql);		
		return $filas;
	}
	
	public function quita_alumno_grupo($id){
		$db = Zend_Db_Table_Abstract::getDefaultAdapter();
		foreach($id as $id_alumno){
			$query = "UPDATE tb_alumno SET N_ASIGNADO = 0 WHERE N_DATOS_PERSONALES ='$id_alumno'";
			$db->query($query);
		}	
	}
}