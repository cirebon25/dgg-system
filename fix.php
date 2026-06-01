<?php $models = ["DeploymentSparepart", "PartBorrowing", "PartBorrowingHeader", "PartBorrowingItem", "PartReplacement", "PartReturn", "SparepartEntry", "TechnicianStock", "TechnicianStockHistory"];
foreach ($models as $m) {
    $p = "app/Models/$m.php";
    $c = file_get_contents($p);
    if (strpos($c, "    use UppercaseAttributes;") === false) {
        $c = preg_replace("/(class \w+ extends Model[^{]*{)/", "$1\n    use UppercaseAttributes;", $c, 1);
        file_put_contents($p, $c);
        echo "Fixed: $m\n";
    } else {
        echo "OK: $m\n";
    }
}
