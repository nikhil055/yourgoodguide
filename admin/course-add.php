<?php
require_once __DIR__ . '/includes/auth.php';
checkAdminAuth();

$page_title = "Add New Course";
$error_msg = '';

// Helper to make slug
function makeSlug($string) {
    $slug = preg_replace('/[^a-z0-9]+/i', '-', trim(strtolower($string)));
    return trim($slug, '-');
}

// Fetch categories
$categories = $pdo->query("SELECT * FROM course_categories WHERE status = 'active' ORDER BY name ASC")->fetchAll();

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category_id = (int)($_POST['category_id'] ?? 0);
    $title = trim($_POST['title'] ?? '');
    $slug = trim($_POST['slug'] ?? '');
    if (empty($slug)) {
        $slug = makeSlug($title);
    } else {
        $slug = makeSlug($slug);
    }

    $duration = trim($_POST['duration'] ?? '12 Months');
    $study_mode = trim($_POST['study_mode'] ?? 'Classroom & Practical Labs');
    $short_desc = trim($_POST['short_desc'] ?? '');
    $full_desc = trim($_POST['full_desc'] ?? '');
    $what_you_learn = trim($_POST['what_you_learn'] ?? '');
    $how_we_teach = trim($_POST['how_we_teach'] ?? '');
    $career_roles = trim($_POST['career_roles'] ?? '');
    $eligibility = trim($_POST['eligibility'] ?? '10+2 (12th Pass) in any stream');
    $certification = trim($_POST['certification'] ?? 'Industry Recognized Certification');
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    $status = ($_POST['status'] ?? 'active') === 'inactive' ? 'inactive' : 'active';

    if (empty($title)) {
        $error_msg = 'Please enter a course title.';
    } elseif ($category_id <= 0) {
        $error_msg = 'Please select a valid course category.';
    } else {
        // Ensure unique slug
        $chk = $pdo->prepare("SELECT id FROM courses WHERE slug = ?");
        $chk->execute([$slug]);
        if ($chk->fetch()) {
            $slug .= '-' . rand(10, 99);
        }

        // Image upload handling
        $image_path = null;
        if (isset($_FILES['course_image']) && $_FILES['course_image']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = __DIR__ . '/../uploads/courses/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            $ext = strtolower(pathinfo($_FILES['course_image']['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
            if (in_array($ext, $allowed)) {
                $new_filename = 'course_' . time() . '_' . rand(100, 999) . '.' . $ext;
                if (move_uploaded_file($_FILES['course_image']['tmp_name'], $upload_dir . $new_filename)) {
                    $image_path = 'uploads/courses/' . $new_filename;
                }
            } else {
                $error_msg = 'Invalid image format. Allowed formats: JPG, PNG, WEBP.';
            }
        }

        if (empty($error_msg)) {
            $stmt = $pdo->prepare("INSERT INTO courses (
                category_id, title, slug, duration, study_mode, image, short_desc, full_desc,
                what_you_learn, how_we_teach, career_roles, eligibility, certification, is_featured, status
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $category_id, $title, $slug, $duration, $study_mode, $image_path, $short_desc, $full_desc,
                $what_you_learn, $how_we_teach, $career_roles, $eligibility, $certification, $is_featured, $status
            ]);

            header("Location: courses.php?msg=added");
            exit;
        }
    }
}

include __DIR__ . '/includes/header.php';
?>

<!-- Summernote Lite WYSIWYG Editor Styles -->
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-lite.min.css" rel="stylesheet">
<style>
.note-editor.note-frame {
    border: 1px solid #cbd5e1;
    border-radius: 10px;
    box-shadow: none !important;
}
.note-editor.note-frame .note-statusbar {
    border-top: 1px solid #f1f5f9;
}
.note-toolbar {
    background: #f8fafc !important;
    border-bottom: 1px solid #e2e8f0 !important;
    border-top-left-radius: 9px;
    border-top-right-radius: 9px;
    padding: 6px 8px !important;
}
.note-btn {
    border: 1px solid transparent !important;
    background: transparent !important;
    border-radius: 6px !important;
    padding: 5px 8px !important;
}
.note-btn:hover {
    background: #e2e8f0 !important;
}
.preview-box {
    width: 100%;
    height: 180px;
    border: 2px dashed #cbd5e1;
    border-radius: 12px;
    background: #f8fafc;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    position: relative;
}
.preview-box img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
</style>

