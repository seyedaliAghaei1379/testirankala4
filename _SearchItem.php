<?php
session_set_cookie_params(600, '/', $_SERVER['HTTP_HOST'], true, true);
session_start();
/**
 * Created by PhpStorm.
 * User: yousefi
 * Date: 9/6/20
 * Time: 9:16 AM
 */
    include_once(__DIR__."/../../classes/class.cnfg.php");
    $conf = new config();

    include_once($conf->BaseRoot.'/classes/class.main.php');
    $ths = new main();

    $ths->ExternalLinkCheck();

    include_once($conf->BaseRoot.'classes/class.libs_parent.php');

    include_once($conf->BaseRoot.'/classes/libs/class.product.php');
    $ProductClass = new product();

    include_once($conf->BaseRoot.'/classes/libs/class.files.php');
    $FileClass = new files();

    include_once($conf->BaseRoot.'/classes/libs/class.category.php');
    $CatClass = new category();

    include_once($conf->BaseRoot.'/classes/libs/class.product_company.php');
    $ProdCompClass = new product_company();

?>
<script src="<?=$conf->BaseRoot2?>Files/js/lazyload.min.js"></script>
<script>
      (function () {
        function logElementEvent(eventName, element) {
          console.log(Date.now(), eventName, element.getAttribute("data-src"));
        }

        var callback_enter = function (element) {
          logElementEvent("🔑 ENTERED", element);
        };
        var callback_exit = function (element) {
          logElementEvent("🚪 EXITED", element);
        };
        var callback_loading = function (element) {
          logElementEvent("⌚ LOADING", element);
        };
        var callback_loaded = function (element) {
          logElementEvent("👍 LOADED", element);
        };
        var callback_error = function (element) {
          logElementEvent("💀 ERROR", element);
          element.src =
            "https://source.unsplash.com/random/440x560/?text=Error+Placeholder";
        };
        var callback_finish = function () {
          logElementEvent("✔️ FINISHED", document.documentElement);
        };
        var callback_cancel = function (element) {
          logElementEvent("🔥 CANCEL", element);
        };

        var ll = new LazyLoad({
          elements_selector: "[loading=lazy]",
          use_native: true,
          // Assign the callbacks defined above
          callback_enter: callback_enter,
          callback_exit: callback_exit,
          callback_cancel: callback_cancel,
          callback_loading: callback_loading,
          callback_loaded: callback_loaded,
          callback_error: callback_error,
          callback_finish: callback_finish
        });
      })();
    </script>
