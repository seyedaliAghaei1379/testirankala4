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

<button type="button" class="vehicle-picker-modal__close" onclick="CloseModal()">
    <svg width="12" height="12">
        <path d="M10.8,10.8L10.8,10.8c-0.4,0.4-1,0.4-1.4,0L6,7.4l-3.4,3.4c-0.4,0.4-1,0.4-1.4,0l0,0c-0.4-0.4-0.4-1,0-1.4L4.6,6L1.2,2.6
	c-0.4-0.4-0.4-1,0-1.4l0,0c0.4-0.4,1-0.4,1.4,0L6,4.6l3.4-3.4c0.4-0.4,1-0.4,1.4,0l0,0c0.4,0.4,0.4,1,0,1.4L7.4,6l3.4,3.4
	C11.2,9.8,11.2,10.4,10.8,10.8z"/>
    </svg>
</button>
<div class="vehicle-picker-modal__panel vehicle-picker-modal__panel--active" data-panel="list">
    <div class="vehicle-picker-modal__title card-title">انتخاب دسته بندی</div>
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
                        <input class="input-radio__input" name="header-vehicle" type="radio" onchange="SelCat(<?=$c->catid?>)" <?=((isset($_REQUEST['SelSrchCat']) && $_REQUEST['SelSrchCat'] == $c->catid) ? 'checked' : '')?>>
                        <span class="input-radio__circle"></span>
                    </span>
                </span>
                <span class="vehicles-list__item-info">
                    <span class="vehicles-list__item-name">
                        <?=$c->title?>
                    </span>
                </span>
                <button type="button" class="vehicles-list__item-remove" onclick="RemoveSel2(<?=$c->catid?>)">
                    <svg width="16" height="16">
                        <path d="M2,4V2h3V1h6v1h3v2H2z M13,13c0,1.1-0.9,2-2,2H5c-1.1,0-2-0.9-2-2V5h10V13z"/>
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
