<?php
require_once __DIR__ . '/includes/auth.php';
checkAdminAuth();

$page_title = "Manage Courses";
$success_msg = '';
$error_msg = '';

// Check query param messages
if (isset($_GET['msg'])) {
    if ($_GET['msg'] === 'added') $success_msg = 'Course created successfully!';
    if ($_GET['msg'] === 'updated') $success_msg = 'Course updated successfully!';
    if ($_GET['msg'] === 'deleted') $success_msg = 'Course deleted successfully!';
}

// -------------------------------------------------------------
// POST ACTIONS (QUICK ACTIONS)
// -------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // DELETE COURSE
    if ($action === 'delete_course') {
        $delete_id = (int)($_POST['course_id'] ?? 0);
        if ($delete_id > 0) {
            $img_stmt = $pdo->prepare("SELECT image FROM courses WHERE id = ?");
            $img_stmt->execute([$delete_id]);
            $img = $img_stmt->fetchColumn();
            if ($img && strpos($img, 'uploads/courses/') === 0 && file_exists(__DIR__ . '/../' . $img)) {
                @unlink(__DIR__ . '/../' . $img);
            }

            $stmt = $pdo->prepare("DELETE FROM courses WHERE id = ?");
            $stmt->execute([$delete_id]);
            header("Location: courses.php?msg=deleted");
            exit;
        }
    }

    // TOGGLE STATUS
    if ($action === 'toggle_status') {
        $course_id = (int)($_POST['course_id'] ?? 0);
        if ($course_id > 0) {
            $stmt = $pdo->prepare("UPDATE courses SET status = IF(status = 'active', 'inactive', 'active') WHERE id = ?");
            $stmt->execute([$course_id]);
            $success_msg = 'Course status updated!';
        }
    }

    // TOGGLE FEATURED
    if ($action === 'toggle_featured') {
        $course_id = (int)($_POST['course_id'] ?? 0);
        if ($course_id > 0) {
            $stmt = $pdo->prepare("UPDATE courses SET is_featured = IF(is_featured = 1, 0, 1) WHERE id = ?");
            $stmt->execute([$course_id]);
            $success_msg = 'Featured status updated!';
        }
    }

    // ADD CATEGORY
    if ($action === 'add_category') {
        $cat_name = trim($_POST['cat_name'] ?? '');
        $cat_icon = trim($_POST['cat_icon'] ?? 'fa-graduation-cap');
        if (!empty($cat_name)) {
            $cat_slug = preg_replace('/[^a-z0-9]+/i', '-', trim(strtolower($cat_name)));
            $cat_slug = trim($cat_slug, '-');
            
            $chk = $pdo->prepare("SELECT id FROM course_categories WHERE slug = ?");
            $chk->execute([$cat_slug]);
            if ($chk->fetch()) {
                $cat_slug .= '-' . rand(10, 99);
            }

            $stmt = $pdo->prepare("INSERT INTO course_categories (name, slug, icon, status) VALUES (?, ?, ?, 'active')");
            $stmt->execute([$cat_name, $cat_slug, $cat_icon]);
            $success_msg = "Category '{$cat_name}' added!";
        } else {
            $error_msg = 'Please enter a category name.';
        }
    }

    // DELETE CATEGORY
    if ($action === 'delete_category') {
        $cat_id = (int)($_POST['cat_id'] ?? 0);
        if ($cat_id > 0) {
            $chk = $pdo->prepare("SELECT COUNT(*) FROM courses WHERE category_id = ?");
            $chk->execute([$cat_id]);
            if ($chk->fetchColumn() > 0) {
                $error_msg = 'Cannot delete category: courses exist under it.';
            } else {
                $stmt = $pdo->prepare("DELETE FROM course_categories WHERE id = ?");
                $stmt->execute([$cat_id]);
                $success_msg = 'Category removed!';
            }
        }
    }
}

// -------------------------------------------------------------
// FETCH DATA
// -------------------------------------------------------------
$total_count = (int)$pdo->query("SELECT COUNT(*) FROM courses")->fetchColumn();
$active_count = (int)$pdo->query("SELECT COUNT(*) FROM courses WHERE status = 'active'")->fetchColumn();

