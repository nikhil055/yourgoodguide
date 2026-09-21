<?php
$page_title = "Admissions Management";
require_once __DIR__ . '/includes/header.php';

// Handle Actions (Delete / Status Change)
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    if ($_GET['action'] === 'delete') {
        $stmt = $pdo->prepare("DELETE FROM admissions WHERE id = ?");
        $stmt->execute([$id]);
        header("Location: admissions.php?msg=deleted");
        exit;
    }
    if ($_GET['action'] === 'mark_read') {
        $stmt = $pdo->prepare("UPDATE admissions SET is_read = 1 WHERE id = ?");
        $stmt->execute([$id]);
        header("Location: admissions.php");
        exit;
    }
    if (in_array($_GET['action'], ['Pending', 'Approved', 'Rejected'])) {
        $stmt = $pdo->prepare("UPDATE admissions SET status = ? WHERE id = ?");
        $stmt->execute([$_GET['action'], $id]);
        header("Location: admissions.php?msg=status_updated");
        exit;
    }
}

// Filters & Search
$search = trim($_GET['search'] ?? '');
$filter_status = trim($_GET['status'] ?? '');
$filter_course = trim($_GET['course'] ?? '');

$query = "SELECT * FROM admissions WHERE 1=1";
$params = [];

if (!empty($search)) {
    $query .= " AND (name LIKE ? OR email LIKE ? OR mobile LIKE ? OR aadhaar LIKE ?)";
    $term = "%$search%";
    $params = array_merge($params, [$term, $term, $term, $term]);
}
if (!empty($filter_status)) {
    $query .= " AND status = ?";
    $params[] = $filter_status;
}
if (!empty($filter_course)) {
    $query .= " AND course LIKE ?";
    $params[] = "%$filter_course%";
}

$query .= " ORDER BY id DESC";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$admissions = $stmt->fetchAll();

// Mark all as read when page is visited
$pdo->query("UPDATE admissions SET is_read = 1 WHERE is_read = 0");
?>

<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h4 class="fw-bold text-dark mb-1">Admission Registrations</h4>
        <p class="text-muted small mb-0">Total <?= count($admissions) ?> student admission applications found</p>
    </div>
</div>

<!-- FILTER & SEARCH CARD -->
<div class="card border-0 shadow-sm rounded-3 mb-4">
    <div class="card-body p-3">
        <form method="GET" action="admissions.php" class="row g-2 align-items-center">
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control border-start-0" placeholder="Search by name, email, phone..." value="<?= htmlspecialchars($search) ?>">
                </div>
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">All Statuses</option>
                    <option value="Pending" <?= $filter_status === 'Pending' ? 'selected' : '' ?>>Pending</option>
                    <option value="Approved" <?= $filter_status === 'Approved' ? 'selected' : '' ?>>Approved</option>
                    <option value="Rejected" <?= $filter_status === 'Rejected' ? 'selected' : '' ?>>Rejected</option>
                </select>
            </div>
            <div class="col-md-3">
                <input type="text" name="course" class="form-control" placeholder="Filter by course..." value="<?= htmlspecialchars($filter_course) ?>">
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary w-100"><i class="fa-solid fa-filter me-1"></i> Filter</button>
                <a href="admissions.php" class="btn btn-light"><i class="fa-solid fa-rotate-left"></i></a>
            </div>
        </form>
    </div>
</div>

