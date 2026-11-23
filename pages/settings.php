<?php
// Securely fetch user and project data
$user_id = 1; // Replace with $_SESSION['user_id']
$user_res = $conn->query("SELECT * FROM users WHERE id = $user_id");
$user = $user_res->fetch_assoc() ?: [];

$project_res = $conn->query("SELECT * FROM projects LIMIT 1");
$project = $project_res->fetch_assoc() ?: [];
?>

<div class="flex gap-8 h-[85vh]">
    <div class="w-72 bg-white rounded-[2.5rem] p-6 shadow-sm border border-slate-50 h-fit sticky top-0">
        <div class="space-y-2">
            <button onclick="showSection('profile')" class="nav-btn active w-full flex items-center gap-3 px-5 py-4 rounded-2xl font-bold text-[11px] uppercase tracking-wider transition-all bg-orange-50 text-orange-600">👤 Profile & Bio</button>
            <button onclick="showSection('security')" class="nav-btn w-full flex items-center gap-3 px-5 py-4 rounded-2xl font-bold text-[11px] uppercase tracking-wider transition-all text-slate-400">🔒 Account Security</button>
            <button onclick="showSection('project')" class="nav-btn w-full flex items-center gap-3 px-5 py-4 rounded-2xl font-bold text-[11px] uppercase tracking-wider transition-all text-slate-400">📊 Project Settings</button>
            <button onclick="showSection('team')" class="nav-btn w-full flex items-center gap-3 px-5 py-4 rounded-2xl font-bold text-[11px] uppercase tracking-wider transition-all text-slate-400">👥 Team Management</button>
            <button onclick="showSection('notifications')" class="nav-btn w-full flex items-center gap-3 px-5 py-4 rounded-2xl font-bold text-[11px] uppercase tracking-wider transition-all text-slate-400">🔔 Notifications</button>
            <button onclick="showSection('appearance')" class="nav-btn w-full flex items-center gap-3 px-5 py-4 rounded-2xl font-bold text-[11px] uppercase tracking-wider transition-all text-slate-400">🎨 Appearance</button>
        </div>
    </div>

    <div class="flex-1 overflow-y-auto pr-4 custom-scrollbar">
        <form action="process_settings.php" method="POST" enctype="multipart/form-data">

            <div id="sec-profile" class="settings-section space-y-6">
                <h2 class="text-xl font-bold text-slate-800">Profile & Bio</h2>
                <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm space-y-6">
                    <div class="flex items-center gap-8">
                        <div class="relative group">
                            <img src="uploads/profiles/<?= $user['profile_pix'] ?? 'default.png' ?>" id="preview" class="w-24 h-24 rounded-full object-cover border-4 border-white shadow-lg">
                            <input type="file" name="profile_img" onchange="previewImg(event)" class="absolute inset-0 opacity-0 cursor-pointer">
                        </div>
                        <div>
                            <p class="text-xs font-bold text-slate-400 uppercase mb-1">Profile Picture</p>
                            <p class="text-[10px] text-slate-400">Click image to upload new. Role: <b><?= $user['role'] ?? 'Member' ?></b></p>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <input type="text" name="username" placeholder="Full Name" value="<?= $user['username'] ?? '' ?>" class="form-input">
                        <input type="email" name="email" value="<?= $user['email'] ?? '' ?>" class="form-input" readonly>
                        <input type="text" name="phone" placeholder="Contact Number" value="<?= $user['phone'] ?? '' ?>" class="form-input">
                        <select name="role" class="form-input">
                            <option value="Member" <?= ($user['role'] ?? '') == 'Member' ? 'selected' : '' ?>>Member</option>
                            <option value="Leader" <?= ($user['role'] ?? '') == 'Leader' ? 'selected' : '' ?>>Leader</option>
                            <option value="Admin" <?= ($user['role'] ?? '') == 'Admin' ? 'selected' : '' ?>>Admin</option>
                        </select>
                    </div>
                    <input type="text" name="skills" placeholder="Skills (Tags like Java, UI, DB)" value="<?= $user['skills'] ?? '' ?>" class="form-input w-full">
                    <textarea name="bio" placeholder="About Me" class="form-input w-full h-32"><?= $user['bio'] ?? '' ?></textarea>
                    
                    <div class="flex gap-4">
                        <button type="submit" name="save_profile" class="btn-primary">Save Changes</button>
                        <button type="reset" class="btn-secondary">Cancel</button>
                    </div>
                </div>
            </div>

            <div id="sec-security" class="settings-section hidden space-y-6">
                <h2 class="text-xl font-bold text-slate-800">Account Security</h2>
                <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm space-y-6">
                    <div class="space-y-4">
                        <h3 class="text-xs font-bold text-slate-400 uppercase">Change Password</h3>
                        <input type="password" name="curr_pass" placeholder="Current Password" class="form-input w-full">
                        <input type="password" name="new_pass" placeholder="New Password" class="form-input w-full">
                        <input type="password" name="conf_pass" placeholder="Confirm Password" class="form-input w-full">
                        <button type="submit" name="update_pass" class="btn-primary !px-6">Update Password</button>
                    </div>
                    <hr class="opacity-50">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium">Enable Two-Factor Authentication</span>
                        <input type="checkbox" name="2fa" class="w-10 h-5 accent-orange-500">
                    </div>
                    <button type="button" class="text-xs font-bold text-slate-500 uppercase hover:text-orange-600 transition-colors">Logout from all devices</button>
                    <div class="p-4 bg-red-50 rounded-2xl border border-red-100">
                        <h4 class="text-xs font-bold text-red-600 uppercase mb-2">Danger Zone</h4>
                        <button type="button" class="bg-red-500 text-white px-6 py-2 rounded-xl text-[10px] font-bold uppercase hover:bg-red-600">Delete Account</button>
                    </div>
                </div>
            </div>

            <div id="sec-project" class="settings-section hidden space-y-6">
                <h2 class="text-xl font-bold text-slate-800">Project Settings</h2>
                <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm space-y-4">
                    <input type="hidden" name="project_id" value="<?= $project['id'] ?? '' ?>">
                    <input type="text" name="proj_name" value="<?= $project['name'] ?? '' ?>" class="form-input w-full font-bold" placeholder="Project Name">
                    <textarea name="proj_desc" class="form-input w-full h-24" placeholder="Project Description"><?= $project['description'] ?? '' ?></textarea>
                    <div class="grid grid-cols-2 gap-4">
                        <input type="date" name="start_date" value="<?= $project['start_date'] ?? '' ?>" class="form-input">
                        <input type="date" name="deadline" value="<?= $project['deadline'] ?? '' ?>" class="form-input">
                    </div>
                    <select name="proj_status" class="form-input w-full">
                        <option value="Active" <?= ($project['status'] ?? '') == 'Active' ? 'selected' : '' ?>>Active</option>
                        <option value="Completed" <?= ($project['status'] ?? '') == 'Completed' ? 'selected' : '' ?>>Completed</option>
                        <option value="Archived" <?= ($project['status'] ?? '') == 'Archived' ? 'selected' : '' ?>>Archived</option>
                    </select>
                    <div class="flex items-center gap-4 py-2">
                        <label class="text-xs font-bold text-slate-400 uppercase">Visibility:</label>
                        <label class="text-xs flex items-center gap-1"><input type="radio" name="vis" value="Private" checked> Private</label>
                        <label class="text-xs flex items-center gap-1"><input type="radio" name="vis" value="Public"> Public</label>
                    </div>
                    <button type="submit" name="save_project" class="btn-primary">Save Project</button>
                </div>
            </div>

            <div id="sec-team" class="settings-section hidden space-y-6">
                <h2 class="text-xl font-bold text-slate-800">Team Management</h2>
                <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm space-y-6">
                    <table class="w-full text-left text-sm">
                        <thead>
                            <tr class="text-[10px] font-bold text-slate-400 uppercase border-b border-slate-50">
                                <th class="pb-4">Name</th>
                                <th class="pb-4">Role</th>
                                <th class="pb-4">Tasks</th>
                                <th class="pb-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="border-b border-slate-50 last:border-0">
                                <td class="py-4 font-semibold">Current User</td>
                                <td class="py-4">Leader</td>
                                <td class="py-4">12</td>
                                <td class="py-4 text-orange-500 font-bold cursor-pointer uppercase text-[10px]">Remove</td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="pt-6 border-t border-slate-50">
                        <h4 class="text-xs font-bold text-slate-400 uppercase mb-3">Add Member</h4>
                        <div class="flex gap-2">
                            <input type="email" placeholder="Email Address" class="form-input flex-1">
                            <button type="button" class="btn-primary !px-6">Send Invite</button>
                        </div>
                    </div>
                </div>
            </div>

            <div id="sec-notifications" class="settings-section hidden space-y-6">
                <h2 class="text-xl font-bold text-slate-800">Notifications</h2>
                <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm space-y-4">
                    <?php 
                    $notifs = ["Email Notifications", "Task Assigned Alert", "Deadline Reminder", "Resource Upload Alert", "Daily Summary"];
                    foreach($notifs as $n): ?>
                    <div class="flex items-center justify-between p-4 bg-slate-50 rounded-2xl">
                        <span class="text-sm font-medium"><?= $n ?></span>
                        <input type="checkbox" checked class="w-10 h-5 accent-orange-500">
                    </div>
                    <?php endforeach; ?>
                    <button type="submit" class="btn-primary">Save Preferences</button>
                </div>
            </div>

            <div id="sec-appearance" class="settings-section hidden space-y-6">
                <h2 class="text-xl font-bold text-slate-800">Appearance</h2>
                <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm space-y-6">
                    <div class="flex items-center justify-between p-4 bg-slate-50 rounded-2xl">
                        <span class="text-sm font-medium">Dark Mode Theme</span>
                        <button type="button" onclick="toggleDarkMode()" id="dark-btn" class="bg-slate-200 px-6 py-2 rounded-xl text-xs font-bold uppercase">OFF</button>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-slate-400 uppercase mb-3">Accent Color</p>
                        <div class="flex gap-4">
                            <div class="w-8 h-8 rounded-full bg-orange-500 border-4 border-white shadow-sm cursor-pointer"></div>
                            <div class="w-8 h-8 rounded-full bg-blue-500 border-4 border-white shadow-sm cursor-pointer"></div>
                            <div class="w-8 h-8 rounded-full bg-purple-500 border-4 border-white shadow-sm cursor-pointer"></div>
                        </div>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium">Compact Mode</span>
                        <input type="checkbox" class="w-10 h-5 accent-orange-500">
                    </div>
                    <button type="button" class="btn-primary">Apply Theme</button>
                </div>
            </div>

        </form>
    </div>
