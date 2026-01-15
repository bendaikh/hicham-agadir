<?php

/**
 * Laravel - A PHP Framework For Web Artisans
 * 
 * This file serves as the entry point for shared hosting environments
 * where the document root cannot be set to the public folder.
 * It redirects all requests to the public/index.php file.
 */

// Redirect to public folder
require __DIR__.'/public/index.php';
