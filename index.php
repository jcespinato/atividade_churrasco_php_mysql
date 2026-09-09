<?php
require __DIR__ . '/data.php';
require __DIR__ . '/db.php';

$dadosMysql = carregarDadosMysql();
$convidados = $dadosMysql['convidados'] ?? $convidadosPadrao;
$produtos = $dadosMysql['produtos'] ?? $produtosPadrao;

function h(string $valor): string { return htmlspecialchars($valor, ENT_QUOTES, 'UTF-8'); }
function arredondarCompra(float $qtd, string $unidade): float {
    if (str_contains($unidade, 'unid') || str_contains($unidade, 'latas') || str_contains($unidade, 'kits')) {
        return (float)ceil($qtd);
    }
    return ceil($qtd * 10) / 10;
}
function formatarQtd(float $qtd, string $unidade): string {
    if (str_contains($unidade, 'unid') || str_contains($unidade, 'latas') || str_contains($unidade, 'kits')) {
        return number_format($qtd, 0, ',', '.') . ' ' . $unidade;
    }
    return number_format($qtd, 1, ',', '.') . ' ' . $unidade;
}

$selecionados = array_map(fn($p) => $p['id'], $produtos);
$erro = '';
$mostrarResultado = isset($_GET['demo']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selecionados = array_values(array_unique(array_map('intval', $_POST['produtos'] ?? [])));
    if (count($selecionados) < 10 || count($selecionados) > 20) {
        $erro = 'Selecione entre 10 e 20 itens para o churrasco.';
    } else {
        $mostrarResultado = true;
    }
}

$adultos = count(array_filter($convidados, fn($c) => $c['tipo'] === 'adulto'));
$criancas = count($convidados) - $adultos;

// Estimativa média de consumo individual pedida na atividade.
$solidoAdultoKg = 0.60;
$liquidoAdultoL = 2.35;
$solidoCriancaKg = 0.40;
$liquidoCriancaL = 1.25;

$totalSolidos = ($adultos * $solidoAdultoKg) + ($criancas * $solidoCriancaKg);
$totalLiquidos = ($adultos * $liquidoAdultoL) + ($criancas * $liquidoCriancaL);

