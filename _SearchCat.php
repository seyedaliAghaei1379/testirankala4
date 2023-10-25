<?php
session_set_cookie_params(600, '/', $_SERVER['HTTP_HOST'], true, true);
session_start();
/**
 * Created by PhpStorm.
 * User: yousefi
 * Date: 9/6/20
 * Time: 9:22 AM
 */

    include_once(__DIR__."/../../classes/class.cnfg.php");
    $conf = new config();

    include_once($conf->BaseRoot.'/classes/class.main.php');
    $ths = new main();

    $ths->ExternalLinkCheck();

    include_once($conf->BaseRoot.'classes/class.libs_parent.php');

    include_once($conf->BaseRoot.'/classes/libs/class.category.php');
    $CatClass = new category();

?>
<div class="search__dropdown-arrow"></div>
<div class="vehicle-picker__panel vehicle-picker__panel--list vehicle-picker__panel--active" data-panel="list">
    <div class="vehicle-picker__panel-body">

        <div class="vehicle-picker__text">محصول خود را را برای یافتن جستجوی مناسب انتخاب کنید
        </div>
        <div class="vehicles-list">
            <div class="vehicles-list__body">
                <?php
                    $CatCond = [];
                    $CatCond[] = ['display', 1];
                    $CatCond[] = ['deleted', 0];
                    $CatCond[] = ['lang_id', $_SESSION['_Lang_']];
                    $CatCond[] = ['parentid', 0];
                    $AllCat = $CatClass->get_all($CatCond, ['catid', 'title'], ['ordr', 'desc']);

                    if($AllCat){
                        foreach($AllCat as $c){
                ?>
                            <label class="vehicles-list__item">
                                <span class="vehicles-list__item-radio input-radio">
                                    <span class="input-radio__body">
                                        <input class="input-radio__input" id="radio_<?=$c->catid?>" name="header-vehicle" type="radio" onchange="SelCat(<?=$c->catid?>)" <?=((isset($_REQUEST['SelSrchCat']) && $_REQUEST['SelSrchCat'] == $c->catid) ? 'checked' : '')?>>
                                        <span class="input-radio__circle"></span>
                                    </span>
                                </span>
                                <span class="vehicles-list__item-info">
                                    <span class="vehicles-list__item-name">
                                        <?=$c->title?>
                                    </span>
                                </span>
                                <button type="button" class="vehicles-list__item-remove" onclick="RemoveSel(<?=$c->catid?>)">
                                    <svg width="16" height="16">
                                        <path
                                            d="M2,4V2h3V1h6v1h3v2H2z M13,13c0,1.1-0.9,2-2,2H5c-1.1,0-2-0.9-2-2V5h10V13z"/>
                                    </svg>
                                </button>
                            </label>
                <?php
                        }
                    }
                ?>
                </div>
        </div>
    </div>
</div>
