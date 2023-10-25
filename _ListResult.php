<?php
session_set_cookie_params(6600, '/', $_SERVER['HTTP_HOST'], true, true);
session_start();
/**
 * Created by PhpStorm.
 * User: yousefi
 * Date: 8/22/20
 * Time: 11:41 AM
 */
    include_once(__DIR__."/../../classes/class.cnfg.php");
    $conf = new config();

    include_once($conf->BaseRoot.'/classes/class.main.php');
    $ths = new main();

    $ths->ExternalLinkCheck2();

    include_once($conf->BaseRoot.'/classes/class.libs_parent.php');

    include_once($conf->BaseRoot.'/classes/libs/class.product.php');
    $ProductClass = new product();

    include_once($conf->BaseRoot.'/classes/libs/class.product_rel.php');
    $ProdRelClass = new product_rel();

    include_once($conf->BaseRoot.'/classes/libs/class.files.php');
    $FilesClass = new files();

    include_once($conf->BaseRoot.'/classes/libs/class.category.php');
    $CatClass = new category();

    include_once($conf->BaseRoot.'/classes/libs/class.brand.php');
    $BrandClass = new brand();

    include_once($conf->BaseRoot.'/classes/libs/class.adv.php');
    $AdvClass = new adv();

    include_once($conf->BaseRoot.'/classes/libs/class.properties.php');
    $PropClass = new properties();


    include_once($conf->BaseRoot.'/classes/libs/class.category_brand.php');
    $CatBrandClass = new category_brand();


    $Cnfg = $ths->getSetting();

    $CatID = (isset($_REQUEST['CatID']) ? (int)$ths->MakeSecurParam($_REQUEST['CatID']) : 0);
    $BrandID = (isset($_REQUEST['BrandID']) ? (int)$ths->MakeSecurParam($_REQUEST['BrandID']) : 0);
    $MyGiftType = ['Discount'=>'تخفیف (درصد)', 'Credit'=> 'اعتبار هدیه (تومان)','GiftCard'=> 'کارت هدیه (تومان)', 'Score'=>'امتیاز هدیه', 'GiftPlus'=>'هدیه غیر نقدی'];
    $MyGiftType2 = [1=>'تخفیف درصد', 2=>'تومان اعتبار هدیه ', 3=> 'تومان کارت هدیه ', 4=>'امتیاز هدیه', 5=>'به عنوان هدیه '];
    $BrandTitle = '';
    if($BrandID){
        $BrandTitle = $BrandClass->get_title_by_id($BrandID);}
        
    $Tbl = 'product_price';
    $GroupBy = 'product.product_id';


    if(isset($_REQUEST['SrchItem'])){
        $_REQUEST['SrchItem'] = $ths->MakeSecurParam($_REQUEST['SrchItem'] , true);
    }


    $_REQUEST['CatID'] = (isset($_REQUEST['CatID']) ? (int)$ths->MakeSecurParam($_REQUEST['CatID']) : 0);
    $_REQUEST['IsAvailabe'] = (isset($_REQUEST['IsAvailabe']) ? $ths->MakeSecurParam($_REQUEST['IsAvailabe']) : false);
    $_REQUEST['price_from'] = (isset($_REQUEST['price_from']) ? (int)$ths->MakeSecurParam($_REQUEST['price_from']) : 0);
    $_REQUEST['price_to'] = (isset($_REQUEST['price_to']) ? (int)$ths->MakeSecurParam($_REQUEST['price_to']) : 0);
    $_REQUEST['order_by'] = (isset($_REQUEST['order_by']) ? $ths->MakeSecurParam($_REQUEST['order_by']) : '');
    $_REQUEST['per_page'] = (isset($_REQUEST['per_page']) ? (int)$ths->MakeSecurParam($_REQUEST['per_page']) : 10);
    $_REQUEST['CurrPage'] = ((isset($_REQUEST['CurrPage']) && (int)$ths->MakeSecurParam($_REQUEST['CurrPage'])) ? (int)$ths->MakeSecurParam($_REQUEST['CurrPage']) : 0);
    $_REQUEST['BrandID'] = (isset($_REQUEST['BrandID']) ? (int)$ths->MakeSecurParam($_REQUEST['BrandID']) : 0);
    $_REQUEST['Surprise'] = (isset($_REQUEST['Surprise']) ? (int)$ths->MakeSecurParam($_REQUEST['Surprise']) : 0);

    $_SESSION['_Lang_'] = 1;
    $MyFilters = [];
    $PrID = [];
    $IsInPrID = false;

    if(isset($_REQUEST['CatID']) && (int)$_REQUEST['CatID']){
        $_arr = $CatClass->get_all_child($_REQUEST['CatID']);
        $_arr[] = $_REQUEST['CatID'];
        $CondArr[] = ['cat_id', $_arr, 'in'];
        //$MyFilters[] = ['دسته بندی', $CatClass->get_title_by_id($_REQUEST['CatID'])];
    }

    if(isset($_REQUEST['IsAvailabe']) && $_REQUEST['IsAvailabe'] == 'true'){
        $CondArr[] = ['confirm_count', 1];
        $MyFilters[] = ['کالاهای موجود', 'بلی', 'IsAvailabe'];
    }
    if(isset($_REQUEST['price_from']) && (int)$_REQUEST['price_from']){
        $CondArr3 = [];
        $CondArr3[] = ['confirm', 1];
        $CondArr3[] = ['active', 1];
        $CondArr3[] = ['product_price`.`price_off', ($_REQUEST['price_from'] * 10000), '>='];
        $MyFilters[] = ['قیمت از', $ths->money(($_REQUEST['price_from'] * 1000)), 'price_from'];

        $PriceList3 = $ProdRelClass->get_all_price($CondArr3);

        $PrID3 = [];
        if($PriceList3){
            foreach($PriceList3 as $prl){
                $PrID3[] = $prl->product_id;
            }
        }

        if($PrID && count($PrID)){
            $PrID = array_intersect($PrID, $PrID3);
        }else{
            $PrID = $PrID3;
        }
        #var_dump($PrID3);
        #var_dump($PrID);

        $IsInPrID = true;
        #$CondArr[] = ['product_id', $PrID, 'in'];

    }
    if(isset($_REQUEST['price_to']) && (int)$_REQUEST['price_to']){
        $CondArr4 = [];
        $CondArr4[] = ['confirm', 1];
        $CondArr4[] = ['active', 1];
        $CondArr4[] = ['product_price`.`price_off', ($_REQUEST['price_to'] * 10000), '<='];
        $MyFilters[] = ['قیمت تا', $ths->money(($_REQUEST['price_to'] * 1000)), 'price_to'];

        $PriceList4 = $ProdRelClass->get_all_price($CondArr4);

        $PrID4 = [];
        if($PriceList4){
            foreach($PriceList4 as $prl){
                $PrID4[] = $prl->product_id;
            }
        }
        if($PrID && count($PrID)){
            $PrID = array_intersect($PrID, $PrID4);
        }else{
            $PrID = $PrID4;
        }
        #var_dump($PrID4);
        #var_dump($PrID);

        #$CondArr[] = ['product_id', $PrID4, 'in'];
        $IsInPrID = true;

    }
    /*if(isset($_REQUEST['search_filter']) && $_REQUEST['search_filter']){
        $CondArr[] = ['title', '%'.$_REQUEST['search_filter'].'%', 'like'];
    }*/
    if(isset($_REQUEST['SrchItem']) && $_REQUEST['SrchItem'] /*&& $ths->MakeCleanStr($_REQUEST['SrchItem'])*/){
        //$CondArr[] = ['title', '%'.$ths->MakeCleanStr($_REQUEST['SrchItem']).'%', 'like'];
        $CondArr[] = ['product`.`product_id`!=0 and (`title` like "%'.$ths->MakeCleanStr($_REQUEST['SrchItem']).'%" or `model` like "%'.$ths->MakeCleanStr($_REQUEST['SrchItem']).'%" or `product`.`code` like "%'.$ths->MakeCleanStr($_REQUEST['SrchItem']).'%") and `product`.`product_id', 0, '!='];
    }
    $CondArr[] = ['display', 1];
    $CondArr[] = ['deleted', 0];
    $CondArr[] = ['lang_id', $_SESSION['_Lang_']];
    #$CondArr[] = ['product`.`product_id`=`product_price`.`product_id` and  `product_price`.`confirm', 1];
    #1#$CondArr[] = ['product`.`product_id`!=0 and ((`product`.`product_id`=`product_price`.`product_id` and `product_price`.`count_info` not like "{avail_count:0%" and `product_price`.`count_info`!="" and `product_price`.`count_info` is not null and `product_price`.`count_info` not like "{avail_count:-%" and confirm_count=1) or (confirm_count=0)) and `product_price`.`price_off`>1000 and `product_price`.`confirm', 1];
    #2#$CondArr[] = ['confirm_count', 1];


    if(isset($_REQUEST['Surprise']) && $_REQUEST['Surprise'] === '1'){
        $CondArr2 = [];
        $CondArr2[] = [$AdvClass->MyTable.'`.`type', 6];
        $CondArr2[] = [$AdvClass->MyTable.'`.`lang', $_SESSION['_Lang_']];
        $CondArr2[] = [$AdvClass->MyTable.'`.`display', 1];
        $CondArr2[] = [$AdvClass->MyTable.'`.`deleted', 0];
        $CondArr2[] = ['start_date', date('Y-m-d H:i:s'), '<='];
        $CondArr2[] = ['end_date', date('Y-m-d H:i:s'), '>='];
        #$CondArr2[] = [$AdvClass->MyTable.'`.`link`=`'.$ProductClass->MyTable.'`.`'.$ProductClass->MyPrimary.'` and `'.$AdvClass->MyTable.'`.`id', '0', '!='];

        #$MyItems2 = $AdvClass->get_all($CondArr2, [$ProductClass->MyTable.'`.`'.$ProductClass->MyPrimary], ['`'.$AdvClass->MyTable.'`.`ordr`', 'desc'], [], $AdvClass->MyTable.'.'.$AdvClass->MyPrimary, $ProductClass->MyTable);
        $MyItems2 = $AdvClass->get_all($CondArr2, ['link', 'gift', 'end_date']);

        $IDsList = [];
        if($MyItems2){
            foreach($MyItems2 as $mi){
                $IDsList[] = $mi->link;
            }
        }
        $PrID = array_intersect($PrID, $IDsList);

        #$CondArr[] = ['product`.`product_id', $IDsList, 'in'];
        $IsInPrID = true;
    }


    $MyFilter = [];
    if(isset($_REQUEST)){
        foreach($_REQUEST as $ri=>$rv){
            if(strpos($ri, 'fliters') !== false){
                if($rv){
                    $f1 = explode('&', $rv);
                    foreach($f1 as $f2){
                        $f3 = explode('=', $f2);
                        if($f3[1] != 'none' && $f3[0] != 'BrandID'){
                            $MyFilter[str_replace('filter_', '', $f3[0])] = $f3[1];
                        }
                    }
                }
            }
        }

        if($MyFilter){
            $MyFilterProdIDs = [];
            foreach($MyFilter as $fltr_indx=>$fltr_val){
                $res = $ths->GetData($ths->query("select `product_id` from `".$PropClass->MyTable2."` where `prop_id`=".$fltr_indx." and `prop_val` like '%:".$fltr_val.";%';"));

                if($res){
                    foreach($res as $rr){
                        $MyFilterProdIDs[] = $rr->product_id;
                    }
                }else{
                    $MyFilterProdIDs = [];
                    break;
                }
            }
            $PrID_ = array_unique($MyFilterProdIDs);
            #$CondArr[] = ['product_id', $PrID_, 'in'];
        }
    }




    $OrderArr = ['confirm_count', 'desc', 'visit', 'desc'];
    if(isset($_REQUEST['order_by']) && $_REQUEST['order_by']){
        switch($_REQUEST['order_by']){
            case 'most_visit' :     $OrderArr = ['confirm_count', 'desc', 'confirm_price', 'desc', 'visit', 'desc']; break;
            case 'newest' :         $OrderArr = ['confirm_count', 'desc', 'confirm_price', 'desc', 'ordr', 'desc']; break;
            case 'price_down' :     /*$OrderArr = ['`product_price`.`price_off`', 'asc'];*/ break;
            case 'price_up' :       /*$OrderArr = ['`product_price`.`price_off`', 'desc'];*/ break;
            case 'most_score':      $OrderArr = ['confirm_count', 'desc', 'confirm_price', 'desc', 'score', 'desc']; break;
            default :               $OrderArr = ['confirm_count', 'desc', 'confirm_price', 'desc', 'visit', 'desc'];
        }
    }


    $PerPage = (isset($_REQUEST['per_page']) && (int)$_REQUEST['per_page']) ? $_REQUEST['per_page'] : 15;


    $Page = (isset($_REQUEST['CurrPage']) && (int)$_REQUEST['CurrPage']) ? $_REQUEST['CurrPage'] : 1;
    $Offset = ($Page - 1) * $PerPage;


    if($IsInPrID){
        $PrID_ = array_unique($PrID);
        $CondArr[] = ['product_id', $PrID_, 'in'];
    }


    $AllResult = $ProductClass->get_all($CondArr, ['product`.`product_id', 'brand_id']/* #1#, [], [], $GroupBy, $Tbl*/);

    $MyIDs = [];
    if($AllResult){
        $MyIDs = [];
        foreach($AllResult as $res){
            $MyIDs[] = $res->product_id;
        }
    }

    $CondPr = [];
    #$CondPr[] = ['id`!=0 and (`count_info` not like "{avail_count:0%" and `count_info`!="" and `count_info` is not null and `count_info` not like "{avail_count:-%") and `price_off', 1000, '>'];
    $CondPr[] = ['confirm', 1];
    //$CondPr[] = ['active', 1];
    $CondPr[] = ['product_id', $MyIDs, 'in'];
    $PriceList = $ProdRelClass->get_all_price($CondPr);

    if($PriceList){
        $PrID2 = [];
        $PrID5 = [];
        foreach($PriceList as $prl){
            $PrID5[] = $prl->product_id;
            $PrID2[$prl->product_id] = $prl->price_off;
        }
        if(in_array($_REQUEST['order_by'], ['price_down', 'price_up'])){
            asort($PrID2);
            $OrderArr = ['confirm_count', 'desc', 'confirm_price', 'desc', 'FIELD(`product_id`, "'.implode('","', array_keys($PrID2)).'")' , ($_REQUEST['order_by'] == 'price_down' ? 'asc' : 'desc')];
        }
        $IsInPrID = true;
        $PrID = array_intersect($MyIDs, $PrID5);
        #$CondArr[] = ['product_id', $PrID, 'in'];
    }else{
        $PrID = [];
        $IsInPrID = true;
        #$CondArr[] = ['id', '0'];
    }

    if($IsInPrID){
        $PrID_ = array_unique($PrID);
        $CondArr[] = ['product_id', $PrID_, 'in'];
    }


    $AllResult = $ProductClass->get_all($CondArr, ['product`.`product_id', 'brand_id']/* #1#, [], [], $GroupBy, $Tbl*/);


    $BrandStr = '';
    if($AllResult){
        $BrandArr = [];
        #$MyIDs = [];
        foreach($AllResult as $res){
            $BrandArr[] = $res->brand_id;
            #$MyIDs[] = $res->product_id;
        }
        $BrandArr = array_unique($BrandArr);

        $MyBrandTitle = [];
        $MyBrand = $BrandClass->get_all([['brand_id', $BrandArr, 'in'], ['lang_id', $_SESSION['_Lang_']], ['deleted', 0]], ['brand_id', 'title']);
        if($MyBrand){
            foreach($MyBrand as $bb){
                $MyBrandTitle[$bb->brand_id] = $bb->title;
            }
        }


        $brandArr_ = [];
        $SelBrand = [];
        if(isset($_REQUEST['BrandID'])){
            $brandArr_ = explode('-', (int)$_REQUEST['BrandID']);
        }

        foreach($BrandArr as $brnd){
            $IsSel = in_array($brnd, $brandArr_) ? true  : false;

            if($IsSel){
                $SelBrand[] = (isset($MyBrandTitle[$brnd]) ? $MyBrandTitle[$brnd] : 0);
            }

            if(isset($MyBrandTitle[$brnd])){
                $BrandStr .= '<label class="filter-list__item" for="brand_'.$brnd.'"><span class="input-check filter-list__input"><span class="input-check__body"><input type="checkbox" class="input-check__input" id="brand_'.$brnd.'" name="brand" onclick="SetBrand21('.$brnd.', this.checked)" '.($IsSel ? 'checked' : '').'><span class="input-check__box"></span><span class="input-check__icon"><svg width="9px" height="7px"><path d="M9,1.395L3.46,7L0,3.5L1.383,2095L3.46,4.2L7.617,0L9,1.395Z"/></svg></span></span></span><span class="filter-list__title">'.$MyBrandTitle[$brnd].'</span></label>';
            }
        }
    }

    if(isset($_REQUEST['BrandID']) && (int)$_REQUEST['BrandID']){
        $CondArr[] = ['brand_id', $_REQUEST['BrandID']];
        $AllResult = $ProductClass->get_all($CondArr, ['product`.`product_id']/*, [], [], $GroupBy, $Tbl*/);

        if(isset($SelBrand) && is_array($SelBrand)){
            $MyFilters[] = ['برند', implode(' - ', $SelBrand), 'BrandID'];
        }
    }


    if($AllResult){

        #$CondArr[] = [''];

        if($IsInPrID){
            $PrID_ = array_unique($PrID);
            $CondArr[] = ['product_id', $PrID_, 'in'];
        }

        $MyList = $ProductClass->get_all($CondArr, ['product`.`product_id', 'title', 'model', 'seo', 'img_id', 'confirm_count', 'isbest', 'confirm_price', 'score', 'score_person', 'rel_id'/*, 'product_price`.`price', 'product_price`.`price_off', $Tbl.'`.`code'*/], $OrderArr, [$Offset, $PerPage]/*, $GroupBy, $Tbl*/);

        if($MyList){
            $CodeArr = [];
            $PriceArr = [];
            $RelIDsArr = [];
            foreach($MyList as $mll){
                $CodeArr[] = $mll->product_id;
                if($mll->rel_id){
                    $RelIDsArr[$mll->product_id] = $mll->rel_id;
                }
            }

            $Cnd[] = ['confirm', 1];
            $Cnd[] = ['active', 1];
            $Cnd[] = ['product_id', $CodeArr, 'in'];
            $prd = $ProdRelClass->get_all_price($Cnd, ['product_id', 'asc', 'price_off', 'desc']);

            $AdvArr = [];
            if(isset($_REQUEST['Surprise']) && $_REQUEST['Surprise'] === '1'){

                $CondArr2 = [];
                $CondArr2[] = [$AdvClass->MyTable.'`.`type', 6];
                $CondArr2[] = [$AdvClass->MyTable.'`.`lang', $_SESSION['_Lang_']];
                $CondArr2[] = [$AdvClass->MyTable.'`.`display', 1];
                $CondArr2[] = [$AdvClass->MyTable.'`.`deleted', 0];
                $CondArr2[] = ['start_date', date('Y-m-d H:i:s'), '<='];
                $CondArr2[] = ['end_date', date('Y-m-d H:i:s'), '>='];
                $MyItems2 = $AdvClass->get_all($CondArr2, ['link', 'gift', 'end_date']);

                if($MyItems2){
                    foreach($MyItems2 as $a){
                        $AdvArr[$a->link] = ['gift'=>$ths->MyDecode($a->gift), 'type'=>6, 'end_date'=>$a->end_date];
                    }
                }
            }else{
                $Cnd2 = [];
                $Cnd2[] = [$AdvClass->MyTable.'`.`lang', $_SESSION['_Lang_']];
                $Cnd2[] = [$AdvClass->MyTable.'`.`display', 1];
                $Cnd2[] = [$AdvClass->MyTable.'`.`deleted', 0];
                $Cnd2[] = [$AdvClass->MyTable.'`.`start_date', date('Y-m-d H:i:s'), '<='];
                $Cnd2[] = [$AdvClass->MyTable.'`.`end_date', date('Y-m-d H:i:s'), '>='];
                $Cnd2[] = [$AdvClass->MyTable.'`.`link', $CodeArr, 'in'];

                $AdvArr_ = $AdvClass->get_all($Cnd2, ['link', 'gift', 'type', 'end_date']);
                if($AdvArr_){
                   foreach($AdvArr_ as $a){
                       $AdvArr[$a->link] = ['gift'=>$ths->MyDecode($a->gift), 'type'=>$a->type, 'end_date'=>$a->end_date];
                   }
                }
            }

           $priceoff = [];
            if($prd){
                foreach($prd as $pprd){

                    if(isset($pprd->count_info)){
                        $_ = $ths->MyDecode($pprd->count_info);
                        if($_['avail_count'] > 0){
                            $priceoff[$pprd->product_id] = $pprd->price_off;
                            $PriceArr[$pprd->product_id]['price'] = $pprd->price;
                            $PriceArr[$pprd->product_id]['price_off'] = $pprd->price_off;

                            //if exist related product
                            if(isset($RelIDsArr[$pprd->product_id]) && $RelIDsArr[$pprd->product_id]){
                                $rl = explode('-', $RelIDsArr[$pprd->product_id]);
                                if($rl && count($rl)){
                                    foreach($rl as $rll){
                                        $CondRelID = [];
                                        $CondRelID[] = ['product_id', $rll];
                                        $CondRelID[] = ['confirm', 1];
                                        $CondRelID[] = ['active', 1];
                                        $CondRelID[] = ['prop', $pprd->prop];

                                        $MyPrice2 = $ProdRelClass->get_all_price($CondRelID);

                                        if($MyPrice2){
                                            $cnt = $ths->MyDecode($MyPrice2[0]->count_info);
                                            if($cnt){
                                                if(isset($cnt['avail_count']) && (int)$cnt['avail_count']>0){
                                                    $PriceArr[$pprd->product_id]['price_off'] += $MyPrice2[0]->price_off;
                                                    $PriceArr[$pprd->product_id]['price'] += $MyPrice2[0]->price;
                                                }else{
                                                    $PriceArr[$pprd->product_id]['price_off'] = 0;
                                                    $PriceArr[$pprd->product_id]['price'] = 0;
                                                }
                                            }
                                        }
                                    }
                                }
                            }

                            
                            if(isset($AdvArr[$pprd->product_id]['gift'][1])){
                                if($AdvArr[$pprd->product_id]['type'] == 7){
                                    $PriceArr[$pprd->product_id]['price'] = ($pprd->price_off * ((100 + $AdvArr[$pprd->product_id]['gift'][1]) / 100));
                                }else{
                                    $PriceArr[$pprd->product_id]['price_off'] = ($pprd->price_off * ((100 - $AdvArr[$pprd->product_id]['gift'][1]) / 100));
                                }
                            }
                        }
                    }
                }
                
                foreach($prd as $pprd){
                    if(!isset($PriceArr[$pprd->product_id]['price'])){
                        $PriceArr[$pprd->product_id]['price'] = 0;
                        $PriceArr[$pprd->product_id]['price_off'] = 0;
                    }
                }
            }
        }
    }
    //var_dump($priceoff);
