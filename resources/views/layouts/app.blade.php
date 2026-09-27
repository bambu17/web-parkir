<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'ParkirKita' }} | ParkirKita</title>
    <style>
        :root{--ink:#162226;--muted:#687579;--paper:#f5f7f4;--line:#dce5e0;--green:#16735b;--lime:#c9f27b;--orange:#f6a441}*{box-sizing:border-box}body{margin:0;background:var(--paper);color:var(--ink);font-family:Georgia,'Times New Roman',serif}a{color:inherit;text-decoration:none}.shell{display:flex;min-height:100vh}.side{width:240px;background:#102b29;color:#e9f3ed;padding:28px 20px;position:fixed;inset:0 auto 0 0}.brand{font-size:26px;font-weight:bold;letter-spacing:-1px}.brand span{color:var(--lime)}.eyebrow{font:700 11px Arial,sans-serif;text-transform:uppercase;letter-spacing:1.6px;color:#9db5aa;margin:38px 12px 10px}.nav a{display:block;padding:11px 12px;border-radius:8px;color:#c7d9d0;font:600 14px Arial,sans-serif;margin:3px 0}.nav a:hover,.nav a.active{background:#1b4b42;color:#fff}.main{margin-left:240px;width:calc(100% - 240px);padding:34px 5vw 60px}.top{display:flex;justify-content:space-between;align-items:center;margin-bottom:35px}.top h1{font-size:38px;line-height:1;margin:0;letter-spacing:-1.5px}.muted{color:var(--muted);font:14px Arial,sans-serif}.user{display:flex;align-items:center;gap:12px;font:600 13px Arial,sans-serif}.avatar{background:var(--lime);color:#1c3026;border-radius:50%;padding:10px 12px}.btn{border:0;border-radius:7px;padding:11px 16px;background:var(--green);color:#fff;font:700 13px Arial,sans-serif;cursor:pointer}.btn.alt{background:var(--lime);color:#1b3027}.btn.danger{background:#a8463e}.btn.small{padding:8px 10px;font-size:12px}.card{background:#fff;border:1px solid var(--line);border-radius:12px;padding:22px}.grid{display:grid;gap:16px}.stats{grid-template-columns:repeat(4,minmax(0,1fr));margin-bottom:20px}.stat strong{display:block;font-size:30px;margin-top:8px}.stat .label{font:700 11px Arial,sans-serif;color:var(--muted);text-transform:uppercase;letter-spacing:1px}.section{margin-top:20px}.section-head{display:flex;justify-content:space-between;align-items:center;margin-bottom:14px}.section h2{font-size:23px;margin:0}.table-wrap{overflow:auto}.table{width:100%;border-collapse:collapse;font:14px Arial,sans-serif}.table th,.table td{text-align:left;padding:14px 10px;border-bottom:1px solid var(--line);white-space:nowrap}.table th{font-size:11px;text-transform:uppercase;color:var(--muted);letter-spacing:.7px}.badge{display:inline-block;border-radius:99px;padding:5px 9px;background:#e3f4df;color:#27643b;font:700 11px Arial,sans-serif}.badge.warn{background:#fff0ce;color:#8e5b13}.form-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:14px}.field{display:flex;flex-direction:column;gap:7px}.field label{font:700 12px Arial,sans-serif;color:var(--muted)}.field input,.field select{border:1px solid var(--line);border-radius:7px;padding:11px;background:#fbfcfa;font:14px Arial,sans-serif}.full{grid-column:1/-1}.alert{padding:12px 15px;border-radius:8px;background:#e5f3e6;color:#245f38;font:14px Arial,sans-serif;margin-bottom:18px}.errors{background:#ffe9e5;color:#9b342c}.actions{display:flex;gap:8px;align-items:center}.inline{display:inline}.auth{min-height:100vh;display:grid;place-items:center;background:#102b29}.auth-card{width:min(440px,calc(100% - 32px));background:#fff;border-radius:14px;padding:35px}.auth-card h1{font-size:35px;margin:0 0 8px}.auth-card form{margin-top:25px;display:grid;gap:15px}.auth-card .field input{width:100%}.receipt{max-width:620px}.receipt h2{font-size:29px}.receipt-row{display:flex;justify-content:space-between;padding:12px 0;border-bottom:1px dashed var(--line);font:14px Arial,sans-serif}.total{font-size:26px;font-weight:bold;color:var(--green)}@media(max-width:850px){.side{position:static;width:100%;min-height:auto;padding:18px;display:flex;align-items:center;gap:16px}.shell{display:block}.main{margin:0;width:100%;padding:25px 18px}.nav{display:flex;gap:3px;overflow:auto}.nav a{white-space:nowrap}.eyebrow{display:none}.stats{grid-template-columns:repeat(2,1fr)}.top{align-items:flex-start;gap:15px}.top h1{font-size:30px}.user{flex-wrap:wrap;justify-content:flex-end}.form-grid{grid-template-columns:1fr}.full{grid-column:auto}}
    </style>
    <style>
        @media(max-width:850px){
            .shell{display:flex}
            .side{position:fixed;inset:0 auto 0 0;width:200px;min-height:100vh;padding:20px 12px;display:block;overflow-y:auto}
            .brand{font-size:22px}
            .eyebrow{display:block;margin:30px 8px 10px}
            .nav{display:block}
            .nav a{padding:10px 8px;white-space:normal;font-size:13px}
            .main{margin-left:200px;width:calc(100% - 200px);padding:25px 14px 40px}
            .top{display:flex;flex-direction:column;align-items:flex-start;gap:14px}
            .user{justify-content:flex-start;flex-wrap:wrap}
            .stats{grid-template-columns:1fr}
            .card{padding:14px}
        }
        @media(max-width:480px){
            .side{width:145px;padding:18px 8px}
            .brand{font-size:18px}
            .eyebrow{margin:26px 6px 9px;font-size:10px}
            .nav a{padding:9px 6px;font-size:12px}
            .main{margin-left:145px;width:calc(100% - 145px);padding:20px 10px 32px}
            .top h1{font-size:25px}
            .muted{font-size:12px}
            .section-head{align-items:flex-start;gap:8px;flex-wrap:wrap}
        }
    </style>
</head>
<body>
<div class="shell">
    <aside class="side">
        <a class="brand" href="{{ route('dashboard') }}">Parkir<span>Kita</span></a>
        <div class="eyebrow">Menu {{ ucfirst(Auth::user()->role) }}</div>
        <nav class="nav">
            <a href="{{ route('dashboard') }}">Ringkasan</a>
            @if(in_array(Auth::user()->role, ['admin','petugas','owner']))<a href="{{ route('transactions') }}">Transaksi</a>@endif
            @if(in_array(Auth::user()->role, ['admin','owner']))<a href="{{ route('reports') }}">Rekap transaksi</a>@endif
            @if(Auth::user()->role === 'admin')
                <a href="{{ route('users') }}">Pengguna</a><a href="{{ route('rates') }}">Tarif parkir</a><a href="{{ route('areas') }}">Area parkir</a><a href="{{ route('vehicles') }}">Kendaraan</a><a href="{{ route('logs') }}">Log aktivitas</a>
            @endif
        </nav>
    </aside>
    <main class="main">
        <header class="top"><div><div class="muted">{{ now()->translatedFormat('l, d F Y') }}</div><h1>{{ $title ?? 'Ringkasan' }}</h1></div><div class="user"><span class="avatar">{{ strtoupper(substr(Auth::user()->name,0,1)) }}</span>{{ Auth::user()->name }}<form class="inline" method="post" action="{{ route('logout') }}">@csrf<button class="btn small danger">Keluar</button></form></div></header>
        @if(session('success'))<div class="alert">{{ session('success') }}</div>@endif
        @if($errors->any())<div class="alert errors">{{ $errors->first() }}</div>@endif
        @yield('content')
    </main>
</div>
</body>
</html>