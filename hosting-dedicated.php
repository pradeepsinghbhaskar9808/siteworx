    <?php
       $pageTitle = "Affordable Dedicated Hosting in India, SiteWorx-India";
       $Metadescription="Start small and grow your server resources as your business expands. Our scalable dedicated hosting solutions are perfect for startups and growing businesses in India, SiteWorx-India.";
    include('header.php');
    
    // Load dedicated hosting plans
    $dedicatedPlans = [];
    if (isset($pdo)) {
        try {
            $stmt = $pdo->prepare("SELECT * FROM hosting_plans WHERE category = :cat AND status='active' ORDER BY price_monthly ASC");
            $stmt->execute([':cat' => 'dedicated-hosting']);
            $dedicatedPlans = $stmt->fetchAll();
        } catch (Exception $e) {
            $dedicatedPlans = [];
        }
    }
    ?>
        <!-- content begin -->
          
        <!-- content begin -->
        <div id="content" class="no-top no-bottom">
           
            <section class="no-top no-bottom text-light" data-bgimage="url(images/background/12.jpg)" data-stellar-background-ratio=".2">
                <div class="overlay-gradient t80">
                    <div class="center-y mt50">
                        <div class="container">
                            <div class="row align-items-center">
								<div class="col-lg-8 offset-md-2 text-center">
									<h1>Linux India Based Dedicated Server a</h1>
									<p class="lead">We provide best hosting solutions for your hosting needs. Our clients from personal to corporate. Our data center are all over the world to ensure your website is always up. Happy hosting!
								</div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <!-- revolution slider begin -->
          <section id="plans">
                <div class="container">
					<div class="row">
                        <div class="col text-center">
                            <h2>Select Plans</h2>						
							<div class="switch-set">
								<div>Monthly</div>								
								<div><input id="sw-1" class="switch" type="checkbox" /></div>					
								<div>Yearly</div>
								<div class="spacer-20"></div>
							</div>
							
                        </div>
						
						<div class="col-md-12">
							<table class="table table-pricing text-center">
							  <thead>
								<tr>
								  <th scope="col">Ram</th>
								  <th scope="col">CPU</th>
								  <th scope="col">Disk</th>
								  <th scope="col">Bandwidth</th>
								  <th scope="col">Price</th>
								  <th scope="col">Setup Fee</th>
								  <th scope="col"></th>
								</tr>
							  </thead>
							  <tbody>
								<?php
								if (empty($dedicatedPlans)) {
									echo '<tr><td colspan="7" class="text-center">No plans available at the moment.</td></tr>';
								} else {
									foreach ($dedicatedPlans as $plan) {
										$specs = json_decode($plan['specs'], true);
										?>
										<tr>
											<th><span class="lbl">Ram</span><?php echo htmlspecialchars($specs['ram'] ?? ''); ?></th>
											<td><span class="lbl">CPU</span><?php echo htmlspecialchars($specs['cpu_cores'] ?? ''); ?></td>
											<td><span class="lbl">Disk</span><?php echo htmlspecialchars($specs['storage'] ?? ''); ?></td>
											<td><span class="lbl">Bandwidth</span><?php echo htmlspecialchars($specs['bandwidth'] ?? ''); ?></td>
											<td><span class="lbl">Price</span><span class="opt-1"><?php echo htmlspecialchars($plan['currency']); ?> <?php echo number_format($plan['price_monthly'], 0); ?> monthly</span><span class="opt-2"><?php echo htmlspecialchars($plan['currency']); ?> <?php echo number_format($plan['price_yearly'], 0); ?> yearly</span></td>
											<td><span class="lbl">Setup Fee</span><?php echo htmlspecialchars($plan['setup_fee'] > 0 ? $plan['currency'] . ' ' . number_format($plan['setup_fee'], 0) : 'FREE'); ?></td>
											<td><a href="#" class="btn-custom">Order Now</a></td>
										</tr>
										<?php
									}
								}
								?>
							  </tbody>
							</table>
						</div>
                    </div>                            
                </div>
            </section>
			
			<section id="plans" class="relative pos-top no-top   no-bg">
                <div class="container">
                                   <div id="domainPricing" class="bg-whitesmoke">
            <div class="container">
			<div class="section-title"> <h2 style="text-align: center;"><span>DEDICATED SERVERS PRICE IN India</span>India Based Standard Dedicated Server Plans</h2> </div>
              <table class="table table-pricing text-center">
                    <thead>
                        <tr>
                            <th>Dedicated Processors</th>
                            <th>Clock</th>
                            <th>RAM</th>
                            <th>Storage</th>
                            <th>Bandwidth</th>
                            <th>IP's</th>
                            <th>Price</th>
                            <th>Availability</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td data-label="Processor">Intel Core i5-3470 4 Cores</td>
                            <td data-label="Cache">3.20Ghz</td>
                            <td data-label="RAM">8 GB DDR3</td>
                            <td data-label="HardDisk">240 GB SSD</td>
                            <td data-label="Bandwidth">2000 GB</td>
                            <td data-label="Dedicated IP">1 IP's</td>
                            <td data-label="Price"><div><span>₹  2999</span>/m</div></td>
                            <td data-label="Order"><a href="/buynow.php?a=add&amp;pid=97&amp;carttpl=cart&amp;currency=1" class="btn btn-custom">Configure Now</a></td>
                        </tr>
                        <tr>
                            <td data-label="Processor">Intel Core i7-3770 4 Cores</td>
                            <td data-label="Cache">3.40Ghz</td>
                            <td data-label="RAM">8 GB DDR3</td>
                            <td data-label="HardDisk">480 GB SSD</td>
                            <td data-label="Bandwidth">2000 GB</td>
                            <td data-label="Dedicated IP">1 IP's</td>
                            <td data-label="Price"><div><span>₹  3599</span>/m</div></td>
                            <td data-label="Order"><a href="/buynow.php?a=add&amp;pid=98&amp;carttpl=cart&amp;currency=1" class="btn btn-custom">Configure Now</a></td>
                        </tr>
                        <tr>
                            <td data-label="Processor">Intel Xeon 3430 4 Cores</td>
                            <td data-label="Cache">2.40Ghz</td>
                            <td data-label="RAM">8 GB DDR3</td>
                            <td data-label="HardDrive">480 GB SSD</td>
                            <td data-label="Bandwidth">200 TB</td>
                            <td data-label="Dedicated IP">1 IP's</td>
                            <td data-label="Price"><div><span>₹  3999</span>/m</div></td>
                            <td data-label="Order"><a href="/buynow.php?a=add&amp;pid=99&amp;carttpl=cart&amp;currency=1" class="btn btn-custom">Configure Now</a></td>
                        </tr>
                        <tr>
                            <td data-label="Processor">Intel Xeon 1231_v3 4 Cores</td>
                            <td data-label="Cache">3.40Ghz</td>
                            <td data-label="RAMs">16 GB DDR3</td>
                            <td data-label="HardDrive">480 GB SSD</td>
                            <td data-label="Bandwidth">200 TB</td>
                            <td data-label="Dedicated IP">1 IP's</td>
                            <td data-label="Price"><div><span>₹  4999</span>/m</div></td>
                            <td data-label="Order"><a href="/buynow.php?a=add&amp;pid=100&amp;carttpl=cart&amp;currency=1" class="btn btn-custom">Configure Now</a></td>
                        </tr>
                        <tr>
                            <td data-label="Processor">Intel Xeon E5-2620 6 Cores</td>
                            <td data-label="Cache">2.5Ghz</td>
                            <td data-label="RAMs">32 GB DDR3</td>
                            <td data-label="HardDrive">480 GB SSD</td>
                            <td data-label="Bandwidth">200 TB</td>
                            <td data-label="Dedicated IP">1 IP's</td>
                            <td data-label="Price"><div><span>₹  5999</span>/m</div></td>
                            <td data-label="Order"><a href="/buynow.php?a=add&amp;pid=101&amp;carttpl=cart&amp;currency=1" class="btn btn-custom">Configure Now</a></td>
                        </tr>
                        <tr>
                            <td data-label="Processor">Intel Xeon E5-2620 6 Cores</td>
                            <td data-label="Cache">2.5Ghz</td>
                            <td data-label="RAMs">64 GB DDR3</td>
                            <td data-label="HardDrive">480 GB SSD</td>
                            <td data-label="Bandwidth">200 TB</td>
                            <td data-label="Dedicated IP">1 IP's</td>
                            <td data-label="Price"><div><span>₹  6999</span>/m</div></td>
                            <td data-label="Order"><a href="/buynow.php?a=add&amp;pid=102&amp;carttpl=cart&amp;currency=1" class="btn btn-custom">Configure Now</a></td>
                        </tr>
                        <tr>
                            <td data-label="Processor">Intel Xeon E5-2650 8 Cores</td>
                            <td data-label="Cache">2.60Ghz</td>
                            <td data-label="RAMs">64 GB DDR3</td>
                            <td data-label="HardDrive">480 GB SSD</td>
                            <td data-label="Bandwidth">200 TB</td>
                            <td data-label="Dedicated IP">1 IP's</td>
                            <td data-label="Price"><div><span>₹  7999</span>/m</div></td>
                            <td data-label="Order"><a href="/buynow.php?a=add&amp;pid=103&amp;carttpl=cart&amp;currency=1" class="btn btn-custom">Configure Now</a></td>
                        </tr>
                        <tr>
                            <td data-label="Processor">Intel Xeon E5-2620 12 Corse</td>
                            <td data-label="Cache">2.60Ghz</td>
                            <td data-label="RAMs">64 GB DDR3</td>
                            <td data-label="HardDrive">480 GB SSD</td>
                            <td data-label="Bandwidth">200 TB</td>
                            <td data-label="Dedicated IP">1 IP's</td>
                            <td data-label="Price"><div><span>₹  8999</span>/m</div></td>
                            <td data-label="Order"><a href="/buynow.php?a=add&amp;pid=104&amp;carttpl=cart&amp;currency=1" class="btn btn-custom">Configure Now</a></td>
                        </tr>
                        <tr>
                            <td data-label="Processor">Intel Xeon E5-2670  16 Corse</td>
                            <td data-label="Cache">2.60Ghz</td>
                            <td data-label="RAMs">128 GB DDR3</td>
                            <td data-label="HardDrive">960 GB SSD</td>
                            <td data-label="Bandwidth">200 TB</td>
                            <td data-label="Dedicated IP">1 IP's</td>
                            <td data-label="Price"><div><span>₹  13999</span>/m</div></td>
                            <td data-label="Order"><a href="/buynow.php?a=add&amp;pid=105&amp;carttpl=cart&amp;currency=1" class="btn btn-custom">Configure Now</a></td>
                        </tr>
                        <tr>
                            <td data-label="Processor">Dual Xeon E5-2670 16 Cores</td>
                            <td data-label="Cache">2.60Ghz</td>
                            <td data-label="RAMs">256 GB DDR3</td>
                            <td data-label="HardDrive">960 GB SSD</td>
                            <td data-label="Bandwidth">200 TB</td>
                            <td data-label="Dedicated IP">1 IP's</td>
                            <td data-label="Price"><div><span>₹  17999</span>/m</div></td>
                            <td data-label="Order"><a href="/buynow.php?a=add&amp;pid=106&amp;carttpl=cart&amp;currency=1" class="btn btn-custom">Configure Now</a></td>
                        </tr>
                        <tr>
                            <td data-label="Processor">Dual E5-2683v4 24 Cores</td>
                            <td data-label="Cache">3.0GHz Turbo</td>
                            <td data-label="RAMs">128 GB DDR3</td>
                            <td data-label="HardDrive">960 GB SSD</td>
                            <td data-label="Bandwidth">200 TB</td>
                            <td data-label="Dedicated IP">1 IP's</td>
                            <td data-label="Price"><div><span>₹  17999</span>/m</div></td>
                            <td data-label="Order"><a href="/buynow.php?a=add&amp;pid=106&amp;carttpl=cart&amp;currency=1" class="btn btn-custom">Configure Now</a></td>
                        </tr>
                        <tr>
                            <td data-label="Processor">Dual E5-2683v4 24 Cores</td>
                            <td data-label="Cache">3.0GHz Turbo</td>
                            <td data-label="RAMs">256 GB DDR3</td>
                            <td data-label="HardDrive">960 GB SSD</td>
                            <td data-label="Bandwidth">200 TB</td>
                            <td data-label="Dedicated IP">1 IP's</td>
                            <td data-label="Price"><div><span>₹  21999</span>/m</div></td>
                            <td data-label="Order"><a href="/buynow.php?a=add&amp;pid=107&amp;carttpl=cart&amp;currency=1" class="btn btn-custom">Configure Now</a></td>
                        </tr>
                        <tr>
                            <td data-label="Processor">Xeon E7-4860 4O Cores</td>
                            <td data-label="Cache">2.67GHz Turbo</td>
                            <td data-label="RAMs">256GB DDR3</td>
                            <td data-label="HardDrive">960 GB SSD</td>
                            <td data-label="Bandwidth">200TB</td>
                            <td data-label="Dedicated IP">5 IP's</td>
                            <td data-label="Price"><div><span>₹  29999</span>/m</div></td>
                            <td data-label="Order"><a href="/buynow.php?a=add&amp;pid=108&amp;carttpl=cart&amp;currency=1" class="btn btn-custom">Configure Now</a></td>
                        </tr>
                    </tbody>
                </table>
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

        </div>
        <!-- content close -->


   <?php include('footer.php'); ?>
            <!-- footer begin -->
           