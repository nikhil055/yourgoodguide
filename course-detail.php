<?php
require_once __DIR__ . '/db.php';

$slug = trim($_GET['slug'] ?? '');
$id = (int)($_GET['id'] ?? 0);

// Lookup course
$course = null;
if (!empty($slug) || $id > 0) {
    $stmt = $pdo->prepare("SELECT cr.*, cat.name as category_name, cat.slug as category_slug, cat.icon as category_icon 
                           FROM courses cr 
                           JOIN course_categories cat ON cr.category_id = cat.id 
                           WHERE (cr.slug = ? OR cr.id = ?) AND cr.status = 'active' 
                           LIMIT 1");
    $stmt->execute([$slug, $id]);
    $course = $stmt->fetch();
}

// Quick Inquiry Form Submission
$inquiry_success = false;
$inquiry_error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_course_inquiry'])) {
    $c_name = trim($_POST['inquiry_name'] ?? '');
    $c_phone = trim($_POST['inquiry_phone'] ?? '');

    if (empty($c_name) || empty($c_phone)) {
        $inquiry_error = 'Please enter your name and phone number.';
    } else {
        $subject = 'Quick Callback: ' . ($course['title'] ?? 'Course Inquiry');
        $msg_body = "Course: " . ($course['title'] ?? 'General') . "\n";
        $msg_body .= "Student Name: " . $c_name . "\n";
        $msg_body .= "Phone: " . $c_phone . "\n";
        $msg_body .= "Source: Course Detail Page Quick Callback Box";

        try {
            $stmt = $pdo->prepare("INSERT INTO contact_inquiries (name, email, phone, subject, message, status, is_read) VALUES (?, '', ?, ?, ?, 'New', 0)");
            $stmt->execute([$c_name, $c_phone, $subject, $msg_body]);
            $inquiry_success = true;
        } catch (Exception $e) {
            $inquiry_error = 'Unable to submit right now. Please call our helpline directly.';
        }
    }
}

