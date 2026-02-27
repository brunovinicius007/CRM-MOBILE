<?php
require_once 'src/auth.php';
requireAuth();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IAFinance - Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="manifest" href="manifest.json">
    <meta name="theme-color" content="#1f2937">
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: '#3b82f6',
                        secondary: '#10b981',
                        danger: '#ef4444',
                        dark: '#1f2937',
                        darker: '#111827'
                    }
                }
            }
        }
    </script>
    <!-- Simple Chart Lib (Chart.js is reliable and pretty) -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-100 dark:bg-darker text-gray-800 dark:text-gray-100 pb-20">

    <!-- Header -->
    <header class="bg-white dark:bg-dark shadow-sm sticky top-0 z-10 p-4 flex justify-between items-center">
        <div class="flex items-center gap-2">
            <img src="assets/logo.svg" alt="Logo" class="w-8 h-8">
            <h1 class="font-bold text-xl tracking-tight">IAFinance</h1>
        </div>
        <button onclick="logout()" class="text-gray-500 hover:text-danger">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
            </svg>
        </button>
    </header>

    <!-- Content -->
    <main class="p-4 max-w-2xl mx-auto space-y-6">
        
        <!-- Welcome -->
        <div>
            <h2 class="text-2xl font-bold">Olá, <span id="userName"><?php echo $_SESSION['nome']; ?></span> 👋</h2>
            <p class="text-gray-500 dark:text-gray-400 text-sm">Aqui está o resumo das suas finanças.</p>
        </div>

        <!-- Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
             <!-- Saldo -->
             <div class="bg-gradient-to-br from-primary to-blue-600 rounded-2xl p-6 text-white shadow-lg shadow-blue-500/20 transform transition-all hover:scale-[1.02]">
                <p class="text-blue-100 text-sm font-medium mb-1">Saldo Total</p>
                <h3 class="text-3xl font-bold" id="totalSaldo">R$ 0,00</h3>
                <div class="mt-4 flex items-center gap-2" id="statusBadge">
                    <!-- JS will populate -->
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="bg-white dark:bg-dark p-4 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-800">
                    <div class="w-10 h-10 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center text-green-600 mb-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12" /></svg>
                    </div>
                    <p class="text-xs text-gray-500 font-medium">Receitas</p>
                    <p class="text-lg font-bold text-secondary" id="totalReceitas">R$ 0,00</p>
                </div>
                <div class="bg-white dark:bg-dark p-4 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-800">
                    <div class="w-10 h-10 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center text-red-600 mb-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6" /></svg>
                    </div>
                    <p class="text-xs text-gray-500 font-medium">Despesas</p>
                    <p class="text-lg font-bold text-danger" id="totalDespesas">R$ 0,00</p>
                </div>
            </div>
        </div>

        <!-- Chart -->
        <div class="bg-white dark:bg-dark p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-800">
            <h3 class="font-bold mb-4">Fluxo de Caixa</h3>
            <canvas id="financeChart" height="200"></canvas>
        </div>

        <!-- Quick Actions -->
        <div class="grid grid-cols-2 gap-4">
             <a href="receitas.php" class="flex flex-col items-center justify-center p-4 bg-white dark:bg-dark rounded-xl border border-dashed border-gray-300 dark:border-gray-700 text-gray-500 hover:border-secondary hover:text-secondary transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                <span class="text-sm font-medium">Nova Receita</span>
             </a>
             <a href="despesas.php" class="flex flex-col items-center justify-center p-4 bg-white dark:bg-dark rounded-xl border border-dashed border-gray-300 dark:border-gray-700 text-gray-500 hover:border-danger hover:text-danger transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                <span class="text-sm font-medium">Nova Despesa</span>
             </a>
        </div>

    </main>

    <!-- Custom Navbar -->
    <nav class="fixed bottom-0 w-full bg-white dark:bg-dark border-t border-gray-100 dark:border-gray-800 pb-safe pt-2 px-6 flex justify-between shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.05)]">
        <a href="dashboard.php" class="flex flex-col items-center p-2 text-primary">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 20 20" fill="currentColor"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" /></svg>
            <span class="text-[10px] font-medium mt-1">Home</span>
        </a>
        <a href="receitas.php" class="flex flex-col items-center p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            <span class="text-[10px] font-medium mt-1">Receitas</span>
        </a>
        <div class="w-12"></div> <!-- Spacer for FAB if needed, or just visual balance -->
        <a href="despesas.php" class="flex flex-col items-center p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            <span class="text-[10px] font-medium mt-1">Despesas</span>
        </a>
        <?php if(isAdmin()): ?>
        <a href="usuarios.php" class="flex flex-col items-center p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
            <span class="text-[10px] font-medium mt-1">Usuários</span>
        </a>
        <?php endif; ?>
    </nav>

    <script>
        // Init
        if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) document.documentElement.classList.add('dark');

        async function loadDashboardData() {
            // Fetch Receitas
            const rRes = await fetch('api/list_receitas.php');
            const receitas = await rRes.json();
            
            // Fetch Despesas
            const dRes = await fetch('api/list_despesas.php');
            const despesas = await dRes.json();

            // Calculate Totals
            const totalR = receitas.reduce((acc, item) => acc + parseFloat(item.valor), 0);
            const totalD = despesas.reduce((acc, item) => acc + parseFloat(item.valor), 0);
            const saldo = totalR - totalD;

            // Update DOM
            document.getElementById('totalReceitas').innerText = `R$ ${totalR.toFixed(2)}`;
            document.getElementById('totalDespesas').innerText = `R$ ${totalD.toFixed(2)}`;
            document.getElementById('totalSaldo').innerText = `R$ ${saldo.toFixed(2)}`;

            const badge = document.getElementById('statusBadge');
            if (saldo >= 0) {
                badge.innerHTML = '<span class="px-2 py-1 bg-white/20 rounded-lg text-xs font-bold">Empresa Positiva 🚀</span>';
            } else {
                badge.innerHTML = '<span class="px-2 py-1 bg-red-400/20 rounded-lg text-xs font-bold text-red-100">Empresa no Vermelho ⚠️</span>';
            }

            // Render Chart
            const ctx = document.getElementById('financeChart').getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['Receitas', 'Despesas'],
                    datasets: [{
                        label: 'Valor (R$)',
                        data: [totalR, totalD],
                        backgroundColor: ['#10b981', '#ef4444'],
                        borderRadius: 10
                    }]
                },
                options: {
                    responsive: true,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { beginAtZero: true, grid: { display: false } },
                        x: { grid: { display: false } }
                    }
                }
            });
        }

        async function logout() {
            if(!confirm('Sair do sistema?')) return;
            await fetch('api/logout.php');
            window.location.href = 'index.php';
        }

        loadDashboardData();
    </script>
</body>
</html>
