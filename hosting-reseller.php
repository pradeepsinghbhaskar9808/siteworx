 <?php 
     $pageTitle = "Best Hosting Providers in India,Cheap services and Domains";

   $canonical='https://www.siteworx.in/hosting-reseller"';
   
   $Metadescription="Find the perfect plan to fit your website's needs. High-performance servers, automatic backups, and a user-friendly control panel. Web Hosting,Hosting Services,Domain Hosting,Website Hosting,Shared Hosting,VPS Hosting ,Dedicated Server Hosting,Cloud Hosting,Managed Hosting,Reseller Hosting. Buy Now!";
  $metakeywords="Web hosting in India, Best web hosting in India, cheapest web hosting in india, web hosting company in india, best web hosting company in india, web hosting india, web hosting services in india, domain name in india, domain name, web hosting companies India, email hosting, best web hosting, cheap web hosting, Bulk Email Marketing Server, Best web development in india, web development in india, web design in india, Best SEO Service in India, Best ADS Service In india, Best Facebook ADS in india, Best web Development In Jaipur, Best Instagram ADS In India";
   include('header.php');
   
   // Load plans for Linux and Windows
   $linuxPlans = [];
   $windowsPlans = [];
   
   if (isset($pdo)) {
       try {
           $stmt = $pdo->prepare("SELECT * FROM hosting_plans WHERE category = :cat AND status='active' ORDER BY price_monthly ASC");
           
           // Get Linux plans
           $stmt->execute([':cat' => 'reseller-linux']);
           $linuxPlans = $stmt->fetchAll();
           
           // Get Windows plans
           $stmt->execute([':cat' => 'reseller-windows']);
           $windowsPlans = $stmt->fetchAll();
       } catch (Exception $e) {
           // fallback empty
           $linuxPlans = [];
           $windowsPlans = [];
       }
   }
   ?>  
        <!-- content begin -->
        <div class="no-bottom no-top" id="content">
            <div id="top"></div>
			  <section class="no-top no-bottom text-light" data-bgimage="url(images/background/server-cabinets-banner.jpg) center" data-stellar-background-ratio=".2">
                <div class="overlay-gradient t80">
                    <div class="center-y mt50">
                        <div class="container">
                            <div class="row align-items-center">
								<div class="col-lg-6 col-12 text-lg-left text-center mb-sm-30">
									<h1 style='color:white '>Reseller Hosting<span class="label">5% OFF </span></h1>
									<p class="lead">Start your own hosting business effortlessly with our reseller hosting plan, offering top-notch performance, reliability, and dedicated support for your success. </p>
									<div class="spacer-half"></div>
									<a class="btn-custom" href="#plans">See All Plans</a>
								</div>
								
								<div class="col-lg-3 offset-lg-2 col-4 offset-4 text-center">
									<img src="images/data-transfer-icon.gif" class="img-fluid" alt="" style="opacity: 0.5; " >
								</div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <!-- revolution slider begin -->
          
			<section id="plans" class="pb40">
                <div class="container">
                                    <div class="row">
                                        <div class="col-md-12 wow fadeInUp">
                            <div class="text-center">
                                <h1 style="color:#115256">Best Reseller Hosting Plans At Affordable Price</h1>
                                <h2><span class="uptitle id-color">Select Your</span> Reseller Hosting Plans</h2>
                                <div class="spacer-20"></div>
                            </div>
                            <div class="col-md-12">
                            <div class="switch-set text-center wow fadeInUp" data-wow-delay=".2s" style="margin-top: 40px;">
                                <div>Linux</div>
                                <div>
                                    <input id="sw-1" class="switch" type="checkbox" />
                                </div>
                                <div>Windows</div>
                                <div class="spacer-20"></div>
                            </div>
                         </div>
                        </div>
                        <div class="row" id="plans-container">
                                            <?php
                                            // Combine plans with platform indicator
                                            $allPlans = [];
                                            foreach ($linuxPlans as $plan) {
                                                $plan['platform'] = 'linux';
                                                $allPlans[] = $plan;
                                            }
                                            foreach ($windowsPlans as $plan) {
                                                $plan['platform'] = 'windows';
                                                $allPlans[] = $plan;
                                            }
                                            
                                            if (!empty($allPlans)) {
                                                $counter = 0;
                                                foreach ($allPlans as $plan) {
                                                    $counter++;
                                                    $specs = json_decode($plan['specs'], true);
                                                    $platformClass = ($plan['platform'] === 'linux') ? 'opt-1' : 'opt-2';
                                                    $displayStyle = ($plan['platform'] === 'linux') ? 'display: block;' : 'display: none;';
                                                    $isHOT = ($counter === 2) ? true : false;
                                                    
                                                    // Build order URL based on platform
                                                    $orderUrl = ($plan['platform'] === 'linux') 
                                                        ? "http://siteworx.in/manage/index.php?rp=/store/linux-reseller-hosting/" . urlencode($plan['slug'])
                                                        : "http://siteworx.in/manage/index.php?rp=/store/window-reseller-hosting/" . urlencode($plan['slug']);
                                                    ?>
                                                    <div class="col-lg-3 col-md-6 col-sm-12" data-platform="<?php echo htmlspecialchars($plan['platform']); ?>" style="<?php echo $displayStyle; ?>">
                                                        <div class="pricing-s1 mb30">
                                                            <?php if ($isHOT): ?>
                                                                <div class="ribbon">HOT</div>
                                                            <?php endif; ?>
                                                            <div class="top">
                                                                <h2><?php echo htmlspecialchars($plan['name']); ?></h2>
                                                                <p class="price">
                                                                    <span class="txt">Start from</span>
                                                                    <span class="currency"><?php echo htmlspecialchars($plan['currency']); ?></span>
                                                                    <span class="m"><?php echo number_format($plan['price_monthly'], 0); ?></span>
                                                                    <span class="month">p/mo</span>
                                                                </p>               
                                                            </div>
                                                            
                                                            <div class="bottom">
                                                                <ul>
                                                                    <?php if (isset($specs['storage'])): ?>
                                                                        <li><i class="fa fa-check-square"></i><?php echo htmlspecialchars($specs['storage']); ?> Storage</li>
                                                                    <?php endif; ?>
                                                                    <?php if (isset($specs['websites'])): ?>
                                                                        <li><i class="fa fa-check-square"></i><?php echo htmlspecialchars($specs['websites']); ?> Websites</li>
                                                                    <?php endif; ?>
                                                                    <?php if (isset($specs['accounts'])): ?>
                                                                        <li><i class="fa fa-check-square"></i><?php echo htmlspecialchars($specs['accounts']); ?> Accounts</li>
                                                                    <?php endif; ?>
                                                                    <?php if (isset($specs['bandwidth'])): ?>
                                                                        <li><i class="fa fa-check-square"></i><?php echo htmlspecialchars($specs['bandwidth']); ?> Bandwidth</li>
                                                                    <?php endif; ?>
                                                                    <?php if (isset($specs['emails'])): ?>
                                                                        <li><i class="fa fa-check-square"></i><?php echo htmlspecialchars($specs['emails']); ?> Email Accounts</li>
                                                                    <?php endif; ?>
                                                                    <?php if (isset($specs['subdomains'])): ?>
                                                                        <li><i class="fa fa-check-square"></i><?php echo htmlspecialchars($specs['subdomains']); ?> Sub Domains</li>
                                                                    <?php endif; ?>
                                                                    <?php if (isset($specs['databases'])): ?>
                                                                        <li><i class="fa fa-check-square"></i><?php echo htmlspecialchars($specs['databases']); ?> Database</li>
                                                                    <?php endif; ?>
                                                                    <?php if (isset($specs['free_setup'])): ?>
                                                                        <li><i class="fa fa-check-square"></i><?php echo htmlspecialchars($specs['free_setup']); ?></li>
                                                                    <?php endif; ?>
                                                                    <?php if (isset($specs['uptime'])): ?>
                                                                        <li><i class="fa fa-check-square"></i><?php echo htmlspecialchars($specs['uptime']); ?></li>
                                                                    <?php endif; ?>
                                                                </ul>
                                                            </div>
                                                            
                                                            <div class="action">
                                                                <a href="<?php echo htmlspecialchars($orderUrl); ?>" class="btn-custom">Order Now</a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <?php
                                                }
                                            } else {
                                                echo '<div class="col-lg-12 text-center"><p>No plans available at the moment.</p></div>';
                                            }
                                            ?>
                        </div>
