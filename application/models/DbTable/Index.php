<?php 
class Application_Model_DbTable_Index extends Zend_Db_Table_Abstract
{
	public function obtiene_perfil($usuario,$contrasena)
	{
		$db = Zend_Db_Table_Abstract::getDefaultAdapter();
		$query = "SELECT N_ID_PERIFL AS perfil FROM tb_datos_generales WHERE S_USUARIO = '$usuario' AND S_CONTRASENA = '$contrasena'";
		$filas = $db->fetchAll($query);
		return $filas;
	}
}
?>