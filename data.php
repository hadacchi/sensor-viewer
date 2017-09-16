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
// the number of records for a day is 24*60/10=144 records.
// to read latest 144 records, order by desc + limit 144
$sql = $pdo->prepare("SELECT DATE_FORMAT(t, '%Y-%m-%d %H:%i') AS m, AVG(val) AS value FROM ".$dbattr['table']." GROUP BY SUBSTRING(m, 1, 15) ORDER BY m DESC LIMIT 576");
$sql->execute();

// dygraphs accept time series csv order by asc, thus reverse records order by desc.
foreach(array_reverse($sql->fetchAll()) as $row) {
    echo $row['m'], ',', $row['value'], "\n";
}
?>