</div>

<style>
.form-input { background: #f8fafc; border: 0; border-radius: 1.25rem; padding: 0.85rem 1.5rem; font-size: 0.875rem; width: 100%; outline: none; transition: all 0.2s; }
.form-input:focus { background: #fff; box-shadow: 0 0 0 2px #f97316; }
.btn-primary { background: #0f172a; color: white; padding: 0.85rem 2.5rem; border-radius: 1.25rem; font-size: 0.75rem; font-weight: bold; text-transform: uppercase; transition: all 0.2s; }
.btn-primary:hover { background: #f97316; transform: translateY(-2px); box-shadow: 0 4px 12px rgba(249, 115, 22, 0.2); }
.btn-secondary { background: #f1f5f9; color: #64748b; padding: 0.85rem 2.5rem; border-radius: 1.25rem; font-size: 0.75rem; font-weight: bold; text-transform: uppercase; }
.settings-section { animation: sectionFade 0.4s ease forwards; }
@keyframes sectionFade { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

/* Dark Theme Overrides */
.dark-mode { background: #121212 !important; color: #e2e8f0 !important; }
.dark-mode .bg-white { background: #1e1e1e !important; border: 1px solid #334155 !important; }
.dark-mode .text-slate-800 { color: #f8fafc !important; }
.dark-mode .form-input { background: #334155 !important; color: white !important; }
.dark-mode .bg-slate-50 { background: #334155 !important; }
</style>

<script>
function showSection(id) {
    // 1. Hide all
    document.querySelectorAll('.settings-section').forEach(sec => {
        sec.classList.add('hidden');
    });

    // 2. Show targeted section
    const target = document.getElementById('sec-' + id);
    if(target) target.classList.remove('hidden');

    // 3. Update Nav
    document.querySelectorAll('.nav-btn').forEach(btn => {
        btn.classList.remove('bg-orange-50', 'text-orange-600', 'active');
        btn.classList.add('text-slate-400');
    });

    // Handle button highlighting
    if (window.event && window.event.currentTarget) {
        window.event.currentTarget.classList.add('bg-orange-50', 'text-orange-600', 'active');
        window.event.currentTarget.classList.remove('text-slate-400');
    }
}

function toggleDarkMode() {
    // Target documentElement so the whole site (sidebar, header, etc.) can see the class
    const isDark = document.documentElement.classList.toggle('dark-mode');
    
    // Update the button text
    const btn = document.getElementById('dark-btn');
    if(btn) btn.innerText = isDark ? 'ON' : 'OFF';
    
    // Save the choice globally
    localStorage.setItem('theme', isDark ? 'dark' : 'light');
}

// Persist theme
if(localStorage.getItem('theme') === 'dark') {
    document.body.classList.add('dark-mode');
    if(document.getElementById('dark-btn')) document.getElementById('dark-btn').innerText = 'ON';
}

function previewImg(event) {
    var reader = new FileReader();
    reader.onload = function(){ document.getElementById('preview').src = reader.result; }
    reader.readAsDataURL(event.target.files[0]);
}
</script>