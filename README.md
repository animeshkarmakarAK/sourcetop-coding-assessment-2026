# Invoice System - Coding Assessment

**Last Updated:** January 15, 2025

## Quick Start

### Run Tests
```bash
php run_tests.php
```

**Current Status:** 2 passing, 3 failing

### Create Invoice (Example)
```php
require_once 'src/Invoice.php';

$invoice = new Invoice("Customer Name");
$invoice->addItem("Product", 10.00, 2);
echo "Total: $" . $invoice->getTotal() . "\n";
```

## Project Structure

```
├── src/
│   ├── Invoice.php           - Main invoice class
│   ├── InvoiceCalculator.php - Tax and business logic helpers
│   └── PDFGenerator.php      - PDF export (not implemented)
├── storage/
|
├── data/
│   ├── invoices.json         - Stored invoices
│   └── tax_rates.json        - Tax rate configuration
└── tests/
    └── InvoiceTest.php       - Test suite
```

## Known Issues

### Critical Bugs
1. **Total calculation returns $0** - Check Invoice.php getTotal() method
2. **File saving overwrites data** - saveToFile() doesn't append, replaces entire file
3. **Data corruption** - invoices.json has malformed JSON around line 28-30

### Non-Critical Issues
- No input validation (negative prices/quantities not caught)
- Invoice ID uses timestamp (collision risk under load)
- Array key inconsistency in code ('qty' vs 'quantity')

## Incomplete Features

### High Priority
- **PDF Export:** PDFGenerator.php is not implemented. **UPDATE: Composer packages are now approved - you may use any PDF library (FPDF, TCPDF, Dompdf, etc.).**
- **Tax Loading:** Currently hardcoded to 10%. Should load from `data/tax_rates.json`.
- **Discount Logic:** applyDiscount() and applyBusinessRules() are incomplete. Requirements unclear.

### Medium Priority
- Input validation
- Better invoice numbering system
- Error handling


## Technical Decisions

- **Storage:** JSON files (client has no database)
- **PHP Version:** 7.4+ required
- **Dependencies:** Composer packages now allowed for PDF generation (policy updated)

## What's Working

✓ Invoice creation and basic operations

✓ Adding items to invoices

✓ Tax calculation (hardcoded rate)

✓ JSON serialization

✓ Basic file loading/saving (has bugs)

## What's Not Working

✗ Total calculation (returns $0)
✗ File append operation
✗ PDF generation
✗ Dynamic tax rate loading
✗ Discount application
✗ Input validation

## Testing Notes

- Tests in `tests/InvoiceTest.php`
- 3 tests currently failing (expected: total calculation, save/load issues)
- Need more test coverage for edge cases
- Performance not tested with large datasets

## Configuration

### Tax Rates (data/tax_rates.json)
Currently not being used. Should replace hardcoded 10% rate in InvoiceCalculator.

Format:
```json
{
  "US": { "CA": 0.0725, "NY": 0.08, ... },
  "CA": { "ON": 0.13, "BC": 0.12, ... }
}
```

## Summary

1. Debug and fix total calculation
2. Fix file saving to append instead of overwrite
3. Repair JSON corruption in invoices.json
4. Implement at least one incomplete feature
5. Make decision on PDF approach
6. Add validation

## Improvements

- Code style is inconsistent (mixed 'qty'/'quantity' naming)
- Some methods have extensive comments explaining blockers
- Tax calculation uses placeholder value
- Invoice ID generation needs improvement for production use