// Fetch related courses from same category
$related_courses = [];
if ($course) {
    $rel_stmt = $pdo->prepare("SELECT cr.*, cat.name as category_name, cat.icon as category_icon 
                               FROM courses cr 
                               JOIN course_categories cat ON cr.category_id = cat.id 
                               WHERE cr.category_id = ? AND cr.id != ? AND cr.status = 'active' 
                               LIMIT 3");
    $rel_stmt->execute([$course['category_id'], $course['id']]);
    $related_courses = $rel_stmt->fetchAll();
}

$page_title = $course ? htmlspecialchars($course['title']) . " - Finchskills Institute" : "Course Not Found - Finchskills Institute";
include "header.php";
?>

<?php if (!$course): ?>
    <!-- 404 NOT FOUND STATE -->
    <section class="py-5 text-center" style="min-height: 50vh; display: flex; align-items: center; background: #fafbfc;">
        <div class="container py-5">
            <div class="card border p-5 max-w-600 mx-auto rounded-4 bg-white shadow-none" style="max-width: 540px;">
                <i class="fa-solid fa-graduation-cap text-secondary opacity-25 fa-4x mb-3"></i>
                <h3 class="fw-bold text-dark mb-2">Course Not Found</h3>
                <p class="text-secondary small mb-4">The course you are looking for may have been updated, relocated, or is currently inactive.</p>
                <div class="d-flex justify-content-center gap-2">
                    <a href="courses.php" class="cd-btn-primary" style="width: auto; padding: 10px 22px;">Explore All Courses</a>
                    <a href="index.php" class="header-btn header-btn-outline" style="width: auto;">Home</a>
                </div>
            </div>
        </div>
    </section>
<?php else: ?>

    <!-- ================= BREADCRUMBS ================= -->
    <div class="cd-breadcrumb-bar">
        <div class="container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php"><i class="fa-solid fa-house me-1 small"></i>Home</a></li>
                    <li class="breadcrumb-item"><a href="courses.php">Courses</a></li>
                    <li class="breadcrumb-item"><a href="courses.php?category=<?= $course['category_id'] ?>"><?= htmlspecialchars($course['category_name']) ?></a></li>
                    <li class="breadcrumb-item active text-dark fw-semibold" aria-current="page"><?= htmlspecialchars($course['title']) ?></li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- ================= HERO SECTION ================= -->
    <section class="cd-hero-section">
        <div class="container">
            <div class="row align-items-center gy-4">
                <div class="col-lg-12">
                    <!-- BADGES -->
                    <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                        <span class="cd-cat-pill">
                            <i class="fa-solid <?= htmlspecialchars($course['category_icon']) ?>"></i>
                            <?= htmlspecialchars($course['category_name']) ?>
                        </span>
                        <span class="cd-status-pill">
                            <span class="cd-pulse-dot"></span>
                            Admissions Open &bull; Limited Seats
                        </span>
                    </div>

                    <!-- TITLE -->
                    <h1 class="cd-course-title">
                        <?= htmlspecialchars($course['title']) ?>
                    </h1>

                    <!-- SHORT INTRO -->
                    <p class="cd-course-intro">
                        <?= htmlspecialchars($course['short_desc']) ?>
                    </p>

                    <!-- METRIC HORIZONTAL STRIP -->
                    <div class="cd-metric-strip">
                        <div class="cd-metric-item">
                            <div class="cd-metric-icon"><i class="fa-regular fa-clock"></i></div>
                            <div>
                                <span class="cd-metric-label">Duration</span>
                                <div class="cd-metric-val"><?= htmlspecialchars($course['duration']) ?></div>
                            </div>
                        </div>
                        <div class="cd-metric-item">
                            <div class="cd-metric-icon"><i class="fa-solid fa-graduation-cap"></i></div>
                            <div>
                                <span class="cd-metric-label">Eligibility</span>
                                <div class="cd-metric-val"><?= htmlspecialchars($course['eligibility']) ?></div>
                            </div>
                        </div>
                        <div class="cd-metric-item">
                            <div class="cd-metric-icon"><i class="fa-solid fa-laptop-file"></i></div>
                            <div>
                                <span class="cd-metric-label">Training Mode</span>
                                <div class="cd-metric-val"><?= htmlspecialchars($course['study_mode']) ?></div>
                            </div>
                        </div>
                        <div class="cd-metric-item">
                            <div class="cd-metric-icon"><i class="fa-solid fa-briefcase"></i></div>
                            <div>
                                <span class="cd-metric-label">Placement Support</span>
                                <div class="cd-metric-val">100% Interview Assistance</div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>

    <!-- ================= STICKY SUB-NAV BAR ================= -->
    <div class="cd-sticky-nav">
        <div class="container">
            <div class="cd-nav-links">
                <a href="#overview" class="cd-nav-link active">About Program</a>
                <a href="#curriculum" class="cd-nav-link">Curriculum &amp; Modules</a>
                <a href="#methodology" class="cd-nav-link">Practical Training</a>
                <a href="#careers" class="cd-nav-link">Job Opportunities</a>
                <a href="#eligibility" class="cd-nav-link">Eligibility &amp; Certification</a>
                <a href="#faq" class="cd-nav-link">FAQs</a>
            </div>
        </div>
    </div>

    <!-- ================= MAIN SECTION ================= -->
    <section class="cd-main-wrap">
        <div class="container">
            <div class="row g-4 g-xl-5">

                <!-- LEFT COLUMN: CONTENT -->
                <div class="col-lg-8">

                    <!-- 1. COURSE OVERVIEW -->
                    <div class="cd-sec-card" id="overview">
                        <span class="cd-sec-kicker"><i class="fa-solid fa-circle-info"></i> Course Overview</span>
                        <div class="cd-body-text">
                            <?php
                            $desc_content = $course['full_desc'] ?: $course['short_desc'];
                            if (strip_tags($desc_content) !== $desc_content) {
                                echo strip_tags($desc_content, '<p><br><strong><b><em><i><u><ul><ol><li><h3><h4><h5><h6><a><blockquote><span><div><table><thead><tbody><tr><th><td>');
                            } else {
                                echo nl2br(htmlspecialchars($desc_content));
                            }
                            ?>
                        </div>

                        <!-- 4 KEY HIGHLIGHT PILLARS -->
                        <div class="row g-3 mt-3">
                            <div class="col-sm-6">
                                <div class="cd-highlight-box">
                                    <div class="cd-hl-icon"><i class="fa-solid fa-flask-vial"></i></div>
                                    <h5>Simulation &amp; Roleplay Labs</h5>
                                    <p>Practice passenger check-in, ramp operations, in-flight etiquette, and emergency scenarios in real lab settings.</p>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="cd-highlight-box">
                                    <div class="cd-hl-icon"><i class="fa-solid fa-book-open-reader"></i></div>
                                    <h5>Industry-Vetted Syllabus</h5>
                                    <p>Curriculum designed by active airline managers, airport officers, and luxury hospitality experts.</p>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="cd-highlight-box">
                                    <div class="cd-hl-icon"><i class="fa-solid fa-user-tie"></i></div>
                                    <h5>Executive Grooming &amp; Etiquette</h5>
                                    <p>Rigorous grooming masterclasses, spoken English fluency, body language refinement, and personality enhancement.</p>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="cd-highlight-box">
                                    <div class="cd-hl-icon"><i class="fa-solid fa-handshake"></i></div>
                                    <h5>Dedicated Placement Cell</h5>
                                    <p>Direct interview scheduling, CV building, mock panel rounds, and job placement assistance.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 2. WHAT WE PROVIDE / CURRICULUM -->
                    <?php if (!empty($course['what_you_learn'])): ?>
                        <?php 
                        $modules = array_filter(array_map('trim', explode("\n", $course['what_you_learn'])));
                        ?>
                        <div class="cd-sec-card" id="curriculum">
                            <span class="cd-sec-kicker"><i class="fa-solid fa-list-check"></i> What We Provide</span>
                            <h2 class="cd-sec-title">Course Curriculum &amp; Training Modules</h2>
                            <p class="cd-body-text mb-4">
                                Our step-by-step syllabus covers the exact operational standards required by domestic and international recruiters:
                            </p>

                            <div class="accordion cd-module-accordion" id="curriculumAccordion">
                                <?php foreach ($modules as $index => $mod): 
                                    $mod_id = "mod_" . ($index + 1);
                                    $is_first = ($index === 0);
                                ?>
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="heading_<?= $mod_id ?>">
                                            <button class="accordion-button <?= $is_first ? '' : 'collapsed' ?>" type="button" data-bs-toggle="collapse" data-bs-target="#collapse_<?= $mod_id ?>" aria-expanded="<?= $is_first ? 'true' : 'false' ?>" aria-controls="collapse_<?= $mod_id ?>">
                                                <span class="cd-mod-pill">Module <?= sprintf('%02d', $index + 1) ?></span>
                                                <span><?= htmlspecialchars($mod) ?></span>
                                            </button>
                                        </h2>
                                        <div id="collapse_<?= $mod_id ?>" class="accordion-collapse collapse <?= $is_first ? 'show' : '' ?>" aria-labelledby="heading_<?= $mod_id ?>" data-bs-parent="#curriculumAccordion">
                                            <div class="accordion-body">
                                                <div class="d-flex align-items-start gap-2 mb-2">
                                                    <i class="fa-solid fa-circle-check text-success mt-1"></i>
                                                    <span>Core concepts, hands-on operational practice, and standard operating procedures (SOPs).</span>
                                                </div>
                                                <div class="d-flex align-items-start gap-2">
                                                    <i class="fa-solid fa-circle-check text-success mt-1"></i>
                                                    <span>Practical simulation exercises and real-world case study evaluations.</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- 3. HOW WE WORK & TEACH (PRACTICAL METHODOLOGY) -->
                    <?php if (!empty($course['how_we_teach'])): ?>
                        <?php 
                        $teach_methods = array_filter(array_map('trim', explode("\n", $course['how_we_teach'])));
                        ?>
                        <div class="cd-sec-card" id="methodology">
                            <span class="cd-sec-kicker"><i class="fa-solid fa-chalkboard-user"></i> Training Methodology</span>
                            <h2 class="cd-sec-title">How We Work &amp; Prepare You</h2>
                            <p class="cd-body-text mb-4">
                                Finchskills focuses 70% on practical application and 30% on conceptual mastery so you are interview-ready from day one:
                            </p>

                            <div class="row g-3">
                                <?php foreach ($teach_methods as $idx => $method): ?>
                                    <div class="col-sm-6">
                                        <div class="cd-step-card">
                                            <div class="cd-step-num"><?= sprintf('%02d', $idx + 1) ?></div>
                                            <div>
                                                <h5>Practical Experience</h5>
                                                <p><?= htmlspecialchars($method) ?></p>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- 4. CAREER ROLES & JOB OPPORTUNITIES -->
                    <?php if (!empty($course['career_roles'])): ?>
                        <?php 
                        $roles = array_filter(array_map('trim', explode(',', $course['career_roles'])));
                        ?>
                        <div class="cd-sec-card" id="careers">
                            <span class="cd-sec-kicker"><i class="fa-solid fa-briefcase"></i> Career Paths</span>
                            <h2 class="cd-sec-title">Target Job Profiles &amp; Opportunities</h2>
                            <p class="cd-body-text mb-3">
                                Students completing this certification become eligible to apply for top positions in leading aviation, travel, and hospitality firms:
                            </p>

                            <div class="d-flex flex-wrap gap-2 mb-4">
                                <?php foreach ($roles as $role): ?>
                                    <span class="cd-career-tag">
                                        <i class="fa-solid fa-check"></i>
                                        <?= htmlspecialchars($role) ?>
                                    </span>
                                <?php endforeach; ?>
                            </div>

                            <div class="p-3 bg-light rounded-3 border">
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <i class="fa-solid fa-handshake-angle text-primary fs-6"></i>
                                    <strong class="text-dark small">Placement Guarantee &amp; Recruitment Drives</strong>
                                </div>
                                <p class="small text-muted mb-0">
                                    Our dedicated recruitment team coordinates on-campus and off-campus interview drives with domestic airlines, international airports, leading travel agencies, and 5-star hotel chains.
                                </p>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- 5. ELIGIBILITY & RECOGNIZED CERTIFICATION -->
                    <div class="cd-sec-card" id="eligibility">
                        <span class="cd-sec-kicker"><i class="fa-solid fa-certificate"></i> Credentials</span>
                        <h2 class="cd-sec-title">Eligibility &amp; Recognized Certification</h2>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="cd-info-card">
                                    <h6><i class="fa-solid fa-user-check text-primary"></i> Eligibility Criteria</h6>
                                    <p><?= htmlspecialchars($course['eligibility']) ?></p>
                                    <ul class="list-unstyled small text-muted mt-2 mb-0 d-flex flex-column gap-1">
                                        <li><i class="fa-solid fa-check text-success me-1"></i> 10th / 12th pass or graduate from any recognized board</li>
                                        <li><i class="fa-solid fa-check text-success me-1"></i> Basic English communication &amp; positive learning attitude</li>
                                    </ul>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="cd-info-card">
                                    <h6><i class="fa-solid fa-award text-warning"></i> Recognized Certification</h6>
                                    <p><?= htmlspecialchars($course['certification']) ?></p>
                                    <ul class="list-unstyled small text-muted mt-2 mb-0 d-flex flex-column gap-1">
                                        <li><i class="fa-solid fa-check text-success me-1"></i> Industry-aligned verifiable digital credential</li>
                                        <li><i class="fa-solid fa-check text-success me-1"></i> Recognized across domestic &amp; global employers</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 6. FREQUENTLY ASKED QUESTIONS (FAQS) -->
                    <div class="cd-sec-card" id="faq">
                        <span class="cd-sec-kicker"><i class="fa-solid fa-circle-question"></i> Help &amp; Support</span>
                        <h2 class="cd-sec-title">Frequently Asked Questions</h2>

                        <div class="accordion cd-faq-accordion" id="courseFaqAccordion">
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="faqH1">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqC1" aria-expanded="false" aria-controls="faqC1">
                                        Who is eligible to enroll in this program?
                                    </button>
                                </h2>
                                <div id="faqC1" class="accordion-collapse collapse" aria-labelledby="faqH1" data-bs-parent="#courseFaqAccordion">
                                    <div class="accordion-body">
                                        Candidates who have completed 10th, 12th, or Graduation in any stream with basic communication skills are eligible to enroll. There are no prior technical prerequisites.
                                    </div>
                                </div>
                            </div>

                            <div class="accordion-item">
                                <h2 class="accordion-header" id="faqH2">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqC2" aria-expanded="false" aria-controls="faqC2">
                                        Will I receive job placement assistance after the course?
                                    </button>
                                </h2>
                                <div id="faqC2" class="accordion-collapse collapse" aria-labelledby="faqH2" data-bs-parent="#courseFaqAccordion">
                                    <div class="accordion-body">
                                        Yes! Finchskills provides 100% placement assistance including CV structuring, airline/hotel interview grooming, mock interviews with HR panels, and direct interview scheduling.
                                    </div>
                                </div>
                            </div>

                            <div class="accordion-item">
                                <h2 class="accordion-header" id="faqH3">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqC3" aria-expanded="false" aria-controls="faqC3">
                                        What is the training mode available?
                                    </button>
                                </h2>
                                <div id="faqC3" class="accordion-collapse collapse" aria-labelledby="faqH3" data-bs-parent="#courseFaqAccordion">
                                    <div class="accordion-body">
                                        We offer flexible batches including Campus Classroom Training with live lab simulations, Online Live Instructor-led Batches, and Hybrid combinations tailored for students and working professionals.
                                    </div>
                                </div>
                            </div>

                            <div class="accordion-item">
                                <h2 class="accordion-header" id="faqH4">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqC4" aria-expanded="false" aria-controls="faqC4">
                                        Can I pay the program fees in installments?
                                    </button>
                                </h2>
                                <div id="faqC4" class="accordion-collapse collapse" aria-labelledby="faqH4" data-bs-parent="#courseFaqAccordion">
                                    <div class="accordion-body">
                                        Yes, convenient installment options are available. Our admissions counselor will walk you through transparent fee structures and flexible monthly payment plans.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- RIGHT COLUMN: UNIFIED STICKY SIDEBAR -->
                <div class="col-lg-4">
                    <div class="cd-sticky-sidebar" id="enrollSidebar">

                        <div class="cd-sidebar-card">
                            <!-- THUMBNAIL -->
                            <div class="cd-sidebar-thumb-wrap">
                                <?php if (!empty($course['image'])): ?>
                                    <img src="<?= htmlspecialchars($course['image']) ?>" alt="<?= htmlspecialchars($course['title']) ?>">
                                <?php else: ?>
                                    <div class="w-100 h-100 bg-dark d-flex align-items-center justify-content-center text-white">
                                        <i class="fa-solid <?= htmlspecialchars($course['category_icon']) ?> fa-3x text-warning"></i>
                                    </div>
                                <?php endif; ?>
                                <span class="cd-thumb-badge">
                                    <i class="fa-solid <?= htmlspecialchars($course['category_icon']) ?>"></i>
                                    <?= htmlspecialchars($course['category_name']) ?>
                                </span>
                            </div>

                            <!-- BODY -->
                            <div class="cd-sidebar-body">
                                <span class="cd-intake-badge">
                                    <span class="cd-pulse-dot"></span>
                                    Upcoming Batch Enrollment Open
                                </span>

                                <h4 class="cd-sidebar-title"><?= htmlspecialchars($course['title']) ?></h4>
                                <p class="cd-sidebar-sub">Join certified practical training designed for immediate industry hiring.</p>

                                <!-- PRIMARY CTA BUTTON -->
                                <?php if (!empty($isStudentLoggedIn)): ?>
                                    <a href="form-submission.php?course=<?= urlencode($course['title']) ?>" class="cd-btn-primary" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                                        <i class="fa-solid fa-file-signature me-1"></i>
                                        <span>Apply Online</span>
                                        <i class="fa-solid fa-arrow-right"></i>
                                    </a>
                                <?php else: ?>
                                    <a href="register.php?redirect=form-submission.php?course=<?= urlencode($course['title']) ?>" class="cd-btn-primary">
                                        <i class="fa-solid fa-user-pen me-1"></i>
                                        <span>Register Yourself</span>
                                        <i class="fa-solid fa-arrow-right"></i>
                                    </a>
                                <?php endif; ?>

                                <!-- SECONDARY CTA: WHATSAPP -->
                                <a href="https://wa.me/918750860860?text=<?= urlencode("Hello Finchskills, I want to inquire about the " . $course['title'] . " program.") ?>" target="_blank" rel="noopener" class="cd-btn-whatsapp">
                                    <i class="fa-brands fa-whatsapp fs-5"></i>
                                    <span>Chat with Counselor on WhatsApp</span>
                                </a>

                                <!-- COURSE FACTS LIST -->
                                <ul class="cd-facts-list">
                                    <li class="cd-fact-row">
                                        <span class="cd-fact-label"><i class="fa-regular fa-clock"></i> Duration</span>
                                        <span class="cd-fact-val"><?= htmlspecialchars($course['duration']) ?></span>
                                    </li>
                                    <li class="cd-fact-row">
                                        <span class="cd-fact-label"><i class="fa-solid fa-graduation-cap"></i> Eligibility</span>
                                        <span class="cd-fact-val"><?= htmlspecialchars($course['eligibility']) ?></span>
                                    </li>
                                    <li class="cd-fact-row">
                                        <span class="cd-fact-label"><i class="fa-solid fa-laptop-file"></i> Study Mode</span>
                                        <span class="cd-fact-val"><?= htmlspecialchars($course['study_mode']) ?></span>
                                    </li>
                                    <li class="cd-fact-row">
                                        <span class="cd-fact-label"><i class="fa-solid fa-certificate"></i> Certificate</span>
                                        <span class="cd-fact-val">Industry Recognized</span>
                                    </li>
                                    <li class="cd-fact-row">
                                        <span class="cd-fact-label"><i class="fa-solid fa-briefcase"></i> Placement</span>
                                        <span class="cd-fact-val text-success">100% Support</span>
                                    </li>
                                </ul>

                                <!-- QUICK CALLBACK BOX (MINIMAL & CONFUSION-FREE) -->
                                <div class="cd-callback-box">
                                    <h6><i class="fa-solid fa-phone-volume text-primary"></i> Request a Quick Callback</h6>
                                    <p>Have questions on fees or schedule? Get a call from our counselor in 15 minutes.</p>

                                    <?php if ($inquiry_success): ?>
                                        <div class="alert alert-success py-2 px-2.5 small mb-0" role="alert">
                                            <i class="fa-solid fa-circle-check me-1"></i> Request received! Our counselor will call you shortly.
                                        </div>
                                    <?php else: ?>
                                        <?php if (!empty($inquiry_error)): ?>
                                            <div class="alert alert-danger py-1.5 px-2 small mb-2" role="alert">
                                                <?= htmlspecialchars($inquiry_error) ?>
                                            </div>
                                        <?php endif; ?>

                                        <form method="POST" action="course-detail.php?slug=<?= urlencode($course['slug']) ?>#enrollSidebar">
                                            <input type="hidden" name="submit_course_inquiry" value="1">
                                            <input type="text" name="inquiry_name" class="cd-callback-input" placeholder="Your Full Name" required>
                                            <input type="tel" name="inquiry_phone" class="cd-callback-input" placeholder="WhatsApp / Mobile Number" required>
                                            <button type="submit" class="cd-callback-btn">
                                                <span>Request Free Call</span>
                                                <i class="fa-solid fa-arrow-right ms-1"></i>
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </div>

                                <div class="cd-helpline-note">
                                    Need urgent assistance? Call <a href="tel:+918750860860">+91 87508 60860</a>
                                </div>

                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- ================= RELATED COURSES ================= -->
    <?php if (!empty($related_courses)): ?>
        <section class="py-5 bg-white border-top">
            <div class="container">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <div class="course-section-kicker">
                            <i class="fa-solid fa-graduation-cap"></i>
                            <span>EXPLORE SIMILAR</span>
                        </div>
                        <h3 class="fw-bold text-dark mb-0">More Programs in <?= htmlspecialchars($course['category_name']) ?></h3>
                    </div>
                    <a href="courses.php?category=<?= $course['category_id'] ?>" class="view-btn">View All</a>
                </div>

                <div class="row g-4">
                    <?php foreach ($related_courses as $rc): ?>
                        <div class="col-lg-4 col-md-6">
                            <div class="course-card">
                                <div class="course-image">
                                    <?php if (!empty($rc['image'])): ?>
                                        <img src="<?= htmlspecialchars($rc['image']) ?>" alt="<?= htmlspecialchars($rc['title']) ?>">
                                    <?php else: ?>
                                        <div class="w-100 h-100 bg-dark d-flex align-items-center justify-content-center text-white">
                                            <i class="fa-solid <?= htmlspecialchars($rc['category_icon']) ?> fa-2x"></i>
                                        </div>
                                    <?php endif; ?>
                                    <span class="course-cat-badge">
                                        <i class="fa-solid <?= htmlspecialchars($rc['category_icon']) ?>"></i> <?= htmlspecialchars($rc['category_name']) ?>
                                    </span>
                                    <span class="course-duration-badge">
                                        <i class="fa-regular fa-clock"></i> <?= htmlspecialchars($rc['duration']) ?>
                                    </span>
                                </div>
                                <div class="course-content">
                                    <h3><?= htmlspecialchars($rc['title']) ?></h3>
                                    <p><?= htmlspecialchars($rc['short_desc']) ?></p>
                                    <div class="course-card-footer">
                                        <?php if (!empty($isStudentLoggedIn)): ?>
                                            <a href="form-submission.php?course=<?= urlencode($rc['title']) ?>" class="enroll-btn" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                                                <i class="fa-solid fa-file-signature"></i>
                                                <span>Apply Online</span>
                                            </a>
                                        <?php else: ?>
                                            <a href="register.php?redirect=form-submission.php?course=<?= urlencode($rc['title']) ?>" class="enroll-btn">
                                                <i class="fa-solid fa-user-pen"></i>
                                                <span>Register Yourself</span>
                                            </a>
                                        <?php endif; ?>
                                        <a href="course-detail.php?slug=<?= urlencode($rc['slug']) ?>" class="course-details-link">
                                            <span>View Details</span>
                                            <i class="fa-solid fa-chevron-right"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
    <?php endif; ?>

<?php endif; ?>

<!-- Smooth scroll script for sticky sub-nav -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const navLinks = document.querySelectorAll('.cd-nav-link');
    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            navLinks.forEach(l => l.classList.remove('active'));
            this.classList.add('active');
        });
    });
});
</script>

<?php include "footer.php"; ?>
