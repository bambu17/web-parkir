<?php

namespace Tests\Feature;

use App\Models\ParkingArea;
use App\Models\ParkingRate;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminCrudTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(User::factory()->create(['role' => 'admin']));
    }

    public function test_admin_can_create_update_and_delete_users_rates_areas_and_vehicles(): void
    {
        $user = User::factory()->make();
        $this->post(route('users.store'), [
            'name' => $user->name,
            'email' => $user->email,
            'role' => 'petugas',
            'password' => 'password123',
        ])->assertRedirect();
        $createdUser = User::where('email', $user->email)->firstOrFail();
        $this->assertDatabaseHas('users', ['id' => $createdUser->id, 'role' => 'petugas']);

        $this->put(route('users.update', $createdUser), [
            'name' => 'Petugas Diperbarui',
            'email' => $user->email,
            'role' => 'owner',
        ])->assertRedirect();
        $this->assertDatabaseHas('users', ['id' => $createdUser->id, 'name' => 'Petugas Diperbarui', 'role' => 'owner']);
        $this->delete(route('users.delete', $createdUser))->assertRedirect();
        $this->assertDatabaseMissing('users', ['id' => $createdUser->id]);

        $this->post(route('rates.store'), [
            'vehicle_type' => 'motor',
            'first_hour_rate' => 3000,
            'next_hour_rate' => 2000,
        ])->assertRedirect();
        $rate = ParkingRate::firstOrFail();
        $this->put(route('rates.update', $rate), [
            'vehicle_type' => 'mobil',
            'first_hour_rate' => 5000,
            'next_hour_rate' => 3000,
        ])->assertRedirect();
        $this->assertDatabaseHas('parking_rates', ['id' => $rate->id, 'vehicle_type' => 'mobil', 'first_hour_rate' => 5000]);
        $this->delete(route('rates.delete', $rate))->assertRedirect();
        $this->assertDatabaseMissing('parking_rates', ['id' => $rate->id]);

        $this->post(route('areas.store'), ['name' => 'Lantai Satu', 'code' => 'L1', 'capacity' => 30])->assertRedirect();
        $area = ParkingArea::firstOrFail();
        $this->put(route('areas.update', $area), ['name' => 'Lantai Dua', 'code' => 'L2', 'capacity' => 40])->assertRedirect();
        $this->assertDatabaseHas('parking_areas', ['id' => $area->id, 'name' => 'Lantai Dua', 'capacity' => 40]);
        $this->delete(route('areas.delete', $area))->assertRedirect();
        $this->assertDatabaseMissing('parking_areas', ['id' => $area->id]);

        $this->post(route('vehicles.store'), [
            'plate_number' => 'B 1234 XYZ',
            'owner_name' => 'Pemilik Awal',
            'vehicle_type' => 'motor',
            'color' => 'Hitam',
        ])->assertRedirect();
        $vehicle = Vehicle::firstOrFail();
        $this->put(route('vehicles.update', $vehicle), [
            'plate_number' => 'B 5678 XYZ',
            'owner_name' => 'Pemilik Baru',
            'vehicle_type' => 'mobil',
            'color' => '',
        ])->assertRedirect();
        $this->assertDatabaseHas('vehicles', ['id' => $vehicle->id, 'plate_number' => 'B 5678 XYZ', 'owner_name' => 'Pemilik Baru']);
        $this->delete(route('vehicles.delete', $vehicle))->assertRedirect();
        $this->assertDatabaseMissing('vehicles', ['id' => $vehicle->id]);
    }

    public function test_admin_can_create_and_check_out_a_parking_transaction(): void
    {
        $area = ParkingArea::create(['name' => 'Area A', 'code' => 'A1', 'capacity' => 20]);
        ParkingRate::create(['vehicle_type' => 'motor', 'first_hour_rate' => 3000, 'next_hour_rate' => 2000]);

        $this->post(route('transactions.store'), [
            'plate_number' => 'D 1234 ABC',
            'vehicle_type' => 'motor',
            'parking_area_id' => $area->id,
        ])->assertRedirect();

        $transaction = Transaction::firstOrFail();
        $this->assertDatabaseHas('transactions', ['id' => $transaction->id, 'status' => 'parked']);

        $this->patch(route('transactions.checkout', $transaction))
            ->assertRedirect(route('receipt', $transaction));

        $this->assertDatabaseHas('transactions', [
            'id' => $transaction->id,
            'status' => 'paid',
            'total_amount' => 3000,
        ]);
        $this->get(route('receipt', $transaction))->assertOk();
    }

    public function test_records_referenced_by_transactions_cannot_be_deleted(): void
    {
        $area = ParkingArea::create(['name' => 'Area Terpakai', 'code' => 'A2', 'capacity' => 20]);
        $user = User::factory()->create(['role' => 'petugas']);
        $transaction = Transaction::create([
            'ticket_number' => 'PKR-TEST-0001',
            'user_id' => $user->id,
            'parking_area_id' => $area->id,
            'plate_number' => 'F 1234 ABC',
            'vehicle_type' => 'motor',
            'entry_at' => now(),
        ]);

        $this->delete(route('users.delete', $user))->assertStatus(422);
        $this->delete(route('areas.delete', $area))->assertStatus(422);

        $this->assertDatabaseHas('users', ['id' => $user->id]);
        $this->assertDatabaseHas('parking_areas', ['id' => $area->id]);
        $this->assertDatabaseHas('transactions', ['id' => $transaction->id]);
    }

    public function test_non_admin_cannot_access_admin_crud_routes(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'petugas']));

        $this->get(route('users'))->assertForbidden();
        $this->post(route('areas.store'), ['name' => 'Blocked', 'code' => 'X1', 'capacity' => 10])->assertForbidden();
    }
}