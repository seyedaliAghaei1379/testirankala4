<?php
session_set_cookie_params(600, '/', $_SERVER['HTTP_HOST'], true, true);
session_start();
/**
 * Created by PhpStorm.
 * User: yousefi
 * Date: 8/22/20
 * Time: 11:40 AM
 */

    include_once(__DIR__."/../../classes/class.cnfg.php");
    $conf = new config();

    include_once($conf->BaseRoot.'/classes/class.main.php');
    $ths = new main();

    $ths->ExternalLinkCheck2();

    include_once($conf->BaseRoot.'classes/class.libs_parent.php');

    include_once($conf->BaseRoot.'/classes/libs/class.category.php');
    $CatClass = new category();

    include_once($conf->BaseRoot.'classes/libs/class.properties.php');
    $PropClass = new properties();

    include_once($conf->BaseRoot.'classes/libs/class.category_properties.php');
    $PropCatClass = new category_properties();

    $CatID = (isset($_REQUEST['CatID']) ? (int)$ths->MakeSecurParam($_REQUEST['CatID']) : 0);
    $BrandID = (isset($_REQUEST['BrandID']) ? (int)$ths->MakeSecurParam($_REQUEST['BrandID']) : 0);
    $IsSurprise = (isset($_REQUEST['Surprise']) ? (int)$ths->MakeSecurParam($_REQUEST['Surprise']) : 0);


?>

<div class="widget__header widget-filters__header">
    <h2>فیلتر</h2>
