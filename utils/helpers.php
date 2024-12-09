<?php
function sanitizeInput($data) {
    return htmlspecialchars(strip_tags($data));
}

function formatCurrency($amount) {
    return "$" . number_format($amount, 2);
}
?>
