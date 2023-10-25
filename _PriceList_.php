<?php session_start();?>
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

    if(!isset($_SESSION['_LoginID_'])){
        exit;
    }

    set_time_limit(0);

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


    $AllProduct = $ths->GetData($ths->query("select * from `tahlilgar_product2` where `flag`=1 order by `update_date` desc;"));

    if($AllProduct){
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
                    کد کالا
                </th>
                <th style="text-align: right">
                    نام محصول
                </th>
                <th>
                    قیمت  خرید(ريال)
                </th>
                <th>
                    قیمت همکار نقدی(ريال)
                </th>
                <th>
                    تفاوت قیمت (ريال)
                </th>
            </tr>
<?php
        $Row = 0;
        foreach($AllProduct as $ap){
            if($ap->price0 == 0 /*|| $ap->price == 0*/){
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
                <td style="text-align: right">
                        <?=$ap->onvan?>
                </td>
                <td>
                    &nbsp;
                    <?=$ths->money($ap->price0)?>
                    &nbsp;
                </td>
                <td>
                    &nbsp;
                    <?=$ths->money($ap->price)?>
                    &nbsp;
                </td>
                <td>
                    &nbsp;
                    <?php
                        $p = $ap->price - $ap->price0;
                        echo $ths->money($p);
                        echo '&nbsp;';
                        echo '(';
                        echo round((100 - ($ap->price * 100 / $ap->price0)), 1);
                        echo '%)';
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