?>

<script>
$( "#button" ).click(function() {
    if ( $("#cont").height() == 70)
          $( "#cont" ).animate({ height: 170 }, 500 );
    else
          $( "#cont" ).animate({ height: 70 }, 500 );
});
</script>
<style>@media (max-width:575.98px){.block-split__row.row.no-gutters .block, .block.block-products-carousel {
    padding-left: 5px;
    padding-right: 5px;}
}</style>
<div class="block">
<div class="products-view">
<div id="cont" class="products-view__options view-options view-options--offcanvas--mobile">
    <div class="view-options__body">
        <button id="button" type="button" class="view-options__filters-button filters-button">
            <span class="filters-button__icon">
                <svg width="16" height="16" id="Layer_1" style="enable-background:new 0 0 128 128;" version="1.1" viewBox="0 0 128 128" width="128px" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><g><g><g><line style="fill:none;stroke:#2F3435;stroke-width:12;stroke-linecap:square;stroke-miterlimit:10;" x1="63.185" x2="37.821" y1="84.061" y2="109.423"/><line style="fill:none;stroke:#2F3435;stroke-width:12;stroke-linecap:square;stroke-miterlimit:10;" x1="37.821" x2="12.456" y1="109.423" y2="84.061"/></g><line style="fill:none;stroke:#2F3435;stroke-width:12;stroke-miterlimit:10;" x1="37.821" x2="37.821" y1="109.423" y2="9.801"/></g><g><g><line style="fill:none;stroke:#2F3435;stroke-width:12;stroke-linecap:square;stroke-miterlimit:10;" x1="64.815" x2="90.181" y1="44.241" y2="18.877"/><line style="fill:none;stroke:#2F3435;stroke-width:12;stroke-linecap:square;stroke-miterlimit:10;" x1="90.181" x2="115.544" y1="18.877" y2="44.241"/></g><line style="fill:none;stroke:#2F3435;stroke-width:12;stroke-miterlimit:10;" x1="90.181" x2="90.181" y1="18.877" y2="118.199"/></g></g></svg>
            </span>
        </button>
        <button type="button" class="view-options__filters-button filters-button">
            <span class="filters-button__icon">
                <svg width="16" height="16">
                    <path d="M7,14v-2h9v2H7z M14,7h2v2h-2V7z M12.5,6C12.8,6,13,6.2,13,6.5v3c0,0.3-0.2,0.5-0.5,0.5h-2
	C10.2,10,10,9.8,10,9.5v-3C10,6.2,10.2,6,10.5,6H12.5z M7,2h9v2H7V2z M5.5,5h-2C3.2,5,3,4.8,3,4.5v-3C3,1.2,3.2,1,3.5,1h2
	C5.8,1,6,1.2,6,1.5v3C6,4.8,5.8,5,5.5,5z M0,2h2v2H0V2z M9,9H0V7h9V9z M2,14H0v-2h2V14z M3.5,11h2C5.8,11,6,11.2,6,11.5v3
	C6,14.8,5.8,15,5.5,15h-2C3.2,15,3,14.8,3,14.5v-3C3,11.2,3.2,11,3.5,11z"/>
                </svg>
            </span>
            <span class="filters-button__title">فیلتر ها</span>
        </button>
        
        <div class="view-options__layout layout-switcher">
            <div class="layout-switcher__list">
                <button type="button" class="ddis layout-switcher__button layout-switcher__button--active" data-layout="grid" data-with-features="false" aria-label="view grid">
                    <svg width="16" height="16">
                        <path d="M15.2,16H9.8C9.4,16,9,15.6,9,15.2V9.8C9,9.4,9.4,9,9.8,9h5.4C15.6,9,16,9.4,16,9.8v5.4C16,15.6,15.6,16,15.2,16z M15.2,7
	H9.8C9.4,7,9,6.6,9,6.2V0.8C9,0.4,9.4,0,9.8,0h5.4C15.6,0,16,0.4,16,0.8v5.4C16,6.6,15.6,7,15.2,7z M6.2,16H0.8
	C0.4,16,0,15.6,0,15.2V9.8C0,9.4,0.4,9,0.8,9h5.4C6.6,9,7,9.4,7,9.8v5.4C7,15.6,6.6,16,6.2,16z M6.2,7H0.8C0.4,7,0,6.6,0,6.2V0.8
	C0,0.4,0.4,0,0.8,0h5.4C6.6,0,7,0.4,7,0.8v5.4C7,6.6,6.6,7,6.2,7z"/>
                    </svg>
                </button>
                <button type="button" class="mdis layout-switcher__button" data-layout="grid" data-with-features="false" aria-label="view grid">
                    <svg width="16" height="16">
                        <path d="M15.2,16H9.8C9.4,16,9,15.6,9,15.2V9.8C9,9.4,9.4,9,9.8,9h5.4C15.6,9,16,9.4,16,9.8v5.4C16,15.6,15.6,16,15.2,16z M15.2,7
	H9.8C9.4,7,9,6.6,9,6.2V0.8C9,0.4,9.4,0,9.8,0h5.4C15.6,0,16,0.4,16,0.8v5.4C16,6.6,15.6,7,15.2,7z M6.2,16H0.8
	C0.4,16,0,15.6,0,15.2V9.8C0,9.4,0.4,9,0.8,9h5.4C6.6,9,7,9.4,7,9.8v5.4C7,15.6,6.6,16,6.2,16z M6.2,7H0.8C0.4,7,0,6.6,0,6.2V0.8
	C0,0.4,0.4,0,0.8,0h5.4C6.6,0,7,0.4,7,0.8v5.4C7,6.6,6.6,7,6.2,7z"/>
                    </svg>
                </button>
                <button type="button" class="ddis layout-switcher__button" data-layout="table" data-with-features="false" aria-label="view grid">
                    <svg width="16" height="16">
                        <path d="M15.2,16H0.8C0.4,16,0,15.6,0,15.2v-2.4C0,12.4,0.4,12,0.8,12h14.4c0.4,0,0.8,0.4,0.8,0.8v2.4C16,15.6,15.6,16,15.2,16z
	 M15.2,10H0.8C0.4,10,0,9.6,0,9.2V6.8C0,6.4,0.4,6,0.8,6h14.4C15.6,6,16,6.4,16,6.8v2.4C16,9.6,15.6,10,15.2,10z M15.2,4H0.8
	C0.4,4,0,3.6,0,3.2V0.8C0,0.4,0.4,0,0.8,0h14.4C15.6,0,16,0.4,16,0.8v2.4C16,3.6,15.6,4,15.2,4z"/>
                    </svg>
                </button>
                                <button type="button" class="mdis layout-switcher__button  layout-switcher__button--active" data-layout="table" data-with-features="false" aria-label="view table">
                    <svg width="16" height="16">
                        <path d="M15.2,16H0.8C0.4,16,0,15.6,0,15.2v-2.4C0,12.4,0.4,12,0.8,12h14.4c0.4,0,0.8,0.4,0.8,0.8v2.4C16,15.6,15.6,16,15.2,16z
	 M15.2,10H0.8C0.4,10,0,9.6,0,9.2V6.8C0,6.4,0.4,6,0.8,6h14.4C15.6,6,16,6.4,16,6.8v2.4C16,9.6,15.6,10,15.2,10z M15.2,4H0.8
	C0.4,4,0,3.6,0,3.2V0.8C0,0.4,0.4,0,0.8,0h14.4C15.6,0,16,0.4,16,0.8v2.4C16,3.6,15.6,4,15.2,4z"/>
                    </svg>
                </button>
            </div>
        </div>
        <?php if(isset($MyList)){?>
        <div class="view-options__legend">
            نمایش
            <?=((isset($MyList) && $MyList) ? count($MyList) : 0)?>
            از
            <?=count($AllResult)?>
            محصول
        </div>
        <?php }?>
        <div class="view-options__spring"></div>
        <div class="view-options__select"><label for="order_by_">مرتب‌سازی:</label>
            <select id="order_by_" class="form-control form-control-sm" name="order_by_" onchange="SetOrderList21(this.value)">
                <option value="newest" <?=($_REQUEST['order_by'] == 'newest' ? 'selected' : '')?>>جدیدترین</option>
                <option value="price_down" <?=($_REQUEST['order_by'] == 'price_down' ? 'selected' : '')?>>ارزانترین</option>
                <option value="price_up" <?=($_REQUEST['order_by'] == 'price_up' ? 'selected' : '')?>>گرانترین</option>
                <option value="most_visit" <?=($_REQUEST['order_by'] == 'most_visit' ? 'selected' : '')?>>پربازدیدترین</option>
                <option value="most_score" <?=($_REQUEST['order_by'] == 'most_score' ? 'selected' : '')?>>بیشترین امتیاز</option>
            </select></div>
        <div class="view-options__select">
            <label for="per_page_">نمایش:</label>
            <select id="per_page_" class="form-control form-control-sm" name="per_page_" onchange="SetPerPg21(this.value)">
                <option value="1" <?=($PerPage == 1 ? 'selected' : '')?>>1</option>
                <option value="5" <?=($PerPage == 5 ? 'selected' : '')?>>5</option>
                <option value="10" <?=($PerPage == 10 ? 'selected' : '')?>>10</option>
                <option value="20" <?=($PerPage == 20 ? 'selected' : '')?>>20</option>
                <option value="30" <?=($PerPage == 30 ? 'selected' : '')?>>30</option>
                <option value="40" <?=($PerPage == 40 ? 'selected' : '')?>>40</option>
                <option value="50" <?=($PerPage == 50 ? 'selected' : '')?>>50</option>
            </select>
        </div>
    </div>
    <?php
      if(!isset($_SESSION['_Lang_'])){
            $_SESSION['_Lang_'] = 1;
        }

        $c1 = $CatClass->get_all_child($CatID);
        $c1[] = $CatID;


        #$cnd1[] = ['deleted', 0];
        #$cnd1[] = ['display', 1];
        $cnd1[] = ['cat_id', $c1, 'in'];
        #$cnd1[] = ['lang_id', $_SESSION['_Lang_']];
        $p1 = $ProductClass->get_all($cnd1, ['brand_id']);
        $b1 = [];
        if($p1){
            foreach($p1 as $pp1){
                $b1[] = $pp1->brand_id;
            }
        }


        $CondArr = [];
        #$CondArr[] = ['display', 1];
        $CondArr[] = ['deleted', 0];
        $CondArr[] = ['brand_id', $b1, 'in'];
        $CondArr[] = ['lang_id', /*$_SESSION['_Lang_']*/ 1];
        $AllBrand = $BrandClass->get_all($CondArr, [], ['ordr', 'desc'], [0, 16]);
        $countb = $AllBrand ? count($AllBrand) : 0;
        
   if($AllBrand && $countb <= 3 && $countb > 0 && !$BrandID){
    ?>
    <div class="view-options__body view-options__body--filters">
        <div class="view-options__label">برندهای مرتبط</div>
        <div class="applied-filters">
            <script>let text = document.getElementById("titmain").textContent;</script>
            <ul class="applied-filters__list">
                <?php foreach($AllBrand as $br){ 
                 $CatBrnd = [];
        $CatBrnd[] = ['cat_id', $CatID];
        $CatBrnd[] = ['brand_id', $br->brand_id];
        $CatBrnd[] = ['display', 1];
        $CatBrnd[] = ['deleted', 0];

        $CatBrandRow_ = $CatBrandClass->get_all($CatBrnd);
        if($CatBrandRow_){
            $CatBrandRow = $CatBrandRow_[0];
        }
       //var_dump($CatBrandRow_);
                ?>
                    <li class="breadcrumb__item breadcrumb__item--parent breadcrumb__item--first ml-1">
                        <a href="<?=$conf->BaseRoot2.'category/'.$CatID.'/1/'.$ths->UrlFriendly($br->title).'?BrandID='.$br->brand_id?>" class="breadcrumb__item-link">
                          <span class="ml-1" id="onv<?=$br->brand_id?>"></span>  <?=" ".$br->title?>
                        </a>
                    </li>
                        <script>
document.getElementById("onv<?=$br->brand_id?>").innerHTML = text;  
</script>
                    <?php }?>
                </ul>
            
            
         
        </div>
    </div>

<?php }
?>    
  <?php  if(isset($MyFilters) && is_array($MyFilters) && count($MyFilters)){?>
    <div class="view-options__body view-options__body--filters">
        <div class="view-options__label">فیلترهای فعال</div>
        <div class="applied-filters">
            <ul class="applied-filters__list">
                <?php foreach($MyFilters as $fltr){ ?>
                            <li class="applied-filters__item">
                                <a href="#"class="applied-filters__button applied-filters__button--filter">
                                    <?=$fltr[0]?> : <?=$fltr[1]?>
                                    <svg width="9" height="9" onclick="ClearFilter021('<?=$fltr[2]?>')">
                                        <path d="M9,8.5L8.5,9l-4-4l-4,4L0,8.5l4-4l-4-4L0.5,0l4,4l4-4L9,0.5l-4,4L9,8.5z"/>
                                    </svg>
                                </a>
                            </li>
                <?php }?>
                <li class="applied-filters__item">
                    <button type="button" class="applied-filters__button applied-filters__button--clear" onclick="ClearFilter21()">
                        پاک کردن همه
                    </button>
                </li>
            </ul>
        </div>
    </div>
    <?php }?>
