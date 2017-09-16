<?php
header('Content-type: text/csv; charset=utf-8');
// private parameters
require_once '../../dbattr.inc.php';
?>
t,value
<?php
$pdo = new PDO(
    'mysql:host='.$dbattr['host'].';dbname='.$dbattr['dbname'],
    $dbattr['user'],
    $dbattr['pass']);
// 1日分=24*60/10=144 records
// 最新1日の取得 = order by desc + limit 144
$sql = $pdo->prepare("SELECT DATE_FORMAT(t, '%Y-%m-%d %H:%i') AS m, AVG(val) AS value FROM ".$dbattr['table']." GROUP BY SUBSTRING(m, 1, 15) ORDER BY m DESC LIMIT 576");
$sql->execute();

// dygraphsには時系列はascで渡さないといけない
foreach(array_reverse($sql->fetchAll()) as $row) {
    echo $row['m'], ',', $row['value'], "\n";
}
?>
