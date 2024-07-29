<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\ClientTrackList;
use App\Models\Configuration;
use App\Models\Message;
use App\Models\QrCodes;
use App\Models\TrackList;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index ()
    {

        $qrChina = QrCodes::query()->select()->where('id', 1)->first();
        $qrShimkent = QrCodes::query()->select()->where('id', 2)->first();
        $qrAktobe = QrCodes::query()->select()->where('id', 3)->first();
        $qrAktau = QrCodes::query()->select()->where('id', 4)->first();
        $qrTaraz = QrCodes::query()->select()->where('id', 5)->first();
        $qrPavlodar = QrCodes::query()->select()->where('id', 6)->first();
        $config = Configuration::query()->select('address', 'title_text')->first();
        $china_address = $this->getChinaAddress($config->address);
        $messages = Message::all();

        if (Auth::user()->is_active === 1 && Auth::user()->type === null){
            $tracks = ClientTrackList::query()
                ->leftJoin('track_lists', 'client_track_lists.track_code', '=', 'track_lists.track_code')
                ->select( 'client_track_lists.track_code', 'client_track_lists.detail', 'client_track_lists.created_at',
                    'track_lists.to_china','track_lists.to_almaty','track_lists.city','client_track_lists.id','track_lists.to_client','track_lists.client_accept','track_lists.status')
                ->where('client_track_lists.user_id', Auth::user()->id)
                ->where('client_track_lists.status',null)
                ->orderByDesc('client_track_lists.id')
                ->get();
            $count = count($tracks);
            return view('dashboard')->with(compact('tracks', 'count', 'messages', 'config', 'china_address'));
        }elseif (Auth::user()->is_active === 1 && Auth::user()->type === 'stock'){
            $count = TrackList::query()->whereDate('created_at', Carbon::today())->count();
            return view('stock')->with(compact('count', 'config', 'china_address'));
        }elseif (Auth::user()->type === 'newstock') {
            $count = TrackList::query()->whereDate('created_at', Carbon::today())->count();
            return view('newstock')->with(compact('count', 'config', 'qrChina', 'china_address'));
        }elseif (Auth::user()->type === 'shimkentin') {
            $count = TrackList::query()->whereDate('to_almaty', Carbon::today())->where('status', 'Получено на складе в Шымкенте')->count();
            return view('almaty', ['count' => $count, 'config' => $config, 'cityin' => 'Шымкенте', 'qr' => $qrShimkent, 'china_address' => $china_address]);
        }elseif (Auth::user()->type === 'aktobein') {
            $count = TrackList::query()->whereDate('to_almaty', Carbon::today())->where('status', 'Получено на складе в Ақтобе')->count();
            return view('almaty', ['count' => $count, 'config' => $config, 'cityin' => 'Ақтобе', 'qr' => $qrAktobe, 'china_address' => $china_address]);
        }elseif (Auth::user()->type === 'aktauin') {
            $count = TrackList::query()->whereDate('to_almaty', Carbon::today())->where('status', 'Получено на складе в Актау')->count();
            return view('almaty', ['count' => $count, 'config' => $config, 'cityin' => 'Актау', 'qr' => $qrAktau, 'china_address' => $china_address]);
        }elseif (Auth::user()->type === 'tarazin') {
            $count = TrackList::query()->whereDate('to_almaty', Carbon::today())->where('status', 'Получено на складе в Таразе')->count();
            return view('almaty', ['count' => $count, 'config' => $config, 'cityin' => 'Таразе', 'qr' => $qrTaraz, 'china_address' => $china_address]);
        }elseif (Auth::user()->type === 'pavlodarin') {
            $count = TrackList::query()->whereDate('to_almaty', Carbon::today())->where('status', 'Получено на складе в Павлодаре')->count();
            return view('almaty', ['count' => $count, 'config' => $config, 'cityin' => 'Павлодаре', 'qr' => $qrPavlodar, 'china_address' => $china_address]);
        }elseif (Auth::user()->type === 'astanain') {
            $count = TrackList::query()->whereDate('to_almaty', Carbon::today())->where('status', 'Получено на складе в Астане')->count();
            return view('almaty', ['count' => $count, 'config' => $config, 'cityin' => 'Астане', 'qr' => $qrPavlodar, 'china_address' => $china_address]);
        }elseif (Auth::user()->type === 'kizilordain') {
            $count = TrackList::query()->whereDate('to_almaty', Carbon::today())->where('status', 'Получено на складе в Кызылорде')->count();
            return view('almaty', ['count' => $count, 'config' => $config, 'cityin' => 'Кузылорде', 'qr' => $qrPavlodar, 'china_address' => $china_address]);
        }elseif (Auth::user()->type === 'almatyin') {
            $count = TrackList::query()->whereDate('to_almaty', Carbon::today())->where('status', 'Получено на складе в Алматы')->count();
            return view('almaty', ['count' => $count, 'config' => $config, 'cityin' => 'Алматы', 'qr' => $qrPavlodar, 'china_address' => $china_address]);
        }elseif (Auth::user()->type === 'shimkentout') {
            $count = TrackList::query()->whereDate('to_client', Carbon::today())->where('city', 'Шымкент')->count();
            return view('almatyout', ['count' => $count, 'config' => $config, 'cityin' => 'Шымкенте', 'qr' => $qrShimkent, 'china_address' => $china_address]);
        }elseif (Auth::user()->type === 'aktobeout') {
            $count = TrackList::query()->whereDate('to_client', Carbon::today())->where('city', 'Ақтобe')->count();
            return view('almatyout', ['count' => $count, 'config' => $config, 'cityin' => 'Ақтобе', 'qr' => $qrAktobe, 'china_address' => $china_address]);
        }elseif (Auth::user()->type === 'aktauout') {
            $count = TrackList::query()->whereDate('to_client', Carbon::today())->where('city', 'Актау')->count();
            return view('almatyout', ['count' => $count, 'config' => $config, 'cityin' => 'Актау', 'qr' => $qrAktau, 'china_address' => $china_address]);
        }elseif (Auth::user()->type === 'tarazout') {
            $count = TrackList::query()->whereDate('to_client', Carbon::today())->where('city', 'Тараз')->count();
            return view('almatyout', ['count' => $count, 'config' => $config, 'cityin' => 'Таразе', 'qr' => $qrTaraz, 'china_address' => $china_address]);
        }elseif (Auth::user()->type === 'pavlodarout') {
            $count = TrackList::query()->whereDate('to_client', Carbon::today())->where('city', 'Павлодар')->count();
            return view('almatyout', ['count' => $count, 'config' => $config, 'cityin' => 'Павлодаре', 'qr' => $qrPavlodar, 'china_address' => $china_address]);
        }elseif (Auth::user()->type === 'astanaout') {
            $count = TrackList::query()->whereDate('to_client', Carbon::today())->where('city', 'Астана')->count();
            return view('almatyout', ['count' => $count, 'config' => $config, 'cityin' => 'Астане', 'qr' => $qrPavlodar, 'china_address' => $china_address]);
        }elseif (Auth::user()->type === 'kizilordaout') {
            $count = TrackList::query()->whereDate('to_client', Carbon::today())->where('city', 'Кызылорда')->count();
            return view('almatyout', ['count' => $count, 'config' => $config, 'cityin' => 'Кызылорде', 'qr' => $qrPavlodar, 'china_address' => $china_address]);
        }elseif (Auth::user()->type === 'almatyout') {
            $count = TrackList::query()->whereDate('to_client', Carbon::today())->where('city', 'Алматы')->count();
            return view('almatyout', ['count' => $count, 'config' => $config, 'cityin' => 'Алматы', 'qr' => $qrPavlodar, 'china_address' => $china_address]);
        }elseif (Auth::user()->is_active === 1 && Auth::user()->type === 'othercity'){
            $count = TrackList::query()->whereDate('to_client', Carbon::today())->count();
            return view('othercity')->with(compact('count', 'config', 'qrChina', 'china_address'));
        }elseif (Auth::user()->is_active === 1 && Auth::user()->type === 'admin' || Auth::user()->is_active === 1 && Auth::user()->type === 'moderator'){
            $search_phrase = '';
            if (Auth::user()->city){
                $users = User::query()
                    ->select('id', 'name', 'surname', 'type', 'login', 'city', 'is_active', 'block', 'password', 'created_at')
                    ->where('type', null)
                    ->where('city', Auth::user()->city)
                    ->where('is_active', false)->get();
            }else{
                $users = User::query()
                    ->select('id', 'name', 'surname', 'type', 'login', 'city', 'is_active', 'block', 'password', 'created_at')
                    ->where('type', null)
                    ->where('is_active', false)
                    ->get();
            }
            return view('admin')->with(compact('users', 'messages', 'search_phrase', 'config', 'china_address'));
        }
        $branch = Branch::query()->select('whats_app', 'address')->where('title', auth()->user()->branch)->first();
        return view('register-me')->with(compact( 'branch'));
    }

    public function archive ()
    {

        $tracks = ClientTrackList::query()
            ->leftJoin('track_lists', 'client_track_lists.track_code', '=', 'track_lists.track_code')
            ->select( 'client_track_lists.track_code', 'client_track_lists.detail', 'client_track_lists.created_at',
                'track_lists.to_china','track_lists.to_almaty','track_lists.to_client', 'track_lists.city','track_lists.client_accept','track_lists.status')
            ->where('client_track_lists.user_id', Auth::user()->id)
            ->where('client_track_lists.status', '=', 'archive')
            ->get();
        $config = Configuration::query()->select('address', 'title_text')->first();
        $china_address = $this->getChinaAddress($config->address);
        $count = count($tracks);
        return view('dashboard')->with(compact('tracks', 'count', 'config', 'china_address'));
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