// Fetch Categories
$categories = $pdo->query("SELECT c.*, (SELECT COUNT(*) FROM courses WHERE category_id = c.id) as course_count 
                           FROM course_categories c ORDER BY c.name ASC")->fetchAll();

// Filters & Search
$selected_category = isset($_GET['category']) ? (int)$_GET['category'] : 0;
$selected_status = trim($_GET['status'] ?? '');
$search = trim($_GET['search'] ?? '');

$query = "SELECT cr.*, cat.name as category_name, cat.icon as category_icon 
          FROM courses cr 
          JOIN course_categories cat ON cr.category_id = cat.id 
          WHERE 1=1";
$params = [];

if ($selected_category > 0) {
    $query .= " AND cr.category_id = ?";
    $params[] = $selected_category;
}

if (!empty($selected_status)) {
    $query .= " AND cr.status = ?";
    $params[] = $selected_status;
}

if (!empty($search)) {
    $query .= " AND (cr.title LIKE ? OR cr.short_desc LIKE ?)";
    $term = "%{$search}%";
    $params = array_merge($params, [$term, $term]);
}

$query .= " ORDER BY cr.id DESC";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$courses = $stmt->fetchAll();

include __DIR__ . '/includes/header.php';
?>

<!-- Minimalist Custom CSS for Course Management -->
<style>
/* Clean Minimal Page Styling */
.course-page-wrap {
    max-width: 1200px;
    margin: 0 auto;
}
.page-title-text {
    font-size: 1.25rem;
    font-weight: 700;
    color: #0f172a;
    letter-spacing: -0.2px;
}
.count-pill {
    font-size: 11.5px;
    font-weight: 600;
    background: #f1f5f9;
    color: #475569;
    padding: 2px 8px;
    border-radius: 9999px;
    border: 1px solid #e2e8f0;
}

/* Action Buttons */
.btn-compact {
    font-size: 13px;
    font-weight: 600;
    padding: 6px 14px;
    border-radius: 6px;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: all 0.15s ease;
}
.btn-compact-primary {
    background: #4361ee;
    color: #ffffff;
    border: 1px solid #4361ee;
}
.btn-compact-primary:hover {
    background: #3651d4;
    color: #ffffff;
}
.btn-compact-outline {
    background: #ffffff;
    color: #475569;
    border: 1px solid #cbd5e1;
}
.btn-compact-outline:hover {
    background: #f8fafc;
    color: #0f172a;
    border-color: #94a3b8;
}

/* Minimal Filter Bar */
.filter-bar {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 8px 12px;
    margin-bottom: 16px;
}
.filter-select {
    font-size: 12.5px;
    border: 1px solid #cbd5e1;
    border-radius: 6px;
    padding: 5px 10px;
    color: #334155;
    background-color: #ffffff;
    outline: none;
}
.filter-select:focus {
    border-color: #4361ee;
}
.search-input-box {
    position: relative;
    min-width: 240px;
}
.search-input-box input {
    width: 100%;
    font-size: 12.5px;
    border: 1px solid #cbd5e1;
    border-radius: 6px;
    padding: 5px 10px 5px 30px;
    outline: none;
}
.search-input-box input:focus {
    border-color: #4361ee;
}
.search-icon {
    position: absolute;
    left: 10px;
    top: 50%;
    transform: translateY(-50%);
    color: #94a3b8;
    font-size: 11px;
}

/* Sleek Minimal Table */
.minimal-table-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    overflow: hidden;
}
.table-minimal {
    margin-bottom: 0;
    font-size: 13px;
}
.table-minimal thead th {
    background: #f8fafc;
    color: #64748b;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    padding: 10px 14px;
    border-bottom: 1px solid #e2e8f0;
}
.table-minimal tbody td {
    padding: 11px 14px;
    vertical-align: middle;
    border-bottom: 1px solid #f1f5f9;
    color: #334155;
}
.table-minimal tbody tr:last-child td {
    border-bottom: none;
}
.table-minimal tbody tr:hover td {
    background-color: #fbfcfd;
}