<!--                                        <div class="col-md-12 wow fadeInUp">-->
<!--                            <div class="text-center">-->
<!--                                <h2><span class="uptitle id-color">Select Your</span>Windows Reseller Hosting Plan</h2>-->
<!--                                <div class="spacer-20"></div>-->
<!--                            </div>-->
<!--                        </div>-->
<!--                        <div class="col-lg-3 col-md-6 col-sm-12">-->
<!--                                            <div class="pricing-s1 mb30">-->
<!--                                                <div class="top">-->
<!--                                                    <h2>BEGINNER BUSINESS PLAN</h2>-->
<!--                                                    <p class="price">-->
<!--														<span class="txt">Start from</span>-->
<!--														<span class="currency">INR</span>-->
<!--														<span class="m opt-1">699</span>-->
<!--														<span class="y opt-2">749</span>-->
<!--														<span class="month">p/mo</span>-->
<!--													</p>               -->
<!--                                                </div>-->
												
<!--                                                <div class="bottom">-->

<!--                                                    <ul>-->
<!--                                                        <li><i class="fa fa-check-square"></i>25 GB Storage</li>-->
<!--                                                        <li><i class="fa fa-check-square"></i>Unlimited Website </li>-->
<!--                                                        <li><i class="fa fa-check-square"></i>Unlimited Bandwidth </li>-->
<!--                                                        <li><i class="fa fa-check-square"></i>Unlimited Email Accounts</li>-->
<!--                                                        <li><i class="fa fa-check-square"></i>Unlimited Sub Domains-->
<!--</li>-->
<!--                                                        <li><i class="fa fa-check-square"></i>Unlimited Mysql Database</li>-->
<!--                                                        <li><i class="fa fa-check-square"></i>FREE Set Up!!-->
<!--</li>-->
<!--                                                        <li><i class="fa fa-check-square"></i>99.9% Uptime Guarantee</li>-->
<!--                                                    </ul>-->
<!--                                                </div>-->
												
