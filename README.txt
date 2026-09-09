ATIVIDADE PRÁTICA - PHP COM MYSQL
Aluno: João Carlos de Souza Espinato
Data: 09/09/2026

ARQUIVOS
- index.php: interface, validação, cálculos e resultado HTML.
- data.php: dados padrão de 30 convidados e 15 produtos.
- db.php: conexão opcional com MySQL por variáveis de ambiente.
- database.sql: criação e carga das tabelas MySQL.
- assets/: imagens SVG usadas na página.
- Dockerfile / render.yaml: publicação em serviço compatível com Docker.
- resultado.html: versão estática do resultado para conferência ou GitHub Pages.

EXECUÇÃO LOCAL (PHP)
1. Abra o terminal nesta pasta.
2. Execute: php -S localhost:8000
3. Acesse: http://localhost:8000/index.php?demo=1

USO COM MYSQL
1. Importe database.sql no MySQL/phpMyAdmin.
2. Configure as variáveis MYSQL_HOST, MYSQL_PORT, MYSQL_DATABASE, MYSQL_USER e MYSQL_PASSWORD.
3. O sistema passará a carregar convidados e produtos do banco.