</div>
    <div class="mdis products-view__list products-list products-list--grid--4" data-layout="table"
     data-with-features="false">
<div class="mdis products-list__head">
    <div class="products-list__column products-list__column--image">تصویر</div>
    <div class="products-list__column products-list__column--meta">کد محصول</div>
    <div class="products-list__column products-list__column--product">محصول</div>
    <div class="products-list__column products-list__column--rating">رتبه</div>
    <div class="products-list__column products-list__column--price">قیمت</div>
</div>
<div class="products-list__content">
<?php
    if(isset($MyList) && $MyList){
        foreach($MyList as $itmi=>$ml){

            $AppOnly = (isset($AdvArr[$ml->product_id]['type']) && $AdvArr[$ml->product_id]['type'] == 11);
?>
<div class="products-list__item <?=($AppOnly ? 'app-only' : '')?>">
    <div class="product-card">
        <div class="product-card__actions-list">
            <button class="product-card__action product-card__action--wishlist" type="button" aria-label="Add to لیست علاقه مندیها">
                <svg width="16" height="16">
                    <path d="M13.9,8.4l-5.4,5.4c-0.3,0.3-0.7,0.3-1,0L2.1,8.4c-1.5-1.5-1.5-3.8,0-5.3C2.8,2.4,3.8,2,4.8,2s1.9,0.4,2.6,1.1L8,3.7
	l0.6-0.6C9.3,2.4,10.3,2,11.3,2c1,0,1.9,0.4,2.6,1.1C15.4,4.6,15.4,6.9,13.9,8.4z"/>
                </svg>
            </button>
            <a onclick="AddToCompare('<?=$ml->product_id?>')" id="back2Top" class="product-card__action product-card__action--compare" type="button" aria-label="Add to compare">
                <svg width="16" height="16" version="1.1" id="Capa_1"  x="0px" y="0px" viewBox="0 0 469.333 469.333" style="enable-background:new 0 0 469.333 469.333;" xml:space="preserve">
                    <g>
                        <path d="M192,42.667H85.333c-23.573,0-42.667,19.093-42.667,42.667V384c0,23.573,19.093,42.667,42.667,42.667H192v42.667h42.667     V0H192V42.667z M192,362.667H85.333l106.667-128V362.667z"/>
                        <path d="M384,42.667H277.333v42.667H384v277.333l-106.667-128v192H384c23.573,0,42.667-19.093,42.667-42.667V85.333     C426.667,61.76,407.573,42.667,384,42.667z"/>
                    </g>
                </svg>
            </a>
        </div>
        

        <div class="product-card__image">
            <a href="<?=(!$AppOnly ? $conf->BaseRoot2.'product/'.$ml->product_id.'/'.$ml->seo : '#')?>" title="<?=$ml->title.($ml->model ? ' '.$ml->model : '')?>">

                <?php
                    if($ml->img_id){
                        $MyImg = $FilesClass->get_by_id($ml->img_id, 1);
                        if($MyImg){
                            $MyImgUrl = dirname($MyImg['path2']).'/'.$MyImg['fileid'].'_thumb_'.$MyImg['filename'];
                        }else{
                            $MyImgUrl = $conf->BaseRoot2.'MyFile/Product/none.jpg';
                        }
                    }else{
                        $MyImgUrl = $conf->BaseRoot2.'MyFile/Product/none.jpg';
                    }
                ?>
                <img class="pss" src="<?=$MyImgUrl?>" alt="<?=$ml->title.($ml->model ? ' '.$ml->model : '')?>">
            </a>

            <?php
                $IsSurprise = (isset($AdvArr[$ml->product_id]['type']) && $AdvArr[$ml->product_id]['type'] == 6);

                if($IsSurprise && $ml->confirm_count && $ml->confirm_price){
            ?>
            <div class="c-promotion__badge c-promotion__badge--incredible-offer    "><div class="c-promotion__special-deal-timer ">
            <div class="c-product-box__amazing"><div class="c-product-box__timer   js-counter" data-countdown="2021-02-04 00:00:00">
                    <div class="block-sale__timer">
                    <?php
                        if(date('Y-m-d H:i:s') > $AdvArr[$ml->product_id]['end_date']){
                            $MyDiff_ = 'زمان به پایان رسیده است';
                            $MyDiff = '';
                        }else{
                            $date1 = new DateTime($AdvArr[$ml->product_id]['end_date']);
                            $date2 = new DateTime(date('Y-m-d H:i:s'));

                            $MyDiff_ = $date2->diff($date1)->format("%a:%h:%i:%s");
                            $MyDiff = explode(":", $MyDiff_);
                        }
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
                                        <!--<div class="timer__part-label1">ثانیه</div>-->
                                    </div>
                                    <div class="dot">:</div>
                                    <div class="timer__part1">
                                        <div class="timer__part-value1 timer__part-value--minutes timer_minutes<?=($itmi + 1)?>">
                                            <?=sprintf('%02d', $MyDiff[2])?>
                                        </div>
                                        <!--<div class="timer__part-label1">دقیقه</div>-->
                                    </div>
                                    <div class="dot">:</div>
                                    <div class="timer__part1">
                                        <div class="timer__part-value1 timer__part-value--hours  timer_hours<?=($itmi + 1)?>">
                                            <?=sprintf('%02d', $MyDiff[1])?>
                                        </div>
                                        <!--<div class="timer__part-label1">ساعت</div>-->
                                    </div>
                                    <div class="dot">:</div>
                                    <div class="timer__part1">
                                        <div class="timer__part-value1 timer__part-value--days  timer_days<?=($itmi + 1)?>">
                                            <?=$MyDiff[0]?>
                                        </div>
                                        <!--<div class="timer__part-label1">روز</div>-->
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
                    <img src="<?=$conf->BaseRoot2?>images/icons8-timer-24.png" alt="timer" title="تایمر" />
                </div>
                <div class="c-product-box__remained"></div>
            </div> </div> </div>
            <?php }?>

        </div>
        <div class="product-card__info">
            <div class="product-card__name">
                <div>
                    <?php
                        if($IsSurprise){
                    ?>
                        <!--<div class="product-card__badges">b
                            <div class="tag-badge tag-badge--hot">
                                شگفت انگیز
                            </div>
                        </div>-->
                    <?php }?>

                    <a href="<?=(!$AppOnly ? $conf->BaseRoot2.'product/'.$ml->product_id.'/'.$ml->seo : '#')?>">
                        <?=$ml->title.($ml->model ? ' '.$ml->model : '')?>
                    </a>
                </div>
            </div>
            <div class="product-card__rating" style="direction: rtl">
                <?php
                    $MyScore = round($ml->score / 2 , 1);
                ?>
                <div class="product-card__rating-label">
                    <div class="star on"></div>
                    <?=$MyScore?>
                    از
                    <?=$ml->score_person?>
                    نظر
                </div>
            </div>


            <div class="c-product-box__digiplus">
                <?php if(isset($AdvArr[$ml->product_id]['gift'][3])){ ?>
                    <span class="c-product-box__digiplus-data c-digiplus-sign--before">
                        <img class="ngift" src="<?=$conf->BaseRoot2?>images/amazing.png" alt="gift card" title="کارت هدیه نقدی" width="24" style="width:24px !important">
                        <?=$ths->money($AdvArr[$ml->product_id]['gift'][3])?>
                        <?=$MyGiftType2[3]?>
                    </span>
                <?php } ?>
            </div>

        </div>
        
        <div class="product-card__footer">
            <?php
                if(!$AppOnly){
                    if($ml->confirm_count && $ml->confirm_price && isset($PriceArr[$ml->product_id]['price_off']) && (int)($PriceArr[$ml->product_id]['price_off'])>1000){
                        if($PriceArr[$ml->product_id]['price_off'] != $PriceArr[$ml->product_id]['price']){
                            
            ?>
                            <div class="product-card__prices">
                                <div class="product-card__price product-card__price--new">
                                    <?=$ths->money($PriceArr[$ml->product_id]['price_off'] / 10)?>
                                    <span class="MoneyUnit">
                                                             تومان
                                     </span>
                                </div>
                                <?php
                                      if($PriceArr[$ml->product_id]['price'] > $PriceArr[$ml->product_id]['price_off']){
                                              ?>
                                <div class="product-card__price product-card__price--old">
                                    <?php
                                        $d = round(100 - (($PriceArr[$ml->product_id]['price_off'] * 100) / $PriceArr[$ml->product_id]['price']) , 0);
                                        if($d != $Cnfg['PriceOffPercent']){
                                    ?>
                                        <span class="badge-danger d-inline-block ml-2 p-1 rounded mt-1">
                                            <?=$d?>%
                                        </span>
                                        <?=$ths->money($PriceArr[$ml->product_id]['price'] / 10)?>
                                        <span class="MoneyUnit">
                                                                 تومان
                                        </span>
                                    <?php }?>
                                </div>
                                <?php } ?>
                                
                            </div>
            <?php
                        }else{
            ?>
                            <div class="product-card__prices">
                                <div class="product-card__price product-card__price--current">
                                    <?=$ths->money($PriceArr[$ml->product_id]['price_off'] / 10)?>
                                    <span class="MoneyUnit">
                                                             تومان
                                    </span>
                                </div>
                            </div>
            <?php
                        }
                    }
            ?>

            <?php if($ml->confirm_count && $ml->confirm_price && $PriceArr[$ml->product_id]['price_off']  && $PriceArr[$ml->product_id]['price_off'] > 1000){?>

            <button class="product-card__addtocart-icon js-cd-add-to-cart prid_<?=$ml->product_id?>" type="button" aria-label="افزودن به سبد خرید">
                <svg width="20" height="20">
                    <circle cx="7" cy="17" r="2"/>
                    <circle cx="15" cy="17" r="2"/>
                    <path
                        d="M20,4.4V5l-1.8,6.3c-0.1,0.4-0.5,0.7-1,0.7H6.7c-0.4,0-0.8-0.3-1-0.7L3.3,3.9C3.1,3.3,2.6,3,2.1,3H0.4C0.2,3,0,2.8,0,2.6
	V1.4C0,1.2,0.2,1,0.4,1h2.5c1,0,1.8,0.6,2.1,1.6L5.1,3l2.3,6.8c0,0.1,0.2,0.2,0.3,0.2h8.6c0.1,0,0.3-0.1,0.3-0.2l1.3-4.4
	C17.9,5.2,17.7,5,17.5,5H9.4C9.2,5,9,4.8,9,4.6V3.4C9,3.2,9.2,3,9.4,3h9.2C19.4,3,20,3.6,20,4.4z"/>
                </svg>
            </button>

            <?php }else{ ?>
                <div class="text-center w-100 listcat mt-2 mb-2">
                    ناموجود
                </div>
            <?php }?>
                <?php }else{?>
                    <div class=" badge-danger p-2 rounded m-auto small">
                        <a href="" class="text-white">
                        <img src="<?=$conf->BaseRoot2?>images/for-app.png">
                        ویژه اپلیکیشن
                        </a>
                    </div>
                <?php }?>


        </div>
    </div>
</div>
<?php
        }
    }
