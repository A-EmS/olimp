<?php
namespace app\repositories;

use app\models\DocumentTypes;
use Yii;

class DocumentTypesRep extends DocumentTypes
{
    const SCENARIO_TYPE_CONTRACT = 1;
    const SCENARIO_TYPE_ANNEX = 2;
    const SCENARIO_TYPE_AD_AGREEMENT = 3;
    const SCENARIO_TYPE_ACCOUNT = 4;
    const SCENARIO_TYPE_ACT = 5;
    const SCENARIO_TYPE_COMMERCIAL_OFFERING = 6;

    const SCENARIO_TYPE_CONTRACT_TEXT = 'Contract Scenario';
    const SCENARIO_TYPE_ANNEX_TEXT = 'Annex Scenario';
    const SCENARIO_TYPE_AD_AGREEMENT_TEXT = 'Additional Agreement Scenario';
    const SCENARIO_TYPE_ACCOUNT_TEXT = 'Account Scenario';
    const SCENARIO_TYPE_ACT_TEXT = 'Act Scenario';
    const SCENARIO_TYPE_COMMERCIAL_OFFERING_TEXT = 'Commercial Offering';

    const SCENARIOS = [
        self::SCENARIO_TYPE_CONTRACT => self::SCENARIO_TYPE_CONTRACT_TEXT,
        self::SCENARIO_TYPE_ANNEX => self::SCENARIO_TYPE_ANNEX_TEXT,
        self::SCENARIO_TYPE_AD_AGREEMENT => self::SCENARIO_TYPE_AD_AGREEMENT_TEXT,
        self::SCENARIO_TYPE_ACCOUNT => self::SCENARIO_TYPE_ACCOUNT_TEXT,
        self::SCENARIO_TYPE_ACT => self::SCENARIO_TYPE_ACT_TEXT,
        self::SCENARIO_TYPE_COMMERCIAL_OFFERING => self::SCENARIO_TYPE_COMMERCIAL_OFFERING_TEXT,
    ];

    const SCENARIOS_IDS = [
        self::SCENARIO_TYPE_CONTRACT_TEXT => self::SCENARIO_TYPE_CONTRACT,
        self::SCENARIO_TYPE_ANNEX_TEXT => self::SCENARIO_TYPE_ANNEX,
        self::SCENARIO_TYPE_AD_AGREEMENT_TEXT => self::SCENARIO_TYPE_AD_AGREEMENT,
        self::SCENARIO_TYPE_ACCOUNT_TEXT => self::SCENARIO_TYPE_ACCOUNT,
        self::SCENARIO_TYPE_ACT_TEXT => self::SCENARIO_TYPE_ACT,
        self::SCENARIO_TYPE_COMMERCIAL_OFFERING_TEXT => self::SCENARIO_TYPE_COMMERCIAL_OFFERING,
    ];

    public static function checkDuplicateByCountryAndName(int $countryId, string $name, $exceptedId = null)
    {
        $item = self::findOne(['country_id' => $countryId, 'name' => $name]);
        return ($item !== null && $item->id != $exceptedId && !empty($item->name));
    }
}