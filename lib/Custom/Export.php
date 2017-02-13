<?php
require_once ROOT_PATH. 'lib/PHPExcel.php';
class Custom_Export {
	public static function export($arr, $title) {
		//print_r($arr); exit;
		require_once ROOT_PATH . 'lib/PHPExcel.php';
		$objPHPExcel = new PHPExcel();
		$objPHPExcel->setActiveSheetIndex(0);
		
		$objPHPExcel->getActiveSheet()->setTitle('中奖名单');
		
		//$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(24);
		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(12);
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(18);
		$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
		
		$objPHPExcel->getActiveSheet()->setCellValue('A1', '手机号码');
		$objPHPExcel->getActiveSheet()->setCellValue('B1', '用户名');
		$objPHPExcel->getActiveSheet()->setCellValue('C1', '现金券内容');
		$objPHPExcel->getActiveSheet()->setCellValue('D1', '券ID');
		$i = 2;
		foreach($arr as $item) {
			$objPHPExcel->getActiveSheet()->setCellValue('A'.$i, $item['0']);
			$objPHPExcel->getActiveSheet()->setCellValue('B'.$i, $item['1']);
			$objPHPExcel->getActiveSheet()->setCellValue('C'.$i, $item['2']);
			$objPHPExcel->getActiveSheet()->setCellValue('D'.$i, $item['3']);
			$i++;
		}
		
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'. $title. '.xls"');
		header('Cache-Control: max-age=0');
		// If you're serving to IE 9, then the following may be needed
		header('Cache-Control: max-age=1');
		
		// If you're serving to IE over SSL, then the following may be needed
		header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
		header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
		header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
		header ('Pragma: public'); // HTTP/1.0
		
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save('php://output');
		exit;
		
	}
}