<!-- Header  -->
<?php include "header.php" ?>


<!-- ===================================== -->
<!-- BLOG HERO -->
<!-- ===================================== -->

<section class="admission-hero-section">

    <div class="container">

        <h1 class="admission-hero-title">
            Online Admission
        </h1>
        <!-- <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">About Us</li>
                </ol>
            </nav> -->
        <div class="bloghero-breadcrumb text-start">

            <a href="index.php">Home</a>

            <span>
                <i class="fa-solid fa-chevron-right"></i>
            </span>

            <a href="#">Online Admission</a>

        </div>
    </div>
</section>


<section class="eduadm-main-wrapper">

    <div class="container-fluid">

        <!-- TITLE -->

        <div class="eduadm-form-title">

            <h1>Online Admission</h1>

            <div class="eduadm-title-line"></div>

        </div>

        <!-- FORM -->

        <div class="eduadm-form-box">

            <form action="send-admission.php" method="POST" enctype="multipart/form-data">

                <div class="row g-4">

                    <div class="col-lg-4 col-md-6">
                        <label class="eduadm-label">Name*</label>
                        <input type="text" name="name" class="eduadm-input" required>
                    </div>

                    <div class="col-lg-4 col-md-6">
                        <label class="eduadm-label">Father's Name*</label>
                        <input type="text" name="father_name" class="eduadm-input" required>
                    </div>

                    <div class="col-lg-4 col-md-6">
                        <label class="eduadm-label">Email*</label>
                        <input type="email" name="email" class="eduadm-input" required>
                    </div>

                    <div class="col-lg-4 col-md-6">
                        <label class="eduadm-label">Aadhaar*</label>
                        <input type="text" name="aadhaar" class="eduadm-input" required>
                    </div>

                    <div class="col-lg-4 col-md-6">
                        <label class="eduadm-label">Mobile No.*</label>
                        <input type="text" name="mobile" class="eduadm-input" required>
                    </div>

                    <div class="col-lg-4 col-md-6">
                        <label class="eduadm-label">DOB*</label>
                        <input type="date" name="dob" class="eduadm-input" required>
                    </div>

                    <div class="col-lg-4 col-md-6">
                        <label class="eduadm-label">Gender*</label>

                        <select name="gender" class="eduadm-select" required>
                            <option value="">Select Gender</option>
                            <option>Male</option>
                            <option>Female</option>
                            <option>Other</option>
                        </select>
                    </div>

                    <div class="col-lg-4 col-md-6">
                        <label class="eduadm-label">Course*</label>

                        <?php
                        $selected_course = isset($_GET['course']) ? trim($_GET['course']) : '';
                        $db_courses = [];
                        try {
                            if (file_exists(__DIR__ . '/db.php')) {
                                require_once __DIR__ . '/db.php';
                                $c_stmt = $pdo->query("SELECT title FROM courses WHERE status = 'active' ORDER BY title ASC");
                                $db_courses = $c_stmt->fetchAll(PDO::FETCH_COLUMN);
                            }
                        } catch (Exception $e) {}

                        $fallback_courses = [
                            "Airport & Ground Operations Management",
                            "Executive Diploma in Hotel & Hospitality Management",
                            "Travel, Tourism & GDS Air Ticketing Professional",
                            "Cruise Ship Hospitality & Service Operations",
                            "Foundation Course in Tourism",
                            "Certificate Course in Travel & Air Ticketing",
                            "Professional Course in Travel & Tourism",
                            "Professional Course in Ground Staff & Hospitality",
                            "Professional Course in Personality Development",
                            "Professional Course in Airport Terminal Management",
                            "Certificate Course in Customer Service",
                            "Professional Course in Event Management",
                            "Foundation Course in Hotel Management",
                            "Professional Course in Air Hostess",
                            "Professional Course in Cabin Crew"
                        ];

                        $course_options = !empty($db_courses) ? array_unique(array_merge($db_courses, $fallback_courses)) : $fallback_courses;
                        ?>

                        <select name="course" class="eduadm-select" required>
                            <option value="">Select Course</option>
                            <?php foreach ($course_options as $c_opt): ?>
                                <option value="<?= htmlspecialchars($c_opt) ?>" <?= (strcasecmp($selected_course, $c_opt) === 0) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($c_opt) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-lg-4 col-md-6">
                        <label class="eduadm-label">Education*</label>

                        <select name="education" class="eduadm-select" required>
                            <option value="">Select Education</option>
                            <option>10th</option>
                            <option>12th</option>
                        </select>
                    </div>

                    <div class="col-lg-4 col-md-6">
                        <label class="eduadm-label">State*</label>

                        <select name="state" class="eduadm-select" required>
                            <option value="">Select State</option>
                            <option>Andhra Pradesh</option>
                            <option>Arunachal Pradesh</option>
                            <option>Assam</option>
                            <option>Bihar</option>
                            <option>Chhattisgarh</option>
                            <option>Goa</option>
                            <option>Gujarat</option>
                            <option>Haryana</option>
                            <option>Himachal Pradesh</option>
                            <option>Karnataka</option>
                            <option>Kerala</option>
                            <option>Madhya Pradesh</option>
                            <option>Maharashtra</option>
                            <option>Manipur</option>
                            <option>Meghalaya</option>
                            <option>Mizoram</option>
                            <option>Nagaland</option>
                            <option>Odisha</option>
                            <option>Punjab</option>
                            <option>Rajasthan</option>
                            <option>Sikkim</option>
                            <option>Tamil Nadu</option>
                            <option>Telangana</option>
                            <option>Tripura</option>
                            <option>Uttar Pradesh</option>
                            <option>Uttarakhand</option>
                            <option>West Bengal</option>
                        </select>
                    </div>

                    <div class="col-lg-4 col-md-6">
                        <label class="eduadm-label">City*</label>
                        <input type="text" name="city" class="eduadm-input" required>
                    </div>

                    <div class="col-lg-4 col-md-6">
                        <label class="eduadm-label">Post Office*</label>
                        <input type="text" name="post_office" class="eduadm-input" required>
                    </div>

                    <div class="col-lg-4 col-md-6">
                        <label class="eduadm-label">Pincode*</label>
                        <input type="text" name="pincode" class="eduadm-input" required>
                    </div>

                    <div class="col-lg-4 col-md-6">
                        <label class="eduadm-label">Address*</label>
                        <input type="text" name="address" class="eduadm-input" required>
                    </div>

                    <div class="col-lg-4 col-md-6">
                        <label class="eduadm-label">10th Marksheet</label>
                        <input type="file" class="eduadm-file" name="marksheet10">
                    </div>

                    <div class="col-lg-4 col-md-6">
                        <label class="eduadm-label">12th Marksheet</label>
                        <input type="file" class="eduadm-file" name="marksheet12">
                    </div>

                    <!-- <div class="col-lg-4 col-md-6">
                        <label class="eduadm-label">Graduation Document</label>
                        <input type="file" class="eduadm-file" name="graduation">
                    </div> -->

                    <div class="col-lg-4 col-md-6">
                        <label class="eduadm-label">Aadhaar Card</label>
                        <input type="file" class="eduadm-file" name="aadhaar_card">
                    </div>

                    <div class="col-lg-4 col-md-6">
                        <label class="eduadm-label">Photo</label>
                        <input type="file" class="eduadm-file" name="photo">
                    </div>

                </div>

                <div class="eduadm-check-wrap">
                    <input type="checkbox" id="terms" class="mt-0" required>
                    <label for="terms" class="text-dark">I agree to <a href="terms-conditions.php">Terms & Conditions</a>.</label>
                </div>

                <button type="submit" class="eduadm-submit-btn">
                    Submit
                </button>

            </form>

        </div>

    </div>

