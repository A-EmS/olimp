<?php
/**
 * @author Ruslan Bondarenko (Dnipro) r.i.bondarenko@gmail.com
 * @copyright Copyright (C) 2016-2017 Ruslan Bondarenko (Dnipro)
 * @license http://www.yiiframework.com/license/
 */


namespace app\models;

/**
 * This is the ActiveQuery class for [[AcUserRole]].
 *
 * @see AcUserRole
 */
class AcUserRoleQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        $this->andWhere('[[status]]=1');
        return $this;
    }*/

    /**
     * @inheritdoc
     * @return AcUserRole[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return AcUserRole|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}