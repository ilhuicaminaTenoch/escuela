<?php

/**
 * {0}
 * 
 * @author Jose Manuel Moreno Plaza
 * @version 
 */
class Application_Model_DbTable_Circulares extends Zend_Db_Table_Abstract {
    public function todos($idCombo){
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $query = "SELECT
                    tb_agp.N_ID_GRUPO AS id_grupo                   
                    FROM
                    tb_agp ,
                    tb_grupo ,
                    tb_profesor ,
                    tb_datos_generales
                    WHERE
                    tb_agp.N_ID_GRUPO = tb_grupo.N_ID_GRUPO
                    AND tb_agp.N_ID_PROFESOR = tb_profesor.N_ID_PROFESOR
                    AND tb_profesor.N_DATOS_PERSONALES = tb_datos_generales.N_DATOS_PERSONALES
                    AND tb_datos_generales.N_DATOS_PERSONALES = $idCombo";
        $filas = $db->fetchAll($query);
        $cadenaGrupo = "";
        foreach ($filas as $grupo){
        	$cadenaGrupo.="{$grupo['id_grupo']},";
        }
        $limpia = trim($cadenaGrupo,',');
        return  $limpia;
    }
    public function todo_los_alumnos($grupos,$id_profesor,$empieza,$temina){
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $sql = "SELECT              
                tb_datos_generales.S_NOMBRE as nombre,
                tb_datos_generales.S_CORREO AS email,
                tb_alumno.ID_ALUMNO AS id_alumno             
                FROM
                tb_alumno ,
                tb_datos_generales ,
                tb_grupo,
                tb_agp
                WHERE
                tb_alumno.N_ASIGNADO = tb_grupo.N_ID_GRUPO
                AND tb_alumno.N_DATOS_PERSONALES = tb_datos_generales.N_DATOS_PERSONALES
                AND tb_agp.N_ID_GRUPO = tb_alumno.N_ASIGNADO
                AND tb_grupo.N_ID_GRUPO IN ($grupos) AND tb_agp.N_ID_PROFESOR = $id_profesor
                LIMIT $empieza,$temina";
        $filas = $db->fetchAll($sql);
        return $filas;        
    }
    public function id_profesor($id_datos_generales){
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $sql = "SELECT tb_profesor.N_ID_PROFESOR FROM tb_profesor WHERE tb_profesor.N_DATOS_PERSONALES = $id_datos_generales";
        $fila = $db->fetchAll($sql);
        $id_profesor = $fila[0]['N_ID_PROFESOR'];
        return $id_profesor;
    }
    public function guarda_circulares($tipo,$idMateria,$idGrupo,$alumnos,$id_datos_generales,$nota){
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $sql = "";       
        $sql.= "insert into tb_circulares(id_tipo,id_materia,id_grupo,id_alumno,id_datos_generales,nota,fecha_hora_envio) values";
        $arreglo = explode(',', $alumnos);
        //echo"<pre>"; print_r($arreglo); echo"</pre>";        
        foreach ($arreglo as $value) {              
            $separa = explode('-', $value);
            $id_alumno = $separa[0];
            $email = $separa[1];
            $sql.= "($tipo,$idMateria,$idGrupo,$id_alumno,$id_datos_generales,'$nota',now()),";
        }
        $limpia = trim($sql,',');
        $db->getConnection()->query($limpia);
                
        
    }
    public function manda_mensaje($array_correo,$correo_remitente,$nombre_remitente,$asunto_mensaje,$mensaje_html,$contrasena_correo){
        //Configuración SMTP
        $host = 'smtp.gmail.com';
        $param = array(
        		'auth' => 'login',
        		'ssl' => 'ssl',
        		'port' => '465',
        		'username' => "$correo_remitente",
        		'password' => "$contrasena_correo"
        );
        $tr = new Zend_Mail_Transport_Smtp($host, $param);
        Zend_Mail::setDefaultTransport($tr);
        $arreglo = explode(',', $array_correo);
        //echo"<pre>"; print_r($arreglo); echo"</pre>";
        //Creamos email
        $mail = new Zend_Mail();
        $mail->setFrom($correo_remitente, $nombre_remitente)
             ->setSubject($asunto_mensaje)
             ->setBodyHtml($mensaje_html);
        foreach ($arreglo as $correo) {
            $separa = explode('-', $correo);
            $direccion_correo = $separa[1];           
            $mail->addTo($direccion_correo);           
                   
        }
        try {
        	$mail->send();
        	return 'ok';
        }
        catch (Exception $e) {
        	$sent = false;
        	return $e;
        }
        
        
    }
    
