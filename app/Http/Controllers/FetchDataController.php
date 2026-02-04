<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class FetchDataController extends Controller
{
    public function index()
    {
        //fetching data for productionlist/dashboard
        $SAPData = DB::table('sap_data')->get();
        $hubData = DB::table('hub_data')->get();

        return Inertia::render('Dashboard', [
            'sapData' => $SAPData,
            'hubData' => $hubData,
        ]);
    }
}
