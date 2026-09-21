<!-- Header  -->
<?php 
include "header.php";

$page_address = getSetting('address', 'Orbit Plaza, Crossing Republik, NH-24, Ghaziabad, Uttar Pradesh, 201016');
$page_phone_1 = getSetting('phone_1', '+919650386711');
$page_phone_2 = getSetting('phone_2', '');
$page_email_1 = getSetting('email_1', 'hello@finchskills.com');
$page_email_2 = getSetting('email_2', 'admission@finchskills.com');
$timing_mf    = getSetting('timing_mon_fri', 'Monday - Friday: 10:00 - 05:00');
$timing_sat   = getSetting('timing_sat', 'Saturday: 10:00 - 02:00');
$map_url      = getSetting('map_iframe', 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3501.9501409758395!2d77.43262847457278!3d28.63125638414035!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x390cee300c363997%3A0xe53a3aaacf648c83!2sOrbit%20plaza%2C%20Crossings%20Republik%2C%20Ghaziabad%2C%20Uttar%20Pradesh%20201016!5e0!3m2!1sen!2sin!4v1784362846035!5m2!1sen!2sin');
?>

<!-- ===================================== -->
<!-- BLOG HERO -->
<!-- ===================================== -->

<section class="contact-hero-section">
    <div class="container">
        <h1 class="contact-hero-title">
            Contact Finchskill Institute
        </h1>
        <div class="bloghero-breadcrumb text-start">
            <a href="index.php">Home</a>
            <span>
                <i class="fa-solid fa-chevron-right"></i>
            </span>
            <a href="#">Contact Us</a>
        </div>
    </div>
</section>

<!-- CONTACT INFO SECTION -->
<section class="custom-contact-info-section">
    <div class="container px-lg-4">
        <div class="row g-4">

            <!-- CARD 1: ADDRESS -->
            <div class="col-lg-4 col-md-4">
                <div class="custom-contact-card h-100">
                    <div class="custom-contact-icon custom-contact-blue">
                        <i class="fa-solid fa-map-location-dot"></i>
                    </div>
                    <h3>Location</h3>
                    <p><?= htmlspecialchars($page_address) ?></p>
                </div>
            </div>

            <!-- CARD 2: CONTACT INFO -->
            <div class="col-lg-4 col-md-4">
                <div class="custom-contact-card h-100">
                    <div class="custom-contact-icon custom-contact-green">
                        <i class="fa-solid fa-id-card"></i>
                    </div>
                    <h3>Contact Info</h3>
                    <?php if (!empty($page_phone_1)): ?>
                    <p>
                        <a href="tel:<?= htmlspecialchars($page_phone_1) ?>" style="color: #666666;">
                            Mobile: <?= htmlspecialchars($page_phone_1) ?>
                        </a>
                    </p>
                    <?php endif; ?>
                    <?php if (!empty($page_phone_2)): ?>
                    <p>
                        <a href="tel:<?= htmlspecialchars($page_phone_2) ?>" style="color: #666666;">
                            Alt: <?= htmlspecialchars($page_phone_2) ?>
                        </a>
                    </p>
                    <?php endif; ?>
                    <?php if (!empty($page_email_1)): ?>
                    <p>
                        <a href="mailto:<?= htmlspecialchars($page_email_1) ?>" style="color: #666666;">
                            Mail: <?= htmlspecialchars($page_email_1) ?>
                        </a>
                    </p>
                    <?php endif; ?>
                    <?php if (!empty($page_email_2)): ?>
                    <p>
                        <a href="mailto:<?= htmlspecialchars($page_email_2) ?>" style="color: #666666;">
                            Mail: <?= htmlspecialchars($page_email_2) ?>
                        </a>
                    </p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- CARD 3: WORK TIMER -->
            <div class="col-lg-4 col-md-4">
                <div class="custom-contact-card h-100">
                    <div class="custom-contact-icon custom-contact-yellow">
                        <i class="fa-regular fa-clock"></i>
                    </div>
                    <h3>Work Timer</h3>
                    <?php if (!empty($timing_mf)): ?>
                    <p><?= htmlspecialchars($timing_mf) ?></p>
                    <?php endif; ?>
                    <?php if (!empty($timing_sat)): ?>
                    <p><?= htmlspecialchars($timing_sat) ?></p>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- CONTACT FORM SECTION -->
<section class="py-5" id="contact-form" style="background: #f8fafc;">
    <div class="container px-lg-4">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm p-4 p-md-5 rounded-4 bg-white">
                    <div class="text-center mb-4">
                        <span class="badge bg-primary-subtle text-primary px-3 py-2 rounded-pill fw-semibold mb-2">Get in Touch</span>
                        <h2 class="fw-bold text-dark">Send Us a Message</h2>
                        <p class="text-muted">Have queries about courses or admission? Fill the form below and we will get back to you shortly.</p>
                    </div>

                    <?php if (isset($_GET['success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show d-flex align-items-center mb-4" role="alert">
                        <i class="fa-solid fa-circle-check fs-4 me-2"></i>
                        <div>
                            <strong>Thank you!</strong> Your message has been sent successfully. We will contact you soon.
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php endif; ?>

                    <?php if (isset($_GET['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center mb-4" role="alert">
                        <i class="fa-solid fa-circle-exclamation fs-4 me-2"></i>
                        <div><?= htmlspecialchars($_GET['error']) ?></div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php endif; ?>

                    <form action="send-contact.php" method="POST">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Your Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control form-control-lg fs-6" placeholder="e.g. Rahul Sharma" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Email Address <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control form-control-lg fs-6" placeholder="name@example.com" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Phone Number</label>
                                <input type="tel" name="phone" class="form-control form-control-lg fs-6" placeholder="+91 9876543210">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Subject</label>
                                <input type="text" name="subject" class="form-control form-control-lg fs-6" placeholder="e.g. Admission Query">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Message <span class="text-danger">*</span></label>
                                <textarea name="message" class="form-control fs-6" rows="5" placeholder="Write your inquiry or question here..." required></textarea>
                            </div>
                            <div class="col-12 text-center mt-4">
                                <button type="submit" class="btn btn-primary btn-lg px-5 py-3 fw-bold rounded-pill shadow-sm" style="background-color: #0b57d0; border-color: #0b57d0;">
                                    <i class="fa-solid fa-paper-plane me-2"></i> Submit Inquiry
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- MAP SECTION -->
<section class="custom-map-section py-4">
    <div class="container px-lg-5">
        <div class="custom-map-wrap rounded-4 overflow-hidden shadow-sm">
            <iframe src="<?= htmlspecialchars($map_url) ?>" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="strict-origin-when-cross-origin"></iframe>
        </div>
    </div>
</section>

<!-- FOOTER  -->
<?php include "footer.php" ?>