<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Airtable;
use Carbon\Carbon;

class ApiController extends Controller
{
    public function getVelocityList()
    {
        $results = Airtable::table('Sites')->all();
        $data = '';
        $count = 1;
        foreach ($results as $result) {
            $data .=  $count . ". " . $result['fields']['Site Name'] . "\n";
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
        $result = Airtable::where('Site Name', $request->text)->get();

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

    public function getStatus()
    {
        $result = Airtable::where('Site Name', 'AAA')->get();
        return response()->json(['status' => $result[0]['fields']['Status']]);
    }

    public function getLaunchDate()
    {
        $result = Airtable::where('Site Name', 'AAA')->get();
        $date = Carbon::parse($result[0]['fields']['Launch Date'])->subHours(4);
        ;
        return response()->json([
            'long_ago' => $date->diffForHumans(),
            'date_time' => $date->toDayDateTimeString()
        ]);
    }

    public function LiveURL()
    {
        $result = Airtable::where('Site Name', 'AAA')->get();
        return response()->json($result[0]['fields']['Live URL']);
    }

    public function StagingURL()
    {
        $result = Airtable::where('Site Name', 'AAA')->get();
        if ($result[0]['fields']['Staging URL']) {
            return response()->json(['staging_url' => $result[0]['fields']['Staging URL']]);
        } else {
            return response()->json(['err' => 'This property does not have a staging listed.']);
        }
    }

    public function getVelocityListTest(Request $request)
    {
        return response()->json([
            "response_type" => "in_channel",
            "text" => "Text is: " . $request->text
        ]);
    }
}
