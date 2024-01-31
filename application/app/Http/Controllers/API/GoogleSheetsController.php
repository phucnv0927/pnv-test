<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Google_Client;
use Google\Service\Sheets;

class GoogleSheetsController extends Controller
{
    //
    public function getData()
    {
        // Set the path to your credentials file
        $credentialsPath = storage_path('app/credentials.json');

        // Create a Google Sheets client
        $client = new Google_Client();
        $client->setScopes(Sheets::SPREADSHEETS);
        $client->setAuthConfig($credentialsPath);
        // Create Sheets service
        $service = new \Google_Service_Sheets($client);
        // ID of your Google Sheets document
        $spreadsheetId = env('SPREADSHEET_ID');
        // Name of the sheet within the document
        $range = "⑥1501シリーズ 追加分!A3:Z1001";
        $result = $service->spreadsheets_values->get($spreadsheetId, $range);
        try{
            $numRows = $result->getValues() != null ? count($result->getValues()) : 0;
            printf("%d rows retrieved.", $numRows);
            return $result;
        }
        catch(\Exception $e) {
            // TODO(developer) - handle error appropriately
            echo 'Message: ' .$e->getMessage();
        }
    }
}
