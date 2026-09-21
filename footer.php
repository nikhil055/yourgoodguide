<!-- ================= FOOTER ================= -->

    <footer class="footer-section">

        <div class="container">

            

            <!-- ================= MAIN ================= -->

            <div class="footer-main">

                <div class="row">

                    <!-- LOGO + CONTACT -->
                    <div class="col-lg-4">

                        <!-- LOGO -->

                        <div class="footer-logo">

                            <img src="<?= htmlspecialchars(getSetting('footer_logo', getSetting('site_logo', 'img/footer-logo.png'))) ?>" alt="Footer Logo">

                        </div>

                        <!-- CONTACT -->
                        <p class="footer-about-text">
                            Finchskills Institute is a modern learning platform offering
                            industry-focused courses, expert trainers, and flexible
                            learning experiences to help students build successful careers.
                        </p>
                        <!-- SOCIAL -->


                    </div>

                    <!-- Quick Links -->
                    <div class="col-lg-2 col-md-4 col-6 footer-column">

                        <h3 class="footer-heading">

                            Quick Links

                        </h3>

                        <ul class="footer-links">

                            <li><a href="index.php">Home</a></li>
                            <li><a href="about.php">About</a></li>
                            <li><a href="courses.php">Courses</a></li>
                            <li><a href="placement.php">Placement</a></li>
                            <li><a href="register.php">Register Yourself</a></li>
                            <li><a href="student-login.php">Student Portal</a></li>
                            <li><a href="contact.php">Contact</a></li>
                            <li><a href="terms-conditions.php">Terms & Conditions</a></li>

                        </ul>

                    </div>

                    <!-- Courses -->
                    <div class="col-lg-2 col-md-4 col-6 footer-column">

                        <h3 class="footer-heading">

                            Courses

                        </h3>

                        <ul class="footer-links">

                            <li><a href="">Travel & Tourism</a></li>
                            <li><a href="">Airport Management</a></li>
                            <li><a href="">Cruise Management</a></li>
                            <li><a href="">Hospitality & Customer Service</a></li>
                            
                        </ul>

                    </div>

                    <!-- RECOMMEND -->
                    <div class=" col-md-4 footer-column">

                        <h3 class="footer-heading">

                            Contact Info

                        </h3>

                        <ul class="footer-contact">

                            <?php if (!empty($site_phone_1)): ?>
                            <li>
                                <a href="tel:<?= htmlspecialchars($site_phone_1) ?>" style="color: #9e9e9e;">
                                    <i class="fa-solid fa-phone me-2"></i> <?= htmlspecialchars($site_phone_1) ?>
                                </a>
                            </li>
                            <?php endif; ?>
                            <?php if (!empty($site_phone_2)): ?>
                            <li>
                                <a href="tel:<?= htmlspecialchars($site_phone_2) ?>" style="color: #9e9e9e;">
                                    <i class="fa-solid fa-phone me-2"></i> <?= htmlspecialchars($site_phone_2) ?>
                                </a>
                            </li>
                            <?php endif; ?>

                            <li>
                                <i class="fa-solid fa-location-dot me-2"></i>
                                <?= htmlspecialchars(getSetting('address', 'Orbit Plaza, Crossing Republik, NH-24, Ghaziabad, Uttar Pradesh, 201016')) ?>
                            </li>

                            <?php if (!empty($site_email_1)): ?>
                            <li>
                                <a href="mailto:<?= htmlspecialchars($site_email_1) ?>" style="color: #9e9e9e;">
                                    <i class="fa-regular fa-envelope me-2"></i> <?= htmlspecialchars($site_email_1) ?>
                                </a>
                            </li>
                            <?php endif; ?>
                            <?php if (!empty($site_email_2)): ?>
                            <li>
                                <a href="mailto:<?= htmlspecialchars($site_email_2) ?>" style="color: #9e9e9e;">
                                    <i class="fa-regular fa-envelope me-2"></i> <?= htmlspecialchars($site_email_2) ?>
                                </a>
                            </li>
                            <?php endif; ?>

                        </ul>

                    </div>

                </div>

            </div>

            <!-- ================= BOTTOM ================= -->

            <div class="footer-bottom">

                <div class="row align-items-center g-4">

                    <div class="col-md-6">
                        <p class="copyright-text mb-0">
                            © <?= date('Y') ?> Finchskills Institute. All Rights Reserved.
                        </p>
                    </div>
                    <div class="col-md-6">
                        <div class="footer-social mt-0 justify-content-center justify-content-md-end gap-2">

                            <?php if ($fb = getSetting('facebook_url')): ?>
                            <a href="<?= htmlspecialchars($fb) ?>" target="_blank" rel="noopener noreferrer">
                                <i class="fa-brands fa-facebook-f"></i>
                            </a>
                            <?php endif; ?>

                            <?php if ($tw = getSetting('twitter_url')): ?>
                            <a href="<?= htmlspecialchars($tw) ?>" target="_blank" rel="noopener noreferrer">
                                <i class="fa-brands fa-x-twitter"></i>
                            </a>
                            <?php endif; ?>

                            <?php if ($insta = getSetting('instagram_url')): ?>
                            <a href="<?= htmlspecialchars($insta) ?>" target="_blank" rel="noopener noreferrer">
                                <i class="fa-brands fa-instagram"></i>
                            </a>
                            <?php endif; ?>

                            <?php if ($li = getSetting('linkedin_url')): ?>
                            <a href="<?= htmlspecialchars($li) ?>" target="_blank" rel="noopener noreferrer">
                                <i class="fa-brands fa-linkedin-in"></i>
                            </a>
                            <?php endif; ?>

                            <?php if ($yt = getSetting('youtube_url')): ?>
                            <a href="<?= htmlspecialchars($yt) ?>" target="_blank" rel="noopener noreferrer">
                                <i class="fa-brands fa-youtube"></i>
                            </a>
                            <?php endif; ?>

                            <?php if ($wa = getSetting('whatsapp_number')): ?>
                            <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $wa) ?>" target="_blank" rel="noopener noreferrer">
                                <i class="fa-brands fa-whatsapp"></i>
                            </a>
                            <?php endif; ?>

                        </div>
                    </div>






                    <!-- RIGHT -->


                </div>

            </div>

        </div>

    </footer>



    </body>

<!-- Bootstrap -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- jQuery (required for Owl Carousel) -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

<!-- Owl Carousel JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>

</body>

</html>