/* Course Thumbnail */
.course-thumb-mini {
    width: 44px;
    height: 32px;
    border-radius: 5px;
    object-fit: cover;
    border: 1px solid #e2e8f0;
    background: #f1f5f9;
    flex-shrink: 0;
}
.course-title-text {
    font-size: 13.5px;
    font-weight: 600;
    color: #0f172a;
    text-decoration: none;
}
.course-title-text:hover {
    color: #4361ee;
}
.course-slug-sub {
    font-size: 11.5px;
    color: #94a3b8;
}

/* Category Pill */
.cat-tag {
    display: inline-block;
    font-size: 11.5px;
    font-weight: 500;
    color: #475569;
    background: #f1f5f9;
    border: 1px solid #e2e8f0;
    padding: 2px 7px;
    border-radius: 4px;
}

/* Status Dot Badges */
.status-pill {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    font-size: 12px;
    font-weight: 500;
    padding: 2px 7px;
    border-radius: 4px;
    border: none;
    background: transparent;
    cursor: pointer;
}
.status-dot {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    display: inline-block;
}
.status-active {
    color: #059669;
}
.status-active .status-dot {
    background-color: #10b981;
}
.status-inactive {
    color: #94a3b8;
}
.status-inactive .status-dot {
    background-color: #cbd5e1;
}

/* Featured Star Button */
.star-btn {
    border: none;
    background: transparent;
    cursor: pointer;
    font-size: 13px;
    padding: 2px 4px;
}

/* Action Icon Buttons */
.act-btn {
    width: 28px;
    height: 28px;
    border-radius: 5px;
    border: 1px solid #e2e8f0;
    background: #ffffff;
    color: #64748b;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    text-decoration: none;
    transition: all 0.15s ease;
}
.act-btn:hover {
    color: #4361ee;
    border-color: #cbd5e1;
    background: #f8fafc;
}
.act-btn-del:hover {
    color: #dc2626;
    border-color: #fecaca;
    background: #fef2f2;
}
</style>

