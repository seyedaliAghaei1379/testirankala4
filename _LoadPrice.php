<?php
session_set_cookie_params(32600, '/', $_SERVER['HTTP_HOST'], true, true);
@session_start();
/**
 * Created by PhpStorm.
 * User: yousefi
 * Date: 8/25/20
 * Time: 10:00 AM
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

    include_once($conf->BaseRoot.'/classes/libs/class.adv.php');
    $AdvClass = new adv();


    $MyGiftType = ['Discount'=>'تخفیف (درصد)', 'Credit'=> 'اعتبار هدیه (تومان)','GiftCard'=> 'کارت هدیه (تومان)', 'Score'=>'امتیاز هدیه', 'GiftPlus'=>'هدیه غیر نقدی'];
    $MyGiftType2 = [1=>'تخفیف درصد', 2=>'تومان اعتبار هدیه ', 3=> 'تومان کارت هدیه ', 4=>'امتیاز هدیه', 5=>'به عنوان هدیه '];

    if(!isset($_REQUEST['ProdID'])){
        exit;
    }
    
    $_REQUEST['ProdID'] = (int)$ths->MakeSecurParam($_REQUEST['ProdID']);
    $_REQUEST['MyProp'] = $ths->MakeSecurParam($_REQUEST['MyProp']);
    $_ = explode('{', ' '.$_REQUEST['MyProp']);
    if(isset($_[1])){
        $__ = explode('}', $_[1]);
        $_REQUEST['MyProp'] = '{'.$__[0].'}';
    }else{
        $_REQUEST['MyProp'] = '{}';
    }
    

    $AdvArr = [];
    $Cnd2 = [];
    $Cnd2[] = [$AdvClass->MyTable.'`.`lang', /*$_SESSION['_Lang_']*/ 1];
    $Cnd2[] = [$AdvClass->MyTable.'`.`display', 1];
    $Cnd2[] = [$AdvClass->MyTable.'`.`deleted', 0];
    $Cnd2[] = [$AdvClass->MyTable.'`.`start_date', date('Y-m-d H:i:s'), '<='];
    $Cnd2[] = [$AdvClass->MyTable.'`.`end_date', date('Y-m-d H:i:s'), '>='];
    $Cnd2[] = [$AdvClass->MyTable.'`.`link', $_REQUEST['ProdID']];

    $AdvArr_ = $AdvClass->get_all($Cnd2, ['gift', 'type', 'end_date']);
    if($AdvArr_){
        foreach($AdvArr_ as $a){
            $AdvArr = ['gift'=>$ths->MyDecode($a->gift), 'type'=>$a->type, 'end_date'=>$a->end_date];
        }
    }

    $_REQUEST['MyCompany'] = explode(',',$_REQUEST['MyCompany']);
    $Cond = [];
    $Cond[] = ['product_id', $_REQUEST['ProdID']];
    $Cond[] = ['prop', $_REQUEST['MyProp']];
    $Cond[] = ['confirm', 1];
    $Cond[] = ['active', 1];
    $Cond[] = ['company_id', $_REQUEST['MyCompany'], 'in'];

    $MyInfo = $ProdRelClass->get_all_price($Cond);
    $minprice = [];
    if($MyInfo){
        foreach($MyInfo as $my){
            $minprice[] = $my->price_off;
        }
    }
     foreach($MyInfo as $m){
            if($m->price_off == min($minprice)){
                $MyInfo = $m;
            }
        }
        $MyInfo = array($MyInfo);
   
    if($MyInfo){
        $IsAvailable = false;
        $cnt = $ths->MyDecode($MyInfo[0]->count_info);
        
        if($cnt && isset($cnt['avail_count']) && $cnt['avail_count']>0){
            $IsAvailable = true;
        }

        if(isset($AdvArr['gift'][1]) && $AdvArr['gift'][1]){
            if($AdvArr['type'] == 7){
                //$PriceArr['price_off'] = ($MyInfo[0]->price_off * ((100 - $AdvArr['gift'][1]) / 100));
                $PriceArr['price_off'] = $MyInfo[0]->price_off;
                $PriceArr['price'] = ($MyInfo[0]->price_off * ((100 + $AdvArr['gift'][1]) / 100));
            }
            if($AdvArr['type'] == 10 ){
                $PriceArr['price_off'] = ($MyInfo[0]->price_off * ((100 - $AdvArr['gift'][1]) / 100));
                $PriceArr['price'] = $MyInfo[0]->price;
            }
        }else{
            $PriceArr['price_off'] = $MyInfo[0]->price_off;
            $PriceArr['price'] = $MyInfo[0]->price;
        }


        //if exist related product
        $this_prod = $ProductClass->get_by_id($_REQUEST['ProdID'], ['rel_id', 'confirm_count', 'confirm_price']);
        
        if($this_prod->rel_id){
            $rl = explode('-', $this_prod->rel_id);
            if($rl && count($rl)){
                foreach($rl as $rll){
                    $CondRelID = [];
                    $CondRelID[] = ['product_id', $rll];
                    $CondRelID[] = ['confirm', 1];
                    $CondRelID[] = ['company_id', $_REQUEST['MyCompany']];
                    $CondRelID[] = ['prop', $_REQUEST['MyProp']];
                    
                    $MyPrice2 = $ProdRelClass->get_all_price($CondRelID);
                    
                    if($MyPrice2){
                        $cnt = $ths->MyDecode($MyPrice2[0]->count_info);
                        if($cnt){
                            if($_REQUEST['MyCompany'] == 1){
                                if(isset($cnt['avail_count']) && (int)$cnt['avail_count']>0){
                                    $PriceArr['price_off'] += $MyPrice2[0]->price_off;
                                    $MyInfo[0]->price += $MyPrice2[0]->price;
                                }else{
                                    $IsAvailable = 0;
                                }
                            }else{
                                if(isset($cnt['avail_count']) && (int)$cnt['avail_count']>0){
                                    $PriceArr['price_off'] = $MyPrice2[0]->price_off;
                                    $MyInfo[0]->price -= $MyPrice2[0]->price;
                                }else{
                                    $IsAvailable = 0;
                                }
                            }
                        }
                    }
                }
            }
        }

        

        if($PriceArr['price_off'] < 1000 || !$this_prod->confirm_count || !$this_prod->confirm_price){
            $IsAvailable = false;
        }
        //var_dump($this_prod->confirm_price);
        echo json_encode([  'price_off'=>($IsAvailable ? $ths->money($PriceArr['price_off'] / 10) : ''),
                            'price'=>($IsAvailable ? $ths->money($PriceArr['price'] / 10) : ''),
                            'is_available'=>(int)$IsAvailable,
                            'code'=>$MyInfo[0]->code,
                            'code2'=>$MyInfo[0]->code2]);
    }else{
        echo json_encode([  'price_off'=>'', 'price'=>'', 'is_available'=>0, 'code'=>'- - -']);
    }
