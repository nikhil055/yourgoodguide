<?php
$page_title = "Contact Inquiries";
require_once __DIR__ . '/includes/header.php';

// Handle Actions
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    if ($_GET['action'] === 'delete') {
        $stmt = $pdo->prepare("DELETE FROM contact_inquiries WHERE id = ?");
        $stmt->execute([$id]);
        header("Location: contacts.php?msg=deleted");
        exit;
    }
    if (in_array($_GET['action'], ['New', 'In Progress', 'Resolved'])) {
        $stmt = $pdo->prepare("UPDATE contact_inquiries SET status = ? WHERE id = ?");
        $stmt->execute([$_GET['action'], $id]);
        header("Location: contacts.php?msg=status_updated");
        exit;
    }
}

// Filters & Search
$search = trim($_GET['search'] ?? '');
$filter_status = trim($_GET['status'] ?? '');

$query = "SELECT * FROM contact_inquiries WHERE 1=1";
$params = [];

if (!empty($search)) {
    $query .= " AND (name LIKE ? OR email LIKE ? OR phone LIKE ? OR subject LIKE ? OR message LIKE ?)";
    $term = "%$search%";
    $params = array_merge($params, [$term, $term, $term, $term, $term]);
}
if (!empty($filter_status)) {
    $query .= " AND status = ?";
    $params[] = $filter_status;
}

$query .= " ORDER BY id DESC";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$inquiries = $stmt->fetchAll();

// Mark unread as read
$pdo->query("UPDATE contact_inquiries SET is_read = 1 WHERE is_read = 0");
?>

<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h4 class="fw-bold text-dark mb-1">Contact Inquiries & Leads</h4>
        <p class="text-muted small mb-0">Total <?= count($inquiries) ?> inquiries received from website contact form</p>
    </div>
</div>

<!-- FILTER & SEARCH -->
<div class="card border-0 shadow-sm rounded-3 mb-4">
    <div class="card-body p-3">
        <form method="GET" action="contacts.php" class="row g-2 align-items-center">
            <div class="col-md-6">
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control border-start-0" placeholder="Search by name, email, message..." value="<?= htmlspecialchars($search) ?>">
                </div>
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">All Statuses</option>
                    <option value="New" <?= $filter_status === 'New' ? 'selected' : '' ?>>New</option>
                    <option value="In Progress" <?= $filter_status === 'In Progress' ? 'selected' : '' ?>>In Progress</option>
                    <option value="Resolved" <?= $filter_status === 'Resolved' ? 'selected' : '' ?>>Resolved</option>
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary w-100"><i class="fa-solid fa-filter me-1"></i> Filter</button>
                <a href="contacts.php" class="btn btn-light"><i class="fa-solid fa-rotate-left"></i></a>
            </div>
        </form>
    </div>
</div>

<!-- INQUIRIES TABLE -->
<div class="card border-0 shadow-sm rounded-3">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Sender Name & Contact</th>
                        <th>Subject</th>
                        <th>Message</th>
                        <th>Status</th>
                        <th>Received On</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($inquiries)): ?>
                        <tr><td colspan="7" class="text-center py-5 text-muted">No contact inquiries found.</td></tr>
                    <?php else: ?>
                        <?php foreach ($inquiries as $idx => $row): ?>
                            <tr>
                                <td><?= $idx + 1 ?></td>
                                <td>
                                    <div class="fw-bold text-dark"><?= htmlspecialchars($row['name']) ?></div>
                                    <div class="small text-muted"><i class="fa-solid fa-envelope me-1"></i> <a href="mailto:<?= htmlspecialchars($row['email']) ?>" class="text-decoration-none"><?= htmlspecialchars($row['email']) ?></a></div>
                                    <?php if (!empty($row['phone'])): ?>
                                        <div class="small text-muted"><i class="fa-solid fa-phone me-1"></i> <a href="tel:<?= htmlspecialchars($row['phone']) ?>" class="text-decoration-none"><?= htmlspecialchars($row['phone']) ?></a></div>
                                    <?php endif; ?>
                                </td>
                                <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($row['subject'] ?? 'General') ?></span></td>
                                <td style="max-width: 320px;">
                                    <div class="text-truncate"><?= htmlspecialchars($row['message']) ?></div>
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-sm dropdown-toggle rounded-pill px-2 py-1 <?= $row['status'] === 'Resolved' ? 'btn-success' : ($row['status'] === 'In Progress' ? 'btn-info' : 'btn-danger') ?>" type="button" data-bs-toggle="dropdown">
                                            <?= htmlspecialchars($row['status']) ?>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" href="contacts.php?action=New&id=<?= $row['id'] ?>">Mark New</a></li>
                                            <li><a class="dropdown-item" href="contacts.php?action=In Progress&id=<?= $row['id'] ?>">Set In Progress</a></li>
                                            <li><a class="dropdown-item" href="contacts.php?action=Resolved&id=<?= $row['id'] ?>">Set Resolved</a></li>
                                        </ul>
                                    </div>
                                </td>
                                <td class="text-muted small">
                                    <?= date('d M Y', strtotime($row['created_at'])) ?><br>
                                    <span class="text-secondary"><?= date('h:i A', strtotime($row['created_at'])) ?></span>
                                </td>
                                <td class="text-end">
                                    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#inquiryModal<?= $row['id'] ?>" title="View Message">
                                        <i class="fa-solid fa-eye"></i>
                                    </button>
                                    <a href="mailto:<?= htmlspecialchars($row['email']) ?>" class="btn btn-sm btn-outline-success" title="Reply via Email">
                                        <i class="fa-solid fa-reply"></i>
                                    </a>
                                    <a href="contacts.php?action=delete&id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this inquiry?')" title="Delete">
                                        <i class="fa-solid fa-trash"></i>
                                    </a>
                                </td>
                            </tr>

                            <!-- VIEW MODAL -->
                            <div class="modal fade" id="inquiryModal<?= $row['id'] ?>" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content border-0 shadow">
                                        <div class="modal-header bg-warning-subtle text-dark">
                                            <h5 class="modal-title fw-bold"><i class="fa-solid fa-envelope-open me-2"></i> Inquiry from <?= htmlspecialchars($row['name']) ?></h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body p-4">
                                            <div class="mb-3">
                                                <small class="text-muted d-block">Subject</small>
                                                <strong><?= htmlspecialchars($row['subject']) ?></strong>
                                            </div>
                                            <div class="mb-3">
                                                <small class="text-muted d-block">Contact Info</small>
                                                <span>Email: <?= htmlspecialchars($row['email']) ?> | Phone: <?= htmlspecialchars($row['phone'] ?? 'N/A') ?></span>
                                            </div>
                                            <div class="mb-3">
                                                <small class="text-muted d-block">Message</small>
                                                <div class="p-3 bg-light rounded-3 mt-1" style="white-space: pre-wrap;"><?= htmlspecialchars($row['message']) ?></div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <a href="mailto:<?= htmlspecialchars($row['email']) ?>" class="btn btn-primary"><i class="fa-solid fa-paper-plane me-1"></i> Reply Email</a>
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
