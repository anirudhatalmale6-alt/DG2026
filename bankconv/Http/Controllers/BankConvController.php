<?php

namespace Modules\CIMS_BANKCONV\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\CIMS_BANKCONV\Models\BankConversion;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class BankConvController extends Controller
{
    /**
     * Conversion history listing.
     */
    public function index(Request $request)
    {
        $query = BankConversion::query();

        if ($request->filled('bank_type')) {
            $query->where('bank_type', $request->get('bank_type'));
        }
        if ($request->filled('client_id')) {
            $query->where('client_id', $request->get('client_id'));
        }
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('company_name', 'like', "%{$search}%")
                  ->orWhere('client_code', 'like', "%{$search}%")
                  ->orWhere('account_number', 'like', "%{$search}%");
            });
        }

        $conversions = $query->orderBy('id', 'desc')->paginate(20);

        $clients = DB::table('client_master')
            ->select('client_id', 'company_name', 'client_code')
            ->where('is_active', 1)
            ->orderBy('company_name')
            ->get();

        return view('cims_bankconv::bankconv.index', compact('conversions', 'clients'));
    }

    /**
     * Unified bank conversion page.
     * Loads banks with show_in_conversion flag and clients.
     */
    public function convert()
    {
        $clients = DB::table('client_master')
            ->where('is_active', 1)
            ->orderBy('company_name')
            ->get();

        $banks = DB::table('cims_bank_names')
            ->where('show_in_conversion', 1)
            ->where('is_active', 1)
            ->orderBy('bank_name')
            ->get();

        return view('cims_bankconv::bankconv.convert', compact('clients', 'banks'));
    }

    /**
     * AJAX: Parse bank statement text extracted by PDF.js.
     * Routes to the correct parser based on bank_type.
     */
    public function apiParseStatement(Request $request)
    {
        $pages = $request->input('pages', []);
        $bankType = $request->input('bank_type', 'fnb');

        if (empty($pages)) {
            return response()->json(['error' => 'No text data received.'], 400);
        }

        // Route to the correct parser based on bank type
        switch (strtolower($bankType)) {
            case 'fnb':
            case 'first national bank':
                $result = $this->parseFnbText($pages);
                break;
            case 'nedbank':
                $result = $this->parseNedbankText($pages);
                break;
            case 'absa':
                $result = $this->parseAbsaText($pages);
                break;
            case 'capitec bank':
            case 'capitec':
                $result = $this->parseCapitecText($pages);
                break;
            case 'standard bank':
            case 'standard':
                $result = $this->parseStandardText($pages);
                break;
            default:
                return response()->json([
                    'error' => 'Parser for "' . $bankType . '" is not yet available. Currently FNB, Nedbank, ABSA, Capitec, and Standard Bank are supported.'
                ], 400);
        }

        return response()->json($result);
    }

    /**
     * AJAX: Save conversion record to database.
     */
    public function apiSaveConversion(Request $request)
    {
        $data = $request->only([
            'client_id', 'client_code', 'company_name', 'bank_type',
            'account_number', 'statement_period',
            'opening_balance', 'closing_balance',
            'total_credits', 'total_debits',
            'credit_count', 'debit_count', 'transaction_count',
            'original_filename', 'notes',
        ]);

        $data['user_id'] = Auth::id();
        $data['converted_by'] = Auth::user()
            ? Auth::user()->first_name . ' ' . Auth::user()->last_name
            : 'Unknown';

        $conversion = BankConversion::create($data);

        return response()->json([
            'success' => true,
            'id' => $conversion->id,
        ]);
    }

    // =========================================================================
    // FNB PARSER
    // =========================================================================

    /**
     * Parse FNB bank statement text into structured data.
     */
    private function parseFnbText(array $pages): array
    {
        $allText = implode("\n", $pages);
        $lines = explode("\n", $allText);

        $header = $this->parseFnbHeader($lines);
        $transactions = $this->parseFnbTransactions($lines, $header);

        // Calculate totals from parsed transactions
        $totalCredits = 0;
        $totalDebits = 0;
        $creditCount = 0;
        $debitCount = 0;

        foreach ($transactions as $txn) {
            if ($txn['amount'] > 0) {
                $totalCredits += $txn['amount'];
                $creditCount++;
            } else {
                $totalDebits += abs($txn['amount']);
                $debitCount++;
            }
        }

        // Balance check
        $calculatedClosing = $header['opening_balance'] + $totalCredits - $totalDebits;
        $balanceMatch = abs($calculatedClosing - $header['closing_balance']) < 0.02;

        return [
            'header' => $header,
            'transactions' => $transactions,
            'summary' => [
                'transaction_count' => count($transactions),
                'credit_count' => $creditCount,
                'debit_count' => $debitCount,
                'total_credits' => round($totalCredits, 2),
                'total_debits' => round($totalDebits, 2),
                'calculated_closing' => round($calculatedClosing, 2),
                'balance_match' => $balanceMatch,
            ],
        ];
    }

    /**
     * Parse header info from FNB statement text.
     */
    private function parseFnbHeader(array $lines): array
    {
        $header = [
            'account_number' => '',
            'account_holder' => '',
            'branch_code' => '',
            'statement_period' => '',
            'statement_date' => '',
            'opening_balance' => 0,
            'closing_balance' => 0,
        ];

        $fullText = implode(' ', $lines);

        // Account number
        if (preg_match('/(?:Platinum\s+Business\s+Account|Business\s+Account|Cheque\s+Account)\s*:?\s*(\d{8,15})/i', $fullText, $m)) {
            $header['account_number'] = $m[1];
        } elseif (preg_match('/Account\s*(?:No|Number|#)?\s*:?\s*(\d{8,15})/i', $fullText, $m)) {
            $header['account_number'] = $m[1];
        }

        // Branch code
        if (preg_match('/Universal\s+Branch\s+Code\s*:?\s*(\d+)/i', $fullText, $m)) {
            $header['branch_code'] = $m[1];
        }

        // Statement period
        if (preg_match('/Statement\s+Period\s*:?\s*(.+?)(?:Statement\s+Date|$)/i', $fullText, $m)) {
            $header['statement_period'] = trim($m[1]);
        }

        // Statement date
        if (preg_match('/Statement\s+Date\s*:?\s*(\d{1,2}\s+\w+\s+\d{4})/i', $fullText, $m)) {
            $header['statement_date'] = trim($m[1]);
        }

        // Opening balance
        if (preg_match('/Opening\s+Balance\s+([\d,]+\.\d{2})\s*(Cr|Dr)?/i', $fullText, $m)) {
            $val = (float) str_replace(',', '', $m[1]);
            if (isset($m[2]) && strtolower($m[2]) === 'dr') {
                $val = -$val;
            }
            $header['opening_balance'] = $val;
        }

        // Closing balance
        if (preg_match('/Closing\s+Balance\s+([\d,]+\.\d{2})\s*(Cr|Dr)?/i', $fullText, $m)) {
            $val = (float) str_replace(',', '', $m[1]);
            if (isset($m[2]) && strtolower($m[2]) === 'dr') {
                $val = -$val;
            }
            $header['closing_balance'] = $val;
        }

        // Account holder
        foreach ($lines as $line) {
            if (preg_match('/^\*(.+?)$/m', trim($line), $m)) {
                $header['account_holder'] = trim($m[1]);
                break;
            }
        }

        return $header;
    }

    /**
     * Parse transactions from FNB statement text.
     */
    private function parseFnbTransactions(array $lines, array $header): array
    {
        $transactions = [];
        $year = '';

        // Extract year from statement period or date
        if (preg_match('/(\d{4})/', $header['statement_period'] . ' ' . $header['statement_date'], $m)) {
            $year = $m[1];
        }
        if (!$year) {
            $year = date('Y');
        }

        $inTransactionSection = false;

        foreach ($lines as $line) {
            $trimmed = trim($line);

            if ($trimmed === '') continue;

            // Detect transaction section start
            if (preg_match('/Transactions\s+in\s+RAND/i', $trimmed)) {
                $inTransactionSection = true;
                continue;
            }

            // Skip header row
            if (preg_match('/^\s*Date\s+Description\s+Amount\s+Balance/i', $trimmed)) continue;

            // Skip page headers/footers
            if (preg_match('/^Page\s+\d+\s+of\s+\d+/i', $trimmed)) continue;
            if (preg_match('/^Delivery\s+Method/i', $trimmed)) continue;
            if (preg_match('/^EN:EM/i', $trimmed)) continue;
            if (preg_match('/^\d{5,6}$/', $trimmed)) continue;

            // Skip summary section
            if (preg_match('/No\.\s+Credit\s+Transactions/i', $trimmed)) continue;
            if (preg_match('/No\.\s+Debit\s+Transactions/i', $trimmed)) continue;
            if (preg_match('/Turnover\s+for\s+Statement/i', $trimmed)) continue;

            // Skip header info lines
            if (preg_match('/Opening\s+Balance|Closing\s+Balance|Statement\s+Balances|Bank\s+Charges|Interest\s+Rate|Service\s+Fees|Cash\s+Deposit|Cash\s+Handling|Other\s+Fees|Credit\s+Rate|Debit\s+Rate|Inclusive\s+of\s+VAT|Total\s+VAT/i', $trimmed)) continue;
            if (preg_match('/^(P\s*O\s*Box|Street\s*Address|Universal\s*Branch|Lost\s*Cards|Account\s*Enquiries|Fraud|Relationship|Customer\s*VAT|Bank\s*VAT|Statement\s*(Period|Date)|Platinum|Tax\s*Invoice)/i', $trimmed)) continue;
            if (preg_match('/Accrued\s+Bank\s+Charges/i', $trimmed)) continue;

            if (!$inTransactionSection) continue;

            // Match transaction line
            if (preg_match('/^(\d{2}\s+[A-Za-z]{3})\s+(.+)$/', $trimmed, $lineMatch)) {
                $dateStr = trim($lineMatch[1]);
                $rest = trim($lineMatch[2]);

                if (preg_match('/^(.+?)\s+([\d,]+\.\d{2})(Cr)?\s+([\d,]+\.\d{2})(Cr|Dr)\s*(\d+\.\d{2})?$/', $rest, $txnMatch)) {
                    $description = trim($txnMatch[1]);
                    $amount = (float) str_replace(',', '', $txnMatch[2]);
                    $isCredit = !empty($txnMatch[3]);
                    $balance = (float) str_replace(',', '', $txnMatch[4]);
                    $balanceType = $txnMatch[5];

                    if (!$isCredit) {
                        $amount = -$amount;
                    }

                    $fullDate = $this->parseFnbDate($dateStr, $year);

                    $transactions[] = [
                        'date' => $fullDate,
                        'description' => $description,
                        'amount' => $amount,
                        'balance' => ($balanceType === 'Dr') ? -$balance : $balance,
                    ];
                }
            }
        }

        return $transactions;
    }

    /**
     * Parse FNB date format "DD Mon" to "YYYY-MM-DD".
     */
    private function parseFnbDate(string $dateStr, string $year): string
    {
        $months = [
            'jan' => '01', 'feb' => '02', 'mar' => '03', 'apr' => '04',
            'may' => '05', 'jun' => '06', 'jul' => '07', 'aug' => '08',
            'sep' => '09', 'oct' => '10', 'nov' => '11', 'dec' => '12',
        ];

        if (preg_match('/(\d{2})\s+([A-Za-z]{3})/', $dateStr, $m)) {
            $day = $m[1];
            $monthKey = strtolower($m[2]);
            $month = $months[$monthKey] ?? '01';
            return $year . '-' . $month . '-' . $day;
        }

        return $year . '-01-01';
    }

    // =========================================================================
    // NEDBANK PARSER
    // =========================================================================

    /**
     * Parse Nedbank bank statement text into structured data.
     * Nedbank format: separate Debits/Credits columns, DD/MM/YYYY dates,
     * running balance in last column. Uses balance-difference method to
     * determine credit vs debit since column positions are lost in text extraction.
     */
    private function parseNedbankText(array $pages): array
    {
        $allText = implode("\n", $pages);
        $lines = explode("\n", $allText);

        $header = $this->parseNedbankHeader($lines);
        $transactions = $this->parseNedbankTransactions($lines, $header);

        // Calculate totals
        $totalCredits = 0;
        $totalDebits = 0;
        $creditCount = 0;
        $debitCount = 0;

        foreach ($transactions as $txn) {
            if ($txn['amount'] > 0) {
                $totalCredits += $txn['amount'];
                $creditCount++;
            } else {
                $totalDebits += abs($txn['amount']);
                $debitCount++;
            }
        }

        // Balance check
        $calculatedClosing = $header['opening_balance'] + $totalCredits - $totalDebits;
        $balanceMatch = abs($calculatedClosing - $header['closing_balance']) < 0.02;

        return [
            'header' => $header,
            'transactions' => $transactions,
            'summary' => [
                'transaction_count' => count($transactions),
                'credit_count' => $creditCount,
                'debit_count' => $debitCount,
                'total_credits' => round($totalCredits, 2),
                'total_debits' => round($totalDebits, 2),
                'calculated_closing' => round($calculatedClosing, 2),
                'balance_match' => $balanceMatch,
            ],
        ];
    }

    /**
     * Parse header info from Nedbank statement text.
     */
    private function parseNedbankHeader(array $lines): array
    {
        $header = [
            'account_number' => '',
            'account_holder' => '',
            'branch_code' => '',
            'statement_period' => '',
            'statement_date' => '',
            'opening_balance' => 0,
            'closing_balance' => 0,
        ];

        $fullText = implode(' ', $lines);

        // Account number — Nedbank uses "Account number XXXXXXXXXX"
        if (preg_match('/Account\s+number\s+(\d{8,15})/i', $fullText, $m)) {
            $header['account_number'] = $m[1];
        }

        // Statement period — "10/12/2025 – 10/01/2026" or with dash
        if (preg_match('/Statement\s+period:?\s*(\d{2}\/\d{2}\/\d{4}\s*[–\-]\s*\d{2}\/\d{2}\/\d{4})/i', $fullText, $m)) {
            $header['statement_period'] = trim($m[1]);
        }

        // Statement date
        if (preg_match('/Statement\s+date:?\s*(\d{2}\/\d{2}\/\d{4})/i', $fullText, $m)) {
            $header['statement_date'] = trim($m[1]);
        }

        // Opening balance — from Cashflow section: "Opening balance R238,296.83"
        if (preg_match('/Opening\s+balance\s+R?([\d,]+\.\d{2})/i', $fullText, $m)) {
            $header['opening_balance'] = (float) str_replace(',', '', $m[1]);
        }

        // Closing balance — from Cashflow section: "Closing balance R887,729.54"
        if (preg_match('/Closing\s+balance\s+R?([\d,]+\.\d{2})/i', $fullText, $m)) {
            $header['closing_balance'] = (float) str_replace(',', '', $m[1]);
        }

        // Account holder — look for name lines near top (before "Account summary")
        foreach ($lines as $line) {
            $t = trim($line);
            if (preg_match('/^THE\s+/i', $t) || preg_match('/^[A-Z\s]{10,}$/', $t)) {
                if (!preg_match('/Account|Statement|Nedbank|Bank|Page|Rivonia|Box|Lost|Client|VAT/i', $t)) {
                    $header['account_holder'] = $t;
                    break;
                }
            }
        }

        return $header;
    }

    /**
     * Parse transactions from Nedbank statement text.
     * Strategy: Use balance-difference method.
     * - Last number on each line = running balance
     * - Amount = current_balance - previous_balance
     * - Positive diff = credit, negative diff = debit
     * - Description = text between date and the trailing financial numbers
     */
    private function parseNedbankTransactions(array $lines, array $header): array
    {
        $transactions = [];
        $prevBalance = $header['opening_balance'];

        foreach ($lines as $line) {
            $trimmed = trim($line);
            if ($trimmed === '') continue;

            // Handle "Opening balance" line — extract balance to set starting point
            if (preg_match('/Opening\s+balance/i', $trimmed)) {
                if (preg_match('/([\d,]+\.\d{2})\s*$/', $trimmed, $m)) {
                    $prevBalance = (float) str_replace(',', '', $m[1]);
                }
                continue;
            }

            // Skip non-transaction lines
            if (preg_match('/Closing\s+balance|Balance\s+carried\s+forward/i', $trimmed)) continue;
            if (preg_match('/^(Narrative\s+Description|Tran\s+list\s+no|Bank\s+charges\s+for|Cash\s+fees|Electronic\s+banking|Service\s+fees|Transaction\s+service|Other\s+charges|Total\s+Charges|Item\s+cost|VAT\s*\(R\)|Total\s*\(R\)|Page\s+\d|see\s+money|Nedbank\s+Ltd|We\s+subscribe|through\s+the)/i', $trimmed)) continue;
            if (preg_match('/Account\s+(type|number|summary)|Current\s+account|Statement\s+(date|period|frequency)|Envelope|Total\s+pages/i', $trimmed)) continue;
            if (preg_match('/Cashflow|Funds\s+received|Funds\s+used|Annual\s+credit|Bank\s+charge|VAT\s+inclusive|VAT\s+calculated/i', $trimmed)) continue;
            if (preg_match('/Atm.teller|Electronic\s+payments|Investment\s+repayments|Transfers\s+in|Other\s+credits|Account\s+payments|Debit.stop|Electronic\s+transfers|Total\s+charges|Other\s+debits/i', $trimmed)) continue;
            if (preg_match('/^\s*Total\s*$/i', $trimmed)) continue;
            if (preg_match('/%\s+of\s+(funds|utilisation)/i', $trimmed)) continue;
            if (preg_match('/eConfirm|Reg\s+No/i', $trimmed)) continue;
            if (preg_match('/Some\s+of\s+our\s+fees|dedicated\s+to\s+keeping|Simplify\s+your|personal\.nedbank/i', $trimmed)) continue;
            if (preg_match('/Please\s+examine|reported\s+within\s+30/i', $trimmed)) continue;
            if (preg_match('/^\d{4,6}$/', $trimmed)) continue; // Page serial numbers
            if (preg_match('/Fees\s*\(R\)|Debits\s*\(R\)|Credits\s*\(R\)|Balance\s*\(R\)/i', $trimmed)) continue;

            // Must contain a date DD/MM/YYYY to be a transaction line
            if (!preg_match('/(\d{2}\/\d{2}\/\d{4})/', $trimmed, $dateMatch)) continue;

            $dateStr = $dateMatch[1];

            // Get everything after the date
            $datePos = strpos($trimmed, $dateStr);
            $afterDate = trim(substr($trimmed, $datePos + strlen($dateStr)));

            if (empty($afterDate)) continue;

            // Find all financial numbers in the line after the date
            // Use negative lookbehind to skip R-prefixed amounts (part of description)
            preg_match_all('/(?<![A-Za-z])([\d,]+\.\d{2})\s*\*?/', $afterDate, $allNums, PREG_OFFSET_CAPTURE);

            if (empty($allNums[1]) || count($allNums[1]) < 1) continue;

            $numEntries = $allNums[1];

            // Last number is always the running balance
            $lastEntry = end($numEntries);
            $currentBalance = (float) str_replace(',', '', $lastEntry[0]);

            // Calculate transaction amount from balance difference
            $diff = round($currentBalance - $prevBalance, 2);

            // Skip zero-amount lines (informational rows like VAT breakdowns)
            if (abs($diff) < 0.01) {
                $prevBalance = $currentBalance;
                continue;
            }

            // Extract description: everything before the trailing financial numbers
            // Strip the last 2 numbers (debit/credit amount + balance) from the right
            if (count($numEntries) >= 2) {
                $secondLast = $numEntries[count($numEntries) - 2];
                $cutPos = $secondLast[1];
                $description = trim(substr($afterDate, 0, $cutPos));
            } else {
                // Only 1 number (the balance) — description is everything before it
                $cutPos = $lastEntry[1];
                $description = trim(substr($afterDate, 0, $cutPos));
            }

            // Normalize whitespace in description
            $description = preg_replace('/\s+/', ' ', $description);

            if (empty($description)) continue;

            // Convert date DD/MM/YYYY to YYYY-MM-DD
            $parts = explode('/', $dateStr);
            $fullDate = $parts[2] . '-' . $parts[1] . '-' . $parts[0];

            $transactions[] = [
                'date' => $fullDate,
                'description' => $description,
                'amount' => $diff,
                'balance' => $currentBalance,
            ];

            $prevBalance = $currentBalance;
        }

        return $transactions;
    }

    // =========================================================================
    // ABSA PARSER
    // =========================================================================

    /**
     * Parse ABSA bank statement text into structured data.
     * ABSA format: YYYY-MM-DD dates, signed Amount column (negative = debit),
     * running Balance column. Multi-line descriptions supported.
     * Uses balance-difference method for reliability.
     */
    private function parseAbsaText(array $pages): array
    {
        $allText = implode("\n", $pages);
        $lines = explode("\n", $allText);

        $header = $this->parseAbsaHeader($lines);
        $transactions = $this->parseAbsaTransactions($lines, $header);

        // Calculate totals
        $totalCredits = 0;
        $totalDebits = 0;
        $creditCount = 0;
        $debitCount = 0;

        foreach ($transactions as $txn) {
            if ($txn['amount'] > 0) {
                $totalCredits += $txn['amount'];
                $creditCount++;
            } else {
                $totalDebits += abs($txn['amount']);
                $debitCount++;
            }
        }

        // Balance check
        $calculatedClosing = $header['opening_balance'] + $totalCredits - $totalDebits;
        $balanceMatch = abs($calculatedClosing - $header['closing_balance']) < 0.02;

        return [
            'header' => $header,
            'transactions' => $transactions,
            'summary' => [
                'transaction_count' => count($transactions),
                'credit_count' => $creditCount,
                'debit_count' => $debitCount,
                'total_credits' => round($totalCredits, 2),
                'total_debits' => round($totalDebits, 2),
                'calculated_closing' => round($calculatedClosing, 2),
                'balance_match' => $balanceMatch,
            ],
        ];
    }

    /**
     * Parse header info from ABSA statement text.
     */
    private function parseAbsaHeader(array $lines): array
    {
        $header = [
            'account_number' => '',
            'account_holder' => '',
            'branch_code' => '',
            'statement_period' => '',
            'statement_date' => '',
            'opening_balance' => 0,
            'closing_balance' => 0,
        ];

        $fullText = implode(' ', $lines);

        // Statement period — "Statement for Period 2025-07-01 - 2025-07-31"
        if (preg_match('/Statement\s+for\s+Period\s+(\d{4}-\d{2}-\d{2}\s*-\s*\d{4}-\d{2}-\d{2})/i', $fullText, $m)) {
            $header['statement_period'] = trim($m[1]);
        }

        // Transaction History date — "Transaction History (2025-09-02 08:14:38)"
        if (preg_match('/Transaction\s+History\s+\((\d{4}-\d{2}-\d{2}\s+\d{2}:\d{2}:\d{2})\)/i', $fullText, $m)) {
            $header['statement_date'] = trim($m[1]);
        }

        // Opening balance from "Balance Brought Forward" line
        if (preg_match('/Balance\s+Brought\s+Forward\s+([\d,]+\.\d{2})/i', $fullText, $m)) {
            $header['opening_balance'] = (float) str_replace(',', '', $m[1]);
        }

        // Closing balance from "Balance Carried Forward" line
        if (preg_match('/Balance\s+Carried\s+Forward\s+([\d,]+\.\d{2})/i', $fullText, $m)) {
            $header['closing_balance'] = (float) str_replace(',', '', $m[1]);
        }

        // Account holder — company name (typically PTY, LTD, CC, etc.)
        foreach ($lines as $line) {
            $t = trim($line);
            if (preg_match('/(?:PTY|LTD|CC|TRUST|INC)/i', $t)) {
                if (!preg_match('/ABSA|Transaction|Statement|Balance|Current|Available|Page|Date/i', $t)) {
                    $header['account_holder'] = $t;
                    break;
                }
            }
        }

        return $header;
    }

    /**
     * Parse transactions from ABSA statement text.
     * Strategy: Accumulate multi-line descriptions, use balance-difference method.
     * - Lines starting with YYYY-MM-DD begin a new transaction
     * - Non-date lines are continuation of current description
     * - Balance-difference determines credit vs debit
     * - Zero-amount transactions are skipped
     */
    private function parseAbsaTransactions(array $lines, array $header): array
    {
        $transactions = [];
        $prevBalance = $header['opening_balance'];
        $currentTxn = null;
        $inTransactionSection = false;

        foreach ($lines as $line) {
            $trimmed = trim($line);
            if ($trimmed === '') continue;

            // Detect Balance Brought Forward — set starting balance
            if (preg_match('/Balance\s+Brought\s+Forward/i', $trimmed)) {
                if (preg_match('/([\d,]+\.\d{2})\s*$/', $trimmed, $m)) {
                    $prevBalance = (float) str_replace(',', '', $m[1]);
                }
                $inTransactionSection = true;
                continue;
            }

            // Balance Carried Forward — finalize pending transaction and stop
            if (preg_match('/Balance\s+Carried\s+Forward/i', $trimmed)) {
                if ($currentTxn !== null) {
                    $txn = $this->finalizeAbsaTransaction($currentTxn, $prevBalance);
                    if ($txn) {
                        $transactions[] = $txn;
                        $prevBalance = $txn['balance'];
                    }
                    $currentTxn = null;
                }
                continue;
            }

            if (!$inTransactionSection) continue;

            // Skip non-transaction lines
            if (preg_match('/Transaction\s+History/i', $trimmed)) continue;
            if (preg_match('/^ABSA$/i', $trimmed)) continue;
            if (preg_match('/Current\s+balance/i', $trimmed)) continue;
            if (preg_match('/Available\s+Balance/i', $trimmed)) continue;
            if (preg_match('/Unclaimed\s+Cheques/i', $trimmed)) continue;
            if (preg_match('/Statement\s+for\s+Period/i', $trimmed)) continue;
            if (preg_match('/^Date:?\s+Transaction\s+Description/i', $trimmed)) continue;
            if (preg_match('/Page\s+\d+\s+of\s+\d+/i', $trimmed)) continue;
            if (preg_match('/^\d{4}-\d{2}-\d{2}\s+\d{2}:\d{2}:\d{2}\s*$/', $trimmed)) continue;

            // Check if line starts with a date (YYYY-MM-DD)
            if (preg_match('/^(\d{4}-\d{2}-\d{2})\s+(.+)$/', $trimmed, $dateMatch)) {
                // Skip timestamp lines (e.g., "2025-09-02 08:14:38 Page 1 of 2")
                if (preg_match('/^\d{2}:\d{2}:\d{2}/', $dateMatch[2])) continue;
                if (preg_match('/Page\s+\d+\s+of\s+\d+/i', $dateMatch[2])) continue;

                // Finalize previous transaction if exists
                if ($currentTxn !== null) {
                    $txn = $this->finalizeAbsaTransaction($currentTxn, $prevBalance);
                    if ($txn) {
                        $transactions[] = $txn;
                        $prevBalance = $txn['balance'];
                    }
                }

                // Start new transaction
                $currentTxn = [
                    'date' => $dateMatch[1],
                    'textLines' => [$dateMatch[2]],
                ];
            } else {
                // Continuation line for multi-line description
                if ($currentTxn !== null) {
                    $currentTxn['textLines'][] = $trimmed;
                }
            }
        }

        // Finalize last transaction if any
        if ($currentTxn !== null) {
            $txn = $this->finalizeAbsaTransaction($currentTxn, $prevBalance);
            if ($txn) {
                $transactions[] = $txn;
            }
        }

        return $transactions;
    }

    /**
     * Finalize an ABSA transaction from accumulated text lines.
     * Extracts balance (last financial number), computes amount via balance-difference,
     * and builds description by stripping the trailing amount + balance numbers.
     * Returns null for zero-amount transactions (informational lines).
     */
    private function finalizeAbsaTransaction(array $txnData, float $prevBalance): ?array
    {
        $fullText = implode(' ', $txnData['textLines']);

        // Find all financial numbers: optional negative sign, digits with optional commas, dot, 2 decimals
        preg_match_all('/-?[\d,]+\.\d{2}/', $fullText, $allNums, PREG_OFFSET_CAPTURE);

        if (empty($allNums[0]) || count($allNums[0]) < 1) return null;

        $numEntries = $allNums[0];

        // Last number is the running balance
        $lastEntry = end($numEntries);
        $currentBalance = (float) str_replace(',', '', $lastEntry[0]);

        // Calculate amount from balance difference
        $diff = round($currentBalance - $prevBalance, 2);

        // Skip zero-amount transactions (NOTICE FEE, ARCHIVE STMT, PROOF OF PMT, etc.)
        if (abs($diff) < 0.01) return null;

        // Build description by removing the last 2 financial numbers (amount + balance)
        // Process from right to left to preserve string positions
        $desc = $fullText;
        $removeCount = min(2, count($numEntries));
        $toRemove = array_slice($numEntries, -$removeCount);
        // Sort by position descending so removals don't shift earlier positions
        usort($toRemove, function ($a, $b) { return $b[1] - $a[1]; });

        foreach ($toRemove as $entry) {
            $desc = substr_replace($desc, '', $entry[1], strlen($entry[0]));
        }

        $description = trim(preg_replace('/\s+/', ' ', $desc));

        if (empty($description)) return null;

        return [
            'date' => $txnData['date'],
            'description' => $description,
            'amount' => $diff,
            'balance' => $currentBalance,
        ];
    }

    // =========================================================================
    // CAPITEC PARSER
    // =========================================================================

    /**
     * Parse Capitec Business Bank statement text into structured data.
     * Capitec format: DD/MM/YY dates (Post Date + Trans Date), spaces as thousand
     * separators, signed amounts (+/-), separate Fees/Amount/Balance columns.
     * Multi-line descriptions with reference numbers wrapping to next lines.
     */
    private function parseCapitecText(array $pages): array
    {
        $allText = implode("\n", $pages);
        $lines = explode("\n", $allText);

        $header = $this->parseCapitecHeader($lines);
        $transactions = $this->parseCapitecTransactions($lines, $header);

        // Calculate totals
        $totalCredits = 0;
        $totalDebits = 0;
        $creditCount = 0;
        $debitCount = 0;

        foreach ($transactions as $txn) {
            if ($txn['amount'] > 0) {
                $totalCredits += $txn['amount'];
                $creditCount++;
            } else {
                $totalDebits += abs($txn['amount']);
                $debitCount++;
            }
        }

        // Balance check — closing from last transaction balance
        $closingBalance = $header['closing_balance'];
        if (!empty($transactions)) {
            $lastTxn = end($transactions);
            $closingBalance = $lastTxn['balance'];
            $header['closing_balance'] = $closingBalance;
        }

        $calculatedClosing = $header['opening_balance'] + $totalCredits - $totalDebits;
        $balanceMatch = abs($calculatedClosing - $closingBalance) < 0.02;

        return [
            'header' => $header,
            'transactions' => $transactions,
            'summary' => [
                'transaction_count' => count($transactions),
                'credit_count' => $creditCount,
                'debit_count' => $debitCount,
                'total_credits' => round($totalCredits, 2),
                'total_debits' => round($totalDebits, 2),
                'calculated_closing' => round($calculatedClosing, 2),
                'balance_match' => $balanceMatch,
            ],
        ];
    }

    /**
     * Parse header info from Capitec statement text.
     */
    private function parseCapitecHeader(array $lines): array
    {
        $header = [
            'account_number' => '',
            'account_holder' => '',
            'branch_code' => '',
            'statement_period' => '',
            'statement_date' => '',
            'opening_balance' => 0,
            'closing_balance' => 0,
        ];

        $fullText = implode(' ', $lines);

        // Account number — "Account No. 1053041250" or "Account No.: 1053041250"
        if (preg_match('/Account\s+No\.?\s*:?\s*(\d{8,15})/i', $fullText, $m)) {
            $header['account_number'] = $m[1];
        }

        // Branch code — "Branch: 450105"
        if (preg_match('/Branch:\s*(\d+)/i', $fullText, $m)) {
            $header['branch_code'] = $m[1];
        }

        // Statement date — "Date 01/02/2026" (header date, DD/MM/YYYY)
        if (preg_match('/(?:^|\s)Date\s+(\d{2}\/\d{2}\/\d{4})/i', $fullText, $m)) {
            $header['statement_date'] = $m[1];
            // Use as statement period basis
            $header['statement_period'] = $m[1];
        }

        // Opening balance from "Balance brought forward" line
        if (preg_match('/Balance\s+brought\s+forward\s+.*?([+-]?\d[\d ]*\.\d{2})/i', $fullText, $m)) {
            $header['opening_balance'] = (float) str_replace(['+', ' '], '', $m[1]);
        }

        // Account holder — company name with PTY/LTD etc.
        foreach ($lines as $line) {
            $t = trim($line);
            if (preg_match('/(?:PTY|LTD|CC|TRUST|INC)/i', $t)) {
                if (!preg_match('/Capitec|Statement|Balance|Account|Page|Date|Fee|VAT|financial|provider/i', $t)) {
                    $header['account_holder'] = $t;
                    break;
                }
            }
        }

        return $header;
    }

    /**
     * Parse transactions from Capitec statement text.
     * Strategy: Two DD/MM/YY dates start a transaction. Accumulate multi-line
     * descriptions. Use second-to-last financial number as amount, last as balance.
     * Fees stay in description text naturally.
     */
    private function parseCapitecTransactions(array $lines, array $header): array
    {
        $transactions = [];
        $currentTxn = null;
        $inTransactionSection = false;
        $prefixLines = []; // buffer orphan lines before first transaction

        foreach ($lines as $line) {
            $trimmed = trim($line);
            if ($trimmed === '') continue;

            // Detect Balance brought forward — set starting balance
            if (preg_match('/Balance\s+brought\s+forward/i', $trimmed)) {
                $inTransactionSection = true;
                continue;
            }

            if (!$inTransactionSection) continue;

            // Skip page header/footer lines
            if (preg_match('/^Capitec\s+Bank/i', $trimmed)) continue;
            if (preg_match('/^Branch:\s*/i', $trimmed)) continue;
            if (preg_match('/^Device:\s*/i', $trimmed)) continue;
            if (preg_match('/^Tel:\s*/i', $trimmed)) continue;
            if (preg_match('/^Date\s+\d{2}\/\d{2}\/\d{4}/i', $trimmed)) continue;
            if (preg_match('/^Account\s+(type|No)/i', $trimmed)) continue;
            if (preg_match('/^Statement\s+No/i', $trimmed)) continue;
            if (preg_match('/^Business\s+Account\s+Statement/i', $trimmed)) continue;
            if (preg_match('/^Telephone\s+No/i', $trimmed)) continue;
            if (preg_match('/^Business\s+Reg/i', $trimmed)) continue;
            if (preg_match('/^Client\s+VAT/i', $trimmed)) continue;
            if (preg_match('/^Relationship\s+Suite/i', $trimmed)) continue;
            if (preg_match('/Page:?\s+\d+/i', $trimmed)) continue;
            if (preg_match('/^Post\s+Trans/i', $trimmed)) continue;
            if (preg_match('/^Date\s+Date\s*$/i', $trimmed)) continue;
            if (preg_match('/^\d{2}\/\d{2}\/\d{4}\s*$/i', $trimmed)) continue;

            // Skip intermediate balance line on page break (e.g., "Balance +37 924.05")
            if (preg_match('/^Balance\s+[+-]?\d/i', $trimmed) && !preg_match('/Balance\s+(brought|carried)/i', $trimmed)) continue;

            // Skip fee summary and footer lines
            if (preg_match('/^Fee\s+Total/i', $trimmed)) continue;
            if (preg_match('/^VAT\s+@/i', $trimmed)) continue;
            if (preg_match('/^VAT\s+Total/i', $trimmed)) continue;
            if (preg_match('/^All\s+fees\s+charged/i', $trimmed)) continue;
            if (preg_match('/^Statements\s+are\s+accepted/i', $trimmed)) continue;
            if (preg_match('/capitecbank/i', $trimmed)) continue;
            if (preg_match('/financial\s+services\s+provider/i', $trimmed)) continue;
            if (preg_match('/Reg\.?\s+No/i', $trimmed)) continue;
            if (preg_match('/^VAT\s+Reg/i', $trimmed)) continue;
            if (preg_match('/24hr\s+Business/i', $trimmed)) continue;
            if (preg_match('/Neutron\s+Road/i', $trimmed)) continue;
            if (preg_match('/No\s+Limit\s*\(No\s+Lim\)/i', $trimmed)) continue;
            if (preg_match('/Overdraft\s+(Excess|Expiry)/i', $trimmed)) continue;
            if (preg_match('/Prime\s+Lending\s+rate/i', $trimmed)) continue;
            if (preg_match('/^\d{1,3}\.\d{4}%\s*$/', $trimmed)) continue;
            if (preg_match('/^Description\s+Reference\s+Fees/i', $trimmed)) continue;
            if (preg_match('/^Fees\s+Amount\s+Balance/i', $trimmed)) continue;

            // Check if line starts with two DD/MM/YY dates (Post Date + Trans Date)
            if (preg_match('/^(\d{2}\/\d{2}\/\d{2})\s+(\d{2}\/\d{2}\/\d{2})\s+(.+)$/', $trimmed, $dateMatch)) {
                // Finalize previous transaction if exists
                if ($currentTxn !== null) {
                    $txn = $this->finalizeCapitecTransaction($currentTxn);
                    if ($txn) {
                        $transactions[] = $txn;
                    }
                }

                // Convert post date DD/MM/YY to YYYY-MM-DD
                $postDate = $this->parseCapitecDate($dateMatch[1]);

                // Start new transaction with any buffered prefix lines
                $currentTxn = [
                    'date' => $postDate,
                    'textLines' => array_merge($prefixLines, [$dateMatch[3]]),
                ];
                $prefixLines = [];
            } else {
                // Continuation line for multi-line description
                if ($currentTxn !== null) {
                    $currentTxn['textLines'][] = $trimmed;
                } else {
                    // Orphan line before first transaction (e.g., "Interest Rate @ 0.000")
                    $prefixLines[] = $trimmed;
                }
            }
        }

        // Finalize last transaction if any
        if ($currentTxn !== null) {
            $txn = $this->finalizeCapitecTransaction($currentTxn);
            if ($txn) {
                $transactions[] = $txn;
            }
        }

        return $transactions;
    }

    /**
     * Parse Capitec date DD/MM/YY to YYYY-MM-DD.
     */
    private function parseCapitecDate(string $dateStr): string
    {
        $parts = explode('/', $dateStr);
        if (count($parts) !== 3) return '2026-01-01';
        $year = '20' . $parts[2];
        return $year . '-' . $parts[1] . '-' . $parts[0];
    }

    /**
     * Finalize a Capitec transaction from accumulated text lines.
     * Financial numbers use spaces as thousand separators and +/- signs.
     * Second-to-last number = amount (from Amount column).
     * Last number = balance (from Balance column).
     * Fees naturally remain in description text.
     * Returns null if no valid financial numbers found.
     */
    private function finalizeCapitecTransaction(array $txnData): ?array
    {
        $fullText = implode(' ', $txnData['textLines']);

        // Find all financial numbers: optional +/- sign, digits with optional spaces, dot, 2 decimals
        // (?!\d) ensures we don't match partial numbers like "0.000" (interest rate with 3 decimals)
        preg_match_all('/[+-]?\d[\d ]*\.\d{2}(?!\d)/', $fullText, $allNums, PREG_OFFSET_CAPTURE);

        if (empty($allNums[0]) || count($allNums[0]) < 2) return null;

        $numEntries = $allNums[0];

        // Last number = balance, second-to-last = amount
        $lastEntry = end($numEntries);
        $secondLastEntry = $numEntries[count($numEntries) - 2];

        $currentBalance = (float) str_replace(['+', ' '], '', $lastEntry[0]);
        $amount = (float) str_replace(['+', ' '], '', $secondLastEntry[0]);

        // Skip zero-amount transactions
        if (abs($amount) < 0.01) return null;

        // Build description by removing the last 2 financial numbers
        $desc = $fullText;
        $toRemove = array_slice($numEntries, -2);
        usort($toRemove, function ($a, $b) { return $b[1] - $a[1]; });

        foreach ($toRemove as $entry) {
            $desc = substr_replace($desc, '', $entry[1], strlen($entry[0]));
        }

        $description = trim(preg_replace('/\s+/', ' ', $desc));

        if (empty($description)) return null;

        return [
            'date' => $txnData['date'],
            'description' => $description,
            'amount' => $amount,
            'balance' => $currentBalance,
        ];
    }

    // =========================================================================
    // STANDARD BANK PARSER
    // =========================================================================

    /**
     * Parse Standard Bank statement text into structured data.
     * Standard Bank format: DD Mon YY dates, separate Payments/Deposits columns,
     * running Balance column, comma thousand separators.
     * Uses balance-difference method since column positions are lost in text extraction.
     */
    private function parseStandardText(array $pages): array
    {
        $allText = implode("\n", $pages);
        $lines = explode("\n", $allText);

        $header = $this->parseStandardHeader($lines);
        $transactions = $this->parseStandardTransactions($lines, $header);

        // Calculate totals
        $totalCredits = 0;
        $totalDebits = 0;
        $creditCount = 0;
        $debitCount = 0;

        foreach ($transactions as $txn) {
            if ($txn['amount'] > 0) {
                $totalCredits += $txn['amount'];
                $creditCount++;
            } else {
                $totalDebits += abs($txn['amount']);
                $debitCount++;
            }
        }

        // Closing balance from last transaction
        $closingBalance = $header['closing_balance'];
        if (!empty($transactions)) {
            $lastTxn = end($transactions);
            $closingBalance = $lastTxn['balance'];
            $header['closing_balance'] = $closingBalance;
        }

        $calculatedClosing = $header['opening_balance'] + $totalCredits - $totalDebits;
        $balanceMatch = abs($calculatedClosing - $closingBalance) < 0.02;

        return [
            'header' => $header,
            'transactions' => $transactions,
            'summary' => [
                'transaction_count' => count($transactions),
                'credit_count' => $creditCount,
                'debit_count' => $debitCount,
                'total_credits' => round($totalCredits, 2),
                'total_debits' => round($totalDebits, 2),
                'calculated_closing' => round($calculatedClosing, 2),
                'balance_match' => $balanceMatch,
            ],
        ];
    }

    /**
     * Parse header info from Standard Bank statement text.
     */
    private function parseStandardHeader(array $lines): array
    {
        $header = [
            'account_number' => '',
            'account_holder' => '',
            'branch_code' => '',
            'statement_period' => '',
            'statement_date' => '',
            'opening_balance' => 0,
            'closing_balance' => 0,
        ];

        $fullText = implode(' ', $lines);

        // Account number — "Account number: 10 15 539 452 3"
        if (preg_match('/Account\s+number:\s*([\d\s]+)/i', $fullText, $m)) {
            $header['account_number'] = trim($m[1]);
        }

        // Account holder — "Account holder: ARCHAUS ARCHITECTURE (PTY) LTD"
        if (preg_match('/Account\s+holder:\s*(.+?)(?:\s*Product|\s*Address|$)/i', $fullText, $m)) {
            $header['account_holder'] = trim($m[1]);
        }

        // Statement period — "From: 28 Oct 22 To: 28 Apr 23"
        if (preg_match('/From:?\s*(\d{1,2}\s+[A-Za-z]{3}\s+\d{2,4})\s+To:?\s*(\d{1,2}\s+[A-Za-z]{3}\s+\d{2,4})/i', $fullText, $m)) {
            $header['statement_period'] = $m[1] . ' - ' . $m[2];
        }

        // Opening balance from "STATEMENT OPENING BALANCE" line
        if (preg_match('/STATEMENT\s+OPENING\s+BALANCE\s+([\d,]+\.\d{2})/i', $fullText, $m)) {
            $header['opening_balance'] = (float) str_replace(',', '', $m[1]);
        }

        return $header;
    }

    /**
     * Parse transactions from Standard Bank statement text.
     * Strategy: Accumulate multi-line descriptions, use balance-difference method.
     * - Lines starting with DD Mon YY begin a new transaction
     * - Non-date lines are continuation of current description
     * - Balance-difference determines credit vs debit
     * - "Statement Summary" or "Please verify" stops processing
     */
    private function parseStandardTransactions(array $lines, array $header): array
    {
        $transactions = [];
        $prevBalance = $header['opening_balance'];
        $currentTxn = null;
        $inTransactionSection = false;

        foreach ($lines as $line) {
            $trimmed = trim($line);
            if ($trimmed === '') continue;

            // Detect STATEMENT OPENING BALANCE — set starting balance
            if (preg_match('/STATEMENT\s+OPENING\s+BALANCE/i', $trimmed)) {
                if (preg_match('/([\d,]+\.\d{2})\s*$/', $trimmed, $m)) {
                    $prevBalance = (float) str_replace(',', '', $m[1]);
                }
                $inTransactionSection = true;
                continue;
            }

            // Statement Summary / Please verify — finalize and stop
            if (preg_match('/Statement\s+Summary/i', $trimmed) ||
                preg_match('/Please\s+verify\s+all\s+transactions/i', $trimmed)) {
                if ($currentTxn !== null) {
                    $txn = $this->finalizeStandardTransaction($currentTxn, $prevBalance);
                    if ($txn) {
                        $transactions[] = $txn;
                    }
                    $currentTxn = null;
                }
                break;
            }

            if (!$inTransactionSection) continue;

            // Skip page header/footer lines (repeat on all 31 pages)
            if (preg_match('/Customer\s+Care/i', $trimmed)) continue;
            if (preg_match('/standardbank/i', $trimmed)) continue;
            if (preg_match('/^STANDARD\s+BANK$/i', $trimmed)) continue;
            if (preg_match('/^QUEENSBURGH$/i', $trimmed)) continue;
            if (preg_match('/month\s+statement/i', $trimmed)) continue;
            if (preg_match('/^(From|To):?\s+\d{1,2}\s+[A-Za-z]{3}\s+\d{2,4}/i', $trimmed)) continue;
            if (preg_match('/^Account\s+(number|holder|name)/i', $trimmed)) continue;
            if (preg_match('/^Product\s+name/i', $trimmed)) continue;
            if (preg_match('/^\d{5,6}$/', $trimmed)) continue;
            if (preg_match('/Transaction\s+details/i', $trimmed)) continue;
            if (preg_match('/Available\s+Balance/i', $trimmed)) continue;
            if (preg_match('/^Date\s+Description/i', $trimmed)) continue;
            if (preg_match('/^Payments\s+Deposits/i', $trimmed)) continue;
            if (preg_match('/Pg\s+\d+\s+of\s+\d+/i', $trimmed)) continue;
            if (preg_match('/Standard\s+Bank\s+of\s+South/i', $trimmed)) continue;
            if (preg_match('/Authorised\s+financial/i', $trimmed)) continue;
            if (preg_match('/Registered\s+credit/i', $trimmed)) continue;
            if (preg_match('/^R[\d,]+\.\d{2}\s*$/', $trimmed)) continue;
            if (preg_match('/^Address:/i', $trimmed)) continue;
            if (preg_match('/^MY\/MOB/i', $trimmed)) continue;
            if (preg_match('/Today.s\s+debits/i', $trimmed)) continue;
            if (preg_match('/^Payments\s+R[\d,]/i', $trimmed)) continue;
            if (preg_match('/^Deposits\s+R[\d,]/i', $trimmed)) continue;

            // Check if line starts with a date (DD Mon YY)
            if (preg_match('/^(\d{1,2}\s+[A-Za-z]{3}\s+\d{2})\s+(.+)$/', $trimmed, $dateMatch)) {
                // Skip if it's actually a 4-digit year (e.g., "26 Apr 2023")
                if (preg_match('/^\d{1,2}\s+[A-Za-z]{3}\s+\d{4}/', $trimmed)) continue;

                // Finalize previous transaction if exists
                if ($currentTxn !== null) {
                    $txn = $this->finalizeStandardTransaction($currentTxn, $prevBalance);
                    if ($txn) {
                        $transactions[] = $txn;
                        $prevBalance = $txn['balance'];
                    }
                }

                // Convert DD Mon YY to YYYY-MM-DD
                $fullDate = $this->parseStandardDate($dateMatch[1]);

                // Start new transaction
                $currentTxn = [
                    'date' => $fullDate,
                    'textLines' => [$dateMatch[2]],
                ];
            } else {
                // Continuation line for multi-line description
                if ($currentTxn !== null) {
                    $currentTxn['textLines'][] = $trimmed;
                }
            }
        }

        // Finalize last transaction if any
        if ($currentTxn !== null) {
            $txn = $this->finalizeStandardTransaction($currentTxn, $prevBalance);
            if ($txn) {
                $transactions[] = $txn;
            }
        }

        return $transactions;
    }

    /**
     * Parse Standard Bank date DD Mon YY to YYYY-MM-DD.
     */
    private function parseStandardDate(string $dateStr): string
    {
        $months = [
            'jan' => '01', 'feb' => '02', 'mar' => '03', 'apr' => '04',
            'may' => '05', 'jun' => '06', 'jul' => '07', 'aug' => '08',
            'sep' => '09', 'oct' => '10', 'nov' => '11', 'dec' => '12',
        ];

        if (preg_match('/(\d{1,2})\s+([A-Za-z]{3})\s+(\d{2})/', $dateStr, $m)) {
            $day = str_pad($m[1], 2, '0', STR_PAD_LEFT);
            $monthKey = strtolower($m[2]);
            $month = $months[$monthKey] ?? '01';
            $year = '20' . $m[3];
            return $year . '-' . $month . '-' . $day;
        }

        return '2023-01-01';
    }

    /**
     * Finalize a Standard Bank transaction from accumulated text lines.
     * Extracts balance (last financial number), computes amount via balance-difference,
     * and builds description by stripping the trailing amount + balance numbers.
     * Returns null for zero-amount transactions.
     */
    private function finalizeStandardTransaction(array $txnData, float $prevBalance): ?array
    {
        $fullText = implode(' ', $txnData['textLines']);

        // Find all financial numbers: digits with optional commas, dot, 2 decimals
        preg_match_all('/[\d,]+\.\d{2}/', $fullText, $allNums, PREG_OFFSET_CAPTURE);

        if (empty($allNums[0]) || count($allNums[0]) < 1) return null;

        $numEntries = $allNums[0];

        // Last number is the running balance
        $lastEntry = end($numEntries);
        $currentBalance = (float) str_replace(',', '', $lastEntry[0]);

        // Calculate amount from balance difference
        $diff = round($currentBalance - $prevBalance, 2);

        // Skip zero-amount transactions
        if (abs($diff) < 0.01) return null;

        // Build description by removing the last 2 financial numbers (amount + balance)
        $desc = $fullText;
        $removeCount = min(2, count($numEntries));
        $toRemove = array_slice($numEntries, -$removeCount);
        usort($toRemove, function ($a, $b) { return $b[1] - $a[1]; });

        foreach ($toRemove as $entry) {
            $desc = substr_replace($desc, '', $entry[1], strlen($entry[0]));
        }

        $description = trim(preg_replace('/\s+/', ' ', $desc));

        if (empty($description)) return null;

        return [
            'date' => $txnData['date'],
            'description' => $description,
            'amount' => $diff,
            'balance' => $currentBalance,
        ];
    }
}
