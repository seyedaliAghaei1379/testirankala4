<?php @session_start();
/**
 * Created by PhpStorm.
 * User: yousefi
 * Date: 8/15/20
 * Time: 8:53 AM
 */
    include_once(__DIR__."/../../classes/class.cnfg.php");
    $conf = new config();

    include_once($conf->BaseRoot.'/classes/class.main.php');
    $ths = new main();

    $ths->ExternalLinkCheck();

    include_once($conf->BaseRoot.'classes/class.libs_parent.php');

    include_once($conf->BaseRoot.'/classes/libs/class.product_rel.php');
    $ProdRelClass = new product_rel();

    include_once($conf->BaseRoot.'/classes/libs/class.product.php');
    $ProductClass = new product();

    include_once($conf->BaseRoot.'/classes/libs/class.adv.php');
    $AdvClass = new adv();
    
    include_once($conf->BaseRoot.'/classes/libs/class.files.php');
    $FilesClass = new files();

    include_once($conf->BaseRoot.'/classes/libs/class.files.php');
    $FilesClass = new files();
    #$starttime = microtime(true);

 $Cnfg = $ths->getSetting();


    $MyGiftType = ['Discount'=>'تخفیف (درصد)', 'Credit'=> 'اعتبار هدیه (تومان)','GiftCard'=> 'کارت هدیه (تومان)', 'Score'=>'امتیاز هدیه', 'GiftPlus'=>'هدیه غیر نقدی'];
    $MyGiftType2 = [1=>'تخفیف درصد', 2=>'تومان اعتبار هدیه ', 3=> 'تومان کارت هدیه ', 4=>'امتیاز هدیه', 5=>'به عنوان هدیه '];


    $Cond = [];
    $Cond[] = ['product`.`display', 1];
    $Cond[] = ['product`.`deleted', 0];
    $Cond[] = ['product_similar`.`display', 1];
    $Cond[] = ['product_similar`.`deleted', 0];
    $Cond[] = ['lang_id', /*$_SESSION['_Lang_']*/ 1];
    $Cond[] = ['product_similar`.`rel_id`=`product`.`product_id` and `product`.`product_id', 0, '!='];
    $Cond[] = ['product_similar`.`my_product_id', $_REQUEST['MyID']];
    $Cond[] = ['product_similar`.`type_id', 1];

    $MyList = $ProductClass->get_all($Cond, ['code', 'title', 'score', 'model', 'score_person', 'img_id', 'seo', 'confirm_count', 'confirm_price', 'rel_id'], ['`product_similar`.`ordr`', 'desc'], [0, 4], '`product`.`product_id`', 'product_similar');

    $Flag2 = false;

    if(!$MyList){
        $MyCat = $ProductClass->get_by_id($_REQUEST['MyID'], ['cat_id']);

        $Cond2[] = ['product`.`display', 1];
        $Cond2[] = ['product`.`deleted', 0];
        $Cond2[] = ['product`.`confirm_count', 1];
        $Cond2[] = ['product`.`cat_id', $MyCat->cat_id];
        $Cond2[] = ['product`.`product_id', $_REQUEST['MyID'], '!='];

        $MyList = $ProductClass->get_all($Cond2, ['code', 'title', 'model', 'score', 'score_person', 'img_id', 'seo', 'confirm_count', 'confirm_price', 'product_id'], ['`product`.`ordr`', 'desc'], [0, 4]);

        $Flag2 = true;
    }


    if($MyList){
?>
        <div class="container block block-products-carousel" data-layout="grid-9">
            <div class="cosoff">
                <div class="section-header">
                    <div class="section-header__body"><h2 class="section-header__title underline">محصولات پیشنهادی</h2>

                        <div class="section-header__spring"></div>

                        <div class="section-header__arrows">
                            <div class="arrow section-header__arrow section-header__arrow--prev arrow--prev">
                                <button class="arrow__button" type="button" aria-label="Left Align">
                                    <svg width="7" height="11">
                                        <path
                                            d="M6.7,0.3L6.7,0.3c-0.4-0.4-0.9-0.4-1.3,0L0,5.5l5.4,5.2c0.4,0.4,0.9,0.3,1.3,0l0,0c0.4-0.4,0.4-1,0-1.3l-4-3.9l4-3.9C7.1,1.2,7.1,0.6,6.7,0.3z"/>
                                    </svg>
                                </button>
                            </div>
                            <div class="arrow section-header__arrow section-header__arrow--next arrow--next">
                                <button class="arrow__button" type="button" aria-label="Right Align">
                                    <svg width="7" height="11">
                                        <path d="M0.3,10.7L0.3,10.7c0.4,0.4,0.9,0.4,1.3,0L7,5.5L1.6,0.3C1.2-0.1,0.7,0,0.3,0.3l0,0c-0.4,0.4-0.4,1,0,1.3l4,3.9l-4,3.9
	C-0.1,9.8-0.1,10.4,0.3,10.7z"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                        
                    </div>
                </div>
                <div class="block-products-carousel__carousel">
                    <div class="block-products-carousel__carousel-loader"></div>
                    <div class="owl-carousel">

                        <?php
                            if($MyList){
                                $CodeArr = [];
                                $PriceArr = [];
                                $AdvArr = [];
                                foreach($MyList as $mll){
                                    $CodeArr[] = $mll->product_id;
                                }

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

                                $Cnd = [];
                                $Cnd[] = ['confirm', 1];
                                $Cnd[] = ['product_id', $CodeArr, 'in'];
                                $prd = $ProdRelClass->get_all_price($Cnd, ['product_id', 'asc', 'prop', 'desc']);

                                if($prd){
                                    foreach($prd as $pprd){
                                        $PriceArr[$pprd->product_id]['price'] = $pprd->price;
                                        if(isset($AdvArr[$pprd->product_id][1])){
                                            $PriceArr[$pprd->product_id]['price_off'] = ($pprd->price_off * ((100 - $AdvArr[$pprd->product_id][1]) / 100));
                                        }else{
                                            $PriceArr[$pprd->product_id]['price_off'] = $pprd->price_off;
                                        }
                                    }
                                }


                            }

                                foreach($MyList as $itmi=>$ml){

                                    $AppOnly = (isset($AdvArr[$ml->product_id]['type']) && $AdvArr[$ml->product_id]['type'] == 11);
                                    $IsSurprise = (isset($AdvArr[$ml->product_id]['type']) && $AdvArr[$ml->product_id]['type'] == 6);
                                    ?>
            <div class="block-products-carousel__column">
                <div class="block-products-carousel__cell">
                    <div class="product-card product-card--layout--grid">
                        <div class="product-card__actions-list <?=($IsSurprise ? '' : ' tp0')?>">
                            <button class="product-card__action product-card__action--wishlist" type="button"
                                    aria-label="Add to لیست علاقه مندیها">
                                <svg width="16" height="16">
                                    <path d="M13.9,8.4l-5.4,5.4c-0.3,0.3-0.7,0.3-1,0L2.1,8.4c-1.5-1.5-1.5-3.8,0-5.3C2.8,2.4,3.8,2,4.8,2s1.9,0.4,2.6,1.1L8,3.7
	l0.6-0.6C9.3,2.4,10.3,2,11.3,2c1,0,1.9,0.4,2.6,1.1C15.4,4.6,15.4,6.9,13.9,8.4z"/>
                                </svg>
                            </button>

                            <?php
                                $IsInCompare = (isset($_SESSION['CompareIDs']) && in_array($ml->product_id, $_SESSION['CompareIDs']));
                            ?>

                            <a onclick="AddToCompare('<?=$ml->product_id?>')" class="product-card__action product-card__action--compare <?=($IsInCompare ? 'sbg' :'')?>" type="button" aria-label="Add to compare">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" class=""><path d="M9 15H7c-.6 0-1-.4-1-1V2c0-.6.4-1 1-1h2c.6 0 1 .4 1 1v12c0 .6-.4 1-1 1zM1 9h2c.6 0 1 .4 1 1v4c0 .6-.4 1-1 1H1c-.6 0-1-.4-1-1v-4c0-.6.4-1 1-1zM15 5h-2c-.6 0-1 .4-1 1v8c0 .6.4 1 1 1h2c.6 0 1-.4 1-1V6c0-.6-.4-1-1-1z"></path></svg>
                            </a>
                        </div>
      <div class="product-card__image">
          
           <?php
                                                    if($IsSurprise){
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
                                                                <div>:</div>
                                                                <div class="timer__part1">
                                                                    <div class="timer__part-value1 timer__part-value--minutes timer_minutes<?=($itmi + 1)?>">
                                                                        <?=sprintf('%02d', $MyDiff[2])?>
                                                                    </div>
                                                                    <!--<div class="timer__part-label1">دقیقه</div>-->
                                                                </div>
                                                                <div>:</div>
                                                                <div class="timer__part1">
                                                                    <div class="timer__part-value1 timer__part-value--hours  timer_hours<?=($itmi + 1)?>">
                                                                        <?=sprintf('%02d', $MyDiff[1])?>
                                                                    </div>
                                                                    <!--<div class="timer__part-label1">ساعت</div>-->
                                                                </div>
                                                                <div>:</div>
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
                                                    </div>
                                               
                                
                                </div></div>
           <?php }?>
          
          
                                                <a href="<?=(!$AppOnly ? $conf->BaseRoot2.'product/'.$ml->{$Flag2 ? 'product_id' : 'rel_id'}.'/'.$ml->seo : '#')?>" title="<?=$ml->title?>">
                                                    <?php
                                                        if($ml->img_id){
                                                            $MyImg = $FilesClass->get_by_id($ml->img_id, 1);
                                                            $MyImgUrl = dirname($MyImg['path2']).'/'.$MyImg['fileid'].'_thumb_'.$MyImg['filename'];
                                                        }else{
                                                            $MyImgUrl = $conf->BaseRoot2.'MyFile/Product/none.jpg';
                                                        }
                                                    ?>

                                                    <img src="<?=$MyImgUrl?>" width="300" height="300" alt="<?=$ml->title?>">
                                                </a>


                                              
                                                
                                                
                                            </div>
                                            
                                            
                                            
                                            
                       <div class="product-card__info">
                                                <div class="product-card__name">
                                                    <div>
                                                        <a href="<?=(!$AppOnly ? $conf->BaseRoot2.'product/'.$ml->product_id.'/'.$ml->seo : '#')?>" title="<?=$ml->title.($ml->model ? '-'.$ml->model : '')?>" style="line-height: 1.7rem;">
                                                <?=$ml->title.($ml->model ? '<span class="product-card__meta" style="padding:0;">مدل: '.$ml->model.'</span>' : '')?>
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
                                                    if($ml->confirm_price && $ml->confirm_count && isset($PriceArr[$ml->product_id]['price_off']) && $PriceArr[$ml->product_id]['price_off']){
                                                        /*$MyPrice = $ProdRelClass->get_all_price([['product_id', $ml->{$Flag2 ? 'product_id' : 'rel_id'}], ['confirm', 1]], ['price_off', 'asc'], [0,1]);
                                                        if($MyPrice){*/

                                                        if($PriceArr[$ml->product_id]['price_off'] != $PriceArr[$ml->product_id]['price']){

                                                ?>
                                                       
                                                       
                                                       
                                                       
                                                       
                                                        <div class="product-card__prices">
                                                            <div class="product-card__price product-card__price--new">
                                                                <?=$ths->money($PriceArr[$ml->product_id]['price_off'] / 10)?>
                                                                <span class="MoneyUnit">
                                                             تومان
                                                                </span>
                                                            </div>
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

                                                    <?php if($ml->confirm_count && $ml->confirm_price && isset($PriceArr[$ml->product_id]['price_off']) && $PriceArr[$ml->product_id]['price_off']){?>

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
            </div>
<?php
        }
?>
                    </div>
                </div>
            </div>
        </div>
<?php
    }

    #$endtime = microtime(true);
    #printf("Page loaded in %f seconds", $endtime - $starttime );
?>



