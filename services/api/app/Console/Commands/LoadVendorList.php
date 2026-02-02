<?php

namespace App\Console\Commands;

use App\Models\VendorList;
use App\Services\AutoFleetService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Console\Command\Command as SymfonyCommand;

class LoadVendorList extends Command
{
    protected $autoFleetService;

    public function __construct(AutoFleetService $autoFleetService)
    {
        parent::__construct();
        $this->autoFleetService = $autoFleetService;
    }

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:load-vendor-list';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Load vendor liststo match the NO OPPS vendor list with the actual vendor list.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $vendorLists = $this->autoFleetService->getVendorList();

        $normal = [];
        $noOpps = [];

        foreach ($vendorLists as $item) {
            $name = trim($item['name']);
            $key = strtolower($name);

            if (str_contains($name, "NO OPPS")) {
                $clean = trim(str_replace("NO OPPS ", "", $name));
                $cleanKey = strtolower($clean);

                $noOpps[$cleanKey] = $item;
            } else {
                $normal[$key] = $item;
            }
        }

        $countInserted = 0;

        foreach ($normal as $key => $vendor) {
            $noOppsVendor = $noOpps[$key] ?? ['name' => 'No NO OPPS Vendor'];

            $exists = VendorList::where('vendor_id', $vendor['name'])->exists();

            if ($exists) {
                $this->warn("Skipping existing vendor: {$vendor['name']}");
                continue;
            }

            VendorList::insert([
                'vendor_id' => $vendor['name'],
                'no_opps_id' => $noOppsVendor['name'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $this->info("Inserted: {$vendor['name']}  | NO OPPS: " . ($noOppsVendor['name'] ?? 'none'));
            $countInserted++;
        }

        $this->info("--------------------------------------------------");
        $this->info("Vendor list loading complete! Inserted: {$countInserted} vendors.");
        $this->info("--------------------------------------------------");

        return SymfonyCommand::SUCCESS;
    }
}
