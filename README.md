# IAFinanceCRM 🚀

Sistema completo de CRM Financeiro com PHP, MySQL e TailwindCSS. Mobile-first e pronto para instalar como PWA.

## 📋 Pré-requisitos

- Servidor Web (Apache/Nginx/IIS) com PHP 7.4+
- Banco de Dados MySQL / MariaDB
- Conexão com Internet (para carregar TailwindCSS e ícones via CDN)

## 🛠️ Instalação

1. **Banco de Dados**:
   - Crie um banco chamado `iafinance_crm` (ou outro nome de sua preferência).
   - Importe o arquivo `database.sql` para criar as tabelas.

2. **Configuração**:
   - Abra o arquivo `src/db.php`.
   - Configure as credenciais do banco:
     ```php
     $host = 'localhost';
     $db   = 'iafinance_crm';
     $user = 'seu_usuario';
     $pass = 'sua_senha';
     ```

3. **Deploy (Local)**:
   - Se usar XAMPP/WAMP, mova a pasta do projeto para `htdocs` ou `www`.
   - Se tiver PHP instalado, rode na pasta do projeto:
     ```bash
     php -S localhost:8000
     ```
   - Acesse `http://localhost:8000` no navegador.

4. **PWA (Instalar no Celular)**:
   - Acesse o site pelo Chrome no Android ou Safari no iOS.
   - Android: Toque em "Adicionar à Tela Inicial" no aviso que aparecer ou no menu do Chrome.
   - iOS: Toque no botão de Compartilhar > "Adicionar à Tela de Início".
   - *Nota*: Para PWA funcionar 100% (Service Workers), é recomendado usar HTTPS (ou localhost).

## 🔑 Login de Teste
Cadastre um novo usuário na tela de registro ou insira manualmente no banco.
Para testar admin, altere a coluna `role` na tabela `users` para `'admin'`.

## 📁 Estrutura

- `/api`: Endpoints REST que retornam JSON.
- `/src`: Lógica de conexão e autenticação.
- `/assets`: Imagens e ícones.
- `index.php`: Login.
- `dashboard.php`: Painel principal.
- `manifest.json` & `service-worker.js`: Configurações PWA.
