<?php

require_once dirname(__FILE__) .'/PHPExcel-1.8/Classes/PHPExcel/IOFactory.php';

class ExceltoCSV
{
    const UPLOAD_DIR = "uploads/";

    public function convert(
        $filename,
	$locationSwitch = 'master/',
        $sheetName = 'workspace',
        $format = array(
            'fileExt' => 'xlsx',
            'ExcelFormat' => 'Excel2007'
        )
    ) {
        try {
            $objReader = PHPExcel_IOFactory::createReader($format['ExcelFormat']);
            $objPHPExcelReader = $objReader->load(self::UPLOAD_DIR.$locationSwitch.$filename.'.' .$format['fileExt']);
        } catch (Exception $e) {
            return [false, "Error input file: " . $e->getMessage()];
        }

        $loadedSheetNames = $objPHPExcelReader->getSheetNames();

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcelReader, 'CSV');

        foreach($loadedSheetNames as $sheetIndex => $loadedSheetName) {

            //if (strpos(strtolower($loadedSheetName), strtolower($sheetName)) !== false) {
                $objWriter->setSheetIndex($sheetIndex);
                try {
                    $objWriter->save(self::UPLOAD_DIR .$locationSwitch . $filename . '.csv');
                } catch (Exception $e) {
                    return array(false, "Error output file: " . $e->getMessage());
                }
                //return [true, $filename];
            //}
        }

        return array(true, "pass");
    }
}
