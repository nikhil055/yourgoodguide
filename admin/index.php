<?php
$page_title = "Dashboard";
require_once __DIR__ . '/includes/header.php';

// Fetch Recent Admissions
$recent_admissions = $pdo->query("SELECT * FROM admissions ORDER BY id DESC LIMIT 5")->fetchAll();
// Fetch Recent Fees
$recent_fees = $pdo->query("SELECT * FROM fee_submissions ORDER BY id DESC LIMIT 5")->fetchAll();
// Fetch Recent Inquiries
$recent_inquiries = $pdo->query("SELECT * FROM contact_inquiries ORDER BY id DESC LIMIT 5")->fetchAll();
// Fetch Recent Candidates
$recent_students = $pdo->query("SELECT * FROM students ORDER BY id DESC LIMIT 5")->fetchAll();
?>

<!-- STATS CARDS -->
<div class="row g-4 mb-4">
    <!-- Candidates Card -->
    <div class="col-xl-4 col-md-6">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="text-muted fw-semibold small">REGISTERED CANDIDATES</span>
                <div class="stat-icon bg-primary-subtle text-primary">
                    <i class="fa-solid fa-users"></i>
                </div>
            </div>
            <h3 class="fw-bold mb-1 text-dark"><?= $admin_stats['total_students'] ?? 0 ?></h3>
            <div class="d-flex justify-content-between align-items-center">
                <span class="badge bg-success-subtle text-success"><?= $admin_stats['confirmed_students'] ?? 0 ?> Seats Confirmed</span>
                <a href="students.php" class="small fw-semibold text-decoration-none">View all <i class="fa-solid fa-arrow-right ms-1"></i></a>
            </div>
        </div>
    </div>

    <!-- Admissions Card -->
    <div class="col-xl-4 col-md-6">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="text-muted fw-semibold small">TOTAL ADMISSIONS</span>
                <div class="stat-icon bg-info-subtle text-info">
                    <i class="fa-solid fa-user-graduate"></i>
                </div>
            </div>
            <h3 class="fw-bold mb-1 text-dark"><?= $admin_stats['total_admissions'] ?></h3>
            <div class="d-flex justify-content-between align-items-center">
                <span class="badge bg-info-subtle text-info"><?= $admin_stats['unread_admissions'] ?> New Submissions</span>
                <a href="admissions.php" class="small fw-semibold text-decoration-none text-info">View all <i class="fa-solid fa-arrow-right ms-1"></i></a>
            </div>
        </div>
    </div>

    <!-- Fee Submissions Card -->
    <div class="col-xl-4 col-md-6">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="text-muted fw-semibold small">FEE SUBMISSIONS</span>
                <div class="stat-icon bg-success-subtle text-success">
                    <i class="fa-solid fa-receipt"></i>
                </div>
            </div>
            <h3 class="fw-bold mb-1 text-dark"><?= $admin_stats['total_fees'] ?></h3>
            <div class="d-flex justify-content-between align-items-center">
                <span class="badge bg-success-subtle text-success"><?= $admin_stats['unread_fees'] ?> New Submissions</span>
                <a href="fees.php" class="small fw-semibold text-decoration-none text-success">View all <i class="fa-solid fa-arrow-right ms-1"></i></a>
            </div>
        </div>
    </div>

    <!-- Inquiries Card -->
    <div class="col-xl-6 col-md-6">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="text-muted fw-semibold small">CONTACT INQUIRIES</span>
                <div class="stat-icon bg-warning-subtle text-warning">
                    <i class="fa-solid fa-envelope-open-text"></i>
                </div>
            </div>
            <h3 class="fw-bold mb-1 text-dark"><?= $admin_stats['total_inquiries'] ?></h3>
            <div class="d-flex justify-content-between align-items-center">
                <span class="badge bg-warning-subtle text-warning"><?= $admin_stats['unread_inquiries'] ?> Unread</span>
                <a href="contacts.php" class="small fw-semibold text-decoration-none text-warning">View all <i class="fa-solid fa-arrow-right ms-1"></i></a>
            </div>
        </div>
    </div>

    <!-- Courses Card -->
    <div class="col-xl-6 col-md-6">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="text-muted fw-semibold small">ACTIVE COURSES</span>
                <div class="stat-icon bg-secondary-subtle text-secondary">
                    <i class="fa-solid fa-book-open"></i>
                </div>
            </div>
            <h3 class="fw-bold mb-1 text-dark"><?= $admin_stats['total_courses'] ?></h3>
            <div class="d-flex justify-content-between align-items-center">
                <span class="badge bg-secondary-subtle text-secondary">Programs Available</span>
                <a href="courses.php" class="small fw-semibold text-decoration-none text-secondary">Manage <i class="fa-solid fa-arrow-right ms-1"></i></a>
            </div>
        </div>
    </div>