<!-- ================= BREADCRUMBS & TOP BAR ================= -->
<div class="mb-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb small mb-2">
            <li class="breadcrumb-item"><a href="index.php" class="text-decoration-none">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="courses.php" class="text-decoration-none">Manage Courses</a></li>
            <li class="breadcrumb-item active text-dark fw-semibold" aria-current="page">Add New Course</li>
        </ol>
    </nav>
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
        <div>
            <h4 class="fw-bold text-dark mb-0">Create New Course</h4>
            <p class="text-muted small mb-0">Enter course curriculum, practical training details, and overview.</p>
        </div>
        <div>
            <a href="courses.php" class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-2">
                <i class="fa-solid fa-arrow-left"></i>
                <span>Back to Courses</span>
            </a>
        </div>
    </div>
</div>

<!-- ================= ERROR ALERT ================= -->
<?php if (!empty($error_msg)): ?>
    <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2 mb-4 shadow-sm" role="alert">
        <i class="fa-solid fa-circle-exclamation fs-5 text-danger"></i>
        <div><?= htmlspecialchars($error_msg) ?></div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<!-- ================= MAIN FORM ================= -->
<form method="POST" action="course-add.php" enctype="multipart/form-data" id="courseForm">
    <div class="row g-4">

        <!-- ================= LEFT COLUMN: MAIN CONTENT (8 COLS) ================= -->
        <div class="col-lg-8">

            <!-- CARD 1: BASIC INFORMATION -->
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                        <i class="fa-solid fa-file-lines text-primary"></i>
                        <span>Basic Course Information</span>
                    </h6>
                </div>
                <div class="card-body p-4">
                    
                    <!-- TITLE -->
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark small">Course Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" id="courseTitle" class="form-control" placeholder="e.g. Professional Course in Air Hostess & Cabin Crew" value="<?= htmlspecialchars($_POST['title'] ?? '') ?>" required>
                        <div class="form-text small">Use a clear, industry-recognized program title.</div>
                    </div>

                    <!-- SLUG -->
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark small">URL Slug <span class="text-muted fw-normal">(Auto-generated or custom)</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted small border-end-0">/course-detail.php?slug=</span>
                            <input type="text" name="slug" id="courseSlug" class="form-control border-start-0" placeholder="professional-course-in-air-hostess" value="<?= htmlspecialchars($_POST['slug'] ?? '') ?>">
                        </div>
                    </div>

                    <!-- SHORT DESCRIPTION -->
                    <div class="mb-0">
                        <label class="form-label fw-bold text-dark small">Short Description / Card Excerpt <span class="text-danger">*</span></label>
                        <textarea name="short_desc" class="form-control" rows="2" placeholder="Brief summary (1-2 sentences) shown on course listing cards and hero snippet..."><?= htmlspecialchars($_POST['short_desc'] ?? '') ?></textarea>
                    </div>

                </div>
            </div>

            <!-- CARD 2: DETAILED OVERVIEW (WITH RICH TEXT EDITOR) -->
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                        <i class="fa-solid fa-paragraph text-primary"></i>
                        <span>Detailed Course Overview &amp; Description</span>
                    </h6>
                    <span class="badge bg-light text-secondary border">Rich Text Editor Enabled</span>
                </div>
                <div class="card-body p-4">
                    <label class="form-label fw-semibold text-secondary small mb-2">Write complete program description, objectives, and industry scope:</label>
                    <textarea name="full_desc" id="summernoteDesc" class="form-control"><?= htmlspecialchars($_POST['full_desc'] ?? '') ?></textarea>
                </div>
            </div>

            <!-- CARD 3: CURRICULUM & MODULES (WHAT WE PROVIDE) -->
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                        <i class="fa-solid fa-list-check text-primary"></i>
                        <span>Curriculum Modules (What We Provide)</span>
                    </h6>
                </div>
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <label class="form-label fw-semibold text-secondary small mb-0">Module Topics (One per line):</label>
                        <span class="text-muted small">Each line creates a numbered module card</span>
                    </div>
                    <textarea name="what_you_learn" class="form-control font-monospace small" rows="6" placeholder="Aviation Industry Overview & Aircraft Familiarization&#10;In-Flight Safety Standards & Emergency Evacuation Protocols&#10;Passenger Service Excellence & First Aid Medical Training&#10;Aviation English & International Communication&#10;In-Flight Food & Luxury Beverage Service Standards&#10;Executive Grooming, Skin Care, Makeup & Hair Masterclass"><?= htmlspecialchars($_POST['what_you_learn'] ?? '') ?></textarea>
                    <div class="form-text small mt-2">
                        <i class="fa-solid fa-circle-info text-primary me-1"></i> These will automatically appear as <strong>Module 01, Module 02...</strong> with accordion details on the course page.
                    </div>
                </div>
            </div>

            <!-- CARD 4: PRACTICAL METHODOLOGY & CAREER ROLES -->
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                        <i class="fa-solid fa-chalkboard-user text-primary"></i>
                        <span>Practical Training &amp; Career Opportunities</span>
                    </h6>
                </div>
                <div class="card-body p-4">
                    
                    <!-- HOW WE TEACH -->
                    <div class="mb-4">
                        <label class="form-label fw-bold text-dark small">How We Work &amp; Teach (Practical Training Points):</label>
                        <textarea name="how_we_teach" class="form-control font-monospace small" rows="4" placeholder="Hands-on Cabin Mock-Up & Emergency Drill Simulations&#10;Professional Uniform, Hair, Makeup & Grooming Workshops&#10;Customer Conflict Resolution & De-escalation Roleplay&#10;1-on-1 Airline Mock HR & GD Technical Rounds with Ex-Crew"><?= htmlspecialchars($_POST['how_we_teach'] ?? '') ?></textarea>
                        <div class="form-text small">Enter key training highlights (one per line). These render as numbered practical training steps.</div>
                    </div>

                    <!-- CAREER ROLES -->
                    <div class="mb-0">
                        <label class="form-label fw-bold text-dark small">Target Career Job Roles:</label>
                        <input type="text" name="career_roles" class="form-control" placeholder="Air Hostess, Cabin Crew, Flight Attendant, Ground Staff, VIP Lounge Host" value="<?= htmlspecialchars($_POST['career_roles'] ?? '') ?>">
                        <div class="form-text small">Separate multiple job titles with commas. These display as interactive job profile badges.</div>
                    </div>

                </div>
            </div>

        </div>

        <!-- ================= RIGHT COLUMN: SIDEBAR METAS (4 COLS) ================= -->
        <div class="col-lg-4">

            <!-- CARD 1: PUBLISH ACTIONS -->
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                        <i class="fa-solid fa-sliders text-primary"></i>
                        <span>Publish Settings</span>
                    </h6>
                </div>
                <div class="card-body p-3">
                    
                    <!-- STATUS -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-secondary small">Course Status</label>
                        <select name="status" class="form-select form-select-sm">
                            <option value="active" selected>Active &bull; Visible on Website</option>
                            <option value="inactive">Inactive &bull; Hidden Draft</option>
                        </select>
                    </div>

                    <!-- FEATURED TOGGLE -->
                    <div class="mb-3 form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_featured" value="1" id="featuredSwitch" checked>
                        <label class="form-check-label small fw-semibold text-dark" for="featuredSwitch">Feature on Homepage</label>
                        <div class="form-text small">Display this course in the homepage Popular Courses grid.</div>
                    </div>

                    <hr class="my-3 text-muted opacity-25">

                    <!-- SUBMIT BUTTONS -->
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary d-flex align-items-center justify-content-center gap-2 py-2">
                            <i class="fa-solid fa-check"></i>
                            <span class="fw-semibold">Save &amp; Publish Course</span>
                        </button>
                        <a href="courses.php" class="btn btn-light border btn-sm text-secondary">
                            Cancel
                        </a>
                    </div>

                </div>
            </div>

            <!-- CARD 2: CATEGORY & SPECS -->
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                        <i class="fa-solid fa-tags text-primary"></i>
                        <span>Category &amp; Specifications</span>
                    </h6>
                </div>
                <div class="card-body p-3">

                    <!-- CATEGORY -->
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="form-label fw-semibold text-secondary small mb-0">Category <span class="text-danger">*</span></label>
                            <a href="courses.php" class="small text-decoration-none" data-bs-toggle="modal" data-bs-target="#categoryModal" style="font-size: 11.5px;">+ Manage Categories</a>
                        </div>
                        <select name="category_id" class="form-select form-select-sm" required>
                            <option value="">Select Category</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= $cat['id'] ?>" <?= (isset($_POST['category_id']) && $_POST['category_id'] == $cat['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($cat['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- DURATION -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-secondary small">Course Duration</label>
                        <input type="text" name="duration" class="form-control form-control-sm" placeholder="e.g. 12 Months / 6 Months" value="<?= htmlspecialchars($_POST['duration'] ?? '12 Months') ?>">
                    </div>

                    <!-- STUDY MODE -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-secondary small">Study Mode</label>
                        <input type="text" name="study_mode" class="form-control form-control-sm" placeholder="e.g. Classroom & Practical Labs" value="<?= htmlspecialchars($_POST['study_mode'] ?? 'Classroom & Practical Labs') ?>">
                    </div>

                    <!-- ELIGIBILITY -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-secondary small">Eligibility Criteria</label>
                        <input type="text" name="eligibility" class="form-control form-control-sm" placeholder="e.g. 10+2 (12th Pass) in any stream" value="<?= htmlspecialchars($_POST['eligibility'] ?? '10+2 (12th Pass) in any stream') ?>">
                    </div>

                    <!-- CERTIFICATION -->
                    <div class="mb-0">
                        <label class="form-label fw-semibold text-secondary small">Certification Title</label>
                        <input type="text" name="certification" class="form-control form-control-sm" placeholder="e.g. Industry Recognized Diploma" value="<?= htmlspecialchars($_POST['certification'] ?? 'Industry Recognized Certification') ?>">
                    </div>

                </div>
            </div>

            <!-- CARD 3: THUMBNAIL IMAGE -->
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                        <i class="fa-solid fa-image text-primary"></i>
                        <span>Course Thumbnail Image</span>
                    </h6>
                </div>
                <div class="card-body p-3 text-center">
                    
                    <div class="preview-box mb-3" id="imagePreviewBox">
                        <div class="text-muted small" id="previewPlaceholder">
                            <i class="fa-regular fa-image fa-3x mb-2 d-block opacity-25"></i>
                            <span>No image selected</span>
                        </div>
                        <img src="" alt="Preview" id="previewImg" style="display: none;">
                    </div>

                    <div class="text-start">
                        <input type="file" name="course_image" id="courseImageInput" class="form-control form-control-sm" accept="image/*">
                        <div class="form-text small mt-1 text-muted" style="font-size: 11.5px;">
                            Recommended: 800x520px (JPG, PNG, WEBP). Max 2MB.
                        </div>
                    </div>

                </div>
            </div>

        </div>

    </div>
