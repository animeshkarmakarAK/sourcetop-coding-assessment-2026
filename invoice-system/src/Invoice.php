<?php

namespace InvoiceSystem;

use Exception;

/**
 * Invoice Class
 *
 * Handles invoice creation and management
 * Started: 2 weeks ago
 * Last modified: Friday (was in a hurry)
 */
class Invoice
{
    private $customer;
    private $items = [];
    private $discount = 0;
    private $id;
    private $createdAt;

    public const FILEPATH = 'data/invoices.json';

    public function __construct($customerName)
    {
        $this->customer = $customerName;
        $this->id = self::generateNextId();
        $this->createdAt = date('Y-m-d H:i:s');
    }

    /**
     * Generate the next sequential invoice ID
     * Reads existing invoices and returns the next ID in ascending order
     */
    private static function generateNextId(): int
    {
        $maxId = 0;

        if (file_exists(self::FILEPATH)) {
            $contents = file_get_contents(self::FILEPATH);
            $invoices = json_decode($contents, true);

            if (!empty($invoices)) {
                // Handle both single invoice and array of invoices
                if (isset($invoices['id'])) {
                    $invoices = [$invoices];
                }

                foreach ($invoices as $invoice) {
                    if (isset($invoice['id']) && is_numeric($invoice['id'])) {
                        $maxId = max($maxId, (int)$invoice['id']);
                    }
                }
            }
        }

        return $maxId + 1;
    }

    /**
     * Add an item to the invoice
     * Note: Make sure to use consistent naming!
     */
    public function addItem($name, $price, $quantity): void
    {
        // No validation yet - add later?
        $this->items[] = [
            'name' => $name,
            'price' => $price,
            'quantity' => $quantity,
        ];
    }

    /**
     * Calculate total
     * BUG: This doesn't match up with addItem() - need to fix
     */
    public function getTotal(): float
    {
        $total = 0;

        foreach ($this->items as $item) {
            $total += $item['price'] * $item['quantity'];
        }
        return $total - $this->discount;
    }

    /**
     * Apply discount to invoice
     * TODO: Should discounts apply before or after tax?
     * TODO: Client hasn't decided on the business rules yet
     */
    public function applyDiscount($percent)
    {
        // Started implementing but not sure about requirements
        // throw new Exception("Not implemented - waiting on client clarification");

        // Trying basic implementation but commented out until we get clarity
        // $subtotal = $this->getTotal();
        // $this->discount = $subtotal * ($percent / 100);

        // For now just throw exception
        throw new Exception("Discount feature incomplete - need business rules from client");
    }

    /**
     * Get invoice ID
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Get customer name
     */
    public function getCustomer(): string
    {
        return $this->customer;
    }

    /**
     * Get items array
     */
    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * Convert invoice to array for JSON serialization
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'customer' => $this->customer,
            'items' => $this->items,
            'discount' => $this->discount,
            'total' => $this->getTotal(),
            'created_at' => $this->createdAt
        ];
    }

    /**
     * Save invoice to file
     * FIXME: This overwrites everything! Need to fix but running out of time
     * Should APPEND to the file, not replace it
     */
    public function saveToFile(): bool
    {
        $newData = $this->toArray();
        $existingData = [];

        if (file_exists(self::FILEPATH)) {
            $existingContent = file_get_contents(self::FILEPATH);
            $existingData = json_decode($existingContent, true) ?: [];
        }

        $existingData[] = $newData;

        file_put_contents(self::FILEPATH, json_encode($existingData, JSON_PRETTY_PRINT));

        return true;
    }

    /**
     * Load invoice from file by ID
     * Started this but didn't finish testing it
     */
    public static function loadFromFile($id): Invoice
    {
        if (!file_exists(self::FILEPATH)) {
            throw new Exception("Invoice file not found");
        }

        $contents = file_get_contents(self::FILEPATH);
        $invoices = json_decode($contents, true);

        // Handle both single invoice and array of invoices
        // (since saveToFile is broken and only saves one)
        if (isset($invoices['id'])) {
            $invoices = [$invoices];
        }

        foreach ($invoices as $invoiceData) {
            if ($invoiceData['id'] == $id) {
                $invoice = new Invoice($invoiceData['customer']);
                $invoice->id = $invoiceData['id'];
                $invoice->discount = $invoiceData['discount'];

                foreach ($invoiceData['items'] as $item) {
                    $quantity = $item['quantity'];
                    $invoice->addItem($item['name'], $item['price'], $quantity);
                }

                return $invoice;
            }
        }

        throw new Exception("Invoice not found: " . $id);
    }
}
