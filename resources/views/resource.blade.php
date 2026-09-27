@extends('layouts.app')
@section('content')
<section class="card">
	<div class="section-head"><h2>Tambah {{ strtolower($title) }}</h2></div>
	<form method="post" action="{{ route($resource.'.store') }}" class="form-grid">
		@csrf
		@foreach($fields as $field => $label)
			<div class="field">
				<label>{{ $label }}</label>
				@if($field === 'role')
					<select name="{{ $field }}" required>
						<option value="admin">Admin</option>
						<option value="petugas">Petugas</option>
						<option value="owner">Owner</option>
					</select>
				@elseif($field === 'vehicle_type')
					<select name="{{ $field }}" required>
						<option value="motor">Motor</option>
						<option value="mobil">Mobil</option>
						<option value="lainnya">Lainnya</option>
					</select>
				@else
					<input
						name="{{ $field }}"
						type="{{ $field === 'capacity' || str_contains($field, 'rate') ? 'number' : ($field === 'email' ? 'email' : 'text') }}"
						@if($field === 'capacity') step="1" @elseif(str_contains($field, 'rate')) step="0.01" @endif
						@if($field !== 'color') required @endif
					>
				@endif
			</div>
		@endforeach
		@if($resource === 'users')
			<div class="field"><label>Password</label><input name="password" type="password" minlength="8" required></div>
		@endif
		<div class="full"><button class="btn">Simpan data</button></div>
	</form>
</section>

<section class="card section">
	<div class="section-head"><h2>Daftar {{ strtolower($title) }}</h2></div>
	<div class="table-wrap">
		<table class="table">
			<thead>
				<tr>@foreach($fields as $label)<th>{{ $label }}</th>@endforeach<th>Aksi</th></tr>
			</thead>
			<tbody>
				@forelse($items as $item)
					<tr>
						@foreach($fields as $field => $label)
							<td>
								@if($field === 'role')
									<select name="{{ $field }}" form="update-resource-{{ $item->id }}" required>
										<option value="admin" @selected($item->{$field} === 'admin')>Admin</option>
										<option value="petugas" @selected($item->{$field} === 'petugas')>Petugas</option>
										<option value="owner" @selected($item->{$field} === 'owner')>Owner</option>
									</select>
								@elseif($field === 'vehicle_type')
									<select name="{{ $field }}" form="update-resource-{{ $item->id }}" required>
										<option value="motor" @selected($item->{$field} === 'motor')>Motor</option>
										<option value="mobil" @selected($item->{$field} === 'mobil')>Mobil</option>
										<option value="lainnya" @selected($item->{$field} === 'lainnya')>Lainnya</option>
									</select>
								@else
									<input
										name="{{ $field }}"
										value="{{ $item->{$field} }}"
										type="{{ $field === 'capacity' || str_contains($field, 'rate') ? 'number' : ($field === 'email' ? 'email' : 'text') }}"
										form="update-resource-{{ $item->id }}"
										@if($field === 'capacity') step="1" @elseif(str_contains($field, 'rate')) step="0.01" @endif
										@if($field !== 'color') required @endif
									>
								@endif
							</td>
						@endforeach
						<td class="actions">
							<form id="update-resource-{{ $item->id }}" method="post" action="{{ route($resource.'.update', $item) }}">
								@csrf
								@method('PUT')
							</form>
							<button class="btn small alt" type="submit" form="update-resource-{{ $item->id }}">Simpan</button>
							<form method="post" action="{{ route($resource.'.delete', $item) }}" onsubmit="return confirm('Hapus data ini?')" class="inline">
								@csrf
								@method('DELETE')
								<button class="btn small danger" type="submit">Hapus</button>
							</form>
						</td>
					</tr>
				@empty
					<tr><td colspan="{{ count($fields) + 1 }}">Belum ada data.</td></tr>
				@endforelse
			</tbody>
		</table>
	</div>
</section>
@endsection