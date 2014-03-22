<?php
class Application_Model_DbTable_CHP extends Zend_Db_Table_Abstract {
	public function carga_grupos(){
	    $db = Zend_Db_Table_Abstract::getDefaultAdapter();
	    $sql = "select * from tb_grupo";
	    $ejecuta = $db->fetchAll($sql);
	    return $ejecuta;	    
	}
	public function consulta_materias($id_grupo){
	    $db = Zend_Db_Table_Abstract::getDefaultAdapter();
	    $sql = "SELECT
                tb_grupo.S_GRUPO AS grupo,
                tb_horas.hora,
                tb_materias.materia,
                tb_datos_generales.S_NOMBRE AS nombre_profesor,
                tb_apg.id_materia,
                tb_apg.id_profesor,
                tb_apg.id_hora,
                tb_apg.dia
                FROM
                tb_apg ,
                tb_grupo ,
                tb_horas ,
                tb_materias ,
                tb_profesor ,
                tb_datos_generales
                WHERE
                tb_apg.id_grupo = tb_grupo.N_ID_GRUPO
                AND tb_apg.id_hora = tb_horas.id_hora
                AND tb_apg.id_materia = tb_materias.id
                AND tb_apg.id_profesor = tb_profesor.N_DATOS_PERSONALES
                AND tb_profesor.N_DATOS_PERSONALES = tb_datos_generales.N_DATOS_PERSONALES
                AND tb_apg.id_grupo = $id_grupo";
	    $ejecuta = $db->fetchAll($sql);
	    return $ejecuta;
	}
	public function carga_horas(){
	    $db = Zend_Db_Table_Abstract::getDefaultAdapter();
	    $sql = "SELECT * FROM tb_horas";
	    $ejecuta = $db->fetchAll($sql);
	    return $ejecuta;
	}
}