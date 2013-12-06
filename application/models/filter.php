<?php

class Application_Model_Filter extends Zend_Controller_Action {
	public $filter;
	public $entityFilter;	
    public function init(){
        
    	$this->entityFilter = new Zend_Filter_HtmlEntities();
    	$this->filter = new Application_Model_InputFilter();		
    }
    
    public function sql_command($cadena){    
		$minusculas = strtolower($cadena);
		$search = array('select', 'from', ' or ','where', ' and ', 'copy', 'like', ' inner ', 'delete', 'insert', 'update',' union ','drop','truncate');
		$replace ="";
		return str_replace($search, $replace, $minusculas);	
    }
    
    public function normaliza_date($date,$separador){
		if(!empty($date)){
			$var = explode($separador,$date);
			return "$var[2]-$var[1]-$var[0]";
		}
    }
    
    public function normaliza_date_user($date,$separador){
		if(!empty($date)){
				$var = explode($separador,$date);
				 return "$var[0]-$var[1]-$var[2]";
		}
    }
	
	public function _process( $username, $password )    {       
        $adapter = $this->_getAuthAdapter();
        $adapter->setIdentity($username);
        $adapter->setCredential($password);

        $auth = Zend_Auth::getInstance();
        $result = $auth->authenticate($adapter);
        if ($result->isValid()) {
            $user = $adapter->getResultRowObject(array('N_DATOS_PERSONALES', 'S_NOMBRE'));
            $auth->getStorage()->write($user);			           
			return true;
        }else{
			return false;
		}
    }
	
	protected function _getAuthAdapter()
	{
		$dbAdapter = Zend_Db_Table::getDefaultAdapter();
		$authAdapter = new Zend_Auth_Adapter_DbTable($dbAdapter);

		$authAdapter->setTableName('tb_datos_generales')
		->setIdentityColumn('S_USUARIO')
		->setCredentialColumn('S_CONTRASENA')
		->setCredentialTreatment('MD5(?)'); 
		return $authAdapter;
	}	
	
	public function calculaedad($fechanacimiento){
		list($ano,$mes,$dia) = explode("-",$fechanacimiento);
		$ano_diferencia  = date("Y") - $ano;
		$mes_diferencia = date("m") - $mes;
		$dia_diferencia   = date("d") - $dia;
		if ($dia_diferencia < 0 || $mes_diferencia < 0)
			$ano_diferencia--;
		return $this->view->anios = $ano_diferencia;
	}
   
}