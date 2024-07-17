<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SyncData extends Command
{
    protected $signature = 'sync:data';
    protected $description = 'Sync data between crm_3 and crm_1 databases';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        // Define tables and columns to sync
        $tables = [
            'client_info_sheet' => ['client_id', 'company_name'],
            'item_master' => ['item_id', 'item_name'],
            'purchase_order' => ['purchase_order_id', 'po_number', 'date'],
            'po_items' => ['po_item_id', 'po_id', 'item', 'quantity'],
            'sales_order' => ['sales_order_id', 'SO_number', 'date', 'client', 'po_no', 'status']
        ];

        foreach ($tables as $table => $columns) {
            $this->syncTable($table, $columns);
        }

        $this->info('Data synchronization complete.');
    }

    private function syncTable($table, $columns)
    {
        // Fetch data from crm_3
        $data = DB::connection('crm_3')->table($table)->get($columns);

        // Upsert data to crm_1
        foreach ($data as $row) {
            // Convert object to array
            $rowArray = (array) $row;

            // Ensure required fields are present
            if (empty($rowArray[$columns[0]])) {
                continue; // Skip rows with missing primary key
            }

            // Sanitize data: ensure quantity is an integer
            if (isset($rowArray['quantity'])) {
                $rowArray['quantity'] = is_numeric($rowArray['quantity']) ? (int) $rowArray['quantity'] : 0;
            }

            // Update or insert
            DB::connection('crm_1')->table($table)->updateOrInsert(
                [$columns[0] => $rowArray[$columns[0]]], // Use array key to access primary key
                $rowArray // Pass entire row as an array
            );
        }

        $this->info('Synchronized table: ' . $table);
    }
}
