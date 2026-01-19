<?php

use InvoiceSystem\Invoice;

/**
 * InvoiceCalculator - Helper class for invoice calculations
 *
 * Static utility methods for business logic
 * Client keeps changing their mind on requirements...
 */
class InvoiceCalculator
{
    /**
     * Calculate tax for an invoice
     *
     * @param float $subtotal The subtotal before tax
     * @param string $region Region code (e.g., "US-CA", "CA-ON")
     * @return float Tax amount
     */
    public static function calculateTax($subtotal, $region = 'US-CA'): float
    {
        $taxRate = 0.10;

        $taxData = json_decode(file_get_contents('data/tax_rates.json'), true);
        $region = explode('-', $region);
        $country = $region[0];
        $state = $region[1] ?? '';

        if (isset($taxData[$country])) {
            if ($state && isset($taxData[$country][$state])) {
                $taxRate = $taxData[$country][$state];
            } elseif (isset($taxData[$country]['default'])) {
                $taxRate = $taxData[$country]['default'];
            }
        }

        return round($subtotal * $taxRate, 2);
    }

    /**
     * Apply business rules to an invoice
     *
     * Rules from client (received via email last Thursday):
     * 1. Orders over $1000 get automatic 5% discount
     * 2. BUT discount should NOT apply to items marked as "sale" items
     * 3. How do we even track which items are on sale??
     * 4. Does the $1000 include tax or not?? (Waiting for response)
     *
     * Client keeps changing their mind on this feature
     * Started implementation 3 times, gave up
     *
     * @param Invoice $invoice
     * @return Invoice Modified invoice
     */
    public static function applyBusinessRules(Invoice $invoice): Invoice
    {
        // Need to figure out requirements first

        // Pseudo-code for what they MIGHT want:
        // if (invoice total > 1000 && !has_sale_items) {
        //     apply 5% discount
        // }

        // Problems:
        // 1. How to identify sale items? Add a flag to item array?
        // 2. Does discount apply before or after tax?
        // 3. Can discounts stack with other discounts?
        // 4. What if they return items - does discount get recalculated?

        // For now, just return the invoice unchanged
        // Need meeting with client to clarify

        return $invoice;
    }

    /**
     * Calculate line item total
     * This one actually works correctly!
     *
     * @param array $item Item with price and quantity
     * @return float Line item total
     */
    public static function calculateLineItem($item): float
    {
        $price = $item['price'];
        $quantity = $item['quantity'];

        return $price * $quantity;
    }

    /**
     * Format currency for display
     * Quick helper I added
     *
     * @param float $amount
     * @return string Formatted currency
     */
    public static function formatCurrency($amount): string
    {
        return '$' . number_format($amount, 2);
    }

    /**
     * Validate invoice data
     * Started but didn't finish
     *
     * Should check:
     * - No negative prices
     * - No negative quantities
     * - Customer name not empty
     * - At least one item
     * - etc.
     */
    public static function validateInvoice($invoice): array
    {
        $errors = [];

        $customer = $invoice->getCustomer();
        if (empty($customer) || trim($customer) === '') {
            $errors[] = 'Customer name cannot be empty';
        }

        // Validate at least one item exists
        $items = $invoice->getItems();
        if (empty($items)) {
            $errors[] = 'Invoice must contain at least one item';
        }

        // Validate each item
        foreach ($items as $index => $item) {
            $itemNum = $index + 1;

            // Check if item has required fields
            if (!isset($item['name']) || empty(trim($item['name']))) {
                $errors[] = "Item #{$itemNum}: name is required";
            }

            if (!isset($item['price'])) {
                $errors[] = "Item #{$itemNum}: price is required";
            } elseif (!is_numeric($item['price'])) {
                $errors[] = "Item #{$itemNum}: price must be numeric";
            } elseif ($item['price'] < 0) {
                $errors[] = "Item #{$itemNum}: price cannot be negative";
            }

            // Check quantity field (handle both 'quantity' and 'qty')
            $quantity = isset($item['quantity']) ? $item['quantity'] : (isset($item['qty']) ? $item['qty'] : null);

            if ($quantity === null) {
                $errors[] = "Item #{$itemNum}: quantity is required";
            } elseif (!is_numeric($quantity)) {
                $errors[] = "Item #{$itemNum}: quantity must be numeric";
            } elseif ($quantity <= 0) {
                $errors[] = "Item #{$itemNum}: quantity must be greater than zero";
            }
        }

        return $errors;
    }
}
