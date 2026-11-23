<?php
$project_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch project details to show the name
$project_query = $conn->query("SELECT project_name, description FROM projects WHERE id = $project_id");
if ($project_query && $project_query->num_rows > 0) {
    $project = $project_query->fetch_assoc();
} else {
    echo "<div class='p-10 text-center'>Project not found.</div>";
    exit;
}

if (isset($_POST['final_submit'])) {
    $notes = mysqli_real_escape_string($conn, $_POST['submission_notes']);
    
    // Update project status to 'Pending Approval' and append the final notes to description
    $update_query = "UPDATE projects SET 
                    status = 'Pending Approval', 
                    description = CONCAT(description, '\n\n--- FINAL SUBMISSION NOTES ---\n', '$notes') 
                    WHERE id = $project_id";
                    
    if ($conn->query($update_query)) {
        echo "<script>alert('Project successfully submitted for final review!'); window.location.href='index.php?page=dashboard';</script>";
    }
}
?>

<div class="max-w-2xl mx-auto mt-10">
    <a href="index.php?page=project_details&id=<?= $project_id ?>" class="text-slate-400 font-bold mb-6 inline-block hover:text-orange-500 transition-colors">
        ← Back to Project Details
    </a>

    <div class="bg-white p-12 rounded-[3.5rem] shadow-2xl shadow-slate-200/50 border border-slate-50">
        <div class="mb-10 text-center">
            <span class="text-4xl mb-4 block">🚀</span>
            <h2 class="text-3xl font-black text-slate-800 tracking-tight">Final Submission</h2>
            <p class="text-slate-400 font-medium mt-2">Ready to wrap up <span class="text-orange-500 font-bold"><?= htmlspecialchars($project['project_name']) ?></span>?</p>
        </div>

        <form method="POST" class="space-y-8">
            <div class="space-y-3">
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] ml-2">
                    Final Deliverables & Notes
                </label>
                <textarea 
                    name="submission_notes" 
                    required 
                    placeholder="Enter file links (Drive/GitHub), summary of work completed, or final messages for your manager..." 
                    class="w-full p-6 rounded-[2rem] border border-slate-100 bg-slate-50 focus:bg-white focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 outline-none h-52 transition-all text-slate-700 leading-relaxed"
                ></textarea>
            </div>

            <div class="pt-4">
                <button type="submit" name="final_submit" 
                    class="w-full py-5 bg-blue-600 text-white font-black rounded-2xl shadow-xl shadow-blue-500/40 hover:bg-blue-700 hover:-translate-y-1 active:scale-[0.98] transition-all flex items-center justify-center gap-3">
                    Confirm & Send for Approval
                </button>
                <p class="text-center text-[10px] text-slate-400 font-bold uppercase tracking-widest mt-6">
                    Note: This will notify your manager for final review
                </p>
            </div>
        </form>
    </div>
</div>