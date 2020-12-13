<?php
namespace app\repositories;

use app\models\AcRole;
use app\models\UserPermissions;
use app\models\UserSettings;
use Yii;

class UsersSettingsRep extends UserSettings
{
    const INTERFACE_LANGUAGE = 'interface_language';

    const INTERFACE_LANGUAGE_LABEL = 'Interface Language';

    const USER_SETTINGS = [
        self::INTERFACE_LANGUAGE_LABEL => self::INTERFACE_LANGUAGE,
    ];

    const USER_SETTINGS_LABELS = [
        self::INTERFACE_LANGUAGE => self::INTERFACE_LANGUAGE_LABEL,
    ];

}