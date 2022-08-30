<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Airtable;
use Carbon\Carbon;

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

        if (!$result->isEmpty()) {
            return response()->json(
                [
                "response_type" => "in_channel",
                'text' => $request->text . " has a monthly retainer of " . $recordset['Retainer'] . ". Last updated on: ". $recordset['Last Updated']
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
}
