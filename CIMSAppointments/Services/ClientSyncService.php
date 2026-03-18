<?php

namespace Modules\CIMSAppointments\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ClientSyncService
{
    /**
     * The custom field ID for client_code in Grow CRM.
     */
    protected int $clientCodeFieldId;

    /**
     * The Grow CRM database connection name.
     */
    protected string $growCrmConnection;

    public function __construct()
    {
        $this->clientCodeFieldId = config('cims_appointments.client_code_field_id', 38);
        $this->growCrmConnection = config('cims_appointments.growcrm_connection', 'growcrm');
    }

    /**
     * Check if a client_master record has a matching Grow CRM customer.
     * Returns the Grow CRM client_id if found, null otherwise.
     */
    public function findGrowCrmClient($clientCode): ?int
    {
        try {
            $record = DB::connection($this->growCrmConnection)
                ->table('tblcustomfieldsvalues')
                ->where('fieldid', $this->clientCodeFieldId)
                ->where('value', $clientCode)
                ->first();

            return $record ? (int) $record->relid : null;
        } catch (\Exception $e) {
            Log::warning('ClientSyncService: Could not check Grow CRM for client_code=' . $clientCode . ': ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Check if a Grow CRM customer has a matching client_master record.
     * Returns the client_master client_id if found, null otherwise.
     */
    public function findClientMaster($growCrmClientId): ?int
    {
        try {
            // Get the client_code from Grow CRM custom fields
            $customField = DB::connection($this->growCrmConnection)
                ->table('tblcustomfieldsvalues')
                ->where('fieldid', $this->clientCodeFieldId)
                ->where('relid', $growCrmClientId)
                ->first();

            if (!$customField || empty($customField->value)) {
                return null;
            }

            // Look up in client_master
            $client = DB::table('client_master')
                ->where('client_code', $customField->value)
                ->whereNull('deleted_at')
                ->first();

            return $client ? (int) $client->client_id : null;
        } catch (\Exception $e) {
            Log::warning('ClientSyncService: Could not check client_master for growCrmId=' . $growCrmClientId . ': ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Create a new client in client_master and return the record.
     */
    public function createClientMaster(array $data): ?object
    {
        try {
            $clientCode = $this->generateClientCode($data['company_name'] ?? $data['client_name'] ?? 'NEW');

            $clientId = DB::table('client_master')->insertGetId([
                'company_name' => $data['company_name'] ?? $data['client_name'] ?? '',
                'client_code' => $clientCode,
                'email' => $data['email'] ?? null,
                'phone_mobile' => $data['phone'] ?? null,
                'phone_business' => $data['phone'] ?? null,
                'is_active' => 1,
                'created_by' => $data['created_by'] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return DB::table('client_master')->where('client_id', $clientId)->first();
        } catch (\Exception $e) {
            Log::error('ClientSyncService: Failed to create client_master: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Create a matching customer record in Grow CRM and link via client_code.
     */
    public function createGrowCrmClient(object $clientMaster): ?int
    {
        try {
            $growCrmClientId = DB::connection($this->growCrmConnection)
                ->table('clients')
                ->insertGetId([
                    'client_company_name' => $clientMaster->company_name ?? '',
                    'client_creatorid' => 1,
                    'client_status' => 'active',
                    'client_created' => now()->toDateString(),
                    'client_billing_street' => '',
                    'client_billing_city' => '',
                    'client_billing_state' => '',
                    'client_billing_zip' => '',
                    'client_billing_country' => '',
                ]);

            // Link via custom field
            DB::connection($this->growCrmConnection)
                ->table('tblcustomfieldsvalues')
                ->insert([
                    'fieldid' => $this->clientCodeFieldId,
                    'relid' => $growCrmClientId,
                    'value' => $clientMaster->client_code,
                    'fieldto' => 'clients',
                ]);

            return $growCrmClientId;
        } catch (\Exception $e) {
            Log::error('ClientSyncService: Failed to create Grow CRM client: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Full sync: create a new lead in both client_master and Grow CRM.
     * Returns ['client_id' => int, 'client_code' => string, 'growcrm_client_id' => int|null].
     */
    public function createNewLead(array $data): array
    {
        // Step 1: Create in client_master
        $clientMaster = $this->createClientMaster($data);

        if (!$clientMaster) {
            return ['client_id' => null, 'client_code' => null, 'growcrm_client_id' => null];
        }

        // Step 2: Create in Grow CRM and link
        $growCrmClientId = $this->createGrowCrmClient($clientMaster);

        return [
            'client_id' => $clientMaster->client_id,
            'client_code' => $clientMaster->client_code,
            'growcrm_client_id' => $growCrmClientId,
        ];
    }

    /**
     * Check sync status for a client_master record.
     * Returns ['synced' => bool, 'growcrm_client_id' => int|null].
     */
    public function checkClientMasterSync($clientCode): array
    {
        $growCrmClientId = $this->findGrowCrmClient($clientCode);
        return [
            'synced' => $growCrmClientId !== null,
            'growcrm_client_id' => $growCrmClientId,
        ];
    }

    /**
     * Check sync status for a Grow CRM customer.
     * Returns ['synced' => bool, 'client_master_id' => int|null, 'client_code' => string|null].
     */
    public function checkGrowCrmSync($growCrmClientId): array
    {
        try {
            $customField = DB::connection($this->growCrmConnection)
                ->table('tblcustomfieldsvalues')
                ->where('fieldid', $this->clientCodeFieldId)
                ->where('relid', $growCrmClientId)
                ->first();

            if (!$customField || empty($customField->value)) {
                return ['synced' => false, 'client_master_id' => null, 'client_code' => null];
            }

            $client = DB::table('client_master')
                ->where('client_code', $customField->value)
                ->whereNull('deleted_at')
                ->first();

            return [
                'synced' => $client !== null,
                'client_master_id' => $client ? $client->client_id : null,
                'client_code' => $customField->value,
            ];
        } catch (\Exception $e) {
            Log::warning('ClientSyncService: checkGrowCrmSync failed: ' . $e->getMessage());
            return ['synced' => false, 'client_master_id' => null, 'client_code' => null];
        }
    }

    /**
     * Generate a client code from the company name.
     * Format: 3 significant letters + 3-digit number (e.g. "ATP100").
     */
    protected function generateClientCode(string $companyName): string
    {
        // Extract significant words (skip common words)
        $skipWords = ['the', 'and', 'of', 'for', 'in', 'at', 'to', 'pty', 'ltd', 'cc', 'inc'];
        $words = preg_split('/[\s\-\_\.\,]+/', strtolower(trim($companyName)));
        $words = array_filter($words, function ($w) use ($skipWords) {
            return strlen($w) > 0 && !in_array($w, $skipWords);
        });
        $words = array_values($words);

        // Build 3-letter prefix from first letters of significant words
        $prefix = '';
        foreach ($words as $word) {
            if (strlen($prefix) >= 3) break;
            $prefix .= strtoupper(substr($word, 0, 1));
        }
        // Pad with remaining letters from first word if needed
        if (strlen($prefix) < 3 && count($words) > 0) {
            $firstWord = strtoupper($words[0]);
            while (strlen($prefix) < 3 && strlen($prefix) < strlen($firstWord)) {
                $prefix .= $firstWord[strlen($prefix)];
            }
        }
        $prefix = str_pad($prefix, 3, 'X');

        // Find next available number
        $existing = DB::table('client_master')
            ->where('client_code', 'LIKE', $prefix . '%')
            ->pluck('client_code')
            ->toArray();

        $maxNum = 0;
        foreach ($existing as $code) {
            $num = (int) substr($code, 3);
            if ($num > $maxNum) {
                $maxNum = $num;
            }
        }

        $nextNum = $maxNum > 0 ? $maxNum + 100 : 100;
        return $prefix . $nextNum;
    }

    /**
     * Search clients in client_master.
     */
    public function searchClients(string $search): array
    {
        $clients = DB::table('client_master')
            ->whereNull('deleted_at')
            ->where('is_active', 1)
            ->where(function ($q) use ($search) {
                $q->where('company_name', 'LIKE', '%' . $search . '%')
                  ->orWhere('client_code', 'LIKE', '%' . $search . '%')
                  ->orWhere('email', 'LIKE', '%' . $search . '%')
                  ->orWhere('phone_mobile', 'LIKE', '%' . $search . '%')
                  ->orWhere('trading_name', 'LIKE', '%' . $search . '%');
            })
            ->orderBy('company_name', 'asc')
            ->limit(20)
            ->get();

        return $clients->toArray();
    }
}
