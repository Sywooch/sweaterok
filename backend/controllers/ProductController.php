<?php

namespace backend\controllers;

use common\models\Category;
use Yii;
use common\models\Product;
use backend\models\ProductSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\UploadedFile;

/**
 * ProductController implements the CRUD actions for Product model.
 */
class ProductController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['admin'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all Product models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ProductSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Product model.
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
     * Creates a new Product model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $categories = Category::find()->all();
        $model = new Product();
        $model->is_active = 1;
        $model->is_in_stock = 1;

        if($post = Yii::$app->request->post()) {
            if (is_array($post['Product']['color'])) {
                $model->color = implode(",", $post['Product']['color']);
            }
            if (is_array($post['Product']['sizes'])) {
                $model->sizes = implode(",", $post['Product']['sizes']);
            }
            if ($model->load($post) && $model->save()) {
                $model->imageFiles = UploadedFile::getInstances($model, 'imageFiles');
                if ($model->upload()) {
                    return $this->redirect(['view', 'id' => $model->id]);
                }
            } else {
                return $this->render('create', [
                    'model' => $model,
                    'categories' => $categories,
                ]);
            }
        } else {
            return $this->render('create', [
                'model' => $model,
                'categories' => $categories,
            ]);
        }
    }

    /**
     * Updates an existing Product model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $categories = Category::find()->all();
        $model = $this->findModel($id);
        if($post = Yii::$app->request->post()){
            if (is_array($post['Product']['color']))
            {
                $model->color = implode(",",$post['Product']['color']);
            }
            if (is_array($post['Product']['sizes']))
            {
                $model->sizes = implode(",",$post['Product']['sizes']);
            }
            if ($model->load($post) && $model->save()) {
                $model->imageFiles = UploadedFile::getInstances($model, 'imageFiles');
                if ($model->upload()) {
                    return $this->redirect(['view', 'id' => $model->id]);
                }
            } else {
                return $this->render('update', [
                    'model' => $model,
                    'categories' => $categories,
                ]);
            }
        } else {
            $model->color = !empty($model->color)?explode(",",$model->color):[];
            $model->sizes = !empty($model->sizes)?explode(",",$model->sizes):[];
            return $this->render('update', [
                'model' => $model,
                'categories' => $categories,
            ]);
        }
    }

    /**
     * Deletes an existing Product model.
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
     * Finds the Product model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Product the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Product::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
