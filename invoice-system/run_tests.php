<?php

/**
 * Simple Test Runner
 *
 * Runs all tests in the tests/ directory
 * No external dependencies required
 */

use InvoiceSystem\Tests\InvoiceTest;

// Include source files
require_once __DIR__ . '/src/Invoice.php';
require_once __DIR__ . '/src/InvoiceCalculator.php';
require_once __DIR__ . '/src/PDFGenerator.php';


echo "\n";
echo "╔════════════════════════════════════════════════════════════╗\n";
echo "║           INVOICE SYSTEM - TEST RUNNER                     ║\n";
echo "╚════════════════════════════════════════════════════════════╝\n";
echo "\n";

// Check PHP version
if (version_compare(PHP_VERSION, '7.4.0', '<')) {
    echo "ERROR: PHP 7.4 or higher required. You have: " . PHP_VERSION . "\n";
    exit(1);
}

echo "PHP Version: " . PHP_VERSION . " ✓\n";
echo "Working Directory: " . getcwd() . "\n";
echo "\n";

// Include test file
$testFile = __DIR__ . '/tests/InvoiceTest.php';

if (!file_exists($testFile)) {
    echo "ERROR: Test file not found: $testFile\n";
    exit(1);
}

require_once $testFile;

// Run tests
try {
    $test = new InvoiceTest();
    $success = $test->runAll();

    echo "\n";
    if ($success) {
        echo "╔════════════════════════════════════════════════════════════╗\n";
        echo "║                  ALL TESTS PASSED ✓                        ║\n";
        echo "╚════════════════════════════════════════════════════════════╝\n";
        exit(0);
    } else {
        echo "╔════════════════════════════════════════════════════════════╗\n";
        echo "║                  SOME TESTS FAILED ✗                       ║\n";
        echo "╚════════════════════════════════════════════════════════════╝\n";
        echo "\n";
        echo "Fix the failing tests and run again.\n";
        exit(1);
    }
} catch (Exception $e) {
    echo "\n";
    echo "╔════════════════════════════════════════════════════════════╗\n";
    echo "║                    FATAL ERROR ✗                           ║\n";
    echo "╚════════════════════════════════════════════════════════════╝\n";
    echo "\n";
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "\n";
    exit(1);
}
