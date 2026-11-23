<?php
// 1. Secure the ID from the URL
$project_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// 2. Safely check for user roles - Fixed "Undefined Index" warning
$current_role = $_SESSION['role'] ?? $_SESSION['user_role'] ?? 'User';
$current_user_id = $_SESSION['user_id'] ?? $_SESSION['id'] ?? 0;

// 3. Fetch project details
$query = "SELECT * FROM projects WHERE id = $project_id";
$result = $conn->query($query);

if ($result && $result->num_rows > 0) {
    $project = $result->fetch_assoc(); 
    // This forces the detail page to show the TRUE task-based progress
    $project_progress = getProjectProgress($conn, $project_id); 
} else {
    echo "<div class='p-20 text-center font-bold text-slate-400'>Project not found in database.</div>";
    exit;
}
?>

<div class="bg-white p-10 rounded-[3rem] shadow-sm border border-slate-50 mb-10">
    
    <?php if ($current_role === 'Mentor'): ?>
        <div class="mb-8 p-6 bg-blue-50 rounded-[2rem] border border-blue-100 flex items-start gap-4">
            <span class="text-2xl">👨‍🏫</span>
            <div>
                <h4 class="text-blue-800 font-bold mb-1">Supervisor's View</h4>
                <p class="text-sm text-blue-600 leading-relaxed">
                    You are overseeing <strong><?= htmlspecialchars($project['project_name']) ?></strong>. 
                    Review final submissions in your <a href="index.php?page=mentor_dashboard" class="underline font-black">Management Tab</a>.
                </p>
            </div>
        </div>
    <?php endif; ?>

    <div class="flex justify-between items-start mb-10">
        <div>
            <h2 class="text-4xl font-black text-slate-800 tracking-tight mb-2 italic">
                <?= htmlspecialchars($project['project_name']) ?>
            </h2>
            <p class="text-slate-400 font-medium uppercase text-[10px] tracking-widest">Project ID: #<?= $project['id'] ?></p>
        </div>
        <span class="px-5 py-2 rounded-full bg-orange-100 text-orange-600 font-black text-xs uppercase tracking-widest border border-orange-200">
            <?= $project['status'] ?>
        </span>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-10 mb-12 pb-10 border-b border-slate-50">
        <div>
            <p class="text-[10px] font-black text-slate-300 uppercase tracking-[0.2em] mb-2">Project Manager</p>
            <p class="text-xl font-bold text-slate-700"><?= htmlspecialchars($project['manager_name'] ?? 'Unassigned') ?></p>
        </div>
        <div>
            <p class="text-[10px] font-black text-slate-300 uppercase tracking-[0.2em] mb-2">Completion Progress</p>
            <div class="flex items-center gap-4">
                <div class="flex-1 bg-slate-100 h-3 rounded-full overflow-hidden">
                    <div class="bg-orange-500 h-full transition-all duration-700 ease-out" style="width: <?= $project_progress ?>%"></div>
                </div>
                <p class="text-xl font-black text-orange-500"><?= $project_progress ?>%</p>
            </div>
        </div>
    </div>

    <div class="mt-10">
        <h3 class="text-xl font-black text-slate-800 mb-6 flex items-center gap-2">
            Project Tasks <span class="text-slate-300 text-sm font-medium">(Phase 1)</span>
        </h3>
        <div class="space-y-4">
            <?php
            $tasks_sql = "SELECT t.*, u.full_name as assignee_name 
                          FROM tasks t 
                          LEFT JOIN users u ON t.assigned_to = u.id 
                          WHERE t.project_id = $project_id";
            
            $tasks_query = $conn->query($tasks_sql);
            
            if ($tasks_query && $tasks_query->num_rows > 0):
                while($task = $tasks_query->fetch_assoc()): 
                    $t_status = $task['status'] ?? 'Pending';
                    $is_my_task = ($task['assigned_to'] == $current_user_id);
                    // FIX: Check if progress exists in the array before using it
                    $task_perc = isset($task['progress']) ? $task['progress'] . '%' : ($t_status == 'Completed' ? '100%' : 'In Progress');
            ?>
                <div class="flex items-center justify-between p-6 <?= $is_my_task ? 'bg-orange-50/30 border-orange-100' : 'bg-slate-50 border-transparent' ?> rounded-[2rem] border hover:border-slate-200 hover:bg-white transition-all group">
                    <div class="flex items-center gap-4">
                        <div class="w-2.5 h-2.5 rounded-full <?= $t_status == 'Completed' ? 'bg-green-500 shadow-[0_0_8px_rgba(34,197,94,0.4)]' : 'bg-blue-500 shadow-[0_0_8px_rgba(59,130,246,0.4)]' ?>"></div>
                        <div>
                            <p class="font-bold text-slate-700 group-hover:text-slate-900 transition-colors">
                                <?= htmlspecialchars($task['task_title']) ?>
                                <?php if($is_my_task): ?>
                                    <span class="ml-2 text-[8px] bg-slate-900 text-white px-2 py-0.5 rounded-md uppercase tracking-tighter">My Task</span>
                                <?php endif; ?>
                            </p>
                            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider mt-0.5">
                                <?= $t_status ?> • Assigned to: <span class="text-slate-600"><?= htmlspecialchars($task['assignee_name'] ?? 'Unassigned') ?></span>
                            </p>
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-6">
                        <span class="text-xs font-black text-slate-400"><?= $task_perc ?></span>
                        <?php if ($t_status == 'Completed'): ?>
                            <span class="w-8 h-8 flex items-center justify-center bg-green-100 text-green-600 rounded-full text-xs">✓</span>
                        <?php else: ?>
                            <span class="text-[9px] font-black py-1.5 px-4 bg-white border border-slate-200 rounded-xl text-slate-400 uppercase tracking-tight">Active</span>
                        <?php endif; ?>
                    </div>
                </div>
            <?php 
                endwhile; 
            else: 
                echo "<div class='py-12 text-center bg-slate-50/50 rounded-[2rem] border-2 border-dashed border-slate-100 text-slate-300 text-[10px] font-black uppercase tracking-widest'>No tasks defined for this project.</div>";
            endif; 
            ?>
        </div>
    </div>

    <div class="flex flex-col sm:flex-row items-center justify-between gap-6 mt-14 pt-8 border-t border-slate-50">
        <a href="index.php?page=projects" class="text-slate-400 font-black hover:text-orange-500 transition-all flex items-center gap-2 group text-sm">
            <span class="group-hover:-translate-x-1 transition-transform">←</span> Return to Projects
        </a>

        <div class="flex items-center gap-4 w-full sm:w-auto">
            <?php if ($current_role === 'Leader'): ?>
                <a href="index.php?page=assign_task&project_id=<?= $project_id ?>" 
                   class="bg-white border-2 border-slate-100 text-slate-700 px-8 py-3.5 rounded-2xl font-black hover:bg-slate-50 transition-all text-xs shadow-sm uppercase tracking-widest">
                    + Assign Step
                </a>
            <?php endif; ?>

            <?php if ($project_progress == 100 && $project['status'] !== 'Completed' && $project['status'] !== 'Pending Approval'): ?>
                <a href="index.php?page=submit_project&id=<?= $project_id ?>" 
                   class="bg-slate-900 text-white px-10 py-4 rounded-2xl font-black shadow-xl hover:bg-orange-600 hover:-translate-y-1 transition-all flex items-center gap-3 text-sm uppercase">
                    🚀 Submit Project
                </a>
            <?php elseif ($project['status'] === 'Pending Approval'): ?>
                <div class="bg-amber-50 text-amber-600 px-8 py-4 rounded-2xl font-black border border-amber-100 flex items-center gap-2 text-xs uppercase tracking-widest">
                    <span class="animate-pulse">⏳</span> Awaiting Manager
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="mt-10">
    <?php include 'pages/chat_view.php'; ?>
</div>