<div class="course-page-wrap">

    <!-- ================= HEADER ================= -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <div class="d-flex align-items-center gap-2">
                <span class="page-title-text">Courses</span>
                <span class="count-pill"><?= $total_count ?> total &bull; <?= $active_count ?> active</span>
            </div>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn-compact btn-compact-outline" data-bs-toggle="modal" data-bs-target="#categoryModal">
                <i class="fa-solid fa-layer-group"></i>
                <span>Categories</span>
            </button>
            <a href="course-add.php" class="btn-compact btn-compact-primary">
                <i class="fa-solid fa-plus"></i>
                <span>Add Course</span>
            </a>
        </div>
    </div>

    <!-- ================= ALERTS ================= -->
    <?php if (!empty($success_msg)): ?>
        <div class="alert alert-success alert-dismissible fade show py-2 px-3 small d-flex align-items-center mb-3" role="alert">
            <i class="fa-solid fa-circle-check me-2"></i>
            <div><?= htmlspecialchars($success_msg) ?></div>
            <button type="button" class="btn-close py-2" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (!empty($error_msg)): ?>
        <div class="alert alert-danger alert-dismissible fade show py-2 px-3 small d-flex align-items-center mb-3" role="alert">
            <i class="fa-solid fa-circle-exclamation me-2"></i>
            <div><?= htmlspecialchars($error_msg) ?></div>
            <button type="button" class="btn-close py-2" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- ================= FILTER & SEARCH BAR ================= -->
    <div class="filter-bar">
        <form method="GET" action="courses.php" class="d-flex flex-wrap justify-content-between align-items-center gap-2">
            
            <div class="d-flex align-items-center gap-2">
                <!-- Category Select -->
                <select name="category" class="filter-select" onchange="this.form.submit()">
                    <option value="">All Categories (<?= count($categories) ?>)</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>" <?= $selected_category === (int)$cat['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat['name']) ?> (<?= $cat['course_count'] ?>)
                        </option>
                    <?php endforeach; ?>
                </select>

                <!-- Status Select -->
                <select name="status" class="filter-select" onchange="this.form.submit()">
                    <option value="">All Status</option>
                    <option value="active" <?= $selected_status === 'active' ? 'selected' : '' ?>>Active only</option>
                    <option value="inactive" <?= $selected_status === 'inactive' ? 'selected' : '' ?>>Inactive only</option>
                </select>

                <?php if ($selected_category > 0 || !empty($selected_status) || !empty($search)): ?>
                    <a href="courses.php" class="btn-compact btn-compact-outline py-1 px-2" style="font-size: 11px;" title="Reset filters">
                        <i class="fa-solid fa-xmark"></i> Clear
                    </a>
                <?php endif; ?>
            </div>

            <!-- Search Input -->
            <div class="search-input-box">
                <i class="fa-solid fa-search search-icon"></i>
                <input type="text" name="search" placeholder="Search course title..." value="<?= htmlspecialchars($search) ?>">
            </div>

        </form>
    </div>

    <!-- ================= MINIMAL TABLE ================= -->
    <div class="minimal-table-card">
        <div class="table-responsive">
            <table class="table table-minimal align-middle">
                <thead>
                    <tr>
                        <th style="min-width: 300px;">Course</th>
                        <th>Category</th>
                        <th>Duration</th>
                        <th>Mode</th>
                        <th class="text-center" style="width: 70px;">Featured</th>
                        <th style="width: 90px;">Status</th>
                        <th class="text-end" style="width: 110px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($courses)): ?>
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <div class="py-3">
                                    <i class="fa-regular fa-folder-open fa-2x mb-2 text-secondary opacity-50 d-block"></i>
                                    <span class="d-block small fw-semibold text-dark">No courses found</span>
                                    <span class="small text-muted">Try clearing your filters or create a new course.</span>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($courses as $c): ?>
                            <tr>
                                <!-- Course Title & Thumbnail -->
                                <td>
                                    <div class="d-flex align-items-center gap-2.5">
                                        <?php if (!empty($c['image'])): ?>
                                            <img src="../<?= htmlspecialchars($c['image']) ?>" alt="" class="course-thumb-mini">
                                        <?php else: ?>
                                            <div class="course-thumb-mini d-flex align-items-center justify-content-center text-muted">
                                                <i class="fa-solid <?= htmlspecialchars($c['category_icon']) ?> small"></i>
                                            </div>
                                        <?php endif; ?>
                                        <div>
                                            <a href="course-edit.php?id=<?= $c['id'] ?>" class="course-title-text d-block">
                                                <?= htmlspecialchars($c['title']) ?>
                                            </a>
                                            <span class="course-slug-sub">/course-detail.php?slug=<?= htmlspecialchars($c['slug']) ?></span>
                                        </div>
                                    </div>
                                </td>

                                <!-- Category -->
                                <td>
                                    <span class="cat-tag">
                                        <i class="fa-solid <?= htmlspecialchars($c['category_icon']) ?> me-1 text-secondary" style="font-size: 10px;"></i>
                                        <?= htmlspecialchars($c['category_name']) ?>
                                    </span>
                                </td>

                                <!-- Duration -->
                                <td>
                                    <span class="text-dark small"><?= htmlspecialchars($c['duration']) ?></span>
                                </td>

                                <!-- Study Mode -->
                                <td>
                                    <span class="text-muted small"><?= htmlspecialchars($c['study_mode']) ?></span>
                                </td>

                                <!-- Featured Star -->
                                <td class="text-center">
                                    <form method="POST" action="courses.php" class="d-inline">
                                        <input type="hidden" name="action" value="toggle_featured">
                                        <input type="hidden" name="course_id" value="<?= $c['id'] ?>">
                                        <button type="submit" class="star-btn" title="Toggle Home Feature">
                                            <?php if ($c['is_featured']): ?>
                                                <i class="fa-solid fa-star text-warning"></i>
                                            <?php else: ?>
                                                <i class="fa-regular fa-star text-muted opacity-40"></i>
                                            <?php endif; ?>
                                        </button>
                                    </form>
                                </td>

                                <!-- Status -->
                                <td>
                                    <form method="POST" action="courses.php" class="d-inline">
                                        <input type="hidden" name="action" value="toggle_status">
                                        <input type="hidden" name="course_id" value="<?= $c['id'] ?>">
                                        <?php if ($c['status'] === 'active'): ?>
                                            <button type="submit" class="status-pill status-active" title="Click to disable">
                                                <span class="status-dot"></span> Active
                                            </button>
                                        <?php else: ?>
                                            <button type="submit" class="status-pill status-inactive" title="Click to enable">
                                                <span class="status-dot"></span> Inactive
                                            </button>
                                        <?php endif; ?>
                                    </form>
                                </td>

                                <!-- Actions -->
                                <td class="text-end">
                                    <div class="d-inline-flex gap-1">
                                        <!-- Edit -->
                                        <a href="course-edit.php?id=<?= $c['id'] ?>" class="act-btn" title="Edit Course">
                                            <i class="fa-solid fa-pen"></i>
                                        </a>

                                        <!-- Live Preview -->
                                        <a href="../course-detail.php?slug=<?= htmlspecialchars($c['slug']) ?>" target="_blank" class="act-btn" title="Live Preview">
                                            <i class="fa-solid fa-arrow-up-right-from-square"></i>
                                        </a>

                                        <!-- Delete -->
                                        <form method="POST" action="courses.php" onsubmit="return confirm('Delete \'<?= htmlspecialchars(addslashes($c['title'])) ?>\'?');" class="d-inline">
                                            <input type="hidden" name="action" value="delete_course">
                                            <input type="hidden" name="course_id" value="<?= $c['id'] ?>">
                                            <button type="submit" class="act-btn act-btn-del" title="Delete Course">
                                                <i class="fa-regular fa-trash-can"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- ================= CATEGORY MANAGEMENT MODAL ================= -->
