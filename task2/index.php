<h2>Прочитать файл product.csv (он находится в этой же папке)</h2>
<?php
$file_path = __DIR__ . '/product.csv';
$file_data = file_get_contents($file_path);
$aResult = [];
$aRows = explode("\r\n", $file_data);
foreach ($aRows as $k => $v) {
    if($k == 0) continue;
    $aResult[$k] = explode(';', $v);
}
print('<pre>' . print_r($aResult, true) . '</pre>');
?>
    <hr>
    <h2>Написать SQL-запрос через PDO</h2>
<?php
try {
    $pdo = new PDO('sqlite:db.sqlite');
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


    $pdo->beginTransaction(); // transaction start

    $updatedCount = 0;
    $addedCount = 0;
    foreach ($aResult as $product) {
        list($name, $art, $price, $quantity) = $product;

        # < Экранирование
        $name = trim($name);
        $art = trim($art);
        $price = floatval($price);
        $quantity = intval($quantity);
        # > Экранирование

        $query_check = $pdo->prepare("SELECT COUNT(*) FROM product WHERE name = ? AND art = ?");
        $query_check->execute([$name, $art]);
        if ($query_check->fetchColumn() > 0) {
            $query_update = $pdo->prepare("UPDATE product SET price = ?, quantity = ? WHERE name = ? AND art = ?");
            $query_update->execute([$price, $quantity, $name, $art]);
            $updatedCount += $query_update->rowCount();
        } else {
            $query_insert = $pdo->prepare("INSERT INTO product (name, art, price, quantity) VALUES (?, ?, ?, ?)");
            $query_insert->execute([$name, $art, $price, $quantity]);
            $addedCount += $query_insert->rowCount();
        }
    }
    $pdo->commit(); // transaction end

    echo <<<HD
<p><strong>Обновлено</strong> {$updatedCount} записей</p>
<p><strong>Добавлено</strong> {$addedCount} записей</p>

HD;

} catch (PDOException $e) {
    if (isset($pdo) && $pdo->inTransaction()) $pdo->rollBack();
    echo "Ошибка базы данных: " . $e->getMessage() . "\n";
} catch (Exception $e) {
    echo "Ошибка: " . $e->getMessage() . "\n";
}