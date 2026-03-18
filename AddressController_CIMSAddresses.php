<?php

namespace Modules\CIMSAddresses\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AddressController extends Controller
{
    /**
     * Display listing of addresses
     */
    public function index()
    {
        // Get non-deleted addresses
        $addresses = DB::table('cims_addresses')
            ->whereNull('deleted_at')
            ->orderBy('created_at', 'DESC')
            ->get();

        // Get deleted addresses (soft deleted)
        $deletedAddresses = DB::table('cims_addresses')
            ->whereNotNull('deleted_at')
            ->orderBy('deleted_at', 'DESC')
            ->get();

        // Calculate stats - Total includes ALL addresses (active + inactive + deleted)
        $stats = [
            'total' => $addresses->count() + $deletedAddresses->count(),
            'active' => $addresses->where('is_active', 1)->count(),
            'inactive' => $addresses->where('is_active', 0)->count(),
            'deleted' => $deletedAddresses->count(),
        ];

        $provinces = $this->getProvinces();

        return view('cimsaddresses::addresses.index', compact('addresses', 'deletedAddresses', 'stats', 'provinces'));
    }

    /**
     * Search addresses for AJAX dropdown
     */
    public function search(Request $request)
    {
        $q = $request->input('q', '');

        $query = DB::table('cims_addresses')
            ->select('id', 'long_address', 'street_number', 'street_name', 'suburb', 'city', 'province', 'postal_code')
            ->whereNull('deleted_at')
            ->orderBy('long_address')
            ->limit(100);

        if (!empty($q)) {
            $query->where(function($qry) use ($q) {
                $qry->where('long_address', 'LIKE', "%{$q}%")
                    ->orWhere('street_name', 'LIKE', "%{$q}%")
                    ->orWhere('suburb', 'LIKE', "%{$q}%")
                    ->orWhere('city', 'LIKE', "%{$q}%");
            });
        }

        return response()->json($query->get());
    }

    /**
     * Show create form
     */
    public function create()
    {
        $provinces = $this->getProvinces();
        $address = null;

        return view('cimsaddresses::addresses.form', compact('provinces', 'address'));
    }

    /**
     * Store new address
     */
    public function store(Request $request)
    {
        try {
            $now = now();

            $data = [
                'unit_number'    => trim($request->input('unit_number', '')),
                'complex_name'   => trim($request->input('complex_name', '')),
                'street_number'  => trim($request->input('street_number', '')),
                'street_name'    => trim($request->input('street_name', '')),
                'suburb'         => trim($request->input('suburb', '')),
                'city'           => trim($request->input('city', '')),
                'postal_code'    => trim($request->input('postal_code', '')),
                'province'       => trim($request->input('province', '')),
                'country'        => trim($request->input('country', 'South Africa')),
                'municipality'   => trim($request->input('municipality', '')),
                'ward'           => trim($request->input('ward', '')),
                'google_address' => trim($request->input('google_address', '')),
                'latitude'       => trim($request->input('latitude', '')),
                'longitude'      => trim($request->input('longitude', '')),
                'map_url'        => trim($request->input('map_url', '')),
                'is_active'      => $request->has('is_active') ? 1 : 1,
                'created_at'     => $now,
                'updated_at'     => $now,
                'created_by'     => auth()->id() ?? 1,
                'updated_by'     => auth()->id() ?? 1,
            ];

            // Build long_address
            $data['long_address'] = $this->buildLongAddress($data);

            // Check for duplicate address
            $duplicate = $this->checkDuplicateAddress($data);
            if ($duplicate) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'This address already exists in the system. Address ID: #A-' . str_pad($duplicate->id, 6, '0', STR_PAD_LEFT));
            }

            $id = DB::table('cims_addresses')->insertGetId($data);

            // Log audit
            $this->logAudit($id, 'created', null, $data);