<div class="modal fade" id="categoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-sm rounded-3">
            <div class="modal-header border-bottom py-2.5 px-3">
                <h6 class="modal-title fw-bold text-dark mb-0">Course Categories</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-3">
                
                <!-- Quick Add -->
                <form method="POST" action="courses.php" class="mb-3">
                    <input type="hidden" name="action" value="add_category">
                    <div class="d-flex gap-2">
                        <input type="text" name="cat_name" class="form-control form-control-sm" placeholder="New category name..." required>
                        <input type="text" name="cat_icon" class="form-control form-control-sm" style="max-width: 130px;" value="fa-graduation-cap" placeholder="Icon class">
                        <button type="submit" class="btn btn-sm btn-primary flex-shrink-0">Add</button>
                    </div>
                </form>

                <!-- List -->
                <div class="border rounded-2 overflow-hidden">
                    <table class="table table-sm table-hover mb-0" style="font-size: 12.5px;">
                        <thead class="table-light">
                            <tr>
                                <th>Name</th>
                                <th>Courses</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($categories as $cat): ?>
                                <tr>
                                    <td>
                                        <i class="fa-solid <?= htmlspecialchars($cat['icon']) ?> text-muted me-1.5 small"></i>
                                        <strong class="text-dark"><?= htmlspecialchars($cat['name']) ?></strong>
                                    </td>
                                    <td><span class="text-muted"><?= $cat['course_count'] ?></span></td>
                                    <td class="text-end">
                                        <?php if ($cat['course_count'] == 0): ?>
                                            <form method="POST" action="courses.php" onsubmit="return confirm('Delete this category?');" class="d-inline">
                                                <input type="hidden" name="action" value="delete_category">
                                                <input type="hidden" name="cat_id" value="<?= $cat['id'] ?>">
                                                <button type="submit" class="border-0 bg-transparent text-danger p-0 small" title="Delete">
                                                    <i class="fa-solid fa-trash-can"></i>
                                                </button>
                                            </form>
                                        <?php else: ?>
                                            <span class="text-muted small" title="Contains courses"><i class="fa-solid fa-lock" style="font-size: 10px;"></i></span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

            </div>
            <div class="modal-footer border-top py-2 px-3">
                <button type="button" class="btn btn-sm btn-light border" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
