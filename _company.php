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

include_once($conf->BaseRoot . '/classes/libs/class.company.php');
$CompanyClass = new company();


//Request
$comId = $_REQUEST['MyCompany'];

$comp = $CompanyClass->get_by_id($comId);

echo $comp->title;