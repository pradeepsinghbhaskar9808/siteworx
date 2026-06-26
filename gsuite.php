 <?php 
 $pageTitle = "G-Suite Made in India: Elevate Your Business Efficiency SiteWorx-India";
   $canonical='https://www.siteworx.in/gshuit"';
  $Metadescription="G-Suite for Small Businesses in India,Discover how G-Suite can transform your Indian business with powerful cloud-based tools for communication, collaboration, and productivity. SiteWorx-India";
  $metakeywords="Web hosting in India, Best web hosting in India, cheapest web hosting in india, web hosting company in india, best web hosting company in india, web hosting india, web hosting services in india, domain name in india, domain name, web hosting companies India, email hosting, best web hosting, cheap web hosting, Bulk Email Marketing Server, Best web development in india, web development in india, web design in india, Best SEO Service in India, Best ADS Service In india, Best Facebook ADS in india, Best web Development In Jaipur, Best Instagram ADS In India";
   include('header.php');

   // Load Google Workspace plans from admin hosting_plans table.
   $gsuitePlans = [];
   if (isset($pdo)) {
       try {
           $stmt = $pdo->prepare("SELECT * FROM hosting_plans WHERE category = :cat AND status='active' ORDER BY price_monthly ASC");
           $stmt->execute([':cat' => 'gsuite']);
           $gsuitePlans = $stmt->fetchAll();
       } catch (Exception $e) {
           $gsuitePlans = [];
       }
   }
   ?>
        <!-- content begin -->
 <!-- content begin -->
        <div id="content" class="no-top no-bottom">
           
            <section class="no-top no-bottom text-light hero-image" data-bgimage="url(images/background/banner4.jpg)" >
                <div class="overlay-gradient ">
                    <div class="center-y mt50">
                        <div class="container">
        <!--                    <div class="row align-items-center">-->
								<!--<div class="col-lg-6 col-12 text-lg-left text-center mb-sm-30">-->
								<!--	<h1 style="color:#fff" >Google Workspace<span class="label">5% OFF</span></h1>-->
								<!--	<p class="lead"> Google Workspace is designed to support stringent privacy and security standards based on industry best practices. </p>-->
								<!--	<div class="spacer-half"></div>-->
								<!--	<a class="btn-custom" href="#plans">See All Plans</a>-->
								<!--</div>-->
								
								<!--<div class="col-lg-3 offset-lg-2 col-4 offset-4 text-center">-->
								<!--	<img src="images/big-icon-shared-hosting.png" class="img-fluid" alt="">-->
								<!--</div>-->
        <!--                    </div>-->
                        </div>
                    </div>
                </div>
            </section>
            <!-- revolution slider begin -->
            
            
            <section id="plans" class="pb40" >
                <div class="container">
                                    <div class="row">
                                         <div class="col-md-12 wow fadeInUp">
                            <div class="text-center">
                                <h1 style="color:#115256"> Google Workspace Reseller in India</h1>
                                <h2><span class="uptitle id-color">Select Your</span>Gsuite Plans</h2>
                                <div class="spacer-20"></div>
                            </div>
                        </div>
                                        <?php if (!empty($gsuitePlans)): ?>
                                            <?php foreach ($gsuitePlans as $index => $plan): ?>
                                                <?php
                                                $specs = json_decode($plan['specs'] ?? '{}', true);
                                                if (!is_array($specs)) {
                                                    $specs = [];
                                                }
                                                $features = $specs['features'] ?? [];
                                                if (!is_array($features)) {
                                                    $features = [];
                                                }
                                                $orderUrl = $specs['order_url'] ?? 'https://siteworx.in/';
                                                $buttonText = $specs['button_text'] ?? 'Order Now';
                                                $monthlyPrice = (float)($plan['price_monthly'] ?? 0);
                                                $yearlyPrice = (float)($plan['price_yearly'] ?? 0);
                                                ?>
                                                <div class="col-lg-3 col-md-6 col-sm-12">
                                                    <div class="pricing-s1 pricing-gsuite mb30">
                                                        <?php if ($index === 1): ?>
                                                            <div class="ribbon">HOT</div>
                                                        <?php endif; ?>
                                                        <div class="top">
                                                            <h2><?php echo htmlspecialchars($plan['name']); ?></h2>
                                                            <p class="price">
                                                                <?php if ($monthlyPrice > 0): ?>
                                                                    <span class="txt">Start from</span>
                                                                    <span class="currency"><?php echo htmlspecialchars($plan['currency'] ?? 'INR'); ?></span>
                                                                    <span class="m opt-1"><?php echo number_format($monthlyPrice, 0); ?></span>
                                                                    <span class="y opt-2"><?php echo number_format($yearlyPrice > 0 ? $yearlyPrice : ($monthlyPrice * 12), 0); ?></span>
                                                                    <span class="month">p/mo</span>
                                                                <?php else: ?>
                                                                    <span><a href="<?php echo htmlspecialchars($orderUrl); ?>" class="btn-custom"><?php echo htmlspecialchars($buttonText); ?></a></span>
                                                                <?php endif; ?>
                                                            </p>
                                                        </div>
                                                        <div class="bottom">
                                                            <ul>
                                                                <?php foreach ($features as $feature): ?>
                                                                    <li><i class="fa fa-check-square"></i><?php echo htmlspecialchars($feature); ?></li>
                                                                <?php endforeach; ?>
                                                                <?php if (isset($specs['email'])): ?>
                                                                    <li><i class="fa fa-check-square"></i><?php echo htmlspecialchars($specs['email']); ?></li>
                                                                <?php endif; ?>
                                                                <?php if (isset($specs['meetings'])): ?>
                                                                    <li><i class="fa fa-check-square"></i><?php echo htmlspecialchars($specs['meetings']); ?></li>
                                                                <?php endif; ?>
                                                                <?php if (isset($specs['storage'])): ?>
                                                                    <li><i class="fa fa-check-square"></i><?php echo htmlspecialchars($specs['storage']); ?></li>
                                                                <?php endif; ?>
                                                                <?php if (isset($specs['security'])): ?>
                                                                    <li><i class="fa fa-check-square"></i><?php echo htmlspecialchars($specs['security']); ?></li>
                                                                <?php endif; ?>
                                                                <?php if (isset($specs['support'])): ?>
                                                                    <li><i class="fa fa-check-square"></i><?php echo htmlspecialchars($specs['support']); ?></li>
                                                                <?php endif; ?>
                                                            </ul>
                                                        </div>
                                                        <div class="gsuit-action">
                                                            <a href="<?php echo htmlspecialchars($orderUrl); ?>" class="btn-custom"><?php echo htmlspecialchars($buttonText); ?></a>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                        <div class="col-lg-3 col-md-6 col-sm-12">
                                            <div class="pricing-s1 pricing-gsuite mb30">
                                                <div class="top">
                                                    <h2>Business Starter </h2>
                                                    <p class="price">
														<span class="txt">Start from</span>
														<span class="currency">INR</span>
														<span class="m opt-1">163</span>
														<span class="y opt-2">1950</span>
														<span class="month">p/mo</span>
													</p>               
                                                </div>
                                                
												
                                                <div class="bottom">

                                                    <ul>
                                                        <li><i class="fa fa-check-square"></i>Custom and secure business email</li>
                                                        <li><i class="fa fa-check-square"></i>100 participant video meetings</li>
                                                        <li><i class="fa fa-check-square"></i>30 GB pooled storage per user**</li>
                                                        <li><i class="fa fa-check-square"></i>Security and management controls</li>
                                                        <li><i class="fa fa-check-square"></i>Standard support</li>
                                                        
                                                    </ul>
                                                </div>
                                                <div class="gsuit-action">
													<a href="https://siteworx.in/" class="btn-custom">Order Now</a>
													<!--<a href="http://siteworx.in/manage/cart.php?a=add&pid=25" class="btn-custom">Order Now</a>-->
												</div>
												
												
                                            </div>
                                        </div>
                                        <div class="col-lg-3 col-md-6 col-sm-12">
                                            <div class="pricing-s1 pricing-gsuite mb30">
												<!--<div class="ribbon">HOT</div>-->
                                                <div class="top">
                                                    <h2>Business Standard </h2>
                                                    <p class="price">
														<span class="txt">Start from</span>
														<span class="currency">INR</span>
														<span class="m opt-1">580</span>
														<span class="y opt-2">6960</span>
														<span class="month">p/mo</span>
													</p>     
                                                </div>
                                                
                                                <div class="bottom">
                                                    <ul>
                                                        <li><i class="fa fa-check-square"></i>Custom and secure business email</li>
                                                        <li><i class="fa fa-check-square"></i>150-participant video meetings + recording</li>
                                                        <li><i class="fa fa-check-square"></i>2 TB pooled storage per user**</li>
                                                        <li><i class="fa fa-check-square"></i>Security and management controls</li>
                                                        <li><i class="fa fa-check-square"></i>Standard support (paid upgrade to enhanced support)</li>
                                                       
                                                    </ul>
                                                </div>
                                                	<div class="gsuit-action">
													<a href="https://siteworx.in/" class="btn-custom">Order Now</a>
													<!--<a href="http://siteworx.in/manage/cart.php?a=add&pid=26" class="btn-custom">Order Now</a>-->
												</div>
												
											
                                            </div>
                                        </div>
										<div class="col-lg-3 col-md-6 col-sm-12">
                                            <div class="pricing-s1 pricing-gsuite mb30">
                                                <div class="top">
                                                    <h2>Business Plus</h2>
                                                    <p class="price">
														<span class="txt">Start from</span>
														<span class="currency">INR</span>
														<span class="m opt-1">999</span>
														<span class="y opt-2">11988</span>
														<span class="month">p/mo</span>
													</p>     
                                                </div>
                                                
                                                <div class="bottom">
                                                    <ul>
                                                        <li><i class="fa fa-check-square"></i>Custom and secure business email + ediscovery, retention</li>
                                                        <li><i class="fa fa-check-square"></i>500 participant video meetings + recording, attendance tracking</li>
                                                        <li><i class="fa fa-check-square"></i>5 TB pooled storage per user**</li>
                                                        <li><i class="fa fa-check-square"></i>Enhanced security and management controls, including Vault and advanced endpoint management</li>
                                                        <li><i class="fa fa-check-square"></i>Standard support (paid upgrade to enhanced support)</li>
                                                       
                                                    </ul>
                                                </div>
                                                <div class="gsuit-action">
													<a href="https://siteworx.in/" class="btn-custom">Order Now</a>
													<!--<a href="http://siteworx.in/manage/cart.php?a=add&pid=27" class="btn-custom">Order Now</a>-->
												</div>
												
												
                                            </div>
                                        </div>
                                    
                                    <div class="col-lg-3 col-md-6 col-sm-12">
                                            <div class="pricing-s1 pricing-gsuite mb30">
                                                <div class="top">
                                                    <h2>Enterprise </h2>
                                                    
                                                    <p class="price">
														<span class="txt">Start from</span>
                                                        <span><a href="https://siteworx.in/" class="btn-custom">Contact Sales</a></span>
													<!--	<span class="currency">INR</span>-->
													<!--	<span class="m opt-1"></span>-->
													<!--	<span class="y opt-2"></span>-->
													<!--	<span class="month">p/mo</span>-->
													</p>     
                                                </div>
                                                
                                                <div class="bottom">
                                                    <ul>
                                                        <li><i class="fa fa-check-square"></i>Custom and secure business email + eDiscovery, retention, S/MIME encryption</li>
                                                        <li><i class="fa fa-check-square"></i>1000 participant video meetings + recording, attendance tracking, noise cancellation, in-domain live streaming</li>
                                                        <li><i class="fa fa-check-square"></i>5 TB pooled storage per user, with ability to request more**</li>
                                                        <li><i class="fa fa-check-square"></i>Advanced security, management and compliance controls, including Vault, DLP, data regions and enterprise endpoint management</li>
                                                        <!--<li><i class="fa fa-check-square"></i>Enhanced support (paid upgrade to Premium Support)</li>-->
                                                 
                                                    </ul>
                                                </div>
                                                <div class="gsuit-action"> <a href="https://siteworx.in/" class="btn-custom">Contact Sales</a>
													<!--<a href="http://siteworx.in/manage/cart.php?a=add&pid=28" class="btn-custom">Contact Sales</a>-->
												</div>
												
												
                                            </div>
                                        </div>
                                        <?php endif; ?>
                                    
                                    </div>
                                </div>
            </section>
 
            
            
            
            
            
            
          
			<!--<section id="plans" class="pb40">-->
   <!--             <div class="container">-->
   <!--                                 <div class="row">-->
   <!--                                     <div class="col-md-12 wow fadeInUp">-->
   <!--                         <div class="text-center">-->
   <!--                             <h1 style="color:#2d72e9">India's Best Gsuite Provider</h1>-->
   <!--                             <h2><span class="uptitle id-color">Select Your</span> Gsuite Plans</h2>-->
   <!--                             <div class="spacer-20"></div>-->
   <!--                         </div>-->
   <!--                     </div>-->
   <!--                                     <div class="col-lg-3 col-md-6 col-sm-12 wow fadeInUp">-->
   <!--                         <div  class="pricing-s2 bg rec mb30 text-light" data-bgimage="url(images/misc/bg-small-1.jpg)">-->
   <!--                             <div class="top">-->
                                    <!-- <img src="images/big-icon-cloud-hosting.png" class="size96" alt=""> -->
   <!--                                 <div class="inner">-->
   <!--                                     <div class="clearfix"></div>-->
   <!--                                     <h2>Business Starter Plan</h2>-->
   <!--                                     <p>Foremost webhosting plan for any beginner developer</p>-->
   <!--                                    <table class="table ">-->
			<!--														<tbody>-->
			<!--														  <tr><td>Custom and secure business email</td> </tr>-->
			<!--														  <tr><td><strong>100-</strong>participant video meetings</td> </tr>-->
			<!--														  <tr>	<td><strong> 30 GB</strong>  pooled storage per user**</td></tr>-->
			<!--														  <tr>	<td> Security and management controls </td></tr>-->
			<!--														  <tr>	<td> Standard support </td></tr>-->
																	  <!--<tr>	<td> <strong>1</strong> MySQL Database </td></tr>-->
			<!--														</tbody>-->
			<!--														</table>-->
			<!--						  <p class="price"><span class="txt">Start from</span><span class="currency">INR</span>-->
   <!--                                         <span class="num opt-1">139</span>-->
   <!--                                         <span class="num opt-2">119</span>-->
   <!--                                         <span class="month">p/mo</span></p>-->
   <!--                                     <a href="http://siteworx.in/manage/cart.php?a=add&pid=2" class="btn-custom">Order Now</a>-->
   <!--                                 </div>-->
   <!--                             </div>-->
   <!--                         </div>-->
   <!--                     </div>-->
   <!--                     <div class="col-lg-3 col-md-6 col-sm-12 wow fadeInUp">-->
   <!--                         <div class="pricing-s2 mb30">-->
   <!--                             <div class="inner">-->
   <!--                                 <div class="top">-->
                                        <!-- <img src="images/icon-w-vps-hosting.png" class="size96" alt=""> -->
                                        <!-- <div class="ribbon">POPULAR</div> -->
   <!--                                     <div class="clearfix"></div>-->
   <!--                                     <h2>Business Standard Plan</h2>-->
   <!--                                     <p>For Growing Businesses with high traffic and unlimited resources</p>-->
			<!--							 <table class="table ">-->
			<!--														<tbody>-->
			<!--														  <tr><td>Custom and secure business email </tr>-->
			<!--														  <tr><td><strong>150-</strong>participant video meetings + recording</td> </tr>-->
			<!--														  <tr>	<td><strong>2 TB</strong> pooled storage per user**</td></tr>-->
			<!--														  <tr>	<td> Security and management controls </td></tr>-->
			<!--														  <tr>	<td> Standard support(paid upgrade to enhanced support)  </td></tr>-->
																	  <!--<tr>	<td> <strong>Unlimited</strong> MYSQL Database </td></tr>-->
			<!--														</tbody>-->
			<!--													  </table>-->
																										
									 
   <!--                                     <p class="price"><span class="txt">Start from</span><span class="currency">INR</span>-->
   <!--                                         <span class="num opt-1">199</span>-->
   <!--                                         <span class="num opt-2">189</span>-->
   <!--                                         <span class="month">p/mo</span></p>-->
   <!--                                     <a href="http://siteworx.in/manage/cart.php?a=add&pid=3" class="btn-custom">Order Now</a>-->
   <!--                                     <div class="clearfix"></div>-->
   <!--                                 </div>-->
   <!--                             </div>-->
   <!--                         </div>-->
   <!--                     </div>-->
   <!--                     <div class="col-lg-3 col-md-6 col-sm-12 wow fadeInUp">-->
   <!--                         <div class="pricing-s2 mb30">-->
   <!--                             <div class="inner">-->
   <!--                                 <div class="top">-->
                                        <!-- <img src="images/icon-cloud-hosting.png" class="size96" alt=""> -->
   <!--                                     <div class="clearfix"></div>-->
   <!--                                     <h2>Business Plus</h2>-->
   <!--                                     <p>For Multiple Sites handler and unlimited resources</p>-->
			<!--							 <table class="table ">-->
			<!--														<tbody>-->
			<!--														  <tr><td>Custom and secure business email + ediscovery, retention</td> </tr>-->
			<!--														  <tr><td><strong>500</strong>  participant video meetings + recording, attendance tracking</td> </tr>-->
			<!--														  <tr>	<td><strong>5 TB</strong> pooled storage per user**</td></tr>-->
			<!--														  <tr>	<td> Enhanced security and management controls, including Vault and advanced endpoint management </td></tr>-->
			<!--														  <tr>	<td> Standard support (paid upgrade to enhanced support) </td></tr>-->
																	  <!--<tr>	<td> <strong>Unlimited</strong> MYSQL Database </td></tr>-->
			<!--														</tbody>-->
			<!--														</table>-->
   <!--                                     <p class="price"><span class="txt">Start from</span><span class="currency">INR</span>-->
   <!--                                         <span class="num opt-1">299</span>-->
   <!--                                         <span class="num opt-2">269</span>-->
   <!--                                         <span class="month">p/mo</span></p>-->
   <!--                                     <a href="http://siteworx.in/manage/cart.php?a=add&pid=4" class="btn-custom mb10">Order Now</a>-->
   <!--                                 </div>-->
   <!--                             </div>-->
   <!--                         </div>-->
   <!--                     </div>-->

   <!-- <div class="col-lg-3 col-md-6 col-sm-12 wow fadeInUp">-->
   <!--                         <div class="pricing-s2 mb30">-->
   <!--                             <div class="inner">-->
   <!--                                 <div class="top"6-->
                                        <!-- <img src="images/big-icon-vps-hosting.png" class="size96" alt="">
   <!--                                     <div class="clearfix"></div>-->
   <!--                                     <h2>Enterprise Plan</h2>-->
   <!--                                     <p>Acquire Most from the packages by hosting unlimited websites</p>-->
			<!--							 <table class="table ">-->
			<!--														<tbody>-->
			<!--														  <tr><td>Custom and secure business email + eDiscovery, retention, S/MIME encryption</td> </tr>-->
			<!--														  <tr><td><strong>1000 </strong> participant video meetings + recording, attendance tracking, noise cancellation, in-domain live streaming</td> </tr>-->
			<!--														  <tr>	<td><strong>5 TB</strong> pooled storage per user, with ability to request more**</td></tr>-->
			<!--														  <tr>	<td> Advanced security, management and compliance controls, including Vault, DLP, data regions and enterprise endpoint management </td></tr>-->
			<!--														  <tr>	<td> Enhanced support (paid upgrade to Premium Support) </td></tr>-->
																	  <!--<tr>	<td> <strong>Unlimited</strong> MYSQL Database </td></tr>-->
			<!--														</tbody>-->
			<!--														</table>-->
																	
   <!--                                     <p class="price"><span class="txt">Start from</span><span class="currency">INR</span>-->
   <!--                                         <span class="num opt-1">439</span>-->
   <!--                                         <span class="num opt-2">399</span>-->
   <!--                                         <span class="month">p/mo</span></p>-->
   <!--                                     <a href="http://siteworx.in/manage/cart.php?a=add&pid=5" class="btn-custom mb10">Order Now</a>-->
   <!--                                 </div>-->
   <!--                             </div>-->
   <!--                         </div>-->
   <!--                     </div>-->
   
   <!--                     </section>-->

                
