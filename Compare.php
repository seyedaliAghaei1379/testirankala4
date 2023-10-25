<?php @session_start();
/**
 * Created by PhpStorm.
 * User: yousefi
 * Date: 8/19/20
 * Time: 9:06 AM
 */

    include_once(__DIR__ . "/../../classes/class.cnfg.php");
    $conf = new config();

    include_once($conf->BaseRoot . '/classes/class.main.php');
    $ths = new main();

    include_once($conf->BaseRoot . 'classes/class.libs_parent.php');

    include_once($conf->BaseRoot . '/classes/libs/class.product.php');
    $ProductClass = new product();

    include_once($conf->BaseRoot . '/classes/libs/class.product_rel.php');
    $ProdRelClass = new product_rel();

    include_once($conf->BaseRoot . '/classes/libs/class.files.php');
    $FilesClass = new files();

    include_once($conf->BaseRoot.'/classes/libs/class.properties.php');
    $PropClass = new properties();



    if(isset($_SESSION['CompareIDs']) && is_array($_SESSION['CompareIDs']) && count($_SESSION['CompareIDs'])){

        $Tbl = 'product_price';
        $GroupBy = 'product.product_id';

        $_SESSION['_Lang_'] = 1;
        $CondArr[] = ['display', 1];
        $CondArr[] = ['deleted', 0];
        $CondArr[] = ['lang_id', $_SESSION['_Lang_']];
        $CondArr[] = ['product`.`product_id', $_SESSION['CompareIDs'], 'in'];
        $CondArr[] = ['product`.`product_id`=`product_price`.`product_id` and `product_price`.`company_id`=1 and `product_price`.`confirm', 1];

        $AllResultArr = [];
        $AllResult = $ProductClass->get_all($CondArr, [$ProductClass->MyTable.'`.`code', $ProductClass->MyTable.'`.`product_id', $Tbl.'`.`price', $Tbl.'`.`price_off', $ProductClass->MyTable.'`.`confirm_count', $ProductClass->MyTable.'`.`confirm_price', $ProductClass->MyTable.'`.`score_person', $ProductClass->MyTable.'`.`score', $ProductClass->MyTable.'`.`title', $ProductClass->MyTable.'`.`img_id', $ProductClass->MyTable.'`.`seo'], [], [], $GroupBy, $Tbl);
        if($AllResult){
            foreach($AllResult as $alr){
                //$AllResultArr[$alr->product_id] = $alr;

                $AllResultArr[$alr->product_id] = ['code'=>$alr->code, 'product_id'=>$alr->product_id, 'price'=>$alr->price, 'price_off'=>$alr->price_off, 'confirm_count'=>$alr->confirm_count, 'confirm_price'=>$alr->confirm_price, 'score_person'=>$alr->score_person, 'score'=>$alr->score, 'title'=>$alr->title, 'img_id'=>$alr->img_id, 'seo'=>$alr->seo];

            }
        }

        foreach($_SESSION['CompareIDs'] as $cmp){
            if(!array_key_exists($cmp, $AllResultArr)){

                $_ = $ProductClass->get_by_id($cmp, [], $_SESSION['_Lang_']);

                $AllResultArr[$cmp] = ['code'=>$_->code, 'product_id'=>$_->product_id, 'price'=>0, 'price_off'=>0, 'confirm_count'=>$_->confirm_count, 'confirm_price'=>$_->confirm_price, 'score_person'=>$_->score_person, 'score'=>$_->score, 'title'=>$_->title, 'img_id'=>$_->img_id, 'seo'=>$_->seo];
            }
        }
    }


    $PropArr = [];

    if(isset($AllResultArr)){
        foreach($AllResultArr as $arai=>$ara){
            $MyProp = $PropClass->get_by_product($ara['product_id']);
            if($MyProp['All']){
                foreach($MyProp['All'] as $pi=>$p){
                    #if($pi == 0){
                        if($p['prop_id']){
                            $__ = $PropClass->get_by_id($p['prop_id']);
                            $PropArr[$p['prop_id']] = $__->title.($__->unit ? ' ('.$__->unit.')' : '');
                        }
                    #}
                    $AllResultArr[$arai]['prop'][$p['prop_id']] = $p['prop_val'];
                }
            }
        }
    }

?>

