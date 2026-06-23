<?php
echo "<pre>";
if (file_exists('session_debug.txt')) {
    echo htmlspecialchars(file_get_contents('session_debug.txt'));
} else {
    echo "No debug log yet.";
}
echo "</pre>";
