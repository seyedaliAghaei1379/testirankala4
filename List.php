<?php
/**
 * Created by PhpStorm.
 * User: yousefi
 * Date: 8/18/20
 * Time: 10:00 AM
 */
    global $ths, $conf, $PID;

    include_once($conf->BaseRoot.'/classes/libs/class.category.php');
    $CatClass = new category();

    include_once($conf->BaseRoot.'/classes/libs/class.files.php');
    $FilesClass = new files();

    include_once($conf->BaseRoot.'/classes/libs/class.product.php');
    $ProductClass = new product();
    
    include_once($conf->BaseRoot.'/classes/libs/class.brand.php');
    $BrandClass = new brand();
    
    include_once($conf->BaseRoot.'/classes/libs/class.product_rel.php');
    $ProdRelClass = new product_rel();


    $CatID = (isset($_REQUEST['CatID']) ? (int)$ths->MakeSecurParam($_REQUEST['CatID']) : 0);
    $BrandID = (isset($_REQUEST['BrandID']) ? (int)$ths->MakeSecurParam($_REQUEST['BrandID']) : 0);
    $IsSurprise = (isset($_REQUEST['Surprise']) ? $_REQUEST['Surprise'] : 0);


    if(isset($_REQUEST['SrchItem'])){
        $_REQUEST['SrchItem'] = $ths->MakeSecurParam($_REQUEST['SrchItem'], true);
    }
    
    if(isset($_REQUEST['SrchItem2'])){
        $_REQUEST['SrchItem2'] = $ths->MakeSecurParam($_REQUEST['SrchItem2'], true);
    }
    
    if(!$CatID && !$BrandID && !in_array($PID, [6, 8])){
?>

    <div class="container">
        <div class="block-split__row row no-gutters">
            <div class="block-split__item block-split__item-content col-auto">
                <div class="block">
                    <div class="categories-list categories-list--layout--columns-4-full">
                        <ul class="categories-list__body">

                            <?php
                                $Cond[] = ['display', 1];
                                $Cond[] = ['deleted', 0];
                                $Cond[] = ['lang_id', 1];
                                $Cond[] = ['parentid', 0];

                                $AllCat = $CatClass->get_all($Cond);

                                if($AllCat){
                                    foreach($AllCat as $c){

                                        $Img = $FilesClass->get_all(2, $c->catid);

                                        $ImgUrl1 = '';
                                        $ImgAlt = $ImgTitle = '';
                                        if($Img && $Img[count($Img) - 1]){
                                            $ImgUrl1 = $Img[count($Img) - 1]['path2'];
                                            $ImgAlt = $Img[count($Img) - 1]['alt'];
                                            $ImgTitle = $Img[count($Img) - 1]['title'];
                                        }
                                        if(!$ImgAlt){
                                            $ImgAlt = $c->title.' '.(isset($c->model) ? $c->model : '');
                                        }
                                        if(!$ImgTitle){
                                            $ImgTitle = $c->title.' '.(isset($c->model) ? $c->model : '');
                                        }
                            ?>
                                        <li class="categories-list__item">
                                            <a href="<?=$conf->BaseRoot2.'category/'.$c->catid.'/'.$ths->UrlFriendly($c->title)?>" title="<?=$ImgTitle?>">
                                                <img src="<?=$ImgUrl1?>" alt="<?=$ImgAlt?>">
                                                <div class="categories-list__item-name">
                                                    <?=$c->title?>
                                                </div>
                                            </a>
                                            <div class="categories-list__item-products">
                                                <?php
                                                    $PrCond = [];
                                                    $PrCond[] = ['display', 1];
                                                    $PrCond[] = ['deleted', 0];
                                                    $PrCond[] = ['lang_id', 1];
                                                    $PrCond[] = ['product`.`product_id`=`product_price`.`product_id` and `product_price`.`confirm', 1];
                                                    $PrCond[] = ['cat_id', $CatClass->get_all_child($c->catid), 'in'];
                                                    $prd = $ProductClass->get_all($PrCond, ['product`.`product_id'], [], [], 'product.product_id', 'product_price');

                                                    if($prd){
                                                        echo count($prd);
                                                    }else{
                                                        echo 'بدون';
                                                    }
                                                ?>
                                                محصول
                                            </div>
                                        </li>
                                        <li class="categories-list__divider"></li>
                            <?php
                                    }
                                }
                            ?>
                        </ul>
                    </div>
                </div>

                <div class="block-space block-space--layout--divider-nl"></div>












                <div class="block block-products-carousel" data-layout="horizontal">
                    <?= $ths->LoadPage($conf->UserPanelUrl . 'Home/Special') ?>
                </div>

                <div class="block-space block-space--layout--divider-nl"></div>

                <?= $ths->LoadPage($conf->UserPanelUrl . 'Home/Brands') ?>

                </div>
        </div>

        <div class="block-space block-space--layout--before-footer"></div>
    </div>

<?php
    }elseif($CatID || $BrandID || in_array($PID, [6, 8])){

        $ProInfo = $CatClass->get_by_id($CatID, ['parentid']);
        ?>
<div class="block-split block-split--has-sidebar">
    <div class="container">
        
        
        
       
       
<?php
    if(!$BrandID){

        if(!isset($_SESSION['_Lang_'])){
            $_SESSION['_Lang_'] = 1;
        }

        $c1 = $CatClass->get_all_child($CatID);
        $c1[] = $CatID;


        #$cnd1[] = ['deleted', 0];
        #$cnd1[] = ['display', 1];
        $cnd1[] = ['cat_id', $c1, 'in'];
        #$cnd1[] = ['lang_id', $_SESSION['_Lang_']];
        $p1 = $ProductClass->get_all($cnd1, ['brand_id']);
        $b1 = [];
        if($p1){
            foreach($p1 as $pp1){
                $b1[] = $pp1->brand_id;
            }
        }


        $CondArr = [];
        #$CondArr[] = ['display', 1];
        $CondArr[] = ['deleted', 0];
        $CondArr[] = ['brand_id', $b1, 'in'];
        $CondArr[] = ['lang_id', $_SESSION['_Lang_']];
        $AllBrand = $BrandClass->get_all($CondArr, [], ['ordr', 'desc'], [0, 16]);
$countb = ($AllBrand ? count($AllBrand) : 0);
if($countb > 3){
        if($AllBrand){
            $ImgIDArr = [];
            foreach($AllBrand as $mll){
                $ImgIDArr[] = $mll->img_id;
            }

            $ImgArr = $FilesClass->get_by_id_all($ImgIDArr, 3);
        }

        if($AllBrand){

?>




<style>
    .brandcat .product-card__image {
    max-height: 126px;
}
.brandcat .product-card__image a{
    background-image: unset;
}
.block-brands__item-link {
    padding: 12px 60px;}
    .block.block-products-carousel {
    padding-right: 0;
}.block-split__row.row.no-gutters .block {
    padding-left: 0;
}
.block-split__row.row.no-gutters .block {
    padding-left: 5px;
}
</style>

<?php
       if($ProInfo && $ProInfo->parentid != 0){
?>

<div class="block block-products-carousel" data-layout="horizontalbr">
    <div class="container">


      
                <div class="block-products-carousel__carousel">
                    <div class="block-products-carousel__carousel-loader"></div>
                    <div class="owl-carousel">
                        <?php
        foreach($AllBrand as $b){
/*    $CondPr = [];
    $CondPr[] = ['confirm', 1];
    $CondPr[] = ['product_id', $b->brand_id, 'in'];
    $PriceList = $ProdRelClass->get_all_price($CondPr, ['product_id', 'asc', 'prop', 'desc']);
    //var_dump($PriceList);
    if($PriceList){
        $PrID5 = [];
        foreach($PriceList as $prl){
            $PrID5[] = $prl->product_id;
        }
           */ 
            
            
            
        if(isset($b->brand_id) && (int)$b->brand_id){
       /* $CondArr[] = ['brand_id', $b->brand_id];
        $CondArr[] = ['display', 1];
        $CondArr[] = ['deleted', 0];*/
       // $AllResult2 = $ProductClass->get_all($CondArr/*, ['product`.`product_id'], [], [], $GroupBy, $Tbl*/);
          $AllResult2 = $ths->GetData($ths->query("select * from `product` where `display`=1 and `deleted`=0 and `brand_id` = '".$b->brand_id."';"));
        //var_dump(count($AllResult2));
        $countpr = (($AllResult2 == 1) ? 0 : count($AllResult2));
        //var_dump($b->brand_id);
                 if(count($AllResult2) > 0){
             
        
            if($b->img_id && isset($ImgArr[$b->img_id])){
                $ImgUrl = $ImgArr[$b->img_id]['path2'];//dirname($ImgArr[$b->img_id]['path2']).'/'.$ImgArr[$b->img_id]['fileid'].'_thumb_'.$ImgArr[$b->img_id]['filename'];
            }else{
                $ImgUrl = $conf->BaseRoot2.'MyFile/Slider/none.jpg';
            }
?>
                                <div class="block-products-carousel__column">
                           
                            <div class="block-products-carousel__cell">
                                <div class="product-card brandcat">
                       
                                   
                                        <li class="block-brands__itemo">
                                          <a href="<?=$conf->BaseRoot2.'category/'.$CatID.'/1/'.$ths->UrlFriendly($b->title).'?BrandID='.$b->brand_id?>" class="block-brands__item-link" title="<?='برند '.$b->title?>">

                    <img loading="lazy" data-src="<?=$ImgUrl?>" alt="<?=$b->title?>">
                    <span class="block-brands__item-name">
                        <?=$b->title?>
                    </span>
                </a></li>

                                </div>
                            </div>
                            
                            
                            
                                </div>
                                
                                <?php
        }}}
?>
                     
                    </div>
                </div>
    

    </div>
</div> <?php } ?>
<div class="block-space block-space--layout--divider-nlo"></div>
<?php
    }
    }
    }
?>



      

<?php

if($ProInfo && $ProInfo->parentid == 0){ ?>
        
        
     <div class="o-box c-filter-shortcut"><div class="c-filter-shortcut__list-container"><div class="c-filter-shortcut__list-title">دسته‌بندی‌ها</div><div class="c-filter-shortcut__list c-filter-shortcut__list--category">



<ul id="results" class="categories-list__body">

                            <?php
                                if($CatID){
                        $ThisCat = $CatClass->get_by_id($CatID);
                    }
                    $CatChild = $CatClass->get_all([['parentid', (($CatID && isset($ThisCat) && $ThisCat) ? $ThisCat->catid : 0)], ['deleted', 0], ['lang_id', /*$_SESSION['_Lang_']*/ 1]]);


                    if(isset($CatChild) && $CatChild){
                        $FamilyCat = $CatClass->get_all([['parentid', ((isset($ThisCat) && $ThisCat) ? $ThisCat->parentid : 0)], ['deleted', 0], ['lang_id', /*$_SESSION['_Lang_']*/ 1]]);
                        $CurrFather = $CatID;

                    }else{
                        $ThisCat = $CatClass->get_by_id((isset($ThisCat) ? $ThisCat->parentid : 0));
                        $CatChild = $CatClass->get_all([['parentid', (isset($ThisCat->catid) ? $ThisCat->catid : 0)], ['deleted', 0], ['lang_id', /*$_SESSION['_Lang_']*/ 1]]);
                        $FamilyCat = $CatClass->get_all([['parentid', (isset($ThisCat->parentid) ? $ThisCat->parentid : 0)], ['deleted', 0], ['lang_id', /*$_SESSION['_Lang_']*/ 1]]);
                        $CurrFather = isset($ThisCat->catid) ? $ThisCat->catid : 0;
                    }


                                            if($CatChild){
                                    foreach($CatChild as $ch){
                                        if($ch->img_id){
                                                $Img = $FilesClass->get_by_id($ch->img_id, 2);
                                                $ImgUrl = $Img['path2'];
                                            }
                                            else{
                                                $ImgUrl = $conf->BaseRoot2.'/MyFile/Adv/none.jpg';
                                            }
                            ?>
                                        <li class="c-filter-shortcut__category-item js-shortcut-category">
                                            <a href="<?=$conf->BaseRoot2.($IsSurprise ? 'surprise' : 'category').'/'.$ch->catid.'/'.$ths->UrlFriendly($ch->title)?>">
                                                <img src="<?=$ImgUrl?>" alt="<?=$ch->title?>">
                                                <div class="c-filter-shortcut__category-title">
                                                    <?=$ch->title?>
                                                </div>
                                            </a>
                                            <!--<div class="categories-list__item-products">
                                                <?php
                                                    $PrCond = [];
                                                    $PrCond[] = ['display', 1];
                                                    $PrCond[] = ['deleted', 0];
                                                    $PrCond[] = ['lang_id', 1];
                                                    $PrCond[] = ['product`.`product_id`=`product_price`.`product_id` and `product_price`.`confirm', 1];
                                                    $PrCond[] = ['cat_id', $CatClass->get_all_child($ch->catid), 'in'];
                                                    $prd = $ProductClass->get_all($PrCond, ['product`.`product_id'], [], [], 'product.product_id', 'product_price');

                                                    if($prd){
                                                        echo count($prd);
                                                    }else{
                                                        echo 'بدون';
                                                    }
                                                ?>
                                                محصول
                                            </div>-->
                                        </li>
                                        
                                        
                                        <li class="categories-list__divider"></li>
                         <?php
                                    }
                                }
                            ?>

                            
                       <div id="results-show-more" class="c-filter-shortcut__category-item c-filter-shortcut__category-item--show-more js-show-more-categories"><span class="c-filter-shortcut__show-more-count"><span id="chide"></span> +</span><span class="c-filter-shortcut__show-more-text">دسته‌بندی دیگر</span></div>

                        </ul>





</div></div></div>
    <?php } ?>



<script>
     var limit = 6;
var per_page = 20;
var numberlist = $("#results li.js-shortcut-category").length;
var hidelist = numberlist - limit;
document.getElementById("chide").innerHTML = hidelist;
if(jQuery('#results > li.js-shortcut-category').length == 7){
    limit = 7;
    jQuery('#results-show-more').hide();
}
jQuery('#results > li.js-shortcut-category:gt('+(limit-1)+')').hide();

if(jQuery('#results > li.js-shortcut-category').length == 7){
    limit = 7;
    jQuery('#results-show-more').hide();
}
if (jQuery('#results > li.js-shortcut-category').length <= limit) {
    jQuery('#results-show-more').hide();
}

jQuery('#results-show-more').bind('click', function(event){
    event.preventDefault();
    limit += per_page;
    jQuery('#results > li.js-shortcut-category:lt('+(limit)+')').show();
    if (jQuery('#results > li.js-shortcut-category').length <= limit) {
        jQuery(this).hide();
    }
});
</script>

<script>
    /*!
 * Theia Sticky Sidebar v1.7.0
 * https://github.com/WeCodePixels/theia-sticky-sidebar
 *
 * Glues your website's sidebars, making them permanently visible while scrolling.
 *
 * Copyright 2013-2016 WeCodePixels and other contributors
 * Released under the MIT license
 */

(function ($) {
    $.fn.theiaStickySidebar = function (options) {
        var defaults = {
            'containerSelector': '',
            'additionalMarginTop': 0,
            'additionalMarginBottom': 0,
            'updateSidebarHeight': true,
            'minWidth': 0,
            'disableOnResponsiveLayouts': true,
            'sidebarBehavior': 'modern',
            'defaultPosition': 'relative',
            'namespace': 'TSS'
        };
        options = $.extend(defaults, options);

        // Validate options
        options.additionalMarginTop = parseInt(options.additionalMarginTop) || 0;
        options.additionalMarginBottom = parseInt(options.additionalMarginBottom) || 0;

        tryInitOrHookIntoEvents(options, this);

        // Try doing init, otherwise hook into window.resize and document.scroll and try again then.
        function tryInitOrHookIntoEvents(options, $that) {
            var success = tryInit(options, $that);

            if (!success) {
                console.log('TSS: Body width smaller than options.minWidth. Init is delayed.');

                $(document).on('scroll.' + options.namespace, function (options, $that) {
                    return function (evt) {
                        var success = tryInit(options, $that);

                        if (success) {
                            $(this).unbind(evt);
                        }
                    };
                }(options, $that));
                $(window).on('resize.' + options.namespace, function (options, $that) {
                    return function (evt) {
                        var success = tryInit(options, $that);

                        if (success) {
                            $(this).unbind(evt);
                        }
                    };
                }(options, $that))
            }
        }

        // Try doing init if proper conditions are met.
        function tryInit(options, $that) {
            if (options.initialized === true) {
                return true;
            }

            if ($('body').width() < options.minWidth) {
                return false;
            }

            init(options, $that);

            return true;
        }

        // Init the sticky sidebar(s).
        function init(options, $that) {
            options.initialized = true;

            // Add CSS
            var existingStylesheet = $('#theia-sticky-sidebar-stylesheet-' + options.namespace);
            if (existingStylesheet.length === 0) {
                $('head').append($('<style id="theia-sticky-sidebar-stylesheet-' + options.namespace + '">.theiaStickySidebar:after {content: ""; display: table; clear: both;}</style>'));
            }

            $that.each(function () {
                var o = {};

                o.sidebar = $(this);

                // Save options
                o.options = options || {};

                // Get container
                o.container = $(o.options.containerSelector);
                if (o.container.length == 0) {
                    o.container = o.sidebar.parent();
                }

                // Create sticky sidebar
                o.sidebar.parents().css('-webkit-transform', 'none'); // Fix for WebKit bug - https://code.google.com/p/chromium/issues/detail?id=20574
                o.sidebar.css({
                    'position': o.options.defaultPosition,
                    'overflow': 'visible',
                    // The "box-sizing" must be set to "content-box" because we set a fixed height to this element when the sticky sidebar has a fixed position.
                    '-webkit-box-sizing': 'border-box',
                    '-moz-box-sizing': 'border-box',
                    'box-sizing': 'border-box'
                });

                // Get the sticky sidebar element. If none has been found, then create one.
                o.stickySidebar = o.sidebar.find('.theiaStickySidebar');
                if (o.stickySidebar.length == 0) {
                    // Remove <script> tags, otherwise they will be run again when added to the stickySidebar.
                    var javaScriptMIMETypes = /(?:text|application)\/(?:x-)?(?:javascript|ecmascript)/i;
                    o.sidebar.find('script').filter(function (index, script) {
                        return script.type.length === 0 || script.type.match(javaScriptMIMETypes);
                    }).remove();

                    o.stickySidebar = $('<div>').addClass('theiaStickySidebar').append(o.sidebar.children());
                    o.sidebar.append(o.stickySidebar);
                }

                // Get existing top and bottom margins and paddings
                o.marginBottom = parseInt(o.sidebar.css('margin-bottom'));
                o.paddingTop = parseInt(o.sidebar.css('padding-top'));
                o.paddingBottom = parseInt(o.sidebar.css('padding-bottom'));

                // Add a temporary padding rule to check for collapsable margins.
                var collapsedTopHeight = o.stickySidebar.offset().top;
                var collapsedBottomHeight = o.stickySidebar.outerHeight();
                o.stickySidebar.css('padding-top', 1);
                o.stickySidebar.css('padding-bottom', 1);
                collapsedTopHeight -= o.stickySidebar.offset().top;
                collapsedBottomHeight = o.stickySidebar.outerHeight() - collapsedBottomHeight - collapsedTopHeight;
                if (collapsedTopHeight == 0) {
                    o.stickySidebar.css('padding-top', 0);
                    o.stickySidebarPaddingTop = 0;
                }
                else {
                    o.stickySidebarPaddingTop = 1;
                }

                if (collapsedBottomHeight == 0) {
                    o.stickySidebar.css('padding-bottom', 0);
                    o.stickySidebarPaddingBottom = 0;
                }
                else {
                    o.stickySidebarPaddingBottom = 1;
                }

                // We use this to know whether the user is scrolling up or down.
                o.previousScrollTop = null;

                // Scroll top (value) when the sidebar has fixed position.
                o.fixedScrollTop = 0;

                // Set sidebar to default values.
                resetSidebar();

                o.onScroll = function (o) {
                    // Stop if the sidebar isn't visible.
                    if (!o.stickySidebar.is(":visible")) {
                        return;
                    }

                    // Stop if the window is too small.
                    if ($('body').width() < o.options.minWidth) {
                        resetSidebar();
                        return;
                    }

                    // Stop if the sidebar width is larger than the container width (e.g. the theme is responsive and the sidebar is now below the content)
                    if (o.options.disableOnResponsiveLayouts) {
                        var sidebarWidth = o.sidebar.outerWidth(o.sidebar.css('float') == 'none');

                        if (sidebarWidth + 50 > o.container.width()) {
                            resetSidebar();
                            return;
                        }
                    }

                    var scrollTop = $(document).scrollTop();
                    var position = 'static';

                    // If the user has scrolled down enough for the sidebar to be clipped at the top, then we can consider changing its position.
                    if (scrollTop >= o.sidebar.offset().top + (o.paddingTop - o.options.additionalMarginTop)) {
                        // The top and bottom offsets, used in various calculations.
                        var offsetTop = o.paddingTop + options.additionalMarginTop;
                        var offsetBottom = o.paddingBottom + o.marginBottom + options.additionalMarginBottom;

                        // All top and bottom positions are relative to the window, not to the parent elemnts.
                        var containerTop = o.sidebar.offset().top;
                        var containerBottom = o.sidebar.offset().top + getClearedHeight(o.container);

                        // The top and bottom offsets relative to the window screen top (zero) and bottom (window height).
                        var windowOffsetTop = 0 + options.additionalMarginTop;
                        var windowOffsetBottom;

                        var sidebarSmallerThanWindow = (o.stickySidebar.outerHeight() + offsetTop + offsetBottom) < $(window).height();
                        if (sidebarSmallerThanWindow) {
                            windowOffsetBottom = windowOffsetTop + o.stickySidebar.outerHeight();
                        }
                        else {
                            windowOffsetBottom = $(window).height() - o.marginBottom - o.paddingBottom - options.additionalMarginBottom;
                        }

                        var staticLimitTop = containerTop - scrollTop + o.paddingTop;
                        var staticLimitBottom = containerBottom - scrollTop - o.paddingBottom - o.marginBottom;

                        var top = o.stickySidebar.offset().top - scrollTop;
                        var scrollTopDiff = o.previousScrollTop - scrollTop;

                        // If the sidebar position is fixed, then it won't move up or down by itself. So, we manually adjust the top coordinate.
                        if (o.stickySidebar.css('position') == 'fixed') {
                            if (o.options.sidebarBehavior == 'modern') {
                                top += scrollTopDiff;
                            }
                        }

                        if (o.options.sidebarBehavior == 'stick-to-top') {
                            top = options.additionalMarginTop;
                        }

                        if (o.options.sidebarBehavior == 'stick-to-bottom') {
                            top = windowOffsetBottom - o.stickySidebar.outerHeight();
                        }

                        if (scrollTopDiff > 0) { // If the user is scrolling up.
                            top = Math.min(top, windowOffsetTop);
                        }
                        else { // If the user is scrolling down.
                            top = Math.max(top, windowOffsetBottom - o.stickySidebar.outerHeight());
                        }

                        top = Math.max(top, staticLimitTop);

                        top = Math.min(top, staticLimitBottom - o.stickySidebar.outerHeight());

                        // If the sidebar is the same height as the container, we won't use fixed positioning.
                        var sidebarSameHeightAsContainer = o.container.height() == o.stickySidebar.outerHeight();

                        if (!sidebarSameHeightAsContainer && top == windowOffsetTop) {
                            position = 'fixed';
                        }
                        else if (!sidebarSameHeightAsContainer && top == windowOffsetBottom - o.stickySidebar.outerHeight()) {
                            position = 'fixed';
                        }
                        else if (scrollTop + top - o.sidebar.offset().top - o.paddingTop <= options.additionalMarginTop) {
                            // Stuck to the top of the page. No special behavior.
                            position = 'static';
                        }
                        else {
                            // Stuck to the bottom of the page.
                            position = 'absolute';
                        }
                    }

                    /*
                     * Performance notice: It's OK to set these CSS values at each resize/scroll, even if they don't change.
                     * It's way slower to first check if the values have changed.
                     */
                    if (position == 'fixed') {
                        var scrollLeft = $(document).scrollLeft();

                        o.stickySidebar.css({
                            'position': 'fixed',
                            'width': getWidthForObject(o.stickySidebar) + 'px',
                            'transform': 'translateY(' + top + 'px)',
                            'left': (o.sidebar.offset().left + parseInt(o.sidebar.css('padding-left')) - scrollLeft) + 'px',
                            'top': '0px'
                        });
                    }
                    else if (position == 'absolute') {
                        var css = {};

                        if (o.stickySidebar.css('position') != 'absolute') {
                            css.position = 'absolute';
                            css.transform = 'translateY(' + (scrollTop + top - o.sidebar.offset().top - o.stickySidebarPaddingTop - o.stickySidebarPaddingBottom) + 'px)';
                            css.top = '0px';
                        }

                        css.width = getWidthForObject(o.stickySidebar) + 'px';
                        css.left = '';

                        o.stickySidebar.css(css);
                    }
                    else if (position == 'static') {
                        resetSidebar();
                    }

                    if (position != 'static') {
                        if (o.options.updateSidebarHeight == true) {
                            o.sidebar.css({
                                'min-height': o.stickySidebar.outerHeight() + o.stickySidebar.offset().top - o.sidebar.offset().top + o.paddingBottom
                            });
                        }
                    }

                    o.previousScrollTop = scrollTop;
                };

                // Initialize the sidebar's position.
                o.onScroll(o);

                // Recalculate the sidebar's position on every scroll and resize.
                $(document).on('scroll.' + o.options.namespace, function (o) {
                    return function () {
                        o.onScroll(o);
                    };
                }(o));
                $(window).on('resize.' + o.options.namespace, function (o) {
                    return function () {
                        o.stickySidebar.css({'position': 'static'});
                        o.onScroll(o);
                    };
                }(o));

                // Recalculate the sidebar's position every time the sidebar changes its size.
                if (typeof ResizeSensor !== 'undefined') {
                    new ResizeSensor(o.stickySidebar[0], function (o) {
                        return function () {
                            o.onScroll(o);
                        };
                    }(o));
                }

                // Reset the sidebar to its default state
                function resetSidebar() {
                    o.fixedScrollTop = 0;
                    o.sidebar.css({
                        'min-height': '1px'
                    });
                    o.stickySidebar.css({
                        'position': 'static',
                        'width': '',
                        'transform': 'none'
                    });
                }

                // Get the height of a div as if its floated children were cleared. Note that this function fails if the floats are more than one level deep.
                function getClearedHeight(e) {
                    var height = e.height();

                    e.children().each(function () {
                        height = Math.max(height, $(this).height());
                    });

                    return height;
                }
            });
        }

        function getWidthForObject(object) {
            var width;

            try {
                width = object[0].getBoundingClientRect().width;
            }
            catch (err) {
            }

            if (typeof width === "undefined") {
                width = object.width();
            }

            return width;
        }

        return this;
    }
})(jQuery);

//# sourceMappingURL=maps/theia-sticky-sidebar.js.map

    
</script>
<div class="block-split__row row no-gutters">
            
            
            
            
            <div id="side1" class="rightSidebar block-split__item block-split__item-sidebar col-auto">
                <div id="sticky-sidebar" class="sidebar sidebar--offcanvas--mobile theiaStickySidebar">
                    <div class="sidebar__backdrop"></div>
                    <div class="sidebar__body">
                        <div class="sidebar__header">
                            <div class="sidebar__title">فیلتر</div>
                            <button class="sidebar__close" type="button">
                                <svg width="12" height="12">
                                    <path d="M10.8,10.8L10.8,10.8c-0.4,0.4-1,0.4-1.4,0L6,7.4l-3.4,3.4c-0.4,0.4-1,0.4-1.4,0l0,0c-0.4-0.4-0.4-1,0-1.4L4.6,6L1.2,2.6
	c-0.4-0.4-0.4-1,0-1.4l0,0c0.4-0.4,1-0.4,1.4,0L6,4.6l3.4-3.4c0.4-0.4,1-0.4,1.4,0l0,0c0.4,0.4,0.4,1,0,1.4L7.4,6l3.4,3.4
	C11.2,9.8,11.2,10.4,10.8,10.8z"/>
                                </svg>
                            </button>
                        </div>
                        <div class="sidebar__content">
                            <div class="widget widget-filters widget-filters--offcanvas--mobile MySideFilter" data-collapse data-collapse-opened-class="filter--opened"></div>
                            <div class="card widget widget-products d-none d-lg-block MyLatest"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="block-split__item block-split__item-content col-auto MyProductList"></div>
        </div>
        <div class="block-space block-space--layout--before-footer"></div>
    </div>
</div>

<script>
			$(document).ready(function() {
				$('.leftSidebar, .content2, .rightSidebar')
					.theiaStickySidebar({
						additionalMarginTop: 130
					});
			});
		</script>
        <input type="hidden" id="SrchItem" name="SrchItem" value="<?=(isset($_REQUEST['SrchItem']) ? $_REQUEST['SrchItem'] : (isset($_REQUEST['SrchItem2']) ? $_REQUEST['SrchItem2'] : ''))?>">
        <input type="hidden" id="MyCatID" name="MyCatID" value="<?=$CatID?>">
        <input type="hidden" id="MyBrandID" name="MyBrandID" value="<?=$BrandID?>">
        <?php
            $SrchType = 'Cat';
            if(isset($_REQUEST['Surprise']) && $_REQUEST['Surprise'] === '1'){
                $SrchType = 'Surprise';
            }elseif(isset($_REQUEST['SrchItem'])){
                $SrchType = 'Search';
            }
        ?>
        <input type="hidden" id="MyListType" name="MyListType" value="<?=$SrchType?>">
        <input type="hidden" id="MyCatTitle" name="MyCatTitle" value="<?=(isset($CatID) ? $CatClass->get_title_by_id($CatID) : '')?>">
        <input type="hidden" id="CurrPage" name="CurrPage" value="<?=((isset($_REQUEST['CurrPage']) && (int)($ths->MakeSecurParam($_REQUEST['CurrPage']))) ? (int)($ths->MakeSecurParam($_REQUEST['CurrPage'])) : 1)?>">
        <input type="hidden" id="BrandID" name="BrandID" value="<?=(isset($_REQUEST['BrandID']) ? $BrandID : '')?>">
        <input type="hidden" id="order_by" name="order_by" value="<?=((isset($_REQUEST['order_by']) && in_array($_REQUEST['order_by'], ['newest', 'price_down', 'price_up', 'most_visit',  'most_score'])) ? $ths->MakeSecurParam($_REQUEST['order_by']) : '')?>">
        <input type="hidden" id="per_page" name="per_page" value="<?=(isset($_REQUEST['per_page']) ? (int)($ths->MakeSecurParam($_REQUEST['per_page'])) : '')?>">
        <input type="hidden" id="price_from" name="price_from" value="<?=(isset($_REQUEST['price_from']) ? (float)($ths->MakeSecurParam($_REQUEST['price_from'])) : '')?>">
        <input type="hidden" id="price_to" name="price_to" value="<?=(isset($_REQUEST['price_to']) ? (float)($ths->MakeSecurParam($_REQUEST['price_to'])) : '')?>">
        <input type="hidden" id="Surprise" name="Surprise" value="<?=((isset($_REQUEST['Surprise']) && $_REQUEST['Surprise'] === '1') ? 1 : '')?>">


    <?php
    }
?>
