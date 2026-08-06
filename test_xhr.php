<?php
$options = array(
  'http'=>array(
    'method'=>"GET",
    'header'=>"X-Requested-With: XMLHttpRequest\r\n"
  )
);
$context = stream_context_create($options);
$html = file_get_contents("http://localhost/sport-shoes-website-main/public/products?gender=female", false, $context);
if ($html === false) {
    echo "HTTP request failed!\n";
} else {
    echo "Response length: " . strlen($html) . "\n";
    if (strpos($html, '<html') !== false) {
        echo "It returned FULL PAGE HTML!\n";
    } else {
        echo "It returned GRID ONLY HTML!\n";
    }
    
    if (strpos($html, 'Không tìm thấy') !== false) {
        echo "Returns: Không tìm thấy sản phẩm phù hợp\n";
    }
    
    // Does it contain anything about gender?
    if (strpos($html, 'gender_nam') !== false) {
        echo "WARNING: product_grid.php contains gender inputs!!!\n";
    }
}
