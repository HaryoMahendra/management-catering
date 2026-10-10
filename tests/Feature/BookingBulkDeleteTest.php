<?php

namespace Tests\Feature;

use App\Models\Karyawan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingBulkDeleteTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_can_delete_multiple_karyawan_records(): void
    {
        $user = User::factory()->create([
            'role' => 'super_admin',
        ]);

        $karyawan1 = Karyawan::create([
            'employee_no' => 'E001',
            'name' => 'Budi',
            'employment_under' => 'PT ABC',
            'departemen' => 'Produksi',
            'indonesia_level' => 'L1',
            'location' => 'GCPI 1',
            'schedule' => 'Shift 1',
        ]);

        $karyawan2 = Karyawan::create([
            'employee_no' => 'E002',
            'name' => 'Siti',
            'employment_under' => 'PT ABC',
            'departemen' => 'Produksi',
            'indonesia_level' => 'L2',
            'location' => 'GCPI 1',
            'schedule' => 'Shift 2',
        ]);

        $karyawan3 = Karyawan::create([
            'employee_no' => 'E003',
            'name' => 'Tono',
            'employment_under' => 'PT ABC',
            'departemen' => 'IT',
            'indonesia_level' => 'L3',
            'location' => 'GCPI 2',
            'schedule' => 'Non Shift',
        ]);

        $this->actingAs($user)
            ->delete(route('booking.delete-multiple'), [
                'ids' => [$karyawan1->id, $karyawan2->id],
            ])
            ->assertRedirect();

        $this->assertDatabaseMissing('karyawans', ['id' => $karyawan1->id]);
        $this->assertDatabaseMissing('karyawans', ['id' => $karyawan2->id]);
        $this->assertDatabaseHas('karyawans', ['id' => $karyawan3->id]);
    }
}
