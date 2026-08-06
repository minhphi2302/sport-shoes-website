<?php
$html = file_get_contents("http://localhost/products?gender=female");
if (strpos($html, 'id="gender_nu" checked') !== false || strpos($html, 'id="gender_nu"  checked') !== false) {
    echo "Nữ is checked in HTTP response!\n";
} else {
    echo "Nữ is NOT checked in HTTP response!\n";
}
if (strpos($html, 'id="gender_nam" checked') !== false || strpos($html, 'id="gender_nam"  checked') !== false) {
    echo "Nam is checked in HTTP response!\n";
} else {
    echo "Nam is NOT checked in HTTP response!\n";
}
