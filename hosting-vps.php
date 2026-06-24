    <?php 
           $pageTitle = "Affordable VPS Hosting India: Your Gateway to Online Success";

       $Metadescription="Experience lightning-fast website loading speeds with Site-Worx India's VPS hosting solutions. With data centers strategically located across India, your site will benefit from low latency and superior performance.";

    include('header.php');
    
    // Load VPS hosting plans
    $vpsPlans = [];
    if (isset($pdo)) {
        try {
            $stmt = $pdo->prepare("SELECT * FROM hosting_plans WHERE category = :cat AND status='active' ORDER BY price_monthly ASC");
            $stmt->execute([':cat' => 'vps-hosting']);
            $vpsPlans = $stmt->fetchAll();
        } catch (Exception $e) {
            $vpsPlans = [];
        }
    }
    ?>
        <!-- content begin -->
        <div class="no-bottom no-top" id="content">
            <div id="top"></div>
			   <section class="no-top no-bottom text-light" data-bgimage="url(images/slider/1.jpg)" data-stellar-background-ratio=".2">
                <div class="overlay-gradient t80">
                    <div class="center-y mt50">
                        <div class="container">
                            <div class="row align-items-center">
								<div class="col-md-12 text-center">
									<h1> USA Windows VPS Hosting a</h1>						
										
									
								</div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <!-- revolution slider begin -->
          
			<section id="plans" class="relative pos-top no-top mt-60 no-bg">
                <div class="container">
                    <div class="row">
                        <?php
                        if (empty($vpsPlans)) {
                            echo '<div class="col-12 text-center"><p>No plans available at the moment.</p></div>';
                        } else {
                            $counter = 0;
                            foreach ($vpsPlans as $plan) {
                                $counter++;
                                $specs = json_decode($plan['specs'], true);
                                $isHOT = ($counter === 2) ? true : false;
                                ?>
                                <div class="col-lg-3 col-md-6 col-sm-12">
                                    <div class="pricing-s1 mb30">
                                        <?php if ($isHOT): ?>
                                            <div class="ribbon">HOT</div>
                                        <?php endif; ?>
                                        <div class="top">
                                            <h2><?php echo htmlspecialchars($plan['name']); ?></h2>
                                            <p class="price">
                                                <span class="txt">Start from</span>
                                                <span class="currency"><?php echo htmlspecialchars($plan['currency']); ?></span>
                                                <span class="m opt-1"><?php echo number_format($plan['price_monthly'], 0); ?></span>
                                                <span class="y opt-2"><?php echo number_format($plan['price_yearly'] / 12, 0); ?></span>
                                                <span class="month">p/mo</span>
                                            </p>
                                        </div>
                                        <div class="bottom">
                                            <ul>
                                                <?php if (isset($specs['cpu_cores'])): ?>
                                                    <li><i class="fa fa-check-square"></i><?php echo htmlspecialchars($specs['cpu_cores']); ?> CPU Cores</li>
                                                <?php endif; ?>
                                                <?php if (isset($specs['ram'])): ?>
                                                    <li><i class="fa fa-check-square"></i><?php echo htmlspecialchars($specs['ram']); ?> RAM</li>
                                                <?php endif; ?>
                                                <?php if (isset($specs['storage'])): ?>
                                                    <li><i class="fa fa-check-square"></i><?php echo htmlspecialchars($specs['storage']); ?> Hard Disk - SSD</li>
                                                <?php endif; ?>
                                                <?php if (isset($specs['bandwidth'])): ?>
                                                    <li><i class="fa fa-check-square"></i><?php echo htmlspecialchars($specs['bandwidth']); ?> Bandwidth</li>
                                                <?php endif; ?>
                                                <?php if (isset($specs['ip_addresses'])): ?>
                                                    <li><i class="fa fa-check-square"></i><?php echo htmlspecialchars($specs['ip_addresses']); ?> IP Addresses</li>
                                                <?php endif; ?>
                                                <?php if (isset($specs['control_panel'])): ?>
                                                    <li><i class="fa fa-check-square"></i>Free <?php echo htmlspecialchars($specs['control_panel']); ?> CPanel</li>
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
                                            <a href="#" class="btn-custom">Order Now</a>
                                        </div>
                                    </div>
                                </div>
                                <?php
                            }
                        }
                        ?>
                    </div>
                                                    <ul>
                                                        <li><i class="fa fa-check-square"></i>4 CPU Cores</li>
                                                        <li><i class="fa fa-check-square"></i>8 GB RAM</li>
                                                        <li><i class="fa fa-check-square"></i>100 GB Hard Disk - SSD</li>
                                                        <li><i class="fa fa-check-square"></i>2TB Bandwidth</li>
                                                        <li><i class="fa fa-check-square"></i>1 IP Addresses</li>
                                                        <li><i class="fa fa-check-square"></i>Free CentOS WP-Cpanel</li>
                                                        <li><i class="fa fa-check-square"></i>FREE Set Up!!</li>
                                                        <li><i class="fa fa-check-square"></i>99.9% Uptime Guarantee</li>
                                                    </ul>
                                                </div>
												
												<div class="action">
													<a href="#" class="btn-custom">Order Now</a>
												</div>
                                            </div>
                                        </div>
                                    
                                    </div>
                                </div>
            </section>
 
<hr>
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
                            <div class="feature-box-type-2">
                                <i class="icon-alarmclock"></i>
                                <h4>Instant Activation</h4>
                            </div>
                        </div>

                        <div class="col-md col-sm-6 mb-sm-30">
                            <div class="feature-box-type-2">
                                <i class="icon-profile-male"></i>
                                <h4>24 / 7 Support</h4>
                            </div>
                        </div>

                        <div class="col-md col-sm-6 mb-sm-30">
                            <div class="feature-box-type-2">
                                <i class="icon-refresh"></i>
                                <h4>99.9% Uptime</h4>
                            </div>
                        </div>

                        <div class="col-md col-sm-6 mb-sm-30">
                            <div class="feature-box-type-2">
                                <i class="icon-upload"></i>
                                <h4>Cloud Powered</h4>
                            </div>
                        </div>
                        <div class="col-md col-sm-6 mb-sm-30">
                            <div class="feature-box-type-2">
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
           