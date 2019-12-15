<?php
/**
 * @author Ruslan Bondarenko (Dnipro) r.i.bondarenko@gmail.com
 * @copyright Copyright (C) 2016-2017 Ruslan Bondarenko (Dnipro)
 * @license http://www.yiiframework.com/license/
 */


namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\AcRole;

/**
 * AcRoleSearch represents the model behind the search form about `app\models\AcRole`.
 */
class AcRoleSearch extends AcRole
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['acr_id'], 'integer'],
            [['acr_name', 'acr_desc', 'acr_create_user', 'acr_create_time', 'acr_update_user', 'acr_update_time', 'acr_create_ip', 'acr_update_ip'], 'safe'],
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
        $query = AcRole::find();

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
            'acr_id' => $this->acr_id
        ]);

        $query
            ->andFilterWhere(['like', 'acr_name', $this->acr_name])
            ->andFilterWhere(['like', 'acr_desc', $this->acr_desc])
            ->andFilterWhere(['like', 'acr_create_user', $this->acr_create_user])
            ->andFilterWhere(['like', 'acr_update_user', $this->acr_update_user])
            ->andFilterWhere(['like', 'acr_create_time', $this->acr_create_time])
            ->andFilterWhere(['like', 'acr_update_time', $this->acr_update_time])
            ->andFilterWhere(['like', 'acr_create_ip', $this->acr_create_user])
            ->andFilterWhere(['like', 'acr_update_ip', $this->acr_update_user]);

        return $dataProvider;
    }
}
