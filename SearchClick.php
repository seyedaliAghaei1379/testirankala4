<?php session_start();

    include_once("../../classes/class.cnfg.php");
    $conf = new config();

    include_once($conf->BaseRoot.'/classes/class.main.php');
    $ths = new main();

    $_REQUEST['MyID'] = (int)$ths->MakeSecurParam($_REQUEST['MyID']);
    $_REQUEST['Hash'] = $ths->MakeSecurParam($_REQUEST['Hash']);
    $_REQUEST['SrchItem'] = $ths->MakeSecurParam($_REQUEST['SrchItem'], true);


    /*if(!isset($_REQUEST['MyID']) || !isset($_REQUEST['Hash']) || $ths->GeneratePass($_REQUEST['MyID']) != $_REQUEST['Hash']){
        $ths->MyRedirect($conf->BaseRoot2.'404', false);
        exit;
    }*/
    
    $ths->query("insert into `search` (`product_id`, `phrase`, `person_id`, `ip`, `createdate`) values ('".$_REQUEST['MyID']."', '".$_REQUEST['SrchItem']."', '".(isset($_SESSION['_LoginUserID_']) ? $_SESSION['_LoginUserID_'] : 0)."', '".$_SERVER['REMOTE_ADDR']."', '".date('Y-m-d H:i:s')."');");
    
    
    $ths->MyRedirect($conf->BaseRoot2.'product/'.$_REQUEST['MyID'], false);
    
