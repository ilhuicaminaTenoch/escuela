<?php
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap{

      protected function _initAutoload(){
           $moduleLoader = new Zend_Application_Module_Autoloader(array('namespace' => '', 'basePath' => APPLICATION_PATH));
           return $moduleLoader;
      }
	protected function _initViewHelpers(){
		$this->bootstrap('layout');
		$layout = $this->getResource('layout');		
		$view = $layout->getView();
		$view->setEncoding('UTF-8');
		$view->doctype('HTML5');
		$view->headMeta()->appendHttpEquiv('Content-Type', 'text/html;charset=utf-8');
		$view->headMeta()->appendHttpEquiv('Cache-Control', 'no-cache');
		$view->headTitle()->setSeparator(' - ');
		$view->headTitle('ESCUELA');
		Zend_Session::start();
	}
	protected function _initConfig() {
        $config = new Zend_Config_Ini(APPLICATION_PATH.'/configs/application.ini');
        Zend_Registry::set('config', $config->get(APPLICATION_ENV));
    }

}
