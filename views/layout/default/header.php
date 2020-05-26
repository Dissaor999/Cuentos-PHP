<!DOCTYPE html>
<html menu_url="<?= MENU_URL?>" lang="<?= @$_layoutParams['lang'] ?>">
<head>


    <?php
    $actual_link = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

   $title= (isset($this->titulo)) ? $this->titulo : APP_NAME;;
   $desc= (isset($this->description)) ? $this->description : "Cuentos personalizados para niños de 0 a 9 años, el mejor regalo para cumpleaños, navidades, comuniones, bautizos, ocasiones especiales";
   $img_social = (isset($this->image_socials)) ? $this->image_socials : $_layoutParams['public_img'].'share_img.jpg';
    ?>

    <!-- PAGE TITLE -->
    <title><?= $title ?></title>
    <meta name="title" content="<?= $title ?>" />

    <meta name="description" content="<?= $desc ?>">



    <!-- Google Authorship and Publisher Markup -->
    <link rel="author" href=" https://plus.google.com/[Google+_Profile]/posts"/>
    <link rel="publisher" href="https://plus.google.com/[Google+_Page_Profile]"/>

    <!-- Schema.org markup for Google+ -->
    <meta itemprop="name" content="<?=$title?>">
    <meta itemprop="description" content="<?=$desc?>">
    <meta itemprop="image" content=" <?= (isset($this->image_socials)) ? $this->image_socials : $_layoutParams['public_img'].'share_img.jpg'; ?>">

    <!-- Twitter Card data -->
    <meta name="twitter:title" content="<?=$title?>">
    <meta name="twitter:description" content="<?=$desc?>">
    <!-- Twitter summary card with large image must be at least 280x150px -->
    <meta name="twitter:image:src" content=" <?=$actual_link?>">

    <!-- Open Graph data -->
    <meta property="og:title" content="<?=$title?>" />
    <meta property="og:type" content="article" />
    <meta property="og:url" content="<?=$actual_link?>" />
    <meta property="og:image" content=" <?= (isset($this->image_socials)) ? $this->image_socials : $_layoutParams['public_img'].'share_img.jpg'; ?>" />
    <meta property="og:description" content="<?=$desc?>" />
    <meta property="og:site_name" content="<?=$title?>" />




   <meta name="keywords" content="cuentos personalizados para niños" />
    <meta name="revisit-after" content="7 days" />
    <meta name="revisit" content="7 days" />
    <meta name="robot" content="index,follow" />
    <meta name="robots" content="all" />
    <meta name="distribution" content="global" />
    <meta http-equiv="pragma" content="no-cache" />
    <meta name="rating" content="general" />
    <meta name="language" content=""<?= @$_layoutParams['lang'] ?>" />



    <link rel="apple-touch-icon" sizes="180x180" href="<?=$_layoutParams['template_img']?>favicon/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="<?=$_layoutParams['template_img']?>favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="<?=$_layoutParams['template_img']?>favicon/favicon-16x16.png">
    <link rel="manifest" href="<?=$_layoutParams['template_img']?>favicon/site.webmanifest">
    <link rel="mask-icon" href="<?=$_layoutParams['template_img']?>favicon/safari-pinned-tab.svg" color="#5bbad5">
    <meta name="msapplication-TileColor" content="#da532c">
    <meta name="theme-color" content="#ffffff">

    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <meta name="format-detection" content="telephone=no" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">

    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet" type="text/css">
    <link href="<?=$_layoutParams['template_js']?>plugins/ionicons-2.0.1/ionicons.css" rel="stylesheet" type="text/css">
    <link href="<?=$_layoutParams['template_js']?>plugins/font-awesome-4.7.0/css/font-awesome.min.css" rel="stylesheet" type="text/css">
    <link href="<?=$_layoutParams['template_js']?>plugins/OwlCarousel2-2.2.1/owl.carousel.css" rel="stylesheet" type="text/css">
    <link href="<?=$_layoutParams['template_js']?>plugins/OwlCarousel2-2.2.1/owl.theme.default.css" rel="stylesheet" type="text/css">
    <link href="<?=$_layoutParams['template_js']?>plugins/OwlCarousel2-2.2.1/animate.css" rel="stylesheet" type="text/css">

    <!--
     <link href="<?=$_layoutParams['template_js']?>plugins/magnific-popup/magnific-popup.css" rel="stylesheet" type="text/css">

    <link href="<?=$_layoutParams['template_js']?>plugins/parallaxslider/parallaxslider.css" rel="stylesheet" type="text/css">

    <link href="<?=$_layoutParams['template_css']?>pignose.popup.css" rel="stylesheet" type="text/css">

    <link href="<?= $_layoutParams['template_js'] ?>plugins/Swiper-master/dist/css/swiper.min.css" rel="stylesheet" type="text/css">
    -->

    <link href="<?=$_layoutParams['template_css']?>main_styles.css" rel="stylesheet" type="text/css">
    <link href="<?=$_layoutParams['template_css']?>responsive.css" rel="stylesheet" type="text/css">


    <script src="<?=$_layoutParams['template_js']?>snap.svg-min.js"></script>

    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Gloria+Hallelujah" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">

    <script type="text/javascript">

        var MENU_URL = "<?= MENU_URL ?>";
        var BASE_URL = "<?= BASE_URL ?>";
    </script>

    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-111974104-1"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', 'UA-111974104-1');
    </script>


    <?php if (isset($this->_css) && sizeof($this->_css) > 0) foreach ($this->_css as $k => $v) echo '<link href="' . $v . '"  rel="stylesheet" type="text/css">'; ?>

