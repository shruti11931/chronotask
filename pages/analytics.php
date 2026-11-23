<?php
// Feature 1: Progress Analytics Logic
// Fetch task counts by status for the Pie Chart

$status_query = $conn->query("SELECT status, COUNT(*) as count FROM tasks GROUP BY status");
$statuses = [];
$counts = [];

while ($row = $status_query->fetch_assoc()) {
    $statuses[] = $row['status'];
    $counts[] = $row['count'];
}

// Fetch project progress for the Bar Chart
$progress_query = $conn->query("SELECT project_name, progress FROM projects LIMIT 5");
$proj_names = [];
$proj_progress = [];

while ($row = $progress_query->fetch_assoc()) {
    $proj_names[] = $row['project_name'];
    $proj_progress[] = $row['progress'];
}
$highest_val = 0;
$lowest_val = 100;
$best_project = "N/A";
$worst_project = "N/A";

foreach ($proj_names as $index => $name) {
    if ($proj_progress[$index] >= $highest_val) {
        $highest_val = $proj_progress[$index];
        $best_project = $name;
    }
    if ($proj_progress[$index] <= $lowest_val) {
        $lowest_val = $proj_progress[$index];
        $worst_project = $name;
    }
}

?>

<style>
    .analytics-card {
        background: white;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        border: 1px solid rgba(226, 232, 240, 0.8);
    }
    .analytics-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.05);
    }
    .chart-label {
        letter-spacing: 0.05em;
        color: #64748b;
        font-weight: 700;
    }
    .pulse-container {
    display: flex;
    align-items: center;
    gap: 8px;
    background: rgba(16, 185, 129, 0.1);
    padding: 4px 12px;
    border-radius: 99px;
}

.pulse-dot {
    width: 8px;
    height: 8px;
    background-color: #10b981;
    border-radius: 50%;
    box-shadow: 0 0 0 rgba(16, 185, 129, 0.4);
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
    70% { transform: scale(1); box-shadow: 0 0 0 10px rgba(16, 185, 129, 0); }
    100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
}
.bg-green-50 { background-color: rgba(16, 185, 129, 0.08); }
.bg-orange-50 { background-color: rgba(249, 115, 22, 0.08); }

/* Soft entrance animation for the summary row */
.analytics-card .grid {
    animation: fadeInUp 0.8s ease backwards;
    animation-delay: 0.3s;
}

@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}
/* Makes the doughnut appear slightly recessed for depth */
#statusPieChart {
    filter: drop-shadow(0px 10px 15px rgba(0,0,0,0.03));
}
</style>


<div class="space-y-10">
    <h2 class="text-2xl font-bold text-slate-800">Project Analytics</h2>

    <div class="analytics-card p-8 rounded-[2.5rem] relative">
    <div class="flex justify-between items-center mb-6">
        <h3 class="chart-label text-[10px] uppercase">Task Distribution</h3>
        <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">Current Sprint</span>
    </div>
    <div class="h-64 relative">
        <canvas id="statusPieChart"></canvas>
        <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
            <span class="text-2xl font-black text-slate-800"><?= array_sum($counts) ?></span>
            <span class="text-[9px] font-bold text-slate-400 uppercase">Total Tasks</span>
        </div>
    </div>
