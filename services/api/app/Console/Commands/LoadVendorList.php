<?php

namespace App\Console\Commands;

use App\Models\VendorList;
use App\Services\Autofleet\AutofleetVendorService;
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
  protected $description = 'Sync vendor lists';

  public function handle(AutofleetVendorService $autofleetVendorService)
  {
    $autofleetVendorService->syncVendorLists();
  }
}