</head>
<body>


<div class="super_container">
<?php
global $me;
/*
if($me->has_access('cart')) {
    if($this->_controlador!='cart') {
    ?>
    <div class="cd-cart-container empty">
        <a href="#0" class="cd-cart-trigger">
            Cart
            <ul class="count"> <!-- cart items count -->
                <li>0</li>
                <li>0</li>
            </ul> <!-- .count -->
        </a>

        <div class="cd-cart">
            <div class="wrapper">
                <header>
                    <h2><i class="fa fa-cart"></i> Carrito</h2>
                    <span class="undo">Artículo borrado. <a href="#0">Deshacer</a></span>
                </header>

                <div class="body">
                    <ul>
                        <!-- products added to the cart will be inserted here using JavaScript -->
                    </ul>
                </div>

                <footer>
                    <a href="<?= MENU_URL ?>cart" class="checkout">Realizar Pedido - <span>0</span>€</a>
                </footer>
            </div>
        </div> <!-- .cd-cart -->
    </div> <!-- cd-cart-container -->
<?php
    }
}*/

?>
<style>
    .class{background:white;}
    .top-bar {
        background: #2a90b7;
        color: White;
        font-size: 14px;
        padding: 10px 0;
        font-family:Roboto;
    }
    .top-bar a{display:inline-block; margin-right:16px; color:white;}

    .social-topbar {margin-bottom:0; display:inline-block; font-size:11px;}
    .social-topbar li{display:inline-block;}
    .social-topbar li a{margin-right:5px; width:22px;height:22px; text-align: center; line-height:1.9em;border:1px solid white; border-radius:100%; display:inline-block;}

    .cesta a {background:orange; color:white !important; border-radius:20px; line-height:1.4em; vertical-align: middle; padding:5px 12px !important;}
    .cesta a i {top:3px; position:relative; font-size:20px;}
    .cesta .items {
        position:absolute;
        top:-3px;right:-3px;
        font-size:12px;
        font-family:Dosis;
        background:#0a6aa1;
        border-radius:100%;
        width:17px;
        height:17px;
        line-height:1.4em;
        text-align:center;
    }

    /* Menu Button */
    .menu-button {
        position: fixed;
        z-index: 99999;

        padding: 0;
        width: 2.5em;
        height: 2.25em;
        border: none;
        text-indent: 2.5em;
        font-size: 1.5em;
        color: transparent;
        background: transparent;
        right: 0;
        margin-top: 0.4%;
    }

    .menu-button::before {
        position: absolute;
        top: 0.5em;
        right: 0.5em;
        bottom: 0.5em;
        left: 0.5em;
        background: linear-gradient(#373a47 20%, transparent 20%, transparent 40%, #373a47 40%, #373a47 60%, transparent 60%, transparent 80%, #373a47 80%);
        content: '';
    }

    .menu-button:hover {
        opacity: 0.6;
    }

    /* Close Button */
    .close-button {
        width: 16px;
        height: 16px;
        position: absolute;
        right: 1em;
        top: 1em;
        overflow: hidden;
        text-indent: 16px;
        border: none;
        z-index: 1001;
        background: transparent;
        color: transparent;
    }

    .close-button::before,
    .close-button::after {
        content: '';
        position: absolute;
        width: 2px;
        height: 100%;
        top: 0;
        left: 50%;
        background: #fff;
    }

    .close-button::before {
        -webkit-transform: rotate(45deg);
        transform: rotate(45deg);
    }

    .close-button::after {
        -webkit-transform: rotate(-45deg);
        transform: rotate(-45deg);
    }

    /* Menu */
    .menu-wrap {
        position: absolute;
        z-index: 1001;
        width: 280px;
        height: 100%;
        font-size: 1.15em;
        -webkit-transform: translate3d(-280px,0,0);
        transform: translate3d(-280px,0,0);
        -webkit-transition: -webkit-transform 0.4s;
        transition: transform 0.4s;
    }

    .menu {
        position: relative;
        z-index: 1000;
        padding: 3em 1em 0;
    }

    .menu,
    .close-button {
        opacity: 0;
        -webkit-transform: translate3d(-160px,0,0);
        transform: translate3d(-160px,0,0);
        -webkit-transition: opacity 0s 0.3s, -webkit-transform 0s 0.3s;
        transition: opacity 0s 0.3s, transform 0s 0.3s;
        -webkit-transition-timing-function: cubic-bezier(.17,.67,.1,1.27);
        transition-timing-function: cubic-bezier(.17,.67,.1,1.27);
    }

    .icon-list a {
        display: block;
        color:#fff;
        padding: 0.8em;
        font-family: 'Gloria Hallelujah', cursive;
    }

    .icon-list i {
        font-size: 1.5em;
        vertical-align: middle;
        color: #282a35;
    }

    .icon-list a span {
        margin-left: 10px;
        font-size: 0.85em;
        font-weight: 700;
        vertical-align: middle;
    }

    /* Morph Shape */
    .morph-shape {
        position: absolute;
        width: 100%;
        height: 100%;
        top: 0;
        right: 0;
        fill: #2a90b7;
    }

    /* Shown menu */
    .show-menu .menu-wrap,
    .show-menu .content::before {
        -webkit-transition-delay: 0s;
        transition-delay: 0s;
    }

    .show-menu .menu-wrap,
    .show-menu .menu,
    .show-menu .close-button,
    .show-menu .morph-shape,
    .show-menu .content::before {
        -webkit-transform: translate3d(0,0,0);
        transform: translate3d(0,0,0);
    }

    .show-menu .menu,
    .show-menu .close-button {
        opacity: 1;
        -webkit-transition: opacity 0.3s, -webkit-transform 0.3s;
        transition: opacity 0.3s, transform 0.3s;
        -webkit-transition-delay: 0.4s;
        transition-delay: 0.4s;
    }

    .show-menu .content::before {
        opacity: 1;
        -webkit-transition: opacity 0.4s;
        transition: opacity 0.4s;
    }

    @media screen and (min-width: 992px) {
        .menu-button{display:none !important ;}
    }
</style>

    <!-- HEADER AND MENU -->

    <header class="header">
        <div class="top-bar clearfix ">
                <div class="container">
                    <div class="col-sm-6 col-xs-8">
                        <a href="mailto:info@micuentomagico.com"><i class="fa fa-envelope-o"></i>  info@micuentomagico.com</a>
                    </div>
                    <div class="col-sm-6 col-xs-4 text-right">
                        <a class="hidden" href="#">Colaboradores</a>
                        <a class="hidden" href="#">Opiniones</a>
                        <ul class="social-topbar">
                            <li><a href="https://www.facebook.com/micuentomagicopersonalizado/" target="_BLANK"><i class="fa fa-facebook"></i></a></li>
                            <li><a href="https://www.instagram.com/micuentomagico/" target="_BLANK"><i class="fa fa-instagram"></i></a></li>
                            <li><a href="https://twitter.com/micuentomagico" target="_BLANK"><i class="fa fa-twitter"></i></a></li>
                        </ul>
                    </div>
                </div>
        </div>

        <div class="menu-wrap">
            <nav class="menu">
                <div class="icon-list">
                    <a href="<?=MENU_URL?>libros" class=" crea" data-scroll-to="#elige-aventura-home">Crea tu cuento</a>
                     <a href="<?=MENU_URL?>encargos">Encargos a medida</a>
                     <a href="<?=MENU_URL?>nosotros">Nosotros</a>
                    <a href="<?=MENU_URL?>blog">Blog</a>
                    <a href="<?=MENU_URL?>contacto">Contacto</a>
                    <a href="<?=MENU_URL?>cart" class=" cart-icon">CESTA <i class="material-icons">&#xE8CC;</i><span class="items"><?= sizeof(json_decode(@$_COOKIE['books'])) ?></span></a>

                </div>
            </nav>
            <button class="close-button" id="close-button">Close Menu</button>
            <div class="morph-shape" id="morph-shape" data-morph-open="M-7.312,0H15c0,0,66,113.339,66,399.5C81,664.006,15,800,15,800H-7.312V0z;M-7.312,0H100c0,0,0,113.839,0,400c0,264.506,0,400,0,400H-7.312V0z">
                <svg xmlns="http://www.w3.org/2000/svg" width="100%" height="100%" viewBox="0 0 100 800" preserveAspectRatio="none">
                    <path d="M-7.312,0H0c0,0,0,113.839,0,400c0,264.506,0,400,0,400h-7.312V0z"/>
                </svg>
            </div>
        </div>
        <button class="menu-button" id="open-button">Open Menu</button>


        <div class="container">


        <div class="header_content row">
            <div class="logo_container col-sm-3">
                <a class="logo" href="<?=MENU_URL?>">
                  <img src="<?=$_layoutParams['public_img']?>micuentomagico.png" style="    max-height: 83px;">
                </a>
            </div>
            <div class="home_nav col-sm-9 text-right">
                <ul>
                    <li class="link-braces"><a href="<?=MENU_URL?>libros" class=" crea" data-scroll-to="#elige-aventura-home">Crea tu cuento</a></li>
                    <li class="link-braces"><a href="<?=MENU_URL?>encargos">Encargos a medida</a></li>
                    <li class="link-braces"><a href="<?=MENU_URL?>nosotros">Nosotros</a></li>
                    <li class="link-braces"><a href="<?=MENU_URL?>blog">Blog</a></li>
                    <li class="link-braces"><a href="<?=MENU_URL?>contacto">Contacto</a></li>
                    <li class="cesta"><a href="<?=MENU_URL?>cart" class=" cart-icon">CESTA <i class="material-icons">&#xE8CC;</i><span class="items"><?= sizeof(json_decode(@$_COOKIE['books'])) ?></span></a></li>
                </ul>
            </div>
            </div>
        </div>


    </header>
