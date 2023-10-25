
<?php
/**
 * Created by PhpStorm.
 * User: moradi
 * Date: 11/23/20
 * Time: 5:03 PM
 */

    include_once("../../classes/class.cnfg.php");
    $conf = new config();


    include_once($conf->BaseRoot.'/classes/class.main.php');
    $ths = new main();

    $PropArr = [];
    $PropList = $ths->GetData($ths->query("SELECT prop_id, value_list FROM `properties` where deleted=0"));
    if($PropList){
        foreach($PropList as $p){
            $PropArr[$p->prop_id] = $ths->MyDecode($p->value_list);
        }
    }


    #$AllProduct = $ths->GetData($ths->query("select `product`.`product_id`, `product_price`.`code`, `product_price`.`code2`, `title`, `model`, `prop`, `product_price`.`count_info`, `price_off` from `product`, `product_price` where `product_price`.`product_id`=`product`.`product_id` and `product`.`display`=1 and `product`.`deleted`=0 and `product`.`confirm_price`=1 and `product_price`.`confirm`=1 group by `product_price`.`code` order by `product`.`product_id` asc;"));

    $AllProduct = $ths->GetData($ths->query("select `product_id`, `title`, `model`, `confirm_price`, `confirm_count`, `rel_id` from `product` where `display`=1 and `deleted`=0 and `confirm_price`=1 and `confirm_count`=1 order by `product_id` asc;"));

    $ProdIDs = [];
    $ProdInfo = [];
    if($AllProduct){
        foreach($AllProduct as $ap){
            $ProdIDs[] = $ap->product_id;
            $ProdInfo[$ap->product_id] = [  'title'=>$ap->title,
                                            'model'=>$ap->model,
                                            'confirm_price'=>$ap->confirm_price,
                                            'confirm_count'=>$ap->confirm_count,
                                            'RelID'=>$ap->rel_id];
        }
    }


    $AllCode = $ths->GetData($ths->query("select `code`, `code2`, `prop`, `count_info`, `price_off`, `product_id` from `product_price` where `product_id` in (".implode(',', $ProdIDs).") and `confirm`=1 and `active`=1 order by `product_id` desc;"));


    if($AllCode){
?>
   
<?php
        $Row = 0;
        foreach($AllCode as $ap){
            if($ap->price_off < 1000) continue;
            if(!$ProdInfo[$ap->product_id]['confirm_price'] || !$ProdInfo[$ap->product_id]['confirm_count']) continue;

            $cnt = $ths->MyDecode($ap->count_info);
            if(isset($cnt) && $cnt && isset($cnt['avail_count']) && $cnt['avail_count'] <= 0){
                continue;
            }
            $att = '';
            if($ap->prop && $ap->prop!='{}'){
                                $_ = $ths->MyDecode($ap->prop);
                                if($_ && is_array($_)){
                                    foreach($_ as $_i=>$_v){
                                        if(isset($PropArr[$_i]) && isset($PropArr[$_i][$_v])){
                                            $att = ' - '.$PropArr[$_i][$_v];
                                        }
                                    }
                                }
                            }
            $title = $ProdInfo[$ap->product_id]['title'] . ($ProdInfo[$ap->product_id]['model'] ? ' '.$ProdInfo[$ap->product_id]['model'] : '') . ' - ' . $att;
            
                        $MyPrice = $ap->price_off;
                        if($ap->price_off < $ap->price){
                            $old_price = $ap->price;   
                        } else {
                            $old_price = null;
                        }
                        if($ProdInfo[$ap->product_id]['RelID']){
                            $rl = explode('-', $ProdInfo[$ap->product_id]['RelID']);
                            if($rl && count($rl)){
                                foreach($rl as $rll){

                                    $MyPrice2 = $ths->GetData($ths->query("select `code`, `code2`, `prop`, `count_info`, `price_off`, `product_id` from `product_price` where `product_id`=".$rll." and `confirm`=1 and `prop`='".$ap->prop."' order by `product_id` desc;"));

                                    if($MyPrice2){
                                        $cnt = $ths->MyDecode($MyPrice2[0]->count_info);
                                        if($cnt && isset($cnt['avail_count']) && (int)$cnt['avail_count']>0){
                                            $MyPrice += $MyPrice2[0]->price_off;
                                        }
                                    }
                                }
                            }
                        }
            
          $products[] = [
                  'id' => $ap->code ? $ap->code : $ap->product_id,
                  'title' => $title,
                  'url' => '/product/'.$ap->product_id.'/'.$ths->UrlFriendly($ProdInfo[$ap->product_id]['title'].($ProdInfo[$ap->product_id]['model'] ? '-'.$ProdInfo[$ap->product_id]['model'] : '')),
                  'price' => $MyPrice,
                  'guarantee' => NULL,
                  'old_price' => $old_price,
                  'is_available' => true,
              ];  
              ++$Row;
?>
            
<?php
        }
?>
            
<?php
            
    }
    /*var_dump($products);
    exit();*/
$arr = array (
              'success' => true,
              'products' => 
              $products,
              'total' => $Row,
            );
    header('Content-Type = application/json; charset=utf-8;');
    echo json_encode($arr);
