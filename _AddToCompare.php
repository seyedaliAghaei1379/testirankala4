<?php
session_set_cookie_params(600, '/', $_SERVER['HTTP_HOST'], true, true);
@session_start();
/**
 * Created by PhpStorm.
 * User: yousefi
 * Date: 8/17/20
 * Time: 1:56 PM
 */
    include_once(__DIR__."/../../classes/class.cnfg.php");
    $conf = new config();

    include_once($conf->BaseRoot.'/classes/class.main.php');
    $ths = new main();

    $ths->ExternalLinkCheck2();

    include_once($conf->BaseRoot.'classes/class.libs_parent.php');

    include_once($conf->BaseRoot.'/classes/libs/class.product.php');
    $ProductClass = new product();



    if(isset($_REQUEST['DelCompareID']) && $_REQUEST['DelCompareID']){
        if($_REQUEST['DelCompareID'] == 'All'){
            unset($_SESSION['CompareIDs']);
        }else{
            foreach($_SESSION['CompareIDs'] as $idi=>$idv){
                if($idv == $_REQUEST['DelCompareID']){
                    unset($_SESSION['CompareIDs'][$idi]);
                }
            }
        }

        echo (isset($_SESSION['CompareIDs']) && is_array($_SESSION['CompareIDs']) ) ? count($_SESSION['CompareIDs']) : 0;

    }else{

        if(!isset($_SESSION['CompareIDs']) || !is_array($_SESSION['CompareIDs']) || !count($_SESSION['CompareIDs'])){
            $_SESSION['CompareIDs'][] = $_REQUEST['ProdID'];
            echo count($_SESSION['CompareIDs']);
        }elseif(!in_array($_REQUEST['ProdID'], $_SESSION['CompareIDs'])){

            if(isset($_SESSION['CompareIDs']) && count($_SESSION['CompareIDs']) >= 4){
                echo 'MaxCount';
            }else{
                $_1 = $ProductClass->get_by_id($_REQUEST['ProdID'], ['cat_id'], $_SESSION['_Lang_']);
                $_2 = $ProductClass->get_by_id($_SESSION['CompareIDs'][0], ['cat_id'], $_SESSION['_Lang_']);


                if($_1->cat_id == $_2->cat_id){
                    $_SESSION['CompareIDs'][] = $_REQUEST['ProdID'];
                    echo count($_SESSION['CompareIDs']);
                }else{
                    echo 'DiffCat';
                }
            }
        }else{
            foreach($_SESSION['CompareIDs'] as $idi=>$idv){
                if($idv == $_REQUEST['ProdID']){
                    unset($_SESSION['CompareIDs'][$idi]);
                }
            }
            echo count($_SESSION['CompareIDs']);
        }


    }


