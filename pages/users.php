<div class="bg-white rounded-[2rem] shadow-sm p-8">
    <h3 class="text-xl font-bold mb-6">Team Directory</h3>
    <table class="w-full text-left">
        <tr class="text-slate-400 text-xs uppercase border-b">
            <th class="pb-4">Name</th>
            <th class="pb-4">Role</th>
            <th class="pb-4">Status</th>
        </tr>
        <?php 
        $res = $conn->query("SELECT * FROM users");
        while($u = $res->fetch_assoc()): ?>
        <tr class="border-b last:border-0">
            <td class="py-4 font-semibold"><?= $u['full_name'] ?></td>
            <td class="py-4 text-slate-500 font-medium">
    <?= htmlspecialchars($u['user_role'] ?? $u['role'] ?? 'Team Member') ?>
</td>
            <td class="py-4"><span class="text-green-500 text-xs font-bold">● Active</span></td>
        </tr>
        <?php endwhile; ?>
    </table>
</div>