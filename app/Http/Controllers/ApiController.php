<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Airtable;
use Carbon\Carbon;
use Akaunting\Money\Currency;
use Akaunting\Money\Money;

class ApiController extends Controller
{
    public function getVelocityList(Request $request)
    {
        $results = Airtable::table('Velocity Sites')->all();
        $data = '';
        $count = 1;
        foreach ($results as $result) {
            $data .=  $count . ". " . ucwords($result['fields']['Site Name']) . "\n";
            $count++;
        }

        $obj = (object) array(
            "type" => "section",
                "text" => [
                    "type" => "mrkdwn",
                    "text" => $data
                ])
                ;
        $blocks = [];
        array_push($blocks, $obj);
        return response()->json([
            "response_type" => "in_channel",
            "blocks" => $blocks
        ]);
    }

    public function getAccountManagers(Request $request)
    {
        $result = Airtable::where('Site Name', strtolower($request->text))->get();

        if (!$result->isEmpty()) {
            return response()->json(
                [
                "response_type" => "in_channel",
                'text' => "The account manager for ". $request->text . " is: " . $result[0]['fields']['Account Manager']
                ]
            );
        } else {
            return response()->json(
                [
                "response_type" => "in_channel",
                'text' => "No results found for ". $request->text
                ]
            );
        }
    }

    public function getStatus(Request $request)
    {
        $result = Airtable::where('Site Name', strtolower($request->text))->get();

        if (!$result->isEmpty()) {
            return response()->json(
                [
                "response_type" => "in_channel",
                'text' => $request->text . " is " . $result[0]['fields']['Status']
                ]
            );
        } else {
            return response()->json(
                [
                "response_type" => "in_channel",
                'text' => "No results found for ". $request->text
                ]
            );
        }
    }

    public function LiveURL(Request $request)
    {
        $result = Airtable::where('Site Name', strtolower($request->text))->get();
        if (!$result->isEmpty()) {
            return response()->json(
                [
                "response_type" => "in_channel",
                'text' => "Live URL for " . $request->text . " is " . $result[0]['fields']['Live URL']
                ]
            );
        } else {
            return response()->json(
                [
                "response_type" => "in_channel",
                'text' => "No results found for ". $request->text
                ]
            );
        }
    }

    public function StagingURL(Request $request)
    {
        $result = Airtable::where('Site Name', strtolower($request->text))->get();

        if (!$result->isEmpty()) {
            if ($result[0]['fields']['Staging URL']) {
                return response()->json(
                    [
                    "response_type" => "in_channel",
                    'text' => "Staging URL for " . $request->text . " is " . $result[0]['fields']['Staging URL']
                    ]
                );
            } else {
                return response()->json(
                    [
                    "response_type" => "in_channel",
                    'text' => $request->text . " does not have a staging URL listed."
                    ]
                );
            }
        } else {
            return response()->json(
                [
                "response_type" => "in_channel",
                'text' => "No results found for ". $request->text
                ]
            );
        }
    }

    public function getLaunchDate(Request $request)
    {
        $result = Airtable::where('Site Name', strtolower($request->text))->get();

        if (!$result->isEmpty()) {
            $date = Carbon::parse($result[0]['fields']['Launch Date'])->subHours(4);
            return response()->json(
                [
                "response_type" => "in_channel",
                'text' => $request->text . " has been live since " . $date->diffForHumans() . ". It launched ". $date->toDayDateTimeString()
                ]
            );
        } else {
            return response()->json(
                [
                "response_type" => "in_channel",
                'text' => "No results found for ". $request->text
                ]
            );
        }
    }

    //-- Get Client List
    public function getClientList(Request $request)
    {
        $client = new \Zadorin\Airtable\Client(env('AIRTABLE_KEY'), env('AIRTABLE_BASE'));

        $recordset = $client->table('Clients')
        ->select('Client Name')
        ->orderBy(['Client Name' => 'desc'])
        ->execute()
        ->asArray();

        $data = '';
        $count = 1;
        foreach ($recordset as $result) {
            $data .=  $count . ". " . ucwords($result['Client Name']) . "\n";
            $count++;
        }

        $obj = (object) array(
            "type" => "section",
                "text" => [
                    "type" => "mrkdwn",
                    "text" => $data
                ])
                ;
        $blocks = [];
        array_push($blocks, $obj);
        return response()->json([
            "response_type" => "in_channel",
            "blocks" => $blocks
        ]);
    }

