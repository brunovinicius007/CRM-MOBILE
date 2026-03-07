<?php
/**
 * dashboard.php - O Centro de Comando do Sistema
 * Esta é a tela principal, onde a mágica da análise financeira acontece.
 */
require_once 'src/auth.php';
requireAuth(); // Garante que o usuário está logado
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Minhas Finanças - Dashboard</title>
    <!-- Bibliotecas Externas: Tailwind para Estilo, Chart.js para Gráficos -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="manifest" href="manifest.json">
    <script>
        // Configuração personalizada do tema Tailwind
        tailwind.config = {
            darkMode: 'class', // Permite alternar entre modo claro e escuro via classe CSS
            theme: {
                extend: {
                    colors: { primary: '#3b82f6', secondary: '#10b981', danger: '#ef4444', dark: '#1f2937', darker: '#111827', warning: '#f59e0b' }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-100 dark:bg-darker text-gray-800 dark:text-gray-100 pb-24 transition-colors duration-300">

    <header class="bg-white dark:bg-dark shadow-sm sticky top-0 z-10 p-4 flex justify-between items-center">
        <div class="flex items-center gap-2">
            <img src="assets/logo.svg" alt="Logo" class="w-8 h-8">
            <h1 class="font-bold text-xl tracking-tight">Minhas Finanças</h1>
        </div>
        <div class="flex items-center gap-2">
            <!-- Botão de Tema (Sol/Lua) - Só aparece no celular -->
            <button id="themeToggle" onclick="toggleTheme()" class="hidden p-2 rounded-xl bg-gray-100 dark:bg-gray-800 text-gray-500 dark:text-yellow-400 transition-all">
                <svg id="moonIcon" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" /></svg>
                <svg id="sunIcon" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M12 3v1m0 18v1m9-11h-1M3 12H2m15.364-6.364l-.707.707M6.343 17.657l-.707.707m12.728 0l-.707-.707M6.343 6.343l-.707-.707M12 5a7 7 0 100 14 7 7 0 000-14z" /></svg>
            </button>
            <button onclick="logout()" class="p-2 text-gray-400 hover:text-danger">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M17 16l4-4m0 0l4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
            </button>
        </div>
    </header>

    <main class="p-4 max-w-2xl mx-auto space-y-6">
        
        <!-- Boas-vindas e Navegação de Mês -->
        <div class="flex flex-col space-y-4">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white leading-tight">Olá, <?php echo explode(' ', $_SESSION['nome'])[0]; ?>! 👋</h2>
            
            <div class="flex items-center justify-between bg-white dark:bg-dark p-2 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-800">
                <button onclick="changeMonth(-1)" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-xl text-primary transition-all">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
                </button>
                <div class="flex flex-col items-center">
                    <input type="month" id="dashDate" class="bg-transparent border-none font-black text-gray-800 dark:text-white outline-none text-center cursor-pointer text-sm" onchange="loadDashboardData()">
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest" id="currentMonthName">Carregando...</p>
                </div>
                <button onclick="changeMonth(1)" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-xl text-primary transition-all">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                </button>
            </div>
        </div>

        <!-- Cartão de Saldo Dinâmico (Muda de cor conforme saúde financeira) -->
        <div id="balanceCard" class="bg-gradient-to-br from-primary to-blue-700 rounded-3xl p-8 text-white shadow-2xl shadow-blue-500/20 relative overflow-hidden transition-all duration-500">
            <div class="relative z-10">
                <p class="text-blue-100 text-sm font-medium mb-1 opacity-80 uppercase tracking-wider">Saldo do Mês</p>
                <h3 class="text-4xl font-black mb-4 tracking-tighter" id="totalSaldo">R$ 0,00</h3>
                <div id="statusBadge"></div>
            </div>
            
            <!-- Resumo Rápido dentro do Card -->
            <div class="grid grid-cols-2 gap-4 mt-6 pt-6 border-t border-white/10 relative z-10">
                <div>
                    <p class="text-[9px] uppercase font-black text-blue-200 opacity-70 mb-1">Ganhos</p>
                    <p class="text-sm font-bold" id="totalReceitas">R$ 0,00</p>
                </div>
                <div>
                    <p class="text-[9px] uppercase font-black text-blue-200 opacity-70 mb-1">Gastos</p>
                    <p class="text-sm font-bold" id="totalDespesas">R$ 0,00</p>
                </div>
            </div>

            <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-white/10 rounded-full blur-3xl"></div>
        </div>

        <!-- Indicador de Segurança (Dias de Vida) -->
        <div id="survivalCard" class="bg-white dark:bg-dark p-6 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-800 hidden transition-all duration-300">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xs font-black uppercase text-gray-400 tracking-widest">Segurança Financeira 🛡️</h3>
                <span id="daysBadge" class="px-2 py-1 bg-primary/10 text-primary text-[10px] font-black rounded-lg">CALCULANDO...</span>
            </div>
            <div class="flex items-end gap-2">
                <span id="survivalDays" class="text-5xl font-black text-primary leading-none">0</span>
                <span class="text-lg font-black text-gray-400 uppercase tracking-tighter">dias</span>
            </div>
            <p class="text-[10px] text-gray-500 mt-2">de sobrevida com base nos seus gastos reais.</p>
        </div>

        <!-- Botões de Relatório -->
        <div class="grid grid-cols-2 gap-3">
            <button onclick="shareWhatsApp()" class="py-4 bg-[#25D366] hover:bg-[#128C7E] text-white rounded-2xl font-black uppercase tracking-widest text-[10px] flex items-center justify-center gap-2 shadow-lg shadow-green-500/20 transition-all active:scale-95">Resumo Whats</button>
            <button onclick="openDetailedReport()" class="py-4 bg-primary hover:bg-blue-700 text-white rounded-2xl font-black uppercase tracking-widest text-[10px] flex items-center justify-center gap-2 shadow-lg shadow-blue-500/20 transition-all active:scale-95">Relatório PDF</button>
        </div>

        <!-- Seção de Notícias em Tempo Real -->
        <div class="bg-white dark:bg-dark p-6 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-800 transition-all duration-300">
            <h3 class="text-xs font-black uppercase text-gray-400 tracking-widest mb-4 flex items-center gap-2">
                <span class="relative flex h-2 w-2"><span class="animate-ping absolute h-full w-full rounded-full bg-red-400 opacity-75"></span><span class="relative rounded-full h-2 w-2 bg-red-500"></span></span>
                Economia em Tempo Real
            </h3>
            <div id="newsContainer" class="space-y-4"></div>
        </div>

        <!-- Card de Progresso de Metas -->
        <div id="dashMetaCard" class="bg-white dark:bg-dark p-6 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-800 hidden transition-all duration-300">
            <div class="flex justify-between items-center mb-3">
                <h3 class="text-xs font-black uppercase text-gray-400 tracking-widest">Foco no Sonho 🎯</h3>
                <a href="metas.php" class="text-[10px] font-black text-primary uppercase">Ver todas</a>
            </div>
            <div id="dashMetaData"></div>
        </div>

        <!-- Insights e IA de Investimentos -->
        <div id="insightsSection" class="bg-white dark:bg-dark p-6 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-800 hidden transition-all duration-300">
            <h3 class="font-black text-lg mb-4 flex items-center gap-2 text-gray-800 dark:text-white uppercase tracking-tighter">💡 Assistente de Investimentos</h3>
            <div id="insightsContainer" class="space-y-4"></div>
        </div>

        <!-- Gráfico Principal -->
        <div class="bg-white dark:bg-dark p-6 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-800 transition-all duration-300">
            <div class="flex justify-between items-center mb-6">
                <h3 class="font-bold text-lg text-gray-900 dark:text-white leading-none">Visão Geral</h3>
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Fluxo Mensal</span>
            </div>
            <div class="h-56 relative flex justify-center items-center">
                <canvas id="financeChart"></canvas>
                <div class="absolute flex flex-col items-center pointer-events-none text-center">
                    <span class="text-[10px] text-gray-400 font-bold uppercase">Poupança</span>
                    <span class="text-lg font-black" id="percEconomia">0%</span>
                </div>
            </div>
        </div>

        <!-- BOTÕES DE AÇÃO RÁPIDA PADRONIZADOS -->
        <div class="grid grid-cols-2 gap-4 pb-4">
             <a href="receita-add.php" class="flex flex-col items-center justify-center p-6 bg-secondary text-white rounded-2xl shadow-lg shadow-green-500/20 hover:scale-[1.02] transition-all active:scale-95">
                <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center mb-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
                <span class="text-[10px] font-black uppercase tracking-widest text-white">Nova Receita</span>
             </a>
             <a href="despesa-add.php" class="flex flex-col items-center justify-center p-6 bg-danger text-white rounded-2xl shadow-lg shadow-red-500/20 hover:scale-[1.02] transition-all active:scale-95">
                <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center mb-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
                <span class="text-[10px] font-black uppercase tracking-widest text-white">Nova Despesa</span>
             </a>
        </div>

        <!-- Central de Dados (Export/Import) -->
        <div class="bg-white dark:bg-dark p-6 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-800 transition-all duration-300">
            <h3 class="text-xs font-black uppercase text-gray-400 tracking-widest mb-4 flex items-center gap-2">
                <span>📁</span> Central de Dados
            </h3>
            <div class="grid grid-cols-1 gap-3">
                <a href="api/export_backup.php" class="p-4 bg-gray-50 dark:bg-gray-800 rounded-2xl flex items-center justify-between group hover:bg-primary transition-all">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-blue-50 dark:bg-blue-900/30 text-primary group-hover:bg-white group-hover:text-primary rounded-xl flex items-center justify-center font-bold text-xl transition-all">📥</div>
                        <p class="font-black text-gray-800 dark:text-white group-hover:text-white text-xs uppercase tracking-widest">Exportar Backup</p>
                    </div>
                </a>
                <button onclick="document.getElementById('ofxInput').click()" class="p-4 bg-gray-50 dark:bg-gray-800 rounded-2xl flex items-center justify-between group hover:bg-secondary transition-all text-left">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-green-50 dark:bg-green-900/30 text-secondary group-hover:bg-white group-hover:text-secondary rounded-xl flex items-center justify-center font-bold text-xl transition-all">📄</div>
                        <p class="font-black text-gray-800 dark:text-white group-hover:text-white text-xs uppercase tracking-widest">Importar OFX</p>
                    </div>
                </button>
                <input type="file" id="ofxInput" accept=".ofx" class="hidden" onchange="uploadOfx(this)">
            </div>
        </div>

    </main>

    <!-- Modal Guia do Investidor -->
    <div id="eduModal" class="fixed inset-0 bg-black/50 hidden z-50 flex items-center justify-center p-4 backdrop-blur-sm transition-all">
        <div class="bg-white dark:bg-dark w-full max-w-md rounded-3xl p-8 shadow-2xl overflow-y-auto max-h-[80vh]">
            <h3 class="text-2xl font-black mb-6 uppercase tracking-tighter text-primary">Guia do Investidor 📈</h3>
            <div class="space-y-6 text-sm">
                <div><p class="font-black text-secondary uppercase text-[10px] mb-1">🛡️ Reserva de Emergência</p><p class="text-gray-600 dark:text-gray-400">Dinheiro para imprevistos. Sugestão: Tesouro Selic ou CDB Liquidez Diária.</p></div>
                <div><p class="font-black text-primary uppercase text-[10px] mb-1">📈 Renda Fixa</p><p class="text-gray-600 dark:text-gray-400">Empréstimos seguros para bancos/governo. Sugestão: LCI, LCA e IPCA+.</p></div>
                <div><p class="font-black text-purple-500 uppercase text-[10px] mb-1">🚀 Renda Variável</p><p class="text-gray-600 dark:text-gray-400">Ações e Fundos Imobiliários (FIIs). Foque no longo prazo!</p></div>
            </div>
            <button onclick="closeEduModal()" class="w-full mt-8 py-4 bg-gray-100 dark:bg-gray-800 rounded-2xl font-black text-xs uppercase tracking-widest">Entendi!</button>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="fixed bottom-0 w-full bg-white dark:bg-dark border-t border-gray-100 dark:border-gray-800 pb-safe pt-2 px-6 flex justify-between z-40 transition-colors duration-300">
        <a href="dashboard.php" class="flex flex-col items-center p-2 text-primary">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 20 20" fill="currentColor"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" /></svg>
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
        <?php if(isAdmin()): ?>
        <a href="usuarios.php" class="flex flex-col items-center p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
            <span class="text-[10px] font-black mt-1 uppercase">Usuários</span>
        </a>
        <?php endif; ?>
    </nav>

    <script>
        /**
         * LÓGICA DE TEMA (DARK MODE)
         */
        const isMobile = /iPhone|iPad|iPod|Android/i.test(navigator.userAgent);
        const themeBtn = document.getElementById('themeToggle');
        if (isMobile) {
            themeBtn.classList.remove('hidden'); // Ativa botão de tema apenas se for celular
            if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
                updateThemeIcons(true);
            }
        }
        function toggleTheme() {
            const isDark = document.documentElement.classList.toggle('dark');
            localStorage.theme = isDark ? 'dark' : 'light';
            updateThemeIcons(isDark);
            loadDashboardData(); // Redesenha o gráfico com as cores do novo tema
        }
        function updateThemeIcons(isDark) {
            document.getElementById('moonIcon').classList.toggle('hidden', isDark);
            document.getElementById('sunIcon').classList.toggle('hidden', !isDark);
        }

        /**
         * LÓGICA DE NAVEGAÇÃO MENSAL
         */
        function changeMonth(delta) {
            const dateInput = document.getElementById('dashDate');
            const [year, month] = dateInput.value.split('-').map(Number);
            const date = new Date(year, month - 1 + delta, 1);
            dateInput.value = `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, '0')}`;
            loadDashboardData();
        }

        /**
         * CARREGAMENTO E PROCESSAMENTO DE DADOS (O CÉREBRO DA TELA)
         */
        let financeChart = null;
        const now = new Date();
        document.getElementById('dashDate').value = `${now.getFullYear()}-${String(now.getMonth() + 1).padStart(2, '0')}`;

        async function loadDashboardData() {
            const dateFiltro = document.getElementById('dashDate').value;
            const [year, month] = dateFiltro.split('-');
            const monthName = new Date(year, month - 1).toLocaleString('pt-BR', { month: 'long', year: 'numeric' });
            document.getElementById('currentMonthName').innerText = monthName;

            try {
                // Busca todos os dados necessários em paralelo
                const [rRes, dRes, mRes, nRes] = await Promise.all([
                    fetch(`api/list_receitas.php?data=${dateFiltro}`),
                    fetch(`api/list_despesas.php?data=${dateFiltro}`),
                    fetch(`api/list_metas.php`),
                    fetch(`api/get_news.php`)
                ]);
                
                let receitas = await rRes.json();
                let despesas = await dRes.json();
                let metas = await mRes.json();
                let news = await nRes.json();

                // Garante que os dados sejam arrays
                if (!Array.isArray(receitas)) receitas = [];
                if (!Array.isArray(despesas)) despesas = [];
                if (!Array.isArray(metas)) metas = [];
                if (!Array.isArray(news)) news = [];

                // Renderiza as notícias
                const newsContainer = document.getElementById('newsContainer');
                newsContainer.innerHTML = news.length > 0 ? news.map(n => `
                    <a href="${n.link}" target="_blank" class="block group">
                        <p class="text-[10px] font-black text-primary uppercase mb-1">${n.pubDate}</p>
                        <p class="text-xs font-bold text-gray-700 dark:text-gray-200 group-hover:text-primary transition-all">${n.title}</p>
                    </a>
                `).join('<hr class="border-gray-50 dark:border-gray-800">') : '<p class="text-xs text-gray-400">Sem notícias no momento.</p>';

                // Calcula os totais financeiros
                const totalR = receitas.reduce((acc, item) => acc + (parseFloat(String(item.valor).replace(',', '.')) || 0), 0);
                const totalD = despesas.reduce((acc, item) => acc + (parseFloat(String(item.valor).replace(',', '.')) || 0), 0);
                const saldo = totalR - totalD;

                document.getElementById('totalReceitas').innerText = `R$ ${totalR.toLocaleString('pt-BR', {minimumFractionDigits: 2})}`;
                document.getElementById('totalDespesas').innerText = `R$ ${totalD.toLocaleString('pt-BR', {minimumFractionDigits: 2})}`;
                document.getElementById('totalSaldo').innerText = `R$ ${saldo.toLocaleString('pt-BR', {minimumFractionDigits: 2})}`;

                // Meta em destaque
                const metaCard = document.getElementById('dashMetaCard');
                if (metas.length > 0) {
                    const meta = metas[0]; 
                    const prog = Math.min(meta.progresso, 100).toFixed(0);
                    metaCard.classList.remove('hidden');
                    document.getElementById('dashMetaData').innerHTML = `
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm font-black text-gray-700 dark:text-gray-200">${meta.titulo}</span>
                            <span class="text-xs font-black text-primary">${prog}%</span>
                        </div>
                        <div class="w-full h-2 bg-gray-100 dark:bg-gray-800 rounded-full overflow-hidden">
                            <div class="h-full bg-primary transition-all duration-1000" style="width: ${prog}%"></div>
                        </div>`;
                } else {
                    metaCard.classList.add('hidden');
                }

                // Dias de Vida
                const totalPoupado = metas.reduce((acc, m) => acc + (parseFloat(m.valor_poupado) || 0), 0);
                const survivalCard = document.getElementById('survivalCard');
                if (totalPoupado > 0 && totalD > 0) {
                    const days = Math.floor(totalPoupado / (totalD / 30)); 
                    survivalCard.classList.remove('hidden');
                    document.getElementById('survivalDays').innerText = days;
                    const badge = document.getElementById('daysBadge');
                    if (days > 180) { badge.innerText = "NÍVEL: INABALÁVEL 💎"; badge.className = "px-2 py-1 bg-green-500/10 text-green-500 text-[10px] font-black rounded-lg"; }
                    else if (days > 90) { badge.innerText = "NÍVEL: SEGURO ✅"; badge.className = "px-2 py-1 bg-blue-500/10 text-blue-500 text-[10px] font-black rounded-lg"; }
                    else { badge.innerText = "NÍVEL: ALERTA ⚠️"; badge.className = "px-2 py-1 bg-orange-500/10 text-orange-500 text-[10px] font-black rounded-lg"; }
                } else {
                    survivalCard.classList.add('hidden');
                }

                // Status do Card de Saldo
                const badge = document.getElementById('statusBadge'), card = document.getElementById('balanceCard'), comp = totalR > 0 ? (totalD / totalR) * 100 : (totalD > 0 ? 101 : 0);
                let statusHTML = '', cardClass = 'from-primary to-blue-700';
                if (comp > 100) { statusHTML = 'Perigo: No Vermelho! 🚨'; cardClass = 'from-red-600 to-red-900'; }
                else if (comp > 85) { statusHTML = 'Crítico: Quase sem saldo! ⚠️'; cardClass = 'from-orange-500 to-orange-700'; }
                else if (totalR === 0 && totalD === 0) { statusHTML = 'Aguardando Lançamentos 📊'; cardClass = 'from-gray-500 to-gray-700'; }
                else if (comp > 50) { statusHTML = 'Saúde Financeira Boa ✅'; cardClass = 'from-blue-500 to-blue-700'; }
                else { statusHTML = 'Saúde Financeira Excelente! 💎'; cardClass = 'from-green-500 to-green-700'; }
                badge.innerHTML = `<span class="px-3 py-1 bg-white/20 rounded-full text-[10px] font-black uppercase tracking-widest border border-white/20">${statusHTML}</span>`;
                card.className = `bg-gradient-to-br ${cardClass} rounded-3xl p-8 text-white shadow-2xl relative overflow-hidden transition-all duration-500`;

                // Porcentagem de Economia
                const perc = totalR > 0 ? Math.round(((totalR - totalD) / totalR) * 100) : 0;
                const percEl = document.getElementById('percEconomia');
                percEl.innerText = `${perc}%`;
                percEl.className = perc >= 0 ? 'text-lg font-black text-secondary' : 'text-lg font-black text-danger';

                // Gráfico de Rosca
                const ctx = document.getElementById('financeChart').getContext('2d');
                if (financeChart) financeChart.destroy();
                const isDark = document.documentElement.classList.contains('dark');
                
                // Se não houver dados, mostra um gráfico cinza
                const hasData = totalR > 0 || totalD > 0;
                const chartData = hasData ? [totalR, totalD] : [1, 0];
                const chartColors = hasData ? ['#10b981', '#ef4444'] : [isDark ? '#374151' : '#e5e7eb', '#e5e7eb'];

                financeChart = new Chart(ctx, {
                    type: 'doughnut',
                    data: { 
                        labels: ['Receitas', 'Despesas'], 
                        datasets: [{ 
                            data: chartData, 
                            backgroundColor: chartColors, 
                            borderWidth: isDark ? 2 : 0, 
                            borderColor: '#1f2937' 
                        }] 
                    },
                    options: { 
                        responsive: true, 
                        maintainAspectRatio: false, 
                        cutout: '80%', 
                        plugins: { legend: { display: false } },
                        animation: { duration: 1000 }
                    }
                });
                
                generateInsights(despesas, totalD, totalR);

            } catch (err) {
                console.error("Erro ao carregar dashboard:", err);
            }
        }

        /**
         * MOTOR DE INSIGHTS E IA DE INVESTIMENTOS
         */
        function generateInsights(despesas, totalD, totalR) {
            const section = document.getElementById('insightsSection'), container = document.getElementById('insightsContainer');
            if (despesas.length === 0 && totalR === 0) { section.classList.add('hidden'); return; }
            section.classList.remove('hidden'); container.innerHTML = '';
            
            // Analisa maior categoria de gastos
            const categories = {}; despesas.forEach(d => { categories[d.categoria] = (categories[d.categoria] || 0) + parseFloat(d.valor); });
            let highestCat = '', highestVal = 0; for (let cat in categories) { if (categories[cat] > highestVal) { highestVal = categories[cat]; highestCat = cat; } }
            
            const comp = totalR > 0 ? (totalD / totalR) * 100 : 0, saldo = totalR - totalD;
            let insightsHTML = "";
            
            // Dica de Gastos (Foco em redução)
            if (despesas.length > 0) {
                let tip = comp > 85 ? "URGENTE: Você comprometeu quase toda sua renda!" : (highestCat === 'Alimentação' ? "Reduza delivery e cozinhe mais em casa." : `Você está gastando muito em ${highestCat}.`);
                insightsHTML += `<div class="p-4 rounded-2xl border-l-4 ${comp > 85 ? 'text-orange-600 bg-orange-50' : 'text-blue-600 bg-blue-50'} dark:bg-blue-900/20 font-medium text-sm animate-pulse-slow">${tip}</div>`;
            }
            
            // IA de Investimentos (Sugestão de divisão de saldo)
            if (saldo > 0) {
                insightsHTML += `<div class="mt-4 p-5 rounded-3xl border-l-4 border-secondary bg-green-50 dark:bg-green-900/20 transition-all"><div class="flex justify-between items-start mb-3"><p class="text-[10px] font-black uppercase text-secondary">🎯 Sugestão de Divisão:</p><button onclick="openEduModal()" class="text-[9px] font-black text-white bg-secondary px-2 py-1 rounded-lg uppercase">Onde Investir?</button></div><div class="space-y-2 text-[11px] font-bold"><div class="flex justify-between"><span>🛡️ Reserva (50%)</span><span class="text-secondary font-black">R$ ${(saldo*0.5).toFixed(2)}</span></div><div class="flex justify-between"><span>📈 Renda Fixa (30%)</span><span class="text-secondary font-black">R$ ${(saldo*0.3).toFixed(2)}</span></div><div class="flex justify-between"><span>🚀 Variável (20%)</span><span class="text-secondary font-black">R$ ${(saldo*0.2).toFixed(2)}</span></div></div></div>`;
            }
            container.innerHTML = insightsHTML;
        }

        // Funções do Modal de Educação
        function openEduModal() { document.getElementById('eduModal').classList.remove('hidden'); }
        function closeEduModal() { document.getElementById('eduModal').classList.add('hidden'); }

        // WhatsApp e Relatórios
        async function shareWhatsApp() {
            const dateFiltro = document.getElementById('dashDate').value;
            const [year, month] = dateFiltro.split('-');
            const monthName = new Date(year, month - 1).toLocaleString('pt-BR', { month: 'long', year: 'numeric' });
            const [rRes, dRes] = await Promise.all([fetch(`api/list_receitas.php?data=${dateFiltro}`), fetch(`api/list_despesas.php?data=${dateFiltro}`)]);
            const receitas = await rRes.json(), despesas = await dRes.json(), totalR = receitas.reduce((acc, i) => acc + parseFloat(i.valor), 0), totalD = despesas.reduce((acc, i) => acc + parseFloat(i.valor), 0), saldo = totalR - totalD;
            let msg = `*📊 RESUMO FINANCEIRO - ${monthName.toUpperCase()}*\n\n💰 *Ganhos:* R$ ${totalR.toFixed(2)}\n💸 *Gastos:* R$ ${totalD.toFixed(2)}\n⚖️ *Saldo:* R$ ${saldo.toFixed(2)}\n\n_Enviado por Minhas Finanças App_`;
            window.open(`https://api.whatsapp.com/send?text=${encodeURIComponent(msg)}`);
        }
        function openDetailedReport() { window.location.href = `relatorio.php?month=${document.getElementById('dashDate').value}`; }
        async function logout() { if(!confirm('Deseja sair?')) return; await fetch('api/logout.php'); window.location.href = 'index.php'; }
        
        async function uploadOfx(input) {
            if (!input.files[0]) return;
            const btn = input.parentElement.querySelector('button');
            const originalText = btn.innerHTML;
            btn.innerHTML = 'Processando...';
            btn.disabled = true;

            const formData = new FormData();
            formData.append('ofx_file', input.files[0]);

            try {
                const res = await fetch('api/process_ofx.php', { method: 'POST', body: formData });
                const data = await res.json();
                alert(data.message || data.error);
                if (res.ok) loadDashboardData();
            } catch (err) {
                alert('Erro ao processar arquivo.');
            } finally {
                btn.innerHTML = originalText;
                btn.disabled = false;
                input.value = '';
            }
        }
        
        loadDashboardData();
    </script>
    <style> @keyframes pulse-slow { 0%, 100% { opacity: 1; } 50% { opacity: 0.85; } } .animate-pulse-slow { animation: pulse-slow 3s infinite; } </style>
</body>
</html>
