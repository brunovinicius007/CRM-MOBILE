<?php
require_once 'src/auth.php';
requireAuth();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Minhas Finanças - Receitas</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="manifest" href="manifest.json">
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: { colors: { primary: '#3b82f6', secondary: '#10b981', dark: '#1f2937', darker: '#111827' } }
            }
        }
    </script>
</head>
<body class="bg-gray-100 dark:bg-darker text-gray-800 dark:text-gray-100 pb-24 transition-colors duration-300">

    <header class="bg-white dark:bg-dark shadow-sm sticky top-0 z-10 p-4 flex justify-between items-center transition-colors duration-300">
        <div class="flex items-center gap-3">
            <a href="dashboard.php" class="text-gray-500 hover:text-primary transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <h1 class="font-bold text-xl text-secondary uppercase tracking-tight text-sm">Receitas</h1>
        </div>
        <div class="flex items-center gap-2">
            <!-- MODO ESCURO -->
            <button id="themeToggle" onclick="toggleTheme()" class="hidden p-2 rounded-xl bg-gray-100 dark:bg-gray-800 text-gray-500 dark:text-yellow-400">
                <svg id="moonIcon" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" /></svg>
                <svg id="sunIcon" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M12 3v1m0 18v1m9-11h-1M3 12H2m15.364-6.364l-.707.707M6.343 17.657l-.707.707m12.728 0l-.707-.707M6.343 6.343l-.707-.707M12 5a7 7 0 100 14 7 7 0 000-14z" /></svg>
            </button>
            <a href="receita-add.php" class="bg-secondary text-white px-3 py-2 rounded-xl text-xs font-black shadow-lg shadow-green-500/30 hover:scale-105 transition-all">+ Nova</a>
            <button onclick="logout()" class="p-2 text-gray-400 hover:text-danger">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
            </button>
        </div>
    </header>

    <main class="p-4 max-w-2xl mx-auto space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="bg-white dark:bg-dark p-6 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-800 flex flex-col items-center justify-center transition-colors duration-300">
                <h3 class="text-[10px] font-black text-gray-400 mb-2 uppercase tracking-widest text-center">Total no Período</h3>
                <p class="text-3xl font-black text-secondary" id="totalPeriodo">R$ 0,00</p>
            </div>
            <div class="bg-white dark:bg-dark p-4 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-800 transition-colors duration-300">
                <h3 class="text-[10px] font-black mb-2 uppercase tracking-widest text-gray-400 text-center">Distribuição</h3>
                <div class="h-40 flex justify-center"><canvas id="categoryChart"></canvas></div>
            </div>
        </div>

        <div class="bg-white dark:bg-dark p-4 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-800 flex gap-2 overflow-x-auto transition-colors duration-300">
            <input type="month" id="filterData" class="px-3 py-2 rounded-xl bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-sm font-bold outline-none focus:ring-2 focus:ring-secondary/20" onchange="loadItems()">
            <select id="filterCategoria" class="px-3 py-2 rounded-xl bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-sm font-bold outline-none focus:ring-2 focus:ring-secondary/20" onchange="loadItems()">
                <option value="">Todas</option>
                <option value="Salário">Salário</option>
                <option value="Freelance">Freelance</option>
                <option value="Investimentos">Investimentos</option>
                <option value="Vendas">Vendas</option>
                <option value="Prêmios">Prêmios</option>
                <option value="Reembolso">Reembolso</option>
                <option value="Outros">Outros</option>
            </select>
        </div>

        <div class="space-y-3" id="itemsList"></div>
    </main>

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
        <a href="receitas.php" class="flex flex-col items-center p-2 text-secondary">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" /></svg>
            <span class="text-[10px] font-black mt-1 uppercase">Receitas</span>
        </a>
        <a href="despesas.php" class="flex flex-col items-center p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            <span class="text-[10px] font-black mt-1 uppercase">Despesas</span>
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
            loadItems();
        }
        function updateThemeIcons(isDark) {
            document.getElementById('moonIcon').classList.toggle('hidden', isDark);
            document.getElementById('sunIcon').classList.toggle('hidden', !isDark);
        }

        let categoryChart = null;
        const now = new Date();
        document.getElementById('filterData').value = `${now.getFullYear()}-${String(now.getMonth() + 1).padStart(2, '0')}`;

        async function loadItems() {
            const date = document.getElementById('filterData').value, cat = document.getElementById('filterCategoria').value;
            const res = await fetch(`api/list_receitas.php?data=${date}&categoria=${cat}`);
            const data = await res.json(), list = document.getElementById('itemsList');
            list.innerHTML = '';
            let total = 0; const categoryTotals = {};
            if (data.length === 0) { list.innerHTML = '<p class="text-center text-gray-400 mt-10 font-black text-[10px]">Sem dados.</p>'; document.getElementById('totalPeriodo').innerText = 'R$ 0,00'; updateChart({}); return; }
            data.forEach(item => {
                const valor = parseFloat(item.valor); total += valor;
                categoryTotals[item.categoria] = (categoryTotals[item.categoria] || 0) + valor;
                const el = document.createElement('div');
                el.className = 'bg-white dark:bg-dark p-4 rounded-3xl shadow-sm border border-gray-50 dark:border-gray-800 flex justify-between items-center transition-colors duration-300';
                el.innerHTML = `<div class="flex items-center gap-3"><div class="w-10 h-10 rounded-2xl bg-green-50 dark:bg-green-900/20 flex items-center justify-center text-secondary font-bold">💰</div><div><p class="font-black text-gray-800 dark:text-gray-100 text-sm">${item.descricao}</p><p class="text-[10px] text-gray-400 font-bold uppercase tracking-tighter">${new Date(item.data).toLocaleDateString('pt-BR')} • ${item.categoria}</p></div></div><div class="text-right"><p class="font-black text-secondary text-sm">+ R$ ${valor.toFixed(2)}</p><div class="flex gap-2 justify-end mt-1"><a href="receita-add.php?id=${item.id}" class="text-[10px] font-black text-primary uppercase">Editar</a><button onclick="deleteItem(${item.id})" class="text-[10px] font-black text-red-400 uppercase">Excluir</button></div></div>`;
                list.appendChild(el);
            });
            document.getElementById('totalPeriodo').innerText = `R$ ${total.toLocaleString('pt-BR', {minimumFractionDigits: 2})}`;
            updateChart(categoryTotals);
        }

        function updateChart(totals) {
            const ctx = document.getElementById('categoryChart').getContext('2d');
            if (categoryChart) categoryChart.destroy();
            const isDark = document.documentElement.classList.contains('dark');
            if (Object.keys(totals).length === 0) return;
            categoryChart = new Chart(ctx, {
                type: 'doughnut',
                data: { labels: Object.keys(totals), datasets: [{ data: Object.values(totals), backgroundColor: ['#10b981', '#3b82f6', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899', '#6366f1'], borderWidth: isDark ? 2 : 0, borderColor: '#1f2937' }] },
                options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, cutout: '75%' }
            });
        }

        async function deleteItem(id) { if(!confirm('Excluir?')) return; await fetch('api/delete_receita.php', { method: 'POST', body: JSON.stringify({id}) }); loadItems(); }
        async function logout() { if(!confirm('Deseja sair?')) return; await fetch('api/logout.php'); window.location.href = 'index.php'; }
        loadItems();
    </script>
</body>
</html>
