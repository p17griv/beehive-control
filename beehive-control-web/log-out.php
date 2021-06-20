<?php

// Check if no one is logged in
if (isset($_COOKIE["user"])) {
    setcookie("user", "", time() - 3600, "/"); // Delete the cookie with the user's id as value
}

// Redirect to index.php
header('Location: index.php');