</section>

<!-- Success & Error Modals, and Submit Loader -->
<?php if (isset($_GET['success'])): ?>
<div class="custom-modal-overlay show" id="successModal">
    <div class="custom-modal-card">
        <div class="success-checkmark-wrapper">
            <svg class="checkmark-svg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52">
                <circle class="checkmark-circle" cx="26" cy="26" r="25" fill="none"/>
                <path class="checkmark-check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8"/>
            </svg>
        </div>
        <h2 class="custom-modal-title">Admission Form Submitted Successfully</h2>
        <p class="custom-modal-text">
            Thank you for applying! Your admission form details have been submitted successfully. Please proceed to the fee submission page to finalize your registration.
        </p>
        <div class="custom-modal-actions">
            <a href="fee-submission.php" class="custom-modal-btn">
                Click to Fee Submission <i class="fa-solid fa-arrow-right ms-2"></i>
            </a>
        </div>
    </div>
</div>
<script>
    // Clean history URL parameter so refreshing doesn't show the modal again
    if (window.history.replaceState) {
        const url = new URL(window.location.href);
        url.searchParams.delete('success');
        window.history.replaceState({ path: url.href }, '', url.href);
    }
</script>
<?php endif; ?>

<?php if (isset($_GET['error'])): ?>
<div class="custom-modal-overlay show" id="errorModal">
    <div class="custom-modal-card">
        <div class="error-cross-wrapper">
            <i class="fa-solid fa-circle-xmark text-danger" style="font-size: 80px; margin-bottom: 25px; display: inline-block;"></i>
        </div>
        <h2 class="custom-modal-title text-danger">Submission Failed</h2>
        <p class="custom-modal-text">
            <?php echo htmlspecialchars($_GET['error']); ?>
        </p>
        <div class="custom-modal-actions">
            <button onclick="closeErrorModal()" class="custom-modal-btn btn-secondary-custom">
                Close <i class="fa-solid fa-xmark ms-2"></i>
            </button>
        </div>
    </div>
</div>
<script>
    function closeErrorModal() {
        document.getElementById('errorModal').classList.remove('show');
        if (window.history.replaceState) {
            const url = new URL(window.location.href);
            url.searchParams.delete('error');
            window.history.replaceState({ path: url.href }, '', url.href);
        }
    }