<div class="suggestions__group">

    <?php
        $Cond = [];
        $Cond[] = ['display', 1];
        $Cond[] = ['deleted', 0];
        #$Cond[] = ['confirm_price', 1];
        #$Cond[] = ['confirm_count', 1];
        $Cond[] = ['lang_id', $_SESSION['_Lang_']];
        if(isset($_REQUEST['Srch']) && $_REQUEST['Srch']){

            #$_REQUEST['Srch'] = $ths->MakeSecurParam($_REQUEST['Srch'], true);
            $_REQUEST['Srch'] = mysqli_real_escape_string($ths->dbLink, $ths->MakeSecurParam($_REQUEST['Srch'], true));

            $Cond[] = ['product_id`!=0 and (`model` like "%'.$_REQUEST['Srch'].'%" or `title` like "%'.$_REQUEST['Srch'].'%" or `title_en` like "%'.$_REQUEST['Srch'].'%" or `model_en` like "%'.$_REQUEST['Srch'].'%" or `code` like "%'.$_REQUEST['Srch'].'%") and `product_id', '0', '!='];
        }
        if(isset($_REQUEST['SelSrchCat']) && (int)$_REQUEST['SelSrchCat']){
            $Cond[] = ['cat_id', $CatClass->get_all_child((int)$_REQUEST['SelSrchCat']), 'in'];
        }

        if(isset($_REQUEST['Srch']) && $_REQUEST['Srch']){
            $OrderArr = ['confirm_count', 'desc', 'confirm_price', 'desc', 'score', 'desc', 'visit', 'desc'];
        }else{

            $Cond[] = ['confirm_price', 1];
            $Cond[] = ['confirm_count', 1];

            $OrderArr = ['rand()', ''];
        }

        $AllRes = $ProductClass->get_all($Cond, ['product_id', 'title', 'model', 'seo', 'score', 'score_person', 'img_id', 'cat_id'], $OrderArr, [0, 5]);

        $MyCatArr = [];

        if($AllRes){
            foreach($AllRes as $res){
                if(!in_array($res->cat_id, $MyCatArr)){
                    $MyCatArr[] = $res->cat_id;
                }
            }
        }

    ?>

    <?php if(count($MyCatArr)){?>
        <div class="suggestions__group">
            <div class="suggestions__group-title">دسته بندی ها</div>
            <div class="suggestions__group-content">
                <ul class="breadcrumb__list">
                    <?php
                    $CatTitleArr = [];
                    $CatTitle = $CatClass->get_all([['catid', $MyCatArr, 'in']], ['title', 'catid']);
                    foreach($CatTitle as $cc){
                        $CatTitleArr[$cc->catid] = $cc->title;
                    }
                    foreach($MyCatArr as $c){
                        ?>
                        <li class="breadcrumb__item breadcrumb__item--parent"><a class="breadcrumb__item-link" href="<?=$conf->BaseRoot2?>category/<?=$c?>/<?=(isset($CatTitleArr[$c]) ? $ths->UrlFriendly($CatTitleArr[$c]) : '')?>" style="font-size:10px">&nbsp;<?=(isset($CatTitleArr[$c]) ? $CatTitleArr[$c] : '')?>&nbsp;</a></li>
                    <?php }?>
                </ul>
            </div>
        </div>
    <?php }?>

    <div class="suggestions__group-title">&nbsp;</div>
    <div class="suggestions__group-content">

        <?php
            if($AllRes){
                foreach($AllRes as $res){

                    $CompFlag = $ProdCompClass->get_all([['product_id', $res->product_id], ['deleted', 0]]);
                    if(!$CompFlag){
                        continue;
                    }
        ?>
                    <a class="suggestions__item suggestions__product" href="<?=$conf->BaseRoot2.'search_click/'.$res->product_id.'/'.$ths->GeneratePass($res->product_id).'/'.(isset($_REQUEST['Srch']) ? $_REQUEST['Srch'] : '')?>">
                        <div class="suggestions__product-image">

                            <?php
                                if($res->img_id){
                                    $MyImg = $FileClass->get_by_id($res->img_id, 1);
                                    $MyImgUrl2 = '';
                                    if($MyImg && is_array($MyImg)){
                                     $MyImgUrl2 = dirname($MyImg['path2']).'/'.$MyImg['fileid'].'_tiny_'.$MyImg['filename'];
                                    
                                                  if(!file_exists($MyImgUrl2) && file_exists($MyImg['path'])){

                                                     include_once($conf->BaseRoot.'/classes/class.resize.php');
                                                        $resize_image = new Resize_Image;
                                            
                                                        $resize_image->resizeMainImage(dirname($MyImg['path']).'/', dirname($MyImg['path']).'/', $MyImg['fileid'].'_'.$MyImg['filename'], $MyImg['fileid'].'_tiny_'.$MyImg['filename'], 60, 60, 100);
                                                  
                                                
                                                  }
                                    }
                                }else{
                                    $MyImgUrl = $conf->BaseRoot2.'MyFile/Product/none.jpg';
                                }
                            ?>
                            <img loading="lazy" data-src="<?=$MyImgUrl2?>" alt="<?=$res->title.($res->model ? ' - '.$res->model : '')?>" style="width:60px">

                            <!--<img src="images/products/product-2-40x40.jpg" alt="">-->
                        </div>
                        <div class="suggestions__product-info">
                            <div class="suggestions__product-name">
                                <?=$res->title.($res->model ? ' مدل '.$res->model : '')?>
                            </div>
                            <div class="suggestions__product-rating">
                                <!--<div class="suggestions__product-rating-stars">
                                    <div class="rating">
                                        <div class="rating__body">
                                            <?php
                                                $MyRate = $MyRate0 = (round($res->score) / 10) * 5;
                                                for($r = 1; $r<=5; $r++){
                                                    $Class = '';
                                                    if($MyRate >= 1){
                                                        $Class = 'rating__star--active';
                                                        $MyRate--;
                                                    }elseif($MyRate >= 0.5){
                                                        $Class = 'rating__star--active';
                                                        $MyRate = 0;
                                                    }
                                            ?>
                                                <div class="rating__star <?=$Class?>"></div>
                                            <?php
                                                }
                                            ?>-->
                                            
                                            <div class="rating__star on"></div>
                                            
                                        <!--</div>
                                    </div>
                                </div>-->
                                
                                <div class="suggestions__product-rating-label">
                                    <?='';//round($res->score / 2)?>
                                    
                                    <?=(round($res->score / 2 , 1))?>
                                     از
                                    <?=$res->score_person?>
                                    بررسی
                                </div>
                            </div>
                        </div>
                    </a>
        <?php
                }
            }else{
        ?>
                <div class="suggestions__product-info">
                    <div class="suggestions__product-name text-center p-5">
                        هیچ موردی یافت نشد
                    </div>
                </div>
        <?php
            }
        ?>
    </div>
</div>