</div>
<div class="widget-filters__list">
    <div class="widget-filters__item">
        <div class="filter filter--opened" data-collapse-item>
            <button type="button" class="filter__title" data-collapse-trigger>
                دسته بندی ها
                <span class="filter__arrow">
                    <svg width="12px" height="7px">
                        <path d="M0.286,0.273 L0.286,0.273 C-0070,0.629 -0075,1.204 0.276,1.565 L5.516,6.993 L10.757,1.565 C11.108,1.204 11.103,0.629 10.747,0.273 L10.747,0.273 C10.385,-0089 9.796,-0086 9.437,0.279 L5.516,4.296 L1.596,0.279 C1.237,-0086 0.648,-0089 0.286,0.273 Z"/>
                    </svg>
                </span>
            </button>
            <div class="filter__body" data-collapse-content>

                <?php
                    if($CatID){
                        $ThisCat = $CatClass->get_by_id($CatID);
                    }
                    $CatChild = $CatClass->get_all([['parentid', (($CatID && isset($ThisCat) && $ThisCat) ? $ThisCat->catid : 0)], ['deleted', 0], ['display', 1], ['lang_id', /*$_SESSION['_Lang_']*/ 1]],[], ['ordr', 'desc']);


                    if(isset($CatChild) && $CatChild){
                        $FamilyCat = $CatClass->get_all([['parentid', ((isset($ThisCat) && $ThisCat) ? $ThisCat->parentid : 0)], ['display', 1], ['deleted', 0], ['lang_id', /*$_SESSION['_Lang_']*/ 1]], [], ['ordr', 'desc']);
                        $CurrFather = $CatID;

                    }else{
                        $ThisCat = $CatClass->get_by_id((isset($ThisCat) ? $ThisCat->parentid : 0));
                        $CatChild = $CatClass->get_all([['parentid', (isset($ThisCat->catid) ? $ThisCat->catid : 0)], ['deleted', 0], ['lang_id', /*$_SESSION['_Lang_']*/ 1]]);
                        $FamilyCat = $CatClass->get_all([['parentid', (isset($ThisCat->parentid) ? $ThisCat->parentid : 0)], ['display', 1], ['deleted', 0], ['lang_id', /*$_SESSION['_Lang_']*/ 1]], [], ['ordr', 'desc']);
                        $CurrFather = isset($ThisCat->catid) ? $ThisCat->catid : 0;
                    }



                    if($FamilyCat){
                ?>
                <div class="filter__container">
                    <div class="filter-categories">
                        <ul class="filter-categories__list">

                            <?php
                                foreach($FamilyCat as $fc){
                            ?>

                            <li class="filter-categories__item filter-categories__item--parent">
                                <span class="filter-categories__arrow">
                                    <svg width="7" height="11">
                                        <path d="M0.3,10.7L0.3,10.7c0.4,0.4,0.9,0.4,1.3,0L7,5.5L1.6,0.3C1.2-0.1,0.7,0,0.3,0.3l0,0c-0.4,0.4-0.4,1,0,1.3l4,3.9l-4,3.9 	C-0.1,9.8-0.1,10.4,0.3,10.7z"></path>
                                    </svg>
                                </span>
                                <a href="<?=$conf->BaseRoot2.($IsSurprise ? 'surprise/'.$fc->catid.'/1' : 'category/'.$fc->catid).'/'.$ths->UrlFriendly($fc->title)?>">
                                    <?=$fc->title?>
                                </a>
                            </li>

                            <?php
                                if($CurrFather == $fc->catid && $CatChild){
                                    foreach($CatChild as $ch){
                            ?>

                                    <li class="filter-categories__item filter-categories__item--child">
                                        <a href="<?=$conf->BaseRoot2.($IsSurprise ? 'surprise' : 'category').'/'.$ch->catid.'/'.$ths->UrlFriendly($ch->title)?>">
                                            <?=$ch->title?>
                                        </a>
                                    </li>
                            <?php
                                    }
                                }
                            ?>

                            <?php
                                }
                            ?>

                        </ul>
                    </div>
                </div>
                <?php
                    }
                ?>
            </div>
        </div>
    </div>

    <div class="widget-filters__item">
        <div class="filter filter--opened" data-collapse-item>
            <button type="button" class="filter__title" data-collapse-trigger>
                قیمت
                <span class="filter__arrow">
                    <svg width="12px" height="7px">
                        <path d="M0.286,0.273 L0.286,0.273 C-0070,0.629 -0075,1.204 0.276,1.565 L5.516,6.993 L10.757,1.565 C11.108,1.204 11.103,0.629 10.747,0.273 L10.747,0.273 C10.385,-0089 9.796,-0086 9.437,0.279 L5.516,4.296 L1.596,0.279 C1.237,-0086 0.648,-0089 0.286,0.273 Z"/>
                    </svg>
                </span>
            </button>
            <div class="filter__body" data-collapse-content>
                <div class="filter__container" onclick="SetPrice21(this)">
                    <div class="filter-price" data-min="500" data-max="10000000" data-from="0" data-to="1000000">
                        <div class="filter-price__slider"></div>
                        <div class="filter-price__title-button">
                            <div class="filter-price__title">
                                تومان
                               <span class="filter-price__max-value"></span>
                                – تومان
                                <span class="filter-price__min-value"></span>
                            </div>
                            <!--<button type="button" class="btn btn-xs btn-secondary filter-price__button">
                                فیلتر
                            </button>-->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="widget-filters__item MyBrand0">
        <div class="filter filter--opened" data-collapse-item>
            <button type="button" class="filter__title" data-collapse-trigger>برند <span
                    class="filter__arrow">
                    <svg width="12px" height="7px">
                        <path
                            d="M0.286,0.273 L0.286,0.273 C-0070,0.629 -0075,1.204 0.276,1.565 L5.516,6.993 L10.757,1.565 C11.108,1.204 11.103,0.629 10.747,0.273 L10.747,0.273 C10.385,-0089 9.796,-0086 9.437,0.279 L5.516,4.296 L1.596,0.279 C1.237,-0086 0.648,-0089 0.286,0.273 Z"/>
                    </svg>
                </span>
            </button>
            <div class="filter__body " data-collapse-content>
                <div class="filter__container">
                    <div class="filter-list">
                        <div class="filter-list__list MyBrand">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="widget-filters__item MyFilter0">
        <?php
            $AllProp = $PropCatClass->get_all([['cat_id', $CatID]]);
            $PropIDsArr = [];
            if($AllProp){
                foreach($AllProp as $ap){
                    $PropIDsArr[] = $ap->prop_id;
                }
                $MyProp = $PropClass->get_all([['prop_id', $PropIDsArr, 'in'], ['main_filter', 1], ['type', 2]]);
                if($MyProp){

                    $MyFilter = [];
                    if(isset($_REQUEST)){
                        foreach($_REQUEST as $ri=>$rv){
                            if(strpos($ri, 'fliters') !== false){
                                if($rv){
                                    $f1 = explode('&', $rv);
                                    foreach($f1 as $f2){
                                        $f3 = explode('=', $f2);
                                        $MyFilter[str_replace('filter_', '', $f3[0])] = $f3[1];
                                    }
                                }

                            }
                        }
                    }
                    foreach($MyProp as $mp){
        ?>
                        <div class="filter filter--opened" data-collapse-item>
                            <button type="button" class="filter__title" data-collapse-trigger>
                                <?=$mp->title?>
                                <span class="filter__arrow">
                                    <svg width="12px" height="7px">
                                        <path
                                            d="M0.286,0.273 L0.286,0.273 C-0070,0.629 -0075,1.204 0.276,1.565 L5.516,6.993 L10.757,1.565 C11.108,1.204 11.103,0.629 10.747,0.273 L10.747,0.273 C10.385,-0089 9.796,-0086 9.437,0.279 L5.516,4.296 L1.596,0.279 C1.237,-0086 0.648,-0089 0.286,0.273 Z"/>
                                    </svg>
                                </span>
                            </button>
                            <div class="filter__body " data-collapse-content>
                                <div class="filter__container">
                                    <div class="filter-list">
                                        <div class="filter-list__list MyFilter">
                                            <select id="filter_<?=$mp->prop_id?>" name="filter_<?=$mp->prop_id?>" class="form-control filter_element">
                                                <option value="none" selected>- - -</option>
                                                <?php
                                                    $v = $ths->MyDecode($mp->value_list);
                                                    foreach($v as $vi=>$vv){
                                                ?>
                                                        <option value="<?=$vi?>" <?=((isset($MyFilter[$mp->prop_id]) && $MyFilter[$mp->prop_id] == $vi) ? 'selected' : '')?>><?=$vv?></option>
                                                <?php
                                                    }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
        <?php
                    }
                }
            }
        ?>
    </div>

</div>

<div class="widget-filters__actions d-flex">
    <button class="btn btn-primary btn-sm" onclick="SetFilter21()">اعمال فیلتر</button>
</div>
