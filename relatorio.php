<?php
/**
 * relatorio.php - Gerador de Relatórios Detalhados
 * Permite visualizar, imprimir e baixar a vida financeira mensal em PDF.
 */
require_once 'src/auth.php';
requireAuth();

// Captura o mês selecionado ou assume o atual
$month = $_GET['month'] ?? date('Y-m');
$monthName = date('F Y', strtotime($month . "-01"));

// Tradução para manter o relatório 100% em português
$meses = [
    'January' => 'Janeiro', 'February' => 'Fevereiro', 'March' => 'Março', 'April' => 'Abril',
    'May' => 'Maio', 'June' => 'Junho', 'July' => 'Julho', 'August' => 'Agosto',
    'September' => 'Setembro', 'October' => 'Outubro', 'November' => 'Novembro', 'December' => 'Dezembro'
];
$monthDisplay = strtr($monthName, $meses);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatório Financeiro - <?php echo $monthDisplay; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- html2pdf.js: Biblioteca que converte HTML em PDF de alta qualidade -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <style>
        /* Regra para esconder elementos que não devem aparecer no papel impresso */
        @media print { .no-print { display: none; } }
    </style>
</head>
<body class="bg-gray-50 text-gray-900 p-4 md:p-10">

    <!-- Container do conteúdo que será transformado em PDF -->
    <div id="reportContent" class="max-w-4xl mx-auto space-y-8 bg-white p-2 md:p-8 rounded-3xl">
        
        <div class="flex justify-between items-start border-b-2 border-primary pb-6 relative">
            <!-- Botão Voltar (Escondido no PDF) -->
            <button onclick="history.back()" class="no-print absolute -left-4 md:-left-12 top-1 p-2 text-gray-400 hover:text-primary transition-all">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
            </button>
            <div>
                <h1 class="text-3xl font-black text-primary uppercase tracking-tighter">Minhas Finanças</h1>
                <p class="text-gray-500 font-bold">Relatório Mensal de Atividades</p>
                <p class="text-sm text-gray-400"><?php echo date('d/m/Y H:i'); ?></p>
            </div>
            <div class="text-right no-print">
                <p class="text-lg font-black uppercase"><?php echo $monthDisplay; ?></p>
                <div class="flex gap-2 mt-2">
                    <button onclick="downloadPDF()" class="px-4 py-2 bg-secondary text-white text-[10px] font-black rounded-lg shadow-lg hover:scale-105 transition-all">⬇️ BAIXAR PDF</button>
                    <button onclick="window.print()" class="px-4 py-2 bg-gray-100 text-gray-600 text-[10px] font-black rounded-lg hover:bg-gray-200 transition-all">🖨️ IMPRIMIR</button>
                </div>
            </div>
        </div>

        <!-- Resumo em 3 Colunas -->
        <div class="grid grid-cols-3 gap-4">
            <div class="p-6 bg-gray-50 rounded-3xl border border-gray-100 text-center">
                <p class="text-[10px] font-black text-gray-400 uppercase mb-1">Total Ganhos</p>
                <p class="text-xl font-black text-green-600" id="repReceitas">R$ 0,00</p>
            </div>
            <div class="p-6 bg-gray-50 rounded-3xl border border-gray-100 text-center">
                <p class="text-[10px] font-black text-gray-400 uppercase mb-1">Total Gastos</p>
                <p class="text-xl font-black text-red-600" id="repDespesas">R$ 0,00</p>
            </div>
            <div class="p-6 bg-primary rounded-3xl shadow-lg text-center text-white">
                <p class="text-[10px] font-black opacity-80 uppercase mb-1">Saldo Final</p>
                <p class="text-xl font-black" id="repSaldo">R$ 0,00</p>
            </div>
        </div>

        <!-- Gráficos do Relatório -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm text-center">
                <h3 class="text-xs font-black uppercase text-gray-400 mb-6">Fluxo de Caixa</h3>
                <div class="h-64"><canvas id="chartFluxo"></canvas></div>
            </div>
            <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm text-center">
                <h3 class="text-xs font-black uppercase text-gray-400 mb-6">Gastos por Categoria</h3>
                <div class="h-64"><canvas id="chartCategorias"></canvas></div>
            </div>
        </div>

        <!-- Listas Detalhadas (Tabelas) -->
        <div class="space-y-6">
            <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="bg-green-50 p-4 border-b border-green-100"><h3 class="font-black text-green-700 uppercase text-xs tracking-widest">Entradas Detalhadas</h3></div>
                <table class="w-full text-left text-sm">
                    <thead><tr class="text-[10px] font-black text-gray-400 uppercase border-b bg-gray-50/50"><th class="p-4">Data</th><th class="p-4">Descrição</th><th class="p-4 text-right">Valor</th></tr></thead>
                    <tbody id="tableReceitas"></tbody>
                </table>
            </div>
            <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="bg-red-50 p-4 border-b border-red-100"><h3 class="font-black text-red-700 uppercase text-xs tracking-widest">Saídas Detalhadas</h3></div>
                <table class="w-full text-left text-sm">
                    <thead><tr class="text-[10px] font-black text-gray-400 uppercase border-b bg-gray-50/50"><th class="p-4">Data</th><th class="p-4">Descrição</th><th class="p-4 text-right">Valor</th></tr></thead>
                    <tbody id="tableDespesas"></tbody>
                </table>
            </div>
        </div>

        <footer class="text-center text-[10px] text-gray-400 uppercase font-bold py-10 border-t border-dashed">
            Relatório gerado automaticamente • Minhas Finanças App
        </footer>
    </div>

    <script>
        const month = "<?php echo $month; ?>";
        const monthLabel = "<?php echo $monthDisplay; ?>";

        /**
         * Carrega os dados e preenche o relatório
         */
        async function loadReportData() {
            const [rRes, dRes] = await Promise.all([
                fetch(`api/list_receitas.php?data=${month}`),
                fetch(`api/list_despesas.php?data=${month}`)
            ]);
            const receitas = await rRes.json(), despesas = await dRes.json();
            const totalR = receitas.reduce((acc, i) => acc + parseFloat(i.valor), 0);
            const totalD = despesas.reduce((acc, i) => acc + parseFloat(i.valor), 0);

            document.getElementById('repReceitas').innerText = formatBRL(totalR);
            document.getElementById('repDespesas').innerText = formatBRL(totalD);
            document.getElementById('repSaldo').innerText = formatBRL(totalR - totalD);

            fillTable('tableReceitas', receitas, 'text-green-600');
            fillTable('tableDespesas', despesas, 'text-red-600');
            renderCharts(totalR, totalD, despesas);
        }

        function formatBRL(val) { return `R$ ${val.toLocaleString('pt-BR', {minimumFractionDigits: 2})}`; }

        /**
         * Preenche as tabelas HTML dinamicamente
         */
        function fillTable(id, items, colorClass) {
            const tbody = document.getElementById(id);
            if(items.length === 0) { tbody.innerHTML = `<tr><td colspan="3" class="p-10 text-center text-gray-400 font-bold uppercase text-[10px]">Sem dados este mês</td></tr>`; return; }
            tbody.innerHTML = items.map(i => `<tr class="border-b border-gray-50"><td class="p-4 text-xs text-gray-500">${new Date(i.data).toLocaleDateString('pt-BR')}</td><td class="p-4 font-bold text-gray-700">${i.descricao}</td><td class="p-4 text-right font-black ${colorClass}">${formatBRL(parseFloat(i.valor))}</td></tr>`).join('');
        }

        /**
         * Renderiza os gráficos do relatório
         */
        function renderCharts(tr, td, despesas) {
            // Gráfico de Barras (Receitas vs Despesas)
            new Chart(document.getElementById('chartFluxo'), {
                type: 'bar',
                data: { labels: ['Receitas', 'Despesas'], datasets: [{ data: [tr, td], backgroundColor: ['#10b981', '#ef4444'], borderRadius: 8 }] },
                options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } } }
            });
            // Gráfico de Rosca (Categorias)
            const cats = {};
            despesas.forEach(d => { cats[d.categoria] = (cats[d.categoria] || 0) + parseFloat(d.valor); });
            new Chart(document.getElementById('chartCategorias'), {
                type: 'doughnut',
                data: { labels: Object.keys(cats), datasets: [{ data: Object.values(cats), backgroundColor: ['#ef4444', '#f59e0b', '#3b82f6', '#10b981', '#8b5cf6', '#ec4899', '#6366f1'], borderWidth: 0 }] },
                options: { responsive: true, maintainAspectRatio: false, cutout: '70%', plugins: { legend: { position: 'bottom', labels: { boxWidth: 10, font: { size: 9, weight: 'bold' } } } } }
            });
        }

        /**
         * Lógica de geração e download do PDF
         */
        function downloadPDF() {
            const element = document.getElementById('reportContent');
            const options = {
                margin: 10,
                filename: `Relatorio_Financeiro_${monthLabel.replace(' ', '_')}.pdf`,
                image: { type: 'jpeg', quality: 0.98 },
                html2canvas: { scale: 2, useCORS: true },
                jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
            };
            
            // Esconde botões de interface para que não saiam no arquivo PDF
            document.querySelectorAll('.no-print').forEach(el => el.style.display = 'none');
            
            // Inicia o processo de conversão e salvamento
            html2pdf().set(options).from(element).save().then(() => {
                // Restaura os botões na tela após gerar o arquivo
                document.querySelectorAll('.no-print').forEach(el => el.style.display = 'block');
            });
        }

        loadReportData();
    </script>
</body>
</html>