</div>

        <div class="analytics-card p-8 rounded-[2.5rem] relative">
    <div class="flex justify-between items-center mb-6">
        <h3 class="chart-label text-[10px] uppercase">Project Performance Wave</h3>
        <div class="pulse-container">
            <div class="pulse-dot"></div>
            <span class="text-[9px] font-bold text-green-600 uppercase tracking-tighter">Live Sync</span>
        </div>
    </div>
    <div class="h-64">
        <canvas id="progressBarChart"></canvas>
        <div class="mt-8 pt-6 border-t border-slate-50 grid grid-cols-2 gap-4">
    <div class="flex items-center gap-3">
        <div class="h-10 w-10 rounded-xl bg-green-50 flex items-center justify-center text-green-600">
            <span class="text-sm">🏆</span>
        </div>
        <div>
            <p class="text-[10px] uppercase font-bold text-slate-400 leading-tight">Top Performer</p>
            <p class="text-xs font-black text-slate-800"><?= htmlspecialchars($best_project) ?> (<?= $highest_val ?>%)</p>
        </div>
    </div>
    
    <div class="flex items-center gap-3">
        <div class="h-10 w-10 rounded-xl bg-orange-50 flex items-center justify-center text-orange-600">
            <span class="text-sm">⚠️</span>
        </div>
        <div>
            <p class="text-[10px] uppercase font-bold text-slate-400 leading-tight">Needs Attention</p>
            <p class="text-xs font-black text-slate-800"><?= htmlspecialchars($worst_project) ?> (<?= $lowest_val ?>%)</p>
        </div>
    </div>
</div>
    </div>
</div>
    </div>
</div>

<script>
    // Pie Chart Logic
    // Modern Doughnut Chart Logic
new Chart(document.getElementById('statusPieChart'), {
    type: 'doughnut',
    data: {
        labels: <?= json_encode($statuses) ?>,
        datasets: [{
            data: <?= json_encode($counts) ?>,
            backgroundColor: [
                '#f97316', // Orange (Delayed/Urgent)
                '#3b82f6', // Blue (In Progress)
                '#10b981', // Green (Completed)
                '#94a3b8'  // Slate (Pending)
            ],
            hoverOffset: 15,
            borderWidth: 0,
            weight: 0.5
        }]
    },
    options: {
        maintainAspectRatio: false,
        cutout: '85%', // Makes the ring thin and elegant
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    usePointStyle: true,
                    pointStyle: 'circle',
                    padding: 20,
                    font: {
                        family: 'Plus Jakarta Sans',
                        size: 11,
                        weight: '600'
                    },
                    color: '#64748b'
                }
            },
            tooltip: {
                backgroundColor: '#111',
                padding: 12,
                cornerRadius: 10,
                displayColors: true,
                usePointStyle: true
            }
        },
        // Adds rounded corners to the doughnut segments
        borderRadius: 10
    }
});

    // Bar Chart Logic
    // Wave (Area) Graph with Gradient Logic
const ctxWave = document.getElementById('progressBarChart').getContext('2d');

// Create a linear gradient (Top to Bottom)
const gradient = ctxWave.createLinearGradient(0, 0, 0, 400);
gradient.addColorStop(0, 'rgba(249, 115, 22, 0.4)');   // Vibrant orange at top
gradient.addColorStop(1, 'rgba(249, 115, 22, 0)');     // Fully transparent at bottom

new Chart(ctxWave, {
    type: 'line',
    data: {
        labels: <?= json_encode($proj_names) ?>,
        datasets: [{
            label: 'Progress',
            data: <?= json_encode($proj_progress) ?>,
            borderColor: '#f97316',
            borderWidth: 4,
            fill: true,
            backgroundColor: gradient, // Uses the gradient created above
            tension: 0.45,             // Perfect "wave" curvature
            pointBackgroundColor: '#fff',
            pointBorderColor: '#f97316',
            pointBorderWidth: 2,
            pointRadius: 6,
            pointHoverRadius: 8
        }]
    },
    options: {
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
            tooltip: {
                backgroundColor: '#1e293b',
                padding: 12,
                titleFont: { size: 14 },
                bodyFont: { size: 13 },
                displayColors: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                max: 100,
                ticks: { color: '#94a3b8', font: { size: 11 } },
                grid: { color: 'rgba(226, 232, 240, 0.5)', drawBorder: false }
            },
            x: {
                ticks: { color: '#94a3b8', font: { size: 11 } },
                grid: { display: false }
            }
        }
    }
});
</script>