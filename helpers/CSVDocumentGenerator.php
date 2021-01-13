<?php

namespace app\helpers;

use Yii;

class CSVDocumentGenerator
{

    public function createFile(array $data, string $fileName, bool $attachment = false)
    {
        $storagePath = Yii::getAlias('@app') . '/web/storage/';
        $fullFilePath = $storagePath . $fileName;

//        chmod($storagePath, 0777);
//        chmod($fullFilePath, 0777);
        if (file_exists($fullFilePath)) {
            unlink($fullFilePath);
        }

        if($attachment) {
            header( 'Content-Type: text/csv' );
            header( 'Content-Disposition: attachment;filename='.$fileName);
            $fp = fopen('php://output', 'w');
        } else {
            $fp = fopen($fullFilePath, 'w');
        }

        foreach ($data as $row) {
            array_walk($row, array($this, 'encodeCSV'));
            fputcsv($fp, $row, ';');
        }

        fclose($fp);

        return 'storage/'.$fileName;
    }

    protected function encodeCSV(&$value, $key){
        $value = iconv('UTF-8',"windows-1251//TRANSLIT",$value);
    }
}
