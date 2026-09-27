<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\ParkingArea;
use App\Models\ParkingRate;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ParkingController extends Controller
{
    public function loginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate(['email' => ['required', 'email'], 'password' => ['required']]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors(['email' => 'Email atau password tidak sesuai.'])->onlyInput('email');
        }

        $request->session()->regenerate();
        $this->log('login', 'Masuk ke sistem');

        return redirect()->route('dashboard');
    }

    public function logout(Request $request)
    {
        $this->log('logout', 'Keluar dari sistem');
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    public function dashboard()
    {
        return view('dashboard', [
            'activeCount' => Transaction::where('status', 'parked')->count(),
            'todayCount' => Transaction::whereDate('entry_at', today())->count(),
            'todayRevenue' => Transaction::whereDate('exit_at', today())->sum('total_amount'),
            'areaCount' => ParkingArea::where('active', true)->count(),
            'recentTransactions' => Transaction::with('area')->latest()->limit(8)->get(),
        ]);
    }

    public function users()
    {
        return view('resource', ['title' => 'Pengguna', 'resource' => 'users', 'items' => User::latest()->get(), 'fields' => ['name' => 'Nama', 'email' => 'Email', 'role' => 'Role']]);
    }

    public function storeUser(Request $request)
    {
        $data = $request->validate(['name' => ['required'], 'email' => ['required', 'email', 'unique:users'], 'role' => ['required', 'in:admin,petugas,owner'], 'password' => ['required', 'min:8']]);
        $data['password'] = Hash::make($data['password']);
        User::create($data);
        $this->log('create_user', "Menambah pengguna {$data['email']}");

        return back()->with('success', 'Pengguna berhasil ditambahkan.');
    }

    public function deleteUser(User $user)
    {
        abort_if($user->is(Auth::user()), 422, 'Akun yang sedang digunakan tidak dapat dihapus.');
        abort_if($user->transactions()->exists(), 422, 'Pengguna dengan transaksi parkir tidak dapat dihapus.');
        $user->delete();

        return back()->with('success', 'Pengguna berhasil dihapus.');
    }

    public function updateUser(Request $request, User $user)
    {
        $data = $request->validate(['name' => ['required'], 'email' => ['required', 'email', 'unique:users,email,' . $user->id], 'role' => ['required', 'in:admin,petugas,owner']]);
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->input('password'));
        }
        $user->update($data);

        return back()->with('success', 'Pengguna berhasil diperbarui.');
    }

    public function rates()
    {
        return view('resource', ['title' => 'Tarif Parkir', 'resource' => 'rates', 'items' => ParkingRate::latest()->get(), 'fields' => ['vehicle_type' => 'Jenis kendaraan', 'first_hour_rate' => 'Jam pertama', 'next_hour_rate' => 'Jam berikutnya']]);
    }

    public function storeRate(Request $request)
    {
        ParkingRate::create($request->validate(['vehicle_type' => ['required', 'in:motor,mobil,lainnya'], 'first_hour_rate' => ['required', 'numeric', 'min:0'], 'next_hour_rate' => ['required', 'numeric', 'min:0']]));
        $this->log('create_rate', 'Menambah tarif parkir');

        return back()->with('success', 'Tarif berhasil ditambahkan.');
    }

    public function deleteRate(ParkingRate $rate)
    {
        $rate->delete();

        return back()->with('success', 'Tarif berhasil dihapus.');
    }

    public function updateRate(Request $request, ParkingRate $rate)
    {
        $rate->update($request->validate(['vehicle_type' => ['required', 'in:motor,mobil,lainnya'], 'first_hour_rate' => ['required', 'numeric', 'min:0'], 'next_hour_rate' => ['required', 'numeric', 'min:0']]));

        return back()->with('success', 'Tarif berhasil diperbarui.');
    }

    public function areas()
    {
        return view('resource', ['title' => 'Area Parkir', 'resource' => 'areas', 'items' => ParkingArea::latest()->get(), 'fields' => ['name' => 'Nama area', 'code' => 'Kode', 'capacity' => 'Kapasitas']]);
    }

    public function storeArea(Request $request)
    {
        ParkingArea::create($request->validate(['name' => ['required'], 'code' => ['required', 'unique:parking_areas,code'], 'capacity' => ['required', 'integer', 'min:1']]));
        $this->log('create_area', 'Menambah area parkir');

        return back()->with('success', 'Area berhasil ditambahkan.');
    }

    public function deleteArea(ParkingArea $area)
    {
        abort_if($area->transactions()->exists(), 422, 'Area yang sudah digunakan dalam transaksi tidak dapat dihapus.');
        $area->delete();

        return back()->with('success', 'Area berhasil dihapus.');
    }

    public function updateArea(Request $request, ParkingArea $area)
    {
        $area->update($request->validate(['name' => ['required'], 'code' => ['required', 'unique:parking_areas,code,' . $area->id], 'capacity' => ['required', 'integer', 'min:1']]));

        return back()->with('success', 'Area berhasil diperbarui.');
    }

    public function vehicles()
    {
        return view('resource', ['title' => 'Kendaraan', 'resource' => 'vehicles', 'items' => Vehicle::latest()->get(), 'fields' => ['plate_number' => 'Nomor polisi', 'owner_name' => 'Pemilik', 'vehicle_type' => 'Jenis', 'color' => 'Warna']]);
    }

    public function storeVehicle(Request $request)
    {
        Vehicle::create($request->validate(['plate_number' => ['required', 'unique:vehicles,plate_number'], 'owner_name' => ['required'], 'vehicle_type' => ['required', 'in:motor,mobil,lainnya'], 'color' => ['nullable']]));
        $this->log('create_vehicle', 'Menambah kendaraan');

        return back()->with('success', 'Kendaraan berhasil ditambahkan.');
    }

    public function deleteVehicle(Vehicle $vehicle)
    {
        $vehicle->delete();

        return back()->with('success', 'Kendaraan berhasil dihapus.');
    }

    public function updateVehicle(Request $request, Vehicle $vehicle)
    {
        $vehicle->update($request->validate(['plate_number' => ['required', 'unique:vehicles,plate_number,' . $vehicle->id], 'owner_name' => ['required'], 'vehicle_type' => ['required', 'in:motor,mobil,lainnya'], 'color' => ['nullable']]));

        return back()->with('success', 'Kendaraan berhasil diperbarui.');
    }

    public function transactions()
    {
        return view('transactions.index', ['transactions' => Transaction::with('area')->latest()->paginate(15), 'areas' => ParkingArea::where('active', true)->get()]);
    }

    public function storeTransaction(Request $request)
    {
        $data = $request->validate(['plate_number' => ['required'], 'vehicle_type' => ['required', 'in:motor,mobil,lainnya'], 'parking_area_id' => ['required', 'exists:parking_areas,id']]);
        $data['ticket_number'] = 'PKR-' . now()->format('ymdHis') . '-' . Str::upper(Str::random(4));
        $data['user_id'] = Auth::id();
        $data['entry_at'] = now();
        Transaction::create($data);
        $this->log('entry', "Kendaraan {$data['plate_number']} masuk parkir");

        return back()->with('success', 'Transaksi parkir berhasil dibuat.');
    }

    public function checkout(Transaction $transaction)
    {
        abort_if($transaction->status === 'paid', 422, 'Transaksi ini sudah dibayar.');
        $exit = now();
        $hours = max(1, $transaction->entry_at->diffInHours($exit) + ($transaction->entry_at->diffInMinutes($exit) % 60 > 0 ? 1 : 0));
        $rate = ParkingRate::where('vehicle_type', $transaction->vehicle_type)->where('active', true)->first();
        $amount = $rate ? (float) $rate->first_hour_rate + max(0, $hours - 1) * (float) $rate->next_hour_rate : 0;
        $transaction->update(['exit_at' => $exit, 'total_amount' => $amount, 'status' => 'paid']);
        $this->log('checkout', "Kendaraan {$transaction->plate_number} keluar parkir");

        return redirect()->route('receipt', $transaction);
    }

    public function receipt(Transaction $transaction)
    {
        return view('transactions.receipt', compact('transaction'));
    }

    public function report(Request $request)
    {
        $from = Carbon::parse($request->input('from', today()->startOfDay()));
        $to = Carbon::parse($request->input('to', today()->endOfDay()));
        $transactions = Transaction::with('area')->whereBetween('entry_at', [$from, $to])->latest()->get();

        return view('reports.index', compact('transactions', 'from', 'to'));
    }

    public function logs()
    {
        return view('logs.index', ['logs' => ActivityLog::with('user')->latest()->paginate(20)]);
    }

    private function log(string $action, string $description): void
    {
        ActivityLog::create(['user_id' => Auth::id(), 'action' => $action, 'description' => $description, 'ip_address' => request()->ip()]);
    }
}