<hr>
			  <section id="section-features">
                <div class="container">
                    <div class="row">
                        <div class="col-md text-center wow fadeInUp">
                            <h2><span class="uptitle id-color">Build For Your Business </span>Google WorkSpace Services </h2>
                            <div class="spacer-20"></div>
                        </div>
                    </div>
                    <div class="row wow fadeInUp">
                        <div class="col-md col-sm-6 mb-sm-30">
                            <div class="feature-box-type-3">
                                <img src = "images/calaender.png">
                                <h4> calander</h4>
                            </div>
                        </div>

                        <div class="col-md col-sm-6 mb-sm-30">
                            <div class="feature-box-type-3">
                                 <img src = "images/google-drive.png">
                                <h4>google-drive</h4>
                            </div>
                        </div>

                        <div class="col-md col-sm-6 mb-sm-30">
                            <div class="feature-box-type-3">
                                 <img src = "images/google-meet.png">
                                <h4>google-meet</h4>
                            </div>
                        </div>

                        <div class="col-md col-sm-6 mb-sm-30">
                            <div class="feature-box-type-3">
                                <img src = "images/google-sheet.png">
                                <h4> google-sheet</h4>
                            </div>
                        </div>
                        <div class="col-md col-sm-6 mb-sm-30">
                            <div class="feature-box-type-3">
                                 <img src = "images/google-docs.png">
                                <h4>google-docs</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            
            
            <section>
               <div class="container">
               <div class = "googlework2" style=" background-color: whitesmoke; text-aline: center">
                   <img src="images/background/anim2.gif" style="background-size:cover; width: 100%;">
               </div>
               </div>
           </section>
            
            
            
            
        <!--    <section class="no-top no-bottom text-light " data-bgimage="url(images/background/anim2.gif)" >-->
        <!--        <div class="overlay-gradient ">-->
        <!--            <div class="center-y mt50">-->
        <!--                <div class="container">-->
        <!--                    <div class="row align-items-center">-->
								<!--<div class="col-lg-6 col-12 text-lg-left text-center mb-sm-30">-->
								<!--	<h1 style="color:#fff" >Google Workspace<span class="label">5% OFF</span></h1>-->
								<!--	<p class="lead"> Google Workspace is designed to support stringent privacy and security standards based on industry best practices. </p>-->
								<!--	<div class="spacer-half"></div>-->
								<!--	<a class="btn-custom" href="#plans">See All Plans</a>-->
								<!--</div>-->
								
								<!--<div class="col-lg-3 offset-lg-2 col-4 offset-4 text-center">-->
								<!--	<img src="images/big-icon-shared-hosting.png" class="img-fluid" alt="">-->
								<!--</div>-->
        <!--                    </div>-->
        <!--                </div>-->
        <!--            </div>-->
        <!--        </div>-->
        <!--    </section>-->
            
            
             <section id="section-features">
                <div class="container">
                    <div class="row">
                        <div class="col-md text-center wow fadeInUp">
                            <h3 style="font-size: 30px; font-weight: 800">What to Look for in a Cheap price Google Workspace Provider 
