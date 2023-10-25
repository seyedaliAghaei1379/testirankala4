<?php
@session_set_cookie_params(600, '/', $_SERVER['HTTP_HOST'], true, true);
@session_start();
/**
 * Created by PhpStorm.
 * User: yousefi
 * Date: 12/29/21
 * Time: 8:58 AM
 */
include_once(__DIR__ . "/../../classes/class.cnfg.php");
$conf = new config();

include_once($conf->BaseRoot . '/classes/class.main.php');
$ths = new main();

#$ths->ExternalLinkCheck2();


include_once($conf->BaseRoot . 'classes/class.libs_parent.php');

include_once($conf->BaseRoot . '/classes/libs/class.product_rel.php');
$ProdRelClass = new product_rel();

include_once($conf->BaseRoot . '/classes/libs/class.product_company.php');
$ProdCompanyClass = new product_company();

include_once($conf->BaseRoot . '/classes/libs/class.product_price.php');
$productPriceClass = new product_price();

include_once($conf->BaseRoot . '/classes/libs/class.company.php');
$CompanyClass = new company();

include_once($conf->BaseRoot . '/classes/libs/class.garanti.php');
$GarantiClass = new garanti();

include_once($conf->BaseRoot . '/classes/libs/class.person_company.php');
$PrCompanyClass = new person_company();

include_once($conf->BaseRoot . '/classes/libs/class.city.php');
$CityClass = new city();

include_once($conf->BaseRoot . '/classes/libs/class.person.php');
$PersonClass = new person();

$arrPrice = [];
$arrPrice[] = ['product_id', $_REQUEST['MyID']];
$arrPrice[] = ['active', 1];
$arrPrice[] = ['confirm', 1];
$prcomp = $productPriceClass->get_all($arrPrice);

$AllPrice = [];
$ComId = [];
$garantiMonth = [];
$garantiId = [];
if ($prcomp && count($prcomp) > 1) {
    ?>
<div class="product-tabs__pane product-tabs__pane--active" id="product-tab-analogs">
    <table class="analogs-table">
        <thead>
        <tr>
            <th class="analogs-table__column analogs-table__column--name">نام تامین کننده</th>
            <th class="analogs-table__column analogs-table__column--name">شهر تامین کننده</th>
            <th class="analogs-table__column analogs-table__column--vendor">گارانتی</th>
            <th class="analogs-table__column analogs-table__column--rating">مدت گارانتی</th>
            <th class="analogs-table__column analogs-table__column--price">قیمت (تومان)</th>
            <th class="analogs-table__column analogs-table__column--price">انتخاب</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $propArr = [];
        //$checkProp = false;
        foreach ($prcomp as $pc) {
            if($pc->prop == '{}'){
            $AllPrice[] = $pc->price_off;
            $ComId[$pc->price_off] = $pc->company_id;
            $garantiId[$pc->price_off] = $pc->garanti_id;
            $garantiMonth[$pc->price_off] = $pc->garanti_month;
            }else {
                //$checkProp = true;
                
                if($_REQUEST['MyPropStr'] == $pc->prop){
                    $AllPrice[] = $pc->price_off;
                    $ComId[$pc->price_off] = $pc->company_id;
                    $garantiId[$pc->price_off] = $pc->garanti_id;
                    $garantiMonth[$pc->price_off] = $pc->garanti_month;
                }
            }
        }
        
        /*if($checkProp){
        foreach ($prcomp as $pc) {
           
        }
        }*/
        
        $AllPrice = array_map('intval', $AllPrice);
        natsort($AllPrice);
        foreach($AllPrice as $p){
            
            ?>
            <tr>
                <td class="analogs-table__column analogs-table__column--name">
                    <p href=""
                       class="analogs-table__product-name"><?= $CompanyClass->get_title_by_id($ComId[$p]) ?></p>
                </td>
                <td class="analogs-table__column analogs-table__column--rating">
                    <?php
                    
                    $pr_cmp = $PrCompanyClass->get_all([['company_id', $ComId[$p]]]);
                    
                    $ct = $CityClass->get_city($pr_cmp[0]->cityid);
                    echo $ct->title;
                    ?>
                </td>
                <td class="analogs-table__column analogs-table__column--rating">
                    <!--<div class="analogs-table__rating">
                        <div class="analogs-table__rating-stars">
                            <div class="rating">
                                <div class="rating__body">
                                    <div class="rating__star rating__star--active"></div>
                                    <div class="rating__star rating__star--active"></div>
                                    <div class="rating__star rating__star--active"></div>
                                    <div class="rating__star rating__star--active"></div>
                                    <div class="rating__star"></div>
                                </div>
                            </div>
                        </div>
                        <div class="analogs-table__rating-label">10 بررسی</div>
                    </div>-->
                    <?= ($garantiId[$p] ? $GarantiClass->get_title_by_id($garantiId[$p]) : '- - -') ?>
                </td>
                <td class="analogs-table__column analogs-table__column--vendor" data-title="Vendor">
                    <?= ($garantiMonth[$p] ? $garantiMonth[$p] : '- - -') ?>
                </td>
                <td class="analogs-table__column analogs-table__column--price">
                    <?= ($p ? $ths->money($p / 10) : '- - -'); ?>
                    <input type="hidden" id="CompanyRow_<?= $ComId[$p] ?>" name="CompanyRow_<?= $ComId[$p] ?>"
                           value="<?= $ComId[$p] ?>">
                </td>
                <td class="analogs-table__column analogs-table__column--price">
                    <input type="button" id="btn_sel_<?= $ComId[$p] ?>" name="btn_sel_<?= $ComId[$p] ?>"
                           value="انتخاب" class="form-control btn btn-primary btn-block">
                </td>
            </tr>
        <?php } ?>
        </tbody>
    </table>

</div>
    <?php
}
?>
