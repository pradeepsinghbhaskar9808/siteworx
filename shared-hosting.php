 <?php 
     $pageTitle = "Best Hosting Providers in India,Cheap services and Domains";

   $canonical='https://www.siteworx.in/shared-hosting-linux"';
   $Metadescription="Find the perfect plan to fit your website's needs. High-performance servers, automatic backups, and a user-friendly control panel. Web Hosting,Hosting Services,Domain Hosting,Website Hosting,Shared Hosting,VPS Hosting ,Dedicated Server Hosting,Cloud Hosting,Managed Hosting,Reseller Hosting. Buy Now!";
  $metakeywords="Web hosting in India, Best web hosting in India, cheapest web hosting in india, web hosting company in india, best web hosting company in india, web hosting india, web hosting services in india, domain name in india, domain name, web hosting companies India, email hosting, best web hosting, cheap web hosting, Bulk Email Marketing Server, Best web development in india, web development in india, web design in india, Best SEO Service in India, Best ADS Service In india, Best Facebook ADS in india, Best web Development In Jaipur, Best Instagram ADS In India";
   include('header.php');
   ?>
        <!-- content begin -->
 <!-- content begin -->
        <div id="content" class="no-top no-bottom">
           
            <section class="no-top no-bottom text-light" data-bgimage="url(images/background/server-cabinets-banner.jpg) center" data-stellar-background-ratio=".2">
                <div class="overlay-gradient t80">
                    <div class="center-y mt50">
                        <div class="container">
                            <div class="row align-items-center">
								<div class="col-lg-6 col-12 text-lg-left text-center mb-sm-30">
									<h1 style='color:white '>Shared Hosting<span class="label">5% OFF </span></h1>
									<p class="lead"> We offer the best hosting solutions for your needs, catering to clients from personal to corporate. Our data centers are strategically located worldwide to ensure that your website is always up. Happy hosting! </p>
									<div class="spacer-half"></div>
									<a class="btn-custom" href="#plans">See All Plans</a>
								</div>
								
								<div class="col-lg-3 offset-lg-2 col-4 offset-4 text-center">
									<img src="images/cloud-network-icon.gif" class="img-fluid" alt="" style="opacity: 0.5;">
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
                                <h1 style="color:#115256">India's Best Shared Hosting Provider</h1>
                                <h2><span class="uptitle id-color">Select Your</span>  Shared Hosting Plans</h2>
                                <div class="spacer-20"></div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <?php
                        $plans = [];
                        if (isset($pdo)) {
                            try {
                                $stmt = $pdo->prepare("SELECT * FROM hosting_plans WHERE category = :cat AND status='active' ORDER BY price_monthly ASC");
                                $stmt->execute([':cat' => 'shared']);
                                $plans = $stmt->fetchAll();
                            } catch (Exception $e) {
                                // fallback empty
                                $plans = [];
                            }
                        }

                        if (empty($plans)) {
                            echo '<div class="col-12">No plans found.</div>';
                        } else {
                            foreach ($plans as $plan) {
                                $specs = json_decode($plan['specs'], true) ?: [];
                                ?>
                                <div class="col-lg-3 col-md-6 col-sm-12 wow fadeInUp">
                                    <div class="pricing-s1 mb30">
                                        <div class="top">
                                            <h2><?php echo htmlspecialchars($plan['name']); ?></h2>
                                            <p class="price"><span class="txt">Start from</span>
                                                <span class="currency"><?php echo htmlspecialchars($plan['currency'] ?? 'INR'); ?></span>
                                                <span class="m"><?php echo number_format($plan['price_monthly'],2); ?></span>
                                                <span class="month">p/mo</span></p>
                                        </div>
                                        <div class="bottom">
                                            <ul>
                                                <?php if (!empty($specs['websites'])): ?><li><i class="fa fa-check-square"></i><?php echo htmlspecialchars($specs['websites']) . ' Websites'; ?></li><?php endif; ?>
                                                <?php if (!empty($specs['disk'])): ?><li><i class="fa fa-check-square"></i><?php echo htmlspecialchars($specs['disk']) . ' Disk Space'; ?></li><?php endif; ?>
                                                <?php if (!empty($specs['bandwidth'])): ?><li><i class="fa fa-check-square"></i><?php echo htmlspecialchars($specs['bandwidth']) . ' Bandwidth'; ?></li><?php endif; ?>
                                                <?php if (!empty($specs['emails'])): ?><li><i class="fa fa-check-square"></i><?php echo htmlspecialchars($specs['emails']) . ' Email Accounts'; ?></li><?php endif; ?>
                                                <?php if (!empty($specs['subdomains'])): ?><li><i class="fa fa-check-square"></i><?php echo htmlspecialchars($specs['subdomains']) . ' Sub Domains'; ?></li><?php endif; ?>
                                                <?php if (!empty($specs['databases'])): ?><li><i class="fa fa-check-square"></i><?php echo htmlspecialchars($specs['databases']) . ' MySQL Database'; ?></li><?php endif; ?>
                                            </ul>
                                        </div>
                                        <div class="action">
                                            <a href="#" class="btn-custom mb10">Order Now</a>
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
             <section id="section-features">
                <div class="container">
                    <div class="row">
                        <div class="col-md text-center wow fadeInUp">
                            <h3 style="font-size: 30px; font-weight: 800">What to Look for in a Cheap Web Hosting Provider 
</h3>
                            <p  style="fobt-size: 20px; color: black; padding: 0px 30px; font-weight: 500">If you are building your business’s website you must be facing this question – “What to look for in a cheap web hosting provider?”. With multiple options in the market, it can be challenging to look for the best and cheap web hosting India. Knowing what features you need can help you find a cheap web hosting. Below are the 6 considerations for choosing a web service provider for your business:
</p>

                            <!--<h2><span class="uptitle id-color">Build For Speed</span>Why SiteWorx for web hosting service in India?</h2>-->
                            <div class="spacer-20"></div>
                        </div>
                    </div>
                    <div class="row wow fadeInUp">
                        <div class="col-md-4 col-sm-6 mb-sm-30">
                            <div class="feature-box-type-2" style="margin-bottom: 20px;">
                                <i class="icon-alarmclock"></i>
                                <h4>Server Performance</h4>
                                <p class="text-justify" style="padding-bottom: 25px;">Underperforming servers can impact your user’s experience. Look for a provider with optimized resources and has a proven track record.
</p>
                            </div>
                        </div>

                        <div class="col-md-4 col-sm-6 mb-sm-30">
                            <div class="feature-box-type-2">
                                <i class="icon-profile-male"></i>
                                <h4>Uptime Guarantee</h4>
                                <p class="text-justify"  >There’s nothing more disappointing than seeing the website not open. It doesn’t just impact the user experience but also the website's SEO. So, find web hosting solutions with uptime closer to 100%.</p>
                            </div>
                        </div>

                        <div class="col-md-4 col-sm-6 mb-sm-30">
                            <div class="feature-box-type-2">
                                <i class="icon-refresh"></i>
                                <h4>Response Time</h4>
                                <p class="text-justify" style="padding-bottom: 25px;">53% of users leave the website if the response time is over 3 seconds. So, like uptime, ensure the web service provider guarantees an efficient response time.</p>
                            </div>
                        </div>

                        <div class="col-md-4 col-sm-6 mb-sm-30">
                            <div class="feature-box-type-2">
                                <i class="icon-upload"></i>
                                <h4>Server Location</h4>
                                <p class="text-justify" >If your target audience is from one area, it’s better to choose a cheap hosting server with their data center nearby. That way your website will load faster to your visitors than usual!</p>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-6 mb-sm-30 mt-1">
                            <div class="feature-box-type-2">
                                <i class="icon-layers"></i>
                                <h4>Customer Support</h4>
                                <p class="text-justify"  >More than promising all the features, the best hosting must offer fast customer support. Their ability to resolve queries in minutes makes a big difference in making your website a success.
</p>
                            </div>
                        </div>
                          <div class="col-md-4 col-sm-6 mb-sm-30 mt-1">
                            <div class="feature-box-type-2">
                                <i class="icon-layers"></i>
                                <h4>Support Channels</h4>
                                <p class="text-justify" >Look for a web service provider offering multiple support channels, such as email, phone, and live chat. Also ensure the support team is available on these platforms 24/7 to assist you with any issues.
</p>
</p>
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
        <section style='padding: 0'>
                  <div class="row">
                    <div class="col-md  wow fadeInUp" style="padding: 0px 80px"  >
                        <h3 class="uptitle id-color" style="padding: 20px 0px" >Why should I host my site on a Windows shared hosting?  </h3>
                        <p>When figuring the best plan for your business, you have 2 major server options: Linux and Windows hosting. Window hosting is one of the best options there. Though it isn’t suitable for everyone’s goals (for many of the new businesses launching themselves online, choosing a shared hosting based on Linux remains the obvious choice owing to affordability and reliability). But it provides unique advantages for businesses for which it is suitable i.e. Window hosting makes sense for large businesses looking to expand their website.</p>
                        
                        <ul>
                            <li>It operates on Windows Operating System</li>
                            <li>It operates on Windows Operating System</li>
                            <li>It offers a user-friendly Plesk Control Panel</li>
                        </ul>
                        <p>If your business depends more on Windows, then choosing {Windows shared hosting (https://www.siteworx.in/shared-hosting)}can be the best choice. The reason we say this is that the server will work smoothly with other programs you will be using. This also means that you won’t need to build anything from scratch. But before finalizing your decision, understand what your IT team thinks about choosing Windows shared hosting as the server. It is important to bring them into this discussion and let their opinion help you in choosing your web hosting requirement.</p>
                    </div>
                  </div>
              </section>
        <!-- content close -->


   <?php include('footer.php'); ?>
            <!-- footer begin -->