</h3>
                            <p  style="fobt-size: 20px; color: black; padding: 0px 30px; font-weight: 500">Google Workspace (formerly G Suite) is a suite of cloud-based productivity and collaboration tools developed by Google. It includes various applications designed to help individuals and businesses streamline their work processes, communicate effectively, and collaborate seamlessly. Some key components of Google Workspace include:
</p>

                            <!--<h2><span class="uptitle id-color">Build For Speed</span>Why SiteWorx for web hosting service in India?</h2>-->
                            <div class="spacer-20"></div>
                        </div>
                    </div>
                    <div class="row wow fadeInUp">
                        <div class="col-md-4 col-sm-6 mb-sm-30">
                            <div class="feature-box-type-2" style="margin-bottom: 20px;">
                                <img src="images/google-gmail.png" style = "padding:15px;">
                                <h4>Gmail</h4>
                                <p class="text-justify" style="padding-bottom: 25px;">A powerful email service with advanced features such as custom email addresses, spam filtering, and integration with other Google services.
</p>
                            </div>
                        </div>

                        <div class="col-md-4 col-sm-6 mb-sm-30">
                            <div class="feature-box-type-2">
                                <img src="images/google-drive.png" style = "padding:15px;">
                                <h4>Google Drive</h4>
                                <p class="text-justify"  >A cloud storage service that allows users to store, access, and share files from any device. It also includes collaborative features such as real-time editing and commenting.</p>
                            </div>
                        </div>

                        <div class="col-md-4 col-sm-6 mb-sm-30">
                            <div class="feature-box-type-2">
                                <img src="images/google-docs.png" style = "padding:15px;">
                                <h4>Google Docs</h4>
                                <p class="text-justify" style="padding-bottom: 25px;">A word processing application that enables users to create, edit, and collaborate on documents in real-time. It supports features such as version history and offline editing.</p>
                            </div>
                        </div>

                        <div class="col-md-4 col-sm-6 mb-sm-30">
                            <div class="feature-box-type-2">
                                <img src="images/google-sheet.png" style = "padding:15px;">
                                <h4>Google Sheets</h4>
                                <p class="text-justify" >A spreadsheet application similar to Microsoft Excel, which allows users to create, edit, and It supports features such as analyze data collaboratively and integration with other Google services.</p>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-6 mb-sm-30 mt-1">
                            <div class="feature-box-type-2">
                                <img src="images/google-meet.png" style = "padding:15px;">
                                <h4>Google Meet</h4>
                                <p class="text-justify"  >A video conferencing service that allows users to host virtual meetings, webinars, and presentations. It supports features such as screen sharing, chat, and integration with Google Calendar.
