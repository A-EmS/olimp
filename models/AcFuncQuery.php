<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[V1AcFunc]].
 *
 * @see AcFunc
 */
class AcFuncQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        $this->andWhere('[[status]]=1');
        return $this;
    }*/

    /**
     * @inheritdoc
     * @return AcFunc[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return AcFunc|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}