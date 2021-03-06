<?php
use yii\helpers\Html;
use amilna\elevatezoom\ElevateZoom;

$this->title = Yii::$app->params['title'].' - '.$product->title;
$this->params['breadcrumbs'][] = [
    'label' => $category->title,
    'url' => '/catalog/'.$category->slug,
];
$this->params['breadcrumbs'][] = $product->title;
?>

<div class="row">
    <div class="col-md-6">
        <div class="product-slider-wrapper thumbs-bottom">
            <div class="swiper-container product-slider-main">
                <div class="swiper-wrapper">
                    <?php foreach ($product->images as $image):?>
                        <div class="swiper-slide">
                            <div class="easyzoom easyzoom--overlay">
                                <a href="<?= $image->getUrl()?>" title="<?= $product->title?>">
                                    <?= Html::img($image->getUrl(), ['width' => '100%', 'alt'=>$product->title]);?>
                                </a>
                            </div>
                        </div>
                    <?php endforeach;?>
                </div>
                <div class="swiper-button-prev"><i class="fa fa-chevron-left"></i></div>
                <div class="swiper-button-next"><i class="fa fa-chevron-right"></i></div>
            </div><!-- /.swiper-container -->
            <div class="swiper-container product-slider-thumbs">
                <div class="swiper-wrapper">
                    <?php foreach ($product->images as $image):?>
                        <div class="swiper-slide">
                            <?= Html::img($image->getUrl(), ['width' => '100%', 'alt'=>$product->title]);?>
                        </div>
                    <?php endforeach;?>
                </div>
            </div><!-- /.swiper-container -->
        </div><!-- /.product-slider-wrapper -->
    </div>
    <div class="col-md-6 product-desc-col">
        <div class="product-details-wrapper">
            <h2 class="product-name">
                <?= $product->title?>
            </h2><!-- /.product-name -->
            <div class="product-status">
                <span<?php if($product->is_in_stock):?> class="green">В наличии<?php else:?> class="red">Отсутствует<?php endif;?></span>
                <span>-</span>
                <small>Арт: <?= $product->article?></small>
            </div><!-- /.product-status -->
            <div class="product-description">
                <p><?= $product->description?></p>
            </div><!-- /.product-description -->
            <div class="product-details-top">
                <ul>
                    <li>
                        <span><b>Цвет</b></span>
                        <span class="value"><?= $product->color?></span>
                    </li>
                    <li>
                        <span><b>Состав</b></span>
                        <span class="value"><?= $product->structure?></span>
                    </li>
                </ul>
            </div><!-- /.tab-pane -->
            <div class="product-actions-wrapper">
                <?php if($product->is_in_stock):?>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="p_size">Размер</label>
                                <select name="p_size" id="p_size" class="form-control">
                                    <?php $sizes = explode(',', $product->sizes);?>
                                    <?php foreach ($sizes as $size):?>
                                        <option value="<?= $size?>"><?= $size?></option>
                                    <?php endforeach;?>
                                </select>
                            </div>
                        </div>
                        <?= Html::a('Как выбрать размер?', ['site/sizes'], ['class' => 'product-sizes-link'])?>
                    </div>
                <?php endif;?>
                <div class="product-list-actions" data-id="<?= $product->id ?>">
                        <span class="product-price">
                            <?php if($product->new_price > 0):?>
                                <span class="amount"><?= (int)$product->new_price ?>₽</span>
                                <del class="amount"><?= (int)$product->price ?>₽</del>
                            <?php else:?>
                                <span class="amount"><?= (int)$product->price ?>₽</span>
                            <?php endif;?>
                        </span><!-- /.product-price -->
                    <?php if($product->is_in_stock):?>
                        <?php $currentSize = array_shift($sizes);?>
                        <?= Html::a('В корзину', ['cart/add', 'id' => $product->id, 'size' => $currentSize], ['class' => 'btn btn-lg btn-primary'])?>
                        <?= Html::a('Купить', ['cart/add', 'id' => $product->id, 'size' => $currentSize, 'returnUrl' => '/cart/checkout'], ['class' => 'btn  btn-primary btn-lg btn-outline'])?>
                    <?php endif;?>
                </div><!-- /.product-list-actions -->
            </div><!-- /.product-actions-wrapper -->
            <div class="product-meta">
                <span class="product-category">
                    <span>Категория:</span>
                    <a href="/catalog/<?= $category->slug?>" title="<?= $category->title?>"><?= $category->title?></a>
                </span>
            </div><!-- /.product-meta -->
        </div><!-- /.product-details-wrapper -->
    </div>
</div>
<div class="product-socials">
    <ul class="list-socials">
        <li><a href="#" data-toggle="tooltip" title="Instagram"><i class="icon fa fa-instagram"></i></a></li>
        <li><a href="#" data-toggle="tooltip" title="Vk"><i class="icon fa fa-vk"></i></a></li>
        <li><a href="#" data-toggle="tooltip" title="Facebook"><i class="icon icon-facebook"></i></a></li>
    </ul>
</div><!-- /.product-socials -->
<div class="related-products">
    <div class="related-products-header margin-bottom-50">
        <h3 class="upper">Популярное</h3>
    </div>
    <div class="products owl-carousel" data-items="4">
        <?php foreach (array_values($relatedProducts) as $index => $model) :?>
            <?= $this->render('_product', ['model'=>$model, 'category' => $category]); ?>
        <?php endforeach;?>
    </div>
</div><!-- /.related-products -->

<script>
    $(function() { aweProductRender(true); });

    $(function() { editAddCartButtonOnSizeChange(); });
</script>