    public function cargar_grid_notas(){
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $sql = "";
    }
    
    public function aviso(){
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $sql ="select * from tb_avisos";
        $filas = $db->fetchAll($sql);
        return $filas;
    }
    
    public function perfiles(){
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $sql = "select N_ID_PERFIL AS id_materia, S_PERFIL AS nombre_materia from tb_perfil";
        $filas = $db->fetchAll($sql);
        return $filas;
    }
    
    public function contenido_perfil($id,$empieza,$termina,$q){
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();       
        if ($id == 0 || $id == '' || $id == '0') {
            $arreglo = array();
        }else{
            switch (intval($id)) {
            	case 1://alumnos
            	   $sql = "SELECT
                           tb_datos_generales.S_NOMBRE AS nombre,
                           tb_perfil.N_ID_PERFIL as perfil,
                           tb_alumno.ID_ALUMNO AS id_alumno,
            	           tb_datos_generales.S_CORREO as email
                           FROM
                           tb_datos_generales ,
                           tb_perfil ,
                           tb_alumno
                           WHERE tb_datos_generales.N_DATOS_PERSONALES = tb_alumno.N_DATOS_PERSONALES
                           AND tb_datos_generales.N_ID_PERIFL = tb_perfil.N_ID_PERFIL
            	           LIMIT $empieza,$termina";
            	   $sql_cuenta = "SELECT COUNT(*) AS total
            	                  FROM
                                  tb_datos_generales ,
                                  tb_perfil ,
                                  tb_alumno
                                  WHERE tb_datos_generales.N_DATOS_PERSONALES = tb_alumno.N_DATOS_PERSONALES
                                  AND tb_datos_generales.N_ID_PERIFL = tb_perfil.N_ID_PERFIL";        	   
            	break;
            	
            	case 2://profesor
            	    $sql = "SELECT
                            tb_datos_generales.S_NOMBRE AS nombre,
                            tb_perfil.N_ID_PERFIL AS perfil,
                            tb_profesor.N_ID_PROFESOR AS id_alumno,
            	            tb_datos_generales.S_CORREO as email
                            FROM
                            tb_datos_generales ,
                            tb_perfil ,
                            tb_profesor
                            WHERE
                            tb_datos_generales.N_ID_PERIFL = tb_perfil.N_ID_PERFIL 
                            AND tb_datos_generales.N_DATOS_PERSONALES = tb_profesor.N_DATOS_PERSONALES
            	            LIMIT $empieza,$termina";
            	    $sql_cuenta = "SELECT COUNT(*) AS total                        
                                  FROM
                                  tb_datos_generales ,
                                  tb_perfil ,
                                  tb_profesor
                                  WHERE
                                  tb_datos_generales.N_ID_PERIFL = tb_perfil.N_ID_PERFIL 
                                  AND tb_datos_generales.N_DATOS_PERSONALES = tb_profesor.N_DATOS_PERSONALES";        	    
            	    break;        	
            }
            $total = $db->fetchAll($sql_cuenta);
            $filas = $db->fetchAll($sql);
            $arreglo = array("total" => $total[0]['total'], "rows" =>$filas);           
        }      
        
        return $arreglo;
    }
    
