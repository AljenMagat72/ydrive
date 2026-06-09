<?php

namespace App\Services\Autofleet;

use App\Http\Integrations\Autofleet\AutofleetApi;
use App\Models\VendorList;
use Illuminate\Support\Facades\Log;

class AutofleetVendorService
{
    protected array $prefixMap = [
        'NO OPPS' => ['id_field' => 'no_opps_id', 'name_field' => 'no_opps_name'],
    ];

    public function __construct(protected AutofleetApi $autofleetApi)
    {
    }

    public function syncVendorLists()
    {
        $response = $this->autofleetApi->vendors()->get(config('autofleet.fleet_id'));
        $vendors = $response->json();

        Log::info($vendors);

        $normal = [];
        $variants = [];

        foreach ($vendors as $item) {
            $name = trim($item['name']);
            $matchedPrefix = $this->matchPrefix($name);

            if ($matchedPrefix) {
                $clean = strtolower(trim(str_ireplace($matchedPrefix . ' ', '', $name)));

                $variants[$clean][$matchedPrefix] = $item;
            } else {
                $normal[strtolower($name)] = $item;
            }
        }

        foreach ($normal as $key => $vendor) {
            $row = [
                'vendor_id' => $vendor['id'],
                'vendor_name' => $vendor['name'],
            ];

            foreach ($this->prefixMap as $prefix => $fields) {
                $variant = $variants[$key][$prefix] ?? null;
                $row[$fields['id_field']] = $variant['id'] ?? null;
                $row[$fields['name_field']] = $variant['name'] ?? null;
            }

            Log::info($row['vendor_id']);

            VendorList::updateOrCreate(
                ['vendor_id' => $row['vendor_id']],
                $row
            );
        }
    }

    public function findOrSyncVendor(string $autofleetVendorId): VendorList
    {
        $vendorList = VendorList::whereVendorOrNoOpps($autofleetVendorId)->first();

        if (!$vendorList) {
            $this->syncVendorLists();
            $vendorList = VendorList::whereVendorOrNoOpps($autofleetVendorId)->firstOrFail();
        }

        return $vendorList;
    }

    protected function matchPrefix(string $name): ?string
    {
        return array_find_key($this->prefixMap, fn($fields, $prefix) => stripos($name, $prefix) !== false);
    }
}
