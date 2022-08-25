<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Airtable;
use Carbon\Carbon;

class ApiController extends Controller
{
    public function getVelocityList()
    {
        $result = Airtable::table('Sites')->all();
        return response()->json(['sites' => $result]);
    }

    public function getAccountManagers()
    {
        $result = Airtable::where('Site Name', 'AAA')->get();
        return response()->json(['account_manager' => $result[0]['fields']['Account Manager']]);
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
}
