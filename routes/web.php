use Illuminate\Support\Facades\Artisan;

Route::get('/api/pindah-database-aliyah', function() {
    try {
        Artisan::call('migrate', ['--force' => true]);
        return "Database berhasil diperbarui: " . Artisan::output();
    } catch (\Exception $e) {
        return "Gagal migrasi: " . $e->getMessage();
    }
});