<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" dir="rtl">
<head>
    <meta http-equiv="Expires" content="Fri, Jan 01 1900 00:00:00 GMT"/>
    <meta http-equiv="Pragma" content="no-cache"/>
    <meta http-equiv="Cache-Control" content="no-cache"/>
    <meta http-equiv="content-language" content="fa"/>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
    <meta name="keywords" content="List of All Product">
    <meta name="description" content="List of All Product">
</head>
<body>
<?php
/**
 * Created by PhpStorm.
 * User: yousefi
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

    $AllProduct = $ths->GetData($ths->query("select `product_id`, `title`, `model`, `confirm_price`, `confirm_count`, `cat_id` from `product` where `display`=1 and `deleted`=0 and `confirm_price`=1 and `confirm_count`=1 and `title_en`='' order by `product_id` asc;"));

    $ProdIDs = [];
    $CatIDs = [];
    $ProdInfo = [];
    if($AllProduct){
        foreach($AllProduct as $ap){
            $ProdIDs[] = $ap->product_id;
            /*$CatIDs[] = $ap->cat_id;*/
            $ProdInfo[$ap->product_id] = [  'title'=>$ap->title,
                                            'model'=>$ap->model,
                                            'confirm_price'=>$ap->confirm_price,
                                            'confirm_count'=>$ap->confirm_count,
                                            'cat_id'=>$ap->cat_id]
                                            ;
        }
    }
    $Allcat = $ths->GetData($ths->query("select `catid`, `title` from `category` where `display`=1 and `deleted`=0"));

    $AllCode = $ths->GetData($ths->query("select `code`, `code2`, `prop`, `count_info`, `price_off`, `product_id` from `product_price` where `product_id` in (".implode(',', $ProdIDs).") and `confirm`=1 order by `product_id` desc;"));


    if($AllCode){
?>
    <style>
        table{font-family: tahoma, Arial, Helvetica, sans-serif;font-size: 14px;}
        table tr th, table tr td{padding: 5px;text-align: center}
        table tr th{background:#d0d0d0;}
        table tr:nth-child(2n){background:#f0f0f0;}
        table tr:nth-child(2n+1){background:#f8f8f8;}
    </style>

        <table>
            <tr>
                <th>
                    ردیف
                </th>
                <th>
                    شناسه محصول
                </th>
                <th>
                    کد کالا
                </th>
                <th style="text-align: right">
                    نام محصول
                </th>
                <th>
                    قیمت (ريال)
                </th>
                <th>
                    وضعیت
                </th>
                <th>
                    دسته بندی
                </th>
            </tr>
<?php
        $Row = 0;
        foreach($AllCode as $ap){
            if($ap->price_off < 1000) continue;
            if(!$ProdInfo[$ap->product_id]['confirm_price'] || !$ProdInfo[$ap->product_id]['confirm_count']) continue;

            $cnt = $ths->MyDecode($ap->count_info);
            if($cnt['avail_count'] <= 0){
                continue;
            }

?>
            <tr>
                <td>
                    <?=(++$Row)?>
                </td>
                <td>
                    <?=$ap->code?>
                </td>
                <td>
                    <?=($ap->code2 ? $ap->code2 : '- - -')?>
                </td>
                <td style="text-align: right">
                    <a href="<?=$conf->BaseRoot2.'product/'.$ap->product_id.'/'.$ths->UrlFriendly($ProdInfo[$ap->product_id]['title'].($ProdInfo[$ap->product_id]['model'] ? '-'.$ProdInfo[$ap->product_id]['model'] : ''))?>">
                        <?php
                            echo $ProdInfo[$ap->product_id]['title'];
                            echo ($ProdInfo[$ap->product_id]['model'] ? ' '.$ProdInfo[$ap->product_id]['model'] : '');
                            if($ap->prop && $ap->prop!='{}'){
                                $_ = $ths->MyDecode($ap->prop);
                                if($_ && is_array($_)){
                                    foreach($_ as $_i=>$_v){
                                        if(isset($PropArr[$_i]) && isset($PropArr[$_i][$_v])){
                                            echo ' - '.$PropArr[$_i][$_v];
                                        }
                                    }
                                }
                            }
                        ?>
                    </a>
                </td>
                <td>
                    &nbsp;
                    <?=$ths->money($ap->price_off)?>
                    &nbsp;
                </td>
                <td>
                    &nbsp;
                    <?php
                        $cnt = $ths->MyDecode($ap->count_info);
                        echo ($cnt['avail_count'] > 0 ? '' : 'نا').'موجود'
                    ?>
                    &nbsp;
                </td>
                <td>
                    &nbsp;
                    <?php
                    echo $ProdInfo[$ap->product_id]['cat_id'];
                    ?>
                    &nbsp;
                </td>
            </tr>
<?php
        }
?>
            </table>
<?php
    }


