<?php

/**
 * InvoiceCalculator - Helper class for invoice calculations
 *
 * Static utility methods for business logic
 * Client keeps changing their mind on requirements...
 */
class InvoiceCalculator {

    /**
     * Calculate tax for an invoice
     *
     * TODO: Load tax rates from data/tax_rates.json instead of hardcoding
     * Currently just using 10% for everything which is WRONG
     *
     * @param float $subtotal The subtotal before tax
     * @param string $region Region code (e.g., "US-CA", "CA-ON")
     * @return float Tax amount
     */
    public static function calculateTax($subtotal, $region = 'US-CA') {
        // TEMPORARY hardcoded value - need to load from JSON
        // Client said tax rates change frequently so should be in config
        $taxRate = 0.10;

        // TODO: Load from tax_rates.json like this:
        // $taxData = json_decode(file_get_contents('data/tax_rates.json'), true);
        // Parse $region to get country and state
        // Look up actual rate
        // Handle default rates
        //
        // Ran out of time Friday, will fix Monday

        return $subtotal * $taxRate;
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
    public static function applyBusinessRules($invoice) {
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
    public static function calculateLineItem($item) {
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
    public static function formatCurrency($amount) {
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
    public static function validateInvoice($invoice) {
        $errors = [];

        // TODO: Add actual validation logic

        return $errors;
    }
}
