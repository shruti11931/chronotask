<?php
$task_id = $_GET['task_id'];
$project_id = $_GET['project_id'];
?>
<div class="bg-white p-10 rounded-[3rem] shadow-sm max-w-lg mx-auto">
    <h2 class="text-2xl font-bold mb-6">Submit Work for Approval</h2>
    
    <form action="process_submission.php" method="POST" enctype="multipart/form-data" class="space-y-6">
        <input type="hidden" name="task_id" value="<?= $task_id ?>">
        <input type="hidden" name="project_id" value="<?= $project_id ?>">

        <div>
            <label class="block text-xs font-bold text-slate-400 uppercase mb-2">Upload File (Screenshot/PDF)</label>
            <input type="file" name="work_file" required class="w-full p-4 bg-slate-50 rounded-2xl border-2 border-dashed border-slate-200">
        </div>

        <button type="submit" class="w-full bg-blue-600 text-white py-4 rounded-2xl font-black shadow-lg hover:bg-blue-700 transition-all">
            🚀 Send to Mentor
        </button>
    </form>
</div>