$listaCompras = [];
if ($mostrarResultado && !$erro) {
    foreach ($produtos as $produto) {
        if (!in_array($produto['id'], $selecionados, true)) continue;
        $qtd = ($adultos * (float)$produto['adulto']) + ($criancas * (float)$produto['crianca']);
        // Margem de segurança de 10% para alimentos e bebidas. Apoio não recebe margem extra.
        if ($produto['grupo'] !== 'Apoio') $qtd *= 1.10;
        $qtd = arredondarCompra($qtd, $produto['unidade']);
        $produto['quantidade'] = $qtd;
        $listaCompras[] = $produto;
    }
}
?>
<!doctype html>
<html lang="pt-BR">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Churrasco <?= h($aluno) ?></title>
<style>
:root{--bg:#fff7ed;--paper:#ffffff;--ink:#2f2a25;--muted:#6b625b;--brand:#9a3412;--brand2:#ea580c;--line:#f0d3bd;--ok:#166534}
*{box-sizing:border-box} body{margin:0;font-family:Arial,Helvetica,sans-serif;background:linear-gradient(180deg,#fff7ed 0,#fffbf7 45%,#fff 100%);color:var(--ink)}
.hero{background:linear-gradient(135deg,#7c2d12,#c2410c 55%,#f97316);color:#fff;padding:34px 20px 28px;box-shadow:0 8px 30px rgba(124,45,18,.18)}
.wrap{max-width:1100px;margin:auto}.hero-grid{display:grid;grid-template-columns:1.4fr .6fr;gap:24px;align-items:center}.hero h1{margin:0 0 8px;font-size:clamp(28px,4vw,46px);letter-spacing:.5px}.hero p{margin:5px 0;opacity:.95}.hero img{width:100%;max-height:180px;object-fit:contain;filter:drop-shadow(0 10px 14px rgba(0,0,0,.18))}
main{max-width:1100px;margin:26px auto 60px;padding:0 20px}.card{background:var(--paper);border:1px solid var(--line);border-radius:18px;padding:22px;box-shadow:0 8px 24px rgba(98,54,26,.08);margin-bottom:22px}.card h2{margin:0 0 12px;color:var(--brand)}
.grid{display:grid;grid-template-columns:repeat(4,1fr);gap:14px}.metric{border:1px solid var(--line);border-radius:14px;padding:15px;background:#fffaf6}.metric strong{display:block;font-size:24px;color:var(--brand)}.metric span{font-size:13px;color:var(--muted)}
.products{display:grid;grid-template-columns:repeat(3,1fr);gap:10px}.option{display:flex;gap:9px;align-items:center;border:1px solid #ead8cb;border-radius:12px;padding:11px;background:#fff}.option input{width:18px;height:18px;accent-color:var(--brand2)}
button{border:0;background:var(--brand2);color:#fff;font-weight:700;padding:13px 20px;border-radius:11px;cursor:pointer;font-size:15px}button:hover{filter:brightness(.95)}.error{background:#fee2e2;color:#991b1b;padding:12px;border-radius:10px;margin-bottom:12px}
.table-wrap{overflow-x:auto}table{width:100%;border-collapse:collapse}th,td{padding:11px 10px;border-bottom:1px solid #efe3da;text-align:left}th{background:#fff3ea;color:#7c2d12;font-size:13px;text-transform:uppercase;letter-spacing:.4px}tr:last-child td{border-bottom:0}.badge{display:inline-block;border-radius:999px;padding:4px 9px;font-size:12px;font-weight:700;background:#ffedd5;color:#9a3412}.child{background:#e0f2fe;color:#075985}
.result-banner{display:grid;grid-template-columns:1.2fr .8fr;gap:18px;align-items:center;background:linear-gradient(135deg,#fff7ed,#fff);border:1px solid #fed7aa;border-radius:16px;padding:18px}.result-banner img{width:100%;max-height:160px;object-fit:contain}.note{font-size:13px;color:var(--muted);line-height:1.5}.check{color:var(--ok);font-weight:700}.footer{text-align:center;color:#7c6b60;font-size:12px;padding:18px}.code{font-family:Consolas,monospace;background:#2b211d;color:#f7ede6;padding:12px;border-radius:10px;font-size:12px}
@media(max-width:800px){.hero-grid,.result-banner{grid-template-columns:1fr}.grid{grid-template-columns:repeat(2,1fr)}.products{grid-template-columns:1fr 1fr}}@media(max-width:520px){.grid,.products{grid-template-columns:1fr}}
</style>
</head>
<body>
<header class="hero">
  <div class="wrap hero-grid">
    <div>
      <p>ATIVIDADE PRÁTICA • PHP COM MYSQL</p>
      <h1>CHURRASCO JOÃO CARLOS DE SOUZA ESPINATO</h1>
      <p>Planejamento para 30 convidados, cálculo de consumo e lista completa de compras.</p>
    </div>
    <img src="assets/churrasco.svg" alt="Ilustração de churrasqueira com alimentos">
  </div>
</header>
<main>
  <section class="card">
    <h2>1. Monte sua lista de desejos</h2>
    <p>Escolha de 10 a 20 itens. A lista abaixo já vem preenchida com 15 opções típicas de churrasco.</p>
    <?php if ($erro): ?><div class="error"><?= h($erro) ?></div><?php endif; ?>
    <form method="post">
      <div class="products">
        <?php foreach ($produtos as $produto): ?>
          <label class="option"><input type="checkbox" name="produtos[]" value="<?= (int)$produto['id'] ?>" <?= in_array($produto['id'],$selecionados,true)?'checked':'' ?>> <span><?= h($produto['icone'].' '.$produto['nome']) ?></span></label>
        <?php endforeach; ?>
      </div>
      <p class="note">Regra do programa: no mínimo 10 e no máximo 20 itens.</p>
      <button type="submit">Calcular churrasco</button>
    </form>
  </section>

  <section class="card">
    <h2>2. Lista de convidados (30)</h2>
    <div class="grid">
      <div class="metric"><strong><?= count($convidados) ?></strong><span>convidados</span></div>
      <div class="metric"><strong><?= $adultos ?></strong><span>adultos</span></div>
      <div class="metric"><strong><?= $criancas ?></strong><span>crianças</span></div>
      <div class="metric"><strong>10%</strong><span>margem de segurança nas compras</span></div>
    </div>
    <p class="note" style="margin-bottom:0">A relação completa dos 30 convidados, incluindo Paula e seus dois filhos, aparece no final da página.</p>
  </section>

  <?php if ($mostrarResultado && !$erro): ?>
  <section class="card" id="resultado">
    <div class="result-banner">
      <div><h2>3. Resultado do cálculo</h2><p class="check">✓ Cálculo concluído para <?= count($convidados) ?> pessoas.</p><p>A estimativa individual usada foi de <strong>600 g de sólidos e 2,35 L de líquidos por adulto</strong> e <strong>400 g de sólidos e 1,25 L de líquidos por criança</strong>.</p></div>
      <img src="assets/convidados.svg" alt="Ilustração de convidados em um churrasco">
    </div>
    <div class="grid" style="margin-top:14px">
      <div class="metric"><strong><?= number_format($totalSolidos,1,',','.') ?> kg</strong><span>consumo sólido estimado</span></div>
      <div class="metric"><strong><?= number_format($totalLiquidos,1,',','.') ?> L</strong><span>consumo líquido estimado</span></div>
      <div class="metric"><strong><?= count($listaCompras) ?></strong><span>itens selecionados</span></div>
      <div class="metric"><strong><?= count($convidados) ?></strong><span>pessoas atendidas</span></div>
    </div>
  </section>

  <section class="card">
    <h2>4. Lista completa de produtos e quantidades</h2>
    <div class="table-wrap"><table><thead><tr><th>Produto</th><th>Grupo</th><th>Quantidade recomendada</th></tr></thead><tbody>
      <?php foreach ($listaCompras as $p): ?><tr><td><?= h($p['icone'].' '.$p['nome']) ?></td><td><?= h($p['grupo']) ?></td><td><strong><?= h(formatarQtd($p['quantidade'],$p['unidade'])) ?></strong></td></tr><?php endforeach; ?>
    </tbody></table></div>
    <p class="note">As quantidades de alimentos e bebidas recebem uma margem de segurança de aproximadamente 10% para reduzir o risco de faltar. Carvão, gelo e descartáveis são calculados diretamente por pessoa.</p>
  </section>
  <?php endif; ?>

  <section class="card">
    <h2>5. Relação completa dos 30 convidados</h2>
    <div class="table-wrap">
      <table><thead><tr><th>#</th><th>Convidado</th><th>Perfil</th><th>Consumo sólido</th><th>Consumo líquido</th></tr></thead><tbody>
      <?php foreach ($convidados as $i=>$c): $crianca=$c['tipo']==='crianca'; ?>
        <tr><td><?= $i+1 ?></td><td><?= h($c['nome']) ?></td><td><span class="badge <?= $crianca?'child':'' ?>"><?= $crianca?'Criança':'Adulto' ?></span></td><td><?= $crianca?'400 g':'600 g' ?></td><td><?= $crianca?'1,25 L':'2,35 L' ?></td></tr>
      <?php endforeach; ?>
      </tbody></table>
    </div>
  </section>

  <section class="card">
    <h2>Como o programa foi desenvolvido</h2>
    <p>O sistema foi feito em PHP e preparado para carregar convidados e produtos de um banco MySQL. Quando as variáveis de conexão não estão configuradas, ele usa os mesmos dados padrão locais para permitir a demonstração.</p>
    <div class="code">Tecnologias: PHP 8+ • HTML5 • CSS3 • MySQL • validação de 10 a 20 itens • cálculo automático por perfil de convidado</div>
  </section>
</main>
<div class="footer">Projeto acadêmico — <?= h($aluno) ?> — 09/09/2026</div>
</body></html>
