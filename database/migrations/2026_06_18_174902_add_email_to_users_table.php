public function up(): void
{
    Schema::table('users', function (Blueprint $table) {
        $table->string('email')->nullable()->after('username');
    });
}

public function down(): void
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropColumn('email');
    });
}