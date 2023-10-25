
<?php
session_set_cookie_params(['samesite' => 'None']);
@session_start();
/**
 * Created by PhpStorm.
 * User: moradi
 * Date: 8/18/23
 * Time: 10:00 AM
 */

global $ths, $conf, $n, $MyPropCnt;

include_once($conf->BaseRoot.'/classes/libs/class.files.php');
$FilesClass = new files();

include_once($conf->BaseRoot.'/classes/libs/class.product_rel.php');
$ProdRelClass = new product_rel();

include_once($conf->BaseRoot.'/classes/libs/class.brand.php');
$BrandClass = new brand();

include_once($conf->BaseRoot.'/classes/libs/class.category.php');
$CatClass = new category();

include_once($conf->BaseRoot.'/classes/libs/class.comment.php');
$CommentClass = new comment();

include_once($conf->BaseRoot.'/classes/libs/class.company.php');
$CompanyClass = new company();

include_once($conf->BaseRoot.'/classes/libs/class.product_company.php');
$ProdCompClass = new product_company();

include_once($conf->BaseRoot.'/classes/libs/class.adv.php');
$AdvClass = new adv();

include_once($conf->BaseRoot.'/classes/libs/class.properties.php');
$PropClass = new properties();

include_once($conf->BaseRoot.'/classes/libs/class.person.php');
$PersonClass = new person();

include_once($conf->BaseRoot.'classes/libs/class.garanti.php');
$GarantiClass = new garanti();

include_once($conf->BaseRoot.'classes/libs/class.product_text.php');
$ProdTxtClass = new product_text();

include_once($conf->BaseRoot.'classes/libs/class.product_company.php');
$ProdCompanyClass = new product_company();

include_once($conf->BaseRoot.'classes/libs/class.product_price.php');
$productPriceClass = new product_price();


$MyGiftType = ['Discount'=>'تخفیف (درصد)', 'Credit'=> 'اعتبار هدیه (تومان)','GiftCard'=> 'کارت هدیه (تومان)', 'Score'=>'امتیاز هدیه', 'GiftPlus'=>'هدیه غیر نقدی'];
$MyGiftType2 = [1=>'تخفیف درصد', 2=>'تومان اعتبار هدیه ', 3=> 'تومان کارت هدیه ', 4=>'امتیاز هدیه', 5=>'به عنوان هدیه '];


$AdvArr = [];
$Cnd2 = [];
$Cnd2[] = [$AdvClass->MyTable.'`.`lang', $_SESSION['_Lang_']];
$Cnd2[] = [$AdvClass->MyTable.'`.`display', 1];
$Cnd2[] = [$AdvClass->MyTable.'`.`deleted', 0];
$Cnd2[] = [$AdvClass->MyTable.'`.`start_date', date('Y-m-d H:i:s'), '<='];
$Cnd2[] = [$AdvClass->MyTable.'`.`end_date', date('Y-m-d H:i:s'), '>='];
$Cnd2[] = [$AdvClass->MyTable.'`.`link', $_REQUEST['MyID']];

$AdvArr_ = $AdvClass->get_all($Cnd2, ['gift', 'type', 'end_date']);
if($AdvArr_){
    foreach($AdvArr_ as $a){
        $AdvArr = ['gift'=>$ths->MyDecode($a->gift), 'type'=>$a->type, 'end_date'=>$a->end_date];
    }
}


if(isset($AdvArr['type']) && is_array($AdvArr['type']) && in_array(11, $AdvArr['type'])){
    $ths->MyRedirect($conf->BaseRoot2.'category/'.$n->cat_id.'/'.$ths->UrlFriendly($CatClass->get_title_by_id($n->cat_id)), false);
    exit;
}