<!-- ADMISSIONS TABLE -->
<div class="card border-0 shadow-sm rounded-3">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Student Info</th>
                        <th>Course & Education</th>
                        <th>Contact & Location</th>
                        <th>Documents</th>
                        <th>Status</th>
                        <th>Applied On</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($admissions)): ?>
                        <tr><td colspan="8" class="text-center py-5 text-muted">No admission applications found.</td></tr>
                    <?php else: ?>
                        <?php foreach ($admissions as $idx => $row): ?>
                            <tr>
                                <td><?= $idx + 1 ?></td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <?php if (!empty($row['photo']) && file_exists(__DIR__ . '/../uploads/admissions/' . $row['photo'])): ?>
                                            <img src="../uploads/admissions/<?= htmlspecialchars($row['photo']) ?>" class="rounded-circle object-fit-cover" width="40" height="40" alt="Avatar">
                                        <?php else: ?>
                                            <div class="bg-secondary-subtle text-secondary rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 40px; height: 40px;">
                                                <?= strtoupper(substr($row['name'], 0, 1)) ?>
                                            </div>
                                        <?php endif; ?>
                                        <div>
                                            <div class="fw-bold text-dark"><?= htmlspecialchars($row['name']) ?></div>
                                            <small class="text-muted">Father: <?= htmlspecialchars($row['father_name'] ?? 'N/A') ?></small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle"><?= htmlspecialchars($row['course'] ?? 'N/A') ?></span>
                                    <div class="small text-muted mt-1"><?= htmlspecialchars($row['education'] ?? 'N/A') ?></div>
                                </td>
                                <td>
                                    <div><i class="fa-solid fa-phone text-muted me-1 small"></i> <?= htmlspecialchars($row['mobile'] ?? 'N/A') ?></div>
                                    <div class="small text-muted"><i class="fa-solid fa-envelope text-muted me-1 small"></i> <?= htmlspecialchars($row['email'] ?? 'N/A') ?></div>
                                    <div class="small text-muted"><i class="fa-solid fa-location-dot text-muted me-1 small"></i> <?= htmlspecialchars($row['city'] ?? '') ?>, <?= htmlspecialchars($row['state'] ?? '') ?></div>
                                </td>
                                <td>
                                    <div class="d-flex gap-1 flex-wrap">
                                        <?php if (!empty($row['marksheet10'])): ?>
                                            <a href="../uploads/admissions/<?= htmlspecialchars($row['marksheet10']) ?>" target="_blank" class="btn btn-xs btn-outline-secondary py-0 px-2 small" title="10th Marksheet"><i class="fa-solid fa-file-pdf"></i> 10th</a>
                                        <?php endif; ?>
                                        <?php if (!empty($row['marksheet12'])): ?>
                                            <a href="../uploads/admissions/<?= htmlspecialchars($row['marksheet12']) ?>" target="_blank" class="btn btn-xs btn-outline-secondary py-0 px-2 small" title="12th Marksheet"><i class="fa-solid fa-file-pdf"></i> 12th</a>
                                        <?php endif; ?>
                                        <?php if (!empty($row['aadhaar_card'])): ?>
                                            <a href="../uploads/admissions/<?= htmlspecialchars($row['aadhaar_card']) ?>" target="_blank" class="btn btn-xs btn-outline-secondary py-0 px-2 small" title="Aadhaar Card"><i class="fa-solid fa-id-card"></i> Aadhar</a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-sm dropdown-toggle rounded-pill px-2 py-1 <?= $row['status'] === 'Approved' ? 'btn-success' : ($row['status'] === 'Rejected' ? 'btn-danger' : 'btn-warning') ?>" type="button" data-bs-toggle="dropdown">
                                            <?= htmlspecialchars($row['status']) ?>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" href="admissions.php?action=Pending&id=<?= $row['id'] ?>">Set Pending</a></li>
                                            <li><a class="dropdown-item" href="admissions.php?action=Approved&id=<?= $row['id'] ?>">Set Approved</a></li>
                                            <li><a class="dropdown-item" href="admissions.php?action=Rejected&id=<?= $row['id'] ?>">Set Rejected</a></li>
                                        </ul>
                                    </div>
                                </td>
                                <td class="text-muted small">
                                    <?= date('d M Y', strtotime($row['created_at'])) ?><br>
                                    <span class="text-secondary"><?= date('h:i A', strtotime($row['created_at'])) ?></span>
                                </td>
                                <td class="text-end">
                                    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#viewModal<?= $row['id'] ?>" title="View Full Details">
                                        <i class="fa-solid fa-eye"></i>
                                    </button>
                                    <a href="admissions.php?action=delete&id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this admission record?')" title="Delete">
                                        <i class="fa-solid fa-trash"></i>
                                    </a>
                                </td>
                            </tr>

                            <!-- FULL DETAILS MODAL -->
                            <div class="modal fade" id="viewModal<?= $row['id'] ?>" tabindex="-1" aria-labelledby="modalLabel<?= $row['id'] ?>" aria-hidden="true">
                                <div class="modal-dialog modal-lg modal-dialog-centered">
                                    <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                                        <div class="modal-header bg-primary text-white py-3 px-4">
                                            <div class="d-flex align-items-center gap-2">
                                                <i class="fa-solid fa-id-badge fs-4"></i>
                                                <div>
                                                    <h5 class="modal-title fw-bold mb-0" id="modalLabel<?= $row['id'] ?>">Admission Application #<?= $row['id'] ?></h5>
                                                    <small class="text-white-50">Submitted on <?= date('d M Y, h:i A', strtotime($row['created_at'])) ?></small>
                                                </div>
                                            </div>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body p-4 bg-light">
                                            <div class="row g-4">
                                                <!-- Left Profile Box -->
                                                <div class="col-md-4">
                                                    <div class="card border-0 shadow-sm rounded-3 p-3 text-center bg-white h-100">
                                                        <?php if (!empty($row['photo']) && file_exists(__DIR__ . '/../uploads/admissions/' . $row['photo'])): ?>
                                                            <div class="mb-3">
                                                                <img src="../uploads/admissions/<?= htmlspecialchars($row['photo']) ?>" class="rounded-3 img-fluid border shadow-sm" style="max-height: 180px; width: 100%; object-fit: cover;" alt="Photo">
                                                            </div>
                                                        <?php else: ?>
                                                            <div class="bg-primary-subtle text-primary rounded-3 d-flex align-items-center justify-content-center fw-bold mx-auto mb-3" style="width: 100px; height: 100px; font-size: 2.5rem;">
                                                                <?= strtoupper(substr($row['name'], 0, 1)) ?>
                                                            </div>
                                                        <?php endif; ?>
                                                        <h5 class="fw-bold text-dark mb-1"><?= htmlspecialchars($row['name']) ?></h5>
                                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle mb-2 px-3 py-2 text-wrap"><?= htmlspecialchars($row['course'] ?? 'N/A') ?></span>
                                                        <div>
                                                            <span class="badge <?= $row['status'] === 'Approved' ? 'bg-success' : ($row['status'] === 'Rejected' ? 'bg-danger' : 'bg-warning text-dark') ?> px-3 py-2 rounded-pill">
                                                                Status: <?= htmlspecialchars($row['status']) ?>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Right Details Box -->
                                                <div class="col-md-8">
                                                    <div class="card border-0 shadow-sm rounded-3 p-3 bg-white mb-3">
                                                        <h6 class="fw-bold text-primary mb-3 border-bottom pb-2"><i class="fa-solid fa-user me-2"></i> Personal & Educational Information</h6>
                                                        <div class="row g-2">
                                                            <div class="col-sm-6">
                                                                <small class="text-muted d-block">Father's Name</small>
                                                                <span class="fw-semibold text-dark"><?= htmlspecialchars($row['father_name'] ?? 'N/A') ?></span>
                                                            </div>
                                                            <div class="col-sm-6">
                                                                <small class="text-muted d-block">Date of Birth</small>
                                                                <span class="fw-semibold text-dark"><?= htmlspecialchars($row['dob'] ?? 'N/A') ?></span>
                                                            </div>
                                                            <div class="col-sm-6">
                                                                <small class="text-muted d-block">Gender</small>
                                                                <span class="fw-semibold text-dark"><?= htmlspecialchars($row['gender'] ?? 'N/A') ?></span>
                                                            </div>
                                                            <div class="col-sm-6">
                                                                <small class="text-muted d-block">Highest Qualification</small>
                                                                <span class="fw-semibold text-dark"><?= htmlspecialchars($row['education'] ?? 'N/A') ?></span>
                                                            </div>
                                                            <div class="col-sm-6">
                                                                <small class="text-muted d-block">Aadhaar Number</small>
                                                                <span class="fw-semibold text-dark"><?= htmlspecialchars($row['aadhaar'] ?? 'N/A') ?></span>
                                                            </div>
                                                            <div class="col-sm-6">
                                                                <small class="text-muted d-block">Contact Mobile</small>
                                                                <a href="tel:<?= htmlspecialchars($row['mobile'] ?? '') ?>" class="fw-semibold text-decoration-none"><?= htmlspecialchars($row['mobile'] ?? 'N/A') ?></a>
                                                            </div>
                                                            <div class="col-12">
                                                                <small class="text-muted d-block">Email Address</small>
                                                                <a href="mailto:<?= htmlspecialchars($row['email'] ?? '') ?>" class="fw-semibold text-decoration-none"><?= htmlspecialchars($row['email'] ?? 'N/A') ?></a>
                                                            </div>
                                                            <div class="col-12">
                                                                <small class="text-muted d-block">Full Address</small>
                                                                <span class="fw-semibold text-dark">
                                                                    <?= htmlspecialchars($row['address'] ?? '') ?>
                                                                    <?php if (!empty($row['city'])): ?>, <?= htmlspecialchars($row['city']) ?><?php endif; ?>
                                                                    <?php if (!empty($row['state'])): ?>, <?= htmlspecialchars($row['state']) ?><?php endif; ?>
                                                                    <?php if (!empty($row['pincode'])): ?> - <?= htmlspecialchars($row['pincode']) ?><?php endif; ?>
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Uploaded Documents Box -->
                                                    <div class="card border-0 shadow-sm rounded-3 p-3 bg-white">
                                                        <h6 class="fw-bold text-primary mb-2 border-bottom pb-2"><i class="fa-solid fa-folder-open me-2"></i> Attached Documents</h6>
                                                        <div class="d-flex flex-wrap gap-2 pt-1">
                                                            <?php if (!empty($row['marksheet10'])): ?>
                                                                <a href="../uploads/admissions/<?= htmlspecialchars($row['marksheet10']) ?>" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                                                    <i class="fa-solid fa-file-lines me-1"></i> 10th Marksheet
                                                                </a>
                                                            <?php endif; ?>
                                                            <?php if (!empty($row['marksheet12'])): ?>
                                                                <a href="../uploads/admissions/<?= htmlspecialchars($row['marksheet12']) ?>" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                                                    <i class="fa-solid fa-file-lines me-1"></i> 12th Marksheet
                                                                </a>
                                                            <?php endif; ?>
                                                            <?php if (!empty($row['aadhaar_card'])): ?>
                                                                <a href="../uploads/admissions/<?= htmlspecialchars($row['aadhaar_card']) ?>" target="_blank" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                                                                    <i class="fa-solid fa-id-card me-1"></i> Aadhaar Card
                                                                </a>
                                                            <?php endif; ?>
                                                            <?php if (!empty($row['photo'])): ?>
                                                                <a href="../uploads/admissions/<?= htmlspecialchars($row['photo']) ?>" target="_blank" class="btn btn-sm btn-outline-info rounded-pill px-3">
                                                                    <i class="fa-solid fa-image me-1"></i> Student Photo
                                                                </a>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer bg-white px-4 py-3">
                                            <a href="mailto:<?= htmlspecialchars($row['email']) ?>" class="btn btn-outline-primary"><i class="fa-solid fa-envelope me-1"></i> Send Email</a>
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
