<?php

namespace Modules\Zoho\Actions;

use App\Models\Driver; // Or wherever your Driver model is located
use Asciisd\Zoho\Facades\ZohoManager;

class SyncDriverToZohoAction
{
    public function handle($driver)
    {
        $record = ZohoManager::useModule('Contacts')
            ->searchRecords("Email:equals:{$driver->email}")[0] ?? null;

        if ($record) {
            $driver->update(['zoho_id' => $record->EntityId]);
            return $record->EntityId;
        }

        return null;
    }
}
