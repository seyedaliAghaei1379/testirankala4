<?php
@session_set_cookie_params(600, '/', $_SERVER['HTTP_HOST'], true, true);
@session_start();
/**
 * Created by PhpStorm.
 * User: moradi
 * Date: 12/29/23
 * Time: 8:58 AM
 */
include_once(__DIR__ . "/../../classes/class.cnfg.php");
$conf = new config();

include_once($conf->BaseRoot . '/classes/class.main.php');
$ths = new main();

include_once($conf->BaseRoot . 'classes/class.libs_parent.php');

//Requests
$SelPropStr = '';
$PropStr2 = $_REQUEST['MyPropStr'];
$ItemID = $_REQUEST['MyID'];

if(isset($_SESSION['_BasketItems_'])){
    $MyCnt = ((isset($_SESSION['_BasketItems_']) && isset($_SESSION['_BasketItems_'][$_REQUEST['MyID'].'-{'.$SelPropStr.'}'])) ? $_SESSION['_BasketItems_'][$_REQUEST['MyID'].'-{'.$SelPropStr.'}']['count'] : $_SESSION['_BasketItems_'][$_REQUEST['MyID'].'-'.$PropStr2]['count']);
         $MyCnt = ($MyCnt == null ? 0 : $MyCnt);
         $arr = ['MyCnt'=>$MyCnt,
                 'prodid'=>$ItemID,
                 'PropStr'=>$PropStr2];
         header('Content-Type: application/json; charset=utf-8');
         echo json_encode($arr);
} else {
    $arr = ['MyCnt'=>0,
                 'prodid'=>$ItemID,
                 'PropStr'=>$PropStr2];
         header('Content-Type: application/json; charset=utf-8');
         echo json_encode($arr);
}
?>

