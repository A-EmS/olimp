<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\AcUserRole;

/**
 * V1AcUserRoleSearch represents the model behind the search form about `app\models\V1AcUserRole`.
 */
class AcUserRoleSearch extends AcUserRole
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['acur_id', 'acur_user_id', 'acur_acr_id'], 'integer'],
            [['acur_create_user', 'acur_create_time', 'acur_update_user', 'acur_update_time'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = AcUserRole::find()->where(['acur_user_id' => $params['user_id']]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'acur_id' => $this->acur_id,
            'acur_user_id' => $this->acur_user_id,
            'acur_acr_id' => $this->acur_acr_id,
            'acur_create_time' => $this->acur_create_time,
            'acur_update_time' => $this->acur_update_time,
        ]);

        $query->andFilterWhere(['like', 'acur_create_user', $this->acur_create_user])
            ->andFilterWhere(['like', 'acur_update_user', $this->acur_update_user]);

        return $dataProvider;
    }
}
