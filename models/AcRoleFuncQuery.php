<?php
/**
 * @author Ruslan Bondarenko (Dnipro) r.i.bondarenko@gmail.com
 * @copyright Copyright (C) 2016-2017 Ruslan Bondarenko (Dnipro)
 * @license http://www.yiiframework.com/license/
 */

namespace app\models;

/**
 * This is the ActiveQuery class for [[AcRoleFunc]].
 *
 * @see AcRoleFunc
 */
class AcRoleFuncQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        $this->andWhere('[[status]]=1');
        return $this;
    }*/

    /**
     * @inheritdoc
     * @return AcRoleFunc[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return AcRoleFunc|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}