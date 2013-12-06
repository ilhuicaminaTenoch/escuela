<?php
/**
 * Description of Excel
 *
 * @author manuel.moreno
 */
require_once('../library/phpExcel/Classes/PHPExcel.php');
require_once('../library/phpExcel/Classes/PHPExcel/Reader/Excel2007.php');
include '../library/phpExcel/Classes/PHPExcel/IOFactory.php';
class Application_Model_Excel{
    public function importa_excel($fileWithPath){       
        if(file_exists($fileWithPath)) {            
            $objReader = new PHPExcel_Reader_Excel2007();
            $objPHPExcel = $objReader->load($fileWithPath);
            //se obtienen las hojas, el nombre de las hojas y se pone activa la primera hoja
            $total_sheets = $objPHPExcel->getSheetCount();
            $nombre_hojas = $objPHPExcel->getSheetNames();
            
            $contenedor_array = array();                       
            foreach ($nombre_hojas as $indice => $valor) {
                $contenedor_array[$valor] = array();
                $objWorksheet = $objPHPExcel->setActiveSheetIndex($indice);
                $highestRow = $objWorksheet->getHighestRow();
                $highestColumn = $objWorksheet->getHighestColumn();
                $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
                $headingsArray = $objWorksheet->rangeToArray('A1:' . $highestColumn . '1', null, true, true, true);
                $headingsArray = $headingsArray[1];
                $r = -1;
                $array=array();               
                for ($row = 2; $row <= $highestRow; ++$row) {
                    $dataRow = $objWorksheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, null, true, true, true);
                    if ((isset($dataRow[$row]['A'])) && ($dataRow[$row]['A'] > '')) {
                        ++$r;
                        foreach ($headingsArray as $columnKey => $columnHeading) {
                            $contenedor_array[$valor][$r][$columnHeading] = $dataRow[$row][$columnKey];
                        }
                    }
                }
            }            
            return $contenedor_array;
        }else{
            //regresa 8 para validar que se podujo un error (no se encontro el archivo)
            return '8';
        }
    }
    
    public function exportar_excel($array_hojas,$titulo_excel,$perfil) {
        //echo'<pre>'; print_r($array_hojas); echo '</pre>';
        $objPHPExcel = new PHPExcel(); 
        $objPHPExcel->getProperties()->setTitle("Exportar")->setDescription("none");
        $hoja_creada = 1;       
        foreach($array_hojas as $numero_de_hoja=>$nombre_de_hoja){
            $objPHPExcel->getSheet($numero_de_hoja)->setTitle($nombre_de_hoja);
            $objPHPExcel->createSheet($hoja_creada++);
            $objPHPExcel->setActiveSheetIndex($numero_de_hoja);
            $modelo = new Application_Model_DbTable_Alumnos();
            $campos = $modelo->campos_tablas($nombre_de_hoja);
            $datos_de_tablas = $modelo->obtiene_datos_excel($nombre_de_hoja,$perfil);
            
            $col = 0;
            //PINTO NOMBRE DE LOS CAMPOS
            foreach ($campos as $llave_campo => $valor_campo) {
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, 1, $valor_campo['Field']);
                //echo "Columna: $col Fila: 1 Valor: {$valor_campo['Field']}<br>";
                $col++;
            }
            //Obtención de los datos de la tabla       
            foreach ($datos_de_tablas as $fila => $valor_fila){
                $col=0;
                $fila = $fila + 2;
                foreach ($valor_fila as $key_dato => $valor_dato){
                    //echo "Columna: $col Fila: $fila Valor: $valor_dato<br>";
                    $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $fila, $valor_dato);                   
                    $col++;      
                }             
            }
        }
        $objPHPExcel->setActiveSheetIndex(0);        
         // Se modifican los encabezados del HTTP para indicar que se envia un archivo de Excel.
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment;filename='$titulo_excel.xlsx'");
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        exit;
    }
	
	public function descarga_excel(){		
		// Read the template file
		$inputFileType = 'Excel5';
		$inputFileName = 'prueba.xls';
		$objReader = PHPExcel_IOFactory::createReader($inputFileType);
		$objPHPExcel = $objReader->load($inputFileName);
		$objWorksheet = $objPHPExcel->setActiveSheetIndex(1);
		// Add your new data to the template
		//$objPHPExcel->getActiveSheet()->insertNewRowBefore(1,1);

		$objPHPExcel->getActiveSheet()->setCellValue('E21', 'ISBN 962-571-8926');
		/*$objPHPExcel->getActiveSheet()->setCellValue('B3', 'Excel for dummies');
		$objPHPExcel->getActiveSheet()->setCellValue('C3', 17.99);
		$objPHPExcel->getActiveSheet()->setCellValue('D3', 2);
		$objPHPExcel->getActiveSheet()->setCellValue('E3', '=C4*D4');*/
		 
		// Write out as the new file
		$outputFileType = 'Excel5';
		$outputFileName = 'myInvoice.xls';
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, $outputFileType);
		$objWriter->save(str_replace('.php', '.xls', __FILE__));
		
		
	}
}

?>