<!--												<div class="action">-->
<!--													<a href="http://siteworx.in/manage/cart.php?a=add&pid=14" class="btn-custom">Order Now</a>-->
<!--												</div>-->
<!--                                            </div>-->
<!--                                        </div>-->
<!--                                        <div class="col-lg-3 col-md-6 col-sm-12">-->
<!--                                            <div class="pricing-s1 mb30">-->
												<!--<div class="ribbon">HOT</div>-->
<!--                                                <div class="top">-->
<!--                                                    <h2>TRAFFIC PLAN</h2>-->
<!--                                                    <p class="price">-->
<!--														<span class="txt">Start from</span>-->
<!--														<span class="currency">INR</span>-->
<!--														<span class="m opt-1">1152</span>-->
<!--														<span class="y opt-2">899</span>-->
<!--														<span class="month">p/mo</span>-->
<!--													</p>     -->
<!--                                                </div>-->
<!--                                                <div class="bottom">-->
<!--                                                    <ul>-->
<!--                                                        <li><i class="fa fa-check-square"></i>50 GB Storage</li>-->
<!--                                                        <li><i class="fa fa-check-square"></i>Unlimited Website-->
<!--                                        </li>-->
<!--                                                        <li><i class="fa fa-check-square"></i>Unlimited Bandwidth-->
<!--                                        </li>-->
<!--                                                        <li><i class="fa fa-check-square"></i>Unlimited Email Accounts</li>-->
<!--                                                        <li><i class="fa fa-check-square"></i>Unlimited Sub Domains</li>-->
<!--                                                        <li><i class="fa fa-check-square"></i>Unlimited Mysql Database</li>-->
<!--                                                        <li><i class="fa fa-check-square"></i>FREE Set Up!!</li>-->
<!--                                                        <li><i class="fa fa-check-square"></i>99.9% Uptime Guarantee</li>-->
<!--                                                    </ul>-->
<!--                                                </div>-->
												
<!--												<div class="action">-->
<!--													<a href="http://siteworx.in/manage/cart.php?a=add&pid=15" class="btn-custom">Order Now</a>-->
<!--												</div>-->
<!--                                            </div>-->
<!--                                        </div>-->
<!--                                        <div class="col-lg-3 col-md-6 col-sm-12">-->
<!--                                            <div class="pricing-s1 mb30">-->
												<!--<div class="ribbon">HOT</div>-->
