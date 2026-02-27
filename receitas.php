<?php
require_once 'src/auth.php';
requireAuth();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IAFinance - Receitas</title>
    <script src="https://cdn.tailwindcss.com"></script>
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
<body class="bg-gray-100 dark:bg-darker text-gray-800 dark:text-gray-100 pb-24">

    <!-- Header -->
    <header class="bg-white dark:bg-dark shadow-sm sticky top-0 z-10 p-4 flex justify-between items-center">
        <h1 class="font-bold text-xl">Receitas 💰</h1>
        <button onclick="openModal()" class="bg-secondary text-white px-4 py-2 rounded-lg text-sm font-bold shadow-lg shadow-green-500/30 hover:shadow-green-500/50 transition-all">+ Nova</button>
    </header>

    <!-- Filters -->
    <div class="p-4 bg-white dark:bg-dark border-b border-gray-100 dark:border-gray-800">
        <div class="flex gap-2 overflow-x-auto pb-2">
            <input type="month" id="filterData" class="px-3 py-2 rounded-lg bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-sm" onchange="loadItems()">
            <select id="filterCategoria" class="px-3 py-2 rounded-lg bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-sm" onchange="loadItems()">
                <option value="">Todas Categorias</option>
                <option value="Salário">Salário</option>
                <option value="Investimento">Investimento</option>
                <option value="Extra">Extra</option>
                <option value="Outros">Outros</option>
            </select>
        </div>
    </div>

    <!-- List -->
    <main class="p-4 space-y-3" id="itemsList">
        <!-- JS Populated -->
        <p class="text-center text-gray-500 mt-10">Carregando...</p>
    </main>

    <!-- Modal Form -->
    <div id="modal" class="fixed inset-0 bg-black/50 hidden z-50 flex items-end sm:items-center justify-center p-4 backdrop-blur-sm transition-all">
        <div class="bg-white dark:bg-dark w-full max-w-md rounded-2xl p-6 shadow-2xl transform transition-all translate-y-full sm:translate-y-0" id="modalContent">
            <div class="flex justify-between items-center mb-4">
                <h3 class="font-bold text-lg">Nova Receita</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">✕</button>
            </div>
            <form id="form" class="space-y-4">
                <input type="hidden" id="itemId">
                <div>
                    <label class="block text-xs font-medium mb-1">Descrição</label>
                    <input type="text" id="descricao" class="w-full px-4 py-3 rounded-xl bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700" required>
                </div>
                <div>
                    <label class="block text-xs font-medium mb-1">Valor (R$)</label>
                    <input type="number" step="0.01" id="valor" class="w-full px-4 py-3 rounded-xl bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700" required>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium mb-1">Categoria</label>
                        <select id="categoria" class="w-full px-4 py-3 rounded-xl bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700" required>
                            <option value="Salário">Salário</option>
                            <option value="Investimento">Investimento</option>
                            <option value="Extra">Extra</option>
                            <option value="Outros">Outros</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium mb-1">Data</label>
                        <input type="date" id="data" class="w-full px-4 py-3 rounded-xl bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700" required>
                    </div>
                </div>
                <button type="submit" class="w-full py-3.5 rounded-xl bg-secondary text-white font-bold shadow-lg shadow-green-500/30">Salvar</button>
            </form>
        </div>
    </div>

    <!-- Nabvar -->
    <nav class="fixed bottom-0 w-full bg-white dark:bg-dark border-t border-gray-100 dark:border-gray-800 pb-safe pt-2 px-6 flex justify-between z-40">
        <a href="dashboard.php" class="flex flex-col items-center p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg>
            <span class="text-[10px] font-medium mt-1">Home</span>
        </a>
        <a href="receitas.php" class="flex flex-col items-center p-2 text-primary">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            <span class="text-[10px] font-medium mt-1">Receitas</span>
        </a>
        <div class="w-12"></div>
        <a href="despesas.php" class="flex flex-col items-center p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            <span class="text-[10px] font-medium mt-1">Despesas</span>
        </a>
    </nav>

    <script>
        if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) document.documentElement.classList.add('dark');

        async function loadItems() {
            const date = document.getElementById('filterData').value;
            const cat = document.getElementById('filterCategoria').value;
            
            const res = await fetch(`api/list_receitas.php?data=${date}&categoria=${cat}`);
            const data = await res.json();
            
            const list = document.getElementById('itemsList');
            list.innerHTML = '';

            if (data.length === 0) list.innerHTML = '<p class="text-center text-gray-400 mt-10">Nenhuma receita encontrada.</p>';

            data.forEach(item => {
                const el = document.createElement('div');
                el.className = 'bg-white dark:bg-dark p-4 rounded-xl shadow-sm border border-gray-100 dark:border-gray-800 flex justify-between items-center';
                el.innerHTML = `
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center text-green-600 font-bold">
                            💰
                        </div>
                        <div>
                            <p class="font-bold text-gray-800 dark:text-gray-200">${item.descricao}</p>
                            <p class="text-xs text-gray-500">${new Date(item.data).toLocaleDateString('pt-BR')} • ${item.categoria}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="font-bold text-secondary">+ R$ ${parseFloat(item.valor).toFixed(2)}</p>
                        <button onclick="deleteItem(${item.id})" class="text-xs text-red-400 hover:text-red-600 mt-1">Excluir</button>
                    </div>
                `;
                list.appendChild(el);
            });
        }

        async function deleteItem(id) {
            if(!confirm('Excluir esta receita?')) return;
            await fetch('api/delete_receita.php', {
                method: 'POST',
                body: JSON.stringify({id})
            });
            loadItems();
        }

        // Modal Logic
        const modal = document.getElementById('modal');
        const modalContent = document.getElementById('modalContent');
        
        function openModal() {
            modal.classList.remove('hidden');
            setTimeout(() => {
                modalContent.classList.remove('translate-y-full'); 
            }, 10);
            document.getElementById('form').reset();
            document.getElementById('data').valueAsDate = new Date();
        }

        function closeModal() {
            modalContent.classList.add('translate-y-full');
            setTimeout(() => modal.classList.add('hidden'), 300);
        }

        document.getElementById('form').addEventListener('submit', async (e) => {
            e.preventDefault();
            const body = {
                descricao: document.getElementById('descricao').value,
                valor: document.getElementById('valor').value,
                categoria: document.getElementById('categoria').value,
                data: document.getElementById('data').value
            };
            
            await fetch('api/create_receita.php', {
                method: 'POST',
                body: JSON.stringify(body)
            });
            
            closeModal();
            loadItems();
        });

        loadItems();
    </script>
</body>
</html>
