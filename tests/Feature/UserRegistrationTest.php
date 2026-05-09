<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserRegistrationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function dashboard_hanya_bisa_diakses_oleh_user_yang_login()
    {
        // 1. Coba akses tanpa login
        $response = $this->get('/dashboard');

        // 2. Pastikan diarahkan kembali ke halaman login
        $response->assertRedirect('/login');
    }

    /** @test */
    public function user_bisa_melihat_halaman_dashboard()
    {
        // 1. Buat user palsu menggunakan Factory
        $user = User::factory()->create();

        // 2. Akting sebagai user tersebut (Login) dan buka dashboard
        $response = $this->actingAs($user)->get('/dashboard');

        // 3. Pastikan sukses (200) dan ada tulisan tertentu
        $response->assertStatus(200);
        $response->assertSee('Welcome to Dashboard'); 
    }
}