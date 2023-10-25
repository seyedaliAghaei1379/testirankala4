<?php
/**
 * Created by PhpStorm.
 * User: yousefi
 * Date: 9/26/20
 * Time: 12:58 PM
 */
    global $ths, $conf;

    include_once($conf->BaseRoot.'/classes/libs/class.brand.php');
    $BrandClass = new brand();

    include_once($conf->BaseRoot.'/classes/libs/class.files.php');
    $FilesClass = new files();

    include_once($conf->BaseRoot.'/classes/libs/class.product.php');
    $ProductClass = new product();

?>

<div class="container">
    <div class="block-split__row row no-gutters">
        <div class="block-split__item block-split__item-content col-auto">
            <div class="block">
                <div class="categories-list categories-list--layout--columns-4-full">
                    <ul class="categories-list__body">
                        <?php
                            $Cond = [];
                            $Cond[] = ['display', 1];
                            $Cond[] = ['deleted', 0];
                            $Cond[] = ['lang_id', 1];

                            $AllBrand = $BrandClass->get_all($Cond);

                            if($AllBrand){
                                foreach($AllBrand as $c){

                                    $Img = $FilesClass->get_by_id($c->img_id, 3);

                                    $ImgUrl1 = '';
                                    if($Img /*&& $Img[count($Img) - 1]*/){
                                        $ImgUrl1 = $Img/*[count($Img) - 1]*/['path2'];
                                    }else{
                                        $ImgUrl1 = $conf->BaseRoot2.'MyFile/Slider/none.jpg';
                                    }
                                ?>
                                <li class="categories-list__item">
                                    <a href="<?=$conf->BaseRoot2.'brand/'.$c->brand_id.'/'.$ths->UrlFriendly($c->title)?>">
                                        <img loading="lazy" data-src="<?=$ImgUrl1?>" alt="<?=$c->title?>">
                                        <div class="categories-list__item-name">
                                            <?=$c->title?>
                                        </div>
                                    </a>
                                    <div class="categories-list__item-products">
                                        <?php
                                            $PrCond = [];
                                            $PrCond[] = ['display', 1];
                                            $PrCond[] = ['deleted', 0];
                                            $PrCond[] = ['lang_id', 1];
                                            $PrCond[] = ['brand_id', $c->brand_id];
                                            #$PrCond[] = ['product`.`product_id`=`product_price`.`product_id` and `product_price`.`confirm', 1];
                                            $prd = $ProductClass->get_all($PrCond, ['product`.`product_id']/*, [], [], 'product.product_id', 'product_price'*/);

                                            if($prd){
                                                echo count($prd);
                                            }else{
                                                echo 'بدون';
                                            }
                                        ?>
                                        محصول
                                    </div>
                                </li>
                                <li class="categories-list__divider"></li>
                            <?php
                                }
                            }
                        ?>
                    </ul>
                </div>
            </div>

            <div class="block-space block-space--layout--divider-nl"></div>

            <div class="block block-products-carousel" data-layout="horizontal">
                <?= $ths->LoadPage($conf->UserPanelUrl . 'Home/Special') ?>
            </div>


        </div>
    </div>

    <div class="block-space block-space--layout--before-footer"></div>
</div>