</p>
                            </div>
                        </div>
                          <div class="col-md-4 col-sm-6 mb-sm-30 mt-1">
                            <div class="feature-box-type-2">
                                <img src="images/calaender.png" style = "padding:15px;">
                                <h4>Google Calendar</h4>
                                <p class="text-justify" >A calendar application that helps users schedule appointments, meetings, and events. It includes features such as reminders, notifications, and integration with other Google services.
</p>
</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            
           <section>
               <div class="container">
               <div class = "googlework2" style=" background-color: whitesmoke; text-aline: center">
                   <img src="images/background/googlework2.jpg" style="background-size:cover; width: 100%;">
               </div>
               </div>
           </section>
           
           
           <section id="section-features">
                <div class="container">
                    <div class="row">
                        <div class="col-md text-center wow fadeInUp">
                            <h2><span class="uptitle id-color">Pricing and plans for Google Workspace </span>Google WorkSpace Services </h2>
                            <div class="spacer-20"></div>
                        </div>
                    </div>
                    <div class="row wow fadeInUp">
                        <div class="col-md col-sm-6 mb-sm-30">
                            <div class="feature-box-type-3">
                                
                                <h4> Best for Small Businesses</h4>
                            </div>
                        </div>

                        <div class="col-md col-sm-6 mb-sm-30">
                            <div class="feature-box-type-3">
                                 
                                <h4>Ideal for Growing Teams</h4>
                            </div>
                        </div>

                        <div class="col-md col-sm-6 mb-sm-30">
                            <div class="feature-box-type-3">
                                 
                                <h4>Custom Solutions for Large Enterprises</h4>
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
            <section id="section-features">
                <div class="container">
                    <div class="row">
                        <div class="col-md text-center wow fadeInUp">
                            <h2><span class="uptitle id-color">Google Service Terms </h2>
                            <p>Any upgradations by google during the tenure will be applicable to end client.</p>
                            <div class="spacer-20"></div>
                        </div>
                    </div>
                    </section>
           
        <!-- content close -->


   <?php include('footer.php'); ?>
            <!-- footer begin -->