</form>

<!-- jQuery (required for Summernote Lite) -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<!-- Summernote Lite JS -->
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-lite.min.js"></script>

<script>
$(document).ready(function() {
    // Initialize Summernote WYSIWYG
    $('#summernoteDesc').summernote({
        placeholder: 'Write comprehensive course overview, objectives, practical exposure, and benefits...',
        tabsize: 2,
        height: 250,
        toolbar: [
            ['style', ['style']],
            ['font', ['bold', 'italic', 'underline', 'clear']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['table', ['table']],
            ['insert', ['link']],
            ['view', ['fullscreen', 'codeview']]
        ]
    });

    // Auto-generate slug from title
    const titleInput = document.getElementById('courseTitle');
    const slugInput = document.getElementById('courseSlug');
    let userModifiedSlug = false;

    slugInput.addEventListener('input', function() {
        userModifiedSlug = true;
    });

    titleInput.addEventListener('input', function() {
        if (!userModifiedSlug || slugInput.value.trim() === '') {
            slugInput.value = this.value
                .toLowerCase()
                .trim()
                .replace(/[^a-z0-9]+/g, '-')
                .replace(/^-+|-+$/g, '');
        }
    });

    // Image preview
    const imageInput = document.getElementById('courseImageInput');
    const previewImg = document.getElementById('previewImg');
    const previewPlaceholder = document.getElementById('previewPlaceholder');

    imageInput.addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                previewImg.style.display = 'block';
                previewPlaceholder.style.display = 'none';
            }
            reader.readAsDataURL(file);
        } else {
            previewImg.style.display = 'none';
            previewPlaceholder.style.display = 'block';
        }
    });
});
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
