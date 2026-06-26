   <?php 
   $pageTitle = "Email Marketing India: Strategies, Services, Success -All at Siteworx india";
  $Metadescription="Small business owner,  Marketer, or Nonprofit organization, SiteWorx-India offers a user-friendly interface and a range of pricing options to suit your email marketing needs.";
  $metakeywords="Web hosting in India, Best web hosting in India, cheapest web hosting in india, web hosting company in india, best web hosting company in india, web hosting india, web hosting services in india, domain name in india, domain name, web hosting companies India, email hosting, best web hosting, cheap web hosting, Bulk Email Marketing Server, Best web development in india, web development in india, web design in india, Best SEO Service in India, Best ADS Service In india, Best Facebook ADS in india, Best web Development In Jaipur, Best Instagram ADS In India";
   include('header.php');

   // Load Email Marketing plans from admin hosting_plans table.
   $emailMarketingPlans = [];
   if (isset($pdo)) {
       try {
           $stmt = $pdo->prepare("SELECT * FROM hosting_plans WHERE category = :cat AND status='active' ORDER BY price_monthly ASC");
           $stmt->execute([':cat' => 'email-marketing']);
           $emailMarketingPlans = $stmt->fetchAll();
       } catch (Exception $e) {
           $emailMarketingPlans = [];
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
									<h1 style='color:white '>Email Marketing<span class="label">5% OFF </span></h1>
									<p class="lead">Turbocharge your email campaigns with our dedicated email marketing servers, ensuring high deliverability and maximum impact for your messages. </p>
									<div class="spacer-half"></div>
									<a class="btn-custom" href="#plans">See All Plans</a>
								</div>
								
								<div class="col-lg-3 offset-lg-2 col-4 offset-4 text-center">
									<img src="images/data-transfer-icon1.gif" class="img-fluid" alt="" style="opacity: 0.5; " >
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
                                <h1 style="color:#115256">India's Best Bulk Email Marketing Solution</h1>
                                <h2><span class="uptitle id-color">Select Your</span>Email Marketing Plan</h2>
                                <div class="spacer-20"></div>
                            </div>
                        </div>
                                        <?php if (!empty($emailMarketingPlans)): ?>
                                            <?php foreach ($emailMarketingPlans as $index => $plan): ?>
                                                <?php
                                                $specs = json_decode($plan['specs'] ?? '{}', true);
                                                if (!is_array($specs)) {
                                                    $specs = [];
                                                }
                                                $features = $specs['features'] ?? [];
                                                if (!is_array($features)) {
                                                    $features = [];
                                                }
                                                $orderUrl = $specs['order_url'] ?? '#';
                                                $buttonText = $specs['button_text'] ?? 'Order Now';
                                                $monthlyPrice = (float)($plan['price_monthly'] ?? 0);
                                                $yearlyPrice = (float)($plan['price_yearly'] ?? 0);
                                                ?>
                                                <div class="col-lg-3 col-md-6 col-sm-12">
                                                    <div class="pricing-s1 mb30">
                                                        <?php if ($index === 1): ?>
                                                            <div class="ribbon">HOT</div>
                                                        <?php endif; ?>
                                                        <div class="top">
                                                            <h2><?php echo htmlspecialchars($plan['name']); ?></h2>
                                                            <p class="price">
                                                                <span class="txt">Start from</span>
                                                                <span class="currency"><?php echo htmlspecialchars($plan['currency'] ?? 'INR'); ?></span>
                                                                <span class="m opt-1"><?php echo number_format($monthlyPrice, 0); ?></span>
                                                                <span class="y opt-2"><?php echo $yearlyPrice > 0 ? number_format($yearlyPrice, 0) : '#'; ?></span>
                                                                <span class="month">p/mo</span>
                                                            </p>
                                                        </div>
                                                        <div class="bottom">
                                                            <ul>
                                                                <?php foreach ($features as $feature): ?>
                                                                    <li><i class="fa fa-check-square"></i><?php echo htmlspecialchars($feature); ?></li>
                                                                <?php endforeach; ?>
                                                                <?php if (isset($specs['cpu_cores'])): ?>
                                                                    <li><i class="fa fa-check-square"></i><?php echo htmlspecialchars($specs['cpu_cores']); ?> CPU Core</li>
                                                                <?php endif; ?>
                                                                <?php if (isset($specs['ram'])): ?>
                                                                    <li><i class="fa fa-check-square"></i><?php echo htmlspecialchars($specs['ram']); ?> RAM</li>
                                                                <?php endif; ?>
                                                                <?php if (isset($specs['storage'])): ?>
                                                                    <li><i class="fa fa-check-square"></i><?php echo htmlspecialchars($specs['storage']); ?></li>
                                                                <?php endif; ?>
                                                                <?php if (isset($specs['bandwidth'])): ?>
                                                                    <li><i class="fa fa-check-square"></i><?php echo htmlspecialchars($specs['bandwidth']); ?> Bandwidth</li>
                                                                <?php endif; ?>
                                                                <?php if (isset($specs['ip_addresses'])): ?>
                                                                    <li><i class="fa fa-check-square"></i><?php echo htmlspecialchars($specs['ip_addresses']); ?> IP Addresses</li>
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
                                                            <a href="<?php echo htmlspecialchars($orderUrl); ?>" class="btn-custom"><?php echo htmlspecialchars($buttonText); ?></a>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                        <div class="col-lg-3 col-md-6 col-sm-12">
                                            <div class="pricing-s1 mb30">
                                                <div class="top">
                                                    <h2>Business Plan</h2>
                                                    <p class="price">
														<span class="txt">Start from</span>
														<span class="currency">INR</span>
														<span class="m opt-1">6999</span>
														<span class="y opt-2">#</span>
														<span class="month">p/mo</span>
													</p>               
                                                </div>
												
                                                <div class="bottom">

                                                    <ul>
                                                        <li><i class="fa fa-check-square"></i>4 CPU Core</li>
                                                        <li><i class="fa fa-check-square"></i>8 GB RAM</li>
                                                        <li><i class="fa fa-check-square"></i>500 HDD</li>
                                                        <li><i class="fa fa-check-square"></i>5 TB Bandwidth</li>
                                                        <li><i class="fa fa-check-square"></i>5 IP Addresses</li>
                                                        <li><i class="fa fa-check-square"></i>FREE Set Up!!</li>
                                                        <li><i class="fa fa-check-square"></i>99.9% Uptime Guarantee</li>
                                                    </ul>
                                                </div>
												
												<div class="action">
													<a href="https://siteworx.in/manage/cart.php?a=add&pid=59" class="btn-custom">Order Now</a>
												</div>
                                            </div>
                                        </div>
                                        <div class="col-lg-3 col-md-6 col-sm-12">
                                            <div class="pricing-s1 mb30">
												<div class="ribbon">HOT</div>
                                                <div class="top">
                                                    <h2>Traffic Plan</h2>
                                                    <p class="price">
														<span class="txt">Start from</span>
														<span class="currency">INR</span>
														<span class="m opt-1">7999</span>
														<span class="y opt-2">#</span>
														<span class="month">p/mo</span>
													</p>     
                                                </div>
                                                <div class="bottom">
                                                    <ul>
                                                        <li><i class="fa fa-check-square"></i> <span>8 Cores / 8 Threads</span> </li>
                                                        <li><i class="fa fa-check-square"></i>16 GB RAM</li>
                                                        <li><i class="fa fa-check-square"></i>2TB SATA</li>
                                                        <li><i class="fa fa-check-square"></i>5 TB Bandwidth</li>
                                                        <li><i class="fa fa-check-square"></i>5 IP Addresses</li>
                                                        <li><i class="fa fa-check-square"></i>FREE Set Up!!</li>
                                                        <li><i class="fa fa-check-square"></i>99.9% Uptime Guarantee</li>
                                                    </ul>
                                                </div>
												
												<div class="action">
													<a href="https://siteworx.in/manage/cart.php?a=add&pid=60" class="btn-custom">Order Now</a>
												</div>
                                            </div>
                                        </div>
										<div class="col-lg-3 col-md-6 col-sm-12">
                                            <div class="pricing-s1 mb30">
                                                <div class="top">
                                                    <h2>Professional Plan</h2>
                                                    <p class="price">
														<span class="txt">Start from</span>
														<span class="currency">INR</span>
														<span class="m opt-1">8499</span>
														<span class="y opt-2">#</span>
														<span class="month">p/mo</span>
													</p>     
                                                </div>
                                                <div class="bottom">
                                                    <ul>
                                                        <li><i class="fa fa-check-square"></i> 8 Cores/8 Threads</li>
                                                        <li><i class="fa fa-check-square"></i>16 GB RAM</li>
                                                        <li><i class="fa fa-check-square"></i>120GB NVme + 2TB</li>
                                                        <li><i class="fa fa-check-square"></i>5 TB Bandwidth</li>
                                                        <li><i class="fa fa-check-square"></i>5 IP Addresses</li>
                                                        <li><i class="fa fa-check-square"></i>FREE Set Up!!</li>
                                                        <li><i class="fa fa-check-square"></i>99.9% Uptime Guarantee</li>
                                                    </ul>
                                                </div>
												
												<div class="action">
													<a href="https://siteworx.in/manage/cart.php?a=add&pid=61" class="btn-custom">Order Now</a>
												</div>
                                            </div>
                                        </div>
                                    
                                    <div class="col-lg-3 col-md-6 col-sm-12">
                                            <div class="pricing-s1 mb30">
                                                <div class="top">
                                                    <h2>Enterprise Plan</h2>
                                                    <p class="price">
														<span class="txt">Start from</span>
														<span class="currency">INR</span>
														<span class="m opt-1" style="font-size: 38px;">110000</span>
														<span class="y opt-2">#</span>
														<span class="month">p/mo</span>
													</p>     
                                                </div>
                                                <div class="bottom">
                                                    <ul>
                                                        <li><i class="fa fa-check-square"></i>8 Cores/ 8 threads</li>
                                                        <li><i class="fa fa-check-square"></i>16 GB RAM</li>
                                                        <li><i class="fa fa-check-square"></i>500GB HDD</li>
                                                        <li><i class="fa fa-check-square"></i>5 TB Bandwidth</li>
                                                        <li><i class="fa fa-check-square"></i>/24(256) IP Addresses</li>
                                                        <li><i class="fa fa-check-square"></i>FREE Set Up!!</li>
                                                        <li><i class="fa fa-check-square"></i>99.9% Uptime Guarantee</li>
                                                    </ul>
                                                </div>
												
												<div class="action">
													<a href="https://siteworx.in/manage/cart.php?a=add&pid=62" class="btn-custom">Order Now</a>
												</div>
                                            </div>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
            </section>
 
<hr>
			  <section id="section-features">
                <div class="container">
                    <div class="row">
                        <div class="col-md text-center wow fadeInUp">
                            <h2><span class="uptitle id-color">Build For Speed</span>E-mail Marketing Features</h2>
                            <div class="spacer-20"></div>
                        </div>
                    </div>
                    <div class="row wow fadeInUp">
                        <div class="col-md col-sm-6 mb-sm-30">
                            <div class="feature-box-type-3">
                                <img src="images/google-gmail.png" style="padding:15px;">
                                <h4>Gmail</h4>
                            </div>
                        </div>

                        <div class="col-md col-sm-6 mb-sm-30">
                            <div class="feature-box-type-3">
                               <img src="images/emailmark1.webp" style="padding:15px; width: 150px;">
                                <h4>mails</h4>
                            </div>
                        </div>

                       

                        <div class="col-md col-sm-6 mb-sm-30">
                            <div class="feature-box-type-3">
                              <img src="images/emailmark3.webp" style="padding:15px; width: 85px;">
                                <h4>Market Power</h4>
                            </div>
                        </div>
                        <div class="col-md col-sm-6 mb-sm-30">
                            <div class="feature-box-type-3">
                                <img src="images/emailmark2.png" style="padding:15px; width: 150px;">
                                <h4>Mail Datacenter</h4>
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
           
        </div>
        <!-- content close -->


   <?php include('footer.php'); ?>
            <!-- footer begin -->
