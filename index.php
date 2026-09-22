<!-- Header  -->
<?php include "header.php" ?>


    <!-- ================= Hero Section ================= -->
    <section class="hero-section">

        <div class="container">

            <div class="row align-items-center">



                <div class="col-lg-7 col-xl-8">

                    <div class="hero-content">

                        <!-- BADGE -->

                        <div class="hero-top-badge">

                            <span class="hero-badge-icon">
                                <i class="fa-solid fa-graduation-cap"></i>
                            </span>

                            <span class="hero-badge-text">
                                Aviation <span class="badge-dot">•</span> Hospitality <span class="badge-dot">•</span> Travel <span class="badge-dot">•</span> Cruise Courses
                            </span>

                        </div>

                        <h1 class="hero-title">
                            Build Skills <br> Build Your <span class="highlight">Future</span>
                        </h1>

                        <p class="hero-text">
                            Industry-focused training, hands-on practical skills, and dedicated placement support designed to prepare you for high-growth careers across <strong>Aviation</strong>, <strong>Hospitality</strong>, <strong>Travel</strong>, and <strong>Cruise Management</strong>.
                        </p>

                        <ul class="hero-list mb-4">
                            <li class="hero-list-item">
                                <div class="herolist-icon">
                                    <i class="fa-solid fa-briefcase"></i>
                                </div>
                                <div class="d-flex flex-column">
                                    <div class="fw-bold text-dark hero-item-title">Job Oriented Training</div>
                                    <div class="hero-list-text text-muted">Learn skills that get you hired.</div>
                                </div>
                            </li>
                            <li class="hero-list-item">
                                <div class="herolist-icon">
                                    <i class="fa-solid fa-user"></i>
                                </div>
                                <div class="d-flex flex-column">
                                    <div class="fw-bold text-dark hero-item-title">Expert Trainers</div>
                                    <div class="hero-list-text text-muted">Learn from industry experts.</div>
                                </div>
                            </li>
                            <li class="hero-list-item mb-0">
                                <div class="herolist-icon">
                                    <i class="fa-solid fa-medal"></i>
                                </div>
                                <div class="d-flex flex-column">
                                    <div class="fw-bold text-dark hero-item-title">Placement Support</div>
                                    <div class="hero-list-text text-muted">We help you get placed.</div>
                                </div>
                            </li>
                        </ul>


                        <div class="hero-btns">

                            <?php if (!empty($isStudentLoggedIn)): ?>
                                <a href="form-submission.php" class="hero-btn hero-btn-register">
                                    <i class="fa-solid fa-file-signature me-2"></i>
                                    <span>Apply Online</span>
                                    <i class="fa-solid fa-arrow-right ms-2 hero-btn-arrow"></i>
                                </a>
                            <?php else: ?>
                                <a href="register.php?redirect=form-submission.php" class="hero-btn hero-btn-register">
                                    <i class="fa-solid fa-user-pen me-2"></i>
                                    <span>Register Yourself</span>
                                    <i class="fa-solid fa-arrow-right ms-2 hero-btn-arrow"></i>
                                </a>
                            <?php endif; ?>

                            <a href="fee-submission.php" class="hero-btn hero-btn-fee">
                                <i class="fa-solid fa-receipt me-2"></i>
                                <span>Fee Submission</span>
                                <i class="fa-solid fa-chevron-right ms-2 hero-btn-arrow"></i>
                            </a>

                        </div>

                    </div>

                </div>


                <div class="col-lg-5 col-xl-4">
                    <div class="hero-image">
                    </div>
                </div>

            </div>

        </div>


    </section>

    <!-- ================= FIXED SIDEBAR ================= -->

    <section class="features-section">

        <div class="container">

            <!-- TITLE -->

            <div class="features-section-title text-center mx-auto">

                <div class="features-badge">
                    <i class="fa-solid fa-graduation-cap"></i>
                    <span>CAREER EXCELLENCE</span>
                </div>

                <h2>
                    Your Career Starts with the <span class="highlight-text">Right Skills</span>
                </h2>

                <p>
                    Career-focused training designed to help you master practical skills, build professional confidence, and unlock real job opportunities at your own pace.
                </p>
            </div>

            <!-- ROW -->

            <div class="row g-4">

                <!-- CARD 1 -->

                <div class="col-lg-4 col-md-4">

                    <div class="feature-card">

                        <!-- ICON -->

                        <div class="feature-icon">

                            <i class="fa-solid fa-award"></i>

                        </div>

                        <!-- CONTENT -->

                        <div class="feature-content">
                            <h4>
                                Certification Programs
                            </h4>

                            <p>
                                Professional certification programs that help you move from training to placement.
                            </p>
                        </div>

                    </div>

                </div>

                <!-- CARD 2 -->

                <div class="col-lg-4 col-md-4">

                    <div class="feature-card">

                        <!-- ICON -->

                        <div class="feature-icon">

                            <i class="fa-solid fa-laptop-code"></i>

                        </div>

                        <!-- CONTENT -->

                        <div class="feature-content">

                            <h4>
                                Practical Skill Training
                            </h4>

                            <p>
                                Gain practical experience that helps you perform better in interviews and on the job.
                            </p>

                        </div>

                    </div>

                </div>

                <!-- CARD 3 -->

                <div class="col-lg-4 col-md-4">

                    <div class="feature-card">

                        <!-- ICON -->

                        <div class="feature-icon">

                            <i class="fa-solid fa-book-open-reader"></i>

                        </div>

                        <!-- CONTENT -->

                        <div class="feature-content">
                            <h4>
                                Flexible Online Classes
                            </h4>

                            <p>
                                Access your classes anytime and learn at your own pace without a fixed schedule.
                            </p>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </section>

    <section class="learning-section">

        <div class="container">

            <div class="row align-items-center info-homepage">

                <!-- LEFT IMAGE -->

                <div class="col-md-6">

                    <div class="learning-image-wrapper">



                        <!-- MAIN PERSON IMAGE -->

                        <div class="learning-image">

                            <img src="img/about-home.png" alt="">

                        </div>



                    </div>

                </div>

                <!-- RIGHT CONTENT -->

                <div class="col-md-6">

                    <div class="learning-content">

                        <!-- BADGE -->
                        <div class="learning-badge">
                            <i class="fa-solid fa-compass"></i>
                            <span>WHY CHOOSE FINCHSKILLS</span>
                        </div>

                        <h2 class="learning-title">
                            Start Your Professional Journey with <span class="highlight-orange">Finchskills Institute</span>
                        </h2>

                        <p class="learning-desc">
                            Step into a high-growth career with practical, job-oriented training. We equip you with the exact technical and interpersonal skills demanded in <strong>Aviation</strong>, <strong>Hospitality</strong>, <strong>Travel</strong>, and <strong>Cruise Management</strong>—from grooming and communication to live airport terminal and flight simulations.
                        </p>

                        <!-- 2x2 FEATURES GRID -->
                        <div class="learning-features-grid">

                            <div class="learning-feature-card">
                                <div class="lf-icon">
                                    <i class="fa-solid fa-briefcase"></i>
                                </div>
                                <div class="lf-text">
                                    <h5>Job-Oriented Curriculum</h5>
                                    <p>Industry-aligned syllabus updated to airline and hotel recruiter standards.</p>
                                </div>
                            </div>

                            <div class="learning-feature-card">
                                <div class="lf-icon">
                                    <i class="fa-solid fa-chalkboard-user"></i>
                                </div>
                                <div class="lf-text">
                                    <h5>Expert Industry Trainers</h5>
                                    <p>Learn directly from certified flight attendants, ground leads & hospitality pros.</p>
                                </div>
                            </div>

                            <div class="learning-feature-card">
                                <div class="lf-icon">
                                    <i class="fa-solid fa-clock-rotate-left"></i>
                                </div>
                                <div class="lf-text">
                                    <h5>Flexible Learning Batches</h5>
                                    <p>Weekday & weekend batches tailored to fit seamlessly into your schedule.</p>
                                </div>
                            </div>

                            <div class="learning-feature-card">
                                <div class="lf-icon">
                                    <i class="fa-solid fa-handshake-angle"></i>
                                </div>
                                <div class="lf-text">
                                    <h5>100% Placement Support</h5>
                                    <p>Dedicated placement cell with resume building and direct interview drives.</p>
                                </div>
                            </div>

                        </div>

                        <!-- ACTION BUTTONS -->
                        <div class="learning-actions">
                            <a href="courses.php" class="hero-btn hero-btn-register">
                                <span>Explore All Courses</span>
                                <i class="fa-solid fa-arrow-right ms-2 hero-btn-arrow"></i>
                            </a>
                            <?php if (!empty($isStudentLoggedIn)): ?>
                                <a href="form-submission.php" class="hero-btn hero-btn-fee">
                                    <i class="fa-solid fa-file-signature me-2"></i>
                                    <span>Apply Online</span>
                                </a>
                            <?php else: ?>
                                <a href="register.php?redirect=form-submission.php" class="hero-btn hero-btn-fee">
                                    <i class="fa-solid fa-user-plus me-2"></i>
                                    <span>Register Yourself</span>
                                </a>
                            <?php endif; ?>
                        </div>

                    </div>

                </div>

            </div>

        </div>

    </section>


    <section class="popular-course-section">

        <div class="container">

            <!-- HEADER -->

            <div class="row align-items-end section-header g-4">

                <!-- LEFT -->

                <div class="col-lg-8">

                    <div class="course-section-kicker">
                        <i class="fa-solid fa-graduation-cap"></i>
                        <span>FEATURED PROGRAMS</span>
                    </div>

                    <h2>
                        Explore Our Career-Focused <span class="highlight-orange">Courses</span>
                    </h2>

                    <p>
                        Discover job-focused courses designed to help you build practical skills, gain confidence, and
                        move toward real career opportunities.
                    </p>

                </div>

                <!-- RIGHT -->

                <div class="col-lg-4 text-lg-end">

                    <a href="courses.php" class="view-btn mt-0">
                        <span>View All Courses</span>
                        <i class="fa-solid fa-arrow-right ms-2"></i>
                    </a>

                </div>

            </div>

            <!-- COURSES -->

            <div class="row justify-content-center g-4">

                <!-- CARD 1 -->

                <div class="col-md-6 col-lg-4">

                    <div class="course-card">

                        <div class="course-image">

                            <img src="img/airhostess-course.png" alt="Professional Course in Air Hostess">
                            <span class="course-cat-badge">
                                <i class="fa-solid fa-plane-departure"></i> Aviation & Cabin Crew
                            </span>
                            <span class="course-duration-badge">
                                <i class="fa-regular fa-clock"></i> 12 Months
                            </span>

                        </div>

                        <div class="course-content">

                            <h3>
                                Professional Course in Air Hostess
                            </h3>

                            <p>
                                Prepare for a successful career in aviation. Develop professional grooming, in-flight safety protocols, hospitality etiquette, and communication skills required to excel as cabin crew.
                            </p>

                            <div class="course-meta">
                                <span class="cm-item"><i class="fa-regular fa-clock"></i> Duration: <strong>12 Months</strong></span>
                                <span class="cm-item"><i class="fa-solid fa-briefcase"></i> 100% Placement</span>
                            </div>

                            <div class="course-card-footer">
                                <?= renderEnrollBtn('Professional Course in Air Hostess') ?>
                                <a href="course-detail.php?slug=professional-course-in-air-hostess" class="course-details-link">
                                    <span>View Details</span>
                                    <i class="fa-solid fa-chevron-right"></i>
                                </a>
                            </div>

                        </div>

                    </div>

                </div>

                <!-- CARD 2 -->

                <div class="col-md-6 col-lg-4">

                    <div class="course-card">

                        <div class="course-image">

                            <img src="img/Hospitality-course.png" alt="Professional Course in Ground Staff & Hospitality">
                            <span class="course-cat-badge">
                                <i class="fa-solid fa-id-badge"></i> Airport Operations
                            </span>
                            <span class="course-duration-badge">
                                <i class="fa-regular fa-clock"></i> 12 Months
                            </span>

                        </div>

                        <div class="course-content">

                            <h3>
                                Professional Course in Ground Staff & Hospitality
                            </h3>

                            <p>
                                Gain comprehensive training in airport terminal operations, passenger check-in handling, ramp coordination, security protocols, and luxury hospitality management.
                            </p>

                            <div class="course-meta">
                                <span class="cm-item"><i class="fa-regular fa-clock"></i> Duration: <strong>12 Months</strong></span>
                                <span class="cm-item"><i class="fa-solid fa-user-check"></i> Airport Ready</span>
                            </div>

                            <div class="course-card-footer">
                                <?= renderEnrollBtn('Professional Course in Ground Staff & Hospitality') ?>
                                <a href="course-detail.php?slug=ground-staff-hospitality-course" class="course-details-link">
                                    <span>View Details</span>
                                    <i class="fa-solid fa-chevron-right"></i>
                                </a>
                            </div>

                        </div>

                    </div>

                </div>


                <!-- CARD 3 -->

                <div class="col-md-6 col-lg-4">

                    <div class="course-card">

                        <div class="course-image">

                            <img src="img/Diploma-travell.webp" alt="Professional Course in Travel & Tourism">
                            <span class="course-cat-badge">
                                <i class="fa-solid fa-earth-americas"></i> Travel & Tourism
                            </span>
                            <span class="course-duration-badge">
                                <i class="fa-regular fa-clock"></i> 12 Months
                            </span>

                        </div>

                        <div class="course-content">

                            <h3>
                                Professional Course in Travel & Tourism
                            </h3>

                            <p>
                                Build a strong foundation in international tourism trends, tour itinerary planning, visa assistance, and customer relationship expertise for global travel agencies.
                            </p>

                            <div class="course-meta">
                                <span class="cm-item"><i class="fa-regular fa-clock"></i> Duration: <strong>12 Months</strong></span>
                                <span class="cm-item"><i class="fa-solid fa-globe"></i> Global Careers</span>
                            </div>

                            <div class="course-card-footer">
                                <?= renderEnrollBtn('Professional Course in Travel & Tourism') ?>
                                <a href="course-detail.php?slug=professional-course-in-travel-tourism" class="course-details-link">
                                    <span>View Details</span>
                                    <i class="fa-solid fa-chevron-right"></i>
                                </a>
                            </div>

                        </div>

                    </div>

                </div>

                <!-- CARD 4 -->

                <div class="col-md-6 col-lg-4">

                    <div class="course-card">

                        <div class="course-image">

                            <img src="img/Hotel-Management-course.webp" alt="Foundation Course in Hotel Management">
                            <span class="course-cat-badge">
                                <i class="fa-solid fa-hotel"></i> Hotel Management
                            </span>
                            <span class="course-duration-badge">
                                <i class="fa-regular fa-clock"></i> 6 Months
                            </span>

                        </div>

                        <div class="course-content">

                            <h3>
                                Foundation Course in Hotel Management
                            </h3>

                            <p>
                                Learn the fundamentals of hotel operations, front-office systems, housekeeping standards, food & beverage service, and guest relations for 5-star hotels and resorts.
                            </p>

                            <div class="course-meta">
                                <span class="cm-item"><i class="fa-regular fa-clock"></i> Duration: <strong>6 Months</strong></span>
                                <span class="cm-item"><i class="fa-solid fa-star"></i> 5-Star Standards</span>
                            </div>

                            <div class="course-card-footer">
                                <?= renderEnrollBtn('Foundation Course in Hotel Management') ?>
                                <a href="course-detail.php?slug=foundation-course-in-hotel-management" class="course-details-link">
                                    <span>View Details</span>
                                    <i class="fa-solid fa-chevron-right"></i>
                                </a>
                            </div>

                        </div>

                    </div>

                </div>

                <!-- CARD 5 -->

                <div class="col-md-6 col-lg-4">

                    <div class="course-card">

                        <div class="course-image">

                            <img src="img/personality-development-course.webp" alt="Professional Course in Personality Development">
                            <span class="course-cat-badge">
                                <i class="fa-solid fa-user-tie"></i> Grooming & Soft Skills
                            </span>
                            <span class="course-duration-badge">
                                <i class="fa-regular fa-clock"></i> 3 Months
                            </span>

                        </div>

                        <div class="course-content">

                            <h3>
                                Professional Course in Personality Development
                            </h3>

                            <p>
                                Elevate your career presence with corporate etiquette, spoken English fluency, body language refinement, and rigorous mock interview simulations.
                            </p>

                            <div class="course-meta">
                                <span class="cm-item"><i class="fa-regular fa-clock"></i> Duration: <strong>3 Months</strong></span>
                                <span class="cm-item"><i class="fa-solid fa-comments"></i> Interview Prep</span>
                            </div>

                            <div class="course-card-footer">
                                <?= renderEnrollBtn('Professional Course in Personality Development') ?>
                                <a href="course-detail.php?slug=professional-course-in-personality-development" class="course-details-link">
                                    <span>View Details</span>
                                    <i class="fa-solid fa-chevron-right"></i>
                                </a>
                            </div>

                        </div>

                    </div>

                </div>

                <!-- CARD 6 -->

                <div class="col-md-6 col-lg-4">

                    <div class="course-card">

                        <div class="course-image">

                            <img src="img/Certificate-Course.webp" alt="Certificate Course in Travel & Air Ticketing">
                            <span class="course-cat-badge">
                                <i class="fa-solid fa-ticket"></i> GDS & Air Ticketing
                            </span>
                            <span class="course-duration-badge">
                                <i class="fa-regular fa-clock"></i> 6 Months
                            </span>

                        </div>

                        <div class="course-content">

                            <h3>
                                Certificate Course in Travel & Air Ticketing
                            </h3>

                            <p>
                                Master Global Distribution Systems (GDS like Amadeus/Galileo), airfare calculations, PNR creation, and flight ticket issuance for instant airline hiring.
                            </p>

                            <div class="course-meta">
                                <span class="cm-item"><i class="fa-regular fa-clock"></i> Duration: <strong>6 Months</strong></span>
                                <span class="cm-item"><i class="fa-solid fa-laptop-code"></i> Live GDS Training</span>
                            </div>

                            <div class="course-card-footer">
                                <?= renderEnrollBtn('Certificate Course in Travel & Air Ticketing') ?>
                                <a href="course-detail.php?slug=certificate-course-in-travel-air-ticketing" class="course-details-link">
                                    <span>View Details</span>
                                    <i class="fa-solid fa-chevron-right"></i>
                                </a>
                            </div>

                        </div>

                    </div>

                </div>

            </div>
        </div>

    </section>








    <!-- ================= FREE COURSE / LEARNING JOURNEY SECTION ================= -->

    <section class="free-course-section">

        <div class="container">

            <div class="free-course-wrapper">

                <div class="row align-items-center g-4">

                    <!-- LEFT -->

                    <div class="col-lg-6">

                        <div class="left-content">

                            <div class="journey-badge">
                                <i class="fa-solid fa-fire-flame-curved"></i>
                                <span>Limited Seats Available</span>
                            </div>

                            <h2 class="main-title">
                                Start Your Learning Journey <span class="highlight-orange">Today</span>
                            </h2>

                            <p class="description">
                                Choose from online and offline programs built to prepare you for real opportunities.
                                Focus on practical learning, interview preparation, and skill development that actually matters in the industry.
                            </p>

                            <!-- COUNTDOWN -->

                            <div class="countdown-wrapper">

                                <!-- DAYS -->

                                <div class="count-box">

                                    <h2 id="days">00</h2>

                                    <span>DAYS</span>

                                </div>

                                <div class="count-sep">:</div>

                                <!-- HOURS -->

                                <div class="count-box">

                                    <h2 id="hours">00</h2>

                                    <span>HOURS</span>

                                </div>

                                <div class="count-sep">:</div>

                                <!-- MINUTES -->

                                <div class="count-box">

                                    <h2 id="minutes">00</h2>

                                    <span>MINUTES</span>

                                </div>

                                <div class="count-sep">:</div>

                                <!-- SECONDS -->

                                <div class="count-box">

                                    <h2 id="seconds">00</h2>

                                    <span>SECONDS</span>

                                </div>

                            </div>

                            <!-- HIGHLIGHTS -->
                            <div class="journey-highlights">
                                <span><i class="fa-solid fa-circle-check"></i> Live Practical Sessions</span>
                                <span><i class="fa-solid fa-circle-check"></i> 100% Placement Cell</span>
                                <span><i class="fa-solid fa-circle-check"></i> Certified Diploma</span>
                            </div>

                        </div>

                    </div>

                    <!-- RIGHT -->

                    <div class="col-lg-6">

                        <div class="right-content">

                            <h3>Reserve Your Seat Now</h3>

                            <p class="form-text">
                                Fill the form today to receive complete syllabus details, upcoming batch dates, and fee breakdown.
                            </p>

                            <!-- FORM -->

                            <form class="free-course-form" action="form-submission.php" method="GET">

                                <div class="journey-input-group">
                                    <i class="fa-regular fa-user"></i>
                                    <input type="text" name="name" class="form-control" placeholder="Full Name*" required>
                                </div>

                                <div class="journey-input-group">
                                    <i class="fa-regular fa-envelope"></i>
                                    <input type="email" name="email" class="form-control" placeholder="Email Address*" required>
                                </div>

                                <div class="journey-input-group">
                                    <i class="fa-solid fa-phone"></i>
                                    <input type="tel" name="phone" class="form-control" placeholder="Phone Number*" required>
                                </div>

                                <!-- BUTTON -->

                                <button type="submit" class="submit-btn">
                                    <span>Get Free Counseling & Details</span>
                                    <i class="fa-solid fa-arrow-right"></i>
                                </button>

                                <div class="form-privacy-note">
                                    <i class="fa-solid fa-shield-halved"></i>
                                    <span>100% confidential. No spam guaranteed.</span>
                                </div>

                            </form>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </section>


    <!-- =================PopularCOURSE SECTION ================= -->


    <section class="popular-course-section">

        <div class="container">

            <!-- HEADER -->

            <div class="row align-items-end section-header g-4">

                <!-- LEFT -->

                <div class="col-lg-8">

                    <div class="course-section-kicker">
                        <i class="fa-solid fa-laptop-file"></i>
                        <span>ONLINE & FLEXIBLE PROGRAMS</span>
                    </div>

                    <h2>
                        Skill-Based Online Courses <span class="highlight-orange">We Offer</span>
                    </h2>

                    <p>
                        Explore our skill-based online courses designed to build practical knowledge and job-ready
                        skills. Learn at your own pace with simple, focused training that helps you prepare for real
                        career opportunities.
                    </p>

                </div>

                <!-- RIGHT -->

                <div class="col-lg-4 text-lg-end">

                    <a href="courses.php" class="view-btn mt-0">
                        <span>View All Courses</span>
                        <i class="fa-solid fa-arrow-right ms-2"></i>
                    </a>

                </div>

            </div>

            <!-- COURSES -->

            <div class="row g-4">

                <!-- CARD 1 -->

                <div class="col-md-6">

                    <div class="course-card">

                        <!-- IMAGE -->

                        <div class="course-image">

                            <img src="img/airport-management-course.png" alt="Professional Course in Airport Management">
                            <span class="course-cat-badge">
                                <i class="fa-solid fa-plane-departure"></i> Airport Operations
                            </span>
                            <span class="course-duration-badge">
                                <i class="fa-regular fa-clock"></i> 12 Months
                            </span>

                        </div>

                        <!-- CONTENT -->

                        <div class="course-content">

                            <h3>
                                Professional Course in Airport Management
                            </h3>

                            <p>
                                Equip yourself with essential skills in airport terminal operations, passenger services, security compliance, and airline coordination for successful careers in global aviation.
                            </p>

                            <div class="course-meta">
                                <span class="cm-item"><i class="fa-regular fa-clock"></i> Duration: <strong>12 Months</strong></span>
                                <span class="cm-item"><i class="fa-solid fa-briefcase"></i> 100% Placement</span>
                                <span class="cm-item"><i class="fa-solid fa-wifi"></i> Online / Hybrid</span>
                            </div>

                            <div class="course-card-footer">
                                <?= renderEnrollBtn('Airport & Ground Operations Management') ?>
                                <a href="course-detail.php?slug=airport-management-course" class="course-details-link">
                                    <span>View Details</span>
                                    <i class="fa-solid fa-chevron-right"></i>
                                </a>
                            </div>

                        </div>

                    </div>

                </div>

                <!-- CARD 2 -->

                <div class="col-md-6">

                    <div class="course-card">

                        <div class="course-image">

                            <img src="img/Travel-Tourism-course.jpg" alt="Certificate Course in Travel & Tourism">
                            <span class="course-cat-badge">
                                <i class="fa-solid fa-earth-americas"></i> Travel & Tourism
                            </span>
                            <span class="course-duration-badge">
                                <i class="fa-regular fa-clock"></i> 6 Months
                            </span>

                        </div>

                        <div class="course-content">

                            <h3>
                                Certificate Course in Travel & Tourism
                            </h3>

                            <p>
                                Gain practical expertise in international travel operations, tour package design, visa processing, customer relationship management, and airline ticketing.
                            </p>

                            <div class="course-meta">
                                <span class="cm-item"><i class="fa-regular fa-clock"></i> Duration: <strong>6 Months</strong></span>
                                <span class="cm-item"><i class="fa-solid fa-certificate"></i> Certified Course</span>
                                <span class="cm-item"><i class="fa-solid fa-globe"></i> Global Careers</span>
                            </div>

                            <div class="course-card-footer">
                                <?= renderEnrollBtn('Travel, Tourism & GDS Air Ticketing Professional') ?>
                                <a href="course-detail.php?slug=travel-air-ticketing-course" class="course-details-link">
                                    <span>View Details</span>
                                    <i class="fa-solid fa-chevron-right"></i>
                                </a>
                            </div>

                        </div>

                    </div>

                </div>

                <!-- CARD 3 -->

                <div class="col-md-6">

                    <div class="course-card">

                        <div class="course-image">

                            <img src="img/Hospitality-Customer-Service.jpg" alt="Certification in Hospitality & Customer Service">
                            <span class="course-cat-badge">
                                <i class="fa-solid fa-bell-concierge"></i> Hospitality & Relations
                            </span>
                            <span class="course-duration-badge">
                                <i class="fa-regular fa-clock"></i> 3 Months
                            </span>

                        </div>

                        <div class="course-content">

                            <h3>
                                Certification in Hospitality & Customer Service
                            </h3>

                            <p>
                                Master professional guest relationship management, front-office operations, conflict resolution, etiquette, and 5-star hospitality standards demanded across airlines and resorts.
                            </p>

                            <div class="course-meta">
                                <span class="cm-item"><i class="fa-regular fa-clock"></i> Duration: <strong>3 Months</strong></span>
                                <span class="cm-item"><i class="fa-solid fa-star"></i> 5-Star Service</span>
                                <span class="cm-item"><i class="fa-solid fa-graduation-cap"></i> Skill Booster</span>
                            </div>

                            <div class="course-card-footer">
                                <?= renderEnrollBtn('Executive Diploma in Hotel & Hospitality Management') ?>
                                <a href="course-detail.php?slug=hospitality-guest-relations-diploma" class="course-details-link">
                                    <span>View Details</span>
                                    <i class="fa-solid fa-chevron-right"></i>
                                </a>
                            </div>

                        </div>

                    </div>

                </div>

                <!-- CARD 4 -->

                <div class="col-md-6">

                    <div class="course-card">

                        <div class="course-image">

                            <img src="img/Cruise-Management-course.jpg" alt="Professional Course in Cruise Management">
                            <span class="course-cat-badge">
                                <i class="fa-solid fa-ship"></i> Cruise & Maritime
                            </span>
                            <span class="course-duration-badge">
                                <i class="fa-regular fa-clock"></i> 6 Months
                            </span>

                        </div>

                        <div class="course-content">

                            <h3>
                                Professional Course in Cruise Management
                            </h3>

                            <p>
                                Prepare for rewarding careers on international cruise lines with specialized training in shipboard hospitality, guest relations, safety protocols, and onboard crew operations.
                            </p>

                            <div class="course-meta">
                                <span class="cm-item"><i class="fa-regular fa-clock"></i> Duration: <strong>6 Months</strong></span>
                                <span class="cm-item"><i class="fa-solid fa-anchor"></i> Maritime Ready</span>
                                <span class="cm-item"><i class="fa-solid fa-briefcase"></i> Placement Support</span>
                            </div>

                            <div class="course-card-footer">
                                <?= renderEnrollBtn('Cruise Ship Hospitality & Service Operations') ?>
                                <a href="course-detail.php?slug=cruise-ship-operations-course" class="course-details-link">
                                    <span>View Details</span>
                                    <i class="fa-solid fa-chevron-right"></i>
                                </a>
                            </div>

                        </div>

                    </div>

                </div>

            </div>
        </div>

    </section>


    <!-- ================= PACKAGE COURSES / CAREER PLACEMENT SUPPORT ================= -->

    <section class="package-course-section">

        <div class="container">

            <!-- HEADER -->
            <div class="row align-items-center section-header g-4">

                <!-- LEFT -->
                <div class="col-lg-8">
                    <div class="course-section-kicker">
                        <i class="fa-solid fa-briefcase"></i>
                        <span>CAREER PLACEMENT ASSISTANCE</span>
                    </div>
                    <h2>
                        <span class="highlight-orange">Finchskills</span> Institute Career Placement Support in Multiple Industries
                    </h2>
                    <p>
                        Finchskills Institute Offers Job Placement Assistance Across Aviation, Hospitality, Travel &amp; Cruise Industries
                    </p>
                </div>

                <!-- RIGHT -->
                <div class="col-lg-4 text-lg-end">
                    <a href="courses.php" class="view-btn mt-0">
                        <span>View All Courses</span>
                        <i class="fa-solid fa-arrow-right"></i>
                    </a>
                </div>

            </div>

            <!-- CARDS ROW -->
            <div class="row g-4">

                <!-- CARD 1: AVIATION -->
                <div class="col-lg-4 col-md-6">
                    <div class="package-card">
                        <!-- IMAGE -->
                        <div class="package-image">
                            <img src="img/aviation-industry.png" alt="Aviation Industry Career Training">
                            <span class="package-cat-badge">
                                <i class="fa-solid fa-plane-departure"></i> Aviation Sector
                            </span>
                        </div>

                        <!-- CONTENT -->
                        <div class="package-content">
                            <h3 class="package-title">
                                Aviation Industry
                            </h3>
                            <p class="package-desc">
                                Discover exciting career opportunities in the aviation industry with Finchskills Institute, where expert training, industry exposure, and placement support prepare you for long-term professional growth.
                            </p>

                            <div class="package-roles">
                                <span class="role-chip"><i class="fa-solid fa-circle-check"></i> Cabin Crew</span>
                                <span class="role-chip"><i class="fa-solid fa-circle-check"></i> Ground Staff</span>
                                <span class="role-chip"><i class="fa-solid fa-circle-check"></i> Airport Ops</span>
                            </div>

                            <div class="package-card-footer">
                                <?= renderEnrollBtn('Professional Course in Air Hostess') ?>
                                <a href="courses.php" class="course-details-link">
                                    <span>View Details</span>
                                    <i class="fa-solid fa-chevron-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- CARD 2: HOTEL -->
                <div class="col-lg-4 col-md-6">
                    <div class="package-card">
                        <!-- IMAGE -->
                        <div class="package-image">
                            <img src="img/hotel-industry.png" alt="Hotel & Hospitality Career Training">
                            <span class="package-cat-badge">
                                <i class="fa-solid fa-hotel"></i> Hospitality Sector
                            </span>
                        </div>

                        <!-- CONTENT -->
                        <div class="package-content">
                            <h3 class="package-title">
                                Hotel Industry
                            </h3>
                            <p class="package-desc">
                                Finchskills Institute prepares aspiring hospitality professionals with industry-focused training, career guidance, and growth opportunities to build successful careers in the hotel industry.
                            </p>

                            <div class="package-roles">
                                <span class="role-chip"><i class="fa-solid fa-circle-check"></i> Front Office</span>
                                <span class="role-chip"><i class="fa-solid fa-circle-check"></i> Guest Relations</span>
                                <span class="role-chip"><i class="fa-solid fa-circle-check"></i> F&amp;B Service</span>
                            </div>

                            <div class="package-card-footer">
                                <?= renderEnrollBtn('Foundation Course in Hotel Management') ?>
                                <a href="courses.php" class="course-details-link">
                                    <span>View Details</span>
                                    <i class="fa-solid fa-chevron-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- CARD 3: TRAVEL -->
                <div class="col-lg-4 col-md-6">
                    <div class="package-card">
                        <!-- IMAGE -->
                        <div class="package-image">
                            <img src="img/travel-industry.png" alt="Travel & Tourism Career Training">
                            <span class="package-cat-badge">
                                <i class="fa-solid fa-earth-americas"></i> Travel &amp; Cruise
                            </span>
                        </div>

                        <!-- CONTENT -->
                        <div class="package-content">
                            <h3 class="package-title">
                                Travel Industry
                            </h3>
                            <p class="package-desc">
                                Finchskills Institute prepares aspiring travel professionals with industry-focused training, career guidance, and growth opportunities to build successful careers in the travel and tourism industry.
                            </p>

                            <div class="package-roles">
                                <span class="role-chip"><i class="fa-solid fa-circle-check"></i> Tour Manager</span>
                                <span class="role-chip"><i class="fa-solid fa-circle-check"></i> Cruise Staff</span>
                                <span class="role-chip"><i class="fa-solid fa-circle-check"></i> Travel Desk</span>
                            </div>

                            <div class="package-card-footer">
                                <?= renderEnrollBtn('Professional Course in Travel & Tourism') ?>
                                <a href="courses.php" class="course-details-link">
                                    <span>View Details</span>
                                    <i class="fa-solid fa-chevron-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>

    </section>

    <!-- ================= WHY SECTION ================= -->

    <section class="why-section">

        <div class="container">

            <div class="why-wrapper">

                <div class="row align-items-stretch g-4 g-lg-5">

                    <!-- LEFT -->
                    <div class="col-lg-6 d-flex flex-column justify-content-center">
                        <div class="why-content-wrapper">
                            <div class="course-section-kicker">
                                <i class="fa-solid fa-award"></i>
                                <span>WHY CHOOSE US</span>
                            </div>

                            <h2 class="why-title">
                                Why Students Choose <span class="highlight-orange">Finchskills</span> Institute
                            </h2>

                            <p class="why-subtitle">
                                Build strong, job-ready skills with practical training designed for aviation, hospitality, travel, and cruise careers.
                            </p>

                            <ul class="why-list">
                                <li>
                                    <i class="fa-solid fa-circle-check"></i>
                                    <span><strong>Expert Mentorship:</strong> Learn directly from industry professionals with real airline &amp; hospitality experience.</span>
                                </li>
                                <li>
                                    <i class="fa-solid fa-circle-check"></i>
                                    <span><strong>Job-Oriented Practical Training:</strong> Real grooming standards, mock interview rounds, and live terminal scenarios.</span>
                                </li>
                                <li>
                                    <i class="fa-solid fa-circle-check"></i>
                                    <span><strong>Flexible Study Options:</strong> Interactive online and classroom offline sessions tailored to your schedule.</span>
                                </li>
                                <li>
                                    <i class="fa-solid fa-circle-check"></i>
                                    <span><strong>Certification &amp; Placement Support:</strong> Verified credentials backed by active career assistance across leading brands.</span>
                                </li>
                            </ul>

                            <!-- CTA BUTTONS GROUP -->
                            <div class="why-actions-group">
                                <?php if (!empty($isStudentLoggedIn)): ?>
                                    <a href="form-submission.php" class="why-primary-btn">
                                        <i class="fa-solid fa-file-signature me-1"></i>
                                        <span>Apply Online</span>
                                        <i class="fa-solid fa-arrow-right ms-1"></i>
                                    </a>
                                <?php else: ?>
                                    <a href="register.php?redirect=form-submission.php" class="why-primary-btn">
                                        <i class="fa-solid fa-user-pen me-1"></i>
                                        <span>Register Yourself</span>
                                        <i class="fa-solid fa-arrow-right ms-1"></i>
                                    </a>
                                <?php endif; ?>
                                <a href="courses.php" class="why-outline-btn">
                                    <span>Explore Courses</span>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- RIGHT -->
                    <div class="col-lg-6 d-flex flex-column">
                        <div class="why-image-wrapper">
                            <div class="why-image-card">
                                <img src="img/why-learners-choose-us-img.png" alt="Why Choose Finchskills Institute">
                                <!-- TRUST BADGE -->
                                <div class="why-trust-pill">
                                    <i class="fa-solid fa-award"></i>
                                    <span>100% Practical &amp; Placement Focused</span>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

            </div>

        </div>

    </section>

    <!-- ================= EVENTS SECTION ================= -->

    <!-- <section class="event-section">
        <div class="container">

            <div class="row align-items-center section-header g-4">

                <div class="col-lg-8">

                    <h2>
                        Upcoming Courses & Training Programs
                    </h2>

                    <p>
                        Explore expert-led online sessions and skill-based programs
                        designed to help you grow and stay ahead in your career.
                    </p>

                </div>

                <div class="col-lg-4 text-lg-end">

                    <a href="courses.php" class="view-btn mt-0">
                        View All Courses
                    </a>

                </div>

            </div>

            <div class="row g-4">

                <div class="col-lg-6">
                    <div class="event-card d-lg-flex align-items-center">
                        <div class="event-date">
                            <h2>12</h2>
                            <span>Jun</span>
                        </div>
                        <div class="event-content">
                            
                            <h3 class="event-title">
                                Full Stack Web Development Masterclass
                            </h3>
                            <div class="event-info">
                                <span>
                                    <i class="fa-regular fa-clock"></i>
                                    10:00 am - 1:00 pm
                                </span>
                                <span>
                                    <i class="fa-solid fa-location-dot"></i>
                                    Online Live Session
                                </span>
                            </div>
                            <p class="event-desc">
                                Join our expert-led workshop and learn modern frontend
                                and backend development with practical live projects.
                            </p>
                        </div>
                        <div class="event-image">
                            <img src="img/event-1-450x233.webp" alt="Event">
                        </div>
                    </div>
                </div>


                <div class="col-lg-6">

                    <div class="event-card d-lg-flex align-items-center">

                        <div class="event-date">

                            <h2>18</h2>

                            <span>Jun</span>

                        </div>

                        <div class="event-content">

                            <h3 class="event-title">
                                Digital Marketing & SEO Training
                            </h3>

                            <div class="event-info">

                                <span>
                                    <i class="fa-regular fa-clock"></i>
                                    2:00 pm - 5:00 pm
                                </span>

                                <span>
                                    <i class="fa-solid fa-location-dot"></i>
                                    Virtual Classroom
                                </span>

                            </div>

                            <p class="event-desc">
                                Learn SEO, social media marketing, content strategy,
                                and paid advertising from industry professionals.
                            </p>

                        </div>

                        <div class="event-image">

                            <img src="img/event-2-450x233.webp" alt="Event">

                        </div>

                    </div>

                </div>


                <div class="col-lg-6">

                    <div class="event-card d-lg-flex align-items-center">

                        <div class="event-date">

                            <h2>25</h2>

                            <span>Jun</span>

                        </div>

                        <div class="event-content">

                            <h3 class="event-title">
                                UI/UX Design Practical Workshop
                            </h3>

                            <div class="event-info">

                                <span>
                                    <i class="fa-regular fa-clock"></i>
                                    11:00 am - 3:00 pm
                                </span>

                                <span>
                                    <i class="fa-solid fa-location-dot"></i>
                                    Online Workshop
                                </span>

                            </div>

                            <p class="event-desc">
                                Explore modern UI/UX design techniques, wireframing,
                                prototyping, and user experience strategies.
                            </p>

                        </div>

                        <div class="event-image">

                            <img src="img/event-7-450x233.webp" alt="Event">

                        </div>

                    </div>

                </div>


                <div class="col-lg-6">

                    <div class="event-card d-lg-flex align-items-center">

                        <div class="event-date">

                            <h2>30</h2>

                            <span>Jun</span>

                        </div>

                        <div class="event-content">

                            <h3 class="event-title">
                                Artificial Intelligence & Data Science Bootcamp
                            </h3>

                            <div class="event-info">

                                <span>
                                    <i class="fa-regular fa-clock"></i>
                                    9:00 am - 4:00 pm
                                </span>

                                <span>
                                    <i class="fa-solid fa-location-dot"></i>
                                    Live Online Program
                                </span>

                            </div>

                            <p class="event-desc">
                                Gain hands-on experience in AI tools, machine learning,
                                and data analytics through real-world projects.
                            </p>

                        </div>

                        <div class="event-image">

                            <img src="img/courses-14-450x233.webp" alt="Event">

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </section> -->




    <!-- ================= TESTIMONIAL SECTION ================= -->

    <section class="testimonial-section">

        <div class="container">

            <div class="testimonial-wrapper">

                <div class="row align-items-center g-4">

                    <!-- LEFT -->

                    <div class="col-lg-6">

                        <h2 class="section-title">
                            What Our Learners Say
                        </h2>

                        <p class="section-subtitle">
                            Hear from students who transformed their skills and
                            achieved their career goals with Finchskill Institute.
                        </p>

                        <!-- USER GROUP -->

                        <div class="user-group">

                            <div class="avatars">

                                <!-- USER 1 -->

                                <img src="img/womens-profile.png" class="testimonial-user active"
                                    data-text="Finchskill Institute helped me build practical skills through real projects and expert guidance. The learning experience was excellent."
                                    data-name="Sophia Miller" data-role="UI/UX Designer" alt="User">

                                <!-- USER 2 -->

                                <img src="img/mens-profile.png" class="testimonial-user"
                                    data-text="The courses were easy to understand and highly industry-focused. I gained confidence and improved my technical skills."
                                    data-name="James Walker" data-role="Frontend Developer" alt="User">

                                <!-- USER 3 -->

                                <img src="img/womens-profile.png" class="testimonial-user"
                                    data-text="The mentors were supportive and the practical assignments helped me prepare for real-world opportunities."
                                    data-name="Emily Johnson" data-role="Digital Marketer" alt="User">

                                <!-- USER 4 -->

                                <img src="img/mens-profile.png" class="testimonial-user"
                                    data-text="I really enjoyed the flexible online learning experience. The course structure was simple, practical, and effective."
                                    data-name="Olivia Brown" data-role="Data Analyst" alt="User">

                                <!-- USER 5 -->

                                <img src="img/womens-profile.png" class="testimonial-user"
                                    data-text="Finchskill Institute provided valuable career-focused training and helped me improve my professional skills with confidence."
                                    data-name="Daniel Smith" data-role="Software Engineer" alt="User">

                            </div>

                        </div>

                    </div>

                    <!-- RIGHT -->

                    <div class="col-lg-6">

                        <div class="owl-carousel testimonial-carousel owl-theme">

                            <!-- ITEM 1 -->

              <div class="item">
                <div class="testimonial-box">
                  <div class="testimonial-author">
                    <img src="img/womens-profile.png" alt="User" />
                    <div class="author-content">
                      <h4>Priya Sharma</h4>
                      <span>Air Hostess</span>
                    </div>
                  </div>
                  <p class="testimonial-text">
                    "Finchskills Institute provided an excellent online learning experience. The trainers were supportive, the classes were interactive, and the personality development sessions greatly improved my confidence and communication skills."
                  </p>
                </div>
              </div>

              <!-- ITEM 2 -->

              <div class="item">
                <div class="testimonial-box">
                  <div class="testimonial-author">
                    <img src="img/mens-profile.png" alt="User" />
                    <div class="author-content">
                      <h4>Rahul Verma</h4>
                      <span>Airport Terminal Management</span>
                    </div>
                  </div>
                  <p class="testimonial-text">
                    "The course was well-organized and easy to follow. The faculty explained airport operations clearly, and the flexible online classes made learning convenient. Finchskills Institute is a great choice for professional skill development."
                  </p>
                </div>
              </div>

              <!-- ITEM 3 -->

              <div class="item">
                <div class="testimonial-box">
                  <div class="testimonial-author">
                    <img src="img/womens-profile.png" alt="User" />
                    <div class="author-content">
                      <h4>Sneha Patel</h4>
                      <span>Travel & Tourism</span>
                    </div>
                  </div>
                  <p class="testimonial-text">
                    "I had a wonderful experience learning with Finchskills Institute. The study material was informative, the trainers were knowledgeable, and the online sessions helped me build confidence and industry knowledge. Highly recommended!"
                  </p>
                </div>
              </div>

              <!-- ITEM 4 -->

              <div class="item">
                <div class="testimonial-box">
                  <div class="testimonial-author">
                    <img src="img/mens-profile.png" alt="User" />
                    <div class="author-content">
                      <h4>Ankit Singh</h4>
                      <span>Hotel Management</span>
                    </div>
                  </div>
                  <p class="testimonial-text">
                    "Finchskills Institute made online learning simple and engaging. The faculty was experienced, the course content was practical, and every session helped me improve my hospitality skills. It was a valuable learning experience that boosted my confidence."
                  </p>
                </div>
              </div>

              <!-- ITEM 5 -->

              <div class="item">
                <div class="testimonial-box">
                  <div class="testimonial-author">
                    <img src="img/womens-profile.png" alt="User" />
                    <div class="author-content">
                      <h4>Ayesha Khan</h4>
                      <span>Customer Service</span>
                    </div>
                  </div>
                  <p class="testimonial-text">
                    "The online course at Finchskills Institute exceeded my expectations. The trainers explained every topic clearly, and the practical examples made learning easy. I gained valuable customer service skills and felt more confident in my professional communication."
                  </p>
                </div>
              </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </section>

    <!-- ================= INSTRUCTOR SECTION ================= -->

    <section class="instructor-section">

        <div class="container">

            <div class="instructor-content">

                <!-- TITLE -->

                <h2 class="instructor-title">

                    Build Your Skills.<br>
                    Shape Your Future.

                </h2>

                <!-- SUBTITLE -->

                <p class="instructor-subtitle">

                    Join expert-led online courses, gain practical knowledge,
                    and achieve your career goals with confidence.

                </p>
                <!-- BUTTON -->

                <?php if (!empty($isStudentLoggedIn)): ?>
                    <a href="form-submission.php" class="instructor-btn">
                        <i class="fa-solid fa-file-signature me-2"></i>Apply Online Now
                    </a>
                <?php else: ?>
                    <a href="register.php?redirect=form-submission.php" class="instructor-btn">
                        <i class="fa-solid fa-user-pen me-2"></i>Register Yourself
                    </a>
                <?php endif; ?>

            </div>

        </div>

    </section>


        <!-- Footer  -->
    <?php include "footer.php" ?>



    <script>
        $(document).ready(function () {
            const users = document.querySelectorAll(".testimonial-user");
            const owl = $(".testimonial-carousel");

            users.forEach((user, index) => {
                user.addEventListener("click", () => {
                    // REMOVE ACTIVE FROM ALL AVATARS
                    users.forEach(item => item.classList.remove("active"));
                    // ADD ACTIVE TO CLICKED AVATAR
                    user.classList.add("active");
                    // SYNC CAROUSEL
                    owl.trigger('to.owl.carousel', [index, 600]);
                });
            });

            // Optional: Update active avatar when carousel changes
            owl.on('changed.owl.carousel', function (event) {
                const index = event.item.index - event.relatedTarget._clones.length / 2;
                const realIndex = index % users.length;

                users.forEach(item => item.classList.remove("active"));
                if (users[realIndex]) {
                    users[realIndex].classList.add("active");
                }
            });
        });
    </script>



    <!-- TIMER SCRIPT -->

    <script>

        const targetDate = new Date().getTime() + (
            (62 * 24 * 60 * 60 * 1000) +
            (8 * 60 * 60 * 1000) +
            (32 * 60 * 1000) +
            (17 * 1000)
        );

        function updateCountdown() {

            const now = new Date().getTime();

            const distance = targetDate - now;

            const days = Math.floor(distance / (1000 * 60 * 60 * 24));

            const hours = Math.floor(
                (distance % (1000 * 60 * 60 * 24)) /
                (1000 * 60 * 60)
            );

            const minutes = Math.floor(
                (distance % (1000 * 60 * 60)) /
                (1000 * 60)
            );

            const seconds = Math.floor(
                (distance % (1000 * 60)) / 1000
            );

            document.getElementById("days").innerHTML =
                String(days).padStart(2, '0');

            document.getElementById("hours").innerHTML =
                String(hours).padStart(2, '0');

            document.getElementById("minutes").innerHTML =
                String(minutes).padStart(2, '0');

            document.getElementById("seconds").innerHTML =
                String(seconds).padStart(2, '0');

        }

        updateCountdown();

        setInterval(updateCountdown, 1000);

    </script>


<!-- Owl Carousel Initialization -->
<script>
    $(document).ready(function () {
        $(".testimonial-carousel").owlCarousel({
            items: 1,
            loop: true,
            autoplay: true,
            autoplayTimeout: 5000,
            autoplayHoverPause: true,
            smartSpeed: 800,
            dots: true,
            nav: false
        });
    });
</script>

<!-- TAB JS -->

<script>

    const tabBtns = document.querySelectorAll(".tab-btn");
    const contents = document.querySelectorAll(".course-content");

    tabBtns.forEach(btn => {

        btn.addEventListener("mouseenter", () => {

            tabBtns.forEach(item => {
                item.classList.remove("active");
            });

            btn.classList.add("active");

            const target = btn.getAttribute("data-tab");

            contents.forEach(content => {
                content.classList.remove("active-content");
            });

            document.getElementById(target).classList.add("active-content");

        });

    });

</script>

