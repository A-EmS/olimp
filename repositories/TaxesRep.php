<?php
namespace app\repositories;


use app\models\Taxes;

class TaxesRep extends Taxes
{
    const CIRCULATION_005 = 0.05;
    const CIRCULATION_006 = 0.06;
    const BEFORE_ACCRUAL_018 = 0.18;
    const BEFORE_ACCRUAL_020 = 0.20;

    const CIRCULATION_005_id = 1;
    const CIRCULATION_006_id = 2;
    const BEFORE_ACCRUAL_018_id = 3;
    const BEFORE_ACCRUAL_020_id = 4;
}