?>
</div>
</div>
    <div class="ddis products-view__list products-list products-list--grid--4" data-layout="grid"
     data-with-features="false">
<div class="mdis products-list__head">
    <div class="products-list__column products-list__column--image">تصویر</div>
    <div class="products-list__column products-list__column--meta">کد محصول</div>
    <div class="products-list__column products-list__column--product">محصول</div>
    <div class="products-list__column products-list__column--rating">رتبه</div>
    <div class="products-list__column products-list__column--price">قیمت</div>
</div>
<div class="products-list__content">
<?php
    if(isset($MyList) && $MyList){
        foreach($MyList as $itmi=>$ml){

            $AppOnly = (isset($AdvArr[$ml->product_id]['type']) && $AdvArr[$ml->product_id]['type'] == 11);
?>
<div class="products-list__item <?=($AppOnly ? 'app-only' : '')?>">
    <div class="product-card">

        <div class="product-card__actions-list">
            <button class="product-card__action product-card__action--wishlist" type="button" aria-label="Add to لیست علاقه مندیها">
                <svg width="16" height="16">
                    <path d="M13.9,8.4l-5.4,5.4c-0.3,0.3-0.7,0.3-1,0L2.1,8.4c-1.5-1.5-1.5-3.8,0-5.3C2.8,2.4,3.8,2,4.8,2s1.9,0.4,2.6,1.1L8,3.7
	l0.6-0.6C9.3,2.4,10.3,2,11.3,2c1,0,1.9,0.4,2.6,1.1C15.4,4.6,15.4,6.9,13.9,8.4z"/>
                </svg>
            </button>
            <a onclick="AddToCompare('<?=$ml->product_id?>')" id="back2Top" class="product-card__action product-card__action--compare" type="button" aria-label="Add to compare">
                <svg width="16" height="16" version="1.1" id="Capa_1"  x="0px" y="0px" viewBox="0 0 469.333 469.333" style="enable-background:new 0 0 469.333 469.333;" xml:space="preserve">
                    <g>
                        <path d="M192,42.667H85.333c-23.573,0-42.667,19.093-42.667,42.667V384c0,23.573,19.093,42.667,42.667,42.667H192v42.667h42.667     V0H192V42.667z M192,362.667H85.333l106.667-128V362.667z"/>
                        <path d="M384,42.667H277.333v42.667H384v277.333l-106.667-128v192H384c23.573,0,42.667-19.093,42.667-42.667V85.333     C426.667,61.76,407.573,42.667,384,42.667z"/>
                    </g>
                </svg>
            </a>
        </div>

        <?php if(isset($AdvArr[$ml->product_id]['gift'][2]) || isset($AdvArr[$ml->product_id]['gift'][4]) || isset($AdvArr[$ml->product_id]['gift'][5])){?>
            <div class="c-product-box__digiplus c-product-box__digiplus--full psa">
                <span class="c-product-box__digiplus-data c-digiplus-sign--before">
                    +
                    <img class="igift" src="<?=$conf->BaseRoot2?>images/icon/gift.png" alt="gift" title="هدیه روی کالا" width="16" />
                </span>
                          
                <div class="c-wiki__container js-dk-wiki ">
                  <div class="c-wiki__arrow"></div>
                  <p class="c-wiki__text">
                  <?php
                      $AllowedGift = [2, 4, 5];
                      foreach($AllowedGift as $gti=>$gt){
                        if(isset($AdvArr[$ml->product_id]['gift'][$gt])){
                  ?>
                            <p class="c-wiki__text">
                                <?=$AdvArr[$ml->product_id]['gift'][$gt]?>
                                <?=$MyGiftType2[$gt]?>
                            </p>
                  <?php
                        }
                      }
                  ?>
                  </p>
                </div>
            </div>
        <?php }?>


        <?php
            $IsSurprise = (isset($AdvArr[$ml->product_id]['type']) && $AdvArr[$ml->product_id]['type'] == 6);
            if($IsSurprise){
        ?>
            <!--<div class="product-card__badges">
                <div class="tag-badge tag-badge--hot">a
                    شگفت انگیز
                </div>
            </div>-->
        <?php } ?>
        
        <div class="product-card__image">
            <a href="<?=(!$AppOnly ? $conf->BaseRoot2.'product/'.$ml->product_id.'/'.$ml->seo : '#')?>" title="<?=$ml->title.($ml->model ? ' '.$ml->model : '')?>">
                <?php
                    if($ml->img_id){
                        $MyImg = $FilesClass->get_by_id($ml->img_id, 1);
                        if($MyImg && is_array($MyImg)){
                            $MyImgUrl = dirname($MyImg['path2']).'/'.$MyImg['fileid'].'_thumb_'.$MyImg['filename'];
                        }else{
                            $MyImgUrl = $conf->BaseRoot2.'MyFile/Product/none.jpg';
                        }
                    }else{
                        $MyImgUrl = $conf->BaseRoot2.'MyFile/Product/none.jpg';
                    }
                ?>
                <img class="pssm" src="<?=$MyImgUrl?>" alt="<?=$ml->title.($ml->model ? ' '.$ml->model : '')?>">
            </a>

            <?php if($IsSurprise && $ml->confirm_count && $ml->confirm_price){?>
            <div class="c-promotion__badge c-promotion__badge--incredible-offer    "><div class="c-promotion__special-deal-timer ">
             <div class="c-product-box__amazing"><div class="c-product-box__timer   js-counter">
                 <div class="block-sale__timer">
                    <?php
                        if(date('Y-m-d H:i:s') > $AdvArr[$ml->product_id]['end_date']){
                            $MyDiff_ = 'زمان به پایان رسیده است';
                            $MyDiff = '';
                        }else{
                            $date1 = new DateTime($AdvArr[$ml->product_id]['end_date']);
                            $date2 = new DateTime(date('Y-m-d H:i:s'));

                            $MyDiff_ = $date2->diff($date1)->format("%a:%h:%i:%s");
                            $MyDiff = explode(":", $MyDiff_);
                        }
                    ?>
                    <input type="hidden" id="Timer<?=($itmi + 1)?>" class="Timer<?=($itmi + 1)?>" value="<?=$MyDiff_?>">
                    <div class="timer timer<?=($itmi + 1)?>">
                        <?php
                            if(isset($MyDiff) && is_array($MyDiff)){
                        ?>
                        <div class="timer__part1">
                            <div class="timer__part-value1 timer__part-value--seconds timer_seconds<?=($itmi + 1)?>">
                                <?=$MyDiff[3]?>
                            </div>
                            <!--<div class="timer__part-label1">ثانیه</div>-->
                        </div>
                        <div class="dot">:</div>
                        <div class="timer__part1">
                            <div class="timer__part-value1 timer__part-value--minutes timer_minutes<?=($itmi + 1)?>">
                                <?=$MyDiff[2]?>
                            </div>
                            <!--<div class="timer__part-label1">دقیقه</div>-->
                        </div>
                        <div class="dot">:</div>
                        <div class="timer__part1">
                            <div class="timer__part-value1 timer__part-value--hours  timer_hours<?=($itmi + 1)?>">
                                <?=$MyDiff[1]?>
                            </div>
                            <!--<div class="timer__part-label1">ساعت</div>-->
                        </div>
                        <div class="dot">:</div>
                        <div class="timer__part1">
                            <div class="timer__part-value1 timer__part-value--days  timer_days<?=($itmi + 1)?>">
                                <?=$MyDiff[0]?>
                            </div>
                            <!--<div class="timer__part-label1">روز</div>-->
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
             </div></div></div>
            <?php }?>
        </div>
        <div class="product-card__info">
            <div class="product-card__name">
                <div>
                    <?php
                        if($IsSurprise){
                    ?>
                        <div class="product-card__badges">
                            <div class="tag-badge tag-badge--hot">
                                شگفت انگیز
                            </div>
                        </div>
                    <?php }?>

                    <a href="<?=(!$AppOnly ? $conf->BaseRoot2.'product/'.$ml->product_id.'/'.$ml->seo : '#')?>">
                        <?=$ml->title.($ml->model ? ' '.$ml->model : '')?>
                    </a>
                </div>
            </div>
            <div class="product-card__rating" style="direction: rtl">
                <?php
                    $MyScore = round($ml->score / 2 , 1);
                ?>
                <div class="product-card__rating-label">
                    <div class="star on"></div>
                    <?=$MyScore?>
                    از
                    <?=$ml->score_person?>
                    نظر
                </div>
            </div>
            <div class="c-product-box__digiplus">
                <?php if(isset($AdvArr[$ml->product_id]['gift'][3])){ ?>
                    <span class="c-product-box__digiplus-data c-digiplus-sign--before">
                        <img class="ngift" src="<?=$conf->BaseRoot2?>images/amazing.png" alt="gift card" title="کارت هدیه نقدی" width="24" style="width:24px !important">
                        <?=$ths->money($AdvArr[$ml->product_id]['gift'][3])?>
                        <?=$MyGiftType2[3]?>
                    </span>
                <?php } ?>
            </div>

        </div>
        
       <div class="product-card__footer">
            <?php
                if(!$AppOnly){
                    if($ml->confirm_count && $ml->confirm_price && $PriceArr[$ml->product_id] && isset($PriceArr[$ml->product_id]['price_off']) && $PriceArr[$ml->product_id]['price_off'] > 1000){
                        if($PriceArr[$ml->product_id]['price_off'] != $PriceArr[$ml->product_id]['price']){
            ?>
                            <div class="product-card__prices">
                                <div class="product-card__price product-card__price--new">
                                    <?=$ths->money($PriceArr[$ml->product_id]['price_off'] / 10)?>
                                    <span class="MoneyUnit">
                                                             تومان
                                    </span>
                                </div>
                                <?php
                                      if($PriceArr[$ml->product_id]['price'] > $PriceArr[$ml->product_id]['price_off']){
                                              ?>
                                <div class="product-card__price product-card__price--old">
                                    <?php
                                        $d = round(100 - (($PriceArr[$ml->product_id]['price_off'] * 100) / $PriceArr[$ml->product_id]['price']) , 0);
                                        if($d != $Cnfg['PriceOffPercent']){
                                    ?>
                                        <span class="badge-danger d-inline-block ml-2 p-1 rounded mt-1">
                                            <?=$d?>%
                                        </span>
                                        <?=$ths->money($PriceArr[$ml->product_id]['price'] / 10)?>
                                        <span class="MoneyUnit">
                                                                 تومان
                                        </span>
                                    <?php }?>
                                </div>
                                <?php } ?>
                            </div>
            <?php
                        }else{
            ?>
                            <div class="product-card__prices">
                                <div class="product-card__price product-card__price--current">
                                    <?=$ths->money($PriceArr[$ml->product_id]['price_off'] / 10)?>
                                    <span class="MoneyUnit">
                                                             تومان
                                    </span>
                                </div>
                            </div>
            <?php
                        }
                    }
            ?>

            <?php if($ml->confirm_count && $ml->confirm_price && $PriceArr[$ml->product_id]['price_off']){?>

            <button class="product-card__addtocart-icon js-cd-add-to-cart prid_<?=$ml->product_id?>" type="button" aria-label="افزودن به سبد خرید">
                <svg width="20" height="20">
                    <circle cx="7" cy="17" r="2"/>
                    <circle cx="15" cy="17" r="2"/>
                    <path
                        d="M20,4.4V5l-1.8,6.3c-0.1,0.4-0.5,0.7-1,0.7H6.7c-0.4,0-0.8-0.3-1-0.7L3.3,3.9C3.1,3.3,2.6,3,2.1,3H0.4C0.2,3,0,2.8,0,2.6
	V1.4C0,1.2,0.2,1,0.4,1h2.5c1,0,1.8,0.6,2.1,1.6L5.1,3l2.3,6.8c0,0.1,0.2,0.2,0.3,0.2h8.6c0.1,0,0.3-0.1,0.3-0.2l1.3-4.4
	C17.9,5.2,17.7,5,17.5,5H9.4C9.2,5,9,4.8,9,4.6V3.4C9,3.2,9.2,3,9.4,3h9.2C19.4,3,20,3.6,20,4.4z"/>
                </svg>
            </button>

            <?php }else{ ?>
                <div class="text-center w-100 listcat">
                    ناموجود
                </div>
            <?php }?>
                <?php }else{?>
                    <div class=" badge-danger p-2 rounded m-auto small">
                        <a href="" class="text-white">
                        <img src="<?=$conf->BaseRoot2?>images/for-app.png">
                        ویژه اپلیکیشن
                        </a>
                    </div>
                <?php }?>

        </div>

    </div>
</div>
<?php
        }
    }
?>
</div>
</div>



<div class="products-view__pagination">
<?php
    if(isset($MyList) && $MyList){
        $PageCount = ceil(count($AllResult)/$PerPage);
        if($PageCount>1){
?>
    <nav aria-label="Page navigation example">
        <ul class="pagination">
            <li class="page-item">
                <a class="page-link page-link--with-arrow" href="#" aria-label="Previous" onclick="GoToPage21('1')">
                    <span class="page-link__arrow page-link__arrow--left" aria-hidden="true">
                        <svg width="7" height="11">
                            <path d="M6.7,0.3L6.7,0.3c-0.4-0.4-0.9-0.4-1.3,0L0,5.5l5.4,5.2c0.4,0.4,0.9,0.3,1.3,0l0,0c0.4-0.4,0.4-1,0-1.3l-4-3.9l4-3.9C7.1,1.2,7.1,0.6,6.7,0.3z"/>
                        </svg>
                    </span>
                </a>
            </li>

            <?php
                $Start = ( $Page - 4 > 0 ) ? $Page - 4 : 1;
                if($Start > 1){
                    echo '<li class="page-item page-item--dots"><div class="pagination__dots"></div></li> &nbsp;';
                }
                $End = ( $Page + 4 < $PageCount) ? $Page + 4 : $PageCount;

                for($j=$Start; $j<=$PageCount; $j++){
                    if($j > $End){
                        echo '<li class="page-item page-item--dots"><div class="pagination__dots"></div></li>';
                        break;
                    }

                    if($j == $Page){
                        echo '<li class="page-item active" aria-current="page"><span class="page-link">'.$j.' <span class="sr-only">(current)</span></span></li>';
                    }else{
                        echo '<li class="page-item"><a class="page-link" href="#" onclick="GoToPage21('.$j.')">'.$j.'</a></li>';
                    }
                }
            ?>

            <li class="page-item">
                <a class="page-link page-link--with-arrow" href="#" aria-label="Next" onclick="GoToPage21('<?=$PageCount?>')">
                    <span class="page-link__arrow page-link__arrow--right" aria-hidden="true">
                        <svg width="7" height="11">
                            <path d="M0.3,10.7L0.3,10.7c0.4,0.4,0.9,0.4,1.3,0L7,5.5L1.6,0.3C1.2-0.1,0.7,0,0.3,0.3l0,0c-0.4,0.4-0.4,1,0,1.3l4,3.9l-4,3.9
	C-0.1,9.8-0.1,10.4,0.3,10.7z"/>
                        </svg>
                    </span>
                </a>
            </li>

        </ul>
    </nav>
<?php
        }
    }else{
        echo '<div class="text-center m-5 w-100">موردی برای نمایش وجود ندارد</div>';
    }
?>
    <?php if(isset($MyList) && $MyList){?>
    <div class="products-view__pagination-legend">
        نمایش
        <?=(isset($MyList) ? count($MyList) : 0)?>
        از
        <?=count($AllResult)?>
        محصول
    </div>
    <?php }?>
</div>
<?php 
    $IsCatBrand = false;
    if($_REQUEST['BrandID']){
        $CatBrnd = [];
        $CatBrnd[] = ['cat_id', $_REQUEST['CatID']];
        $CatBrnd[] = ['brand_id', $_REQUEST['BrandID']];
        $CatBrnd[] = ['display', 1];
        $CatBrnd[] = ['deleted', 0];

        $CatBrandRow_ = $CatBrandClass->get_all($CatBrnd);
        if($CatBrandRow_){
            $IsCatBrand = true;
            $CatBrandRow = $CatBrandRow_[0];
        }
    }
    if(!$IsCatBrand){
        $catinfo = $CatClass->get_by_id($_REQUEST['CatID'], ['title', 'title1', 'title2', 'comment', 'descriptions', 'meta_description']);
    }


    if( ($IsCatBrand && isset($CatBrandRow) && isset($CatBrandRow->descriptions) && $CatBrandRow->descriptions != "") || (!$IsCatBrand && isset($catinfo) && isset($catinfo->descriptions) && $catinfo->descriptions != "") ){
?>

<?php


    if(($IsCatBrand && isset($CatBrandRow) &&  trim($CatBrandRow->descriptions)) || (!($IsCatBrand && isset($CatBrandRow) &&  trim($CatBrandRow->descriptions)) && (isset($catinfo) &&  trim($catinfo->descriptions)))){
?>
<div class="products-view__pagination">

				<div class="text-container">
					<div id="profile-description">
						<div class="text dynamic-wrap">
						    
						    <div class="elementor-text-editor elementor-clearfix show-mobile">
                                <p style="text-align: right;">
                                    <?= (($IsCatBrand && isset($CatBrandRow)) ? $CatBrandRow->comment : (isset($catinfo) ? $catinfo->comment : '')) ?>
                                </p>
                            </div>
						    
							<p>
                                <?= (($IsCatBrand && isset($CatBrandRow)) ? $CatBrandRow->descriptions : (isset($catinfo) ? $catinfo->descriptions : '')) ?>
							</p>
						</div>
						<div class="btn-wrapper">
							<span class="grey-line"></span>
							<div class="show-more">(نمایش بیشتر)</div>
</a>
							<span class="grey-line"></span>
						</div>
					</div>
				</div>

		</div>
<?php } ?>
<?php } ?>
      <script>
          
              $(".show-more").click(function () {
        if($(".text").hasClass("show-more-height")) {
            $(this).text("(نمایش کمتر)");
        } else {
            $(this).text("(نمایش بیشتر)");
        }

        $(".text").toggleClass("show-more-height");
    });
      </script>

</div>
</div>


<?php
    if($BrandStr){
?>
    <script>
        $('.MyBrand').html('<?=$BrandStr?>');
    </script>
<?php
    }else{
?>
    <script>
        $('.MyBrand0').hide();
    </script>
<?php
    }
?>

<script>
    $('.filter-price').attr('data-min', '1000').attr('data-max', '2000');
</script>
<script id="rendered-js" >
// Sticky Sidebar
var mql = window.matchMedia('screen and (min-width: 60em)');if (mql.matches) {
  !function (i) {i.fn.theiaStickySidebar = function (t) {function o(t, o) {var a = e(t, o);a || (console.log("TST: Body width smaller than options.minWidth. Init is delayed."), i(document).scroll(function (t, o) {return function (a) {var n = e(t, o);n && i(this).unbind(a);};}(t, o)), i(window).resize(function (t, o) {return function (a) {var n = e(t, o);n && i(this).unbind(a);};}(t, o)));}function e(t, o) {return t.initialized === !0 ? !0 : i("body").width() < t.minWidth ? !1 : (a(t, o), !0);}function a(t, o) {t.initialized = !0, i("head").append(i('<style>.theiaStickySidebar:after {content: ""; display: table; clear: both;}</style>')), o.each(function () {function o() {a.fixedScrollTop = 0, a.sidebar.css({ "min-height": "1px" }), a.stickySidebar.css({ position: "static", width: "" });}function e(t) {var o = t.height();return t.children().each(function () {o = Math.max(o, i(this).height());}), o;}var a = {};a.sidebar = i(this), a.options = t || {}, a.container = i(a.options.containerSelector), 0 == a.container.size() && (a.container = a.sidebar.parent()), a.sidebar.css({ position: "relative", overflow: "visible", "-webkit-box-sizing": "border-box", "-moz-box-sizing": "border-box", "box-sizing": "border-box" }), a.stickySidebar = a.sidebar.find(".theiaStickySidebar"), 0 == a.stickySidebar.length && (a.sidebar.find("script").remove(), a.stickySidebar = i("<div>").addClass("theiaStickySidebar").append(a.sidebar.children()), a.sidebar.append(a.stickySidebar)), a.marginTop = parseInt(a.sidebar.css("margin-top")), a.marginBottom = parseInt(a.sidebar.css("margin-bottom")), a.paddingTop = parseInt(a.sidebar.css("padding-top")), a.paddingBottom = parseInt(a.sidebar.css("padding-bottom"));var n = a.stickySidebar.offset().top,d = a.stickySidebar.outerHeight();a.stickySidebar.css("padding-top", 1), a.stickySidebar.css("padding-bottom", 1), n -= a.stickySidebar.offset().top, d = a.stickySidebar.outerHeight() - d - n, 0 == n ? (a.stickySidebar.css("padding-top", 0), a.stickySidebarPaddingTop = 0) : a.stickySidebarPaddingTop = 1, 0 == d ? (a.stickySidebar.css("padding-bottom", 0), a.stickySidebarPaddingBottom = 0) : a.stickySidebarPaddingBottom = 1, a.previousScrollTop = null, a.fixedScrollTop = 0, o(), a.onScroll = function (a) {if (a.stickySidebar.is(":visible")) {if (i("body").width() < a.options.minWidth) return void o();if (a.sidebar.outerWidth(!0) + 50 > a.container.width()) return void o();var n = i(document).scrollTop(),d = "static";if (n >= a.container.offset().top + (a.paddingTop + a.marginTop - a.options.additionalMarginTop)) {var r,s = a.paddingTop + a.marginTop + t.additionalMarginTop,c = a.paddingBottom + a.marginBottom + t.additionalMarginBottom,p = a.container.offset().top,b = a.container.offset().top + e(a.container),g = 0 + t.additionalMarginTop,l = a.stickySidebar.outerHeight() + s + c < i(window).height();r = l ? g + a.stickySidebar.outerHeight() : i(window).height() - a.marginBottom - a.paddingBottom - t.additionalMarginBottom;var h = p - n + a.paddingTop + a.marginTop,f = b - n - a.paddingBottom - a.marginBottom,S = a.stickySidebar.offset().top - n,u = a.previousScrollTop - n;"fixed" == a.stickySidebar.css("position") && "modern" == a.options.sidebarBehavior && (S += u), "legacy" == a.options.sidebarBehavior && (S = r - a.stickySidebar.outerHeight(), S = Math.max(S, r - a.stickySidebar.outerHeight())), S = u > 0 ? Math.min(S, g) : Math.max(S, r - a.stickySidebar.outerHeight()), S = Math.max(S, h), S = Math.min(S, f - a.stickySidebar.outerHeight());var m = a.container.height() == a.stickySidebar.outerHeight();d = (m || S != g) && (m || S != r - a.stickySidebar.outerHeight()) ? n + S - a.sidebar.offset().top - a.paddingTop <= t.additionalMarginTop ? "static" : "absolute" : "fixed";}if ("fixed" == d) a.stickySidebar.css({ position: "fixed", width: a.sidebar.width(), top: S, left: a.sidebar.offset().left + parseInt(a.sidebar.css("padding-left")) });else if ("absolute" == d) {var y = {};"absolute" != a.stickySidebar.css("position") && (y.position = "absolute", y.top = n + S - a.sidebar.offset().top - a.stickySidebarPaddingTop - a.stickySidebarPaddingBottom), y.width = a.sidebar.width(), y.left = "", a.stickySidebar.css(y);} else "static" == d && o();"static" != d && 1 == a.options.updateSidebarHeight && a.sidebar.css({ "min-height": a.stickySidebar.outerHeight() + a.stickySidebar.offset().top - a.sidebar.offset().top + a.paddingBottom }), a.previousScrollTop = n;}}, a.onScroll(a), i(document).scroll(function (i) {return function () {i.onScroll(i);};}(a)), i(window).resize(function (i) {return function () {i.stickySidebar.css({ position: "static" }), i.onScroll(i);};}(a));});}var n = { containerSelector: "", additionalMarginTop: 0, additionalMarginBottom: 0, updateSidebarHeight: !0, minWidth: 0, sidebarBehavior: "modern" };t = i.extend(n, t), t.additionalMarginTop = parseInt(t.additionalMarginTop) || 0, t.additionalMarginBottom = parseInt(t.additionalMarginBottom) || 0, o(t, this);};}(jQuery);
  $(document).ready(function () {$("#side").theiaStickySidebar({ additionalMarginTop: 20, additionalMarginBottom: 20 });});};
//# sourceURL=pen.js
    </script>