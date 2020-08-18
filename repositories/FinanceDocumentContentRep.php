<?php
namespace app\repositories;

use app\models\FinanceDocumentContent;

class FinanceDocumentContentRep extends FinanceDocumentContent
{
    public static function chekOnDuplicate($documentId, $productId = null, $serviceId = null, $exceptedId = null)
    {
        if ($productId != null) {
            $item = self::findOne([
                'document_id' => $documentId,
                'product_id' => $productId,
            ]);
        }

        if ($serviceId != null) {
            $item = self::findOne([
                'document_id' => $documentId,
                'service_id' => $serviceId,
            ]);
        }

        return ($item !== null && $item->id != $exceptedId);
    }
}