<?php 
class Application_Model_DbTable_Index extends Zend_Db_Table_Abstract
{
	public function checa()
	{
		$db = Zend_Db_Table_Abstract::getDefaultAdapter();
		$query = "SELECT S_USUARIO AS usuario, S_CONTRASENA AS contrasena , COUNT(*) AS total FROM tb_datos_generales WHERE S_USUARIO = 'fer' AND S_CONTRASENA = 'feramra'";
		$filas = $db->fetchAll($query);
		return $filas;
	}
}
?>