<div class="block">
    <div class="container">
        <div class="compare card">
        <?php
            if(isset($_SESSION['CompareIDs']) && is_array($_SESSION['CompareIDs']) && count($_SESSION['CompareIDs'])){
        ?>
            <!--<div class="compare__options-list">
                <div class="compare__option">
                    <div class="compare__option-control">
                        <button type="button" class="btn btn-primary btn-xs DelAll">حذف لیست</button>
                    </div>
                </div>
            </div>-->
            <div class="table-responsive">
                <table class="compare__table compare-table">
                    <tbody>
                    <tr>
                        <td colspan="10" class="compare-table__column--header"><div>محصول</div></td>
                    </tr>
                    <tr class="compare-table__row">
                        <?php
                            foreach($_SESSION['CompareIDs'] as $cmp){
                                if(isset($AllResultArr[$cmp]) && $AllResultArr[$cmp]['img_id']){
                                    $Img_ = $FilesClass->get_by_id($AllResultArr[$cmp]['img_id'], 1);
                                    $Img = $Img_['path2'];
                                }else{
                                    $Img = $conf->BaseRoot2.'MyFile/Product/none.jpg';
                                }
                        ?>
                        <td class="compare-table__column compare-table__column--product">

                                <div class="compare-table__product-image">

									 <span class="compare-btn-remove" onclick="DelAllItems('<?=$AllResultArr[$cmp]['product_id']?>')" alt="حذف">
										<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64">
										  <path d="M26.9375 4C26.0435 4 25.203813 4.3940781 24.632812 5.0800781C24.574813 5.1490781 24.527234 5.2256406 24.490234 5.3066406L22.357422 10L11 10C9.346 10 8 11.346 8 13L8 19C8 20.654 9.346 22 11 22L13 22L13 57C13 58.654 14.346 60 16 60L48 60C49.654 60 51 58.654 51 57L51 22L53 22C54.654 22 56 20.654 56 19L56 13C56 11.346 54.654 10 53 10L41.644531 10L39.511719 5.3066406C39.474719 5.2256406 39.426141 5.1480781 39.369141 5.0800781C38.797141 4.3940781 37.957453 4 37.064453 4L26.9375 4 z M 26.9375 6L37.0625 6C37.3225 6 37.569906 6.1003437 37.753906 6.2773438L39.447266 10L24.552734 10L26.246094 6.2773438C26.431094 6.1003438 26.6775 6 26.9375 6 z M 11 12L53 12C53.551 12 54 12.448 54 13L54 19C54 19.552 53.551 20 53 20L11 20C10.449 20 10 19.552 10 19L10 13C10 12.448 10.449 12 11 12 z M 14 14C13.448 14 13 14.447 13 15L13 17C13 17.553 13.448 18 14 18C14.552 18 15 17.553 15 17L15 15C15 14.447 14.552 14 14 14 z M 19 14C18.448 14 18 14.447 18 15L18 17C18 17.553 18.448 18 19 18C19.552 18 20 17.553 20 17L20 15C20 14.447 19.552 14 19 14 z M 24 14C23.448 14 23 14.447 23 15L23 17C23 17.553 23.448 18 24 18C24.552 18 25 17.553 25 17L25 15C25 14.447 24.552 14 24 14 z M 29 14C28.448 14 28 14.447 28 15L28 17C28 17.553 28.448 18 29 18C29.552 18 30 17.553 30 17L30 15C30 14.447 29.552 14 29 14 z M 35 14C34.448 14 34 14.447 34 15L34 17C34 17.553 34.448 18 35 18C35.552 18 36 17.553 36 17L36 15C36 14.447 35.552 14 35 14 z M 40 14C39.448 14 39 14.447 39 15L39 17C39 17.553 39.448 18 40 18C40.552 18 41 17.553 41 17L41 15C41 14.447 40.552 14 40 14 z M 45 14C44.448 14 44 14.447 44 15L44 17C44 17.553 44.448 18 45 18C45.552 18 46 17.553 46 17L46 15C46 14.447 45.552 14 45 14 z M 50 14C49.448 14 49 14.447 49 15L49 17C49 17.553 49.448 18 50 18C50.552 18 51 17.553 51 17L51 15C51 14.447 50.552 14 50 14 z M 15 22L49 22L49 57C49 57.552 48.551 58 48 58L16 58C15.449 58 15 57.552 15 57L15 56L38 56C38.552 56 39 55.553 39 55C39 54.447 38.552 54 38 54L15 54L15 22 z M 20 28C19.448 28 19 28.447 19 29L19 41C19 41.553 19.448 42 20 42C20.552 42 21 41.553 21 41L21 29C21 28.447 20.552 28 20 28 z M 28 28C27.448 28 27 28.447 27 29L27 49C27 49.553 27.448 50 28 50C28.552 50 29 49.553 29 49L29 29C29 28.447 28.552 28 28 28 z M 36 28C35.448 28 35 28.447 35 29L35 49C35 49.553 35.448 50 36 50C36.552 50 37 49.553 37 49L37 29C37 28.447 36.552 28 36 28 z M 44 28C43.448 28 43 28.447 43 29L43 33C43 33.553 43.448 34 44 34C44.552 34 45 33.553 45 33L45 29C45 28.447 44.552 28 44 28 z M 44 36C43.448 36 43 36.447 43 37L43 49C43 49.553 43.448 50 44 50C44.552 50 45 49.553 45 49L45 37C45 36.447 44.552 36 44 36 z M 20 44C19.448 44 19 44.447 19 45L19 49C19 49.553 19.448 50 20 50C20.552 50 21 49.553 21 49L21 45C21 44.447 20.552 44 20 44 z M 42 54C41.448 54 41 54.447 41 55C41 55.553 41.448 56 42 56L46 56C46.552 56 47 55.553 47 55C47 54.447 46.552 54 46 54L42 54 z" fill="#5B5B5B"></path>
										</svg>
									 </span>

									<a href="<?=$conf->BaseRoot2.'product/'.$AllResultArr[$cmp]['product_id'].'/'.$AllResultArr[$cmp]['seo']?>" class="compare-table__product">
										<img src="<?=$Img?>" alt="<?=$AllResultArr[$cmp]['title']?>" class="compare-image">
									</a>
                                </div>
								<br>
                                <div class="compare-table__product-name">
									<a href="<?=$conf->BaseRoot2.'product/'.$AllResultArr[$cmp]['product_id'].'/'.$AllResultArr[$cmp]['seo']?>" class="compare-table__product">
										<?=$AllResultArr[$cmp]['title']?>
									</a>
                                </div>
                        </td>
                        <?php
                            }
                        ?>
                    </tr>
                    <tr>
                        <td colspan="10" class="compare-table__column--header"><div>رتبه</div></td>
                    </tr>
                    <tr class="compare-table__row">
                        <?php
                            foreach($_SESSION['CompareIDs'] as $cmp){
                        ?>

                        <td class="compare-table__column compare-table__column--product">
                            <div class="compare-table__rating">
                                <div class="compare-table__rating-stars ">
                                    <div class="rating">
                                        <div class="rating__body">
                                            <?php
                                                $MyRate = $MyRate0 = (round($AllResultArr[$cmp]['score']) / 10) * 5;
                                                for($r = 1; $r<=5; $r++){
                                                    $Class = '';
                                                    if($MyRate >= 1){
                                                        $Class = 'rating__star--active';
                                                        $MyRate--;
                                                    }elseif($MyRate >= 0.5){
                                                        $Class = 'rating__star--active';
                                                        $MyRate = 0;
                                                    }
                                            ?>
                                                <div class="rating__star <?=$Class?>"></div>
                                            <?php
                                                }
                                            ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="compare-table__Rating-title">
                                    بر اساس

                                    <?=$AllResultArr[$cmp]['score_person']?>
                                    بررسی
                                </div>
                            </div>
                        </td>
                        <?php
                            }
                        ?>
                    </tr>
                    <tr>
                        <td colspan="10" class="compare-table__column--header"><div>قیمت</div></td>
                    </tr>
                    <tr class="compare-table__row">
                        <?php
                            foreach($_SESSION['CompareIDs'] as $cmp){
                        ?>
                        <td class="compare-table__column compare-table__column--product">
                            <?php
                                if($AllResultArr[$cmp]['confirm_count'] && $AllResultArr[$cmp]['confirm_price'] && $AllResultArr[$cmp]['price']){
                                    if(isset($AllResultArr[$cmp]['price_off']) && $AllResultArr[$cmp]['price_off'] && $AllResultArr[$cmp]['price_off'] != $AllResultArr[$cmp]['price']){
                                        echo $ths->money($AllResultArr[$cmp]['price_off']);
                                    }else{
                                        echo $ths->money($AllResultArr[$cmp]['price']);
                                    }
                                }else{
                                    echo '- - -';
                                }
                            ?>
                        </td>
                        <?php
                            }
                        ?>
                    </tr>
                    <tr>
                        <td colspan="10" class="compare-table__column--header"><div>شناسه کالا</div></td>
                    </tr>
                    <tr class="compare-table__row">
                        <?php
                            foreach($_SESSION['CompareIDs'] as $cmp){
                        ?>
                        <td class="compare-table__column compare-table__column--product">
                            <?=$AllResultArr[$cmp]['code']?>
                        </td>
                        <?php
                            }
                        ?>
                    </tr>


                    <?php
                        if($PropArr){
                            foreach($PropArr as $ppi=>$pp){
                    ?>
							<tr>
								<td colspan="10" class="compare-table__column--header"><div><?=$pp?></div></td>
							</tr>
                            <tr class="compare-table__row">
                                <?php
                                    $d = $PropClass->get_all([['prop_id',$ppi], ['lang_id', $_SESSION['_Lang_']], /*['display', 1],*/ ['deleted', 0]]);

                                    foreach($_SESSION['CompareIDs'] as $cmp){
                                        $ddd = [];
                                ?>
                                    <td class="compare-table__column compare-table__column--product">
                                        <?php
                                            if(isset($AllResultArr[$cmp]['prop']) && isset($AllResultArr[$cmp]['prop'][$ppi])){
                                                $p_ = $ths->MyDecode($AllResultArr[$cmp]['prop'][$ppi]);
                                                if($p_){
                                                    if($d[0]->type == 2 && $d[0]->value_list){
                                                        $dd = $ths->MyDecode($d[0]->value_list);
                                                        if(is_array($p_)){
                                                            foreach($p_ as $p__){
                                                                if(isset($dd[$p__])){
                                                                    $ddd[] = $dd[$p__];
                                                                }
                                                            }
                                                        }
                                                        echo implode(' | ', $ddd);
                                                    }else{
                                                        echo $p_[1];
                                                    }
                                                }else{
                                                    echo '- - -';
                                                }
                                        ?>

                                        <?php
                                            }else{
                                                echo '- - -';
                                            }
                                        ?>
                                    </td>
                                <?php
                                    }
                                ?>
                            </tr>
                    <?php
                            }
                        }
                    ?>

                    <tr>
                        <td colspan="10" class="compare-table__column--header"><div>موجودی کالا</div></td>
                    </tr>
                    <tr class="compare-table__row">
                        <?php
                            foreach($_SESSION['CompareIDs'] as $cmp){
                        ?>
                        <td class="compare-table__column compare-table__column--product">
                            <?php if($AllResultArr[$cmp]['confirm_count'] && $AllResultArr[$cmp]['confirm_price'] && $AllResultArr[$cmp]['price']){?>

                            <div class="status-badge status-badge--style--success status-badge--has-text">
                                <div class="status-badge__body">
                                    <div class="status-badge__text">موجود در انبار</div>
                                </div>
                            </div>
                            <?php }else{?>
                                <div class="status-badge status-badge--style--failure status-badge--has-text">
                                    <div class="status-badge__body">
                                        <div class="status-badge__text">ناموجود</div>
                                    </div>
                                </div>
                            <?php }?>
                        </td>

                        <?php
                            }
                        ?>
                    </tr>
                    <tr>
                        <td colspan="10" class="compare-table__column--header"><div>افزودن به سبد خرید</div></td>
                    </tr>
                    <tr class="compare-table__row">
                        <?php
                            foreach($_SESSION['CompareIDs'] as $cmp){
                        ?>
                        <td class="compare-table__column compare-table__column--product">
                            <button type="button" class="btn btn-sm btn-primary js-cd-add-to-cart prid_<?=$AllResultArr[$cmp]['product_id']?>">افزودن به سبد خرید</button>
                        </td>
                        <?php
                            }
                        ?>
                    </tr>
                    <tr class="compare-table__row">
                        <?php
                        foreach($_SESSION['CompareIDs'] as $cmp){
                            ?>
                            <td class="compare-table__column compare-table__column--product">
                                <button type="button" class="btn btn-sm btn-secondary" onclick="DelAllItems('<?=$AllResultArr[$cmp]['product_id']?>')">حذف</button>
                            </td>
                        <?php
                        }
                        ?>
                    </tr>

                    </tbody>
                </table>
            </div>
            <?php
                }else{
            ?>
                    <div class="text-center m-5">
                        موردی برای مقایسه وجود ندارد
                    </div>
            <?php
                }
            ?>

        </div>
    </div>
</div>

<div class="block-space block-space--layout--before-footer"></div>