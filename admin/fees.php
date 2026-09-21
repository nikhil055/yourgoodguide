<?php
$page_title = "Fee Submissions";
require_once __DIR__ . '/includes/header.php';

// Handle Actions (Delete / Status Change)
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    if ($_GET['action'] === 'delete') {
        $stmt = $pdo->prepare("DELETE FROM fee_submissions WHERE id = ?");
        $stmt->execute([$id]);
        header("Location: fees.php?msg=deleted");
        exit;
    }
    if (in_array($_GET['action'], ['Pending', 'Verified', 'Rejected'])) {
        $stmt = $pdo->prepare("UPDATE fee_submissions SET status = ? WHERE id = ?");
        $stmt->execute([$_GET['action'], $id]);
        header("Location: fees.php?msg=status_updated");
        exit;
    }
}

// Filters & Search
$search = trim($_GET['search'] ?? '');
$filter_status = trim($_GET['status'] ?? '');

$query = "SELECT * FROM fee_submissions WHERE 1=1";
$params = [];

if (!empty($search)) {
    $query .= " AND (name LIKE ? OR email LIKE ? OR contact LIKE ? OR purpose LIKE ?)";
    $term = "%$search%";
    $params = array_merge($params, [$term, $term, $term, $term]);
}
if (!empty($filter_status)) {
    $query .= " AND status = ?";
    $params[] = $filter_status;
}

$query .= " ORDER BY id DESC";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$fees = $stmt->fetchAll();

// Mark unread as read
$pdo->query("UPDATE fee_submissions SET is_read = 1 WHERE is_read = 0");
?>

<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h4 class="fw-bold text-dark mb-1">Student Fee Submissions</h4>
        <p class="text-muted small mb-0">Total <?= count($fees) ?> fee receipts records found</p>
    </div>
</div>

<!-- FILTER & SEARCH -->
<div class="card border-0 shadow-sm rounded-3 mb-4">
    <div class="card-body p-3">
        <form method="GET" action="fees.php" class="row g-2 align-items-center">
            <div class="col-md-6">
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control border-start-0" placeholder="Search by student name, email, phone, purpose..." value="<?= htmlspecialchars($search) ?>">
                </div>
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">All Statuses</option>
                    <option value="Pending" <?= $filter_status === 'Pending' ? 'selected' : '' ?>>Pending</option>
                    <option value="Verified" <?= $filter_status === 'Verified' ? 'selected' : '' ?>>Verified</option>
                    <option value="Rejected" <?= $filter_status === 'Rejected' ? 'selected' : '' ?>>Rejected</option>
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary w-100"><i class="fa-solid fa-filter me-1"></i> Filter</button>
                <a href="fees.php" class="btn btn-light"><i class="fa-solid fa-rotate-left"></i></a>
            </div>
        </form>
    </div>
</div>

<!-- FEES TABLE -->
<div class="card border-0 shadow-sm rounded-3">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Student Details</th>
                        <th>Course & Type</th>
                        <th>Purpose</th>
                        <th>Receipt & Signature</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($fees)): ?>
                        <tr><td colspan="8" class="text-center py-5 text-muted">No fee submission records found.</td></tr>
                    <?php else: ?>
                        <?php foreach ($fees as $idx => $row): ?>
                            <tr>
                                <td><?= $idx + 1 ?></td>
                                <td>
                                    <div class="fw-bold text-dark"><?= htmlspecialchars($row['name']) ?></div>
                                    <div class="small text-muted"><i class="fa-solid fa-phone me-1"></i> <?= htmlspecialchars($row['contact'] ?? 'N/A') ?></div>
                                    <div class="small text-muted"><i class="fa-solid fa-envelope me-1"></i> <?= htmlspecialchars($row['email'] ?? 'N/A') ?></div>
                                </td>
                                <td>
                                    <span class="badge bg-info-subtle text-info border border-info-subtle"><?= htmlspecialchars($row['course'] ?? 'N/A') ?></span>
                                    <div class="small text-muted mt-1">Type: <?= htmlspecialchars($row['fee_type'] ?? 'N/A') ?></div>
                                </td>
                                <td><?= htmlspecialchars($row['purpose'] ?? 'N/A') ?></td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <?php if (!empty($row['receipt'])): ?>
                                            <a href="../uploads/fees/<?= htmlspecialchars($row['receipt']) ?>" target="_blank" class="btn btn-sm btn-outline-primary py-1 px-2 small">
                                                <i class="fa-solid fa-receipt me-1"></i> Receipt
                                            </a>
                                        <?php endif; ?>
                                        <?php if (!empty($row['signature'])): ?>
                                            <a href="../uploads/fees/<?= htmlspecialchars($row['signature']) ?>" target="_blank" class="btn btn-sm btn-outline-secondary py-1 px-2 small">
                                                <i class="fa-solid fa-signature me-1"></i> Sign
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-sm dropdown-toggle rounded-pill px-2 py-1 <?= $row['status'] === 'Verified' ? 'btn-success' : ($row['status'] === 'Rejected' ? 'btn-danger' : 'btn-warning') ?>" type="button" data-bs-toggle="dropdown">
                                            <?= htmlspecialchars($row['status']) ?>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" href="fees.php?action=Pending&id=<?= $row['id'] ?>">Set Pending</a></li>
                                            <li><a class="dropdown-item" href="fees.php?action=Verified&id=<?= $row['id'] ?>">Set Verified</a></li>
                                            <li><a class="dropdown-item" href="fees.php?action=Rejected&id=<?= $row['id'] ?>">Set Rejected</a></li>
                                        </ul>
                                    </div>
                                </td>
                                <td class="text-muted small">
                                    <?= date('d M Y', strtotime($row['created_at'])) ?><br>
                                    <span class="text-secondary"><?= date('h:i A', strtotime($row['created_at'])) ?></span>
                                </td>
                                <td class="text-end">
                                    <a href="fees.php?action=delete&id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this fee submission record?')" title="Delete">
                                        <i class="fa-solid fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
