<?php

class Application_Model_DbTable_Profesores extends Zend_Db_Table_Abstract {

    public function consulta_general($empieza, $termina, $q) {
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $query_datos = "SELECT
                        tb_profesor.N_DATOS_PERSONALES,
                        tb_profesor.N_SUELDO,
                        tb_profesor.S_NOTA,
                        tb_profesor.N_ID_PROFESOR,                        
                        tb_datos_generales.N_ID_PERIFL,
                        tb_datos_generales.S_NOMBRE,
                        tb_datos_generales.S_CALLE,
                        tb_datos_generales.S_NUMERO_INTERIOR,
                        tb_datos_generales.S_NUMERO_EXTERIOR,
                        tb_datos_generales.N_ID_CP AS id_codigo_postal,
                        tb_datos_generales.N_ID_PAIS,
                        tb_datos_generales.N_ID_ESTATUS,
                        tb_datos_generales.D_FECHA_NACIMIENTO,
                        tb_datos_generales.D_FECHA_INGRESO,
                        tb_datos_generales.S_MATRICULA,
                        tb_datos_generales.S_CURP,
                        tb_datos_generales.S_FOTO,
                        tb_datos_generales.S_CORREO,
                        tb_datos_generales.S_USUARIO,
                        tb_datos_generales.S_CONTRASENA,
                        tb_datos_generales.S_SEXO,
                        tb_grado_estudio.N_ID_GRADO_ESTUDIO,
                        tb_grado_estudio.S_DESCRIPCION,
                        tb_puesto.N_ID_NIVEL_PUESTO,
                        tb_puesto.S_PUESTO,
                        tbl_codigo_postal.codigo_postal,
                        tbl_codigo_postal.colonia,
                        tbl_codigo_postal.estado,
                        tbl_codigo_postal.municipio,
                        tbl_codigo_postal.localidad,
                        country.`Name`,
                        tb_perfil.S_PERFIL
                        FROM
                        tb_profesor ,
                        tb_datos_generales ,
                        tb_grado_estudio ,
                        tb_puesto ,
                        tbl_codigo_postal ,
                        country ,
                        tb_perfil
                        WHERE tb_profesor.N_DATOS_PERSONALES = tb_datos_generales.N_DATOS_PERSONALES AND
                        tb_profesor.N_ID_GRADO_ESTUDIO = tb_grado_estudio.N_ID_GRADO_ESTUDIO AND
                        tb_profesor.N_ID_NIVEL_PUESTO = tb_puesto.N_ID_NIVEL_PUESTO AND
                        tb_datos_generales.N_ID_CP = tbl_codigo_postal.id_codigo_postal AND
                        tb_datos_generales.N_ID_PAIS = country.`Code` AND
                        tb_datos_generales.N_ID_PERIFL = tb_perfil.N_ID_PERFIL AND
                        tb_perfil.N_ID_PERFIL=2 AND
                        tb_datos_generales.S_NOMBRE LIKE '$q%'
                        LIMIT $empieza,$termina";
        $filas = $db->fetchAll($query_datos);


        $query_cuenta = "SELECT
                        COUNT(*) AS total
                        FROM
                        tb_profesor ,
                        tb_datos_generales ,
                        tb_grado_estudio ,
                        tb_puesto ,
                        tbl_codigo_postal ,
                        country ,
                        tb_perfil
                        WHERE tb_profesor.N_DATOS_PERSONALES = tb_datos_generales.N_DATOS_PERSONALES AND
                        tb_profesor.N_ID_GRADO_ESTUDIO = tb_grado_estudio.N_ID_GRADO_ESTUDIO AND
                        tb_profesor.N_ID_NIVEL_PUESTO = tb_puesto.N_ID_NIVEL_PUESTO AND
                        tb_datos_generales.N_ID_CP = tbl_codigo_postal.id_codigo_postal AND
                        tb_datos_generales.N_ID_PAIS = country.`Code` AND
                        tb_datos_generales.N_ID_PERIFL = tb_perfil.N_ID_PERFIL AND
                        tb_perfil.N_ID_PERFIL=2 AND
                        tb_datos_generales.S_NOMBRE LIKE '$q%'";
        $total = $db->fetchAll($query_cuenta);

        return $arreglo = array("total" => $total[0]['total'], "rows" => $filas);
    }
	
	public function consulta_estudio(){
		$db = Zend_Db_Table_Abstract::getDefaultAdapter();
		$stmt= 'SELECT N_ID_GRADO_ESTUDIO AS ID, S_DESCRIPCION AS ESTUDIO FROM tb_grado_estudio';
		$filas = $db->fetchAll($stmt);
		return $filas;
	}
	
	public function consulta_puesto(){
		$db = Zend_Db_Table_Abstract::getDefaultAdapter();
		$stmt= 'SELECT N_ID_NIVEL_PUESTO AS ID, S_PUESTO AS PUESTO FROM tb_puesto';
		$filas = $db->fetchAll($stmt);
		return $filas;
	}
	
	public function obtener_id($id_datos_personales){
		$db = Zend_Db_Table_Abstract::getDefaultAdapter();
		$sql="select N_ID_PROFESOR AS id_profesor FROM tb_profesor WHERE N_DATOS_PERSONALES = $id_datos_personales";
		$filas = $db->fetchAll($sql);		
		return $filas[0]['id_profesor'];
	}
	

}

?>
