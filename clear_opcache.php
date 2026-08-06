<?php
if (function_exists('opcache_reset')) {
    opcache_reset();
    echo "OPCache reset successfully.\n";
} else {
    echo "OPCache is not enabled.\n";
}
