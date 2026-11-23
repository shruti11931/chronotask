<?php

echo "";

session_start();
include 'db.php';
// Add this to the top of index.php
include 'check_deadlines.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
function getProjectProgress($conn, $project_id)
{
    // Count total tasks
    $total_query = $conn->query("SELECT COUNT(*) as total FROM tasks WHERE project_id = $project_id");
    $total = $total_query->fetch_assoc()['total'];

    if ($total == 0)
        return 0; // Avoid division by zero

    // Count completed tasks
    $done_query = $conn->query("SELECT COUNT(*) as done FROM tasks WHERE project_id = $project_id AND status = 'Completed'");
    $done = $done_query->fetch_assoc()['done'];

    // Calculate percentage
    $progress = round(($done / $total) * 100);

    // Sync back to the projects table so other pages see it
    $conn->query("UPDATE projects SET progress = $progress WHERE id = $project_id");

    return $progress;
}

$page = $_GET['page'] ?? 'dashboard';
$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];
$user_role = $_SESSION['user_role'] ?? 'Member';

// --- DATABASE QUERIES ---
// Fetch last 5 activities
$activities = $conn->query("SELECT t.task_title, t.status, u.full_name, t.deadline 
                            FROM tasks t 
                            JOIN users u ON t.assigned_to = u.id 
                            ORDER BY t.id DESC LIMIT 5");
// Get counts for the Stats Cards

// Stats for Header
// Calculates site-wide average based on actual task completion
$siteStats = $conn->query("SELECT COUNT(*) as t_total, SUM(CASE WHEN status='Completed' THEN 1 ELSE 0 END) as t_comp FROM tasks")->fetch_assoc();
$averageProgress = ($siteStats['t_total'] > 0) ? round(($siteStats['t_comp'] / $siteStats['t_total']) * 100) : 0;

// --- DATABASE QUERIES ---
$total_projects = $conn->query("SELECT COUNT(*) as count FROM projects")->fetch_assoc()['count'];
$active_tasks = $conn->query("SELECT COUNT(*) as count FROM tasks WHERE status != 'Completed'")->fetch_assoc()['count'];

// REPLACED: Live sum for the current logged-in user
$timeData = $conn->query("SELECT SUM(hours_spent) as total FROM time_logs WHERE user_id = $user_id")->fetch_assoc();
$totalHours = $timeData['total'] ?? 0;
$total_hours = $totalHours; // Keep both variables in sync for your cards below

$completed_projects = $conn->query("SELECT COUNT(*) as count FROM projects WHERE status = 'Completed'")->fetch_assoc()['count'];
// --- ADD THIS HERE ---
// Fetch notification count for the bell icon
$notif_count_query = $conn->query("SELECT COUNT(*) as count FROM notifications WHERE is_read = 0");
$notif_count = ($notif_count_query) ? $notif_count_query->fetch_assoc()['count'] : 0;

// Fetch the 5 most recent notifications for the dropdown
// Fetch only notifications for the CURRENT user
$notifs_list = $conn->query("SELECT * FROM notifications WHERE user_id = $user_id ORDER BY id DESC LIMIT 5");// ----------------------
$timeLimit = 1300;
$timePercent = ($totalHours > 0) ? ($totalHours / $timeLimit) * 100 : 0;

// Fetch Projects with Search/Filter
// Fetch Projects with Task Counts
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$filter_status = isset($_GET['filter_status']) ? mysqli_real_escape_string($conn, $_GET['filter_status']) : '';

// We add a subquery to count total tasks and completed tasks for each project
$queryStr = "SELECT p.*, 
            (SELECT COUNT(*) FROM tasks WHERE project_id = p.id) as total_tasks,
            (SELECT COUNT(*) FROM tasks WHERE project_id = p.id AND status = 'Completed') as completed_tasks
            FROM projects p WHERE 1=1";

if ($search) {
    $queryStr .= " AND p.project_name LIKE '%$search%'";
}
if ($filter_status) {
    $queryStr .= " AND p.status = '$filter_status'";
}

$projects = $conn->query($queryStr);
$res_stats = $conn->query("SELECT SUM(estimated_time) as planned FROM tasks WHERE assigned_to = $user_id")->fetch_assoc();
$planned = $res_stats['planned'] ?? 0;
$actual = $total_hours;
// Check for Overload Notification
// --- Resource Overload Check ---
if ($planned > 0 && $actual > $planned) {
    $msg = "Resource Overload: Total hours ($actual) exceeded planned capacity ($planned).";

    // Check if we already notified this specific user about this today
    $stmt = $conn->prepare("SELECT id FROM notifications WHERE user_id = ? AND message = ? AND DATE(created_at) = CURDATE()");
    $stmt->bind_param("is", $user_id, $msg);
    $stmt->execute();
    $check = $stmt->get_result();

    if ($check->num_rows == 0) {
        $insert = $conn->prepare("INSERT INTO notifications (user_id, message) VALUES (?, ?)");
        $insert->bind_param("is", $user_id, $msg);
        $insert->execute();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Promage Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&display=swap"
        rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f3f4f6;
        }

        /* Custom Dark Mode Colors */
        .dark body {
            background-color: #121212 !important;
            color: #e2e8f0 !important;
        }

        .dark .bg-white {
            background-color: #1e1e1e !important;
            border-color: #2d2d2d !important;
        }

        .dark .text-slate-800 {
            color: #f8fafc !important;
        }

        .dark .bg-slate-50 {
            background-color: #262626 !important;
        }

        /* GLOBAL DARK MODE - Put this in your main index or header style tag */
        html.dark-mode body {
            background-color: #0f172a !important;
            color: #f1f5f9 !important;
        }

        /* Sidebar & Cards */
        html.dark-mode aside,
        html.dark-mode .bg-white,
        html.dark-mode nav {
            background-color: #1e293b !important;
            border-color: #334155 !important;
            color: #ffffff !important;
        }

        /* Headings */
        html.dark-mode h1,
        html.dark-mode h2,
        html.dark-mode h3,
        html.dark-mode p {
            color: #f8fafc !important;
        }

        /* Inputs in Dark Mode */
        html.dark-mode .form-input,
        html.dark-mode input,
        html.dark-mode textarea,
        html.dark-mode select {
            background-color: #334155 !important;
            color: #ffffff !important;
            border: 1px solid #475569 !important;
        }

        /* Active Sidebar Button */
        html.dark-mode .bg-orange-50 {
            background-color: rgba(249, 115, 22, 0.2) !important;
            color: #fb923c !important;
        }

        /* Thin dark scrollbar for the sidebar */
        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #333;
            border-radius: 10px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #444;
        }
    </style>
    <script>
        // Check local storage and apply dark mode before the page even finishes loading
        if (localStorage.getItem('theme') === 'dark') {
            document.documentElement.classList.add('dark-mode');
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body class="text-slate-800">

    <div class="flex h-screen overflow-hidden">
        <aside class="w-64 bg-[#111] p-6 flex flex-col h-full border-r border-white/5">
                <div
                    class="h-8 w-8 bg-orange-500 rounded-lg flex items-center justify-center font-bold italic shadow-lg shadow-orange-500/20">
                    P</div>
                <span class="text-xl font-bold tracking-tight">ChronoTask</span>
            </div>

            <nav class="flex-1 space-y-2 overflow-y-auto pr-2 custom-scrollbar">
                <?php
                $menu = [
                    'dashboard' => ['📊', 'Dashboard'],
                    'projects' => ['📂', 'Projects'],
                    'users' => ['👥', 'Users'],
                    'timelog' => ['🕒', 'Time log'],
                    'resource' => ['🛠️', 'Resource mgmt'],
                    'settings' => ['⚙️', 'Settings'],
                    'ai_assistant' => ['🤖', 'AI Assistant'],
                    'tasks' => ['✅', 'My Tasks'],
                    // Change 'resource' or add a new one:
                    'analytics' => ['📈', 'Analytics'],
                    'timeline' => ['📅', 'Roadmap']
                ];
                if ($user_role == 'Mentor') {
                    $menu['mentor_dashboard'] = ['👨‍🏫', 'Supervision'];
                }

                foreach ($menu as $key => $val):
                    $isActive = ($page == $key);
                    $class = $isActive ? 'bg-white text-black font-bold shadow-lg' : 'text-slate-400 hover:bg-white/5 hover:text-white';
                    ?>
                    <a href="index.php?page=<?= $key ?>"
                        class="flex items-center gap-3 p-3 rounded-xl transition-all duration-200 <?= $class ?>">
                        <span class="text-lg opacity-80"><?= $val[0] ?></span>
                        <span class="text-sm"><?= $val[1] ?></span>
                    </a>
                <?php endforeach; ?>
            </nav>

            <div class="pt-6 mt-4 border-t border-white/5 shrink-0">
                <a href="logout.php"
                    class="flex items-center gap-3 p-3 text-red-400 text-sm hover:bg-red-500/10 rounded-xl transition-colors">
                    <span>🚪</span> Log out
                </a>
            </div>
        </aside>

        <main class="flex-1 overflow-y-auto p-10 bg-[#FBFBFF] ">

            <header class="flex justify-between items-center mb-10">
                <form action="index.php" method="GET" class="relative w-96">
                    <input type="hidden" name="page" value="<?= $page ?>">
                    <input type="text" name="search" placeholder="Search for projects..."
                        class="w-full pl-10 pr-4 py-2 rounded-full border-none shadow-sm focus:ring-2 ring-orange-500 bg-white"
                        value="<?= htmlspecialchars($search) ?>">
                    <span class="absolute left-3 top-2">🔍</span>
                </form>
                <div class="flex items-center gap-6">
                    <div class="relative cursor-pointer group">
                        <span class="text-xl">🔔</span>
                        <span id="notif-badge"
                            class="absolute -top-1 -right-1 bg-red-500 text-white text-[9px] w-4 h-4 flex items-center justify-center rounded-full border-2 border-white font-bold <?= ($notif_count > 0) ? '' : 'hidden' ?>">
                            <?= $notif_count ?>
                        </span>
                        <?php if ($notif_count > 0): ?>
                            <span
                                class="absolute -top-1 -right-1 bg-red-500 text-white text-[9px] w-4 h-4 flex items-center justify-center rounded-full border-2 border-white font-bold">
                                <?= $notif_count ?>
                            </span>
                        <?php endif; ?>

                        <div
                            class="absolute right-0 mt-2 w-72 bg-white shadow-2xl rounded-[1.5rem] border border-slate-100 hidden group-hover:block z-50 p-5">
                            <h4 class="text-[10px] font-black uppercase text-slate-400 mb-4 tracking-widest">Recent
                                Notifications</h4>

                            <div class="space-y-3 max-h-60 overflow-y-auto">
                                <?php if ($notifs_list && $notifs_list->num_rows > 0):
                                    while ($n = $notifs_list->fetch_assoc()): ?>
                                        <div id="notif-item-<?= $n['id'] ?>"
                                            class="relative text-[11px] p-3 <?= $n['is_read'] ? 'bg-slate-50 text-slate-500' : 'bg-orange-50 text-slate-800 border-l-4 border-orange-500' ?> rounded-xl group/item">

                                            <?= htmlspecialchars($n['message']) ?>

                                            <button onclick="event.stopPropagation(); deleteNotification(<?= $n['id'] ?>)"
                                                class="absolute top-2 right-2 opacity-0 group-hover/item:opacity-100 transition-opacity text-slate-400 hover:text-red-500 font-bold">
                                                ✕
                                            </button>
                                        </div>
                                    <?php endwhile;
                                else: ?>
                                    <p class="text-xs text-slate-400 text-center py-4">No new alerts</p>
                                <?php endif; ?>
                            </div>
                            <button onclick="markNotificationsAsRead()"
                                class="block w-full text-center text-[10px] font-bold text-orange-500 mt-4 hover:underline cursor-pointer">
                                Mark all as read
                            </button>
                        </div>
                    </div>
                    <div
                        class="flex items-center gap-4 bg-white px-4 py-2 rounded-full shadow-sm border border-slate-50">

                    </div>
                </div>
                <div class="flex items-center gap-4 bg-white px-4 py-2 rounded-full shadow-sm border border-slate-50">
                    <img src="https://ui-avatars.com/api/?name=<?= urlencode($user_name) ?>&background=FF5722&color=fff"
                        class="h-8 w-8 rounded-full">
                    <span class="text-sm font-bold"><?= $user_name ?></span>
                </div>
            </header>

            <div class="content-area">
                <?php
                if ($page !== 'dashboard'):
                    // We add 'assign_task' to this list so it's allowed to load
                    $allowed_pages = ['projects', 'project_details', 'users', 'timelog', 'resource', 'settings', 'analytics', 'timeline', 'assign_task', 'mentor_dashboard', 'submit_task_form', 'ai_assistant', 'tasks', 'submit_project'];

                    if (in_array($page, $allowed_pages)) {
                        $file = "pages/" . $page . ".php";
                        if (file_exists($file)) {
                            include $file;
                        } else {
                            echo "<div class='bg-white p-20 rounded-[3rem] text-center'>File pages/$page.php is missing!</div>";
                        }
                    } else {
                        // Your existing "Under Construction" code stays here
                        echo "<div class='bg-white p-16 rounded-[3rem] text-center'>
                <h2 class='text-3xl font-bold'>$page is Under Construction</h2>
                <a href='index.php?page=dashboard' class='text-orange-500 font-bold'>Back</a>
              </div>";
                    }
                else: ?>
                    <div class="flex gap-4 mb-6">
                        <a href="index.php?page=dashboard"
                            class="px-4 py-2 bg-white rounded-full text-xs font-bold shadow-sm border border-slate-100 hover:bg-orange-50 transition">All
                            Projects</a>
                        <a href="index.php?page=dashboard&filter_status=Completed"
                            class="px-4 py-2 bg-white rounded-full text-xs font-bold shadow-sm border border-slate-100 hover:bg-green-50">Completed</a>
                        <a href="index.php?page=dashboard&filter_status=Ongoing"
                            class="px-4 py-2 bg-white rounded-full text-xs font-bold shadow-sm border border-slate-100 hover:bg-blue-50">Ongoing</a>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-10">
                        <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm">
                            <p class="text-slate-400 text-[10px] font-bold uppercase tracking-widest mb-1">Total Projects
                            </p>
                            <h3 class="text-2xl font-black text-slate-800"><?= $total_projects ?></h3>
                        </div>

                        <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm">
                            <p class="text-slate-400 text-[10px] font-bold uppercase tracking-widest mb-1">Active Tasks</p>
                            <h3 class="text-2xl font-black text-slate-800"><?= $active_tasks ?></h3>
                        </div>

                        <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm">
                            <p class="text-slate-400 text-[10px] font-bold uppercase tracking-widest mb-1">Hours Tracked</p>
                            <h3 class="text-2xl font-black text-orange-500"><?= number_format($total_hours, 1) ?>h</h3>
                        </div>

                        <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm">
                            <p class="text-slate-400 text-[10px] font-bold uppercase tracking-widest mb-1">Completed</p>
                            <h3 class="text-2xl font-black text-green-500"><?= $completed_projects ?></h3>
                        </div>
                    </div>

                    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden mb-10">
                        <div class="p-6 border-b font-bold text-lg">Project Summary</div>
                        <table class="w-full text-left">
                            <thead>
                                <tr class="text-slate-400 text-[11px] uppercase tracking-widest bg-slate-50">
                                    <th class="px-8 py-4">Name</th>
                                    <th class="px-8 py-4">Manager</th>
                                    <th class="px-8 py-4">Status</th>
                                    <th class="px-8 py-4">Progress</th>
                                    <th class="px-8 py-4 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50">
                                <?php while ($row = $projects->fetch_assoc()):

                                    $dynamicProgress = getProjectProgress($conn, $row['id']);
                                    ?>
                                    <tr class="hover:bg-slate-50/50 transition">
                                        <td class="px-8 py-6">
                                            <div class="flex flex-col">
                                                <span class="font-bold text-slate-800 text-base">
                                                    <?= htmlspecialchars($row['project_name']) ?>
                                                </span>

                                                <div class="flex items-center gap-2 mt-1">
                                                    <span
                                                        class="text-[10px] font-medium text-slate-400 uppercase tracking-tight">
                                                        Tasks: <span
                                                            class="text-orange-500"><?= $row['completed_tasks'] ?>/<?= $row['total_tasks'] ?>
                                                            done</span>
                                                    </span>
                                                    <span class="text-slate-300">•</span>
                                                    <span
                                                        class="text-[10px] font-medium text-slate-400 uppercase tracking-tight">
                                                        <?= isset($row['category']) ? htmlspecialchars($row['category']) : 'General' ?>
                                                    </span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-8 py-6 text-sm text-slate-500">
                                            <?= htmlspecialchars($row['manager_name']) ?>
                                        </td>
                                        <td class="px-8 py-6">
                                            <span
                                                class="px-3 py-1 rounded-full text-[10px] font-bold uppercase bg-orange-100 text-orange-600"><?= $row['status'] ?></span>
                                        </td>
                                        <td class="px-8 py-6">
                                            <div class="relative h-10 w-10">
                                                <svg viewBox="0 0 36 36" class="h-10 w-10 transform -rotate-90">
                                                    <path class="text-slate-100" stroke-width="3" stroke="currentColor"
                                                        fill="none"
                                                        d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />

                                                    <path class="text-orange-500"
                                                        stroke-dasharray="<?= $dynamicProgress ?>, 100" stroke-width="3"
                                                        stroke-linecap="round" stroke="currentColor" fill="none"
                                                        d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                                                </svg>

                                                <div
                                                    class="absolute inset-0 flex items-center justify-center text-[9px] font-bold">
                                                    <?= $dynamicProgress ?>%
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-8 py-6 text-right">
                                            <div class="flex justify-end items-center gap-3">
                                                <a href="index.php?page=project_details&id=<?= $row['id'] ?>"
                                                    class="text-xl hover:scale-110 transition-transform inline-block"
                                                    title="View Project Details">
                                                    📋
                                                </a>

                                                <?php if ($user_role == 'Leader'): ?>
                                                    <a href="delete_project.php?id=<?= $row['id'] ?>"
                                                        onclick="return confirm('Delete this project?')"
                                                        class="text-xl text-red-500 hover:scale-110 transition-transform"
                                                        title="Delete">
                                                        🗑️
                                                    </a>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-slate-50">
                        <h3 class="text-lg font-bold mb-6">Recent Activity</h3>
                        <div class="space-y-6">
                            <?php while ($act = $activities->fetch_assoc()): ?>
                                <div class="flex items-start gap-4">
                                    <div
                                        class="h-10 w-10 rounded-full bg-orange-100 flex items-center justify-center text-orange-600 font-bold text-xs shrink-0">
                                        <?= substr($act['full_name'], 0, 1) ?>
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-slate-800">
                                            <span class="text-orange-500"><?= htmlspecialchars($act['full_name']) ?></span>
                                            assigned <span class="italic">"<?= htmlspecialchars($act['task_title']) ?>"</span>
                                        </p>
                                        <p class="text-[10px] text-slate-400 font-bold uppercase mt-1">Due:
                                            <?= date('M d', strtotime($act['deadline'])) ?>
                                        </p>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <script>
        if (document.getElementById('mainGauge')) {
            const ctx = document.getElementById('mainGauge').getContext('2d');
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    datasets: [{
                        data: [<?= $averageProgress ?>, <?= 100 - $averageProgress ?>],
                        backgroundColor: ['#f97316', '#f1f5f9'],
                        borderWidth: 0, cutout: '80%', circumference: 180, rotation: 270
                    }]
                },
                options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } } }
            });
        }
    </script>
    <script>
        function checkNotifications() {
            fetch('fetch_notifications.php')
                .then(response => response.text())
                .then(count => {
                    const badge = document.getElementById('notif-badge');
                    if (count > 0) {
                        if (badge) {
                            badge.innerText = count;
                            badge.classList.remove('hidden');
                        }
                    } else {
                        if (badge) badge.classList.add('hidden');
                    }
                });
        }

        // Run every 10 seconds
        setInterval(checkNotifications, 10000);
    </script>
    <script>
        // If the mentor is on the Supervision page, refresh the task list every 30 seconds
        if (window.location.href.includes('mentor_dashboard')) {
            setInterval(function () {
                location.reload();
            }, 30000);
        }
    </script>
    <script>
        // Track time dynamically while the tab is open
        let startTime = new Date();

        function updateWorkTime() {
            const currentTime = new Date();
            const durationSeconds = Math.floor((currentTime - startTime) / 1000);

            // Send "Heartbeat" to server every minute
            fetch('ajax/track_session.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    action: 'heartbeat',
                    duration: durationSeconds
                })
            });
        }

        // Update every 60 seconds
        setInterval(updateWorkTime, 60000);

        // Final update when user closes the tab or navigates away
        window.addEventListener('beforeunload', function () {
            const finalDuration = Math.floor((new Date() - startTime) / 1000);
            navigator.sendBeacon('ajax/track_session.php', JSON.stringify({
                action: 'final_log',
                duration: finalDuration
            }));
        });
        function markNotificationsAsRead() {
            fetch('mark_all_read.php')
                .then(response => response.text())
                .then(data => {
                    if (data.trim() === 'success') {
                        // 1. Hide the red badges
                        const badges = document.querySelectorAll('#notif-badge');
                        badges.forEach(b => b.classList.add('hidden'));

                        // 2. Visually update the list items to look "read"
                        const notifItems = document.querySelectorAll('.bg-orange-50');
                        notifItems.forEach(item => {
                            item.classList.remove('bg-orange-50', 'text-slate-800', 'border-l-4', 'border-orange-500');
                            item.classList.add('bg-slate-50', 'text-slate-500');
                        });

                        console.log("Notifications cleared.");
                    }
                })
                .catch(err => console.error('Error clearing notifications:', err));
        }
        function deleteNotification(id) {
            if (!confirm('Remove this notification?')) return;

            fetch(`delete_notification.php?id=${id}`)
                .then(response => response.text())
                .then(data => {
                    if (data.trim() === 'success') {
                        // Remove the element from the UI with a fade effect
                        const element = document.getElementById(`notif-item-${id}`);
                        element.style.opacity = '0';
                        setTimeout(() => element.remove(), 300);

                        // Optional: Refresh the badge count
                        checkNotifications();
                    }
                })
                .catch(err => console.error('Error deleting notification:', err));
        }
    </script>

</body>

</html>