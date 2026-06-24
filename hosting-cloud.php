   <?php 
    $pageTitle = "Affordable Cloud Hosting Solutions for Indian Businesses- SiteWorx-india";

   $Metadescription="Find the perfect plan to fit your website's needs. High-performance servers, automatic backups, and a user-friendly control panel. Web Hosting,Hosting Services,Domain Hosting,Website Hosting,Shared Hosting,VPS Hosting ,Dedicated Server Hosting,Cloud Hosting,Managed Hosting,Reseller Hosting. Buy Now!";
  $metakeywords="Web hosting in India, Best web hosting in India, cheapest web hosting in india, web hosting company in india, best web hosting company in india, web hosting india, web hosting services in india, domain name in india, domain name, web hosting companies India, email hosting, best web hosting, cheap web hosting, Bulk Email Marketing Server, Best web development in india, web development in india, web design in india, Best SEO Service in India, Best ADS Service In india, Best Facebook ADS in india, Best web Development In Jaipur, Best Instagram ADS In India";
   include('header.php');   
   // Load cloud hosting plans
   $cloudPlans = [];
   if (isset($pdo)) {
       try {
           $stmt = $pdo->prepare("SELECT * FROM hosting_plans WHERE category = :cat AND status='active' ORDER BY price_monthly ASC");
           $stmt->execute([':cat' => 'cloud-hosting']);
           $cloudPlans = $stmt->fetchAll();
       } catch (Exception $e) {
           $cloudPlans = [];
       }
   }   ?>
        <!-- content begin -->
        <div class="no-bottom no-top" id="content">
            <div id="top"></div>
			   <section class="no-top no-bottom text-light" data-bgimage="url(images/slider/1.jpg)" data-stellar-background-ratio=".2">
                <div class="overlay-gradient t80">
                    <div class="center-y mt50">
                        <div class="container">
                            <div class="row align-items-center">
								<div class="col-md-12 text-center">
									<h1>Cloud Hosting Site Worx</h1>						
										
									
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
                        if (empty($cloudPlans)) {
                            echo '<div class="col-12 text-center"><p>No plans available at the moment.</p></div>';
                        } else {
                            $counter = 0;
                            foreach ($cloudPlans as $plan) {
                                $counter++;
                                $specs = json_decode($plan['specs'], true);
                                $isHOT = ($counter === 2) ? true : false;
                                ?>
                                <div class="col-lg-4 col-md-6 col-sm-12">
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

        </div>
        <!-- content close -->


   <?php include('footer.php'); ?>
            <!-- footer begin -->
           