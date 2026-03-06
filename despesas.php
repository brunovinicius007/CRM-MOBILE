<?php
require_once 'src/auth.php';
requireAuth();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Minhas Finanças - Despesas</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="manifest" href="manifest.json">
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: { colors: { primary: '#3b82f6', secondary: '#10b981', danger: '#ef4444', dark: '#1f2937', darker: '#111827', warning: '#f59e0b' } }
            }
        }
    </script>
</head>
<body class="bg-gray-100 dark:bg-darker text-gray-800 dark:text-gray-100 pb-24 transition-colors duration-300">

    <header class="bg-white dark:bg-dark shadow-sm sticky top-0 z-10 p-4 flex justify-between items-center transition-colors duration-300">
        <div class="flex items-center gap-3">
            <a href="dashboard.php" class="text-gray-500 hover:text-danger transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <h1 class="font-bold text-xl text-danger uppercase tracking-tight text-sm">Despesas</h1>
        </div>
        <div class="flex items-center gap-2">
            <!-- MODO ESCURO -->
            <button id="themeToggle" onclick="toggleTheme()" class="hidden p-2 rounded-xl bg-gray-100 dark:bg-gray-800 text-gray-500 dark:text-yellow-400">
                <svg id="moonIcon" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" /></svg>
                <svg id="sunIcon" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M12 3v1m0 18v1m9-11h-1M3 12H2m15.364-6.364l-.707.707M6.343 17.657l-.707.707m12.728 0l-.707-.707M6.343 6.343l-.707-.707M12 5a7 7 0 100 14 7 7 0 000-14z" /></svg>
            </button>
            <button onclick="openBudgetModal()" class="p-2 bg-gray-100 dark:bg-gray-800 rounded-xl text-gray-500 hover:text-primary transition-all">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>
            </button>
            <a href="despesa-add.php" class="bg-danger text-white px-3 py-2 rounded-xl text-xs font-black shadow-lg shadow-red-500/30 hover:scale-105 transition-all">+ Nova</a>
            <button onclick="logout()" class="p-2 text-gray-400 hover:text-danger">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M17 16l4-4m0 0l4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
            </button>
        </div>
    </header>

    <main class="p-4 max-w-2xl mx-auto space-y-6">
        <div id="budgetSection" class="hidden space-y-4">
            <h3 class="text-xs font-black uppercase text-gray-400 tracking-widest px-2">Meus Limites 🎯</h3>
            <div id="budgetContainer" class="grid grid-cols-1 gap-3"></div>
        </div>

        <div id="cardAnalyzer" class="bg-white dark:bg-dark p-6 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-800 hidden transition-all duration-300">
            <div class="flex items-center gap-2 mb-4"><span class="text-xl">💳</span><h3 class="font-black text-lg">Análise de Cartão</h3></div>
            <div class="space-y-4">
                <div class="flex justify-between items-end"><p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Comprometimento</p><p class="text-sm font-black" id="cardPercText">0%</p></div>
                <div class="w-full h-2 bg-gray-100 dark:bg-gray-800 rounded-full overflow-hidden"><div id="cardBar" class="h-full bg-primary transition-all duration-1000" style="width: 0%"></div></div>
                <div id="cardMessage" class="p-3 rounded-xl text-[10px] font-bold uppercase leading-relaxed mb-4"></div>
                <div class="border-t border-gray-50 dark:border-gray-800 pt-4">
                    <p class="text-[10px] font-black text-gray-400 uppercase mb-3 tracking-widest text-center">Ranking do Cartão:</p>
                    <div id="subcatRanking" class="space-y-2"></div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="bg-white dark:bg-dark p-6 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-800 flex flex-col items-center justify-center transition-colors duration-300">
                <h3 class="text-[10px] font-black text-gray-400 mb-2 uppercase tracking-widest text-center">Total no Período</h3>
                <p class="text-3xl font-black text-danger" id="totalPeriodo">R$ 0,00</p>
            </div>
            <div class="bg-white dark:bg-dark p-4 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-800 transition-colors duration-300">
                <h3 class="text-[10px] font-black mb-2 uppercase tracking-widest text-gray-400 text-center">Distribuição</h3>
                <div class="h-40 flex justify-center"><canvas id="categoryChart"></canvas></div>
            </div>
        </div>

        <div class="bg-white dark:bg-dark p-4 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-800 flex gap-2 overflow-x-auto transition-colors duration-300">
            <input type="month" id="filterData" class="px-3 py-2 rounded-xl bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-sm font-bold outline-none focus:ring-2 focus:ring-danger/20" onchange="loadItems()">
            <select id="filterCategoria" class="px-3 py-2 rounded-xl bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-sm font-bold outline-none focus:ring-2 focus:ring-danger/20" onchange="loadItems()">
                <option value="">Todas</option>
                <option value="Alimentação">Alimentação</option>
                <option value="Transporte">Transporte</option>
                <option value="Moradia">Moradia</option>
                <option value="Saúde">Saúde</option>
                <option value="Educação">Educação</option>
                <option value="Lazer">Lazer</option>
                <option value="Cartão de Crédito">Cartão de Crédito</option>
                <option value="Outros">Outros</option>
            </select>
        </div>

        <div class="space-y-3" id="itemsList"></div>
    </main>

    <!-- Modal Orçamento -->
    <div id="budgetModal" class="fixed inset-0 bg-black/50 hidden z-50 flex items-center justify-center p-4 backdrop-blur-sm">
        <div class="bg-white dark:bg-dark w-full max-w-sm rounded-3xl p-6 shadow-2xl">
            <h3 class="font-black uppercase mb-4 text-center">Definir Limite Mensal</h3>
            <div class="space-y-4">
                <div><label class="text-[10px] font-black text-gray-400 uppercase">Categoria</label><select id="budgetCat" class="w-full px-4 py-3 rounded-xl bg-gray-50 dark:bg-gray-800 border-none outline-none"><option value="Alimentação">Alimentação</option><option value="Transporte">Transporte</option><option value="Lazer">Lazer</option><option value="Moradia">Moradia</option><option value="Cartão de Crédito">Cartão de Crédito</option></select></div>
                <div><label class="text-[10px] font-black text-gray-400 uppercase">Valor Limite (R$)</label><input type="number" id="budgetVal" class="w-full px-4 py-3 rounded-xl bg-gray-50 dark:bg-gray-800 border-none outline-none font-black" placeholder="500,00"></div>
                <div class="flex gap-2 pt-4"><button onclick="closeBudgetModal()" class="flex-1 py-3 text-sm font-bold text-gray-400 uppercase">Cancelar</button><button onclick="saveBudget()" class="flex-1 py-3 bg-primary text-white rounded-xl font-black text-sm uppercase">Salvar</button></div>
            </div>
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
        <a href="despesas.php" class="flex flex-col items-center p-2 text-danger">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM7 9a1 1 0 000 2h6a1 1 0 100-2H7z" clip-rule="evenodd" /></svg>
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
            const [dRes, rRes, bRes] = await Promise.all([fetch(`api/list_despesas.php?data=${date}&categoria=${cat}`), fetch(`api/list_receitas.php?data=${date}`), fetch(`api/list_orcamentos.php`)]);
            const data = await dRes.json(), receitasData = await rRes.json(), budgets = await bRes.json();
            const list = document.getElementById('itemsList'); list.innerHTML = '';
            let total = 0, totalCartao = 0; const categoryTotals = {}, cardSubcatTotals = {};
            if (data.length === 0) { list.innerHTML = '<p class="text-center text-gray-400 mt-10 font-black text-[10px]">Sem dados.</p>'; document.getElementById('totalPeriodo').innerText = 'R$ 0,00'; updateChart({}); updateCardAnalysis(0, 0, {}); updateBudgetBars([], {}); return; }
            data.forEach(item => {
                const valor = parseFloat(item.valor); total += valor;
                if(item.categoria === 'Cartão de Crédito') { totalCartao += valor; const sc = item.subcategoria || 'Outros'; cardSubcatTotals[sc] = (cardSubcatTotals[sc] || 0) + valor; }
                categoryTotals[item.categoria] = (categoryTotals[item.categoria] || 0) + valor;
                const el = document.createElement('div');
                el.className = 'bg-white dark:bg-dark p-4 rounded-3xl shadow-sm border border-gray-50 dark:border-gray-800 flex justify-between items-center transition-colors duration-300';
                el.innerHTML = `<div class="flex items-center gap-3"><div class="w-10 h-10 rounded-2xl bg-red-50 dark:bg-red-900/20 flex items-center justify-center text-danger font-bold">${item.categoria === 'Cartão de Crédito' ? '💳' : '💸'}</div><div><p class="font-black text-gray-800 dark:text-gray-100 text-sm">${item.descricao}</p><p class="text-[10px] text-gray-400 font-bold uppercase tracking-tighter">${new Date(item.data).toLocaleDateString('pt-BR')} • ${item.categoria}</p></div></div><div class="text-right"><p class="font-black text-danger text-sm">- R$ ${valor.toFixed(2)}</p><div class="flex gap-2 justify-end mt-1"><a href="despesa-add.php?id=${item.id}" class="text-[10px] font-black text-primary uppercase">Editar</a><button onclick="deleteItem(${item.id})" class="text-[10px] font-black text-red-400 uppercase">Excluir</button></div></div>`;
                list.appendChild(el);
            });
            document.getElementById('totalPeriodo').innerText = `R$ ${total.toLocaleString('pt-BR', {minimumFractionDigits: 2})}`;
            updateChart(categoryTotals);
            updateCardAnalysis(totalCartao, receitasData.reduce((acc, item) => acc + parseFloat(item.valor), 0), cardSubcatTotals);
            updateBudgetBars(budgets, categoryTotals);
        }

        function updateBudgetBars(budgets, totals) {
            const section = document.getElementById('budgetSection'), container = document.getElementById('budgetContainer');
            if (budgets.length === 0) { section.classList.add('hidden'); return; }
            section.classList.remove('hidden'); container.innerHTML = '';
            budgets.forEach(b => {
                const gasto = totals[b.categoria] || 0, perc = Math.min((gasto / b.valor_limite) * 100, 100).toFixed(0);
                const color = perc > 90 ? 'bg-danger' : (perc > 70 ? 'bg-warning' : 'bg-secondary');
                const div = document.createElement('div');
                div.className = "bg-white dark:bg-dark p-4 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-800";
                div.innerHTML = `<div class="flex justify-between items-end mb-2"><span class="text-[11px] font-black uppercase text-gray-700 dark:text-gray-300">${b.categoria}</span><span class="text-[10px] font-black ${perc > 90 ? 'text-danger' : 'text-gray-400'}">${perc}%</span></div><div class="w-full h-1.5 bg-gray-100 dark:bg-gray-800 rounded-full overflow-hidden"><div class="h-full ${color} transition-all duration-1000" style="width: ${perc}%"></div></div>`;
                container.appendChild(div);
            });
        }

        function updateCardAnalysis(gastoCartao, rendaTotal, subcats) {
            const section = document.getElementById('cardAnalyzer');
            if (gastoCartao === 0) { section.classList.add('hidden'); return; }
            section.classList.remove('hidden');
            const percOfIncome = rendaTotal > 0 ? (gastoCartao / rendaTotal) * 100 : 100;
            document.getElementById('cardPercText').innerText = `${Math.round(percOfIncome)}% da renda`;
            document.getElementById('cardBar').style.width = `${Math.min(percOfIncome / 0.6, 100)}%`;
            const msgBox = document.getElementById('cardMessage');
            if (percOfIncome > 60) { document.getElementById('cardBar').className = "h-full bg-danger"; msgBox.className = "p-3 rounded-xl text-[10px] font-bold bg-red-50 text-red-600 border border-red-100 mt-3 uppercase"; msgBox.innerHTML = "⚠️ PERIGO! Cartão acima de 60%"; }
            else { document.getElementById('cardBar').className = "h-full bg-secondary"; msgBox.className = "p-3 rounded-xl text-[10px] font-bold bg-green-50 text-green-600 border border-green-100 mt-3 uppercase"; msgBox.innerHTML = "✅ TUDO CERTO: Gastos controlados."; }
            const rankingBox = document.getElementById('subcatRanking'); rankingBox.innerHTML = '';
            Object.entries(subcats).sort((a,b) => b[1]-a[1]).forEach(([name, val]) => {
                const item = document.createElement('div'); item.className = "flex items-center justify-between text-[11px]";
                item.innerHTML = `<div class="flex items-center gap-2"><div class="w-1.5 h-1.5 rounded-full bg-primary"></div><span class="font-bold text-gray-600 dark:text-gray-400">${name}</span></div><div class="text-right"><span class="font-black text-gray-800 dark:text-white">R$ ${val.toFixed(2)}</span></div>`;
                rankingBox.appendChild(item);
            });
        }

        function updateChart(totals) {
            const ctx = document.getElementById('categoryChart').getContext('2d');
            if (categoryChart) categoryChart.destroy();
            const isDark = document.documentElement.classList.contains('dark');
            if (Object.keys(totals).length === 0) return;
            categoryChart = new Chart(ctx, {
                type: 'doughnut',
                data: { labels: Object.keys(totals), datasets: [{ data: Object.values(totals), backgroundColor: ['#ef4444', '#f59e0b', '#3b82f6', '#10b981', '#8b5cf6', '#ec4899', '#6366f1'], borderWidth: isDark ? 2 : 0, borderColor: '#1f2937' }] },
                options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, cutout: '75%' }
            });
        }

        function openBudgetModal() { document.getElementById('budgetModal').classList.remove('hidden'); }
        function closeBudgetModal() { document.getElementById('budgetModal').classList.add('hidden'); }
        async function saveBudget() {
            const categoria = document.getElementById('budgetCat').value, valor = document.getElementById('budgetVal').value; if(!valor) return;
            const res = await fetch('api/save_orcamento.php', { method: 'POST', body: JSON.stringify({categoria, valor}) });
            if(res.ok) { closeBudgetModal(); loadItems(); }
        }
        async function deleteItem(id) { if(!confirm('Excluir?')) return; await fetch('api/delete_despesa.php', { method: 'POST', body: JSON.stringify({id}) }); loadItems(); }
        async function logout() { if(!confirm('Deseja sair?')) return; await fetch('api/logout.php'); window.location.href = 'index.php'; }
        loadItems();
    </script>
</body>
</html>
