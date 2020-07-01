<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\AcFunc;

/**
 * V1AcFuncSearch represents the model behind the search form about `app\models\V1AcFunc`.
 */
class AcFuncSearch extends AcFunc
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['acf_id'], 'integer'],
            [['acf_name', 'acf_controller', 'acf_action', 'acf_create_user', 'acf_create_time', 'acf_update_user', 'acf_update_time'], 'safe'],
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
        $query = AcFunc::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 100,
            ],

        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'acf_id' => $this->acf_id,
            'acf_create_time' => $this->acf_create_time,
            'acf_update_time' => $this->acf_update_time,
        ]);

        $query->andFilterWhere(['like', 'acf_name', $this->acf_name])
            ->andFilterWhere(['like', 'acf_controller', $this->acf_controller])
            ->andFilterWhere(['like', 'acf_action', $this->acf_action])
            ->andFilterWhere(['like', 'acf_create_user', $this->acf_create_user])
            ->andFilterWhere(['like', 'acf_update_user', $this->acf_update_user]);

        return $dataProvider;
    }
}
