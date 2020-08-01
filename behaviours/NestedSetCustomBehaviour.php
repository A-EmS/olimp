<?php

namespace app\behaviours;

use creocoder\nestedsets\NestedSetsBehavior;
use yii\db\ActiveRecord;

/**
 * @property ActiveRecord $owner
 */
class NestedSetCustomBehaviour extends NestedSetsBehavior
{
    /**
     * Gets the children of the node.
     * @param integer|null $depth the depth
     * @return \yii\db\ActiveQuery
     */
    public function childrenByPriority($depth = null)
    {
        $condition = [
            'and',
            ['>', $this->leftAttribute, $this->owner->getAttribute($this->leftAttribute)],
            ['<', $this->rightAttribute, $this->owner->getAttribute($this->rightAttribute)],
        ];

        if ($depth !== null) {
            $condition[] = ['<=', $this->depthAttribute, $this->owner->getAttribute($this->depthAttribute) + $depth];
        }

        $this->applyTreeAttributeCondition($condition);

        return $this->owner->find()->andWhere($condition)->addOrderBy(['priority' => SORT_ASC]);
    }
}