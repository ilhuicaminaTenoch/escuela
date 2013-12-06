<?php
class Application_Model_DbTable_Pariente extends Zend_Db_Table_Abstract {
	public function consulta_general($empieza, $termina, $q) {
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $query_datos = "SELECT
						tb_parientes.N_ID_PARIENTE,
						tb_parientes.N_DATOS_PERSONALES,
						tb_parientes.S_LUGAR_TRABAJO,
						tb_parientes.N_SUELDO,
						tb_parientes.S_PUESTO,
						tb_parientes.N_ID_TIPO_PARIENTE AS ID_PARIENTE,
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
						tb_datos_generales.S_CURP,
						tb_datos_generales.S_FOTO,
						tb_datos_generales.S_CORREO,
						tb_datos_generales.S_USUARIO,
						tb_datos_generales.S_CONTRASENA,
						tb_datos_generales.S_SEXO,
						tb_tipo_pariente.S_DESCRIPCION,
						tb_perfil.S_PERFIL,
						tbl_codigo_postal.codigo_postal,
						tbl_codigo_postal.colonia,
						tbl_codigo_postal.estado,
						tbl_codigo_postal.municipio,
						country.`Name`,
						tb_perfil.N_ID_PERFIL
						FROM
						tb_parientes ,
						tb_datos_generales ,
						tb_tipo_pariente ,
						tb_perfil ,
						tbl_codigo_postal ,
						country
						WHERE
						tb_parientes.N_DATOS_PERSONALES = tb_datos_generales.N_DATOS_PERSONALES AND
						tb_parientes.N_ID_TIPO_PARIENTE = tb_tipo_pariente.N_ID_TIPO_PARIENTE AND
						tb_datos_generales.N_ID_CP = tbl_codigo_postal.id_codigo_postal AND
						tb_datos_generales.N_ID_PAIS = country.`Code` AND
						tb_datos_generales.N_ID_PERIFL = tb_perfil.N_ID_PERFIL AND
						tb_perfil.N_ID_PERFIL = '3' AND
						tb_datos_generales.S_NOMBRE LIKE '$q%'
						LIMIT $empieza,$termina";
        $filas = $db->fetchAll($query_datos);


        $query_cuenta = "SELECT COUNT(*) AS total
						FROM
						tb_parientes ,
						tb_datos_generales ,
						tb_tipo_pariente ,
						tb_perfil ,
						tbl_codigo_postal ,
						country
						WHERE
						tb_parientes.N_DATOS_PERSONALES = tb_datos_generales.N_DATOS_PERSONALES AND
						tb_parientes.N_ID_TIPO_PARIENTE = tb_tipo_pariente.N_ID_TIPO_PARIENTE AND
						tb_datos_generales.N_ID_CP = tbl_codigo_postal.id_codigo_postal AND
						tb_datos_generales.N_ID_PAIS = country.`Code` AND
						tb_datos_generales.N_ID_PERIFL = tb_perfil.N_ID_PERFIL AND
						tb_perfil.N_ID_PERFIL = '3' AND
						tb_datos_generales.S_NOMBRE LIKE '$q%'";
        $total = $db->fetchAll($query_cuenta);
        return $arreglo = array("total" => $total[0]['total'], "rows" => $filas);
    }	
	
}

?>