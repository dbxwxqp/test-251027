    <h2>Прочитать файл mock_data.json (он находится в этой же папке).</h2>
<?php
$file_path = __DIR__ . '/mock_data.json';
$file_data = file_get_contents($file_path);
$aData = json_decode($file_data, true);
print('<pre>' . print_r($aData, true) . '</pre>');
?>
    <hr>

    <h2>Вывести всех пользователей в удобном формате.</h2>
<?php
foreach($aData['users'] as $user){
    echo <<<HD
<div class="users" title="User #{$user['ID']}">
    <a href="mailto:{$user['EMAIL']}">{$user['NAME']}</a>
</div>
        
HD;
}
?>
    <hr>

    <h2>Вывести все сделки со статусом <span style="opacity: .1">WON или</span> LOSE.</h2>
<?php
foreach($aData['deals'] as $deal) {
    if($deal['STATUS'] != 'LOSE') continue;

    echo <<<HD
<div class="deals" title="Deal #{$deal['ID']}">
    <strong>{$deal['TITLE']}</strong> / <span>{$deal['AMOUNT']}</span>
</div>
        
HD;
}

