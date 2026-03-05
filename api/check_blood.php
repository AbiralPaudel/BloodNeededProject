<?php
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
    exit;
}

require_once __DIR__ . '/../includes/dbh.inc.php';

$blood = trim($_POST['blood'] ?? '');
if (!$blood) {
    echo json_encode(['status' => 'error', 'message' => 'Missing blood group']);
    exit;
}

// Normalize common inputs to canonical form
    $map = [
    // A
    'a+' => 'AP', 'a-plus' => 'AP', 'a positive' => 'AP', 'a_pos' => 'AP', 'ap' => 'AP', 'aplus' => 'AP', 'a pos' => 'AP', 'apos' => 'AP',
    'a-' => 'AN', 'a-negative' => 'AN', 'a negative' => 'AN', 'an' => 'AN', 'aminus' => 'AN', 'a neg' => 'AN', 'aneg' => 'AN',
    // B
    'b+' => 'BP', 'b-plus' => 'BP', 'b positive' => 'BP', 'bp'=>'BP','bplus'=>'BP','b pos'=>'BP','bpos'=>'BP',
    'b-' => 'BN', 'b-negative' => 'BN','bn'=>'BN','bminus'=>'BN','b neg'=>'BN','bneg'=>'BN',
    // AB
    'ab+' => 'ABP', 'ab-plus' => 'ABP', 'ab positive' => 'ABP','abp'=>'ABP','abplus'=>'ABP','ab pos'=>'ABP','abpos'=>'ABP',
    'ab-' => 'ABN', 'ab-negative' => 'ABN','abn'=>'ABN','abminus'=>'ABN','ab neg'=>'ABN','abneg'=>'ABN',
    // O
    'o+' => 'OP', 'o-plus' => 'OP', 'o positive' => 'OP','op'=>'OP','oplus'=>'OP','o pos'=>'OP','opos'=>'OP','o positive'=>'OP','o plus'=>'OP',
    'o-' => 'ON', 'o-negative' => 'ON','on'=>'ON','ominus'=>'ON','o neg'=>'ON','oneg'=>'ON','o negative'=>'ON',
    // variants without symbols
    'oneg'=>'ON','oplus'=>'OP','aplus'=>'AP','aminus'=>'AN'
];

$key = strtolower(str_replace([' ', '_'], '-', $blood));
$col = $map[$key] ?? null;
if (!$col) {
    // try upper-case raw like 'A+'
    $raw = strtoupper($blood);
    $alt = str_replace(['+','-'], ['P','N'], $raw);
    $col = $map[strtolower($alt)] ?? null;
}

if (!$col) {
    echo json_encode(['status' => 'error', 'message' => 'Unknown blood group']);
    exit;
}

try {
    $query = "SELECT {$col} AS cnt FROM blood WHERE id = 1 LIMIT 1";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $count = $row['cnt'] ?? 0;

    echo json_encode(['status' => 'success', 'blood' => $blood, 'group' => $col, 'count' => (int)$count]);
    exit;
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'DB error']);
    exit;
}

?>
