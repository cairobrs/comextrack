<?php

namespace Tests\Feature;

use App\Models\Import;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class ImportExportTest extends TestCase
{
    use RefreshDatabase;

    private function actingUser(): User
    {
        return User::factory()->create([
            'email_verified_at' => now(),
        ]);
    }

    public function test_import_export_returns_excel_200(): void
    {
        $user = $this->actingUser();
        $import = Import::factory()->create();

        $response = $this->actingAs($user)->get(route('imports.export', $import));

        $response->assertOk();
        $this->assertStringContainsString(
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            (string) $response->headers->get('Content-Type')
        );
    }

    public function test_import_export_route_has_throttle_middleware(): void
    {
        $route = Route::getRoutes()->getByName('imports.export');
        $this->assertNotNull($route);
        $middleware = $route->gatherMiddleware();
        $this->assertContains('auth', $middleware);
        $this->assertTrue(
            collect($middleware)->contains(fn ($m) => str_starts_with((string) $m, 'throttle:')),
            'Esperado middleware throttle na rota imports.export'
        );
    }

    public function test_import_export_throttle_limits_requests(): void
    {
        $user = $this->actingUser();
        $import = Import::factory()->create();

        for ($i = 0; $i < 10; $i++) {
            $this->actingAs($user)->get(route('imports.export', $import))->assertOk();
        }

        $this->actingAs($user)->get(route('imports.export', $import))->assertStatus(429);
    }
}
