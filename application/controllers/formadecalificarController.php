<?php 
	class formadecalificarController extends Application_Model_Filter {	
		public function indexAction(){
			$modelo = new Application_Model_DbTable_Formadecalificar();
			$filas = $modelo->carga_grid($id_materia,$id_grupo,$id_profesor);
			echo"<pre>"; print_r($filas); echo"</pre>";
		}
	}
?>