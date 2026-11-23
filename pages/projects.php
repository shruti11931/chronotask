<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
    <?php 
    $allProj = $conn->query("SELECT * FROM projects");
    while($p = $allProj->fetch_assoc()): 
        // FIX: Calculate the real-time progress using your new function
        $realProgress = getProjectProgress($conn, $p['id']); 
    ?>
    <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-slate-100 hover:shadow-xl transition-all group">
        <div class="flex justify-between items-start mb-6">
            <div class="h-12 w-12 bg-orange-100 rounded-2xl flex items-center justify-center text-xl">📂</div>
            <span class="px-3 py-1 bg-slate-100 text-[10px] font-bold rounded-full text-slate-500 uppercase"><?= $p['status'] ?></span>
        </div>
        <h4 class="text-xl font-bold text-slate-800 mb-2"><?= htmlspecialchars($p['project_name']) ?></h4>
        <p class="text-sm text-slate-400 mb-6">Managed by <?= htmlspecialchars($p['manager_name']) ?></p>
        
        <div class="space-y-2">
            <div class="flex justify-between text-xs font-bold">
                <span>Progress</span>
                <span class="text-orange-500"><?= $realProgress ?>%</span>
            </div>
            <div class="h-1.5 w-full bg-slate-100 rounded-full overflow-hidden">
                <div class="bg-orange-500 h-full transition-all duration-1000" style="width: <?= $realProgress ?>%"></div>
            </div>
        </div>
    </div>
    <?php endwhile; ?>
</div>