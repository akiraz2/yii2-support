<?php
/**
 * @author Harry Tang <harry@modernkernel.com>
 * @link https://modernkernel.com
 * @copyright Copyright (c) 2016 Modern Kernel
 */


namespace modernkernel\support\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * TicketSearch represents the model behind the search form about `modernkernel\ticket\models\Ticket`.
 */
class TicketSearch extends Ticket
{
    public $userSearch=false;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'cat', 'status', 'created_by', 'updated_at'], 'integer'],
            [['title', 'created_at'], 'safe'],
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
        $query = Ticket::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['id'=>SORT_DESC]],
            //'pagination'=>['pageSize'=>20],
        ]);

        $this->load($params);

        if($this->userSearch){
            $query->andFilterWhere(['created_by'=>Yii::$app->user->id]);
        }

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'cat' => $this->cat,
            'status' => $this->status,
            //'created_by' => $this->created_by,
            //'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'title', $this->title]);

        $query->andFilterWhere([
            'DATE(FROM_UNIXTIME(`created_at`))' => $this->created_at,
        ]);

        return $dataProvider;
    }
}