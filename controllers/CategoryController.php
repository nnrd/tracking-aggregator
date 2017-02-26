<?php

namespace app\controllers;

use Yii;
use app\models\Category;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * CategoryController implements the CRUD actions for Category model.
 */
class CategoryController extends \yii\base\Controller
{
    /**
     * @inheritdoc
     */
    public function _behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    // allow authenticated users
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    // everything else is denied by default
                ],
            ],
        ];
    }

    public function actions()
    {
        return \app\widgets\bootstrapTreeview\BootstrapTreeviewWidget::actions([
            'modelClass' => Category::class
        ]);
    }

    /**
     * Lists all Category models.
     * @return mixed
     */
    public function actionIndex()
    {
        return $this->render('index', [
            'modelClass' => Category::class,
            'name' => 'title',
        ]);
    }

    /**
     * Displays a single Category model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Category model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Category();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Category model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Category model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Category model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Category the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Category::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * @return null
     * @throws HttpException
     */
    public function actionCreateNode()
    {
        $model = new Category;

        $params = Yii::$app->getRequest()->getBodyParams();
        $model->load($params);

        if (!$model->validate()) {
            return $model;
        }

        $roots = $model::find()->roots()->all();

        if (isset($roots[0])) {
            return $model->appendTo($roots[0])->save();
        } else {
            return $model->makeRoot()->save();
        }
    }

    /**
     * Move a node (model) below the parent and in between left and right
     *
     * @param integer $id the primaryKey of the moved node
     * @return array
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function actionUpdateNode($id)
    {
        /** @var ActiveRecord|TreeInterface $model */
        $model = $this->findModel($id);
        $model->title = Yii::$app->request->post('title');

        if (!$model->validate())
        {
            return $model;
        }

        return $model->update(true, ['title']);
    }

    /**
     * Move a node (model) below the parent and in between left and right
     *
     * @param integer $id the primaryKey of the moved node
     * @return array
     * @throws NotFoundHttpException
     */
    public function actionDeleteNode($id)
    {
        /** @var ActiveRecord|TreeInterface $model */
        $model = $this->findModel($id);

        return $model->deleteWithChildren();
    }
}
