<?php
require_once 'src/auth.php';
requireAuth();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Minhas Finanças - Perfil</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: { colors: { primary: '#3b82f6', secondary: '#10b981', dark: '#1f2937', darker: '#111827' } }
            }
        }
    </script>
</head>
<body class="bg-gray-100 dark:bg-darker text-gray-800 dark:text-gray-100 pb-20 transition-colors duration-300">

    <header class="bg-white dark:bg-dark shadow-sm p-4 sticky top-0 z-10 flex justify-between items-center transition-colors duration-300">
        <div class="flex items-center gap-3">
            <a href="dashboard.php" class="text-gray-500 hover:text-primary transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <h1 class="font-bold text-xl text-sm"><?php echo isAdmin() ? 'Usuários' : 'Meu Perfil'; ?></h1>
        </div>
        <div class="flex items-center gap-2">
            <!-- MODO ESCURO -->
            <button id="themeToggle" onclick="toggleTheme()" class="hidden p-2 rounded-xl bg-gray-100 dark:bg-gray-800 text-gray-500 dark:text-yellow-400">
                <svg id="moonIcon" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" /></svg>
                <svg id="sunIcon" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M12 3v1m0 18v1m9-11h-1M3 12H2m15.364-6.364l-.707.707M6.343 17.657l-.707.707m12.728 0l-.707-.707M6.343 6.343l-.707-.707M12 5a7 7 0 100 14 7 7 0 000-14z" /></svg>
            </button>
            <?php if(isAdmin()): ?>
            <button onclick="openAddModal()" class="bg-primary hover:bg-blue-600 text-white px-3 py-2 rounded-xl text-xs font-black shadow-lg transition-all">+ Novo</button>
            <?php endif; ?>
            <button onclick="logout()" class="p-2 text-gray-400 hover:text-danger">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M17 16l4-4m0 0l4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
            </button>
        </div>
    </header>

    <main class="p-4 space-y-3" id="userList">
        <p class="text-center text-gray-500 mt-10 uppercase text-xs font-black animate-pulse">Carregando...</p>
    </main>

    <div class="p-4 pt-0 pb-10">
        <h3 class="font-bold text-lg mb-4 text-gray-800 dark:text-white uppercase tracking-tighter text-sm">Central de Dados</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <a href="api/export_backup.php" class="p-5 bg-white dark:bg-dark rounded-3xl shadow-sm border border-gray-100 dark:border-gray-800 flex items-center gap-4 hover:border-primary transition-all">
                <div class="w-12 h-12 bg-blue-50 dark:bg-blue-900/20 text-primary rounded-2xl flex items-center justify-center font-black text-[10px] uppercase">Baixar</div>
                <div><p class="font-black text-gray-800 dark:text-white text-sm">Exportar Backup</p></div>
            </a>
            <button onclick="document.getElementById('ofxInput').click()" class="p-5 bg-white dark:bg-dark rounded-3xl shadow-sm border border-gray-100 dark:border-gray-800 flex items-center gap-4 hover:border-secondary transition-all text-left">
                <div class="w-12 h-12 bg-green-50 dark:bg-green-900/20 text-secondary rounded-2xl flex items-center justify-center font-bold text-xl text-sm">📄</div>
                <div><p class="font-black text-gray-800 dark:text-white text-sm">Importar OFX</p></div>
            </button>
            <input type="file" id="ofxInput" accept=".ofx" class="hidden" onchange="uploadOfx(this)">
        </div>
    </div>

    <!-- Modal Usuário -->
    <div id="userModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
        <div class="bg-white dark:bg-dark w-full max-w-md rounded-3xl shadow-2xl p-6">
            <div class="flex justify-between items-center mb-6"><h2 id="modalTitle" class="text-xl font-black uppercase text-sm">Usuário</h2><button onclick="closeModal()" class="text-gray-400 font-bold text-xl">✕</button></div>
            <form id="userForm" class="space-y-4">
                <input type="hidden" id="editId">
                <div><label class="block text-[10px] font-black uppercase text-gray-400 mb-1">Nome</label><input type="text" id="nome" class="w-full px-4 py-3 rounded-xl bg-gray-50 dark:bg-gray-800 border-none outline-none focus:ring-2 focus:ring-primary" required></div>
                <div><label class="block text-[10px] font-black uppercase text-gray-400 mb-1">E-mail</label><input type="email" id="email" class="w-full px-4 py-3 rounded-xl bg-gray-50 dark:bg-gray-800 border-none outline-none focus:ring-2 focus:ring-primary" required></div>
                <div><label class="block text-[10px] font-black uppercase text-gray-400 mb-1">Senha <span id="pwdLabel" class="text-gray-300 font-normal">(opcional)</span></label><input type="password" id="senha" class="w-full px-4 py-3 rounded-xl bg-gray-50 dark:bg-gray-800 border-none outline-none focus:ring-2 focus:ring-primary" placeholder="••••••••"></div>
                <div id="roleContainer" class="<?php echo !isAdmin() ? 'hidden' : ''; ?>"><label class="block text-[10px] font-black uppercase text-gray-400 mb-1">Nível</label><select id="role" class="w-full px-4 py-3 rounded-xl bg-gray-50 dark:bg-gray-800 border-none outline-none focus:ring-2 focus:ring-primary"><option value="user">Usuário</option><option value="admin">Administrador</option></select></div>
                <button type="submit" id="saveBtn" class="w-full bg-primary text-white font-black py-4 rounded-2xl shadow-lg hover:scale-[1.02] transition-all uppercase mt-4">Salvar</button>
            </form>
        </div>
    </div>

    <!-- Menu Inferior Padronizado -->
    <nav class="fixed bottom-0 w-full bg-white dark:bg-dark border-t border-gray-100 dark:border-gray-800 pb-safe pt-2 px-6 flex justify-between z-40 transition-colors duration-300">
        <a href="dashboard.php" class="flex flex-col items-center p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg>
            <span class="text-[10px] font-black mt-1 uppercase">Home</span>
        </a>
        <a href="metas.php" class="flex flex-col items-center p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
            <span class="text-[10px] font-black mt-1 uppercase">Metas</span>
        </a>
        <div class="w-12"></div>
        <a href="receitas.php" class="flex flex-col items-center p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            <span class="text-[10px] font-black mt-1 uppercase">Receitas</span>
        </a>
        <a href="despesas.php" class="flex flex-col items-center p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            <span class="text-[10px] font-black mt-1 uppercase">Despesas</span>
        </a>
        <a href="usuarios.php" class="flex flex-col items-center p-2 text-primary">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
            <span class="text-[10px] font-black mt-1 uppercase">Perfil</span>
        </a>
    </nav>

    <script>
        const isMobile = /iPhone|iPad|iPod|Android/i.test(navigator.userAgent);
        const themeBtn = document.getElementById('themeToggle');
        if (isMobile) {
            themeBtn.classList.remove('hidden');
            if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
                updateThemeIcons(true);
            }
        }
        function toggleTheme() {
            const isDark = document.documentElement.classList.toggle('dark');
            localStorage.theme = isDark ? 'dark' : 'light';
            updateThemeIcons(isDark);
            loadUsers();
        }
        function updateThemeIcons(isDark) {
            document.getElementById('moonIcon').classList.toggle('hidden', isDark);
            document.getElementById('sunIcon').classList.toggle('hidden', !isDark);
        }

        async function loadUsers() {
            const res = await fetch('api/list_usuarios.php'), data = await res.json();
            const list = document.getElementById('userList'); list.innerHTML = '';
            data.forEach(user => {
                const isMe = user.id == <?php echo getCurrentUserId(); ?>;
                const canEdit = <?php echo isAdmin() ? 'true' : 'isMe'; ?>;
                const el = document.createElement('div');
                el.className = 'bg-white dark:bg-dark p-4 rounded-3xl shadow-sm border border-gray-50 dark:border-gray-800 flex justify-between items-center transition-all duration-300';
                el.innerHTML = `<div class="flex items-center gap-3"><div class="w-10 h-10 rounded-2xl bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center text-primary font-black uppercase text-xs">${user.nome.charAt(0)}</div><div><p class="font-black text-gray-800 dark:text-gray-100 text-sm">${user.nome} ${isMe ? '(Você)' : ''}</p><p class="text-[10px] text-gray-400 font-bold uppercase">${user.email} • ${user.role.toUpperCase()}</p></div></div><div>${canEdit ? `<button onclick="editUser(${user.id}, '${user.nome}', '${user.email}', '${user.role}')" class="text-[10px] font-black text-primary uppercase hover:underline">Editar</button>` : ''}</div>`;
                list.appendChild(el);
            });
        }
        function editUser(id, nome, email, role) { document.getElementById('editId').value = id; document.getElementById('modalTitle').innerText = 'Editar Perfil'; document.getElementById('pwdLabel').classList.remove('hidden'); document.getElementById('nome').value = nome; document.getElementById('email').value = email; document.getElementById('role').value = role; document.getElementById('userModal').classList.remove('hidden'); }
        document.getElementById('userForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const editId = document.getElementById('editId').value, body = { id: editId || null, nome: document.getElementById('nome').value, email: document.getElementById('email').value, senha: document.getElementById('senha').value, role: document.getElementById('role').value };
            const res = await fetch(editId ? 'api/update_usuario.php' : 'api/create_usuario.php', { method: 'POST', body: JSON.stringify(body) });
            if (res.ok) { alert('Sucesso!'); closeModal(); loadUsers(); if (editId == <?php echo getCurrentUserId(); ?>) location.reload(); } else { const d = await res.json(); alert(d.error); }
        });
        async function uploadOfx(input) {
            if (!input.files[0]) return;
            const formData = new FormData(); formData.append('ofx_file', input.files[0]);
            const res = await fetch('api/process_ofx.php', { method: 'POST', body: formData }), data = await res.json();
            alert(data.message || data.error); input.value = '';
        }
        async function logout() { if(!confirm('Deseja sair?')) return; await fetch('api/logout.php'); window.location.href = 'index.php'; }
        loadUsers();
    </script>
</body>
</html>
