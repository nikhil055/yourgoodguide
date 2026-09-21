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

<div class="space-y-4">

    <!-- PAGE HEADER -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white p-4 rounded-md border border-slate-200">
        <div>
            <div class="flex items-center gap-2">
                <h2 class="text-base font-bold text-[#0e1e2e]">Fee Submissions</h2>
                <span class="px-2 py-0.5 text-[11px] font-semibold bg-slate-100 text-slate-600 rounded border border-slate-200">
                    <?= count($fees) ?> Records
                </span>
            </div>
            <p class="text-xs text-slate-400 mt-0.5">Manage and verify manual fee receipts uploaded by students</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="../fee-submission.php" target="_blank" class="px-3.5 py-1.5 text-xs font-semibold text-white bg-[#fe7c03] hover:bg-[#ea6c00] rounded-md transition-colors flex items-center gap-1.5 no-underline">
                <i class="fa-solid fa-receipt text-[11px]"></i>
                <span>Public Fee Submission</span>
            </a>
        </div>
    </div>

    <!-- FILTER & SEARCH BAR -->
    <div class="bg-white border border-slate-200 rounded-md p-3">
        <form method="GET" action="fees.php" class="flex flex-wrap items-center justify-between gap-2.5">
            <div class="flex flex-wrap items-center gap-2">
                <select name="status" onchange="this.form.submit()" class="text-xs bg-white border border-slate-200 rounded-md px-2.5 py-1.5 text-slate-700 focus:outline-none focus:border-[#fe7c03]">
                    <option value="">All Statuses</option>
                    <option value="Pending" <?= $filter_status === 'Pending' ? 'selected' : '' ?>>Pending</option>
                    <option value="Verified" <?= $filter_status === 'Verified' ? 'selected' : '' ?>>Verified</option>
                    <option value="Rejected" <?= $filter_status === 'Rejected' ? 'selected' : '' ?>>Rejected</option>
                </select>

                <?php if (!empty($filter_status) || !empty($search)): ?>
                    <a href="fees.php" class="px-2 py-1 text-xs text-slate-500 hover:text-slate-800 bg-slate-100 hover:bg-slate-200 rounded transition-colors flex items-center gap-1 no-underline">
                        <i class="fa-solid fa-xmark text-[10px]"></i> Reset
                    </a>
                <?php endif; ?>
            </div>

            <!-- Search Input -->
            <div class="relative min-w-[240px]">
                <i class="fa-solid fa-magnifying-glass absolute left-2.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                <input type="text" name="search" placeholder="Search name, phone, purpose..." value="<?= htmlspecialchars($search) ?>" class="w-full text-xs pl-8 pr-3 py-1.5 bg-white border border-slate-200 rounded-md focus:outline-none focus:border-[#fe7c03] text-slate-800">
            </div>
        </form>
    </div>

    <!-- FEES TABLE -->
    <div class="bg-white border border-slate-200 rounded-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 font-semibold uppercase text-[10px] tracking-wider">
                    <tr>
                        <th class="px-4 py-2.5">#</th>
                        <th class="px-4 py-2.5">Student Details</th>
                        <th class="px-4 py-2.5">Course &amp; Type</th>
                        <th class="px-4 py-2.5">Purpose</th>
                        <th class="px-4 py-2.5">Receipt &amp; Sign</th>
                        <th class="px-4 py-2.5">Status</th>
                        <th class="px-4 py-2.5">Submitted Date</th>
                        <th class="px-4 py-2.5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php if (empty($fees)): ?>
                        <tr>
                            <td colspan="8" class="px-4 py-8 text-center text-slate-400">
                                <i class="fa-solid fa-receipt text-2xl mb-1 text-slate-300 block"></i>
                                <span class="font-semibold text-slate-700 block">No fee submission records found</span>
                                <span class="text-[11px]">No submissions match your search or filter.</span>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($fees as $idx => $row): ?>
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="px-4 py-2.5 font-mono text-[11px] text-slate-400"><?= $idx + 1 ?></td>
                                
                                <td class="px-4 py-2.5">
                                    <div class="font-semibold text-[#0e1e2e]"><?= htmlspecialchars($row['name']) ?></div>
                                    <div class="text-[11px] text-slate-500"><i class="fa-solid fa-phone text-[9px] text-slate-400 mr-1"></i><?= htmlspecialchars($row['contact'] ?? 'N/A') ?></div>
                                    <div class="text-[10px] text-slate-400"><i class="fa-regular fa-envelope text-[9px] text-slate-400 mr-1"></i><?= htmlspecialchars($row['email'] ?? 'N/A') ?></div>
                                </td>

                                <td class="px-4 py-2.5">
                                    <span class="px-2 py-0.5 text-[10px] font-semibold rounded bg-sky-50 text-sky-700 border border-sky-200 inline-block">
                                        <?= htmlspecialchars($row['course'] ?? 'N/A') ?>
                                    </span>
                                    <div class="text-[10px] text-slate-400 mt-0.5">Type: <?= htmlspecialchars($row['fee_type'] ?? 'N/A') ?></div>
                                </td>

                                <td class="px-4 py-2.5 text-slate-700">
                                    <?= htmlspecialchars($row['purpose'] ?? 'N/A') ?>
                                </td>

                                <td class="px-4 py-2.5">
                                    <div class="flex items-center gap-1.5">
                                        <?php if (!empty($row['receipt'])): ?>
                                            <a href="../uploads/fees/<?= htmlspecialchars($row['receipt']) ?>" target="_blank" class="px-2 py-1 text-[11px] font-medium rounded bg-slate-100 text-slate-700 border border-slate-200 hover:bg-slate-200 no-underline inline-flex items-center gap-1">
                                                <i class="fa-solid fa-file-invoice text-emerald-600"></i> Receipt
                                            </a>
                                        <?php endif; ?>
                                        <?php if (!empty($row['signature'])): ?>
                                            <a href="../uploads/fees/<?= htmlspecialchars($row['signature']) ?>" target="_blank" class="px-2 py-1 text-[11px] font-medium rounded bg-slate-100 text-slate-700 border border-slate-200 hover:bg-slate-200 no-underline inline-flex items-center gap-1">
                                                <i class="fa-solid fa-signature text-purple-600"></i> Sign
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </td>

                                <td class="px-4 py-2.5">
                                    <div class="dropdown inline-block">
                                        <button class="px-2 py-0.5 text-[10px] font-semibold rounded border dropdown-toggle <?= $row['status'] === 'Verified' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : ($row['status'] === 'Rejected' ? 'bg-rose-50 text-rose-700 border-rose-200' : 'bg-amber-50 text-amber-700 border-amber-200') ?>" type="button" data-bs-toggle="dropdown">
                                            <?= htmlspecialchars($row['status']) ?>
                                        </button>
                                        <ul class="dropdown-menu text-xs border border-slate-200 rounded-md py-1">
                                            <li><a class="dropdown-item py-1.5" href="fees.php?action=Pending&id=<?= $row['id'] ?>">Set Pending</a></li>
                                            <li><a class="dropdown-item py-1.5 text-emerald-600" href="fees.php?action=Verified&id=<?= $row['id'] ?>">Set Verified</a></li>
                                            <li><a class="dropdown-item py-1.5 text-rose-600" href="fees.php?action=Rejected&id=<?= $row['id'] ?>">Set Rejected</a></li>
                                        </ul>
                                    </div>
                                </td>

                                <td class="px-4 py-2.5 text-slate-400 text-[11px] whitespace-nowrap">
                                    <?= date('d M Y', strtotime($row['created_at'])) ?><br>
                                    <span class="text-[10px]"><?= date('h:i A', strtotime($row['created_at'])) ?></span>
                                </td>

                                <td class="px-4 py-2.5 text-right">
                                    <a href="fees.php?action=delete&id=<?= $row['id'] ?>" onclick="return confirm('Delete this fee submission?')" class="w-7 h-7 rounded border border-slate-200 bg-white text-slate-400 hover:text-rose-600 hover:border-rose-200 inline-flex items-center justify-center transition-colors no-underline" title="Delete">
                                        <i class="fa-regular fa-trash-can text-[10px]"></i>
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
