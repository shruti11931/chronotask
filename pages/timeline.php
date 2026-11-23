<div class="p-8 max-w-7xl mx-auto">
    <div class="mb-12">
        <h1 class="text-4xl font-black text-slate-900 tracking-tight mb-2">Project Roadmap 2026</h1>
        <div class="flex items-center gap-3">
            <span class="h-1 w-12 bg-orange-500 rounded-full"></span>
            <p class="text-slate-500 font-semibold uppercase text-[11px] tracking-[0.2em]">Official Sequence Defined by Team Leader</p>
        </div>
    </div>

    <div class="grid gap-10">
        <?php 
        $projects = $conn->query("SELECT * FROM projects ORDER BY id DESC");

        if ($projects && $projects->num_rows > 0):
            while($project = $projects->fetch_assoc()):
                $pid = $project['id'];
        ?>
            <div class="bg-white rounded-[2.5rem] shadow-xl shadow-slate-200/50 border border-slate-100 overflow-hidden">
                <div class="p-8 md:p-10">
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 mb-12">
                        <div>
                            <h2 class="text-3xl font-black text-slate-800 italic leading-tight mb-2">
                                <?php echo htmlspecialchars($project['project_name']); ?>
                            </h2>
                            <div class="flex items-center gap-4">
                                <span class="px-3 py-1 bg-orange-50 text-orange-600 rounded-full text-[9px] font-black uppercase tracking-widest border border-orange-100">
                                    Active Strategy
                                </span>
                                <span class="text-slate-300 font-bold text-[10px]">ID: #<?php echo $pid; ?></span>
                            </div>
                        </div>
                        
                        <div class="relative flex items-center justify-center">
                            <svg class="w-20 h-20 transform -rotate-90">
                                <circle cx="40" cy="40" r="34" stroke="currentColor" stroke-width="8" fill="transparent" class="text-slate-50" />
                                <circle cx="40" cy="40" r="34" stroke="currentColor" stroke-width="8" fill="transparent" 
                                        stroke-dasharray="213.6" 
                                        stroke-dashoffset="<?php echo 213.6 - (213.6 * $project['progress']) / 100; ?>" 
                                        class="text-slate-900 transition-all duration-1000 ease-out" />
                            </svg>
                            <span class="absolute text-lg font-black text-slate-900"><?php echo $project['progress']; ?>%</span>
                        </div>
                    </div>

                    <div class="relative pt-6 pb-4">
                        <?php 
                        $tasks = $conn->query("SELECT * FROM tasks WHERE project_id = $pid ORDER BY id ASC");
                        $step_num = 1;

                        if ($tasks && $tasks->num_rows > 0):
                        ?>
                            <div class="absolute top-14 left-8 right-8 h-[2px] bg-slate-100 hidden md:block"></div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-4 lg:grid-cols-5 gap-8 relative z-10">
                                <?php while($task = $tasks->fetch_assoc()): 
                                    $is_done = ($task['status'] === 'Completed');
                                    $is_active = ($task['status'] === 'In Progress');
                                ?>
                                    <div class="flex flex-col items-center group">
                                        <div class="w-16 h-16 rounded-[1.5rem] flex items-center justify-center font-black text-lg transition-all duration-500 border-4
                                            <?php echo $is_done ? 'bg-green-500 border-green-100 text-white shadow-lg shadow-green-200' : 
                                               ($is_active ? 'bg-orange-500 border-orange-100 text-white animate-pulse shadow-lg shadow-orange-200' : 
                                               'bg-slate-50 border-white text-slate-300'); ?>">
                                            <?php echo $is_done ? '✓' : $step_num; ?>
                                        </div>

                                        <div class="mt-6 text-center">
                                            <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Step <?php echo $step_num; ?></span>
                                            <h4 class="text-sm font-bold text-slate-800 uppercase mt-2 group-hover:text-orange-600 transition-colors">
                                                <?php echo htmlspecialchars($task['task_title']); ?>
                                            </h4>
                                            <div class="mt-3 inline-flex items-center gap-1.5">
                                                <span class="w-1.5 h-1.5 rounded-full <?php echo $is_done ? 'bg-green-500' : ($is_active ? 'bg-orange-500' : 'bg-slate-300'); ?>"></span>
                                                <span class="text-[9px] font-black uppercase <?php echo $is_done ? 'text-green-600' : ($is_active ? 'text-orange-600' : 'text-slate-400'); ?>">
                                                    <?php echo $task['status']; ?>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                <?php $step_num++; endwhile; ?>
                            </div>
                        <?php else: ?>
                            <div class="py-12 flex flex-col items-center justify-center bg-slate-50 rounded-[2rem] border-2 border-dashed border-slate-200">
                                <div class="w-12 h-12 mb-4 text-slate-300">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                </div>
                                <p class="text-[11px] font-black text-slate-400 uppercase tracking-widest">Leader has not defined steps yet</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
        <?php endif; ?>
    </div>
</div>