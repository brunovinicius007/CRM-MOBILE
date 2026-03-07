<?php
require_once 'src/auth.php';
requireAuth();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Minhas Finanças - Metas</title>
    <script src="https://cdn.tailwindcss.com"></script>
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
            <a href="dashboard.php" class="text-gray-500 hover:text-primary transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <h1 class="font-bold text-xl uppercase tracking-tighter text-primary text-sm">Metas</h1>
        </div>
        <div class="flex items-center gap-2">
            <!-- MODO ESCURO -->
            <button id="themeToggle" onclick="toggleTheme()" class="hidden p-2 rounded-xl bg-gray-100 dark:bg-gray-800 text-gray-500 dark:text-yellow-400">
                <svg id="moonIcon" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" /></svg>
                <svg id="sunIcon" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M12 3v1m0 18v1m9-11h-1M3 12H2m15.364-6.364l-.707.707M6.343 17.657l-.707.707m12.728 0l-.707-.707M6.343 6.343l-.707-.707M12 5a7 7 0 100 14 7 7 0 000-14z" /></svg>
            </button>
            <button onclick="openModal()" class="bg-primary text-white px-3 py-2 rounded-xl text-xs font-black shadow-lg hover:scale-105 transition-all">+ Nova</button>
            <button onclick="logout()" class="p-2 text-gray-400 hover:text-danger">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
            </button>
        </div>
    </header>

    <main class="p-4 max-w-2xl mx-auto space-y-6">
        <div id="goalsAI" class="hidden bg-gradient-to-br from-blue-600 to-indigo-700 p-6 rounded-3xl text-white shadow-xl relative overflow-hidden transition-all duration-500">
            <div class="relative z-10">
                <h3 class="text-[10px] font-black uppercase opacity-80 tracking-widest mb-3 flex items-center gap-2">✨ Inteligência de Conquista</h3>
                <div id="aiAdvice" class="text-sm font-bold leading-relaxed mb-2">Analisando...</div>
            </div>
            <div class="absolute -right-10 -bottom-10 w-32 h-32 bg-white/10 rounded-full blur-2xl"></div>
        </div>
        <div id="metasContainer" class="space-y-4">
            <p class="text-center text-gray-400 mt-10 font-black uppercase text-xs tracking-widest animate-pulse">Carregando seus sonhos...</p>
        </div>
    </main>

    <!-- Modal Nova Meta -->
    <div id="metaModal" class="fixed inset-0 bg-black/50 hidden z-50 flex items-center justify-center p-4 backdrop-blur-sm transition-all">
        <div class="bg-white dark:bg-dark w-full max-w-md rounded-3xl p-6 shadow-2xl">
            <div class="flex justify-between items-center mb-6"><h3 id="modalTitle" class="font-black text-lg uppercase text-sm">Nova Meta</h3><button onclick="closeModal()" class="text-gray-400 font-bold text-xl">✕</button></div>
            <form id="metaForm" class="space-y-4">
                <input type="hidden" id="editId">
                <div><label class="block text-[10px] font-black uppercase text-gray-400 mb-1">Título do Sonho</label><input type="text" id="titulo" class="w-full px-4 py-3 rounded-xl bg-gray-50 dark:bg-gray-800 border-none outline-none focus:ring-2 focus:ring-primary" placeholder="Ex: Viagem, Carro..." required></div>
                <div class="grid grid-cols-2 gap-4">
                    <div><label class="block text-[10px] font-black uppercase text-gray-400 mb-1">Valor Alvo</label><input type="text" id="valor_objetivo" class="w-full px-4 py-3 rounded-xl bg-gray-50 dark:bg-gray-800 border-none outline-none font-bold text-sm" placeholder="0.00" required></div>
                    <div><label class="block text-[10px] font-black uppercase text-gray-400 mb-1">Prazo</label><input type="date" id="prazo" class="w-full px-4 py-3 rounded-xl bg-gray-50 dark:bg-gray-800 border-none outline-none text-sm" required></div>
                </div>
                <button type="submit" id="saveBtn" class="w-full py-4 rounded-2xl bg-primary text-white font-black text-sm shadow-lg mt-4 uppercase">Salvar Sonho</button>
            </form>
        </div>
    </div>

    <!-- Modal Adicionar Valor -->
    <div id="addValueModal" class="fixed inset-0 bg-black/50 hidden z-50 flex items-center justify-center p-4 backdrop-blur-sm transition-all">
        <div class="bg-white dark:bg-dark w-full max-w-md rounded-3xl p-6 shadow-2xl">
            <div class="flex justify-between items-center mb-6"><h3 class="font-black text-lg uppercase text-sm">Poupou quanto hoje?</h3><button onclick="closeAddValueModal()" class="text-gray-400 font-bold text-xl">✕</button></div>
            <div class="space-y-4">
                <input type="hidden" id="targetMetaId">
                <div><label class="block text-[10px] font-black uppercase text-gray-400 mb-1">Valor Poupadado</label><input type="text" id="addValueInput" class="w-full px-4 py-3 rounded-xl bg-gray-50 dark:bg-gray-800 border-none outline-none font-bold text-sm" placeholder="0,00" required></div>
                <button onclick="confirmAddValue()" class="w-full py-4 rounded-2xl bg-secondary text-white font-black text-sm shadow-lg mt-4 uppercase">Adicionar Valor</button>
            </div>
        </div>
    </div>

    <!-- Menu Inferior Padronizado -->
    <nav class="fixed bottom-0 w-full bg-white dark:bg-dark border-t border-gray-100 dark:border-gray-800 pb-safe pt-2 px-6 flex justify-between z-40 transition-colors duration-300">
        <a href="dashboard.php" class="flex flex-col items-center p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg>
            <span class="text-[10px] font-black mt-1 uppercase">Home</span>
        </a>
        <a href="metas.php" class="flex flex-col items-center p-2 text-primary">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 20 20" fill="currentColor"><path d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
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
            loadMetas();
        }
        function updateThemeIcons(isDark) {
            document.getElementById('moonIcon').classList.toggle('hidden', isDark);
            document.getElementById('sunIcon').classList.toggle('hidden', !isDark);
        }

        async function loadMetas() {
            try {
                const now = new Date(), month = `${now.getFullYear()}-${String(now.getMonth() + 1).padStart(2, '0')}`;
                const [mRes, rRes, dRes] = await Promise.all([fetch('api/list_metas.php'), fetch(`api/list_receitas.php?data=${month}`), fetch(`api/list_despesas.php?data=${month}`)]);
                const metas = await mRes.json(), receitas = await rRes.json(), despesas = await dRes.json();
                const container = document.getElementById('metasContainer'); container.innerHTML = '';
                generateAIAdvice(metas, receitas, despesas);
                if (metas.length === 0) { container.innerHTML = `<div class="bg-white dark:bg-dark p-10 rounded-3xl text-center border-2 border-dashed border-gray-200 dark:border-gray-800 transition-colors duration-300"><span class="text-5xl block mb-4">🏠</span><p class="text-gray-500 font-black uppercase text-[10px] tracking-widest">Nada por aqui.</p></div>`; return; }
                metas.forEach(meta => {
                    const prog = Math.min(meta.progresso, 100).toFixed(0);
                    const el = document.createElement('div');
                    el.className = 'bg-white dark:bg-dark p-6 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-800 transition-all duration-300';
                    el.innerHTML = `<div class="flex justify-between items-start mb-4"><div><h3 class="font-black text-lg text-gray-800 dark:text-white uppercase tracking-tighter text-sm">${meta.titulo}</h3><p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">${new Date(meta.prazo).toLocaleDateString('pt-BR')}</p></div><div class="text-right"><button onclick="editMeta(${meta.id})" class="text-[10px] font-black text-primary uppercase hover:underline mr-2">Editar</button><span class="text-xs font-black text-primary">${prog}%</span></div></div><div class="w-full h-2 bg-gray-100 dark:bg-gray-800 rounded-full overflow-hidden mb-4"><div class="h-full bg-primary transition-all duration-1000" style="width: ${prog}%"></div></div><div class="flex justify-between items-center text-[11px] mb-6 font-bold"><div class="flex flex-col"><span class="text-gray-400 uppercase text-[9px]">Poupado</span><span class="text-secondary">R$ ${parseFloat(meta.valor_poupado).toFixed(2)}</span></div><div class="flex flex-col text-right"><span class="text-gray-400 uppercase text-[9px]">Objetivo</span><span>R$ ${parseFloat(meta.valor_objetivo).toFixed(2)}</span></div></div><div class="grid grid-cols-2 gap-3"><button onclick="openAddValueModal(${meta.id})" class="py-3 bg-blue-50 dark:bg-blue-900/20 text-primary rounded-xl text-[10px] font-black uppercase transition-all">Poupou?</button><button onclick="deleteMeta(${meta.id})" class="py-3 text-gray-400 rounded-xl text-[10px] font-black uppercase hover:text-red-500 transition-all">Remover</button></div>`;
                    container.appendChild(el);
                });
            } catch (err) { }
        }
        function generateAIAdvice(metas, receitas, despesas) {
            const aiCard = document.getElementById('goalsAI'), aiText = document.getElementById('aiAdvice');
            if (metas.length === 0) { aiCard.classList.add('hidden'); return; }
            aiCard.classList.remove('hidden');
            const totalR = receitas.reduce((acc, i) => acc + (parseFloat(i.valor) || 0), 0), totalD = despesas.reduce((acc, i) => acc + (parseFloat(i.valor) || 0), 0), saldo = totalR - totalD, meta = metas[0];
            const meses = Math.max(1, Math.ceil((new Date(meta.prazo) - new Date()) / (1000*60*60*24*30))), parcela = (meta.valor_objetivo - meta.valor_poupado) / meses;
            aiText.innerText = saldo > parcela ? `Seu saldo atual cobre a parcela para ${meta.titulo}!` : `Poupe R$ ${parcela.toFixed(2)}/mês para conquistar ${meta.titulo}.`;
        }
        function openModal() { document.getElementById('editId').value = ''; document.getElementById('modalTitle').innerText = 'Novo Sonho'; document.getElementById('metaForm').reset(); document.getElementById('metaModal').classList.remove('hidden'); }
        function closeModal() { document.getElementById('metaModal').classList.add('hidden'); }
        async function editMeta(id) {
            const res = await fetch(`api/get_meta.php?id=${id}`), data = await res.json();
            document.getElementById('editId').value = data.id; document.getElementById('modalTitle').innerText = 'Editar Sonho'; document.getElementById('titulo').value = data.titulo; document.getElementById('valor_objetivo').value = data.valor_objetivo; document.getElementById('prazo').value = data.prazo; document.getElementById('metaModal').classList.remove('hidden');
        }
        document.getElementById('metaForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const editId = document.getElementById('editId').value;
            const btn = document.getElementById('saveBtn');
            
            // Tratamento de valor decimal (vírgula para ponto)
            let valorLimpo = document.getElementById('valor_objetivo').value.replace(',', '.');
            valorLimpo = parseFloat(valorLimpo);

            if (isNaN(valorLimpo) || valorLimpo <= 0) {
                alert('Por favor, insira um valor de objetivo válido.');
                return;
            }

            const body = { 
                id: editId, 
                titulo: document.getElementById('titulo').value, 
                valor_objetivo: valorLimpo, 
                prazo: document.getElementById('prazo').value 
            };

            btn.disabled = true;
            btn.innerHTML = 'Salvando...';

            try {
                const res = await fetch(editId ? 'api/update_meta.php' : 'api/create_meta.php', { 
                    method: 'POST', 
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(body) 
                });
                
                const text = await res.text();
                let data;
                try {
                    data = JSON.parse(text);
                } catch(e) {
                    throw new Error('Resposta inválida do servidor: ' + text);
                }

                if (res.ok) {
                    closeModal(); 
                    await loadMetas();
                } else {
                    alert(data.error || 'Erro ao salvar meta.');
                }
            } catch (err) {
                alert('Erro: ' + err.message);
            } finally {
                btn.disabled = false;
                btn.innerHTML = 'Salvar Sonho';
            }
        });
        function openAddValueModal(id) { document.getElementById('targetMetaId').value = id; document.getElementById('addValueModal').classList.remove('hidden'); }
        function closeAddValueModal() { document.getElementById('addValueModal').classList.add('hidden'); }
        async function confirmAddValue() {
            const id = document.getElementById('targetMetaId').value;
            let valor = document.getElementById('addValueInput').value.replace(',', '.');
            valor = parseFloat(valor);
            
            if (isNaN(valor) || valor <= 0) {
                alert('Por favor, insira um valor válido.');
                return;
            }

            await fetch('api/update_meta_valor.php', { 
                method: 'POST', 
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({id, valor}) 
            });
            document.getElementById('addValueInput').value = '';
            closeAddValueModal(); 
            loadMetas();
        }
        async function deleteMeta(id) { if(!confirm('Excluir?')) return; await fetch('api/delete_meta.php', { method: 'POST', body: JSON.stringify({id}) }); loadMetas(); }
        async function logout() { if(!confirm('Sair?')) return; await fetch('api/logout.php'); window.location.href = 'index.php'; }
        loadMetas();
    </script>
</body>
</html>