$CacheName = 'Product/'.$_REQUEST['MyID'];
$CacheFile = $ths->is_cache($CacheName);
if($_REQUEST['MyID']!=15646 && $CacheFile){
    echo $CacheFile;
}else{
    ob_start();

    $prod_id = $_REQUEST['MyID'];
    $_REQUEST['MyID'] = $n->product_id;
    $Descriptions = $n->comment;
    global $MyProductPrice;

    if(!$MyProductPrice){

        echo $ths->LoadPage($conf->UserPanelUrl . 'Product/_offerprod');
    }

    $bsktid = '';
    $bsktprop = '';
    $ItemID = $_REQUEST['MyID'];

    if(isset($_SESSION['_BasketItems_'])){
        $cntprod = '';
        foreach($_SESSION['_BasketItems_'] as $bskti=>$bskt){
            if($bskti == 'TotalPrice' || $bskti == 'TotalCount'){continue;}
            $bsktid = $bskt['id'];
            $bsktprop = $bskt['prop'];
            $cntprod = $bskt['count'];
        }
    }
    ?>

    <style>
        .OldPrice{font-size:16px;}
        .OldPrice span{font-size:12px;}
    </style>



    <div class="block-split">
        <div class="container">
            <div class="block-split__row row no-gutters">
                <div class="block-split__item block-split__item-content col-auto">
                    <div class="product product--layout--full">
                        <div class="product__body">
                            <div class="product__card product__card--one"></div>
                            <div class="product__card product__card--two"></div>
                            <div class="product-gallery product-gallery--layout--product-full product__gallery" data-layout="product-full">



                                <ul class="c-gallery__options">



                                    <li>

                                        <button id="add-to-favorite-button" onclick="AddToFav(this)" class="btn-option btn-option--wishes " aria-label="option wishes">


                                            <svg width="20" version="1.1" id="Layer_11" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                                                 viewBox="0 0 24 24" style="enable-background:new 0 0 24 24;" xml:space="preserve">
    <style type="text/css">
        .st0{display:none;}
    </style>
                                                <path class="st0" d="M19.5,8.3c-1.1,0-2.1,0.5-2.8,1.4L8.6,5.2c0-0.2,0.1-0.4,0.1-0.7c0-2-1.6-3.6-3.6-3.6S1.4,2.5,1.4,4.5
        S3,8.1,5,8.1c1.3,0,2.5-0.7,3.1-1.8l8,4.4c-0.1,0.4-0.2,0.8-0.2,1.2s0.1,0.8,0.2,1.1l-8.6,4.6c-0.6-1-1.8-1.7-3-1.7
        c-2,0-3.6,1.6-3.6,3.6s1.6,3.6,3.6,3.6s3.6-1.6,3.6-3.6c0-0.3,0-0.5-0.1-0.8l8.7-4.6c0.7,0.9,1.7,1.4,2.8,1.4c2,0,3.6-1.6,3.6-3.6
        S21.5,8.3,19.5,8.3z"/>
                                                <g>
                                                    <path d="M9.2,3.6L9.2,3.6L9.2,3.6l0.2-0.3L9.2,3.6z M9.2,3.6L9.2,3.6L9.2,3.6l0.2-0.3L9.2,3.6z"/>
                                                    <path d="M9.2,3.6L9.2,3.6L9.2,3.6l0.2-0.3L9.2,3.6z M9.2,3.6L9.2,3.6L9.2,3.6l0.2-0.3L9.2,3.6z M9.2,3.6L9.2,3.6L9.2,3.6l0.2-0.3
            L9.2,3.6z M9.2,3.6L9.2,3.6L9.2,3.6l0.2-0.3L9.2,3.6z M9.2,3.6L9.2,3.6L9.2,3.6l0.2-0.3L9.2,3.6z M9.2,3.6L9.2,3.6L9.2,3.6l0.2-0.3
            L9.2,3.6z"/>
                                                    <path d="M22.1,3.2c0,0-0.1-0.1-0.1-0.1l0,0l0,0l-0.2-0.3l0,0v0l0,0c-0.1-0.1-0.2-0.3-0.4-0.4c-1.1-1.1-2.7-1.6-4.3-1.6h0
            c-1.4,0-2.6,0.5-3.6,1.2l0,0h0c-0.5,0.4-0.9,0.8-1.3,1.3C12.1,3.4,12,3.5,12,3.6c-0.1-0.1-0.1-0.2-0.2-0.3C11.4,2.8,11,2.4,10.5,2
            l0,0h0C9.5,1.1,8.2,0.7,6.8,0.7c-2,0-3.7,0.9-4.9,2.3l0,0C0.8,4.4,0,6.3,0,8.3v0c0,2.1,0.8,4.1,2.6,6.4l0,0l0,0
            c0.1,0.1,0.1,0.2,0.2,0.3c1.8,2.2,4.5,4.7,8.5,8.1h0l0.4,0.3l0.1,0.1l0.1,0.1l0.6-0.5h0v0c4.5-3.8,7.3-6.5,9-8.7l0,0v0
            c0.8-1.1,1.4-2.1,1.8-3.2l0,0v0l0.1-0.2c0.4-1,0.4-1.9,0.4-2.7v0C23.9,6.3,23.3,4.5,22.1,3.2z M6.7,3.1c0.8,0,1.5,0.3,2.2,0.8
            c0,0,0,0,0,0l0,0l0.1,0l0,0h0c0.6,0.4,1.1,1.1,1.6,2.1l1.2,2.5L13,6.1c0.4-1,0.9-1.5,1.6-2.1l0,0c0,0,0,0,0,0
            c0.7-0.5,1.5-0.8,2.3-0.8c1.3,0,2.4,0.6,3.1,1.4v0c0.8,0.9,1.2,2.2,1.4,3.7v0.1c0,0.2,0,0.5,0,0.7c0,0.5-0.1,0.8-0.3,1.4
            c0,0,0,0,0,0.1c-0.8,2-3.2,5-9.3,10c-3.7-3.1-6.1-5.5-7.6-7.3l0,0c-1.5-2-2-3.4-2-4.9v0c0-1.5,0.5-2.8,1.3-3.7
            C4.3,3.7,5.4,3.1,6.7,3.1z M9.4,3.3L9.2,3.6l0,0h0L9.4,3.3z"/>
                                                </g>
                                                <path class="st0" d="M21.9,3.3h-8.4V0.6h-3.1v2.7h-8c-0.6,0-1,0.5-1,1v15.5c0,0.6,0.5,1,1,1h8v2.5h3.1v-2.5h8.4c0.6,0,1-0.5,1-1V4.4
        C22.9,3.8,22.5,3.3,21.9,3.3z M20.1,18.3h-6.6V5.6h6.6V18.3z"/>
    </svg>


                                        </button>


                                        <span class="c-tooltip c-tooltip--left c-tooltip--short">افزودن به علاقه‌مندی</span></li>





                                    <li><button id="myBtn" class="btn-option btn-option--social js-btn-option--social" aria-label="option social">

                                            <svg width="20" version="1.1" id="Layer_12" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                                                 viewBox="0 0 24 24" style="enable-background:new 0 0 24 24;" xml:space="preserve">
    <path d="M19.5,8.3c-1.1,0-2.1,0.5-2.8,1.4L8.6,5.2c0-0.2,0.1-0.4,0.1-0.7c0-2-1.6-3.6-3.6-3.6S1.4,2.5,1.4,4.5c0,2,1.6,3.6,3.6,3.6
        c1.3,0,2.5-0.7,3.1-1.8l8,4.4c-0.1,0.4-0.2,0.8-0.2,1.2c0,0.4,0.1,0.8,0.2,1.1l-8.6,4.6c-0.6-1-1.8-1.7-3-1.7c-2,0-3.6,1.6-3.6,3.6
        c0,2,1.6,3.6,3.6,3.6s3.6-1.6,3.6-3.6c0-0.3,0-0.5-0.1-0.8l8.7-4.6c0.7,0.9,1.7,1.4,2.8,1.4c2,0,3.6-1.6,3.6-3.6
        C23.1,9.9,21.5,8.3,19.5,8.3z"/>
    </svg>


                                        </button><span class="c-tooltip c-tooltip--left c-tooltip--short">اشتراک گذاری</span></li>




                                    <li>

                                        <button onclick="AddToCompare('<?=$n->product_id?>')" class="btn-option btn-option--social js-btn-option--social " aria-label="option social">


                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="14" class="compareic"><path d="M9 15H7c-.6 0-1-.4-1-1V2c0-.6.4-1 1-1h2c.6 0 1 .4 1 1v12c0 .6-.4 1-1 1zM1 9h2c.6 0 1 .4 1 1v4c0 .6-.4 1-1 1H1c-.6 0-1-.4-1-1v-4c0-.6.4-1 1-1zM15 5h-2c-.6 0-1 .4-1 1v8c0 .6.4 1 1 1h2c.6 0 1-.4 1-1V6c0-.6-.4-1-1-1z"></path></svg>


                                        </button>


                                        <span class="c-tooltip c-tooltip--left c-tooltip--short">مقایسه</span></li>



                                </ul>

                                <!-- The Modal by Dr.Hossein Moradi-->
                                <div id="myModal" class="modal1">

                                    <!-- Modal content by Dr.Hossein Moradi -->
                                    <div class="modal-content1">
                                        <div class="modal-header1">
                                            <span class="close1">&times;</span>
                                            <h2>اشتراک‌گذاری</h2>
                                        </div>
                                        <div class="modal-body">
                                            <p> با استفاده از روش‌های زیر می‌توانید این صفحه را با دوستان خود به اشتراک بگذارید. </p>
                                            <div class="c-share__options"><div class="c-share__social-buttons">

                                                    <a href="https://telegram.me/share/url?text=Custom+Text&url=<?=$conf->BaseRoot2.'product/'.$n->product_id.'/'.$ths->UrlFriendly($n->title)?>" class="o-btn c-share__social c-share__social--telegram" target="_blank" rel="noreferrer">

                                                        <svg width="24" version="1.1" id="Layer_13" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                                                             viewBox="0 0 24 24" style="enable-background:new 0 0 24 24;" xml:space="preserve">
    <path d="M23.5,3.6C23.2,3.3,22.7,3,22.3,3c-0.3,0-0.6,0-0.9,0.1L1.3,10.9c-1,0.4-1.2,1-1.2,1.3c0,0.3,0.1,0.9,1.3,1.3l0,0l4.2,1.2
        l2.2,6.4c0.3,0.9,1,1.5,1.8,1.5c0.4,0,1-0.1,1.5-0.6l2.5-2.4l3.7,3l0,0l0,0l0,0c0.4,0.3,0.9,0.4,1.3,0.4l0,0c0.9,0,1.5-0.6,1.8-1.6
        l3.4-16.2C23.9,4.6,23.9,4.1,23.5,3.6z M6.9,14.5l8-4l-4.9,5.2c-0.1,0.1-0.1,0.3-0.1,0.4L9,20L6.9,14.5z M10.2,21
        C10.1,21,10.1,21,10.2,21l0.7-3.6l1.6,1.3L10.2,21z M22.4,4.9l-3.3,16.2c0,0.1-0.1,0.4-0.4,0.4c-0.1,0-0.3,0-0.4-0.1L13.9,18l0,0
        l-2.5-2.1l7.3-7.7c0.3-0.3,0.3-0.6,0-0.9C18.5,7,18.1,7,17.8,7.2L6.1,13.4l-4.2-1.2L22,4.5c0.1,0,0.3-0.1,0.3-0.1h0.1
        C22.4,4.5,22.6,4.6,22.4,4.9z"/>
    </svg>


                                                    </a>

                                                    <a href="https://twitter.com/intent/tweet?url=<?=$conf->BaseRoot2.'product/'.$n->product_id.'/'.$ths->UrlFriendly($n->title)?>" class="o-btn c-share__social c-share__social--twitter" target="_blank" rel="noreferrer">

                                                        <svg width="24" version="1.1" id="Layer_14" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                                                             viewBox="0 0 24 24" style="enable-background:new 0 0 24 24;" xml:space="preserve">
    <style type="text/css">
        .st0{fill:#010101;}
    </style>
                                                            <path class="str0" d="M24,4.3c-1.5,0.1-1.5,0.1-1.6,0.1l0.9-2.6c0,0-2.8,1-3.5,1.2c-1.9-1.7-4.6-1.7-6.6-0.5c-1.6,1-2.5,2.7-2.2,4.7
        C7.8,6.8,5.2,5.3,3.1,2.8L2.4,2L1.9,2.9C1.3,4,1.1,5.4,1.3,6.6c0.1,0.5,0.3,1,0.5,1.4L1.3,7.8L1.2,8.8c-0.1,1,0.3,2.1,0.9,3
        c0.2,0.3,0.4,0.5,0.7,0.8l-0.3,0l0.4,1.1c0.5,1.4,1.4,2.5,2.7,3.1c-1.3,0.5-2.3,0.9-4,1.4L0,18.7l1.4,0.8c0.5,0.3,2.5,1.3,4.3,1.6
        c4.2,0.7,8.9,0.1,12.1-2.7c2.7-2.4,3.6-5.9,3.4-9.4c0-0.5,0.1-1.1,0.4-1.5C22.3,6.6,24,4.3,24,4.3z M20.6,6.6
        c-0.5,0.7-0.7,1.5-0.7,2.4c0.2,3.6-0.8,6.4-2.9,8.3c-2.5,2.2-6.5,3.1-11,2.4c-0.8-0.1-1.7-0.4-2.4-0.7c1.4-0.5,2.5-0.9,4.3-1.8
        l2.5-1.2l-2.7-0.2c-1.3-0.1-2.4-0.7-3-1.7c0.4,0,0.7-0.1,1-0.2l2.6-0.7l-2.6-0.6c-1.3-0.3-2-1.1-2.4-1.6C3,10.6,2.8,10.2,2.7,9.9
        C3,9.9,3.3,10,3.8,10l2.4,0.2L4.3,8.8C2.9,7.7,2.3,6.1,2.8,4.5c4.3,4.5,9.3,4.1,9.9,4.2c-0.1-1.1-0.1-1.1-0.1-1.2
        c-0.7-2.3,0.8-3.5,1.4-3.9c1.3-0.8,3.5-1,5,0.4c0.3,0.3,0.8,0.4,1.2,0.3c0.4-0.1,0.7-0.2,1-0.3l-0.6,1.7l0.8,0
        C21,6.1,20.8,6.3,20.6,6.6z"/>
    </svg>


                                                    </a><a href="https://www.facebook.com/sharer/sharer.php?m2w&amp;s=100&amp;p[url]=<?=$conf->BaseRoot2.'product/'.$n->product_id.'/'.$ths->UrlFriendly($n->title)?>" class="o-btn c-share__social c-share__social--fb" target="_blank" rel="noreferrer">

                                                        <svg width="24" version="1.1" id="Layer_15" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                                                             viewBox="0 0 24 24" style="enable-background:new 0 0 24 24;" xml:space="preserve">
    <style type="text/css">
        .st0{fill:#010101;}
    </style>
                                                            <g id="XMLID_834_">
                                                                <path id="XMLID_835_" class="str0" d="M7.3,12.7h2.2v10.1c0,0.2,0.1,0.4,0.3,0.4h3.7c0.2,0,0.3-0.2,0.3-0.4v-10h2.5
            c0.2,0,0.3-0.1,0.3-0.3L17,8.8c0-0.1,0-0.2-0.1-0.3c-0.1-0.1-0.1-0.1-0.2-0.1h-2.9V6c0-0.7,0.3-1.1,1-1.1c0.1,0,1.9,0,1.9,0
            C16.8,5,17,4.8,17,4.6V1.2c0-0.2-0.1-0.4-0.3-0.4h-2.6c0,0-0.1,0-0.1,0c-0.4,0-2,0.1-3.2,1.4C9.4,3.6,9.6,5.3,9.6,5.6v2.7H7.3
            C7.2,8.4,7,8.5,7,8.7v3.7C7,12.6,7.2,12.7,7.3,12.7z"/>
                                                            </g>
    </svg>

                                                    </a><a href="https://wa.me?text=<?=$conf->BaseRoot2.'product/'.$n->product_id.'/'.$ths->UrlFriendly($n->title)?>" class="o-btn c-share__social c-share__social--whatsapp" target="_blank" rel="noreferrer">


                                                        <svg width="24" version="1.1" id="Layer_16" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                                                             viewBox="0 0 24 24" style="enable-background:new 0 0 24 24;" xml:space="preserve">
    <style type="text/css">
        .st0{fill:#010101;}
    </style>
                                                            <g>
                                                                <g>
                                                                    <path class="str0" d="M20.3,3.6c-1.1-1.1-2.4-2-3.8-2.5c-1.4-0.6-2.9-0.9-4.5-0.9S8.9,0.5,7.5,1.1C6.1,1.6,4.8,2.5,3.7,3.6
                S1.7,6,1.1,7.4c-0.6,1.4-0.9,2.9-0.9,4.5v0c0,1.9,0.5,3.8,1.4,5.5l0,0.1l-1.4,6.3l6.3-1.4l0.1,0c1.6,0.8,3.5,1.3,5.3,1.3
                c3.1,0,6.1-1.2,8.3-3.4c1.1-1.1,2-2.4,2.6-3.8c0.6-1.4,0.9-2.9,0.9-4.5C23.8,8.8,22.5,5.9,20.3,3.6z M12,22.1
                c-1.7,0-3.4-0.4-4.9-1.2l-0.2-0.1l-4.6,1l1-4.5L3.1,17c-0.9-1.6-1.4-3.4-1.4-5.1c0-2.8,1.2-5.4,3-7.2c1.9-1.8,4.5-3,7.3-3
                c1.4,0,2.7,0.3,3.9,0.8c1.2,0.5,2.4,1.3,3.3,2.2c1,1,1.8,2.1,2.3,3.3c0.5,1.2,0.8,2.5,0.8,3.9C22.3,17.5,17.7,22.1,12,22.1z"/>
                                                                </g>
                                                                <path class="str0" d="M8.7,6.6H8.2C8,6.6,7.7,6.7,7.5,7c-0.3,0.3-1,0.9-1,2.3s1,2.6,1.1,2.8c0.1,0.2,1.9,3,4.7,4.1
            c2.3,0.9,2.8,0.7,3.3,0.7c0.5,0,1.6-0.7,1.9-1.3c0.2-0.6,0.2-1.2,0.2-1.3c-0.1-0.1-0.3-0.2-0.5-0.3c-0.3-0.1-1.6-0.8-1.9-0.9
            c-0.3-0.1-0.4-0.1-0.6,0.1c-0.2,0.3-0.7,0.9-0.9,1.1c-0.2,0.2-0.3,0.2-0.6,0.1c-0.3-0.1-1.1-0.4-2.2-1.4c-0.8-0.7-1.4-1.6-1.5-1.9
            c-0.2-0.3,0-0.4,0.1-0.6c0.1-0.1,0.3-0.3,0.4-0.4c0.1-0.2,0.2-0.3,0.3-0.5c0.1-0.2,0-0.3,0-0.5c-0.1-0.1-0.6-1.5-0.8-2h0
            C9.1,6.7,8.9,6.6,8.7,6.6z"/>
                                                            </g>
    </svg>

                                                    </a><div onclick="myFunctions()" class="o-btn c-share__social c-share__social--email js-email-btn">


                                                        <svg width="24" version="1.1" id="Layer_17" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                                                             viewBox="0 0 24 24" style="enable-background:new 0 0 24 24;" xml:space="preserve">
    <style type="text/css">
        .str0{fill:#fff;}
    </style>
                                                            <path class="str0" d="M24,5.2c0-0.1,0-0.1,0-0.1c-0.1-0.7-0.7-1.3-1.5-1.3h-21C0.8,3.8,0.2,4.3,0,5c0,0,0,0.1,0,0.1c0,0,0,0.1,0,0.1
        v13.5c0,0,0,0.1,0,0.1c0,0.1,0,0.1,0,0.1c0.1,0.7,0.7,1.3,1.5,1.3h21c0.7,0,1.3-0.6,1.5-1.3c0,0,0-0.1,0-0.1c0,0,0-0.1,0-0.1L24,5.2
        C24,5.2,24,5.2,24,5.2z M22,5.3l-8.3,6.1L12,12.6l-1.7-1.2c0,0,0,0,0,0L2,5.3H22z M1.5,6.7L8.7,12l-7.2,5.3V6.7z M2,18.8l7.9-5.8
        l1.6,1.2c0.1,0.1,0.3,0.1,0.4,0.1c0.2,0,0.3,0,0.4-0.1l1.6-1.2l7.9,5.8H2z M22.5,17.3L15.3,12l7.2-5.3V17.3z"/>
    </svg>
                                                    </div></div>
                                                <div id="myDIV" class="js-email-row"><div class="c-share__email-row"><div class="c-share__email"><label class="o-form__field-container"><div class="o-form__field-frame"><input name="send_to_friend[email]" type="email" placeholder="آدرس ایمیل را وارد نمایید" value="" class="o-form__field js-input-field "></div></label><input type="hidden" name="send_to_friend[product_id]" value="3149233"></div><button type="submit" class="o-btn o-btn--contained-red-sm">ارسال</button></div></div>
                                                <div class="topline">
                                                    <input type="text" value="<?=$conf->BaseRoot2.'product/'.$n->product_id.'/'.trim($ths->UrlFriendly($n->title))?>" id="myInput">

                                                    <div class="tooltip1">
                                                        <button class="o-btn o-btn--outlined-gray-sm o-btn--copy c-share__link-btn js-copy-url" onclick="myFunction()" onmouseout="outFunc()">
                                                            <svg class="mrsvg" width="11" version="1.1" id="Layer_18" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                                                                 viewBox="0 0 16 16" style="enable-background:new 0 0 16 16;" xml:space="preserve">
    <style type="text/css">
        .str01{fill:#030305;}
    </style>
                                                                <g>
                                                                    <path class="str01" d="M13.1,0H4.5c-0.6,0-1,0.5-1,1v1.5H2.3c-0.6,0-1,0.5-1,1V15c0,0.6,0.5,1,1,1h8.6c0.6,0,1-0.5,1-1v-1.5h1.2
            c0.6,0,1-0.5,1-1V1.1C14.2,0.5,13.7,0,13.1,0z M11.2,15L11,15.3H2.2l0,0L2,15V3.5l0,0l0.2-0.2H11l0,0l0.2,0.2V15z M13.4,12.5
            L13.4,12.5l-0.2,0.2h-1.3V3.6c0-0.6-0.5-1-1-1H4.2V1l0,0l0.2-0.2h8.7l0,0L13.4,1V12.5z"/>
                                                                    <path class="str01" d="M9.7,5.5c0,0.2-0.2,0.4-0.4,0.4H3.8c-0.2,0-0.4-0.2-0.4-0.4c0-0.2,0.2-0.4,0.4-0.4h5.6
            C9.6,5.1,9.7,5.2,9.7,5.5z"/>
                                                                    <path class="str01" d="M9.7,8.9c0,0.2-0.2,0.4-0.4,0.4H3.8c-0.2,0-0.4-0.2-0.4-0.4c0-0.2,0.2-0.4,0.4-0.4h5.6
            C9.6,8.5,9.7,8.7,9.7,8.9z"/>
                                                                    <path class="str01" d="M9.7,12.1c0,0.2-0.2,0.4-0.4,0.4H3.8c-0.2,0-0.4-0.2-0.4-0.4c0-0.2,0.2-0.4,0.4-0.4h5.6
            C9.6,11.7,9.7,11.9,9.7,12.1z"/>
                                                                </g>
    </svg>
                                                            <span class="tooltiptext" id="myTooltip">کپی کردن لینک محصول</span>
                                                            کپی لینک
                                                        </button>
                                                    </div></div></div>

                                        </div>
                                        <div class="modal-footer d-none">
                                            <div class="c-share__referral"><div class="c-share__referral-content"><div class="c-share__referral-title">جایزه شما</div><div class="c-share__referral-desc">با دعوت دوستانتان، پس از اولین خریدشان، کدتخفیف و امتیاز هدیه بگیرید.</div><div class="o-btn o-btn--outlined-gray-sm o-btn--copy o-btn--full-width c-share__referral-code u-hidden js-copy-referral-code" data-copy=""></div><div class="o-btn o-btn--outlined-red-sm o-btn--full-width o-btn--r-voucher c-share__referral-code  js-get-referral-code">
                                                        دریافت کد تخفیف
                                                    </div></div><div style="margin-left:10px" class="c-share__referral-img"><img src="<?=$conf->BaseRoot2?>Files/images/reward%20%282%29.svg" width="150" height="150" alt="reward"></div></div>
                                        </div>
                                    </div>

                                </div>



                                <div class="product-gallery__featured">
                                    <button type="button" class="product-gallery__zoom" aria-label="gallery zoom">
                                        <svg width="24" height="24">
                                            <path d="M15,18c-2,0-3.8-0.6-5.2-1.7c-1,1.3-2.1,2.8-3.5,4.6c-2.2,2.8-3.4,1.9-3.4,1.9s-0.6-0.3-1.1-0.7

        c-0.4-0.4-0.7-1-0.7-1s-0.9-1.2,1.9-3.3c1.8-1.4,3.3-2.5,4.6-3.5C6.6,12.8,6,11,6,9c0-5,4-9,9-9s9,4,9,9S20,18,15,18z M15,2

        c-3.9,0-7,3.1-7,7s3.1,7,7,7s7-3.1,7-7S18.9,2,15,2z M16,13h-2v-3h-3V8h3V5h2v3h3v2h-3V13z"/>
                                        </svg>
                                    </button>
                                    <div class="owl-carousel easyzoom easyzoom--adjacent">
                                        <?php

                                        if($n->img_id){
                                            $MyImg = $FilesClass->get_all(1, $n->product_id);
                                        }else{
                                            $MyImgUrl = $conf->BaseRoot2.'MyFile/Product/none.jpg';
                                        }


                                        if(isset($MyImg) && is_array($MyImg)){
                                            foreach($MyImg as $imgi=>$img){
                                                $MyImgUrl = dirname($img['path2']).'/'.$img['fileid'].'_'.$img['filename'];
                                                $MyImgUrl2 = dirname($img['path2']).'/'.$img['fileid'].'_thumb_'.$img['filename'];
                                                ?>

                                                <a href="<?=$MyImgUrl?>" target="_blank" title="<?=$img['title']?>">

                                                    <img class=" w100" loading="lazy" data-src="<?=$MyImgUrl?>" width="360" height="360" alt="<?=($img['alt'] ? $img['alt'] : $n->title.' '.$n->model)?>">

                                                </a>
                                                <?php
                                            }
                                        }else{
                                            ?>
                                            <img src="<?=$MyImgUrl?>" style="width:360px !important" height="360"/>
                                            <?php
                                        }
                                        ?>
                                    </div>
                                </div>
                                <div class="product-gallery__thumbnails">
                                    <div class="owl-carousel mt-20-max768">
                                        <?php
                                        if(isset($MyImg) && is_array($MyImg)){
                                            foreach($MyImg as $imgi=>$img){
                                                $MyImgUrl = dirname($img['path2']).'/'.$img['fileid'].'_'.$img['filename'];

                                                $MyImgUrl2 = dirname($img['path2']).'/'.$img['fileid'].'_tiny_'.$img['filename'];

                                                if(!file_exists($MyImgUrl2) && file_exists($img['path'])){

                                                    include_once($conf->BaseRoot.'/classes/class.resize.php');
                                                    $resize_image = new Resize_Image;

                                                    $resize_image->resizeMainImage(dirname($img['path']).'/', dirname($img['path']).'/', $img['fileid'].'_'.$img['filename'], $img['fileid'].'_tiny_'.$img['filename'], 60, 60, 100);


                                                }
                                                ?>

                                                <a href="<?=$MyImgUrl?>" class="product-gallery__thumbnails-item" target="_blank" title="<?=$img['title']?>">
                                                    <img loading="lazy" data-src="<?=$MyImgUrl2?>" width="60" height="100%" alt="<?=($img['alt'] ? $img['alt'] : $n->title.' '.$n->model)?>">
                                                </a>
                                                <?php
                                            }
                                        }
                                        ?>
                                    </div>


                                </div>
                            </div>
                            <div class="product__header">
                                <div class="u-flex u-items-center">
                                    <div class="c-product__title-container--brand">
                                        <?php ($n->brand_id ? $BrandName = $BrandClass->get_title_by_id($n->brand_id) : true)?>
                                        <a href="<?=($n->brand_id ? $conf->BaseRoot2.'brand/'.$n->brand_id.'/'.$ths->UrlFriendly($BrandName) : '#')?>" class="c-product__title-container--brand-link">
                                            <?=($n->brand_id ? $BrandName : '- - -')?>
                                        </a>
                                        <span> / </span>
                                        <a class="c-product__title-container--brand-link" href="<?=($n->brand_id ? $conf->BaseRoot2.'category/'.$n->cat_id.'/1/'.$ths->UrlFriendly($CatClass->get_title_by_id($n->cat_id)).'?BrandID='.$n->brand_id : '#')?>">
                                            <?=$ths->UrlFriendly($CatClass->get_title_by_id($n->cat_id))?>

                                            <?=($n->brand_id ? $BrandName : '- - -')?>
                                        </a>
                                    </div>
                                </div>
                                <h1 class="product__title">
                                    <?=$n->title.' '.$n->model?>
                                </h1>
                                <?php


                                if(isset($_SESSION['_LoginRoleUser_']) && in_array(1, $_SESSION['_LoginRoleUser_'])){ ?><a class="avirayeshdaste" href="/_Admin-Part_/?pid=1113&MyID=<?=$n->product_id?>&part=1&Part=1">ویرایش دسته</a>

                                <?php } ?>
                                <?php if($n->title_en || $n->model_en){?>
                                    <div class="subtitle">
                                        <div class="x-iep">
                                        <span>
                                        <?=($n->title_en ? $n->title_en : '').' '.($n->model_en ? $n->model_en : '')?>
                                        </span>
                                        </div>
                                    </div>
                                <?php }?>

                                <div class="product__subtitle">
                                    <div class="product__rating ">
                                        <?php
                                        $MyScore = round($n->score / 2 , 1);
                                        ?>
                                        <div class="product-card__rating-label">
                                            <?=($MyScore ? '<div class="star on"></div>'.$MyScore : '')?>
                                            <!--از
                                            <?=$n->score_person?>
                                            نظر-->
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="product__main">
                                <div class="product__excerpt text-justify  disflexmax768px">
                                    <?php
                                    $IsSurprise = (isset($AdvArr['type']) && $AdvArr['type'] == 6);
                                    if($IsSurprise){
                                        ?>

                                        <div class="offert">
                                            <span class="color-primary">پیشنهاد شگفت انگیز! </span> :
                                        </div>
                                        <div class="c-product-box__amazing ss">
                                            <div class="c-product-box__timer js-counter" data-countdown="2021-02-04 00:00:00">
                                                <div class="block-sale__timer">
                                                    <?php
                                                    if(date('Y-m-d H:i:s') > $AdvArr['end_date']){
                                                        $MyDiff_ = 'زمان به پایان رسیده است';
                                                        $MyDiff = '';
                                                    }else{
                                                        $date1 = new DateTime($AdvArr['end_date']);
                                                        $date2 = new DateTime(date('Y-m-d H:i:s'));

                                                        $MyDiff_ = $date2->diff($date1)->format("%a:%h:%i:%s");
                                                        $MyDiff = explode(":", $MyDiff_);
                                                    }

                                                    $itmi = 1;
                                                    ?>
                                                    <input type="hidden" id="Timer<?=($itmi + 1)?>" class="Timer<?=($itmi + 1)?>" value="<?=$MyDiff_?>">
                                                    <div class="timer timer<?=($itmi + 1)?>">
                                                        <?php
                                                        if(isset($MyDiff) && is_array($MyDiff)){
                                                            ?>
                                                            <div class="timer__part1">
                                                                <div class="timer__part-value1 timer__part-value--seconds timer_seconds<?=($itmi + 1)?>">
                                                                    <?=sprintf('%02d', $MyDiff[3])?>
                                                                </div>
                                                                <div class="timer__part-label1">ثانیه</div>
                                                            </div>
                                                            <div>:</div>
                                                            <div class="timer__part1">
                                                                <div class="timer__part-value1 timer__part-value--minutes timer_minutes<?=($itmi + 1)?>">
                                                                    <?=sprintf('%02d', $MyDiff[2])?>
                                                                </div>
                                                                <div class="timer__part-label1">دقیقه</div>
                                                            </div>
                                                            <div>:</div>
                                                            <div class="timer__part1">
                                                                <div class="timer__part-value1 timer__part-value--hours  timer_hours<?=($itmi + 1)?>">
                                                                    <?=sprintf('%02d', $MyDiff[1])?>
                                                                </div>
                                                                <div class="timer__part-label1">ساعت</div>
                                                            </div>
                                                            <div>:</div>
                                                            <div class="timer__part1">
                                                                <div class="timer__part-value1 timer__part-value--days  timer_days<?=($itmi + 1)?>">
                                                                    <?=$MyDiff[0]?>
                                                                </div>
                                                                <div class="timer__part-label1">روز</div>
                                                            </div>
                                                            <?php
                                                        }else{
                                                            ?>
                                                            <div class="timer__part w-100 p-2">
                                                                <div class="timer__part-value1">
                                                                    زمان به پایان رسیده است
                                                                </div>
                                                            </div>
                                                            <?php
                                                        }
                                                        ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="c-product-box__remained"></div>
                                        </div>
                                    <?php }?>
                                    <?php
                                    $CondTxt = [];
                                    $CondTxt[] = ['display', 1];
                                    $CondTxt[] = ['deleted', 0];
                                    $CondTxt[] = ["id`!=0 and ((`type`=1 and `rel_id`='".$n->cat_id."') or (`type`=2 and `rel_id`='".$n->brand_id."') or (`type`=3 and `rel_id`='".$n->product_id."')) and `id", 0, '!='];

                                    $ListTxt = $ProdTxtClass->get_all($CondTxt, ['content'], ['ordr', 'desc']);

                                    if( isset($AdvArr['gift'][2]) || isset($AdvArr['gift'][3]) || isset($AdvArr['gift'][4]) || isset($AdvArr['gift'][5]) ){
                                        ?>
                                        <div class="c-product__plus-box js-pdp-plus-box rounded">
                                            <?php
                                            for($i=2; $i<=5; $i++){
                                                if(isset($AdvArr['gift'][$i])){
                                                    ?>
                                                    <span class="c-product-box__digiplus-data c-digiplus-sign--before">
                                                    <img class="ngift" src="<?=$conf->BaseRoot2?>images/amazing.png" alt="gift card" title="کارت هدیه نقدی" width="24"  class="w-24px" >
                                                    <?=($i!=5 ? $ths->money($AdvArr['gift'][$i]) : $AdvArr['gift'][$i] )?>
                                                        <?=$MyGiftType2[$i]?>
                                                </span>
                                                    <?php
                                                }
                                            }
                                            ?>
                                        </div>
                                        <?php
                                    }
                                    ?>

                                </div>
                                <div class="product__features">

                                    <?php
                                    $MyProp = $PropClass->get_by_product($n->product_id);
                                    //var_dump($MyProp);
                                    $PropArr = [];
                                    $Ids = [];
                                    if($MyProp['All']){
                                        foreach($MyProp['All'] as $pri=>$pr){
                                            $Ids[] = $pr["prop_id"];
                                        }
                                    }
                                    $Ids = array_unique($Ids);

                                    if($Ids){
                                        $p_ = $PropClass->get_all([['prop_id', $Ids, 'in'], ['lang_id', 1], ['deleted', 0]]);
                                        if($p_){
                                            foreach($p_ as $p__){
                                                $PropArr[$p__->prop_id]['title'] = $p__->title;
                                                if($p__->type == 2){
                                                    $PropArr[$p__->prop_id]['val'] = $ths->MyDecode($p__->value_list);
                                                }else{
                                                    $PropArr[$p__->prop_id]['val'] = ['null'=>''];
                                                }
                                                $PropArr[$p__->prop_id]['unit'] = $p__->unit;
                                            }
                                        }
                                    }
                                    ?>

                                    <ul id="ul_o" class="uliop">
                                        <?php
                                        if($MyProp['IsMain']){
                                            foreach($MyProp['IsMain'] as $pri=>$pr){

                                                ?>
                                                <li class="js-more-attrs">
                                                    <span class="font-weight-bold"><?=$PropArr[$pr['prop_id']]['title']?>:</span>
                                                    <span>
                                                            <?php
                                                            $PropStr = [];
                                                            $v = $ths->MyDecode($pr["prop_val"]);
                                                            if($v){
                                                                foreach($v as $vi=>$vv){
                                                                    if(array_key_exists('null', $PropArr[$pr['prop_id']]["val"])){
                                                                        $PropStr[] = $vv.' '.$PropArr[$pr['prop_id']]['unit'];
                                                                    }else{
                                                                        $PropStr[] = $PropArr[$pr['prop_id']]["val"][$vv].' '.$PropArr[$pr['prop_id']]['unit'];
                                                                    }
                                                                }
                                                            }
                                                            echo (is_array($PropStr) ? implode(' | ', $PropStr) : ' - - -');
                                                            ?>
                                                        </span>
                                                </li>
                                                <?php
                                            }
                                        }
                                        ?>


                                    </ul>


                                    <br>




                                    <?php
                                    if($ListTxt){

                                        ?>
                                        <section class="task task--warning">
                                            <?php
                                            foreach($ListTxt as $ltxt){
                                                ?>
                                                <div class="lblock">
                                                    <svg class="icon x-s_ x-n_s margin-right-xxs" viewBox="0 0 24 24" aria-hidden="true"><path d="M12,0C5.383,0,0,5.383,0,12s5.383,12,12,12s12-5.383,12-12S18.617,0,12,0z M14.658,18.284 c-0.661,0.26-2.952,1.354-4.272,0.191c-0.394-0.346-0.59-0.785-0.59-1.318c0-0.998,0.328-1.868,0.919-3.957 c0.104-0.395,0.231-0.907,0.231-1.313c0-0.701-0.266-0.887-0.987-0.887c-0.352,0-0.742,0.125-1.095,0.257l0.195-0.799 c0.787-0.32,1.775-0.71,2.621-0.71c1.269,0,2.203,0.633,2.203,1.837c0,0.347-0.06,0.955-0.186,1.375l-0.73,2.582 c-0.151,0.522-0.424,1.673-0.001,2.014c0.416,0.337,1.401,0.158,1.887-0.071L14.658,18.284z M13.452,8c-0.828,0-1.5-0.672-1.5-1.5 s0.672-1.5,1.5-1.5s1.5,0.672,1.5,1.5S14.28,8,13.452,8z"></path></svg>
                                                    <strong>توجه! : </strong> <?=$ltxt->content?>
                                                </div>
                                                <?php
                                            }
                                            ?>
                                        </section>
                                        <?php
                                    }
                                    ?>
                                    <?php  if($MyPropCnt <= 3 && $MyPropCnt != 0){ ?>
                                        <div class="c-product__remaining-in-stock--parent"><div class="c-cart-notification  c-product__remaining-in-stock"><span>فقط
                    <?=$MyPropCnt?>
                </span>

                                                عدد در انبار باقیست - پیش از اتمام بخرید
                                            </div></div>
                                    <?php } ?>

                                    <div class="product__features-link disflexmax768px2">

                                        <a href="#product-tab-description" class="moshkame" >
<svg width="18" height="18"  xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 7.5h-.75A2.25 2.25 0 004.5 9.75v7.5a2.25 2.25 0 002.25 2.25h7.5a2.25 2.25 0 002.25-2.25v-7.5a2.25 2.25 0 00-2.25-2.25h-.75m-6 3.75l3 3m0 0l3-3m-3 3V1.5m6 9h.75a2.25 2.25 0 012.25 2.25v7.5a2.25 2.25 0 01-2.25 2.25h-7.5a2.25 2.25 0 01-2.25-2.25v-.75" />
</svg>


                                            مشخصات کامل را ببینید</a>

                                        <div class="ul_o float-left font-ka">
                                            <?php if($n->code){?>
                                                <span>شناسه کالا :</span><span class="CurrCode"><?=$n->code?></span> <span> - IKP</span>
                                            <?php }?>
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <?php
                            global $MyPropStr;
                            $_REQUEST['MyPropStr'] = $MyPropStr;
                            echo $ths->LoadPage($conf->UserPanelUrl.'Product/_LoadCompany');
                            ?>

                            <div id="sidebar" class="product__info">
                                <div id="cdtopsidebar" class="product__info-card " >
                                    <div class="product__info-body">
                                        <?php
                                        if(false && $IsSurprise){
                                            ?>
                                            <div class="product__badge tag-badge tag-badge--sale">
                                                شگفت انگیز
                                            </div>
                                        <?php }?>

                                        <div class="product__prices-stock disflexmax768px3">

                                            <div class="status-badge status-badge--style--success product__stock status-badge--has-text unavb CurrStock1">
                                                <div class="status-badge__body">
                                                    <div class="status-badge__icon">
                                                        <svg width="13" height="13">
                                                            <path d="M12,4.4L5.5,11L1,6.5l1.4-1.4l3.1,3.1L10.6,3L12,4.4z"></path>
                                                        </svg>
                                                    </div>

                                                    <div class="status-badge__text badespace">موجود در انبار</div>
                                                    <div class="status-badge__tooltip" tabindex="0" data-toggle="tooltip" title="" data-original-title="موجود در انبار" aria-describedby="موجود در انبار"></div>
                                                </div>
                                            </div>

                                            <!-- unavalable -->
                                            <div class="status-badge status-badge--style--success product__stock status-badge--has-text unavb CurrStock0">
                                                <div class="status-badge__body un">
                                                    <div class="status-badge__icon">
                                                        <svg width="13" height="13" viewBox="0 0 48 48">
                                                            <path fill="#F44336" d="M21.5 4.5H26.501V43.5H21.5z" transform="rotate(45.001 24 24)"></path>
                                                            <path fill="#F44336" d="M21.5 4.5H26.5V43.501H21.5z" transform="rotate(135.008 24 24)"></path>
                                                        </svg>
                                                    </div>

                                                    <div class="status-badge__text badespace">ناموجود</div>
                                                    <div class="status-badge__tooltip" tabindex="0" data-toggle="tooltip" title="ناموجود" data-original-title="ناموجود"></div>
                                                </div>
                                            </div>

                                        </div>


                                        <div class="product__meta disflexmax768px4">

                                            <div style="yuidssfds">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 80 80" style="width:24px;">
                                                    <path style="fill: #97750f;" d="M40 5.0058594C39.015961 5.0058594 38.032139 5.3780174 37.287109 6.1230469L35.257812 8.1542969L32.605469 7.0546875L32.603516 7.0546875C30.656636 6.2493141 28.398521 7.1839656 27.591797 9.1308594L27.585938 9.140625L26.476562 12L23.837891 12C21.731235 12 20 13.730155 20 15.837891L20 18.476562L17.142578 19.587891L17.132812 19.591797C15.18607 20.398459 14.247797 22.658176 15.054688 24.605469L16.154297 27.257812L14.125 29.287109C12.633769 30.776996 12.634208 33.222659 14.123047 34.712891L16.152344 36.742188L15.052734 39.394531L15.052734 39.396484C14.247361 41.343364 15.183966 43.601479 17.130859 44.408203L17.140625 44.412109L20 45.523438L20 49.162109C20 51.26942 21.73058 53 23.837891 53L23.939453 53L17.402344 68.183594L27.513672 67.060547L33.363281 75.607422L40.005859 59.988281L46.636719 75.609375L52.486328 67.060547L62.609375 68.185547L55.457031 51.835938L56 51.835938C58.106656 51.835938 59.837891 50.105783 59.837891 47.998047L59.837891 45.535156L62.851562 44.414062L62.869141 44.408203C64.815884 43.601541 65.752203 41.341824 64.945312 39.394531L63.845703 36.742188L65.876953 34.712891C67.367012 33.222832 67.367012 30.775215 65.876953 29.285156L63.845703 27.255859L64.945312 24.603516C65.750687 22.656636 64.816034 20.396568 62.869141 19.589844L62.859375 19.585938L60 18.476562L60 15.837891C60 13.731005 58.270075 12 56.162109 12L53.523438 12L52.412109 9.140625L52.408203 9.1308594C51.601535 7.1841204 49.341824 6.2477982 47.394531 7.0546875L44.742188 8.1542969L42.712891 6.1230469C41.967861 5.3780174 40.984039 5.0058594 40 5.0058594 z M 40 6.9941406C40.467961 6.9941406 40.935858 7.1760919 41.298828 7.5390625L44.273438 10.511719L48.160156 8.9023438C49.108863 8.5092331 50.167209 8.9472278 50.560547 9.8964844L52.15625 14L56.162109 14C57.190143 14 58 14.810776 58 15.837891L58 19.84375L62.103516 21.4375C63.052622 21.830776 63.490283 22.88877 63.097656 23.837891L61.488281 27.724609L64.460938 30.699219C65.186879 31.42516 65.186879 32.572887 64.460938 33.298828L61.488281 36.271484L63.097656 40.160156C63.490382 41.107933 63.052796 42.164339 62.105469 42.558594L57.837891 44.144531L57.837891 47.998047C57.837891 49.024311 57.025344 49.835938 56 49.835938L52.144531 49.835938L50.558594 54.103516C50.164402 55.050692 49.108347 55.489898 48.160156 55.097656L44.273438 53.486328L41.298828 56.460938C40.572887 57.186878 39.427113 57.186878 38.701172 56.460938L35.726562 53.486328L31.839844 55.097656C30.891137 55.490767 29.832791 55.050816 29.439453 54.101562L29.417969 54.052734L27.753906 51L23.837891 51C22.811201 51 22 50.188799 22 49.162109L22 44.15625L17.896484 42.560547C16.947378 42.167271 16.507764 41.109277 16.900391 40.160156L18.511719 36.273438L15.537109 33.298828C14.811996 32.573059 14.812248 31.427286 15.539062 30.701172L18.513672 27.726562L16.902344 23.839844C16.509233 22.891137 16.949183 21.832791 17.898438 21.439453L22 19.84375L22 15.837891C22 14.811627 22.812546 14 23.837891 14L27.845703 14L29.439453 9.8964844C29.832729 8.9473782 30.890723 8.5097171 31.839844 8.9023438L35.726562 10.511719L38.701172 7.5390625C39.064142 7.1760919 39.532039 6.9941406 40 6.9941406 z M 41.128906 13.056641L40.328125 13.458984L40.169922 14.339844L40.78125 14.994141L41.128906 15.056641L41.929688 14.65625L42.087891 13.773438L41.476562 13.119141L41.128906 13.056641 z M 36.626953 13.326172L35.826172 13.728516L35.667969 14.609375L36.28125 15.263672L36.626953 15.326172L37.429688 14.925781L37.585938 14.042969L36.974609 13.388672L36.626953 13.326172 z M 45.560547 13.884766L44.759766 14.287109L44.601562 15.167969L45.212891 15.822266L45.560547 15.884766L46.361328 15.484375L46.519531 14.601562L45.908203 13.947266L45.560547 13.884766 z M 32.34375 14.728516L31.542969 15.128906L31.384766 16.011719L31.996094 16.666016L32.34375 16.728516L33.144531 16.326172L33.302734 15.445312L32.689453 14.789062L32.34375 14.728516 z M 49.636719 15.814453L48.835938 16.216797L48.677734 17.097656L49.289062 17.751953L49.636719 17.814453L50.4375 17.414062L50.595703 16.53125L49.984375 15.876953L49.636719 15.814453 z M 28.527344 17.130859L27.726562 17.533203L27.568359 18.414062L28.179688 19.070312L28.527344 19.130859L29.328125 18.730469L29.486328 17.847656L28.873047 17.193359L28.527344 17.130859 z M 53.107422 18.689453L52.306641 19.091797L52.148438 19.974609L52.761719 20.628906L53.107422 20.689453L53.908203 20.289062L54.066406 19.40625L53.455078 18.751953L53.107422 18.689453 z M 25.451172 20.431641L24.650391 20.832031L24.492188 21.714844L25.105469 22.369141L25.451172 22.431641L26.253906 22.029297L26.410156 21.146484L25.798828 20.492188L25.451172 20.431641 z M 55.771484 22.330078L54.970703 22.730469L54.8125 23.613281L55.425781 24.267578L55.771484 24.330078L56.574219 23.927734L56.730469 23.044922L56.119141 22.390625L55.771484 22.330078 z M 49.939453 23.939453L37 36.878906L30.060547 29.939453L27.939453 32.060547L37 41.121094L52.060547 26.060547L49.939453 23.939453 z M 23.273438 24.376953L22.472656 24.779297L22.314453 25.660156L22.927734 26.316406L23.273438 26.376953L24.074219 25.976562L24.232422 25.09375L23.621094 24.439453L23.273438 24.376953 z M 57.417969 26.527344L56.615234 26.929688L56.458984 27.810547L57.070312 28.464844L57.417969 28.527344L58.21875 28.126953L58.376953 27.244141L57.763672 26.589844L57.417969 26.527344 z M 22.15625 28.746094L21.355469 29.146484L21.197266 30.029297L21.808594 30.683594L22.15625 30.746094L22.957031 30.34375L23.115234 29.462891L22.503906 28.808594L22.15625 28.746094 z M 58 31L57.199219 31.400391L57.199219 31.402344L57.041016 32.283203L57.652344 32.9375L58 33L58.800781 32.597656L58.958984 31.716797L58.347656 31.0625L58 31 z M 22.15625 33.253906L21.355469 33.65625L21.197266 34.537109L21.808594 35.191406L22.15625 35.253906L22.957031 34.853516L23.115234 33.970703L22.503906 33.316406L22.15625 33.253906 z M 57.417969 35.472656L56.615234 35.873047L56.458984 36.755859L57.070312 37.410156L57.417969 37.472656L58.21875 37.070312L58.376953 36.189453L57.763672 35.535156L57.417969 35.472656 z M 23.273438 37.623047L22.472656 38.023438L22.314453 38.90625L22.927734 39.560547L23.273438 39.623047L24.074219 39.220703L24.232422 38.339844L23.621094 37.685547L23.273438 37.623047 z M 55.771484 39.669922L54.970703 40.072266L54.8125 40.953125L55.425781 41.607422L55.771484 41.669922L56.574219 41.269531L56.730469 40.386719L56.119141 39.732422L55.771484 39.669922 z M 25.451172 41.568359L24.650391 41.970703L24.492188 42.853516L25.105469 43.507812L25.451172 43.568359L26.253906 43.167969L26.410156 42.285156L25.798828 41.630859L25.451172 41.568359 z M 53.107422 43.308594L52.306641 43.710938L52.148438 44.59375L52.761719 45.248047L53.107422 45.308594L53.910156 44.908203L54.066406 44.025391L53.455078 43.371094L53.107422 43.308594 z M 28.527344 44.869141L27.726562 45.269531L27.568359 46.152344L28.179688 46.806641L28.527344 46.869141L29.328125 46.466797L29.486328 45.585938L28.875 44.931641L28.527344 44.869141 z M 49.636719 46.185547L48.835938 46.585938L48.677734 47.46875L49.289062 48.123047L49.636719 48.185547L50.4375 47.783203L50.595703 46.902344L49.984375 46.248047L49.636719 46.185547 z M 32.34375 47.271484L31.542969 47.673828L31.384766 48.554688L31.996094 49.210938L32.34375 49.271484L33.144531 48.871094L33.302734 47.988281L32.689453 47.333984L32.34375 47.271484 z M 45.560547 48.115234L44.759766 48.515625L44.601562 49.398438L45.212891 50.052734L45.560547 50.115234L46.361328 49.712891L46.519531 48.832031L45.908203 48.175781L45.560547 48.115234 z M 36.628906 48.673828L35.826172 49.074219L35.669922 49.957031L36.28125 50.611328L36.628906 50.673828L37.429688 50.271484L37.587891 49.390625L36.974609 48.736328L36.628906 48.673828 z M 41.128906 48.943359L40.328125 49.34375L40.169922 50.226562L40.783203 50.880859L41.128906 50.943359L41.929688 50.541016L42.087891 49.660156L41.476562 49.005859L41.128906 48.943359 z M 53.416016 52.158203L59.390625 65.814453L51.513672 64.939453L47.048828 71.462891L41.603516 58.630859C42.004413 58.447204 42.384401 58.203489 42.712891 57.875L44.742188 55.845703L47.394531 56.945312L47.396484 56.945312C49.343364 57.750687 51.601479 56.814081 52.408203 54.867188L52.416016 54.849609L53.416016 52.158203 z M 26.117188 53L26.566406 53L27.636719 54.962891L27.591797 54.867188C28.398459 56.81393 30.658176 57.752202 32.605469 56.945312L35.257812 55.845703L37.287109 57.875C37.618835 58.206726 38.002841 58.452694 38.408203 58.636719L32.951172 71.462891L28.486328 64.939453L20.597656 65.816406L26.117188 53 z" fill="#5B5B5B" />
                                                </svg>
                                                گارانتی
                                                <?= ($n->garanti_id ? $GarantiClass->get_title_by_id($n->garanti_id) : 'گارانتی اصالت و سلامت کالا')?>
                                            </div>


                                        </div>

                                        <div class=" product-card__price product-card__price--old lowprice OldPrice text-center vbnvbn justify-content-center-lg"></div>
                                        
                                        <div class="product__prices">
                                            <div class="product__price product__price--current textcentermax768px" style="color : #1e293b;margin   : 5px 0 ">
                                                <!-- svg123 -->
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-basket" viewBox="0 0 16 16" style="
    margin: 0px 2px 4px 1px;
">
  <path d="M5.757 1.071a.5.5 0 0 1 .172.686L3.383 6h9.234L10.07 1.757a.5.5 0 1 1 .858-.514L13.783 6H15a1 1 0 0 1 1 1v1a1 1 0 0 1-1 1v4.5a2.5 2.5 0 0 1-2.5 2.5h-9A2.5 2.5 0 0 1 1 13.5V9a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h1.217L5.07 1.243a.5.5 0 0 1 .686-.172zM2 9v4.5A1.5 1.5 0 0 0 3.5 15h9a1.5 1.5 0 0 0 1.5-1.5V9H2zM1 7v1h14V7H1zm3 3a.5.5 0 0 1 .5.5v3a.5.5 0 0 1-1 0v-3A.5.5 0 0 1 4 10zm2 0a.5.5 0 0 1 .5.5v3a.5.5 0 0 1-1 0v-3A.5.5 0 0 1 6 10zm2 0a.5.5 0 0 1 .5.5v3a.5.5 0 0 1-1 0v-3A.5.5 0 0 1 8 10zm2 0a.5.5 0 0 1 .5.5v3a.5.5 0 0 1-1 0v-3a.5.5 0 0 1 .5-.5zm2 0a.5.5 0 0 1 .5.5v3a.5.5 0 0 1-1 0v-3a.5.5 0 0 1 .5-.5z"></path>
</svg>
                                                
                                                <!-- svg123 -->


                                                <span class="CurrPrice" style="color : #1e293b;">
                                                    <div class="loader ld">
                                                        <svg class="circular">
                                                            <circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10"/>
                                                        </svg>
                                                    </div>
                                                    <?php
                                                    global $MyProductPrice;
                                                    echo $ths->money($MyProductPrice);
                                                    ?>

                                                </span>
                                                <span>
                                                تومان
                                                </span>
                                            </div>
                                        </div>

                                        


                                    </div>
                                    <?php
                                    $SelPropStr = '';
                                    global $MyPropStr;
                                    $PropStr2 = $MyPropStr;
                                    $PropStr3 = $ths->MyDecode($PropStr2);

                                    if($MyProp['ForPrice']){

                                        ?>
                                        <div class="product-form product__form">
                                            <div class="product-form__body">
                                                <?php

                                                foreach($MyProp['ForPrice'] as $pri=>$pr){


                                                    ?>
                                                    <div class="product-form__row">
                                                        <div class="product-form__title">
                                                            <?=$PropArr[$pr['prop_id']]['title']?>
                                                        </div>
                                                        <div class="product-form__control">
                                                            <div class="input-radio-label">
                                                                <div class="input-radio-label__list">
                                                                    <?php
                                                                    $PropStr = [];
                                                                    $v = $ths->MyDecode($pr["prop_val"]);
                                                                    $com_id;
                                                                    $prid;
                                                                    $sprop;
                                                                    $propval = [];
                                                                    foreach($v as $vi=>$vv){
                                                                        $propval[] = '{'.$pr['prop_id'].':'.$vv.';}';
                                                                    }
                                                                    //var_dump($propval);
                                                                    $ARRp = [];
                                                                    $ARRp[] = ['product_id',$n->product_id];
                                                                    $ARRp[] = ['active',1];
                                                                    $ARRp[] = ['confirm',1];
                                                                    //$ARRp[] = ['prop', $propval];
                                                                    $valsprod = $productPriceClass->get_all($ARRp);
                                                                    /*$valsprod = $ths->GetData($ths->query("select * from `product_price` where `product_id`='".$n->product_id."' and `prop` IN('.$propval.');"));*/
                                                                    //var_dump($valsprod);
                                                                    $check = [];
                                                                    $comid= [];
                                                                    $prices = [];
                                                                    $comidmin = [];
                                                                    if(is_array($valsprod)){
                                                                        foreach($valsprod as $n=>$vp){
                                                                            if(in_array($vp->prop, $propval)){

                                                                                $comid[$vp->prop] = $vp->company_id;
                                                                                $prices[] = $vp->price_off;
                                                                            }

                                                                        }
                                                                    }
                                                                    //var_dump($prices);
                                                                    $minPrice = min($prices);
                                                                    //var_dump($minPrice);
                                                                    foreach($valsprod as $vc){
                                                                        if(in_array($vc->prop, $propval)){
                                                                            if($minPrice == $vc->price_off){
                                                                                $check[] = $vc->prop;
                                                                            }
                                                                        }
                                                                    }
                                                                    //var_dump($minPrice);
                                                                    foreach($valsprod as $vs){
                                                                        if(count($check) == 1){
                                                                            if($minPrice == $vs->price_off){
                                                                                $comidmin[$vs->prop] = $vs->company_id;
                                                                                $com_id = $comidmin[$vs->prop];
                                                                                $prid = $vs->product_id;
                                                                                $sprop = $vs->prop;
                                                                            }
                                                                        } elseif(count($check) > 1) {
                                                                            if($check[0] == $vs->prop){
                                                                                $comidmin[$vs->prop] = $vs->company_id;
                                                                                $com_id = $comidmin[$vs->prop];
                                                                                $prid = $vs->product_id;
                                                                                $sprop = $vs->prop;
                                                                            }
                                                                        }

                                                                    }

                                                                    $s0 = '<label class="input-radio-label__item"><input type="radio" name="prop_'.$pr['prop_id'].'" id="prop_'.$pr['prop_id'].'_###" value="###" class="input-radio-label__input" onclick="SetProp(\''.$pr['prop_id'].'\', \'###\', \'@@@\')"><span class="input-radio-label__title1 MyPropForPrice_'.$pr['prop_id'].' ***">';
                                                                    $s1 = '<label class="input-radio-label__item"><input type="radio" name="prop_'.$pr['prop_id'].'" id="prop_'.$pr['prop_id'].'_###" value="###" class="input-radio-label__input" onclick="SetProp(\''.$pr['prop_id'].'\', \'###\', \'@@@\')"><span class="input-radio-label__title1 MyPropForPrice_'.$pr['prop_id'].' ***">';
                                                                    $s3 = '</span></label>';

                                                                    foreach($v as $vi=>$vv){

                                                                        $is_first = false;
                                                                        if(array_key_exists($pr['prop_id'], $PropStr3) && $PropStr3[$pr['prop_id']] == $vv){
                                                                            $is_first = true;
                                                                        }
                                                                        //var_dump($comidmin['{'.$pr['prop_id'].':'.$vv.';}']);
                                                                        $PropStr[] = str_replace(array('###','@@@','***'), array($vv,(in_array($comidmin['{'.$pr['prop_id'].':'.$vv.';}'], $comid) ? $comidmin['{'.$pr['prop_id'].':'.$vv.';}'] : $comid['{'.$pr['prop_id'].':'.$vv.';}']),((in_array($comidmin['{'.$pr['prop_id'].':'.$vv.';}'], $comid) && $comidmin['{'.$pr['prop_id'].':'.$vv.';}'] != '') ? 'boxselect' : '')), ($is_first ? $s0 : $s1)).(isset($PropArr[$pr['prop_id']]["val"][$vv]) ? $PropArr[$pr['prop_id']]["val"][$vv] : '').' '.(isset($PropArr[$pr['prop_id']]['unit']) ? $PropArr[$pr['prop_id']]['unit'] : '').$s3;
                                                                        //$PropStr[] = str_replace('@@@', $comid['{'.$pr['prop_id'].':'.$vv.';}'], ($is_first ? $s0 : $s1)).(isset($PropArr[$pr['prop_id']]["val"][$vv]) ? $PropArr[$pr['prop_id']]["val"][$vv] : '').' '.(isset($PropArr[$pr['prop_id']]['unit']) ? $PropArr[$pr['prop_id']]['unit'] : '').$s3;
                                                                    }

                                                                    echo (is_array($PropStr) ? implode(' &nbsp; ', $PropStr) : ' - - -');
                                                                    ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <?php

                                                }
                                                ?>
                                            </div>
                                        </div>
                                        <?php
                                    }
                                                       
                                    ?>
                                    <input type="hidden" id="SelProp" name="SelProp" value="<?=($sprop ? $sprop : $PropStr2)?>">
                                    <input type="hidden" id="SelPropstr" name="SelPropstr" value="<?=$SelPropStr?>">
                                    <input type="hidden" id="MyID" name="ProdID" value="<?=$prod_id?>">
                                    <input type="hidden" id="MyCompany" name="MyCompany" value="<?=($com_id ? $com_id : $n->company_id)?>">

                                    <div class="Msg d-none text-center  mb-1">
                                        <div class="d-inline-block Msg0 badge badge-warning p-2">
                                        </div>
                                    </div>


                                    <div class="product__actions_3">
                                        <div class="product__actions">
                                            <div class="d-none product__actions-item product__actions-item--quantity">
                                                <div class="input-number">
                                                    <?php
                                                    $MyCnt = ((isset($_SESSION['_BasketItems_']) && isset($_SESSION['_BasketItems_'][$n->product_id.'-{'.$SelPropStr.'}'])) ? $_SESSION['_BasketItems_'][$n->product_id.'-{'.$SelPropStr.'}']['count'] : 0);
                                                    ?>
                                                    <label for="MyCount_<?=$ItemID?>">
                                                        <input id="MyCount_<?=$ItemID?>" name="MyCount_<?=$ItemID?>" class="input-number__input form-control form-control-lg" alt="<?=$MyCnt?>" type="number" min="1" value="<?=$MyCnt?>">
                                                    </label>
                                                    <div class="input-number__sub" onclick="UpdateMyCartpr('<?=$_REQUEST['MyID']?>', '<?=($sprop ? $sprop : $PropStr2)?>', '<?=($com_id ? $com_id : $n->company_id)?>', -1)">
                                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 26 26">
                                                            <path d="M22 15L4 15C3.398438 15 3 14.601563 3 14L3 12C3 11.398438 3.398438 11 4 11L22 11C22.601563 11 23 11.398438 23 12L23 14C23 14.601563 22.601563 15 22 15Z" fill="#5B5B5B" />
                                                        </svg>
                                                    </div>
                                                    <div class="input-number__add" onclick="UpdateMyCartpr('<?=$_REQUEST['MyID']?>', '<?=($sprop ? $sprop : $PropStr2)?>', '<?=($com_id ? $com_id : $n->company_id)?>', 1)">
                                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 26 26">
                                                            <path d="M12 3C11.398438 3 11 3.398438 11 4L11 11L4 11C3.398438 11 3 11.398438 3 12L3 14C3 14.601563 3.398438 15 4 15L11 15L11 22C11 22.601563 11.398438 23 12 23L14 23C14.601563 23 15 22.601563 15 22L15 15L22 15C22.601563 15 23 14.601563 23 14L23 12C23 11.398438 22.601563 11 22 11L15 11L15 4C15 3.398438 14.601563 3 14 3Z" fill="#5B5B5B" />
                                                        </svg>
                                                    </div>
                                                    <div class="d-none Msg_<?=$ItemID?> small badge badge-warning p-2"></div>
                                                </div>
                                            </div>
                                            <!--<div class="text-center w-100">
                                            <div class="d-none Msg_<?=$MyCnt?> small badge badge-warning p-2"></div>
                                        </div>-->
                                            <div class="addtobcs product__actions-item product__actions-item--addtocart">
                                                <button class="btn btn-primary btn-lg btn-block">
                                                <span class="x-ndc" class="maegintop3px">
                                                    <svg height="20" viewBox="0 0 512 512" width="20" xmlns="http://www.w3.org/2000/svg"><path d="m416.667969 361.492188h-25.167969v-25.167969c0-8.285157-6.714844-15-15-15s-15 6.714843-15 15v25.167969h-25.167969c-8.28125 0-15 6.714843-15 15 0 8.285156 6.71875 15 15 15h25.167969v25.164062c0 8.285156 6.714844 15 15 15s15-6.714844 15-15v-25.164062h25.167969c8.28125 0 15-6.714844 15-15 0-8.285157-6.71875-15-15-15zm0 0"/><path d="m461.191406 270.804688c28.386719-2.230469 50.808594-26.027344 50.808594-54.980469 0-30.417969-24.746094-55.167969-55.167969-55.167969h-17.082031l-52.441406-126.601562c-11.640625-28.101563-43.972656-41.496094-72.078125-29.855469-13.613281 5.640625-24.21875 16.242187-29.855469 29.855469-5.640625 13.613281-5.636719 28.609374.003906 42.222656l34.949219 84.378906h-128.652344l34.949219-84.378906c11.640625-28.105469-1.753906-60.4375-29.855469-72.078125-28.105469-11.640625-60.441406 1.753906-72.078125 29.855469l-52.441406 126.601562h-17.082031c-30.417969 0-55.167969 24.75-55.167969 55.167969 0 25.816406 17.828125 47.539062 41.816406 53.523437l25.308594 189.8125c1.890625 14.195313 9.1875 27.484375 20.535156 37.421875s25.488282 15.410157 39.808594 15.410157h249.03125c74.714844 0 135.5-60.785157 135.5-135.5 0-42.695313-19.855469-80.835938-50.808594-105.6875zm-148.097656-206.007813c-2.574219-6.210937-2.574219-13.050781-.003906-19.261719 2.574218-6.210937 7.410156-11.046875 13.621094-13.617187 12.820312-5.308594 27.574218.800781 32.882812 13.617187l47.683594 115.121094h-54.476563zm-160.6875-19.261719c5.3125-12.816406 20.0625-18.925781 32.882812-13.617187 12.820313 5.308593 18.929688 20.058593 13.617188 32.882812l-39.703125 95.855469h-54.480469zm-122.40625 170.289063c0-13.878907 11.289062-25.167969 25.167969-25.167969h27.019531c.027344 0 .054688.003906.082031.003906.035157 0 .070313-.003906.105469-.003906h86.890625.113281 173.210938.1875 86.847656c.039062 0 .074219.003906.109375.003906.023437 0 .050781-.003906.078125-.003906h27.019531c13.878907 0 25.167969 11.289062 25.167969 25.167969 0 13.875-11.289062 25.167969-25.167969 25.167969h-401.664062c-13.878907 0-25.167969-11.292969-25.167969-25.167969zm97.46875 266.167969c-14.652344 0-28.671875-12.273438-30.605469-26.796876l-24.5625-184.203124h219.273438c-30.820313 24.855468-50.574219 62.910156-50.574219 105.5 0 42.585937 19.753906 80.640624 50.574219 105.5zm249.03125 0c-58.171875 0-105.5-47.328126-105.5-105.5 0-58.175782 47.328125-105.5 105.5-105.5s105.5 47.324218 105.5 105.5c0 58.171874-47.328125 105.5-105.5 105.5zm0 0"/></svg>
                                                    افزودن به سبد خرید
                                                </button>

                                            </div>

                                            <div class="product__actions-item addtobcs2 d-none">
                                                <button onclick="tobas()" id="tobascket" class="btn btn-primary btn-lg btn-block d-block">
                                                <span class="x-ndc"  class="maegintop3px" >
                                                    <svg height="22" viewBox="0 0 512 512" width="25" xmlns="http://www.w3.org/2000/svg"><path d="m416.667969 361.492188h-25.167969v-25.167969c0-8.285157-6.714844-15-15-15s-15 6.714843-15 15v25.167969h-25.167969c-8.28125 0-15 6.714843-15 15 0 8.285156 6.71875 15 15 15h25.167969v25.164062c0 8.285156 6.714844 15 15 15s15-6.714844 15-15v-25.164062h25.167969c8.28125 0 15-6.714844 15-15 0-8.285157-6.71875-15-15-15zm0 0"/><path d="m461.191406 270.804688c28.386719-2.230469 50.808594-26.027344 50.808594-54.980469 0-30.417969-24.746094-55.167969-55.167969-55.167969h-17.082031l-52.441406-126.601562c-11.640625-28.101563-43.972656-41.496094-72.078125-29.855469-13.613281 5.640625-24.21875 16.242187-29.855469 29.855469-5.640625 13.613281-5.636719 28.609374.003906 42.222656l34.949219 84.378906h-128.652344l34.949219-84.378906c11.640625-28.105469-1.753906-60.4375-29.855469-72.078125-28.105469-11.640625-60.441406 1.753906-72.078125 29.855469l-52.441406 126.601562h-17.082031c-30.417969 0-55.167969 24.75-55.167969 55.167969 0 25.816406 17.828125 47.539062 41.816406 53.523437l25.308594 189.8125c1.890625 14.195313 9.1875 27.484375 20.535156 37.421875s25.488282 15.410157 39.808594 15.410157h249.03125c74.714844 0 135.5-60.785157 135.5-135.5 0-42.695313-19.855469-80.835938-50.808594-105.6875zm-148.097656-206.007813c-2.574219-6.210937-2.574219-13.050781-.003906-19.261719 2.574218-6.210937 7.410156-11.046875 13.621094-13.617187 12.820312-5.308594 27.574218.800781 32.882812 13.617187l47.683594 115.121094h-54.476563zm-160.6875-19.261719c5.3125-12.816406 20.0625-18.925781 32.882812-13.617187 12.820313 5.308593 18.929688 20.058593 13.617188 32.882812l-39.703125 95.855469h-54.480469zm-122.40625 170.289063c0-13.878907 11.289062-25.167969 25.167969-25.167969h27.019531c.027344 0 .054688.003906.082031.003906.035157 0 .070313-.003906.105469-.003906h86.890625.113281 173.210938.1875 86.847656c.039062 0 .074219.003906.109375.003906.023437 0 .050781-.003906.078125-.003906h27.019531c13.878907 0 25.167969 11.289062 25.167969 25.167969 0 13.875-11.289062 25.167969-25.167969 25.167969h-401.664062c-13.878907 0-25.167969-11.292969-25.167969-25.167969zm97.46875 266.167969c-14.652344 0-28.671875-12.273438-30.605469-26.796876l-24.5625-184.203124h219.273438c-30.820313 24.855468-50.574219 62.910156-50.574219 105.5 0 42.585937 19.753906 80.640624 50.574219 105.5zm249.03125 0c-58.171875 0-105.5-47.328126-105.5-105.5 0-58.175782 47.328125-105.5 105.5-105.5s105.5 47.324218 105.5 105.5c0 58.171874-47.328125 105.5-105.5 105.5zm0 0"/></svg>
                                                    مشاهده سبد خرید

                                                </button>
                                            </div>

                                            <input type="hidden" class="numbacs" value=""/>

                                        </div>
                                    </div>

                                    <div class="product__actions none768px">
                                        <div class="product__actions-divider"></div>
                                        <button onclick="AddToFav(this)" class="product__actions-item product__actions-item--wishlist buttona123 <?= ((isset($_SESSION['_LoginUserID_']) ? $PersonClass->in_fav($_SESSION['_LoginUserID_'], $n->product_id) : false) ? 'sbg' : '')?>" type="button" aria-label="wishlist">
                                            <svg width="16" height="16">
                                                <path d="M13.9,8.4l-5.4,5.4c-0.3,0.3-0.7,0.3-1,0L2.1,8.4c-1.5-1.5-1.5-3.8,0-5.3C2.8,2.4,3.8,2,4.8,2s1.9,0.4,2.6,1.1L8,3.7
        l0.6-0.6C9.3,2.4,10.3,2,11.3,2c1,0,1.9,0.4,2.6,1.1C15.4,4.6,15.4,6.9,13.9,8.4z"/>
                                            </svg>
                                            <span>علاقه مندی ها</span>
                                        </button>

                                        <?php
                                        $IsInCompare = (isset($_SESSION['CompareIDs']) && in_array($n->product_id, $_SESSION['CompareIDs']));
                                        ?>

                                        <p onclick="AddToCompare('<?=$n->product_id?>')" class="product__actions-item product__actions-item--compare buttona123 <?=($IsInCompare ? 'sbg' :'')?>" type="button">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" class=""><path d="M9 15H7c-.6 0-1-.4-1-1V2c0-.6.4-1 1-1h2c.6 0 1 .4 1 1v12c0 .6-.4 1-1 1zM1 9h2c.6 0 1 .4 1 1v4c0 .6-.4 1-1 1H1c-.6 0-1-.4-1-1v-4c0-.6.4-1 1-1zM15 5h-2c-.6 0-1 .4-1 1v8c0 .6.4 1 1 1h2c.6 0 1-.4 1-1V6c0-.6-.4-1-1-1z"></path></svg>
                                            <span> مقایسه</span>
                                        </p>
                                    </div>

                                    <div class="c-footer__product-id float-left-important" >
                                            <span style="font-size: .786rem;font-weight : 700">
                                        تامین کننده:
                                            </span>
                                        <span class="tamink">
                                                <?php
                                                $comp = $CompanyClass->get_by_id(($com_id ? $com_id : $n->company_id));
                                                /*           if(($com_id ? $com_id : $n->company_id) && $comp && $comp->img_id){
                                                               $img_comp = $FilesClass->get_by_id($comp->img_id, 10);
                                                               $MyImgUrl = dirname($img_comp['path2']).'/'.$img_comp['fileid'].'_'.$img_comp['filename'];
                                                               echo '<img src="'.$MyImgUrl.'" alt="'.$comp->title.'" title="'.$comp->title.'" style="width:70px">';
                                                           }else{*/
                                                echo $comp->title;
                                                //}
                                                ?>

                                            </span>
                                    </div>
                                </div>




                                <!-- onclick="UpdateMyCartpr('<?=''//$_REQUEST['MyID']?>', '<?=''//$PropStr2?>', 1)" -->

                                <div class="product__actions product__actions_2 lop rrttyyuu" ></div>







                                <div class="product__shop-features shop-features">
                                    <ul style="display:none;" class="shop-features__list">
                                        <li class="shop-features__item">
                                            <div class="shop-features__item-icon">
                                                <svg width="48" height="48" viewBox="0 0 48 48">
                                                    <path d="M44.6,26.9l-1.2-5c0.3-0.1,0.6-0.4,0.6-0.7v-0.8c0-1.7-1.4-3.2-3.2-3.2h-5.7v-1.7c0-0.9-0.7-1.6-1.6-1.6H23.1l6.4-2.6
        c0.4-0.2,0.6-0.6,0.4-1c-0.2-0.4-0.6-0.6-1-0.4l-5.2,2.1c1.6-1,3.2-2.2,3.8-2.9c1.2-1.5,0.9-3.7-0.7-4.9c-1.5-1.2-3.7-0.9-4.9,0.7
        l0,0c-0.9,1.1-2,4.3-2.7,6.5c-0.7-2.2-1.9-5.4-2.7-6.5l0,0c-1.2-1.5-3.4-1.8-4.9-0.7C10,5.5,9.7,7.7,10.9,9.2
        c0.6,0.8,2.2,1.9,3.8,2.9l-5.2-2.1c-0.4-0.2-0.8,0-1,0.4c-0.2,0.4,0,0.8,0.4,1l6.4,2.6H4.8c-0.9,0-1.6,0.7-1.6,1.6v13.6
        C3.2,29.6,3.5,30,4,30c0.4,0,0.8-0.3,0.8-0.8V15.6c0,0,0,0,0,0h28.9c0,0,0,0,0,0v13.6c0,0.4,0.3,0.8,0.8,0.8c0.4,0,0.8-0.3,0.8-0.8
        v-0.9H44c0,0,0,0,0,0c0,0,0,0,0,0c1.1,0,2,0.7,2.3,1.7H44c-0.4,0-0.8,0.3-0.8,0.8v1.6c0,1.3,1.1,2.4,2.4,2.4h0.9v3.3h-2
        c-0.6-1.9-2.4-3.2-4.5-3.2c-2.1,0-3.9,1.3-4.5,3.2h-0.4v-5.7c0-0.4-0.3-0.8-0.8-0.8c-0.4,0-0.8,0.3-0.8,0.8v5.7H18.1
        c-0.6-1.9-2.4-3.2-4.5-3.2c-2.1,0-3.9,1.3-4.5,3.2H4.8c0,0,0,0,0,0v-1.7H8c0.4,0,0.8-0.3,0.8-0.8S8.4,34.9,8,34.9H0.8
        c-0.4,0-0.8,0.3-0.8,0.8s0.3,0.8,0.8,0.8h2.5V38c0,0.9,0.7,1.6,1.6,1.6h4.1c0,0,0,0,0,0c0,2.6,2.1,4.8,4.8,4.8s4.8-2.1,4.8-4.8
        c0,0,0,0,0,0h16.9c0,0,0,0,0,0c0,2.6,2.1,4.8,4.8,4.8s4.8-2.1,4.8-4.8c0,0,0,0,0,0h2.5c0.4,0,0.8-0.3,0.8-0.8v-8
        C48,28.8,46.5,27.2,44.6,26.9z M23.1,5.9L23.1,5.9c0.7-0.9,1.9-1,2.8-0.4s1,1.9,0.4,2.8c-0.3,0.3-1.1,1.2-4.1,3
        c-0.6,0.4-1.2,0.7-1.7,1C21.2,10.1,22.4,6.9,23.1,5.9z M12.1,8.3c-0.7-0.9-0.5-2.1,0.4-2.8c0.4-0.3,0.8-0.4,1.2-0.4
        c0.6,0,1.2,0.3,1.6,0.8l0,0c0.7,1,1.9,4.2,2.6,6.5c-0.5-0.3-1.1-0.6-1.7-1C13.2,9.5,12.4,8.7,12.1,8.3z M35.2,21.9h6.7l1.2,4.9h-7.9
        V21.9z M40.8,18.7c0.9,0,1.7,0.7,1.7,1.7v0h-7.3v-1.7L40.8,18.7L40.8,18.7z M13.6,42.9c-1.8,0-3.3-1.5-3.3-3.3s1.5-3.3,3.3-3.3
        s3.3,1.5,3.3,3.3S15.4,42.9,13.6,42.9z M40,42.9c-1.8,0-3.3-1.5-3.3-3.3s1.5-3.3,3.3-3.3s3.3,1.5,3.3,3.3S41.8,42.9,40,42.9z
         M45.6,33.3c-0.5,0-0.9-0.4-0.9-0.9v-0.9h1.7v1.7L45.6,33.3L45.6,33.3z"/>
                                                    <path
                                                        d="M13.6,38.1c-0.9,0-1.6,0.7-1.6,1.6s0.7,1.6,1.6,1.6s1.6-0.7,1.6-1.6S14.4,38.1,13.6,38.1z"/>
                                                    <path
                                                        d="M40,38.1c-0.9,0-1.6,0.7-1.6,1.6s0.7,1.6,1.6,1.6c0.9,0,1.6-0.7,1.6-1.6S40.9,38.1,40,38.1z"/>
                                                    <path
                                                        d="M19.2,35.6c0,0.4,0.3,0.8,0.8,0.8h11.2c0.4,0,0.8-0.3,0.8-0.8s-0.3-0.8-0.8-0.8H20C19.6,34.9,19.2,35.2,19.2,35.6z"/>
                                                    <path
                                                        d="M2.4,33.2H12c0.4,0,0.8-0.3,0.8-0.8s-0.3-0.8-0.8-0.8H2.4c-0.4,0-0.8,0.3-0.8,0.8S1.9,33.2,2.4,33.2z"/>
                                                    <path d="M12,21.9c0.4,0,0.8-0.3,0.8-0.8s-0.3-0.8-0.8-0.8H8.8c-0.4,0-0.8,0.3-0.8,0.8v6.4c0,0.4,0.3,0.8,0.8,0.8
        c0.4,0,0.8-0.3,0.8-0.8v-2.5h1.7c0.4,0,0.8-0.3,0.8-0.8s-0.3-0.8-0.8-0.8H9.5v-1.7L12,21.9L12,21.9z"/>
                                                    <path d="M19.1,23.2c0-1.5-1.2-2.8-2.8-2.8h-2c-0.4,0-0.8,0.3-0.8,0.8v6.4c0,0.4,0.3,0.8,0.8,0.8c0.4,0,0.8-0.3,0.8-0.8V26h1.3
        l1.4,2.1c0.1,0.2,0.4,0.3,0.6,0.3c0.1,0,0.3,0,0.4-0.1c0.3-0.2,0.4-0.7,0.2-1l-1.1-1.7C18.6,25,19.1,24.2,19.1,23.2z M15.1,21.9h1.3
        c0.7,0,1.3,0.6,1.3,1.3s-0.6,1.3-1.3,1.3h-1.3V21.9z"/>
                                                    <path d="M24,21.9c0.4,0,0.8-0.3,0.8-0.8s-0.3-0.8-0.8-0.8h-3.2c-0.4,0-0.8,0.3-0.8,0.8v6.4c0,0.4,0.3,0.8,0.8,0.8H24
        c0.4,0,0.8-0.3,0.8-0.8s-0.3-0.8-0.8-0.8h-2.5v-1.7c0,0,0,0,0,0h1.6c0.4,0,0.8-0.3,0.8-0.8s-0.3-0.8-0.8-0.8h-1.6c0,0,0,0,0,0v-1.7
        L24,21.9L24,21.9z"/>
                                                    <path d="M29.6,21.9c0.4,0,0.8-0.3,0.8-0.8s-0.3-0.8-0.8-0.8h-3.2c-0.4,0-0.8,0.3-0.8,0.8v6.4c0,0.4,0.3,0.8,0.8,0.8h3.2
        c0.4,0,0.8-0.3,0.8-0.8s-0.3-0.8-0.8-0.8h-2.5v-1.7H28c0.4,0,0.8-0.3,0.8-0.8s-0.3-0.8-0.8-0.8h-0.9v-1.7L29.6,21.9L29.6,21.9z"/>
                                                </svg>
                                            </div>
                                            <div class="shop-features__info">
                                                <div class="shop-features__item-title">ارسال رایگان</div>
                                                <div class="shop-features__item-subtitle">
                                                    تا نزدیک ترین باربری شهر
                                                </div>
                                            </div>
                                        </li>
                                        <li class="shop-features__divider" role="presentation"></li>
                                        <li class="shop-features__item">
                                            <div class="shop-features__item-icon">
                                                <svg width="48" height="48" viewBox="0 0 48 48">
                                                    <path d="M46.218,18.168h-0.262v-0.869c0-1.175-1.211-1.766-2.453-1.766c-0.521,0-0.985,0.094-1.366,0.263
        c0.015-0.028,2.29-4.591,2.303-4.62c0.968-2.263-3.041-4.024-4.372-1.449l-5.184,10.166c-0.35,0.648-0.364,1.449,0.033,2.081
        c-0.206-0.107-0.432-0.166-0.668-0.166h-4.879c1.555-1.597,6.638-3.535,6.638-8.011c0-1.599-0.676-3.02-1.903-4.002
        c-1.088-0.87-2.52-1.35-4.033-1.35c-2.802,0-5.779,1.758-5.779,5.015c0,2.195,1.426,2.522,2.275,2.522
        c1.653,0,2.545-1.022,2.545-1.983c0-0.485,0.117-0.981,0.981-0.981c0.906,0,1.003,0.623,1.003,0.891
        c0,2.284-7.074,4.474-7.074,8.399v2.178c0,1.147,1.319,1.781,2.23,1.781h7.995c1.426,0,2.332-2.195,1.348-3.669
        c0.265,0.137,0.569,0.21,0.898,0.21h4.552v1.678c0,1.049,1.01,1.781,2.455,1.781s2.455-0.733,2.455-1.781v-1.678h0.262
        c1.02,0,1.781-1.225,1.781-2.32C48,19.144,47.251,18.168,46.218,18.168L46.218,18.168z M34.241,24.861h-7.987
        c-0.389,0-0.802-0.258-0.824-0.375v-2.179c0-3.056,7.074-5.046,7.074-8.399c0-1.107-0.754-2.298-2.41-2.298
        c-1.473,0-2.388,0.915-2.388,2.388c0,0.236-0.405,0.577-1.138,0.577c-0.492,0-0.869-0.082-0.869-1.116
        c0-2.344,2.253-3.609,4.373-3.609c2.251,0,4.53,1.355,4.53,3.946c0,4.526-6.94,5.826-6.94,8.511v0.202
        c0,0.389,0.315,0.703,0.703,0.703l5.882,0c0.091,0.015,0.354,0.314,0.354,0.802C34.601,24.494,34.349,24.825,34.241,24.861
        L34.241,24.861z M46.194,21.402h-0.941c-0.388,0-0.703,0.315-0.703,0.703v2.381c0,0.151-0.44,0.375-1.048,0.375
        c-0.608,0-1.049-0.224-1.049-0.375v-2.381c0-0.389-0.315-0.703-0.703-0.703h-5.255c-0.518,0-0.545-0.528-0.371-0.846
        c0.003-0.006,0.006-0.012,0.009-0.018l5.186-10.17c0.533-1.031,1.883-0.238,1.884,0.097c-0.011,0.087,0.038-0.035-4.014,8.092
        c-0.233,0.468,0.109,1.017,0.629,1.017h1.932c0.388,0,0.703-0.315,0.703-0.703v-1.572c0-0.123,0.409-0.36,1.051-0.36
        c0.618,0,1.046,0.223,1.046,0.36v1.572c0,0.389,0.315,0.703,0.703,0.703h0.966c0.196,0,0.375,0.435,0.375,0.914
        C46.593,20.951,46.324,21.338,46.194,21.402L46.194,21.402z M41.046,17.984v0.184h-0.092L41.046,17.984z M41.046,17.984"/>
                                                    <path d="M36.976,36.602c2.428-2.291,4.227-5.18,5.202-8.354c0.114-0.371-0.094-0.764-0.465-0.879
        c-0.371-0.114-0.765,0.095-0.879,0.466c-0.903,2.941-2.571,5.62-4.823,7.744c-0.282,0.267-0.295,0.712-0.029,0.994
        C36.249,36.856,36.694,36.869,36.976,36.602L36.976,36.602z M36.976,36.602"/>
                                                    <path d="M35.099,7.86c0.226-0.316,0.152-0.756-0.164-0.981C29.684,3.131,23.098,2.38,17.381,4.38
        c-0.367,0.128-0.559,0.53-0.431,0.896c0.128,0.366,0.53,0.56,0.896,0.431c5.23-1.83,11.346-1.199,16.272,2.316
        C34.434,8.249,34.873,8.176,35.099,7.86L35.099,7.86z M35.099,7.86"/>
                                                    <path d="M25.247,43.573c-0.857-0.491-1.854-0.639-2.807-0.416c-0.525,0.123-1.064,0.207-1.602,0.251
        c-0.387,0.032-0.675,0.371-0.643,0.758c0.032,0.387,0.37,0.675,0.758,0.644c0.606-0.05,1.214-0.145,1.807-0.284
        c0.606-0.141,1.241-0.047,1.788,0.267c1.583,0.908,3.528,0.884,5.076-0.064c3.605-2.207,3.212-1.964,3.359-2.061
        c2.24-1.464,2.922-4.464,1.519-6.755l-2.538-4.145c-1.436-2.345-4.508-3.068-6.835-1.644l-3.235,1.981
        c-1.472,0.901-2.358,2.477-2.371,4.214c-0.001,0.153-0.145,0.269-0.293,0.237c-1.228-0.265-2.372-0.847-3.306-1.683
        c-1.403-1.255-2.633-2.596-3.656-3.984c-0.23-0.313-0.67-0.379-0.983-0.149c-0.313,0.23-0.379,0.671-0.149,0.983
        c1.08,1.465,2.375,2.878,3.85,4.197c1.116,0.999,2.481,1.694,3.947,2.01c1.02,0.22,1.988-0.557,1.996-1.602
        c0.009-1.248,0.644-2.379,1.699-3.025l2.742-1.679l6.261,10.224l-2.742,1.679C27.78,44.209,26.384,44.225,25.247,43.573
        L25.247,43.573z M26.622,30.977c1.54-0.495,3.282,0.119,4.142,1.525l2.538,4.145c0.865,1.413,0.611,3.242-0.524,4.383
        L26.622,30.977z M26.622,30.977"/>
                                                    <path d="M0.403,23.192c0.998,3.783,2.422,7.199,4.232,10.155c1.81,2.956,4.206,5.777,7.121,8.386
        c1.613,1.443,3.59,2.435,5.717,2.868c0.377,0.078,0.751-0.165,0.83-0.549c0.078-0.381-0.168-0.752-0.549-0.829
        c-1.883-0.383-3.632-1.261-5.06-2.538c-2.813-2.517-5.121-5.233-6.859-8.072c-1.739-2.839-3.108-6.13-4.071-9.78
        c-0.902-3.419-0.07-7.084,2.228-9.803c0.632-0.748,0.954-1.704,0.906-2.69C4.834,9.03,5.483,7.795,6.592,7.116l2.742-1.679
        l6.261,10.224l-2.742,1.679c-1.043,0.639-2.327,0.696-3.436,0.153c-0.93-0.455-2.048,0.053-2.319,1.052
        c-0.396,1.462-0.401,3.008-0.015,4.47c0.558,2.115,1.315,4.081,2.249,5.843c0.182,0.343,0.608,0.474,0.951,0.292
        c0.343-0.182,0.474-0.608,0.292-0.951c-0.884-1.667-1.601-3.532-2.132-5.543c-0.323-1.225-0.319-2.519,0.012-3.744
        c0.04-0.147,0.206-0.223,0.342-0.156c1.543,0.756,3.334,0.675,4.789-0.216l3.235-1.981c2.322-1.422,3.082-4.485,1.643-6.835
        l-2.538-4.145c-1.44-2.351-4.516-3.063-6.835-1.643L5.858,5.917C4.31,6.864,3.404,8.585,3.493,10.409
        c0.031,0.63-0.174,1.239-0.575,1.714C0.324,15.192-0.616,19.33,0.403,23.192L0.403,23.192z M14.728,6.314l2.538,4.145
        c0.865,1.414,0.61,3.243-0.524,4.383L10.586,4.788C12.12,4.295,13.864,4.903,14.728,6.314L14.728,6.314z M14.728,6.314"/>
                                                </svg>
                                            </div>
                                            <div class="shop-features__info">
                                                <div class="shop-features__item-title">پشتیبانی 24/7</div>
                                                <div class="shop-features__item-subtitle">
                                                    پاسخگوی تماس تلفنی
                                                </div>
                                            </div>
                                        </li>
                                        <li class="shop-features__divider" role="presentation"></li>
                                        <li class="shop-features__item">
                                            <div class="shop-features__item-icon">
                                                <svg width="48" height="48" viewBox="0 0 48 48">
                                                    <path d="M30,34.4H2.5c-0.5,0-0.9-0.4-0.9-0.9V7.7c0-0.5,0.4-0.9,0.9-0.9H42c0.5,0,0.9,0.4,0.9,0.9v11.2c0,0.4,0.4,0.8,0.8,0.8
        c0.4,0,0.8-0.4,0.8-0.8V7.7c0-1.4-1.1-2.5-2.5-2.5H2.5C1.1,5.2,0,6.3,0,7.7v25.8C0,34.8,1.1,36,2.5,36H30c0.4,0,0.8-0.4,0.8-0.8
        C30.8,34.7,30.5,34.4,30,34.4z"/>
                                                    <path d="M15.4,18v-5.2c0-0.9-0.7-1.7-1.7-1.7H6.8c-0.9,0-1.7,0.7-1.7,1.7V18c0,0.9,0.7,1.7,1.7,1.7h6.9C14.6,19.7,15.4,18.9,15.4,18
        z M13.7,12.8V18c0,0,0,0.1-0.1,0.1h-3.5v-1.8h0.9c0.4,0,0.8-0.4,0.8-0.8c0-0.4-0.4-0.8-0.8-0.8h-0.9v-1.8L13.7,12.8
        C13.7,12.8,13.7,12.8,13.7,12.8z M6.8,18v-5.2c0,0,0-0.1,0.1-0.1h1.8V18L6.8,18C6.8,18,6.8,18,6.8,18z"/>
                                                    <path d="M32.2,11.1c-0.8-0.5-1.7-0.8-2.6-0.8c-2.6,0-4.7,2.1-4.7,4.7s2.1,4.7,4.7,4.7c1,0,1.8-0.3,2.6-0.8c0.8,0.5,1.7,0.8,2.6,0.8
        c2.6,0,4.7-2.1,4.7-4.7s-2.1-4.7-4.7-4.7C33.8,10.3,32.9,10.6,32.2,11.1z M26.5,15c0-1.7,1.4-3.1,3.1-3.1c0.5,0,0.9,0.1,1.4,0.3
        C30.4,13,30.1,14,30.1,15c0,1,0.3,1.9,0.9,2.7c-0.4,0.2-0.9,0.3-1.4,0.3C27.9,18,26.5,16.7,26.5,15z M37.8,15c0,1.7-1.4,3.1-3.1,3.1
        c-0.5,0-0.9-0.1-1.4-0.3c0.6-0.8,0.9-1.7,0.9-2.7c0-0.4-0.4-0.8-0.8-0.8s-0.8,0.4-0.8,0.8c0,0.6-0.2,1.2-0.5,1.6
        c-0.3-0.5-0.5-1.1-0.5-1.6c0-1.7,1.4-3.1,3.1-3.1C36.4,11.9,37.8,13.3,37.8,15z"/>
                                                    <path
                                                        d="M6,24.1c-0.4,0-0.8,0.4-0.8,0.8c0,0.4,0.4,0.8,0.8,0.8h6.9c0.4,0,0.8-0.4,0.8-0.8c0-0.4-0.4-0.8-0.8-0.8H6z"/>
                                                    <path
                                                        d="M30.9,29.2H6c-0.4,0-0.8,0.4-0.8,0.8c0,0.4,0.4,0.8,0.8,0.8h24.9c0.4,0,0.8-0.4,0.8-0.8S31.3,29.2,30.9,29.2z"/>
                                                    <path
                                                        d="M16.3,24.1c-0.4,0-0.8,0.4-0.8,0.8c0,0.4,0.4,0.8,0.8,0.8h6.9c0.4,0,0.8-0.4,0.8-0.8c0-0.4-0.4-0.8-0.8-0.8H16.3z"/>
                                                    <path
                                                        d="M31.7,24.1h-5.2c-0.4,0-0.8,0.4-0.8,0.8c0,0.4,0.4,0.8,0.8,0.8h5.2c0.4,0,0.8-0.4,0.8-0.8C32.5,24.4,32.2,24.1,31.7,24.1z"/>
                                                    <path d="M46.3,30.2v-3.6c0-3.3-2.7-6-6-6c-3.3,0-6,2.7-6,6v3.6c-1,0.3-1.7,1.3-1.7,2.4v7.7c0,1.4,1.1,2.5,2.5,2.5h10.3
        c1.4,0,2.5-1.1,2.5-2.5v-7.7C48,31.5,47.3,30.5,46.3,30.2z M40.3,22.2c2.4,0,4.3,2,4.3,4.3v3.5H36v-3.5C36,24.2,37.9,22.2,40.3,22.2
        z M46.4,40.3c0,0.5-0.4,0.9-0.9,0.9H35.2c-0.5,0-0.9-0.4-0.9-0.9v-7.7c0-0.5,0.4-0.9,0.9-0.9h10.3c0.5,0,0.9,0.4,0.9,0.9V40.3z"/>
                                                    <path d="M40.3,33.5c-1.2,0-2.1,0.9-2.1,2.1c0,0.9,0.5,1.6,1.3,1.9v1.1c0,0.4,0.4,0.8,0.8,0.8s0.8-0.4,0.8-0.8v-1.1
        c0.8-0.3,1.3-1.1,1.3-1.9C42.4,34.4,41.5,33.5,40.3,33.5z M40.3,35.1c0.3,0,0.5,0.2,0.5,0.5s-0.2,0.5-0.5,0.5s-0.5-0.2-0.5-0.5
        S40.1,35.1,40.3,35.1z"/>
                                                </svg>
                                            </div>
                                            <div class="shop-features__info">
                                                <div class="shop-features__item-title">100% اصالت سلامت کالا
                                                    <div class="shop-features__item-subtitle">
                                                        قیمت و کیفیت
                                                    </div>
                                                </div>
                                        </li>
                                        <li class="shop-features__divider" role="presentation"></li>
                                        <li class="shop-features__item">
                                            <div class="shop-features__item-icon">
                                                <svg width="48" height="48" viewBox="0 0 48 48">
                                                    <path d="M48,3.3c0-0.9-0.3-1.7-1-2.3c-0.6-0.6-1.4-1-2.3-1c-0.9,0-1.7,0.3-2.3,1c-0.3,0.3-0.7,0.8-1,1.1c-0.3,0.3-0.2,0.8,0.1,1.1
        c0.3,0.3,0.8,0.2,1.1-0.1c0.4-0.5,0.7-0.9,0.9-1c0.3-0.3,0.8-0.5,1.2-0.5c0,0,0,0,0,0c0.5,0,0.9,0.2,1.2,0.5
        c0.3,0.3,0.5,0.8,0.5,1.2c0,0.5-0.2,0.9-0.5,1.2c-0.3,0.3-1.3,1.1-2.7,2.1c-0.9-1.5-2.4-2.4-4.3-2.4H27.5c-1.5,0-3,0.6-4.1,1.7
        L0.7,28.6C0.3,29,0,29.7,0,30.3s0.3,1.3,0.7,1.7L16,47.3c0.5,0.5,1.1,0.7,1.7,0.7c0.7,0,1.3-0.3,1.7-0.7l22.8-22.8
        c1.1-1.1,1.7-2.5,1.7-4.1V9.1c0-0.3,0-0.7-0.1-1C45.4,7,46.6,6,47,5.6C47.7,5,48,4.1,48,3.3z M42.3,9.1v11.4c0,1.1-0.4,2.2-1.2,3
        L18.3,46.2c-0.3,0.3-0.9,0.3-1.2,0L1.8,30.9c-0.3-0.3-0.3-0.9,0-1.2L24.6,6.9c0.8-0.8,1.8-1.2,3-1.2h11.4c1.3,0,2.4,0.7,3,1.8
        c-0.9,0.6-1.9,1.3-3,1.9c0,0-0.1-0.1-0.1-0.1c-1.3-1.3-3.3-1.3-4.6,0s-1.3,3.3,0,4.6c0.6,0.6,1.5,1,2.3,1c0.8,0,1.7-0.3,2.3-1
        c0.9-0.9,1.1-2.1,0.8-3.1C40.6,10.2,41.5,9.6,42.3,9.1C42.3,9.1,42.3,9.1,42.3,9.1z M35.7,11.9c0.1,0.3,0.4,0.4,0.7,0.4
        c0.1,0,0.2,0,0.3-0.1c0.5-0.2,0.9-0.5,1.4-0.7c0,0.4-0.2,0.9-0.5,1.2c-0.7,0.7-1.8,0.7-2.4,0c-0.7-0.7-0.7-1.8,0-2.4
        c0.3-0.3,0.8-0.5,1.2-0.5c0.3,0,0.7,0.1,1,0.3c-0.4,0.2-0.9,0.5-1.3,0.7C35.7,11.1,35.6,11.5,35.7,11.9z"/>
                                                    <path d="M16.3,25.8c-0.3-0.3-0.8-0.3-1.1,0c-0.3,0.3-0.3,0.8,0,1.1l2.4,2.4l-3,3l-2.4-2.4c-0.3-0.3-0.8-0.3-1.1,0
        c-0.3,0.3-0.3,0.8,0,1.1l5.9,5.9c0.2,0.2,0.4,0.2,0.5,0.2s0.4-0.1,0.5-0.2c0.3-0.3,0.3-0.8,0-1.1l-2.4-2.4l3-3l2.4,2.4
        c0.2,0.2,0.4,0.2,0.5,0.2s0.4-0.1,0.5-0.2c0.3-0.3,0.3-0.8,0-1.1L16.3,25.8z"/>
                                                    <path d="M24.8,21.4c-1.4-1.4-3.8-1.4-5.2,0s-1.4,3.8,0,5.2l1.8,1.8c0.7,0.7,1.7,1.1,2.6,1.1s1.9-0.4,2.6-1.1c1.4-1.4,1.4-3.8,0-5.2
        L24.8,21.4z M25.5,27.3c-0.8,0.8-2.2,0.8-3,0l-1.8-1.8c-0.8-0.8-0.8-2.2,0-3c0.4-0.4,1-0.6,1.5-0.6s1.1,0.2,1.5,0.6l1.8,1.8
        C26.3,25.1,26.3,26.5,25.5,27.3z"/>
                                                    <path d="M27.4,15.8l1.8-1.8c0.3-0.3,0.3-0.8,0-1.1c-0.3-0.3-0.8-0.3-1.1,0l-4.7,4.7c-0.3,0.3-0.3,0.8,0,1.1c0.2,0.2,0.4,0.2,0.5,0.2
        s0.4-0.1,0.5-0.2l1.8-1.8l5.3,5.3c0.2,0.2,0.4,0.2,0.5,0.2c0.2,0,0.4-0.1,0.5-0.2c0.3-0.3,0.3-0.8,0-1.1L27.4,15.8z"/>
                                                </svg>
                                            </div>
                                            <div class="shop-features__info">
                                                <div class="shop-features__item-title">
                                                    تخفیف دائمی
                                                </div>
                                                <div class="shop-features__item-subtitle">
                                                    تمامی محصولات
                                                </div>
                                            </div>
                                        </li>
                                        <li class="shop-features__divider" role="presentation"></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="product__tabs product-tabs product-tabs--layout--full">

                                <!-- <div class="alert alert-success alert-lg alert-dismissible fade show" style="background-color: #e2f2da;color: #44782a;">
                                     <img src="<?=$conf->BaseRoot2?>images/warning.png" style="vertical-align: middle;float: right;padding: 0 0 0 10px;width:44px">
            <p>
    در راستای حمایت ازمصرف کنندگان ، فروشگاه ایرا ن کالا از فروش محصولات به همکاران محترم معذور می باشد و طبق قوانین سایت ، فروشگاه سرای ایرانی می تواند سفارش را  لغو و مبلغ را عودت نماید.
        </div>-->









                                <div class="cd-articles">
                                    <article id="product-tab-description">

                                        <header>

                                            <div class="o-box__header"><span class="o-box__title">نقد و بررسی تخصصی</span><span class="o-box__header-desc"><?=$n->title.' '.$n->model?> </span></div>
                                        </header>


                                        <div class="product-tabs__pane product-tabs__pane--active p-0-impor"  >
                                            <div class="typography1">
                                                <?=$Descriptions?> 
                                            </div>
                                        </div>

                                    </article>

                                    <article id="product-tab-specification" class="pad0important">

                                        <header><div class="o-box__header pt-0"><span class="o-box__title"> مشخصات کالا</span><span class="o-box__header-desc"><?=$n->title.' '.$n->model?> </span></div></header>

                                        <div class="product-tabs__pane product-tabs__pane--active" >
                                            <div class="spec">
                                                <div class="spec__section">

                                                    <div class="spec__row">
                                                        <div class="spec__name">
                                                            شناسه کالا
                                                        </div>
                                                        <div class="spec__value code2">
                                                            - - -
                                                        </div>
                                                    </div>

                                                    <?php
                                                    if($MyProp['All']){
                                                        foreach($MyProp['All'] as $pri=>$pr){
                                                            $PropStr = [];
                                                            $v = $ths->MyDecode($pr["prop_val"]);

                                                            if($v){
                                                                foreach($v as $vi=>$vv){
                                                                    if(array_key_exists('null', $PropArr[$pr['prop_id']]["val"])){
                                                                        $PropStr[] = $vv.' '.$PropArr[$pr['prop_id']]['unit'];
                                                                    }else{
                                                                        $PropStr[] = $PropArr[$pr['prop_id']]["val"][$vv].' '.$PropArr[$pr['prop_id']]['unit'];
                                                                    }
                                                                }
                                                            }

                                                            if(is_array($PropStr) && count($PropStr)){
                                                                ?>
                                                                <div class="spec__row">
                                                                    <div class="spec__name">
                                                                        <?=(isset($PropArr[$pr['prop_id']]['title']) ? $PropArr[$pr['prop_id']]['title'] : '')?>
                                                                    </div>
                                                                    <div class="spec__value spec_linemax768">
                                                                        <?php
                                                                        echo nl2br(is_array($PropStr) ? implode(' | ', $PropStr) : ' - - -');
                                                                        ?>
                                                                    </div>
                                                                </div>
                                                                <?php
                                                            }
                                                        }
                                                    }
                                                    ?>
                                                </div>
                                            </div>
                                        </div>



                                    </article>
                                    <!-- 1 -->
                                    <article id="product-tab-reviews">
                                        <header><div class="o-box__header pt-0"><span class="o-box__title"> امتیاز و نظرات کاربران </span><span class="o-box__header-desc"><?=$n->title.' '.$n->model?> </span></div></header>

                                        <div class="product-tabs__pane  product-tabs__pane--active">
                                            <?php
                                            $_REQUEST['ProdID'] = $n->product_id;
                                            echo $ths->LoadPage($conf->UserPanelUrl . '/Comment/_ShowReview');


                                            $_REQUEST['MyID'] = $n->product_id;
                                            $_REQUEST['MyTitle'] = $n->title.' '.$n->model;
                                            if(isset($AllCmnt) && $AllCmnt){
                                                $_REQUEST['AllCmnt2'] = json_encode($AllCmnt);
                                            }
                                            echo $ths->LoadPage($conf->UserPanelUrl . '/Comment/_Product');
                                            ?>
                                        </div>


                                    </article>
                                    <!-- 2 -->
                                    <article id="product-tab-send-review">
                                        <div class="product-tabs__pane" >
                                            <div class="typography1" onclick="LoadReview(1)">
                                            </div>

                                        </div>

                                    </article>


                                    <aside id="cdtop" class="cd-read-more">
                                        <ul>
                                            <li>
                                                <a href="#product-tab-description">
                                                    <span>نقد و بررسی</span>
                                                    <b>بررسی تخصصی کالا</b>
                                                    <svg x="0px" y="0px" width="36px" height="36px" viewBox="0 0 36 36"><circle fill="none" stroke-width="2" cx="18" cy="18" r="16" stroke-dasharray="100 100" stroke-dashoffset="100" transform="rotate(-90 18 18)"></circle></svg>
                                                </a>
                                            </li>

                                            <li>
                                                <a href="#product-tab-specification">
                                                    <span>مشخصات</span>
                                                    <b>مشخصات و ویژگی های کالا</b>
                                                    <svg x="0px" y="0px" width="36px" height="36px" viewBox="0 0 36 36"><circle fill="none" stroke-width="2" cx="18" cy="18" r="16" stroke-dasharray="100 100" stroke-dashoffset="100" transform="rotate(-90 18 18)"></circle></svg>
                                                </a>
                                            </li>

                                            <li>
                                                <a href="#product-tab-reviews">
                        <span>
                            نظرات کاربران

                            <?php
                            $CmntCond = [];
                            $CmntCond[] = ['type', 3];
                            $CmntCond[] = ['display', 1];
                            $CmntCond[] = ['relid', $_REQUEST['ProdID']];
                            $CmntCond[] = ['lang_id', 1];
                            $AllCmnt2 = $CommentClass->get_all($CmntCond, ['id']);

                            echo '<span class="text-danger d-inline-block"><small>('.($AllCmnt2 ? count($AllCmnt2) : 0).')</small></span>';
                            ?>

                        </span>
                                                    <b>از دیدگاه کاربران ایران کالا</b>
                                                    <svg x="0px" y="0px" width="36px" height="36px" viewBox="0 0 36 36"><circle fill="none" stroke-width="2" cx="18" cy="18" r="16" stroke-dasharray="100 100" stroke-dashoffset="100" transform="rotate(-90 18 18)"></circle></svg>
                                                </a>
                                            </li>


                                        </ul>
                                    </aside> <!-- .cd-read-more -->

                                </div> <!-- .cd-articles -->



                            </div>
                        </div>
                    </div>
                    <div class="stops1 block-space block-space--layout--divider-nl"></div>

                    <?php
                    if($MyProductPrice){
                        $_REQUEST['MyID'] = $n->product_id;
                        echo $ths->LoadPage($conf->UserPanelUrl . 'Product/_Similar');
                    }
                    ?>

                </div>
            </div>
        </div>
    </div>
    </div>
    <div class="block-space block-space--layout--before-footer"></div>
    <?php

    $ths->save_cache($CacheName, ob_get_contents());
    ob_end_flush();
}
?>
<script>
    $(document).ready(function() {
        $(window).scroll(function() {
            if ($(document).scrollTop() > 700) {
                $(".product-tabs__list").addClass("sfix");
                $(".product__info-card").addClass("tptr");
            } else {
                $(".product-tabs__list").removeClass("sfix");
                $(".product__info-card").removeClass("tptr");
            }
        });
    });

    function myFunctions() {
        var element = document.getElementById("myDIV");
        element.classList.add("js-email-show");
    }

    function myFunction() {
        var copyText = document.getElementById("myInput");
        copyText.select();
        copyText.setSelectionRange(0, 99999);
        document.execCommand("copy");

        var tooltip = document.getElementById("myTooltip");
        tooltip.innerHTML = "کپی شد!";
    }

    function outFunc() {
        var tooltip = document.getElementById("myTooltip");
        tooltip.innerHTML = "کپی لینک محصول";
    }

    // Get the modal by Dr.Hossein Moradi
    var modal = document.getElementById("myModal");

    // hssin.ir Get the button that opens the modal by Dr.Hossein Moradi
    var btn = document.getElementById("myBtn");

    // Get the <span> element that closes the modal by Dr.Hossein Moradi
    var span = document.getElementsByClassName("close1")[0];

    // hssin.ir When the user clicks the button, open the modal  by Dr.Hossein Moradi
    btn.onclick = function() {
        modal.style.display = "block";
    }

    // by Dr.Hossein Moradi When the user clicks on <span> (x), close the modal
    span.onclick = function() {
        modal.style.display = "none";
    }

    // When the user clicks anywhere outside of the modal, close it by Dr.Hossein Moradi hssin.ir
    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }

    function tobas(){
        setTimeout(function(){location.href="<?=$conf->BaseRoot2?>factor"} , 500);
    };

    $(document).ready( function(){
        if ($(window).width()<=768) {
            $(".product__actions_2").html( $(".product__actions_3").html() );
            $(".product__actions_3").html("");
            $(document).on("click", ".product__actions-item--addtocart" , function() {
                UpdateMyCartpr($('#MyID').val(), $('#SelProp').val(), $('#MyCompany').val(), 1);
            });
        }
    });

    jQuery(document).ready(function($){
        var articlesWrapper = $('.cd-articles');

        if( articlesWrapper.length > 0 ) {
            // cache jQuery objects
            var windowHeight = $(window).height(),
                articles = articlesWrapper.find('article'),
                aside = $('.cd-read-more'),
                articleSidebarLinks = aside.find('li');
            // initialize variables
            var	scrolling = false,
                sidebarAnimation = false,
                resizing = false,
                mq = checkMQ(),
                svgCircleLength = parseInt(Math.PI*(articleSidebarLinks.eq(0).find('circle').attr('r')*2));

            // check media query and bind corresponding events
            if( mq == 'desktop' ) {
                $(window).on('scroll', checkRead);
                $(window).on('scroll', checkSidebar);
            }

            $(window).on('resize', resetScroll);

            updateArticle();
            updateSidebarPosition();

            aside.on('click', 'a', function(event){
                event.preventDefault();
                var selectedArticle = articles.eq($(this).parent('li').index()),
                    selectedArticleTop = selectedArticle.offset().top;

                $(window).off('scroll', checkRead);

                $('body,html').animate(
                    {'scrollTop': selectedArticleTop + 2},
                    300, function(){
                        checkRead();
                        $(window).on('scroll', checkRead);
                    }
                );
            });
        }

        function checkRead() {
            if( !scrolling ) {
                scrolling = true;
                (!window.requestAnimationFrame) ? setTimeout(updateArticle, 300) : window.requestAnimationFrame(updateArticle);
            }
        }

        function checkSidebar() {
            if( !sidebarAnimation ) {
                sidebarAnimation = true;
                (!window.requestAnimationFrame) ? setTimeout(updateSidebarPosition, 300) : window.requestAnimationFrame(updateSidebarPosition);
            }
        }

        function resetScroll() {
            if( !resizing ) {
                resizing = true;
                (!window.requestAnimationFrame) ? setTimeout(updateParams, 300) : window.requestAnimationFrame(updateParams);
            }
        }

        function updateParams() {
            windowHeight = $(window).height();
            mq = checkMQ();
            $(window).off('scroll', checkRead);
            $(window).off('scroll', checkSidebar);

            if( mq == 'desktop') {
                $(window).on('scroll', checkRead);
                $(window).on('scroll', checkSidebar);
            }
            resizing = false;
        }

        function updateArticle() {
            var scrollTop = $(window).scrollTop();

            articles.each(function(){
                var article = $(this),
                    articleTop = article.offset().top,
                    articleHeight = article.outerHeight(),
                    articleSidebarLink = articleSidebarLinks.eq(article.index()).children('a');

                if( article.is(':last-of-type') ) articleHeight = articleHeight - windowHeight;

                if( articleTop > scrollTop) {
                    articleSidebarLink.removeClass('read reading');
                } else if( scrollTop >= articleTop && articleTop + articleHeight > scrollTop) {
                    var dashoffsetValue = svgCircleLength*( 1 - (scrollTop - articleTop)/articleHeight);
                    articleSidebarLink.addClass('reading').removeClass('read').find('circle').attr({ 'stroke-dashoffset': dashoffsetValue });
                    changeUrl(articleSidebarLink.attr('href'));
                } else {
                    articleSidebarLink.removeClass('reading').addClass('read');
                }
            });
            scrolling = false;
        }

        function updateSidebarPosition() {
            var articlesWrapperTop = articlesWrapper.offset().top,
                articlesWrapperHeight = articlesWrapper.outerHeight(),
                scrollTop = $(window).scrollTop() + 80;

            if( scrollTop < articlesWrapperTop) {
                aside.removeClass('fixed').attr('style', '');
            } else if( scrollTop >= articlesWrapperTop && scrollTop < articlesWrapperTop + articlesWrapperHeight - windowHeight + 600) {
                aside.addClass('fixed').attr('style', '');
            } else {
                var articlePaddingTop = Number(articles.eq(1).css('padding-top').replace('px', ''));
                if( aside.hasClass('fixed') ) aside.removeClass('fixed').css('top', articlesWrapperHeight + articlePaddingTop - windowHeight + 'px');
            }
            sidebarAnimation =  false;
        }

        function changeUrl(link) {
            var pageArray = location.pathname.split('/'),
                actualPage = pageArray[pageArray.length - 1] ;
            if( actualPage != link && history.pushState ) window.history.pushState({path: link},'',link);
        }

        function checkMQ() {
            return window.getComputedStyle(articlesWrapper.get(0), '::before').getPropertyValue('content').replace(/'/g, "").replace(/"/g, "");
        }
    });

    $( document ).ready(function() {
        console.log( "document ready!" );
        if(window.innerWidth > 1200){
            var $sticky = $('.product__info-card');
            var $stickyrStopper = $('.stops1');
            if (!!$sticky.offset()) { // make sure ".sticky" element exists

                var generalSidebarHeight = $sticky.innerHeight();

                var stickyTop = $sticky.offset().top + <?=(!$MyProductPrice ? 500 : 100)?>;
                var stickOffset = 100;
                var stickyStopperPosition = $stickyrStopper.offset().top;
                var stopPoint = stickyStopperPosition - generalSidebarHeight - stickOffset;
                var diff = stopPoint + stickOffset;

                $(window).scroll(function(){ // scroll event
                    var windowTop = $(window).scrollTop(); // returns number

                    if (stopPoint < windowTop) {
                        $sticky.css({ position: 'absolute', bottom: '0px', top: 'unset',transform: 'translateY(0%)' });
                        document.getElementById("cdtopsidebar").classList.add("transf");
                    } else if (stickyTop < windowTop+stickOffset) {

                        $sticky.attr('style', 'transform:translateY(7%);position :fixed;top : '+ stickOffset + 'px' + ';');
                        if($(".is-hidden1").length == 0){
                            $sticky.attr('style', 'transform:translateY(7%);position :fixed;top : '+ stickOffset + 'px' + ';');
                        }
                        else if($(".fixed").length > 0){
                            $sticky.attr('style', 'transform:translateY(10%) !important;position :fixed;top : '+ stickOffset + 'px' + ';');
                        }

                        document.getElementById("cdtopsidebar").classList.remove("transf");
                    } else {
                        $sticky.css({position: 'absolute', top: '0', bottom: 'unset',transform: 'translateY(0%)' });
                    }
                });

            }
        }
    });

    if(!(document.querySelector(".header__navbar").classList.contains(".is-hidden1"))){
        document.querySelector(".product__info-card").style.top = "120px";
    }
</script>