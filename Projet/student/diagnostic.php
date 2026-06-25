<?php
// Diagnostic endpoint disabled in production for security.
// It previously exposed environment variables, DB credentials, and internal table names.
// Re-enable locally only during development by uncommenting below.

http_response_code(404);
header('Content-Type: text/plain; charset=utf-8');
echo "Not found.\n";
