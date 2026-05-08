<?php
$db = new mysqli('localhost','root','','sipp_pa_amuntai');
echo "=== perkara_document_siap ===\n";
$r = $db->query('DESCRIBE perkara_document_siap');
if($r) { while($row=$r->fetch_assoc()) echo $row['Field'].' | '.$row['Type']."\n"; }
else { echo "Table not found\n"; }
echo "\n=== Tables with 'document' ===\n";
$r2 = $db->query("SHOW TABLES LIKE '%document%'");
if($r2) { while($row=$r2->fetch_row()) echo $row[0]."\n"; }
echo "\n=== Tables with 'dokumen' ===\n";
$r3 = $db->query("SHOW TABLES LIKE '%dokumen%'");
if($r3) { while($row=$r3->fetch_row()) echo $row[0]."\n"; }