    public function carga_avisos_enviados($perfil,$id_datos_generales){
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $sql = "SELECT
                tb_circulares.nota,
                DATE_FORMAT(tb_circulares.fecha_hora_envio,'%d %b %Y a las %T') AS fecha_envio,
                tb_avisos.tipo,
                tb_materias.materia,
                tb_datos_generales.S_NOMBRE,
                tb_datos_generales.N_ID_PERIFL,
                tb_perfil.S_PERFIL,
                tb_circulares.id_circular,
                tb_grupo.S_GRUPO
                FROM
                tb_circulares ,
                tb_avisos ,
                tb_materias ,
                tb_alumno ,
                tb_datos_generales ,
                tb_perfil ,
                tb_grupo
                WHERE tb_circulares.id_alumno = tb_alumno.ID_ALUMNO
                AND tb_alumno.N_DATOS_PERSONALES = tb_datos_generales.N_DATOS_PERSONALES
                AND tb_circulares.id_materia = tb_materias.id
                AND tb_circulares.id_tipo = tb_avisos.id_tipo
                AND tb_perfil.N_ID_PERFIL = tb_datos_generales.N_ID_PERIFL
                AND tb_circulares.id_grupo = tb_grupo.N_ID_GRUPO
                AND tb_circulares.id_datos_generales = $id_datos_generales";
        
        $sql_cuenta = "SELECT COUNT(*) as total
                        FROM
                        tb_circulares ,
                        tb_avisos ,
                        tb_materias ,
        				tb_alumno,
                        tb_datos_generales ,
                        tb_perfil
                        WHERE tb_circulares.id_alumno = tb_alumno.ID_ALUMNO
                        AND tb_alumno.N_DATOS_PERSONALES = tb_datos_generales.N_DATOS_PERSONALES
                        AND tb_circulares.id_materia = tb_materias.id
                        AND tb_circulares.id_tipo = tb_avisos.id_tipo
                        AND tb_perfil.N_ID_PERFIL = tb_datos_generales.N_ID_PERIFL
                        AND tb_circulares.id_datos_generales = $id_datos_generales";
        
        $total = $db->fetchAll($sql_cuenta);
        $filas = $db->fetchAll($sql);
        $arreglo = array("total" => $total[0]['total'], "rows" =>$filas);
        return $arreglo;
    }
    
    public function carga_avisos_enviados_profesores($id_datos_generales){
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $sql = "SELECT
                tb_circulares.nota,
                DATE_FORMAT(tb_circulares.fecha_hora_envio,'%d %b %Y a las %T') AS fecha_envio,
                tb_avisos.tipo,
                tb_materias.materia,
                tb_datos_generales.S_NOMBRE,
                tb_datos_generales.N_ID_PERIFL,
                tb_perfil.S_PERFIL,
                tb_circulares.id_circular
                FROM
                tb_circulares ,
                tb_avisos ,
                tb_materias ,
                tb_profesor ,
                tb_datos_generales ,
                tb_perfil
                WHERE tb_circulares.id_alumno = tb_profesor.N_ID_PROFESOR
                AND tb_profesor.N_DATOS_PERSONALES = tb_datos_generales.N_DATOS_PERSONALES
                AND tb_circulares.id_materia = tb_materias.id
                AND tb_circulares.id_tipo = tb_avisos.id_tipo
                AND tb_perfil.N_ID_PERFIL = tb_datos_generales.N_ID_PERIFL
                AND tb_circulares.id_datos_generales = $id_datos_generales";
        
        $sql_cuenta = "SELECT COUNT(*) AS total                
                FROM
                tb_circulares ,
                tb_avisos ,
                tb_materias ,
                tb_profesor ,
                tb_datos_generales ,
                tb_perfil
                WHERE tb_circulares.id_alumno = tb_profesor.N_ID_PROFESOR
                AND tb_profesor.N_DATOS_PERSONALES = tb_datos_generales.N_DATOS_PERSONALES
                AND tb_circulares.id_materia = tb_materias.id
                AND tb_circulares.id_tipo = tb_avisos.id_tipo
                AND tb_perfil.N_ID_PERFIL = tb_datos_generales.N_ID_PERIFL
                AND tb_circulares.id_datos_generales = $id_datos_generales";
        $total = $db->fetchAll($sql_cuenta);
        $filas = $db->fetchAll($sql);
        $arreglo = array("total" => $total[0]['total'], "rows" =>$filas);
        return $arreglo;
        
    }
    public function filter_tipo($array_avisos){
        $total_elemetos = count($array_avisos);
        $contador = 1;
        $data = "";
        $data.="[";
        $data.="{";
        $data.="value:'',";
        $data.="text:'Todos'";
        $data.="},";
        foreach ($array_avisos as $value) {
            $data.="{";
            $data.="value:'{$value['tipo']}',";
            $data.="text:'{$value['tipo']}'";
            if ($total_elemetos == $contador ) {
                $data.="}";
            }else{
                $data.="},";
            }            
            $contador++;
        }
        $data.="]";
        return $data;
    }
    
    public function consulta_grupos(){
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $sql ="select N_ID_GRUPO as id_tipo, S_GRUPO AS tipo from tb_grupo";
        $filas = $db->fetchAll($sql);
        return $filas;
    }
}