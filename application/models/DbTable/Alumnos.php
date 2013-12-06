<?php

class Application_Model_DbTable_Alumnos extends Zend_Db_Table_Abstract {

    protected $_name = 'tbl_alumnos';

    public function consulta_general($empieza, $termina, $q) {
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $query_datos = "SELECT
tb_datos_generales.N_DATOS_PERSONALES,
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
tb_alumno.S_NOTA,
country.`Name`,
tbl_codigo_postal.codigo_postal,
tbl_codigo_postal.colonia,
tbl_codigo_postal.estado,
tbl_codigo_postal.municipio,
tbl_codigo_postal.localidad,
tb_tipo_pariente.S_DESCRIPCION AS PARENTESCO,
tb_parientes.N_ID_PARIENTE
FROM
tb_datos_generales ,
tb_alumno ,
country ,
tb_perfil ,
tbl_codigo_postal ,
tb_parientes ,
tb_tipo_pariente
WHERE
tb_datos_generales.N_ID_PERIFL = tb_alumno.N_ID_PERFIL AND
tb_datos_generales.N_ID_PAIS = country.`Code` AND
tb_datos_generales.N_ID_CP= tbl_codigo_postal.id_codigo_postal AND
tb_datos_generales.N_ID_PERIFL=tb_perfil.N_ID_PERFIL AND
tb_alumno.N_DATOS_PERSONALES = tb_datos_generales.N_DATOS_PERSONALES AND
tb_alumno.N_ID_PARIENTE = tb_parientes.N_ID_PARIENTE AND
tb_parientes.N_ID_TIPO_PARIENTE = tb_tipo_pariente.N_ID_TIPO_PARIENTE AND
tb_perfil.N_ID_PERFIL=1 AND
tb_datos_generales.S_NOMBRE LIKE '$q%'
LIMIT $empieza,$termina";
        $filas = $db->fetchAll($query_datos);		 
		

        $query_cuenta = "SELECT COUNT(*) as total
                        FROM
tb_datos_generales ,
tb_alumno ,
country ,
tb_perfil ,
tbl_codigo_postal ,
tb_parientes ,
tb_tipo_pariente
WHERE
tb_datos_generales.N_ID_PERIFL = tb_alumno.N_ID_PERFIL AND 
tb_datos_generales.N_ID_PAIS = country.`Code` AND
tb_datos_generales.N_ID_CP= tbl_codigo_postal.id_codigo_postal AND
tb_datos_generales.N_ID_PERIFL=tb_perfil.N_ID_PERFIL AND
tb_alumno.N_DATOS_PERSONALES = tb_datos_generales.N_DATOS_PERSONALES AND
tb_alumno.N_ID_PARIENTE = tb_parientes.N_ID_PARIENTE AND
tb_parientes.N_ID_TIPO_PARIENTE = tb_tipo_pariente.N_ID_TIPO_PARIENTE AND
tb_perfil.N_ID_PERFIL=1 AND
tb_datos_generales.S_NOMBRE LIKE '$q%'";
        $total = $db->fetchAll($query_cuenta);

        return $arreglo = array("total" => $total[0]['total'], "rows" =>$filas);
    }

    public function consulta_pais() {
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $query_all = 'SELECT Code, Name FROM country';
        $filas_all = $db->fetchAll($query_all);
        return $filas_all;
    }

    public function consulta_grupo() {
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $query = 'SELECT N_ID_GRUPO, S_GRUPO FROM TB_GRUPO WHERE N_ID_ESTATUS = 1';
        $filas = $db->fetchAll($query);
        return $filas;
    }   

    public function BuscaCP($codigo_postal) {
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $query_total = "SELECT COUNT(*) AS total FROM tbl_codigo_postal WHERE codigo_postal = '$codigo_postal'";
        $fila_total = $db->fetchRow($query_total);
        if ($fila_total['total'] > 1) {
            $query_colonia = "SELECT id_codigo_postal,colonia FROM tbl_codigo_postal WHERE codigo_postal = '$codigo_postal'";
            $fila_colonia = $db->fetchAll($query_colonia);
            $query = "select estado, municipio, localidad from tbl_codigo_postal WHERE codigo_postal= '$codigo_postal' GROUP BY codigo_postal";
            $fila = $db->fetchAll($query);
            return array($fila_colonia, $fila, true);
        } else {
            $query = "select id_codigo_postal,colonia, estado, municipio, localidad from tbl_codigo_postal WHERE codigo_postal= '$codigo_postal'";
            $fila = $db->fetchAll($query);
            return array($fila, false, false);
        }
    }