            return redirect()->route('cimsaddresses.index')
                ->with('success', 'Address added successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error saving address: ' . $e->getMessage());
        }
    }

    /**
     * Show single address
     */
    public function show($id)
    {
        $address = DB::table('cims_addresses')->where('id', $id)->first();

        if (!$address) {
            abort(404);
        }

        $provinces = $this->getProvinces();
        $audit_trail = $this->getAuditTrail($id);

        return view('cimsaddresses::addresses.show', compact('address', 'provinces', 'audit_trail'));
    }

    /**
     * Show edit form
     */
    public function edit($id)
    {
        $address = DB::table('cims_addresses')->where('id', $id)->first();

        if (!$address) {
            abort(404);
        }

        $provinces = $this->getProvinces();

        return view('cimsaddresses::addresses.form', compact('address', 'provinces'));
    }

    /**
     * Update address
     */
    public function update(Request $request, $id)
    {
        try {
            $address = DB::table('cims_addresses')->where('id', $id)->first();

            if (!$address) {
                abort(404);
            }

            $old = (array)$address;

            $data = [
                'unit_number'    => trim($request->input('unit_number', '')),
                'complex_name'   => trim($request->input('complex_name', '')),
                'street_number'  => trim($request->input('street_number', '')),
                'street_name'    => trim($request->input('street_name', '')),
                'suburb'         => trim($request->input('suburb', '')),
                'city'           => trim($request->input('city', '')),
                'postal_code'    => trim($request->input('postal_code', '')),
                'province'       => trim($request->input('province', '')),
                'country'        => trim($request->input('country', 'South Africa')),
                'municipality'   => trim($request->input('municipality', '')),
                'ward'           => trim($request->input('ward', '')),
                'google_address' => trim($request->input('google_address', '')),
                'latitude'       => trim($request->input('latitude', '')),
                'longitude'      => trim($request->input('longitude', '')),
                'map_url'        => trim($request->input('map_url', '')),
                'is_active'      => $request->has('is_active') ? 1 : 0,
                'updated_at'     => now(),
                'updated_by'     => auth()->id() ?? 1,
            ];

            // Build long_address
            $data['long_address'] = $this->buildLongAddress($data);

            // Check for duplicate address (excluding current record)
            $duplicate = $this->checkDuplicateAddress($data, $id);
            if ($duplicate) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'This address already exists in the system. Address ID: #A-' . str_pad($duplicate->id, 6, '0', STR_PAD_LEFT));
            }

            DB::table('cims_addresses')->where('id', $id)->update($data);

            // Log audit
            $this->logAudit($id, 'updated', $old, $data);

            return redirect()->route('cimsaddresses.index')
                ->with('success', 'Address updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error updating address: ' . $e->getMessage());
        }
    }

    /**
     * Toggle active status
     */
    public function toggle($id)
    {
        $address = DB::table('cims_addresses')->where('id', $id)->first();

        if (!$address) {
            return redirect()->route('cimsaddresses.index');
        }

        $old = (array)$address;
        $new_status = $address->is_active == 1 ? 0 : 1;

        DB::table('cims_addresses')->where('id', $id)->update([
            'is_active'  => $new_status,
            'updated_at' => now(),
            'updated_by' => auth()->id() ?? 1,
        ]);

        $this->logAudit($id, 'status_changed', $old, ['is_active' => $new_status]);

        return redirect()->route('cimsaddresses.index')
            ->with('success', 'Address status updated.');
    }

    /**
     * Delete address (soft delete)
     */
    public function destroy($id)
    {
        $address = DB::table('cims_addresses')->where('id', $id)->first();

        if (!$address) {
            return redirect()->route('cimsaddresses.index');
        }

        $old = (array)$address;

        // Soft delete - set deleted_at timestamp
        DB::table('cims_addresses')->where('id', $id)->update([
            'deleted_at' => now(),
            'updated_at' => now(),
            'updated_by' => auth()->id() ?? 1,
        ]);

        $this->logAudit($id, 'deleted', $old, null);

        return redirect()->route('cimsaddresses.index')
            ->with('success', 'Address moved to trash.');
    }

    /**
     * Check if restoring would create a duplicate (AJAX)
     */
    public function checkRestoreDuplicate($id)
    {
        $address = DB::table('cims_addresses')->where('id', $id)->first();

        if (!$address) {
            return response()->json(['error' => 'Address not found'], 404);
        }

        // Check for duplicate in active records
        $data = [
            'unit_number' => $address->unit_number,
            'street_number' => $address->street_number,
            'street_name' => $address->street_name,
            'suburb' => $address->suburb,
            'city' => $address->city,
            'postal_code' => $address->postal_code,
        ];

        $duplicate = $this->checkDuplicateAddress($data, $id);

        if ($duplicate) {
            return response()->json([
                'has_duplicate' => true,
                'duplicate_id' => $duplicate->id,
                'duplicate_code' => '#A-' . str_pad($duplicate->id, 6, '0', STR_PAD_LEFT),
                'message' => 'A matching address already exists: ' . $duplicate->long_address
            ]);
        }

        return response()->json(['has_duplicate' => false]);
    }

    /**
     * Restore a soft-deleted address
     */
    public function restore($id)
    {
        $address = DB::table('cims_addresses')->where('id', $id)->first();

        if (!$address) {
            return redirect()->route('cimsaddresses.index');
        }

        // Check for duplicate before restoring
        $data = [
            'unit_number' => $address->unit_number,
            'street_number' => $address->street_number,
            'street_name' => $address->street_name,
            'suburb' => $address->suburb,
            'city' => $address->city,
            'postal_code' => $address->postal_code,
        ];

        $duplicate = $this->checkDuplicateAddress($data, $id);

        if ($duplicate) {
            return redirect()->route('cimsaddresses.index')
                ->with('error', 'Cannot restore - a matching address already exists: #A-' . str_pad($duplicate->id, 6, '0', STR_PAD_LEFT));
        }

        // Restore - clear deleted_at
        DB::table('cims_addresses')->where('id', $id)->update([
            'deleted_at' => null,
            'updated_at' => now(),
            'updated_by' => auth()->id() ?? 1,
        ]);

        $this->logAudit($id, 'restored', ['deleted_at' => $address->deleted_at], ['deleted_at' => null]);

        return redirect()->route('cimsaddresses.index')
            ->with('success', 'Address restored successfully.');
    }

    /**
     * Permanently delete an address
     */
    public function forceDelete($id)
    {
        $address = DB::table('cims_addresses')->where('id', $id)->first();

        if (!$address) {
            return redirect()->route('cimsaddresses.index');
        }

        $old = (array)$address;

        // Permanently delete
        DB::table('cims_addresses')->where('id', $id)->delete();

        $this->logAudit($id, 'permanently_deleted', $old, null);

        return redirect()->route('cimsaddresses.index')
            ->with('success', 'Address permanently deleted.');
    }

    /**
     * Get provinces list
     */
    private function getProvinces()
    {
        return [
            'KwaZulu-Natal',
            'Eastern Cape',
            'Free State',
            'Gauteng',
            'Limpopo',
            'Mpumalanga',
            'Northern Cape',
            'North West',
            'Western Cape',
        ];
    }

    /**
     * Build long address string
     */
    private function buildLongAddress($data)
    {
        $parts = [];

        if (!empty($data['unit_number'])) {
            $parts[] = $data['unit_number'];
        }
        if (!empty($data['complex_name'])) {
            $parts[] = $data['complex_name'];
        }

        $street = trim(($data['street_number'] ?? '') . ' ' . ($data['street_name'] ?? ''));
        if (!empty($street)) {
            $parts[] = $street;
        }

        if (!empty($data['suburb'])) {
            $parts[] = $data['suburb'];
        }

        $cityPostal = trim(($data['city'] ?? '') . ' ' . ($data['postal_code'] ?? ''));
        if (!empty($cityPostal)) {
            $parts[] = $cityPostal;
        }

        if (!empty($data['province'])) {
            $parts[] = $data['province'];
        }

        if (!empty($data['country'])) {
            $parts[] = $data['country'];
        }

        return implode(', ', $parts);
    }

    /**
     * Get audit trail for address
     */
    private function getAuditTrail($address_id)
    {
        try {
            return DB::table('cims_address_audit')
                ->where('address_id', $address_id)
                ->orderBy('changed_at', 'DESC')
                ->get();
        } catch (\Exception $e) {
            return collect([]);
        }
    }

    /**
     * Log audit entry
     */
    private function logAudit($address_id, $action, $old = null, $new = null)
    {
        try {
            DB::table('cims_address_audit')->insert([
                'address_id' => $address_id,
                'action'     => $action,
                'changed_by' => auth()->id() ?? 1,
                'changed_at' => now(),
                'old_values' => $old ? json_encode($old) : null,
                'new_values' => $new ? json_encode($new) : null,
            ]);
        } catch (\Exception $e) {
            // Audit table may not exist yet - silently continue
        }
    }

    /**
     * Check for duplicate address
     */
    private function checkDuplicateAddress($data, $excludeId = null)
    {
        // Build a normalized address string for comparison
        $newAddressKey = $this->normalizeAddressKey($data);

        // Get all addresses and compare
        $query = DB::table('cims_addresses')
            ->whereNull('deleted_at');

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        $addresses = $query->get();

        foreach ($addresses as $addr) {
            $existingKey = $this->normalizeAddressKey([
                'unit_number' => $addr->unit_number,
                'street_number' => $addr->street_number,
                'street_name' => $addr->street_name,
                'suburb' => $addr->suburb,
                'city' => $addr->city,
                'postal_code' => $addr->postal_code,
            ]);

            if ($newAddressKey === $existingKey) {
                return $addr;
            }
        }

        return null;
    }

    /**
     * Normalize address into a comparable key
     */
    private function normalizeAddressKey($data)
    {
        $unit = strtolower(trim($data['unit_number'] ?? ''));
        $streetNum = strtolower(trim($data['street_number'] ?? ''));
        $streetName = strtolower(trim($data['street_name'] ?? ''));
        $suburb = strtolower(trim($data['suburb'] ?? ''));
        $city = strtolower(trim($data['city'] ?? ''));
        $postal = strtolower(trim($data['postal_code'] ?? ''));

        // Remove common variations
        $streetName = preg_replace('/\s+/', ' ', $streetName);
        $streetName = str_replace([' street', ' st.', ' str', ' road', ' rd', ' rd.', ' avenue', ' ave', ' ave.'], '', $streetName);

        return implode('|', [$unit, $streetNum, $streetName, $suburb, $city, $postal]);
    }
}
