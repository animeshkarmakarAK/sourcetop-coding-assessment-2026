<?php

namespace InvoiceSystem;

use BlakvGhost\PHPValidator\Validator;
use Exception;
use Ramsey\Uuid\Uuid;

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
        $this->id = self::generateInvoiceId();
        $this->createdAt = date('Y-m-d H:i:s');
    }

    private static function generateInvoiceId(): string
    {
        return Uuid::uuid4()->toString();
    }

    /**
     * Add an item to the invoice
     * Note: Make sure to use consistent naming!
     */
    public function addItem($name, $price, $quantity): void
    {
        $data = [
            'name' => $name,
            'price' => $price,
            'quantity' => $quantity
        ];
        $validator = new Validator(
            $data,
            [
                'name' => 'required|string',
                'price' => 'required|numeric|min:0',
                'quantity' => 'required|numeric|min:1'
            ],
            [
                'name.required' => 'Item name is required',
                'price.required' => 'Item price is required',
                'price.numeric' => 'Item price must be a number',
                'price.min' => 'Item price cannot be negative',
                'quantity.required' => 'Item quantity is required',
                'quantity.numeric' => 'Item quantity must be a number',
                'quantity.min' => 'Item quantity must be at least 1'
            ]
        );
        if (!$validator->validated()) {
            $errors = $validator->getErrors();
            throw new Exception("Invalid item data: " . implode('; ', $errors));
        }

        $this->items[] = $data;
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
    public function getId(): string
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
     * @return bool
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