    public function guarda($N_ID_PERIFL, $S_NOMBRE, $S_CALLE, $S_NUMERO_INTERIOR, $S_NUMERO_EXTERIOR, $N_ID_CP, $N_ID_PAIS, $N_ID_ESTATUS, $D_FECHA_NACIMIENTO, $D_FECHA_INGRESO, $S_MATRICULA, $S_CURP, $S_FOTO, $S_CORREO, $S_USUARIO, $S_CONTRASENA, $S_SEXO, $S_NOTA, $BANDERA, $N_DATOS_GENERALES, $TIPO, $GRADO_ESTUDIO,$SUELDO,$NIVEL_PUESTO,$LUGAR_TRABAJO,$SUELDO,$PUESTO,$ID_PARIENTE,$ID_PADRE) {
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $SP = "CALL guarda_alumnos('$N_ID_PERIFL','$S_NOMBRE','$S_CALLE','$S_NUMERO_INTERIOR','$S_NUMERO_EXTERIOR','$N_ID_CP','$N_ID_PAIS','$N_ID_ESTATUS','$D_FECHA_NACIMIENTO','$D_FECHA_INGRESO','$S_MATRICULA','$S_CURP','$S_FOTO','$S_CORREO','$S_USUARIO','$S_CONTRASENA','$S_SEXO','$S_NOTA','$BANDERA','$N_DATOS_GENERALES','$TIPO','$GRADO_ESTUDIO','$SUELDO','$NIVEL_PUESTO','$LUGAR_TRABAJO','$SUELDO','$PUESTO','$ID_PARIENTE','$ID_PADRE')";
        $ejecuta = $db->fetchAll($SP);
        return $ejecuta[0]['resultado'];
    }

    public function elimina($N_DATOS_GENERALES,$nombre_tabla) {
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $sp = "CALL elimina_alumno('$N_DATOS_GENERALES','$nombre_tabla')";
        $ejecuta = $db->fetchAll($sp);
        return $ejecuta[0]['resultado'];
    }

    public function importa_a_excel($datos) {
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $sql = "";
        $sql2 = "";
        foreach ($datos['tb_datos_generales'] as $key => $value) {
            $sql.="insert into tb_datos_generales values(";
            foreach ($value as $key2 => $value2) {
                if ($key2 == 'S_SEXO') {
                    $sql.="'$value2'";
                } else {
                    $sql.="'$value2',";
                }
            }
            $sql.=');';
        }

        foreach ($datos['tb_alumno'] as $key => $value) {
            $sql2.="insert into tb_alumno values(";
            foreach ($value as $key2 => $value2) {
                if ($key2 == 'N_TURNO') {
                    $sql2.="'$value2'";
                } else {
                    $sql2.="'$value2',";
                }
            }
            $sql2.=');';
        }
        $inserts = $sql . $sql2;
        $ejecuta = $db->query($inserts);
    }
    
    public function campos_tablas($nombre_tabla){
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $query="SHOW COLUMNS FROM $nombre_tabla";
        $ejecuta = $db->fetchAll($query);
        return $ejecuta;        
    }
    
    public function obtiene_datos_excel($tabla,$perfil){
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        if($tabla == 'tb_datos_generales'){
            $query ="SELECT * FROM $tabla WHERE N_ID_PERIFL = $perfil" ;
        }else{
            $query ="SELECT * FROM $tabla" ;
        }
        $ejecuta = $db->fetchAll($query);
        return $ejecuta;
    }
	
	public function consulta_padres(){
		$db = Zend_Db_Table_Abstract::getDefaultAdapter();
		$sql="SELECT tb_parientes.N_ID_PARIENTE AS id,tb_datos_generales.S_NOMBRE AS nombre
			  FROM tb_parientes ,tb_datos_generales
			  WHERE tb_parientes.N_DATOS_PERSONALES = tb_datos_generales.N_DATOS_PERSONALES";
		$filas = $db->fetchAll($sql);		
		return $filas;
	}
	
	public function consulta_pariente(){
		$db = Zend_Db_Table_Abstract::getDefaultAdapter();
		$sql="SELECT N_ID_TIPO_PARIENTE AS id, S_DESCRIPCION as tipo FROM tb_tipo_pariente";
		$filas = $db->fetchAll($sql);		
		return $filas;
	}

}