</script>
<?php endif; ?>

<!-- Styling for Custom Modals and Loader -->
<style>
/* Modal Overlay with Glassmorphism */
.custom-modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(14, 30, 46, 0.75);
    backdrop-filter: blur(8px);
    -webkit-backdrop-filter: blur(8px);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 99999;
    opacity: 0;
    visibility: hidden;
    transition: opacity 0.4s ease, visibility 0.4s ease;
}

.custom-modal-overlay.show {
    opacity: 1;
    visibility: visible;
}

/* Modal Card */
.custom-modal-card {
    background: #ffffff;
    border-radius: 24px;
    padding: 45px 35px;
    width: 90%;
    max-width: 520px;
    text-align: center;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.35);
    transform: scale(0.85) translateY(30px);
    transition: transform 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.custom-modal-overlay.show .custom-modal-card {
    transform: scale(1) translateY(0);
}

/* Success SVG Checkmark Animation */
.success-checkmark-wrapper {
    width: 80px;
    height: 80px;
    margin: 0 auto 25px;
}

.checkmark-svg {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    display: block;
    stroke-width: 4;
    stroke: #ffffff;
    stroke-miterlimit: 10;
    box-shadow: inset 0px 0px 0px #4caf50;
    animation: fill-checkmark .4s ease-in-out .4s forwards, scale-checkmark .3s ease-in-out .9s;
}

.checkmark-circle {
    stroke-dasharray: 166;
    stroke-dashoffset: 166;
    stroke-width: 4;
    stroke-miterlimit: 10;
    stroke: #4caf50;
    fill: none;
    animation: stroke-checkmark .6s cubic-bezier(0.65, 0, 0.45, 1) forwards;
}

.checkmark-check {
    transform-origin: 50% 50%;
    stroke-dasharray: 48;
    stroke-dashoffset: 48;
    animation: stroke-checkmark .3s cubic-bezier(0.65, 0, 0.45, 1) .8s forwards;
}

@keyframes stroke-checkmark {
    100% {
        stroke-dashoffset: 0;
    }
}

@keyframes fill-checkmark {
    100% {
        box-shadow: inset 0px 0px 0px 40px #4caf50;
    }
}

@keyframes scale-checkmark {
    0%, 100% {
        transform: none;
    }
    50% {
        transform: scale3d(1.1, 1.1, 1);
    }
}

/* Typography */
.custom-modal-title {
    color: #0e1e2e;
    font-size: 26px;
    font-weight: 700;
    margin-bottom: 16px;
    line-height: 1.3;
}

.custom-modal-text {
    color: #555555;
    font-size: 16px;
    line-height: 1.6;
    margin-bottom: 35px;
}

/* Action Buttons */
.custom-modal-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: #f5a400;
    color: #ffffff !important;
    padding: 15px 32px;
    border-radius: 50px;
    font-size: 16px;
    font-weight: 600;
    text-decoration: none !important;
    transition: all 0.3s ease;
    box-shadow: 0 6px 20px rgba(245, 164, 0, 0.3);
    border: none;
    outline: none;
    cursor: pointer;
}

.custom-modal-btn:hover {
    background: #0e1e2e;
    box-shadow: 0 6px 20px rgba(14, 30, 46, 0.3);
    transform: translateY(-2px);
}

.custom-modal-btn:active {
    transform: translateY(0);
}

.btn-secondary-custom {
    background: #6c757d;
    box-shadow: 0 6px 20px rgba(108, 117, 125, 0.3);
}

.btn-secondary-custom:hover {
    background: #495057;
    box-shadow: 0 6px 20px rgba(73, 80, 87, 0.3);
}

/* Submit Loader Styling */
.submit-loading-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(14, 30, 46, 0.7);
    backdrop-filter: blur(6px);
    -webkit-backdrop-filter: blur(6px);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 99999;
}

.submit-loading-card {
    background: #ffffff;
    border-radius: 20px;
    padding: 40px;
    text-align: center;
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
    max-width: 420px;
    width: 90%;
}

.submit-loading-card h4 {
    font-weight: 600;
    margin-top: 20px;
}
</style>

<script>
// Attach loading overlay on form submit to provide instant feedback and prevent double submission
document.addEventListener("DOMContentLoaded", function() {
    const admissionForm = document.querySelector('.eduadm-form-box form');
    if (admissionForm) {
        admissionForm.addEventListener('submit', function() {
            // Create loading overlay
            const loader = document.createElement('div');
            loader.className = 'submit-loading-overlay';
            loader.innerHTML = `
                <div class="submit-loading-card">
                    <div class="spinner-border" role="status" style="width: 3.5rem; height: 3.5rem; color: #f5a400;">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <h4 class="text-dark">Submitting Admission Form...</h4>
                    <p class="text-muted mb-0 mt-2" style="font-size: 14px;">Please do not refresh this page or close the window.</p>
                </div>
            `;
            document.body.appendChild(loader);
        });
    }
});
</script>

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

<!-- FOOTER  -->
<?php include "footer.php" ?>