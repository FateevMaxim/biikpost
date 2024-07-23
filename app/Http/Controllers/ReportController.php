<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\City;
use App\Models\Configuration;
use App\Models\TrackList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    public function getTrackReportPage(){
        $cities = Branch::query()->select('title')->get();
        $config = Configuration::query()->select('title_text', 'address')->first();
        $china_address =  $this->getChinaAddress($config->address);
        $city = '';
        $date = '';
        $status = '';
        return view('report', compact('cities', 'config', 'city', 'date', 'status', 'china_address'));
    }

    public function getTrackReport(Request $request){

        $city = '';
        $date = '';
        $status = '';
        $query = TrackList::query()
            ->select('track_code', 'status', 'city');
        if ($request->date != null){
            $query->whereDate('to_client', $request->date);
            $date = $request->date;
        }
        if ($request->city != 'Выберите город'){
            $query->where('city', 'LIKE', $request->city);
            $city = $request->city;
        }
        if ($request->status != 'Выберите статус'){
            $query->where('status', 'LIKE', $request->status."%");
            $status = $request->status;
        }
        $cities = Branch::query()->select('title')->get();
        $tracks = $query->with('user')->get();
        $count = $tracks->count();
        $config = Configuration::query()->select('title_text', 'address')->first();
        $china_address =  $this->getChinaAddress($config->address);

        return view('report', compact('tracks', 'cities', 'config', 'city', 'date', 'status', 'count', 'china_address'));

    }

    public function getChinaAddress($address): array
    {
        $branch = Branch::query()->select('whats_app', 'address')->where('title', auth()->user()->branch)->first();
        $china_address =  [
            'address' => $address,
            'picture' => 'chinashim'
        ];
        if (isset(Auth::user()->branchinfo->china_address)){
            $china_address = [
                'address' => $branch->address,
                'picture' => Auth::user()->branchinfo->china_address,
            ];
        }
        return $china_address;
    }
}
