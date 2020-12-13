<?php
namespace app\repositories;

use app\models\AcRole;
use app\models\UserPermissions;
use Yii;

class UsersPermissionsRep extends UserPermissions
{
    const ENTITY_RESTRICTIONS = 'ENTITY_RESTRICTIONS';

    const ENTITY_RESTRICTIONS_LABEL = 'Entity Restrictions';

    const USER_PERMISSIONS = [
        self::ENTITY_RESTRICTIONS_LABEL => self::ENTITY_RESTRICTIONS,
    ];

    const USER_PERMISSIONS_LABELS = [
        self::ENTITY_RESTRICTIONS => self::ENTITY_RESTRICTIONS_LABEL,
    ];

}