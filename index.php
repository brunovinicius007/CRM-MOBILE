<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Minhas Finan�as - Login</title>
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
                        dark: '#1f2937',
                        darker: '#111827'
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-100 dark:bg-darker text-gray-800 dark:text-gray-100 min-h-screen flex flex-col justify-center items-center p-4">

    <div class="w-full max-w-md bg-white dark:bg-dark rounded-2xl shadow-xl p-8 transform transition-all hover:scale-[1.01]">
        <div class="flex justify-center mb-6">
            <img src="assets/logo.svg" alt="Minhas Finan�as Logo" class="w-20 h-20 animate-bounce">
        </div>
        <h1 class="text-3xl font-bold text-center mb-2 text-primary">Bem-vindo</h1>
        <p class="text-center text-gray-500 dark:text-gray-400 mb-8">Gerencie suas finanças com inteligência.</p>

        <form id="loginForm" class="space-y-6">
            <div>
                <label for="email" class="block text-sm font-medium mb-2">E-mail</label>
                <input type="email" id="email" class="w-full px-4 py-3 rounded-xl bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 focus:outline-none focus:ring-2 focus:ring-primary transition-all" placeholder="seu@email.com" required>
            </div>
            <div>
                <label for="senha" class="block text-sm font-medium mb-2">Senha</label>
                <input type="password" id="senha" class="w-full px-4 py-3 rounded-xl bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 focus:outline-none focus:ring-2 focus:ring-primary transition-all" placeholder="••••••••" required>
            </div>
            
            <button type="submit" class="w-full py-3.5 rounded-xl bg-gradient-to-r from-primary to-blue-600 text-white font-bold text-lg shadow-lg shadow-blue-500/30 hover:shadow-blue-500/50 hover:scale-[1.02] transition-all active:scale-95">
                Entrar
            </button>
        </form>

        <div class="mt-8 text-center text-sm">
            <p class="text-gray-500 dark:text-gray-400">Não tem uma conta?</p>
            <a href="register.php" class="text-primary font-semibold hover:underline">Criar conta grátis</a>
        </div>
    </div>

    <script>
        // PWA Service Worker Registration
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('service-worker.js');
            });
        }

        // Login Logic
        document.getElementById('loginForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const email = document.getElementById('email').value;
            const senha = document.getElementById('senha').value;
            const btn = e.target.querySelector('button');
            
            btn.innerHTML = 'Carregando...';
            btn.disabled = true;

            try {
                const res = await fetch('api/login.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify({ email, senha })
                });
                const data = await res.json();
                
                if (res.ok) {
                    window.location.href = data.redirect;
                } else {
                    alert(data.error);
                }
            } catch (err) {
                alert('Erro de conexão.');
            } finally {
                btn.innerHTML = 'Entrar';
                btn.disabled = false;
            }
        });

        // Dark mode auto-detect
        if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
            document.documentElement.classList.add('dark');
        }
    </script>
</body>
</html>
