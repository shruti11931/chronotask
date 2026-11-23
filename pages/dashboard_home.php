<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
    <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-slate-100">
        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Projects</p>
        <p class="text-3xl font-bold text-slate-800"><?= $totalProjects ?? 0 ?> <span class="text-slate-200">/ 100</span></p>
    </div>

    <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-slate-100">
        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Global Success</p>
        <p class="text-3xl font-bold text-emerald-500">92%</p>
    </div>

    <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-slate-100 flex flex-col items-center">
        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Overall Progress</p>
        <div class="relative h-20 w-40">
             <canvas id="mainGauge"></canvas>
             <div class="absolute inset-0 flex items-end justify-center pb-1 font-bold text-lg text-slate-800">
                <?= $averageProgress ?? 0 ?>%
             </div>
        </div>
    </div>

    <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-slate-100 relative overflow-hidden">
        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Time Spent</p>
        <h4 class="text-2xl font-bold text-slate-800"><?= $totalHours ?? 0 ?> <span class="text-slate-200 text-lg">/ 1300 Hrs</span></h4>
        <div class="mt-2 text-[10px] font-bold text-orange-500">↗ <?= round($timePercent ?? 0, 1) ?>% increase</div>
    </div>
</div>

<div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
    <div class="p-8 border-b flex justify-between items-center">
        <h3 class="font-bold text-lg text-slate-800">Project Summary</h3>
    </div>
    <table class="w-full text-left">
        <thead>
            <tr class="text-slate-400 text-[11px] uppercase tracking-widest bg-slate-50">
                <th class="px-8 py-4">Project Name</th>
                <th class="px-8 py-4">Manager</th>
                <th class="px-8 py-4">Status</th>
                <th class="px-8 py-4">Progress</th>
                <th class="px-8 py-4 text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-50">
            <?php if (isset($projects) && $projects->num_rows > 0): ?>
                <?php while($row = $projects->fetch_assoc()): ?>
                <tr class="hover:bg-slate-50/50 transition">
    <td class="px-8 py-6 font-bold text-slate-700"><?= htmlspecialchars($row['project_name']) ?></td>
    <td class="px-8 py-6 text-sm text-slate-500"><?= htmlspecialchars($row['manager_name']) ?></td>
    <td class="px-8 py-6">
        <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase bg-orange-100 text-orange-600">
            <?= $row['status'] ?>
        </span>
    </td>
    <td class="px-8 py-6">
        <div class="flex items-center gap-3">
            <span class="text-xs font-bold"><?= $row['progress'] ?>%</span>
        </div>
    </td>
    <td class="px-8 py-6 text-right">
        <div class="flex justify-end gap-4">
            <a href="index.php?page=project_details&id=<?= $row['id'] ?>" title="View Info" style="text-decoration: none; cursor: pointer;">
                <span style="font-size: 20px;">📋</span>
            </a>
            <a href="delete_project.php?id=<?= $row['id'] ?>" onclick="return confirm('Delete this project?')" title="Delete" style="text-decoration: none; cursor: pointer;">
                <span style="font-size: 20px; color: #ef4444;">🗑️</span>
            </a>
        </div>
    </td>
    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" class="px-8 py-10 text-center text-slate-400">No projects found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script>
    // Initialize the Gauge Chart
    const ctx = document.getElementById('mainGauge').getContext('2d');
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            datasets: [{
                data: [<?= $averageProgress ?? 0 ?>, <?= 100 - ($averageProgress ?? 0) ?>],
                backgroundColor: ['#f97316', '#f1f5f9'],
                borderWidth: 0, cutout: '80%', circumference: 180, rotation: 270
            }]
        },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } } }
    });
</script>