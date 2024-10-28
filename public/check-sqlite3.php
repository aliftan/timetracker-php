<?php
// Check if SQLite3 is available
if (class_exists('SQLite3')) {
    echo "SQLite3 is enabled!<br>";
} else {
    echo "SQLite3 is NOT enabled!<br>";
}

// Display loaded extensions
echo "<br>Loaded extensions:<br>";
print_r(get_loaded_extensions());