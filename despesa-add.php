<?php
require_once 'src/auth.php';
requireAuth();
$id = $_GET['id'] ?? null;
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Minhas Finanças - <?php echo $id ? 'Editar' : 'Nova'; ?> Despesa</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: { primary: '#3b82f6', secondary: '#10b981', danger: '#ef4444', dark: '#1f2937', darker: '#111827' }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50 dark:bg-darker text-gray-800 dark:text-gray-100 min-h-screen transition-colors duration-300">

    <header class="bg-white dark:bg-dark p-4 flex items-center gap-4 shadow-sm transition-colors duration-300">
        <a href="despesas.php" class="text-gray-500 hover:text-danger">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
        </a>
        <h1 class="font-bold text-xl"><?php echo $id ? 'Editar Despesa' : 'Nova Despesa'; ?></h1>
    </header>

    <main class="p-4 max-w-md mx-auto mt-4">
        <form id="despesaForm" class="space-y-6">
            <input type="hidden" id="itemId" value="<?php echo $id; ?>">
            
            <div class="bg-white dark:bg-dark p-6 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-800 space-y-4 transition-colors duration-300">
                <div>
                    <label class="block text-[10px] font-black uppercase text-gray-400 mb-1 tracking-widest">Descrição</label>
                    <input type="text" id="descricao" class="w-full px-4 py-3 rounded-xl bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 focus:ring-2 focus:ring-danger/20 outline-none" placeholder="Ex: Aluguel, Internet..." required>
                </div>

                <div>
                    <label class="block text-[10px] font-black uppercase text-gray-400 mb-1 tracking-widest">Valor (R$)</label>
                    <input type="number" step="0.01" id="valor" class="w-full px-4 py-3 rounded-xl bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 focus:ring-2 focus:ring-danger/20 outline-none font-bold" placeholder="0,00" required>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] font-black uppercase text-gray-400 mb-1 tracking-widest">Categoria</label>
                        <select id="categoria" class="w-full px-4 py-3 rounded-xl bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 focus:ring-2 focus:ring-danger/20 outline-none" onchange="toggleSubcat()" required>
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
                    <div>
                        <label class="block text-[10px] font-black uppercase text-gray-400 mb-1 tracking-widest">Data</label>
                        <input type="date" id="data" class="w-full px-4 py-3 rounded-xl bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 focus:ring-2 focus:ring-danger/20 outline-none" required>
                    </div>
                </div>

                <div id="subcatContainer" class="hidden animate-in fade-in slide-in-from-top-2 duration-300">
                    <label class="block text-[10px] font-black uppercase text-primary mb-1 tracking-widest">Subcategoria (Cartão)</label>
                    <select id="subcategoria" class="w-full px-4 py-3 rounded-xl bg-blue-50 dark:bg-blue-900/20 border border-blue-100 dark:border-blue-800 focus:ring-2 focus:ring-primary outline-none">
                        <option value="Streaming (Netflix, Spotify)">Streaming (Netflix, Spotify)</option>
                        <option value="Compras Online (Amazon, Shopee)">Compras Online (Amazon, Shopee)</option>
                        <option value="Restaurante/iFood">Restaurante/iFood</option>
                        <option value="Mercado">Mercado</option>
                        <option value="Farmácia">Farmácia</option>
                        <option value="Vestuário">Vestuário</option>
                        <option value="Eletrônicos">Eletrônicos</option>
                        <option value="Assinaturas/Apps">Assinaturas/Apps</option>
                        <option value="Outros">Outros</option>
                    </select>
                </div>

                <?php if(!$id): ?>
                <hr class="border-gray-50 dark:border-gray-800">
                <div>
                    <label class="block text-[10px] font-black uppercase text-gray-400 mb-1 tracking-widest">Repetir despesa?</label>
                    <select id="repetir_meses" class="w-full px-4 py-3 rounded-xl bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 focus:ring-2 focus:ring-danger/20 outline-none font-semibold text-primary">
                        <option value="1">Apenas este mês</option>
                        <option value="2">Repetir por 2 meses</option>
                        <option value="3">Repetir por 3 meses</option>
                        <option value="6">Repetir por 6 meses</option>
                        <option value="12">Repetir pelo ano todo</option>
                    </select>
                </div>
                <?php endif; ?>
            </div>

            <button type="submit" id="saveBtn" class="w-full py-4 rounded-2xl bg-danger text-white font-black text-lg shadow-lg shadow-red-500/30 hover:scale-[1.02] active:scale-95 transition-all">
                Salvar
            </button>
        </form>
    </main>

    <script>
        const itemId = document.getElementById('itemId').value;

        function toggleSubcat() {
            const cat = document.getElementById('categoria').value;
            const container = document.getElementById('subcatContainer');
            if (cat === 'Cartão de Crédito') container.classList.remove('hidden');
            else container.classList.add('hidden');
        }

        if (itemId) {
            fetch(`api/get_despesa.php?id=${itemId}`)
                .then(res => res.json())
                .then(data => {
                    if (data.error) {
                        alert(data.error);
                        window.location.href = 'despesas.php';
                        return;
                    }
                    document.getElementById('descricao').value = data.descricao;
                    document.getElementById('valor').value = data.valor;
                    document.getElementById('categoria').value = data.categoria;
                    document.getElementById('data').value = data.data;
                    if (data.categoria === 'Cartão de Crédito') {
                        document.getElementById('subcategoria').value = data.subcategoria;
                        toggleSubcat();
                    }
                });
        } else {
            document.getElementById('data').valueAsDate = new Date();
        }

        document.getElementById('despesaForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const btn = document.getElementById('saveBtn');
            const body = {
                id: itemId,
                descricao: document.getElementById('descricao').value,
                valor: document.getElementById('valor').value,
                categoria: document.getElementById('categoria').value,
                subcategoria: document.getElementById('categoria').value === 'Cartão de Crédito' ? document.getElementById('subcategoria').value : null,
                data: document.getElementById('data').value
            };

            if (!itemId) body.repetir_meses = document.getElementById('repetir_meses').value;

            btn.disabled = true;
            btn.innerText = 'Salvando...';

            const url = itemId ? 'api/update_despesa.php' : 'api/create_despesa.php';

            try {
                const res = await fetch(url, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(body)
                });
                const data = await res.json();

                if (res.ok) {
                    alert(data.message || 'Salvo com sucesso!');
                    window.location.href = 'despesas.php';
                } else {
                    alert(data.error || 'Erro ao salvar');
                }
            } catch (err) {
                alert('Erro de conexão');
            } finally {
                btn.disabled = false;
                btn.innerText = 'Salvar';
            }
        });

        if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) document.documentElement.classList.add('dark');
    </script>
</body>
</html>
