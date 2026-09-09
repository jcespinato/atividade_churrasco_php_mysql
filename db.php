<?php
/**
 * Conexão opcional com MySQL.
 * Em hospedagem com MySQL, configure as variáveis de ambiente:
 * MYSQL_HOST, MYSQL_PORT, MYSQL_DATABASE, MYSQL_USER, MYSQL_PASSWORD.
 * Se o banco não estiver disponível, o sistema usa os dados padrão de data.php.
 */
function carregarDadosMysql(): ?array
{
    if (!extension_loaded('mysqli') || !getenv('MYSQL_HOST')) {
        return null;
    }

    $host = getenv('MYSQL_HOST');
    $port = (int)(getenv('MYSQL_PORT') ?: 3306);
    $db   = getenv('MYSQL_DATABASE') ?: 'churrasco';
    $user = getenv('MYSQL_USER') ?: 'root';
    $pass = getenv('MYSQL_PASSWORD') ?: '';

    mysqli_report(MYSQLI_REPORT_OFF);
    $conn = @new mysqli($host, $user, $pass, $db, $port);
    if ($conn->connect_errno) {
        return null;
    }
    $conn->set_charset('utf8mb4');

    $convidados = [];
    $res = $conn->query('SELECT nome, tipo FROM convidados ORDER BY id');
    if ($res) {
        while ($row = $res->fetch_assoc()) {
            $convidados[] = $row;
        }
    }

    $produtos = [];
    $res = $conn->query('SELECT id, nome, grupo, unidade, fator_adulto AS adulto, fator_crianca AS crianca, icone FROM produtos ORDER BY id');
    if ($res) {
        while ($row = $res->fetch_assoc()) {
            $row['id'] = (int)$row['id'];
            $row['adulto'] = (float)$row['adulto'];
            $row['crianca'] = (float)$row['crianca'];
            $produtos[] = $row;
        }
    }
    $conn->close();

    if (count($convidados) !== 30 || count($produtos) < 10) {
        return null;
    }

    return ['convidados'=>$convidados, 'produtos'=>$produtos];
}