</div>

<!-- RECENT ACTIVITY TABLES -->
<div class="row g-4 mb-4">
    <!-- Recent Registered Candidates -->
    <div class="col-12">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom">
                <h6 class="mb-0 fw-bold text-dark"><i class="fa-solid fa-users text-primary me-2"></i> Recent Registered Candidates</h6>
                <a href="students.php" class="btn btn-sm btn-light">View All Candidates</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Candidate</th>
                                <th>Student ID</th>
                                <th>Phone</th>
                                <th>Aadhaar</th>
                                <th>Seat Status</th>
                                <th>Payment</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($recent_students)): ?>
                                <tr><td colspan="7" class="text-center py-4 text-muted">No candidate registrations yet.</td></tr>
                            <?php else: ?>
                                <?php foreach ($recent_students as $st): ?>
                                    <tr>
                                        <td>
                                            <div class="fw-semibold text-dark"><?= htmlspecialchars($st['name']) ?></div>
                                            <small class="text-muted"><?= htmlspecialchars($st['email']) ?></small>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark border font-monospace"><?= htmlspecialchars($st['student_id']) ?></span>
                                        </td>
                                        <td><?= htmlspecialchars($st['phone']) ?></td>
                                        <td class="font-monospace small"><?= htmlspecialchars($st['aadhaar']) ?></td>
                                        <td>
                                            <span class="badge <?= $st['seat_status'] === 'Confirmed' ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-warning-subtle text-warning border border-warning-subtle' ?>">
                                                <?= htmlspecialchars($st['seat_status']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge <?= $st['payment_status'] === 'Paid' ? 'bg-success text-white' : 'bg-secondary-subtle text-secondary' ?>">
                                                <?= htmlspecialchars($st['payment_status']) ?>
                                            </span>
                                        </td>
                                        <td class="text-muted small"><?= date('d M, Y', strtotime($st['created_at'])) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Recent Admissions -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm rounded-3 h-100">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom">
                <h6 class="mb-0 fw-bold text-dark"><i class="fa-solid fa-user-graduate text-info me-2"></i> Recent Admissions</h6>
                <a href="admissions.php" class="btn btn-sm btn-light">View All</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Name</th>
                                <th>Course</th>
                                <th>Mobile</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($recent_admissions)): ?>
                                <tr><td colspan="4" class="text-center py-4 text-muted">No admission submissions yet.</td></tr>
                            <?php else: ?>
                                <?php foreach ($recent_admissions as $adm): ?>
                                    <tr>
                                        <td>
                                            <div class="fw-semibold text-dark"><?= htmlspecialchars($adm['name']) ?></div>
                                            <small class="text-muted"><?= htmlspecialchars($adm['email']) ?></small>
                                        </td>
                                        <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($adm['course'] ?? 'N/A') ?></span></td>
                                        <td><?= htmlspecialchars($adm['mobile'] ?? 'N/A') ?></td>
                                        <td class="text-muted small"><?= date('d M, Y', strtotime($adm['created_at'])) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Contact Inquiries -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm rounded-3 h-100">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom">
                <h6 class="mb-0 fw-bold text-dark"><i class="fa-solid fa-envelope text-warning me-2"></i> Recent Inquiries</h6>
                <a href="contacts.php" class="btn btn-sm btn-light">View All</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Name</th>
                                <th>Subject</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($recent_inquiries)): ?>
                                <tr><td colspan="4" class="text-center py-4 text-muted">No contact inquiries yet.</td></tr>
                            <?php else: ?>
                                <?php foreach ($recent_inquiries as $inq): ?>
                                    <tr>
                                        <td>
                                            <div class="fw-semibold text-dark"><?= htmlspecialchars($inq['name']) ?></div>
                                            <small class="text-muted"><?= htmlspecialchars($inq['email']) ?></small>
                                        </td>
                                        <td class="text-truncate" style="max-width: 150px;"><?= htmlspecialchars($inq['subject']) ?></td>
                                        <td>
                                            <span class="badge <?= $inq['status'] === 'New' ? 'bg-danger-subtle text-danger' : 'bg-success-subtle text-success' ?>">
                                                <?= htmlspecialchars($inq['status']) ?>
                                            </span>
                                        </td>
                                        <td class="text-muted small"><?= date('d M, Y', strtotime($inq['created_at'])) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
