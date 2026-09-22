<?php
require_once __DIR__ . '/db.php';

// Fetch Categories
$cat_stmt = $pdo->query("SELECT * FROM course_categories WHERE status = 'active' ORDER BY name ASC");
$all_categories = $cat_stmt->fetchAll();

// Category filter
$cat_filter = isset($_GET['category']) ? (int)$_GET['category'] : 0;
$c_query = "SELECT cr.*, cat.name as category_name, cat.icon as category_icon 
            FROM courses cr 
            JOIN course_categories cat ON cr.category_id = cat.id 
            WHERE cr.status = 'active'";
$params = [];
if ($cat_filter > 0) {
    $c_query .= " AND cr.category_id = ?";
    $params[] = $cat_filter;
}
$c_query .= " ORDER BY cr.is_featured DESC, cr.id DESC";
$stmt = $pdo->prepare($c_query);
$stmt->execute($params);
$courses_list = $stmt->fetchAll();

$page_title = "Explore Our Courses - Finchskills Institute";
include "header.php";
?>

<!-- ===================================== -->
<!-- BLOG HERO -->
<!-- ===================================== -->

<section class="educrsv2-hero-section">

    <div class="container">

        <div class="row align-items-center gy-4">

            <!-- LEFT CONTENT -->

            <div class="col-md-6">

                <div class="educrsv2-content-wrap">

                    <!-- BADGE -->

                    <div class="educrsv2-top-badge">

                        <i class="fa-solid fa-graduation-cap"></i>

                        <span>Learn From Industry Experts</span>

                    </div>

                    <!-- HEADING -->

                    <h1>
                        Upgrade Your Skills
                        With Modern Career
                        Focused Courses
                    </h1>

                    <!-- DESCRIPTION -->

                    <p>
                        Join practical learning programs designed for real-world
                        careers with live training, mentorship, projects,
                        and placement-focused guidance.
                    </p>


                    <!-- FEATURES -->

                    <div class="educrsv2-feature-wrap">

                        <div class="educrsv2-feature-item">

                            <div class="educrsv2-feature-icon">
                                <i class="fa-solid fa-laptop-code"></i>
                            </div>

                            <div>
                                <h6>Online Projects</h6>
                                <span>Practical Training</span>
                            </div>

                        </div>

                        <div class="educrsv2-feature-item">

                            <div class="educrsv2-feature-icon">
                                <i class="fa-solid fa-user-tie"></i>
                            </div>

                            <div>
                                <h6>Expert Mentors</h6>
                                <span>Industry Trainers</span>
                            </div>

                        </div>

                    </div>

                </div>

            </div>

            <!-- RIGHT IMAGE -->

            <div class="col-md-6">

                <div class="educrsv2-image-wrapper">

                    <!-- MAIN IMAGE -->
                    <img src="img/course-hero.jpeg"
                        alt="Courses">

                    <div class="course-responsive-box">
                        <!-- CARD 1 -->
                        <div class="educrsv2-floating-card educrsv2-card-one">
                            <h3><?= count($courses_list) ?>+</h3>
                            <p>Courses</p>
                        </div>

                        <!-- CARD 2 -->
                        <div class="educrsv2-floating-card educrsv2-card-two">
                            <i class="fa-solid fa-briefcase"></i>
                            <div>
                                <h5>Career Support</h5>
                                <span>Placement Assistance</span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>

    </div>

</section>


<section class="popular-course-section">

    <div class="container">

        <!-- CATEGORY FILTER TABS -->
        <div class="d-flex flex-wrap align-items-center justify-content-center gap-2 mb-5">
            <a href="courses.php" class="btn <?= $cat_filter === 0 ? 'btn-dark' : 'btn-outline-secondary' ?> rounded-pill px-3 py-2 fw-semibold small">
                All Programs (<?= count($courses_list) ?>)
            </a>
            <?php foreach ($all_categories as $c_cat): ?>
                <a href="courses.php?category=<?= $c_cat['id'] ?>" class="btn <?= $cat_filter === (int)$c_cat['id'] ? 'btn-dark' : 'btn-outline-secondary' ?> rounded-pill px-3 py-2 fw-semibold small d-inline-flex align-items-center gap-2">
                    <i class="fa-solid <?= htmlspecialchars($c_cat['icon']) ?> text-primary"></i>
                    <span><?= htmlspecialchars($c_cat['name']) ?></span>
                </a>
            <?php endforeach; ?>
        </div>

        <!-- COURSES GRID -->
        <?php if (!empty($courses_list)): ?>
            <div class="row g-4">
                <?php foreach ($courses_list as $c): ?>
                    <div class="col-lg-4 col-md-6">
                        <div class="course-card">
                            <div class="course-image">
                                <?php if (!empty($c['image'])): ?>
                                    <img src="<?= htmlspecialchars($c['image']) ?>" alt="<?= htmlspecialchars($c['title']) ?>">
                                <?php else: ?>
                                    <div class="w-100 h-100 bg-dark d-flex align-items-center justify-content-center text-white">
                                        <i class="fa-solid <?= htmlspecialchars($c['category_icon']) ?> fa-2x"></i>
                                    </div>
                                <?php endif; ?>
                                <span class="course-cat-badge">
                                    <i class="fa-solid <?= htmlspecialchars($c['category_icon']) ?>"></i>
                                    <?= htmlspecialchars($c['category_name']) ?>
                                </span>
                                <span class="course-duration-badge">
                                    <i class="fa-regular fa-clock"></i>
                                    <?= htmlspecialchars($c['duration']) ?>
                                </span>
                            </div>

                            <div class="course-content">
                                <h3><?= htmlspecialchars($c['title']) ?></h3>
                                <p><?= htmlspecialchars($c['short_desc']) ?></p>

                                <div class="course-meta">
                                    <span class="cm-item"><i class="fa-regular fa-clock"></i> <?= htmlspecialchars($c['duration']) ?></span>
                                    <span class="cm-item"><i class="fa-solid fa-briefcase"></i> 100% Placement</span>
                                    <span class="cm-item"><i class="fa-solid fa-laptop-file"></i> <?= htmlspecialchars($c['study_mode']) ?></span>
                                </div>

                                <div class="course-card-footer">
                                    <?php if (!empty($isStudentLoggedIn)): ?>
                                        <a href="form-submission.php?course=<?= urlencode($c['title']) ?>" class="enroll-btn" title="Apply Online for this course">
                                            <i class="fa-solid fa-file-signature"></i>
                                            <span>Apply Online</span>
                                        </a>
                                    <?php else: ?>
                                        <a href="register.php?redirect=form-submission.php?course=<?= urlencode($c['title']) ?>" class="enroll-btn" title="Register to apply for this course">
                                            <i class="fa-solid fa-user-pen"></i>
                                            <span>Register Yourself</span>
                                        </a>
                                    <?php endif; ?>
                                    <a href="course-detail.php?slug=<?= urlencode($c['slug']) ?>" class="course-details-link">
                                        <span>View Details</span>
                                        <i class="fa-solid fa-chevron-right"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="text-center py-5">
                <i class="fa-solid fa-graduation-cap text-muted opacity-25 fa-3x mb-3 d-block"></i>
                <h4 class="fw-bold text-dark">No courses found in this category</h4>
                <p class="text-muted small mb-3">Please check back soon or browse all courses.</p>
                <a href="courses.php" class="header-btn header-btn-primary">View All Courses</a>
            </div>
        <?php endif; ?>

    </div>

</section>

<!-- FOOTER  -->
<?php include "footer.php" ?>