<?php

namespace frontend\controllers;

use common\models\Order;
use common\models\OrderItem;
use common\models\Product;
use yz\shoppingcart\ShoppingCart;
use Yii;
use frontend\models\BoxberryApi;

class CartController extends \yii\web\Controller
{
    public function actionAdd($id, $size, $returnUrl = null)
    {
        $product = Product::findOne($id);
        if ($product) {
            $position = $product->getCartPosition();
            $position->size = $size;
            \Yii::$app->cart->put($position);
            if($returnUrl)
                return Yii::$app->getResponse()->redirect($returnUrl);
            else
                return $this->goBack();
        }
    }

    public function actionRemove($id, $size)
    {
        $product = Product::findOne($id);
        if ($product) {
            $position = $product->getCartPosition();
            $position->size = $size;
            \Yii::$app->cart->remove($position);
            $this->redirect(['cart/checkout']);
        }
    }

    public function actionUpdate($id, $size, $quantity)
    {
        $product = Product::findOne($id);
        if ($product) {
            $position = $product->getCartPosition();
            $position->size = $size;
            \Yii::$app->cart->update($product, $quantity);
            $this->redirect(['cart/checkout']);
        }
    }

    public function actionCheckout()
    {
        $order = new Order();
        /* @var $cart ShoppingCart */
        $cart = \Yii::$app->cart;
        if ($cart->getCount() > 0) {
            $products = $cart->getPositions();
            $total = $cart->getCost();

            if ($order->load(\Yii::$app->request->post()) && $order->validate()) {
                $transaction = $order->getDb()->beginTransaction();
                $order->save(false);

                foreach ($products as $product) {
                    $p = $product->getProduct();
                    if($p->is_active && $p->is_in_stock){
                        $orderItem = new OrderItem();
                        $orderItem->order_id = $order->id;
                        $orderItem->title = $p->article . ' ' . $p->title;
                        $orderItem->price = $product->getPrice();
                        $orderItem->product_id = $product->id;
                        $orderItem->size = $product->size;
                        $orderItem->quantity = $product->getQuantity();
                        if (!$orderItem->save(false)) {
                            $transaction->rollBack();
                            \Yii::$app->session->addFlash('error', 'Невозможно создать заказ. Пожалуйста свяжитесь с нами.');
                            return $this->redirect('/catalog/list');
                        }
                    }
                }

                $transaction->commit();
                \Yii::$app->cart->removeAll();

                \Yii::$app->session->addFlash('success', 'Спасибо за заказ! Мы скоро свяжемся с Вами.');
                $order->sendEmail();
                return $this->redirect('/catalog/list');
            }

            $bb = new BoxberryApi();
            $cities = $bb->getListCities();

            return $this->render('checkout', [
                'order' => $order,
                'products' => $products,
                'total' => $total,
                'cities' => $cities,
            ]);
        } else {
            return $this->redirect('/catalog/list');
        }
    }
}
