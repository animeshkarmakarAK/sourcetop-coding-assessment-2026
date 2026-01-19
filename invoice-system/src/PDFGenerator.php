<?php

namespace InvoiceSystem;

use Dompdf\Dompdf;
use Exception;
use InvoiceSystem\Invoice;

/**
 * PDFGenerator - Generate PDF invoices
 *
 * Status: NOT IMPLEMENTED
 *
 * UPDATE (Monday morning): Policy changed - Composer packages are now APPROVED!
 * You may use any PDF library: FPDF, TCPDF, Dompdf, or others.
 *
 * Previous blocker (resolved):
 * - Was blocked on "no external libraries" policy
 * - Policy has been updated - external libraries now allowed
 * - Can proceed with implementation using Composer packages
 */
class PDFGenerator
{
    /**
     * Generate PDF from invoice
     *
     * UPDATE (Monday): Composer packages are now APPROVED!
     *
     * Suggested approaches:
     * - FPDF: Lightweight, simple API
     * - TCPDF: More features, HTML support
     * - Dompdf: HTML/CSS to PDF conversion
     *
     * Requirements:
     * - Generate PDF from invoice data
     * - Include all invoice details (items, totals, tax, etc.)
     * - Return file path or PDF content
     *
     * @param Invoice $invoice
     * @return string PDF file path or content
     * @throws Exception Currently not implemented
     */
    public function generatePDF(Invoice $invoice): string
    {
        try {
            //create storage/invoices directory if not exists
            $invoiceDir = __DIR__ . '/../storage/invoices/';

            if (!is_dir($invoiceDir)) {
                mkdir($invoiceDir, 0777, true);
            }
            $invoiceId = $invoice->getId();
            $pdfPath = $invoiceDir . 'invoice_' . $invoiceId . '.pdf';

            if (file_exists($pdfPath)) {
                return $pdfPath;
            }

            $html = $this->generateHTML($invoice);
            $dompdf = new Dompdf();
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();
            file_put_contents($pdfPath, $dompdf->output());

            return $pdfPath;
        } catch (\Exception $e) {
            throw new Exception("PDF generation failed: " . $e->getMessage());
        }
    }

    /**
     * Generate HTML version of invoice
     * Started this as potential workaround
     *
     * Idea: Generate nice HTML, client can print to PDF from browser?
     * Not ideal but might be acceptable
     *
     * @param Invoice $invoice
     * @return string HTML content
     */
    private function generateHTML($invoice)
    {
        // Basic template - would need styling
        $html = '<html><head><title>Invoice</title></head><body>';
        $html .= '<h1>Invoice #' . $invoice->getId() . '</h1>';
        $html .= '<p>Customer: ' . htmlspecialchars($invoice->getCustomer()) . '</p>';
        $html .= '<table border="1">';
        $html .= '<tr><th>Item</th><th>Price</th><th>Quantity</th><th>Total</th></tr>';

        foreach ($invoice->getItems() as $item) {
            $quantity = $item['quantity'];
            $lineTotal = $item['price'] * $quantity;

            $html .= '<tr>';
            $html .= '<td>' . htmlspecialchars($item['name']) . '</td>';
            $html .= '<td>$' . number_format($item['price'], 2) . '</td>';
            $html .= '<td>' . $quantity . '</td>';
            $html .= '<td>$' . number_format($lineTotal, 2) . '</td>';
            $html .= '</tr>';
        }

        $html .= '</table>';
        $html .= '<p><strong>Total: $' . number_format($invoice->getTotal(), 2) . '</strong></p>';
        $html .= '</body></html>';

        return $html;
    }

    /**
     * Export invoice as HTML (workaround for PDF)
     * At least this works...
     *
     * @param Invoice $invoice
     * @return string HTML file path
     */
    public function exportHTML($invoice)
    {
        $html = $this->generateHTML($invoice);
        $filename = 'invoice_' . $invoice->getId() . '.html';
        file_put_contents($filename, $html);
        return $filename;
    }

    /**
     * Attempted to write raw PDF - gave up after 2 hours
     * Keeping this as evidence of how hard this is
     */
    private function generateRawPDF_ABANDONED($invoice)
    {
        // PDF header
        // %PDF-1.4
        // Then you need:
        // - Catalog object
        // - Pages object
        // - Page object
        // - Content stream
        // - Font definitions
        // - Cross-reference table
        // - Trailer
        //
        // Each object has specific byte offsets that need to be calculated
        // Text positioning uses PostScript-like commands
        // Fonts need to be embedded or referenced correctly
        //
        // This is insane to do by hand for a simple invoice
        // Would take days to get right
        //
        // ABANDONED THIS APPROACH

        return "nope nope nope";
    }
}
