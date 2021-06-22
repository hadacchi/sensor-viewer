<?php
header('Content-type: text/csv; charset=utf-8');
// private parameters
require_once './dbattr.inc.php';

$pdo = new PDO(
    'mysql:host='.$dbattr['host'].';dbname='.$dbattr['dbname'],
    $dbattr['user'],
    $dbattr['pass']);
// the number of records for a day is 24*60/10=144 records.
// to read latest 144 records, order by desc + limit 144

$sql = $pdo->prepare("SELECT DATE_FORMAT(t, '%Y-%m-%d %H:00:00') AS m, host, part, value FROM ".$dbattr['table']." ORDER BY m DESC LIMIT 2880");
$sql->execute();

$out = array();
$parts = array();

// dygraphs accept time series csv order by asc, thus reverse records order by desc.
foreach(array_reverse($sql->fetchAll()) as $row) {
    // 2-dimensional array
    $out[$row['m']][$row['host'].' '.$row['part']] = $row['value'];
    $parts[$row['host'].' '.$row['part']] = 1;

    #echo $row['host'], ',', $row['part'], ',', $row['m'], ',', $row['value'], "\n";
}

ksort($parts);

// header
echo 't,';
foreach (array_keys($parts) as $key) {
    echo $key, ',';
}

// body
ksort($out);
$i = 0;
foreach ($out as $t => $row) {
    if ($i > 10) { break;}
    echo "\n",$t;
    foreach (array_keys($parts) as $key) {
        echo ',';
        if (array_key_exists($key, $row)) {
            echo $row[$key];
        }
    }
}

?>

