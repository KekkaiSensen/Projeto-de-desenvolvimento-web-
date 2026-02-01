<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../../Banco de dados/conexao.php';

try {
    // 1. Get query parameters
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 20;
    $offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;
    $categoria = isset($_GET['categoria']) ? trim($_GET['categoria']) : null;
    $busca = isset($_GET['busca']) ? trim($_GET['busca']) : null;

    // Validate limit and offset
    if ($limit <= 0) $limit = 20;
    if ($offset < 0) $offset = 0;

    // 2. Build Query
    // Join with categorias to get category name
    $sql = "SELECT p.*, c.nome as categoria_nome 
            FROM produtos p 
            LEFT JOIN categorias c ON p.categoria_id = c.id 
            WHERE p.status = 'ativo'";
    $params = [];

    if ($categoria) {
        $sql .= " AND (c.nome = :categoria OR p.categoria_id = :categoria)";
        $params[':categoria'] = $categoria;
    }

    if ($busca) {
        $sql .= " AND (p.nome LIKE :busca OR p.descricao LIKE :busca)";
        $params[':busca'] = '%' . $busca . '%';
    }

    $sql .= " LIMIT :limit OFFSET :offset";

    $stmt = $pdo->prepare($sql);

    // Bind parameters
    if ($categoria) $stmt->bindValue(':categoria', $params[':categoria']);
    if ($busca) $stmt->bindValue(':busca', $params[':busca']);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

    $stmt->execute();
    $produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 3. Process products (add images and parse description)
    $resultado = [];

    foreach ($produtos as $produto) {
        $id = $produto['id'];

        // Fetch secondary images
        $stmt_img = $pdo->prepare("SELECT url_imagem FROM produto_imagens WHERE produto_id = ?");
        $stmt_img->execute([$id]);
        $imagens_secundarias = $stmt_img->fetchAll(PDO::FETCH_COLUMN);

        // Combine main image with secondary images
        $imagens = [];
        if (!empty($produto['imagem_url'])) {
            $imagens[] = $produto['imagem_url'];
        }
        $imagens = array_merge($imagens, $imagens_secundarias);

        // Parse description logic (using same logic as tela_produto.php)
        $descricao_completa = $produto['descricao'] ?? '';
        $caracteristicas = [];
        $especificacoes = [];
        $descricao_texto = '';

        $inicio_caract = strpos($descricao_completa, "--- CARACTERÍSTICAS ---");
        $inicio_espec = strpos($descricao_completa, "--- ESPECIFICAÇÕES ---");
        $inicio_desc = strpos($descricao_completa, "--- DESCRIÇÃO ---");

        // Parse 'Descrição'
        if ($inicio_desc !== false) {
            $descricao_texto = trim(substr($descricao_completa, $inicio_desc + strlen("--- DESCRIÇÃO ---")));
        } else {
            if ($inicio_caract === false && $inicio_espec === false) {
                $descricao_texto = $descricao_completa;
            }
        }

        // Parse 'Características'
        if ($inicio_caract !== false) {
            $fim_caract = $inicio_espec !== false ? $inicio_espec : $inicio_desc;
            if ($fim_caract === false) $fim_caract = strlen($descricao_completa);

            $bloco_caract = substr(
                $descricao_completa,
                $inicio_caract + strlen("--- CARACTERÍSTICAS ---"),
                $fim_caract - ($inicio_caract + strlen("--- CARACTERÍSTICAS ---"))
            );

            $linhas = explode("\n", trim($bloco_caract));
            foreach ($linhas as $linha) {
                $partes = explode(":", $linha, 2);
                if (count($partes) == 2) {
                    $caracteristicas[trim($partes[0])] = trim($partes[1]);
                }
            }
        }

        // Parse 'Especificações'
        if ($inicio_espec !== false) {
            $fim_espec = $inicio_desc !== false ? $inicio_desc : strlen($descricao_completa);

            $bloco_espec = substr(
                $descricao_completa,
                $inicio_espec + strlen("--- ESPECIFICAÇÕES ---"),
                $fim_espec - ($inicio_espec + strlen("--- ESPECIFICAÇÕES ---"))
            );

            $linhas = explode("\n", trim($bloco_espec));
            foreach ($linhas as $linha) {
                if (!empty(trim($linha))) {
                    $especificacoes[] = trim($linha);
                }
            }
        }

        // Build product object
        $resultado[] = [
            'id' => $produto['id'],
            'nome' => $produto['nome'],
            'preco' => (float)$produto['preco'],
            'desconto' => (int)$produto['desconto'],
            'categoria_nome' => $produto['categoria_nome'], // Changed from categoria to categoria_nome
            'estoque' => (int)$produto['estoque'],
            'descricao_curta' => $descricao_texto, // Texto limpo da descrição
            'caracteristicas' => $caracteristicas,
            'especificacoes' => $especificacoes,
            'imagens' => $imagens,
            'status' => $produto['status']
            // Removed criado_em
        ];
    }

    echo json_encode([
        'sucesso' => true,
        'quantidade' => count($resultado),
        'dados' => $resultado
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['sucesso' => false, 'erro' => 'Erro interno do servidor: ' . $e->getMessage()]);
}