<!--                                                <div class="top">-->
<!--                                                    <h2>PROFESSIONAL PLAN</h2>-->
<!--                                                    <p class="price">-->
<!--														<span class="txt">Start from</span>-->
<!--														<span class="currency">INR</span>-->
<!--														<span class="m opt-1">1452</span>-->
<!--														<span class="y opt-2">1199</span>-->
<!--														<span class="month">p/mo</span>-->
<!--													</p>     -->
<!--                                                </div>-->
<!--                                                <div class="bottom">-->
<!--                                                    <ul>-->
<!--                                                        <li><i class="fa fa-check-square"></i>100 GB Storage</li>-->
<!--                                                        <li><i class="fa fa-check-square"></i>Unlimited Website-->
<!--                                        </li>-->
<!--                                                        <li><i class="fa fa-check-square"></i>Unlimited Bandwidth-->
<!--                                        </li>-->
<!--                                                        <li><i class="fa fa-check-square"></i>Unlimited Email Accounts</li>-->
<!--                                                        <li><i class="fa fa-check-square"></i>Unlimited Sub Domains</li>-->
<!--                                                        <li><i class="fa fa-check-square"></i>Unlimited Mysql Database</li>-->
<!--                                                        <li><i class="fa fa-check-square"></i>FREE Set Up!!</li>-->
<!--                                                        <li><i class="fa fa-check-square"></i>99.9% Uptime Guarantee</li>-->
<!--                                                    </ul>-->
<!--                                                </div>-->
												
<!--												<div class="action">-->
<!--													<a href="http://siteworx.in/manage/cart.php?a=add&pid=15" class="btn-custom">Order Now</a>-->
<!--												</div>-->
<!--                                            </div>-->
<!--                                        </div>-->
<!--                                        <div class="col-lg-3 col-md-6 col-sm-12">-->
<!--                                            <div class="pricing-s1 mb30">-->
<!--                                                <div class="top">-->
<!--                                                    <h2>ENTERPRISE PLAN</h2>-->
<!--                                                    <p class="price">-->
<!--														<span class="txt">Start from</span>-->
<!--														<span class="currency">INR</span>-->
<!--														<span class="m opt-1">1999</span>-->
<!--														<span class="y opt-2">1799</span>-->
<!--														<span class="month">p/mo</span>-->
<!--													</p>     -->
<!--                                                </div>-->
<!--                                                <div class="bottom">-->
<!--                                                    <ul>-->
<!--                                                        <li><i class="fa fa-check-square"></i>200 GB Storage</li>-->
<!--                                                        <li><i class="fa fa-check-square"></i>Unlimited Website</li>-->
<!--                                                        <li><i class="fa fa-check-square"></i>Unlimited Bandwidth</li>-->
<!--                                                        <li><i class="fa fa-check-square"></i>Unlimited Email Accounts</li>-->
<!--                                                        <li><i class="fa fa-check-square"></i>Unlimited Sub Domains</li>-->
<!--                                                        <li><i class="fa fa-check-square"></i>Unlimited Mysql Database</li>-->
<!--                                                        <li><i class="fa fa-check-square"></i>FREE Set Up!!</li>-->
<!--                                                        <li><i class="fa fa-check-square"></i>99.9% Uptime Guarantee</li>-->
<!--                                                    </ul>-->
<!--                                                </div>-->
												
<!--												<div class="action">-->
<!--													<a href="http://siteworx.in/manage/cart.php?a=add&pid=16" class="btn-custom">Order Now</a>-->
<!--												</div>-->
                                            </div>
                                        </div>
            </section>
 
