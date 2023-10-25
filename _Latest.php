<?php
/**
 * Created by PhpStorm.
 * User: yousefi
 * Date: 8/22/20
 * Time: 11:42 AM
 */
    include_once(__DIR__."/../../classes/class.cnfg.php");
    $conf = new config();

    include_once($conf->BaseRoot.'/classes/class.main.php');
    $ths = new main();

    $ths->ExternalLinkCheck2();

    include_once($conf->BaseRoot.'classes/class.libs_parent.php');

    include_once($conf->BaseRoot.'/classes/libs/class.product_rel.php');
    $ProdRelClass = new product_rel();

    include_once($conf->BaseRoot.'/classes/libs/class.product.php');
    $ProductClass = new product();

    include_once($conf->BaseRoot.'/classes/libs/class.category.php');
    $CatClass = new category();

    include_once($conf->BaseRoot.'/classes/libs/class.files.php');
    $FilesClass = new files();


    $Cond[] = ['display', 1];
    $Cond[] = ['deleted', 0];
    $Cond[] = ['lang_id', /*$_SESSION['_Lang_']*/1];

    $MyList = $ProductClass->get_all($Cond, ['product_id', 'title',  'model', 'seo', 'score', 'score_person', 'price', 'img_id', 'code', 'confirm_count', 'confirm_price'], ['product_id', 'desc'], [0, 4]);


    if($MyList){
?>


<div class="widget__header"><h3>آخرین محصولات</h3></div>
<div class="widget-products__list">

    <?php foreach($MyList as $mli=>$ml){
    $MyPrice = $ProdRelClass->get_all_price([['product_id', $ml->product_id], ['confirm', 1], ['active',1]], ['price_off', 'asc'], [0,1]);
    if($MyPrice){
    ?>

    <div class="widget-products__item">
        <div class="widget-products__image">
            <a href="<?=$conf->BaseRoot2?>product/<?=$ml->product_id?>/<?=$ths->UrlFriendly($ml->seo)?>" title="<?=$ml->title?>">

                <?php
                    if($ml->img_id){
                        $MyImg = $FilesClass->get_by_id($ml->img_id, 1);
                        $MyImgUrl = dirname($MyImg['path2']).'/'.$MyImg['fileid'].'_thumb_'.$MyImg['filename'];
                    }else{
                        $MyImgUrl = $conf->BaseRoot2.'MyFile/Product/none.jpg';
                    }
                ?>

                <img src="<?=$MyImgUrl?>" alt="<?=$ml->title?>" style="width:80px;">
            </a>
        </div>
        <div class="widget-products__info">
            <div class="widget-products__name">
                <a href="<?=$conf->BaseRoot2?>product/<?=$ml->product_id?>/<?=$ths->UrlFriendly($ml->seo)?>" title="<?=$ml->title?>">
                    <?=$ml->title?>
                    <br>
                    مدل : <?=$ml->model?>
                </a>
            </div>
            
            <div class="widget-products__prices">
                <?php
                    if($ml->confirm_price){
                        if($MyPrice){
                ?>
                        <?php if($MyPrice[0]->price_off != $MyPrice[0]->price){?>
                            <div class="widget-products__price widget-products__price--new">
                                <?=$ths->money($MyPrice[0]->price_off/10)?>
                                تومان
                            </div>
                            <!--<div class="widget-products__price widget-products__price--old">
                                <?/*=$ths->money($MyPrice[0]->price)*/?>
                                تومان
                            </div>-->
                        <?php }elseif($MyPrice[0]->price > 1000){?>
                            <div class="widget-products__price widget-products__price--current">
                                <?=$ths->money($MyPrice[0]->price/10)?>
                                تومان
                            </div>
                        <?php }?>
                <?php
                        }
                    }
                ?>
            </div>
            
        </div>
    </div>

    <?php }}?>

</div>
<?php
    }
?>