<?php

namespace InvoiceSystem\Tests;

use Exception;
use InvoiceCalculator;
use InvoiceSystem\Invoice;

/**
 * Basic tests for Invoice system
 *
 * Note: Only had time to write basic tests
 * Need more coverage (edge cases, validation, error handling, etc.)
 * Some tests are failing - not sure if tests are wrong or code is wrong??
 *
 * Run with: php run_tests.php
 */
class InvoiceTest
{
    private $testsPassed = 0;
    private $testsFailed = 0;
    private $failures = [];

    /**
     * Run all tests
     */
    public function runAll()
    {
        echo "Running Invoice Tests...\n";
        echo str_repeat("=", 50) . "\n\n";

        $this->test_create_invoice();
        $this->test_calculate_total();
        $this->test_add_multiple_items();
        $this->test_save_and_load();
        $this->test_tax_calculation();

        echo "\n" . str_repeat("=", 50) . "\n";
        echo "Tests Passed: " . $this->testsPassed . "\n";
        echo "Tests Failed: " . $this->testsFailed . "\n";

        if ($this->testsFailed > 0) {
            echo "\nFailures:\n";
            foreach ($this->failures as $failure) {
                echo "  - " . $failure . "\n";
            }
        }

        return $this->testsFailed === 0;
    }

    private function test_create_invoice()
    {
        $invoice = new Invoice("Test Customer");

        $this->assert(
            $invoice->getCustomer() === "Test Customer",
            "test_create_invoice",
            "Customer name should match"
        );
    }

    private function test_calculate_total()
    {
        $invoice = new Invoice("Test Customer");
        $invoice->addItem("Test Item", 10.00, 2);

        $expected = 20.00;
        $actual = $invoice->getTotal();

        $this->assert(
            $actual === $expected,
            "test_calculate_total",
            "Total should be $20.00, got $" . number_format($actual, 2)
        );
    }

    private function test_add_multiple_items()
    {
        $invoice = new Invoice("Test Customer");
        $invoice->addItem("Item 1", 10.00, 2);
        $invoice->addItem("Item 2", 15.00, 3);
        $invoice->addItem("Item 3", 5.00, 1);

        $expected = 20.00 + 45.00 + 5.00; // = 70.00
        $actual = $invoice->getTotal();

        $this->assert(
            $actual === $expected,
            "test_add_multiple_items",
            "Total should be $70.00, got $" . number_format($actual, 2)
        );
    }

    private function test_save_and_load()
    {
        $testFile = __DIR__ . '/../data/invoices.json';

        // Clean up first
        if (file_exists($testFile)) {
            unlink($testFile);
        }

        // Create and save first invoice
        $invoice1 = new Invoice("Customer 1");
        $invoice1->addItem("Item A", 100.00, 1);
        $invoice1->saveToFile($testFile);

        // Create and save second invoice
        $invoice2 = new Invoice("Customer 2");
        $invoice2->addItem("Item B", 200.00, 1);
        $invoice2->saveToFile($testFile);


        // Try to load first invoice - this will fail
        // because saveToFile overwrites everything
        try {
            $loaded = Invoice::loadFromFile($invoice1->getId(), $testFile);
            $this->assert(
                $loaded->getCustomer() === "Customer 1",
                "test_save_and_load",
                "Should be able to load first invoice"
            );
        } catch (Exception $e) {
            $this->assert(
                false,
                "test_save_and_load",
                "Failed to load invoice: " . $e->getMessage()
            );
        }

        // Clean up
        if (file_exists($testFile)) {
            unlink($testFile);
        }
    }

    private function test_tax_calculation()
    {
        $subtotal = 100.00;
        $tax = InvoiceCalculator::calculateTax($subtotal, 'US-CA');

        // Hardcoded to 10% currently
        $expected = 10.00;

        $this->assert(
            $tax === $expected,
            "test_tax_calculation",
            "Tax should be $10.00, got $" . number_format($tax, 2)
        );
    }

    /**
     * Simple assertion helper
     */
    private function assert($condition, $testName, $message)
    {
        if ($condition) {
            $this->testsPassed++;
            echo "✓ " . $testName . "\n";
        } else {
            $this->testsFailed++;
            echo "✗ " . $testName . " - " . $message . "\n";
            $this->failures[] = $testName . ": " . $message;
        }
    }
}

// Don't auto-run if included by run_tests.php
if (basename(__FILE__) === basename($_SERVER['PHP_SELF'])) {
    $test = new InvoiceTest();
    $success = $test->runAll();
    exit($success ? 0 : 1);
}
