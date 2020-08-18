<?php
namespace app\repositories;

use app\models\FinanceDocuments;

class FinanceDocumentsRep extends FinanceDocuments
{
    public static function existByParams($ownCompanyId, $documentType, $date, $documentCode, $exceptedId = null)
    {
        $item = self::findOne([
            'own_company_id' => $ownCompanyId,
            'document_type_id' => $documentType,
            'date' => $date,
            'document_code' => $documentCode,
        ]);
        return ($item !== null && $item->id != $exceptedId);
    }

    public static function existByDocumentCode($documentCode, $exceptedId = null)
    {
        $item = self::findOne([
            'document_code' => $documentCode,
        ]);
        return ($item !== null && $item->id != $exceptedId);
    }
}