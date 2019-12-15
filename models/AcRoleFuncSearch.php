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
use app\models\AcRoleFunc;

/**
 * V1AcRoleFuncSearch represents the model behind the search form about `app\models\V1AcRoleFunc`.
 */
class AcRoleFuncSearch extends AcRoleFunc
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['acrf_id', 'acrf_acr_id', 'acrf_acf_id'], 'integer'],
            [['acrf_create_user', 'acrf_create_time', 'acrf_update_user', 'acrf_update_time'], 'safe'],
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
        $query = AcRoleFunc::find()->where(['acrf_acr_id' => $params['acr_id']]);

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
            'acrf_id' => $this->acrf_id,
            'acrf_acr_id' => $this->acrf_acr_id,
            'acrf_acf_id' => $this->acrf_acf_id,
            'acrf_create_time' => $this->acrf_create_time,
            'acrf_update_time' => $this->acrf_update_time,
        ]);

        $query->andFilterWhere(['like', 'acrf_create_user', $this->acrf_create_user])
            ->andFilterWhere(['like', 'acrf_update_user', $this->acrf_update_user]);

        return $dataProvider;
    }
}
