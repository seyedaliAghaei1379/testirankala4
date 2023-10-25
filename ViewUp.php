<?php @session_start();
/**
 * Created by PhpStorm.
 * User: yousefi
 * Date: 8/18/20
 * Time: 10:00 AM
 */
    global $ths, $conf;

    include_once($conf->BaseRoot.'/classes/libs/class.product.php');
    $ProductClass = new product();

    include_once($conf->BaseRoot.'/classes/libs/class.visit_person.php');
    $VisitPrClass = new visit_person();

    include_once($conf->BaseRoot.'/classes/libs/class.files.php');
    $FilesClass = new files();

    include_once($conf->BaseRoot.'/classes/libs/class.product_rel.php');
    $ProdRelClass = new product_rel();

    include_once($conf->BaseRoot.'/classes/libs/class.properties.php');
    $PropClass = new properties();

    include_once($conf->BaseRoot.'/classes/libs/class.brand.php');
    $BrandClass = new brand();

    include_once($conf->BaseRoot.'/classes/libs/class.adv.php');
    $AdvClass = new adv();

    include_once($conf->BaseRoot.'/classes/libs/class.product_company.php');
    $ProdCompClass = new product_company();


    if(!isset($_REQUEST['MyID']) || (int)$ths->MakeSecurParam($_REQUEST['MyID'])<=0){
        $ths->MyRedirect($conf->BaseRoot2, false);
        exit;
    }

    $_REQUEST['MyID'] = (int)$ths->MakeSecurParam($_REQUEST['MyID']);
    
    $n = $ProductClass->get_by_id($_REQUEST['MyID']);

    if(!$n){
        $ths->MyRedirect($conf->BaseRoot2, false);
        exit;
    }

    if($n->deleted){
        $ths->MyRedirect($conf->BaseRoot2, false);
        exit;
    }

    if(!$n->display){
        $ths->MyRedirect($conf->BaseRoot2, false);
        exit;
    }

    
    $CompFlag = $ProdCompClass->get_all([['product_id', $_REQUEST['MyID']], ['deleted', 0]]);
    if(!$CompFlag){
        $ths->MyRedirect($conf->BaseRoot2, false);
        exit;
    }

    $uri = explode('?', $_SERVER['REQUEST_URI']);

    if($uri[0] !=  '/product/'.$_REQUEST['MyID'].'/'.urlencode($ths->UrlFriendly(trim($n->title.($n->model ? '-'.$n->model : ''))))){
        //$ths->MyRedirect($conf->BaseRoot2.'product/'.$_REQUEST['MyID'].'/'.urlencode($ths->UrlFriendly(trim($n->title.($n->model ? '-'.$n->model : '')))), false);
    }else{
        $ProductClass->update($_REQUEST['MyID'], ['visit'=>($n->visit + 1)]);

        if(isset($_SESSION['_LoginUserID_'])){
            $VisitCond[] = ['user_id', $_SESSION['_LoginUserID_']];
            $VisitCond[] = ['product_id', $_REQUEST['MyID']];
            $vFlag = $VisitPrClass->get_all($VisitCond);
            if($vFlag){
                $VisitPrClass->update($vFlag[0]->id, ['count'=>($vFlag[0]->count + 1), 'last_visit'=>date('Y-m-d H:i:s')]);
            }else{
                $VisitPrClass->add(['user_id'=>$_SESSION['_LoginUserID_'], 'cat_id'=>$n->cat_id, 'product_id'=>$_REQUEST['MyID'], 'count'=>1, 'last_visit'=>date('Y-m-d H:i:s')]);
            }
        }
    }


    $CacheName = 'Product/'.$_REQUEST['MyID'];
    $CacheFile = $ths->is_cache($CacheName);
    if(true || !$CacheFile){

        //Load Price
        $MyProp = $PropClass->get_by_product($n->product_id);

        $PropStrArr = [];
        if($MyProp && $MyProp['ForPrice']){
    
            foreach($MyProp['ForPrice'] as $pri=>$pr){
                $v3 = $ths->MyDecode($pr['prop_val']);
                foreach($v3 as $v4i=>$v4){
                    $_p = '';
                    $_p .= $pr['prop_id'].':'.$v4.';';
                    $PropStrArr[] = '{'.trim($_p).'}';
                }
    
                unset($MyProp['ForPrice'][$pri]);
                break;
            }
            if(count($MyProp['ForPrice'])){
                $PropStrArr_ = $PropStrArr;
                $PropStrArr = [];
                $MySecondPrice = reset($MyProp['ForPrice']);
                $v5 = $ths->MyDecode($MySecondPrice['prop_val']);
                foreach($v5 as $v6i=>$v6){
                    foreach($PropStrArr_ as $psa0=>$psa1){
                        $_pp = '';
                        $_pp .= $MySecondPrice['prop_id'].':'.$v6.';';
                        $PropStrArr[] = str_replace('}', '', $psa1).trim($_pp).'}';
                    }
                }
            }
        }


        $MyProductPrice = 0;
        $MyPropStr = '';
    
        if(count($PropStrArr) == 0){
            $PropStrArr[] = '{}';
        }


        foreach($PropStrArr as $psa){
            
            /*change company id*/
            $Cond1 = [];
            $Cond1[] = ['product_id', $n->product_id];
            //$Cond1[] = ['prop', (isset($psa) ? $psa : '{}')];
            $Cond1[] = ['confirm', 1];
            $Cond1[] = ['active', 1];
            
            $MyInfo1 = $ProdRelClass->get_all_price($Cond1);
            
            $prices = [];
            if($MyInfo1){
                foreach($MyInfo1 as $mp){
                    $prices[] = $mp->price_off;
                }
            
            $minprice = min($prices);
            //var_dump($prices);
            $newcomid;
            foreach($MyInfo1 as $mpi){
                if($minprice == $mpi->price_off){
                    $newcomid = $mpi->company_id;
                    $psa = $mpi->prop;
                } 
            }
            $n->company_id = $newcomid;
            //var_dump($psa);
            }
            $Cond = [];
            $Cond[] = ['product_id', $n->product_id];
            $Cond[] = ['prop', (isset($psa) ? $psa : '{}')];
            $Cond[] = ['confirm', 1];
            $Cond[] = ['company_id', $n->company_id];

            $MyInfo = $ProdRelClass->get_all_price($Cond);
    
    
            if($MyInfo){
                $cnt = $ths->MyDecode($MyInfo[0]->count_info);
                if($cnt && isset($cnt['avail_count']) && (int)$cnt['avail_count']>0){
                    $MyProductPrice = $MyInfo[0]->price_off;
                    $MyPropStr = $psa;
                    break;
                }
            }
        }


        if($MyProductPrice == 0 && $MyInfo){

            $cnt = $ths->MyDecode($MyInfo[0]->count_info);
            if($cnt && isset($cnt['avail_count']) && (int)$cnt['avail_count']>0){
                $MyProductPrice = $MyInfo[0]->price_off;//$PriceArr['price_off'];
                $MyPropStr = $psa;
            }
        }


        //if exist related product
        if($n->rel_id){
            $rl = explode('-', $n->rel_id);
            if($rl && count($rl)){
                foreach($rl as $rll){
                    $CondRelID = [];
                    $CondRelID[] = ['product_id', $rll];
                    $CondRelID[] = ['confirm', 1];
                    $CondRelID[] = ['prop', $MyPropStr];
                    $CondRelID[] = ['company_id', $n->company_id];
                    
                    $MyPrice2 = $ProdRelClass->get_all_price($CondRelID);
                    
                    if($MyPrice2){
                        $cnt = $ths->MyDecode($MyPrice2[0]->count_info);
                        if($cnt && isset($cnt['avail_count']) && (int)$cnt['avail_count']>0){
                            $MyProductPrice += $MyPrice2[0]->price_off;
                        }
                    }
                }
            }
        }



        $AdvArr = [];
        $Cnd2 = [];
        $Cnd2[] = [$AdvClass->MyTable.'`.`lang', $_SESSION['_Lang_']];
        $Cnd2[] = [$AdvClass->MyTable.'`.`display', 1];
        $Cnd2[] = [$AdvClass->MyTable.'`.`deleted', 0];
        $Cnd2[] = [$AdvClass->MyTable.'`.`start_date', date('Y-m-d H:i:s'), '<='];
        $Cnd2[] = [$AdvClass->MyTable.'`.`end_date', date('Y-m-d H:i:s'), '>='];
        $Cnd2[] = [$AdvClass->MyTable.'`.`link', $n->product_id];


        $AdvArr_ = $AdvClass->get_all($Cnd2, ['gift', 'type', 'end_date']);
        if($AdvArr_){
            foreach($AdvArr_ as $a){
                $AdvArr = ['gift'=>$ths->MyDecode($a->gift), 'type'=>$a->type, 'end_date'=>$a->end_date];
            }
        }

        

        if(isset($AdvArr['gift'][1]) && $AdvArr['gift'][1]){
            if($AdvArr['type'] == 7){
                $MyProductPrice = ($MyProductPrice * ((100 + $AdvArr['gift'][1]) / 100));
            }else{
                $MyProductPrice = ($MyProductPrice * ((100 - $AdvArr['gift'][1]) / 100));
            }
        }

       $MyProductPrice = ($MyProductPrice >= 1000 ? $MyProductPrice / 10 : '0');
    }
