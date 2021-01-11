<?php

namespace app\controllers;

use app\models\FinanceClasses;
use app\repositories\FinanceClassesRep;
use Yii;
use yii\filters\VerbFilter;

class FinanceClassesController extends BaseController
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => \yii\filters\AccessControl::class,
                'rules' => [
                    [
                        'allow' => ($_SERVER['HTTP_HOST'] == 'olimp.loc'),
                        'roles' => ['?'],
                    ],
                    [
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' =>
                            function ($rule, $action) {
                                return \app\models\AcAccess::checkAction($action);
                            },
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [

                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * @param int $id
     * @return false|string
     * @throws \yii\db\Exception
     */
    public function actionGetById(int $id)
    {
        return json_encode([]);
    }

    /**
     * @return false|string
     * @throws \yii\db\Exception
     */
    public function actionGetAll(int $id)
    {
        if ($id == null){
            $id = (int)Yii::$app->request->get('id');
        }

        $sql = 'SELECT id, name, priority, payment_operation_type_id, depth
                FROM finance_classes AS targetTable
                where targetTable.id != :id
                order by name asc 
                ';

        $command = Yii::$app->db->createCommand($sql);
        $command->bindParam(":id",$id);
        $items = $command->queryAll();

        return json_encode(['items'=> $items]);
    }

    /**
     * @return false|string
     * @throws \yii\db\Exception
     */
    public function actionGetAllForSelect()
    {
        $items = $this->generateForSelect();
        return json_encode(['items'=> $items]);
    }

    public function actionGetInitNodes()
    {
        $mainNode = FinanceClasses::findOne(['depth' => 0]);
        $mainNodeChildren = $mainNode->childrenByPriority(1)->all();

        $initChildren = [];
        foreach ($mainNodeChildren as $mainNodeChild) {
            $dataChild = [
                'id'=> $mainNodeChild->id,
                'name' => $mainNodeChild->name,
                'priority' => $mainNodeChild->priority,
                'payment_operation_type_id' => $mainNodeChild->payment_operation_type_id,
                'depth' => $mainNodeChild->depth,
                'isRoot' => $mainNodeChild->isRoot(),
                'isLeaf' => $mainNodeChild->isLeaf(),
            ];

            if(!$mainNodeChild->isLeaf()) {
                $dataChild['children'] = [];
            }

            $initChildren[] = $dataChild;
        }

        $items[] = ['id'=> $mainNode->id, 'priority'=> $mainNode->priority, 'payment_operation_type_id'=> $mainNode->payment_operation_type_id,  'depth'=> $mainNode->depth, 'name' => $mainNode->name, 'children' => $initChildren, 'isRoot' => (bool)$mainNode->isRoot(), 'isLeaf' => (bool)$mainNode->isLeaf()];

        return json_encode(['items' => $items]);
    }

    public function actionGetChildrenByNodeId(int $id = null)
    {
        if ($id == null){
            $id = (int)Yii::$app->request->get('id');
        }

        $node = FinanceClasses::findOne($id);
        $modelChildren = $node->childrenByPriority(1)->all();

        $items = [];
        foreach ($modelChildren as $modelChild) {
            $dataChild = [
                'id'=> $modelChild->id,
                'name' => $modelChild->name,
                'priority' => $modelChild->priority,
                'payment_operation_type_id' => $modelChild->payment_operation_type_id,
                'depth' => $modelChild->depth,
                'isRoot' => $modelChild->isRoot(),
                'isLeaf' => $modelChild->isLeaf(),
            ];

            if(!$modelChild->isLeaf()) {
                $dataChild['children'] = [];
            }

            $items[] = $dataChild;

        }

        return json_encode(['items' => $items]);
    }

    public function actionCreate()
    {

        $parentNodeId = Yii::$app->request->post('parentNodeId');
        $name = Yii::$app->request->post('name');
        $priority = Yii::$app->request->post('priority');
        $paymentOperationTypeId = Yii::$app->request->post('payment_operation_type_id');

        if (FinanceClassesRep::checkDuplicateByName($name)) {
            return json_encode(['error' => 'Such name is already existed']);
        }

        try{

            $parentNode = FinanceClasses::findOne($parentNodeId);

            $model = new FinanceClassesRep(['name' => $name]);
            $model->name = $name;
            $model->priority = $priority;
            $model->payment_operation_type_id = $parentNode->depth > 0 ? $parentNode->payment_operation_type_id : $paymentOperationTypeId;
            $model->create_user = Yii::$app->user->identity->id;
            $model->create_date = date('Y-m-d H:i:s', time());
            $model->appendTo($parentNode);
            $model->save(false);

            $this->changePriorityChainAfterNode($model);

            return json_encode(['id'=> $model->id, 'name'=> $model->name, 'priority'=> $model->priority, 'payment_operation_type_id'=> $model->payment_operation_type_id, 'depth'=> $model->depth]);
        } catch (\Exception $e){
            return json_encode(['error'=> 'Creating was no happened. Perhaps you have already have same name.']);
        }
    }

    public function actionUpdate(int $id = null)
    {
        if ($id == null){
            $id = (int)Yii::$app->request->post('id');
        }

        $name = Yii::$app->request->post('name');
        $priority = Yii::$app->request->post('priority');
        $paymentOperationTypeId = Yii::$app->request->post('payment_operation_type_id');

        if (FinanceClassesRep::checkDuplicateByName($name, $id)) {
            return json_encode(['error' => 'Such name is already existed']);
        }

        try {
            $model = FinanceClassesRep::findOne($id);
            $parentNode = $model->parents(1)->one();

            $model->name = $name;
            $model->priority = $priority;
            $model->payment_operation_type_id = $parentNode->depth == 0 ? $paymentOperationTypeId : $parentNode->payment_operation_type_id;
            $model->update_user = Yii::$app->user->identity->id;
            $model->update_date = date('Y-m-d H:i:s', time());
            $model->save(false);

            $this->changePriorityChainAfterNode($model);
        } catch (\Exception $e){
            return json_encode(['error'=> 'Updating was no happened. Perhaps you have already have same name.']);
        }
    }

    public function actionDelete(int $id = null) : string
    {
        if ($id == null){
            $id = (int)Yii::$app->request->post('id');
        }

        $model = FinanceClasses::findOne($id);
        $model->deleteWithChildren();

        return json_encode(['status' => true]);
    }

    public function actionMove(int $newParentNodeId = null, int $movableNodeId = null)
    {

        if ($newParentNodeId == null){
            $newParentNodeId = (int)Yii::$app->request->post('newParentNodeId');
        }

        if ($movableNodeId == null){
            $movableNodeId = (int)Yii::$app->request->post('movableNodeId');
        }

        $newParentNode = FinanceClasses::findOne($newParentNodeId);
        $movableNode = FinanceClasses::findOne($movableNodeId);
        $movableNode->appendTo($newParentNode);
        $movableNode->save(false);

        $this->changePriorityChainAfterNode(FinanceClassesRep::findOne($movableNodeId));
        return json_encode(['status' => true]);
    }

    protected function changePriorityChainAfterNode(FinanceClasses $node) {

        $parent = $node->parents(1)->one();

        $modelChildren = $parent->childrenByPriority(1)->all();

        $number = $node->priority + 1;
        foreach ($modelChildren as $modelChild) {
            if ($modelChild->id != $node->id && $modelChild->priority >= $node->priority) {
                $modelChild->priority = $number;
                $modelChild->save(false);
                ++$number;
            }
        }
    }

    protected function generateForSelect(int $id = 1)
    {
        $node = FinanceClasses::findOne($id);
        $modelChildren = $node->leaves()->all();

        $items = [];
        foreach ($modelChildren as $modelChild) {
            $parents = $modelChild->parents()->all();
            $keyName = '';
            foreach ($parents as $parent) {
                $keyName = ($keyName == '') ? $parent->name : $keyName.'::'.$parent->name;
            }
            $keyName = $keyName.'::'.$modelChild->name;
            $items[] = ['id' => ''.$modelChild->id, 'name' => $keyName];
        }

        return $items;
    }
}
