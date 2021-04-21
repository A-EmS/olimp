<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "request_stage_notices".
 *
 * @property int $id
 * @property int $request_id
 * @property int $project_stage_id
 * @property string $notice
 */
class RequestStageNotices extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'request_stage_notices';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['request_id', 'project_stage_id'], 'integer'],
            [['notice'], 'string', 'max' => 1000],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'request_id' => 'Request ID',
            'project_stage_id' => 'Project Stage ID',
            'notice' => 'Notice',
        ];
    }
}
