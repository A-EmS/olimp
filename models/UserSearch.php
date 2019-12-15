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
use app\models\User;
use yii\rbac\Role;

/**
 * UserSearch represents the model behind the search form about `app\models\User`.
 */
class UserSearch extends User
{
    public $user_role;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'user_level'], 'integer'],
            [['user_name', 'user_pwd', 'user_authKey', 'user_accessToken', 'user_real', 'user_role'], 'safe'],
            [['user_create_user', 'user_create_time', 'user_update_user', 'user_update_time', 'user_create_ip', 'user_update_ip'], 'safe'],
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
        $query = User::find()
            ->leftJoin(AcUserRole::tableName(), 'acur_user_id = user_id')
            ->leftJoin(AcRole::tableName(), 'acr_id = acur_acr_id');

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $dataProvider->sort->attributes['user_role'] = [
            'asc' => ['ac_role.acr_name' => SORT_ASC],
            'desc' => ['ac_role.acr_name' => SORT_DESC],
        ];

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'user_id' => $this->user_id,
            'user_level' => $this->user_level,
        ]);

        $query->andFilterWhere(['like', 'user_name', $this->user_name])
            ->andFilterWhere(['like', 'user_real', $this->user_real])
            ->andFilterWhere(['like', 'user_pwd', $this->user_pwd])
            ->andFilterWhere(['like', 'user_authKey', $this->user_authKey])
            ->andFilterWhere(['like', 'user_accessToken', $this->user_accessToken])
            ->andFilterWhere(['like', 'ac_role.acr_name', $this->user_role])
            ->andFilterWhere(['like', 'user_create_user', $this->user_create_user])
            ->andFilterWhere(['like', 'user_update_user', $this->user_update_user])
            ->andFilterWhere(['like', 'user_create_time', $this->user_create_time])
            ->andFilterWhere(['like', 'user_update_time', $this->user_update_time])
            ->andFilterWhere(['like', 'user_create_ip', $this->user_create_user])
            ->andFilterWhere(['like', 'user_update_ip', $this->user_update_user]);

        return $dataProvider;
    }
}