<script>
document.addEventListener('DOMContentLoaded', function() {
    const toggleSwitch = document.getElementById('sw-1');
    const plansContainer = document.getElementById('plans-container');
    
    if (!toggleSwitch || !plansContainer) return;
    
    // Initialize - show Linux plans by default (unchecked)
    updatePlansDisplay(false);
    
    // Listen for toggle change
    toggleSwitch.addEventListener('change', function() {
        updatePlansDisplay(this.checked);
    });
    
    function updatePlansDisplay(isWindows) {
        const planCards = plansContainer.querySelectorAll('[data-platform]');
        
        planCards.forEach(card => {
            const platform = card.getAttribute('data-platform');
            
            if ((isWindows && platform === 'windows') || (!isWindows && platform === 'linux')) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    }
});
</script>
			  <section id="section-features">
                <div class="container">
                    <div class="row">
                        <div class="col-md text-center wow fadeInUp">
                            <h2><span class="uptitle id-color">Build For Speed</span>Hosting Features</h2>
                            <div class="spacer-20"></div>
                        </div>
                    </div>
                    <div class="row wow fadeInUp">
                        <div class="col-md col-sm-6 mb-sm-30">
                            <div class="feature-box-type-3">
                                <i class="icon-alarmclock"></i>
                                <h4>Instant Activation</h4>
                            </div>
                        </div>

                        <div class="col-md col-sm-6 mb-sm-30">
                            <div class="feature-box-type-3">
                                <i class="icon-profile-male"></i>
                                <h4>24 / 7 Support</h4>
                            </div>
                        </div>

                        <div class="col-md col-sm-6 mb-sm-30">
                            <div class="feature-box-type-3">
                                <i class="icon-refresh"></i>
                                <h4>99.9% Uptime</h4>
                            </div>
                        </div>

                        <div class="col-md col-sm-6 mb-sm-30">
                            <div class="feature-box-type-3">
                                <i class="icon-upload"></i>
                                <h4>Cloud Powered</h4>
                            </div>
                        </div>
                        <div class="col-md col-sm-6 mb-sm-30">
                            <div class="feature-box-type-3">
                                <i class="icon-layers"></i>
                                <h4>Multi Datacenter</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
           <section id="our-testimonials" class="no-top no-bottom text-light" data-bgimage="url(images/background/banner6.jpg) center" data-stellar-background-ratio=".2">
                <div class="overlay-gradient t100" style="padding: 50px 0 50px 0">
                    <div class="text-center wow fadeInUp">
                        <h2 style= "color:#ffffff"><span class="uptitle">Web Development &</span>Hosting Provider</h2>
                        <!--<div class="spacer-20"></div>-->
                    </div>
                    <div class="owl-carousel owl-theme wow fadeInUp" id="testimonial-slider">
                        
                        <div class="item">
                            <div class="de_testi opt-2 ">
                                <blockquote  class="new pt-1">
                                    <p>SiteWork is an excellent web hosting service that's simple to use and offers an array of useful plans for consumers and small businesses.</p>
                                    <div class="de_testi_by">
                                        <img alt="" class="rounded-circle" src="images/people/kd.png"> <span>KD Software</span>
                                    </div>
                                </blockquote>
                            </div>
                        </div>
                        <div class="item">
                            <div class="de_testi opt-2 ">
                                <blockquote  class="new pt-1">
                                    <p>Great support, like i have never seen before. Thanks to the support team, they are very helpfull. This company provide customers great solution, that makes them best.</p>
                                    <div class="de_testi_by">
                                        <img alt="" class="rounded-circle" src="images/people/gfp.png"> <span>Go Find A Pro</span>
                                    </div>
                                </blockquote>
                            </div>
                        </div>
                        <div class="item">
                            <div class="de_testi opt-2 ">
                                <blockquote  class="new pt-1">
                                    <p>They offer a lot of value based on their skills, talent, and rate.</p>
                                    <div class="de_testi_by">
                                        <img alt="" class="rounded-circle" src="images/people/tws.png"> <span>Tech Well Solution</span>
                                    </div>
                                </blockquote>
                            </div> 
                        </div>
                        
                    </div> 
                </div>
            </section>
 
<section id="section-fun-facts" class="pt10 pb10 text-light bg-gradient-to-right-review" style="background-size: cover;">
                <div class="container" style="background-size: cover;">

                    <div class="row" style="background-size: cover;">
                        <div class="col-md-3 col-sm-6" style="background-size: cover;">
                            <div class="de_count" style="background-size: cover;">
                                <h3 class="timer" data-to="15425" data-speed="3000">15425</h3>
                                <span>Website Powered</span>
                            </div>
                        </div>

                        <div class="col-md-3 col-sm-6" style="background-size: cover;">
                            <div class="de_count" style="background-size: cover;">
                                <h3 class="timer" data-to="237" data-speed="3000">237</h3>
                                <span>Clients Supported</span>
                            </div>
                        </div>

                        <div class="col-md-3 col-sm-6" data-wow-delay=".5s" style="background-size: cover;">
                            <div class="de_count" style="background-size: cover;">
                                <h3 class="timer" data-to="11" data-speed="3000">11</h3>
                                <span>Awards Winning</span>
                            </div>
                        </div>

                        <div class="col-md-3 col-sm-6" style="background-size: cover;">
                            <div class="de_count" style="background-size: cover;">
                                <h3 class="timer" data-to="4" data-speed="3000">4</h3>
                                <span>Years Experience</span>
                            </div>
                        </div>
                    </div>

                </div>
            </section>
            
           
        <!-- content close -->


   <?php include('footer.php'); ?>
            <!-- footer begin -->
