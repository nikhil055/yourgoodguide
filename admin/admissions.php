<?php
$page_title = "Admissions";
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

<div class="space-y-4">

    <!-- PAGE HEADER -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white p-4 rounded-md border border-slate-200">
        <div>
            <div class="flex items-center gap-2">
                <h2 class="text-base font-bold text-[#0e1e2e]">Admission Applications</h2>
                <span class="px-2 py-0.5 text-[11px] font-semibold bg-slate-100 text-slate-600 rounded border border-slate-200">
                    <?= count($admissions) ?> Applications
                </span>
            </div>
            <p class="text-xs text-slate-400 mt-0.5">Review and manage student admission submissions and attached documents</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="../admission.php" target="_blank" class="px-3.5 py-1.5 text-xs font-semibold text-white bg-[#fe7c03] hover:bg-[#ea6c00] rounded-md transition-colors flex items-center gap-1.5 no-underline">
                <i class="fa-solid fa-file-signature text-[11px]"></i>
                <span>Public Admission Form</span>
            </a>
        </div>
    </div>

    <!-- FILTER & SEARCH BAR -->
    <div class="bg-white border border-slate-200 rounded-md p-3">
        <form method="GET" action="admissions.php" class="flex flex-wrap items-center justify-between gap-2.5">
            <div class="flex flex-wrap items-center gap-2">
                <!-- Status Select -->
                <select name="status" onchange="this.form.submit()" class="text-xs bg-white border border-slate-200 rounded-md px-2.5 py-1.5 text-slate-700 focus:outline-none focus:border-[#fe7c03]">
                    <option value="">All Statuses</option>
                    <option value="Pending" <?= $filter_status === 'Pending' ? 'selected' : '' ?>>Pending</option>
                    <option value="Approved" <?= $filter_status === 'Approved' ? 'selected' : '' ?>>Approved</option>
                    <option value="Rejected" <?= $filter_status === 'Rejected' ? 'selected' : '' ?>>Rejected</option>
                </select>

                <!-- Course Input -->
                <input type="text" name="course" placeholder="Filter by course name..." value="<?= htmlspecialchars($filter_course) ?>" class="text-xs bg-white border border-slate-200 rounded-md px-2.5 py-1.5 text-slate-700 focus:outline-none focus:border-[#fe7c03]">

                <?php if (!empty($filter_status) || !empty($filter_course) || !empty($search)): ?>
                    <a href="admissions.php" class="px-2 py-1 text-xs text-slate-500 hover:text-slate-800 bg-slate-100 hover:bg-slate-200 rounded transition-colors flex items-center gap-1 no-underline">
                        <i class="fa-solid fa-xmark text-[10px]"></i> Reset
                    </a>
                <?php endif; ?>
            </div>

            <!-- Search Input -->
            <div class="relative min-w-[240px]">
                <i class="fa-solid fa-magnifying-glass absolute left-2.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                <input type="text" name="search" placeholder="Search applicant, email, phone..." value="<?= htmlspecialchars($search) ?>" class="w-full text-xs pl-8 pr-3 py-1.5 bg-white border border-slate-200 rounded-md focus:outline-none focus:border-[#fe7c03] text-slate-800">
            </div>
        </form>
    </div>

    <!-- ADMISSIONS TABLE -->
    <div class="bg-white border border-slate-200 rounded-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 font-semibold uppercase text-[10px] tracking-wider">
                    <tr>
                        <th class="px-4 py-2.5">#</th>
                        <th class="px-4 py-2.5">Applicant</th>
                        <th class="px-4 py-2.5">Course &amp; Qualification</th>
                        <th class="px-4 py-2.5">Contact Details</th>
                        <th class="px-4 py-2.5">Attached Files</th>
                        <th class="px-4 py-2.5">Status</th>
                        <th class="px-4 py-2.5">Applied Date</th>
                        <th class="px-4 py-2.5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php if (empty($admissions)): ?>
                        <tr>
                            <td colspan="8" class="px-4 py-8 text-center text-slate-400">
                                <i class="fa-solid fa-id-card text-2xl mb-1 text-slate-300 block"></i>
                                <span class="font-semibold text-slate-700 block">No admission applications found</span>
                                <span class="text-[11px]">No applications match your current filters.</span>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($admissions as $idx => $row): ?>
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="px-4 py-2.5 font-mono text-[11px] text-slate-400"><?= $idx + 1 ?></td>
                                
                                 <!-- Student Info -->
                                <td class="px-4 py-2.5">
                                    <div class="flex items-center gap-2.5">
                                        <?php 
                                        $photoSrc = '';
                                        if (!empty($row['photo'])) {
                                            if (file_exists(__DIR__ . '/../uploads/admissions/' . $row['photo'])) {
                                                $photoSrc = '../uploads/admissions/' . htmlspecialchars($row['photo']);
                                            } elseif (file_exists(__DIR__ . '/../' . $row['photo'])) {
                                                $photoSrc = '../' . htmlspecialchars($row['photo']);
                                            }
                                        }
                                        ?>
                                        <?php if (!empty($photoSrc)): ?>
                                            <img src="<?= $photoSrc ?>" class="w-8 h-8 rounded-full object-cover border border-slate-200 shrink-0" alt="Avatar">
                                        <?php else: ?>
                                            <div class="w-8 h-8 rounded-full bg-slate-100 border border-slate-200 text-slate-600 font-bold flex items-center justify-center text-xs shrink-0">
                                                <?= strtoupper(substr($row['name'], 0, 1)) ?>
                                            </div>
                                        <?php endif; ?>
                                        <div>
                                            <div class="font-semibold text-[#0e1e2e] leading-tight flex items-center gap-1.5">
                                                <span><?= htmlspecialchars($row['name']) ?></span>
                                                <?php if (!empty($row['student_id'])): ?>
                                                    <span class="font-mono text-[9px] px-1 py-0.2 rounded bg-slate-100 text-slate-600 border border-slate-200"><?= htmlspecialchars($row['student_id']) ?></span>
                                                <?php endif; ?>
                                            </div>
                                            <div class="text-[10px] text-slate-400">Father: <?= htmlspecialchars($row['father_name'] ?? 'N/A') ?></div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Course & Education -->
                                <td class="px-4 py-2.5">
                                    <span class="px-2 py-0.5 text-[10px] font-semibold rounded bg-orange-50 text-[#fe7c03] border border-orange-200 inline-block">
                                        <?= htmlspecialchars($row['course'] ?? 'N/A') ?>
                                    </span>
                                    <div class="text-[11px] text-slate-500 mt-0.5"><?= htmlspecialchars($row['education'] ?? 'N/A') ?></div>
                                </td>

                                <!-- Contact & Location -->
                                <td class="px-4 py-2.5">
                                    <div class="font-medium text-slate-800"><?= htmlspecialchars($row['mobile'] ?? 'N/A') ?></div>
                                    <div class="text-[11px] text-slate-400"><?= htmlspecialchars($row['email'] ?? 'N/A') ?></div>
                                    <div class="text-[10px] text-slate-400"><?= htmlspecialchars($row['city'] ?? '') ?>, <?= htmlspecialchars($row['state'] ?? '') ?></div>
                                </td>

                                <!-- Documents -->
                                <td class="px-4 py-2.5">
                                    <div class="flex flex-wrap gap-1">
                                        <?php if (!empty($row['marksheet10'])): ?>
                                            <a href="../uploads/admissions/<?= htmlspecialchars($row['marksheet10']) ?>" target="_blank" class="px-1.5 py-0.5 text-[10px] font-medium rounded bg-slate-100 text-slate-700 border border-slate-200 hover:bg-slate-200 no-underline inline-flex items-center gap-1">
                                                <i class="fa-solid fa-file-pdf text-rose-500"></i> 10th
                                            </a>
                                        <?php endif; ?>
                                        <?php if (!empty($row['marksheet12'])): ?>
                                            <a href="../uploads/admissions/<?= htmlspecialchars($row['marksheet12']) ?>" target="_blank" class="px-1.5 py-0.5 text-[10px] font-medium rounded bg-slate-100 text-slate-700 border border-slate-200 hover:bg-slate-200 no-underline inline-flex items-center gap-1">
                                                <i class="fa-solid fa-file-pdf text-rose-500"></i> 12th
                                            </a>
                                        <?php endif; ?>
                                        <?php if (!empty($row['aadhaar_card'])): ?>
                                            <a href="../uploads/admissions/<?= htmlspecialchars($row['aadhaar_card']) ?>" target="_blank" class="px-1.5 py-0.5 text-[10px] font-medium rounded bg-slate-100 text-slate-700 border border-slate-200 hover:bg-slate-200 no-underline inline-flex items-center gap-1">
                                                <i class="fa-solid fa-id-card text-sky-500"></i> Aadhaar
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </td>

                                <!-- Status -->
                                <td class="px-4 py-2.5">
                                    <div class="dropdown inline-block">
                                        <button class="px-2 py-0.5 text-[10px] font-semibold rounded border dropdown-toggle <?= $row['status'] === 'Approved' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : ($row['status'] === 'Rejected' ? 'bg-rose-50 text-rose-700 border-rose-200' : 'bg-amber-50 text-amber-700 border-amber-200') ?>" type="button" data-bs-toggle="dropdown">
                                            <?= htmlspecialchars($row['status']) ?>
                                        </button>
                                        <ul class="dropdown-menu text-xs border border-slate-200 rounded-md py-1">
                                            <li><a class="dropdown-item py-1.5" href="admissions.php?action=Pending&id=<?= $row['id'] ?>">Set Pending</a></li>
                                            <li><a class="dropdown-item py-1.5 text-emerald-600" href="admissions.php?action=Approved&id=<?= $row['id'] ?>">Set Approved</a></li>
                                            <li><a class="dropdown-item py-1.5 text-rose-600" href="admissions.php?action=Rejected&id=<?= $row['id'] ?>">Set Rejected</a></li>
                                        </ul>
                                    </div>
                                </td>

                                <!-- Applied Date -->
                                <td class="px-4 py-2.5 text-slate-400 text-[11px] whitespace-nowrap">
                                    <?= date('d M Y', strtotime($row['created_at'])) ?><br>
                                    <span class="text-[10px]"><?= date('h:i A', strtotime($row['created_at'])) ?></span>
                                </td>

                                <!-- Actions -->
                                <td class="px-4 py-2.5 text-right">
                                    <div class="inline-flex items-center gap-1">
                                        <!-- View Modal -->
                                        <button type="button" class="w-7 h-7 rounded border border-slate-200 bg-white text-slate-600 hover:text-[#fe7c03] hover:border-orange-200 flex items-center justify-center transition-colors" data-bs-toggle="modal" data-bs-target="#viewModal<?= $row['id'] ?>" title="View Details">
                                            <i class="fa-solid fa-eye text-[10px]"></i>
                                        </button>
                                        <!-- Delete -->
                                        <a href="admissions.php?action=delete&id=<?= $row['id'] ?>" onclick="return confirm('Delete this admission application?')" class="w-7 h-7 rounded border border-slate-200 bg-white text-slate-400 hover:text-rose-600 hover:border-rose-200 flex items-center justify-center transition-colors no-underline" title="Delete">
                                            <i class="fa-regular fa-trash-can text-[10px]"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>

                            <!-- FULL DETAILS MODAL -->
                            <div class="modal fade" id="viewModal<?= $row['id'] ?>" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-lg modal-dialog-centered">
                                    <div class="modal-content border border-slate-200 rounded-md shadow-none overflow-hidden">
                                        <div class="modal-header border-b border-slate-200 py-3 px-4 bg-slate-50">
                                            <div class="flex items-center gap-2">
                                                <span class="w-6 text-center text-[#fe7c03]"><i class="fa-solid fa-id-badge text-sm"></i></span>
                                                <div>
                                                    <h5 class="modal-title text-xs font-bold text-[#0e1e2e] uppercase tracking-wider mb-0">Admission Application #<?= $row['id'] ?></h5>
                                                    <span class="text-[10px] text-slate-400">Submitted on <?= date('d M Y, h:i A', strtotime($row['created_at'])) ?></span>
                                                </div>
                                            </div>
                                            <button type="button" class="btn-close text-xs" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body p-4 bg-slate-50/50 space-y-3">
                                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                                 <!-- Profile Box -->
                                                <div class="bg-white border border-slate-200 rounded-md p-4 text-center">
                                                    <?php if (!empty($photoSrc)): ?>
                                                        <img src="<?= $photoSrc ?>" class="w-24 h-28 rounded object-cover border border-slate-200 mx-auto mb-2" alt="Photo">
                                                    <?php else: ?>
                                                        <div class="w-20 h-20 rounded bg-slate-100 border border-slate-200 text-slate-400 text-2xl flex items-center justify-center mx-auto mb-2">
                                                            <i class="fa-solid fa-user"></i>
                                                        </div>
                                                    <?php endif; ?>
                                                    <h5 class="text-sm font-bold text-[#0e1e2e]"><?= htmlspecialchars($row['name']) ?></h5>
                                                    <?php if (!empty($row['student_id'])): ?>
                                                        <div class="font-mono text-[10px] text-slate-500 mt-0.5">
                                                            <i class="fa-solid fa-id-badge text-[#fe7c03] me-1"></i><?= htmlspecialchars($row['student_id']) ?>
                                                        </div>
                                                    <?php endif; ?>
                                                    <span class="px-2 py-0.5 text-[11px] font-semibold bg-orange-50 text-[#fe7c03] border border-orange-200 rounded mt-1.5 inline-block">
                                                        <?= htmlspecialchars($row['course'] ?? 'N/A') ?>
                                                    </span>
                                                    <div class="mt-2">
                                                        <span class="px-2 py-0.5 text-[10px] font-bold rounded <?= $row['status'] === 'Approved' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : ($row['status'] === 'Rejected' ? 'bg-rose-50 text-rose-700 border border-rose-200' : 'bg-amber-50 text-amber-700 border border-amber-200') ?>">
                                                            Status: <?= htmlspecialchars($row['status']) ?>
                                                        </span>
                                                    </div>
                                                </div>

                                                <!-- Details Box -->
                                                <div class="md:col-span-2 bg-white border border-slate-200 rounded-md p-4 space-y-3">
                                                    <h6 class="text-xs font-bold text-[#0e1e2e] uppercase tracking-wider pb-1 border-b border-slate-100">Personal &amp; Contact Details</h6>
                                                    <div class="grid grid-cols-2 gap-2 text-xs">
                                                        <div>
                                                            <span class="text-[10px] text-slate-400 block uppercase">Father's Name</span>
                                                            <span class="font-semibold text-slate-800"><?= htmlspecialchars($row['father_name'] ?? 'N/A') ?></span>
                                                        </div>
                                                        <div>
                                                            <span class="text-[10px] text-slate-400 block uppercase">Date of Birth</span>
                                                            <span class="font-semibold text-slate-800"><?= htmlspecialchars($row['dob'] ?? 'N/A') ?></span>
                                                        </div>
                                                        <div>
                                                            <span class="text-[10px] text-slate-400 block uppercase">Gender</span>
                                                            <span class="font-semibold text-slate-800"><?= htmlspecialchars($row['gender'] ?? 'N/A') ?></span>
                                                        </div>
                                                        <div>
                                                            <span class="text-[10px] text-slate-400 block uppercase">Highest Qualification</span>
                                                            <span class="font-semibold text-slate-800"><?= htmlspecialchars($row['education'] ?? 'N/A') ?></span>
                                                        </div>
                                                        <div>
                                                            <span class="text-[10px] text-slate-400 block uppercase">Board / University</span>
                                                            <span class="font-semibold text-slate-800"><?= htmlspecialchars($row['board_university'] ?? 'N/A') ?></span>
                                                        </div>
                                                        <div>
                                                            <span class="text-[10px] text-slate-400 block uppercase">Passing Year &amp; Marks</span>
                                                            <span class="font-semibold text-slate-800"><?= htmlspecialchars($row['passing_year'] ?? 'N/A') ?><?= !empty($row['percentage']) ? ' (' . htmlspecialchars($row['percentage']) . ')' : '' ?></span>
                                                        </div>
                                                        <div>
                                                            <span class="text-[10px] text-slate-400 block uppercase">Aadhaar Number</span>
                                                            <span class="font-mono font-semibold text-slate-800"><?= htmlspecialchars($row['aadhaar'] ?? 'N/A') ?></span>
                                                        </div>
                                                        <div>
                                                            <span class="text-[10px] text-slate-400 block uppercase">Contact Mobile</span>
                                                            <a href="tel:<?= htmlspecialchars($row['mobile'] ?? '') ?>" class="font-semibold text-slate-800 hover:text-[#fe7c03]"><?= htmlspecialchars($row['mobile'] ?? 'N/A') ?></a>
                                                        </div>
                                                        <div class="col-span-2">
                                                            <span class="text-[10px] text-slate-400 block uppercase">Email Address</span>
                                                            <a href="mailto:<?= htmlspecialchars($row['email'] ?? '') ?>" class="font-semibold text-slate-800 hover:text-[#fe7c03]"><?= htmlspecialchars($row['email'] ?? 'N/A') ?></a>
                                                        </div>
                                                        <div class="col-span-2">
                                                            <span class="text-[10px] text-slate-400 block uppercase">Address</span>
                                                            <span class="text-slate-800">
                                                                <?= htmlspecialchars($row['address'] ?? '') ?>
                                                                <?php if (!empty($row['city'])): ?>, <?= htmlspecialchars($row['city']) ?><?php endif; ?>
                                                                <?php if (!empty($row['state'])): ?>, <?= htmlspecialchars($row['state']) ?><?php endif; ?>
                                                                <?php if (!empty($row['pincode'])): ?> - <?= htmlspecialchars($row['pincode']) ?><?php endif; ?>
                                                            </span>
                                                        </div>
                                                    </div>

                                                    <!-- Attachments in modal -->
                                                    <div class="pt-2 border-t border-slate-100">
                                                        <span class="text-[10px] text-slate-400 block uppercase mb-1.5 font-bold">Attached Documents</span>
                                                        <div class="flex flex-wrap gap-1.5">
                                                            <?php if (!empty($row['marksheet10'])): ?>
                                                                <a href="../uploads/admissions/<?= htmlspecialchars($row['marksheet10']) ?>" target="_blank" class="px-2 py-1 text-[11px] font-medium rounded bg-slate-100 border border-slate-200 hover:bg-slate-200 text-slate-700 no-underline inline-flex items-center gap-1">
                                                                    <i class="fa-solid fa-file-pdf text-rose-500"></i> 10th Marksheet
                                                                </a>
                                                            <?php endif; ?>
                                                            <?php if (!empty($row['marksheet12'])): ?>
                                                                <a href="../uploads/admissions/<?= htmlspecialchars($row['marksheet12']) ?>" target="_blank" class="px-2 py-1 text-[11px] font-medium rounded bg-slate-100 border border-slate-200 hover:bg-slate-200 text-slate-700 no-underline inline-flex items-center gap-1">
                                                                    <i class="fa-solid fa-file-pdf text-rose-500"></i> 12th Marksheet
                                                                </a>
                                                            <?php endif; ?>
                                                            <?php if (!empty($row['aadhaar_card'])): ?>
                                                                <a href="../uploads/admissions/<?= htmlspecialchars($row['aadhaar_card']) ?>" target="_blank" class="px-2 py-1 text-[11px] font-medium rounded bg-slate-100 border border-slate-200 hover:bg-slate-200 text-slate-700 no-underline inline-flex items-center gap-1">
                                                                    <i class="fa-solid fa-id-card text-sky-500"></i> Aadhaar Card
                                                                </a>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer border-t border-slate-200 py-2.5 px-4 bg-slate-50">
                                            <a href="mailto:<?= htmlspecialchars($row['email']) ?>" class="px-3 py-1.5 text-xs font-semibold text-white bg-[#fe7c03] hover:bg-[#ea6c00] rounded-md transition-colors no-underline inline-flex items-center gap-1">
                                                <i class="fa-solid fa-paper-plane text-[10px]"></i> Send Email
                                            </a>
                                            <button type="button" class="px-3 py-1.5 text-xs text-slate-600 bg-white border border-slate-200 rounded-md hover:bg-slate-50" data-bs-dismiss="modal">Close</button>
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
