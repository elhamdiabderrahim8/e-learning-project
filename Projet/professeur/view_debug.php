<?php
echo "<pre>";
if (file_exists('/tmp/session_debug.txt')) {
    echo htmlspecialchars(file_get_contents('/tmp/session_debug.txt'));
} else {
    echo "No debug log yet.";
}
echo "</pre>";
