<?php

namespace app\widgets\bootstrapTreeview\actions;

use Yii;
use yii\db\ActiveRecord;
use yii\web\HttpException;
use app\widgets\bootstrapTreeview\BootstrapTreeviewWidget;

/**
 * Class ManageTreeAction
 * @package app\widgets\bootstrap_treeview\actions
 */
class ManageTreeAction extends \yii\base\Action
{

    /**
     * @var string Class to use to locate the supplied data ids
     */
    public $modelClass;
    /**
     * @var string Attribute name of a model
     */
    public $nameAttribute;

    protected $tree;

    /**
     * @throws InvalidConfigException
     */
    public function init()
    {
        if (null == $this->modelClass) {
            throw new InvalidConfigException('Param "modelClass" must be contain model name with namespace.');
        }
    }

    /**
     * @param $id
     * @return ActiveRecord
     * @throws NotFoundHttpException
     */
    public function findModel($id)
    {
        /** @var ActiveRecord $model */
        $model = new $this->modelClass;
        /** @var ActiveRecord $model */
        $model = $model::findOne($id);

        if ($model == null) {
            throw new NotFoundHttpException();
        }

        return $model;
    }

    /**
     * @return null
     * @throws HttpException
     */
    public function run()
    {
        try
        {
            $action = Yii::$app->request->post('action');
            $params = Yii::$app->request->post('params');
            $nodesState = Yii::$app->request->post('nodesState');
            $rootNodeId = Yii::$app->request->post('rootNodeId', false);
            $showRoot = Yii::$app->request->post('showRoot', false);
            $this->tree = new BootstrapTreeviewWidget([
                'modelClass' => $this->modelClass,
                'nodesState' => $nodesState,
                'rootNodeId' => $rootNodeId == 'false' ? false : (int) $rootNodeId,
                'showRoot'   => $showRoot == 'true',
            ]);

            switch($action)
            {
            case 'refresh':
                return $this->refresh();
            case 'update':
                return $this->update($params);
            case 'create':
                return $this->create($params);
            case 'delete':
                return $this->delete($params);
            case 'move':
                return $this->move($params);
            }
        }
        catch (\Exception $e)
        {
            return $this->errorMessage($e->getMessage());
        }
        return $this->errorMessage(BootstrapTreeviewWidget::m('Unknown method'));
    }

    protected function response($data)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return json_encode($data);
    }


    protected function errorMessage($message)
    {
        return $this->response(['result' => 'error', 'error' => $message]);
    }

    protected function error($model)
    {
        return $this->errorMessage($model->getErrors());
    }


    protected function success($data)
    {
        return $this->response(['result' => 'success', 'data' => $data]);
    }

    /**
     * Perform create node operation
     * params array should has 2 elements:
     * type string one of root|node|subnode,
     *  for 'root' new root node will be created,
     *  for 'node' new node at same level,
     *  for 'subnode' new subnode of this node
     *
     * model_id int model id, for 'root' type is not used
     *
     * @param array params Parameters passed by Js.
     */
    protected function create($params)
    {
        $type = $params['type'];
        $model_id = $params['model_id'];
        if ( $model_id == -1 ) // Not selected
        {
            $type = 'root';
        }
        $model = new $this->modelClass;
        $model->setAttribute($this->nameAttribute, BootstrapTreeviewWidget::m('Undefined'));

        if ($model_id >= 0)
        {
            $other = $this->findModel($model_id);
            if ($other->isRoot())
            {
                $type = 'subnode';
            }
        }

        switch ($type)
        {
        case 'root':
            if ($model->makeRoot(false))
            {
                return $this->refresh();
            }
            return $this->error($model);
        case 'node':

            if ($model->insertAfter($other, false))
            {
                return $this->refresh();
            }
            return $this->error($model);
        case 'subnode':
            $other = $this->findModel($model_id);

            if ($model->appendTo($other, false))
            {
                return $this->refresh();
            }
            return $this->error($model);
        }

        return $this->refresh();
    }

    /**
     * Perform update operation
     * params array should has 2 elements:
     * model_id int model id to be updated
     * text string New node name
     *
     * @param array params Parameters passed by Js.
     */
    protected function update($params)
    {
        $model = $this->findModel((int) $params['model_id']);
        $model->setAttribute($this->nameAttribute, $params['text']);
        if ($model->save())
        {
            return $this->refresh();
        }
        else
        {
            return $this->error($model);
        }
    }

    /**
     * Perform delete operation
     * params array should has 1 element:
     * model_id int model id to be deleted
     *
     * @param array params Parameters passed by Js.
     */
    protected function delete($params)
    {
        $model = $this->findModel($params['model_id']);
        if ($model->deleteWithChildren())
        {
            return $this->refresh();
        }
        else
        {
            return $this->error($model);
        }
    }

    /**
     * Perform create node operation
     * params array should has 3 elements:
     * source_id int model id to be moved
     * dest_id int model id to be destination of move
     * type string one of before|after|subnode
     *  for before|after source will be moved berofe|after a destination
     *  for subnode source will be moved to be subnode of a destination
     *
     * @param array params Parameters passed by Js.
     */
    protected function move($params)
    {
        $source_model = $this->findModel($params['source_id']);
        $dest_model = $this->findModel($params['dest_id']);
        $type = $params['type'];

        if ($type == 'subnode' && $source_model->appendTo($dest_model))
        {
            return $this->refresh();
        }

        if ($dest_model->isRoot())
        {
            if ($type == 'after')
            {
                if ($source_model->appendTo($dest_model))
                {
                    return $this->refresh();
                }
            }
            elseif ($type == 'before')
            {
                if ($source_model->prependTo($dest_model))
                {
                    return $this->refresh();
                }
            }
            return $this->refresh();
        }

        if ($type == 'before' && $source_model->insertBefore($dest_model))
        {
            return $this->refresh();
        }
        if ($type == 'after' && $source_model->insertAfter($dest_model))
        {
            return $this->refresh();
        }

        return $this->error($source_model);
    }

    /**
     * Send back fresh tree
     */
    protected function refresh()
    {
        return $this->success($this->tree->composeTree());
    }
}
