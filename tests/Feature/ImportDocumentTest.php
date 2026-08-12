<?php

namespace Tests\Feature;

use App\Models\Import;
use App\Models\ImportLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ImportDocumentTest extends TestCase
{
    use RefreshDatabase;

    private function actingUser(): User
    {
        return User::factory()->admin()->create([
            'email_verified_at' => now(),
        ]);
    }

    public function test_document_status_update_creates_log(): void
    {
        $user = $this->actingUser();
        $import = Import::factory()->create(['status_atual' => 'aberto']);
        $document = $import->documents()->where('tipo_documento', 'Invoice')->first();
        $this->assertNotNull($document);

        $this->actingAs($user)->put(route('documents.update', $document), [
            'status' => 'aguardando_correcoes',
            'observacoes' => 'Falta correção na NCM',
        ])->assertRedirect(route('imports.show', $import));

        $this->assertDatabaseHas('import_documents', [
            'id' => $document->id,
            'status' => 'aguardando_correcoes',
        ]);

        $this->assertTrue(
            ImportLog::where('import_id', $import->id)
                ->where('tipo_evento', 'status_documento_alterado')
                ->exists()
        );
    }

    public function test_document_file_upload_and_download(): void
    {
        Storage::fake('local');
        $user = $this->actingUser();
        $import = Import::factory()->create();
        $document = $import->documents()->where('tipo_documento', 'Invoice')->first();
        $file = UploadedFile::fake()->create('nota.pdf', 120, 'application/pdf');

        $this->actingAs($user)->put(route('documents.update', $document), [
            'status' => 'recebido_ok',
            'arquivo' => $file,
        ])->assertRedirect(route('imports.show', $import));

        $document->refresh();
        $this->assertNotNull($document->arquivo);
        Storage::disk('local')->assertExists($document->arquivo);

        $this->actingAs($user)
            ->get(route('documents.download', $document))
            ->assertOk();
    }
}
