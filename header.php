    <?php
    // include DB connection for public pages if available
    if (file_exists(__DIR__ . '/Admin_SiteWorx/connection.php')) {
        require_once __DIR__ . '/Admin_SiteWorx/connection.php';
        // include site data helpers
        if (file_exists(__DIR__ . '/lib/site_data.php')) {
            require_once __DIR__ . '/lib/site_data.php';
        }
    }
    ?>
    <!DOCTYPE html>
<html lang="en">


<head>
    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-4TR00RY39W"></script>
    <link rel="canonical" href="<?php echo $canonical ?>"/>
    <script>
    window.dataLayer = window.dataLayer || [];

    function gtag() {
        dataLayer.push(arguments);
    }
    gtag('js', new Date());

    gtag('config', 'G-4TR00RY39W');
    </script>
    <!-- ====Favicons==== -->
    <link rel="shortcut icon" href="favicon.png" type="image/x-icon">
    <link rel="icon" href="favicon.png" type="image/x-icon">

    <meta charset="utf-8">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' | ':'Web Hosting|Hosting Services|Best Domain Hosting'; ?> </title>
    <!--<title>Web Hosting | Hosting Services |Best Domain Hosting </title>-->
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta
        content="<?php echo $Metadescription ?>"
        name="description">
    <meta
        content="<?php echo $Metadescription ?>"
        name="keywords">
    <meta content="" name="author">
    <meta name="copyright" content="Copyright, SiteWorx" />
    <meta name="language" content="english" />
    <meta name="revisit-after" content="daily" />
    <meta property="og:url" content="https://www.siteworx.in" />


    <!-- CSS Files
    ================================================== -->
    <link href="css/plugins.css" rel="stylesheet" type="text/css">
    <link href="css/style.css" rel="stylesheet" type="text/css">

    <!-- font icons -->
    <!--<link href="fonts/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">-->
    <link href="fonts/elegant_font/HTML_CSS/style.css" rel="stylesheet" type="text/css">
    <link href="fonts/et-line-font/style.css" rel="stylesheet" type="text/css">
    <link href= "https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"  rel="stylesheet" type="text/css">

    <!-- color scheme -->
    <link id="colors" href="css/colors/scheme-02.css" rel="stylesheet" type="text/css">
    <link href="css/coloring.css" rel="stylesheet" type="text/css">


    <!-- RS5.0 Stylesheet -->
    <link href="revolution/css/settings.css" rel="stylesheet" type="text/css">
    <link href="revolution/css/layers.css" rel="stylesheet" type="text/css">
    <link href="revolution/css/navigation.css" rel="stylesheet" type="text/css">
    <link href="css/rev-settings.css" rel="stylesheet" type="text/css">


    <!-- Google Analytics -->
    <script>
    (function(i, s, o, g, r, a, m) {
        i['GoogleAnalyticsObject'] = r;
        i[r] = i[r] || function() {
            (i[r].q = i[r].q || []).push(arguments)
        }, i[r].l = 1 * new Date();
        a = s.createElement(o),
            m = s.getElementsByTagName(o)[0];
        a.async = 1;
        a.src = g;
        m.parentNode.insertBefore(a, m)
    })(window, document, 'script', 'https://www.google-analytics.com/analytics.js', 'ga');

    ga('create', 'G-78N8L2R36W', 'auto');
    ga('send', 'pageview');
    </script>
    <!-- End Google Analytics -->
    <meta name="google-site-verification" content="9C0uUH2qQAM78w-wom8G8bTIbQc6d-wPs12regn0H0A" />
</head>

<body>
    <div id="wrapper">
        <!-- header begin -->
        <header class="header-s1 has-topbar">
            <div id="topbar" class="text-white">
                <div class="container">
                    <div class="topbar-left">
                        <!-- <span class="topbar-widget">
                            <a href="#"><strong></strong></a>
                        </span>  -->
                    </div>

                    <div class="topbar-right">
                        
                        <div class="topbar-widget sm-hide "><a href="">
                           <i class=""></i></a></div>
                        <div class="topbar-widget sm-hide "><a href="mailto: info@siteworx.in "><i
                                    class="fa-solid fa-envelope"></i>info@siteworx.in </a></div>
                        <div class="topbar-widget sm-hide "><a href="https://siteworx.in/manage/clientarea.php">
                            <i class="fa fa-lock"></i>Client Area</a></div>
                    </div>
                    
                    
                    <div class="clearfix"></div>
                </div>
            </div>

            <div class="container">
                <div class="row">
                    <div class="col-md-12">

                        <div class="header-row">
                           <div class="header-col left pt-0 pb-0 pl-2 pr-2 mt-2 mb-1">
                                <!-- logo begin -->
                                <div id="logo">
                                    <a href="https://siteworx.in/"><img alt="siteworx" class="logo" style=" Max-width: 220px;"
                                            src="images/logofoot.png"> <img alt="siteworx" class="logo-2"
                                            style="    Max-width: 220px;" src="images/logo.png"></a>
                                </div>
                                <!-- logo close -->
                            </div>
                           
                            <div class="header-col mid">
                                <!-- mainmenu begin -->
                                <ul id="mainmenu">
                                    <!--<li><a href="https://siteworx.in/">Home </a></li>-->
                                    <li><a href="domain">Domains </a></li>
                                    <li><a href="#">Hosting</a>
                                        <ul>
                                             <li><a href="shared-hosting">Shared Hosting</a></li>
                                             <li><a href="hosting-reseller">Reseller Hosting</a></li>
                                        </ul>
                                    </li>
                                    
                                    <li><a href="#">Servers</a>
                                        <ul>
                                            <li><a href="#">Dedicated Server<span>New</span></a>
                                                <ul>
                                                    <li><a href="dedicated-server-india">India</a></li>
                                                    <li><a href="foreign-dedicated-server">Foreign</a></li>
                                                </ul>
                                            </li>
                                            <!--<li><a href="#">Hosting<span>5% OFF</span></a>-->
                                            <!--    <ul>-->
                                            <!--        <li><a href="shared-hosting">Shared Hosting</a></li>-->
                                            <!--        <li><a href="hosting-reseller">Reseller Hosting</a></li>-->
                                            <!--    </ul>-->
                                            <!--</li>-->
                                            <li><a href="#">VPS </a>
                                                <ul>
                                                    <li><a href="vps-server-india">India </a></li>
                                                    <li><a href="foreign-vps-server">Foreign </a></li>
                                                </ul>
                                            </li>
                                            <li><a href="#">Cloud Server</a>
                                                <ul>
                                                    <li><a href="cloud-server-india">India</a>
                                                    </li>
                                                    <li><a href="foreign-cloud-server">Foreign</a></li>
                                                </ul>
                                            </li>
                                            <!--<li><a href="linux-email-marketing-server-usa">Email Marketing Server</a></li>-->
                                            
                                        </ul>
                                    </li>
                                    <li><a href="email-marketing">Email Marketing</a></li>
                                    <li><a href="gsuite">Google Workspace </a></li>
                                    <li><a href="whm-cpanel">License</a></li>
                                    <li><a href="support">Support</a></li>
                                    <li><a href="about">About</a></li>
                                </ul>
                                <!--<div class="col-right">-->
                                <!--    <a class="btn-custom" href="https://siteworx.in/manage/clientarea.php"><i class="fa fa-lock"></i>-->
                                <!--        Client Area</a>-->
                                <!--</div>-->
                            </div>
                            <div class="clearfix"></div>
                        </div>
                        <!-- small button begin -->
                        <span id="menu-btn"></span>
                        <!-- small button close -->

                    </div>
                </div>
            </div>
        </header>