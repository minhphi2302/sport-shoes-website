<?php
$html = file_get_contents("http://localhost/sport-shoes-website-main/public/products?gender=female");
if ($html === false) {
    echo "HTTP request failed!\n";
} else {
    echo "Response length: " . strlen($html) . "\n";
    if (strpos($html, 'id="gender_nu" checked') !== false || strpos($html, 'id="gender_nu"  checked') !== false) {
        echo "Nữ is checked!\n";
    }
    if (strpos($html, 'id="gender_nam" checked') !== false || strpos($html, 'id="gender_nam"  checked') !== false) {
        echo "Nam is checked!\n";
    }
    
    // Check if it's the full page or just the grid
    if (strpos($html, '<html') !== false) {
        echo "It returned FULL PAGE HTML!\n";
    } else {
        echo "It returned GRID ONLY HTML!\n";
    }
}
