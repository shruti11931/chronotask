<?php
$mentor_id = $_SESSION['user_id'];
// Fetch projects this mentor is supervising
$my_projects = $conn->query("SELECT * FROM projects WHERE mentor_id = $mentor_id");
?>

<div class="space-y-8">
    <h2 class="text-3xl font-black text-slate-800">Supervision Overview</h2>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <?php while($p = $my_projects->fetch_assoc()): ?>
        <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-slate-100">
            <h3 class="font-bold text-lg mb-2"><?= htmlspecialchars($p['project_name']) ?></h3>
            <div class="w-full bg-slate-100 h-2 rounded-full mb-4">
                <div class="bg-orange-500 h-2 rounded-full" style="width: <?= $p['progress'] ?>%"></div>
            </div>
            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest"><?= $p['progress'] ?>% Completed</p>
        </div>
        <?php endwhile; ?>
    </div>

    <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 overflow-hidden">
        <div class="p-6 border-b font-bold">Tasks Awaiting Your Approval</div>
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 text-[10px] uppercase tracking-widest text-slate-400">
                    <th class="p-6">Project</th> <th class="p-6">Task</th>
                    <th class="p-6">Member</th>
                    <th class="p-6">Evidence</th>
                    <th class="p-6">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Fixed SQL: Joining with projects table to get project_name
                $sql = "SELECT t.*, u.full_name, p.project_name 
                        FROM tasks t 
                        JOIN users u ON t.assigned_to = u.id 
                        JOIN projects p ON t.project_id = p.id
                        WHERE t.status = 'Pending Approval' AND p.mentor_id = $mentor_id";
                
                $approvals = $conn->query($sql);
                
                if($approvals && $approvals->num_rows > 0):
                    while($ta = $approvals->fetch_assoc()): ?>
                    <tr class="border-t border-slate-50">
                        <td class="p-6">
                            <a href="index.php?page=project_details&id=<?= $ta['project_id'] ?>" class="text-orange-500 font-bold hover:underline">
                                <?= htmlspecialchars($ta['project_name']) ?>
                            </a>
                        </td>
                        <td class="p-6 font-bold"><?= htmlspecialchars($ta['task_title']) ?></td>
                        <td class="p-6 text-slate-500"><?= htmlspecialchars($ta['full_name']) ?></td>
                        <td class="p-6">
                            <?php if (!empty($ta['submission_file'])): ?>
                                <a href="<?= $ta['submission_file'] ?>" target="_blank" class="text-blue-500 font-bold underline flex items-center gap-2">
                                    <span>📂</span> View Work
                                </a>
                            <?php else: ?>
                                <span class="text-slate-300">No file</span>
                            <?php endif; ?>
                        </td>
                        <td class="p-6 flex gap-2">
                            <a href="approve_task.php?id=<?= $ta['id'] ?>&action=approve" class="bg-green-500 text-white px-4 py-2 rounded-xl text-xs font-bold hover:bg-green-600">Approve</a>
                            <a href="approve_task.php?id=<?= $ta['id'] ?>&action=reject" class="bg-red-500 text-white px-4 py-2 rounded-xl text-xs font-bold hover:bg-red-600">Reject</a>
                        </td>
                    </tr>
                    <?php endwhile; 
                else: ?>
                    <tr>
                        <td colspan="5" class="p-10 text-center text-slate-400 font-medium">
                            No tasks currently awaiting approval.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>