    public function getRetainerDetails(Request $request)
    {
        $client = new \Zadorin\Airtable\Client(env('AIRTABLE_KEY'), env('AIRTABLE_BASE'));

        $recordset = $client->table('Clients')
        ->select('*')
        ->where(['Client Name' => strtolower($request->text)])
        ->execute()
        ->asArray();

        if ($recordset) {
            $date = Carbon::parse($recordset[0]['Last Updated']);
            return response()->json(
                [
                "response_type" => "in_channel",
                'text' => $request->text . " has a monthly retainer of " . Money::USD($recordset[0]['Retainer'], true) . ". Last updated on: ". $date->toFormattedDateString()
                ]
            );
        } else {
            return response()->json(
                [
                "response_type" => "in_channel",
                'text' => "No results found for ". $request->text
                ]
            );
        }
    }

    public function getHostingInformation(Request $request)
    {
        $client = new \Zadorin\Airtable\Client(env('AIRTABLE_KEY'), env('AIRTABLE_BASE'));

        $recordset = $client->table('Clients')
        ->select('*')
        ->where(['Client Name' => strtolower($request->text)])
        ->execute()
        ->asArray();

        if ($recordset) {
            return response()->json(
                [
                "response_type" => "in_channel",
                'text' => $request->text . " is hosted with: " . $recordset[0]['Hosted']
                ]
            );
        } else {
            return response()->json(
                [
                "response_type" => "in_channel",
                'text' => "No results found for ". $request->text
                ]
            );
        }
    }

    public function getDashboardsList(Request $request)
    {
        $client = new \Zadorin\Airtable\Client(env('AIRTABLE_KEY'), env('AIRTABLE_BASE'));

        $recordset = $client->table('Dashboards')
        ->select('Dashboard Name')
        ->orderBy(['Dashboard Name' => 'desc'])
        ->execute()
        ->asArray();

        $data = '';
        $count = 1;
        foreach ($recordset as $result) {
            $data .=  $count . ". " . ucwords($result['Dashboard Name']) . "\n";
            $count++;
        }

        $obj = (object) array(
            "type" => "section",
                "text" => [
                    "type" => "mrkdwn",
                    "text" => $data
                ])
                ;
        $blocks = [];
        array_push($blocks, $obj);
        return response()->json([
            "response_type" => "in_channel",
            "blocks" => $blocks
        ]);
    }

    public function getDashboardDetails(Request $request)
    {
        $client = new \Zadorin\Airtable\Client(env('AIRTABLE_KEY'), env('AIRTABLE_BASE'));

        $recordset = $client->table('Dashboards')
        ->select('*')
        ->where(['Dashboard Name' => strtolower($request->text)])
        ->execute()
        ->asArray();

        if ($recordset) {
            $obj = (object) array(
                "type" => "section",
                    "text" => [
                        "type" => "actions",
                        "elements" => [
                            "type" => "button",
                            "text" => "View " . $request->text . " Dashboard",
                        ],
                        "value" => "click_me_111",
                        "url" => $recordset[0]['URL']
                    ]);
            $divider = (object) array(
                "type" => "divider"
            );

            $context = (object) array(
                "type" => "context",
                "elements" => [
                    "type" => "mrkdwn",
                    "text" => "Account manager for this dashboard is: " . $recordset[0]['Account Manager']

                ]
            );



            $blocks = [];
            array_push($blocks, $obj);
            array_push($blocks, $divider);


            return response()->json([
                "response_type" => "in_channel",
                "blocks" => $blocks
            ]);

        // return response()->json(
            //     [
            //     "response_type" => "in_channel",
            //     'text' => $request->text . " dashboard URL: " . $recordset[0]['URL'] . ". Account manager for this dashboard is: " . $recordset[0]['Account Manager']
            //     ]
            // );
        } else {
            return response()->json(
                [
                "response_type" => "in_channel",
                'text' => "No results found for ". $request->text
                ]
            );
        }
    }
}
