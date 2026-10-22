<?php

require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

echo "=================================================================\n";
echo "   Fresh Schema Migration Verification & Schema Diff Audit       \n";
echo "=================================================================\n\n";

$tempDb = 'altayar_fresh_audit_' . time();
$defaultDb = config('database.connections.mysql.database');

echo "Existing local snapshot database: {$defaultDb}\n";
echo "Isolated temporary database: {$tempDb}\n\n";

try {
    // 1. Create isolated temporary database
    echo "Step 1: Creating isolated database {$tempDb}...\n";
    DB::statement("CREATE DATABASE IF NOT EXISTS `{$tempDb}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

    // 2. Configure dynamic connection
    config(['database.connections.audit_fresh' => array_merge(
        config('database.connections.mysql'),
        ['database' => $tempDb]
    )]);

    $auditConn = DB::connection('audit_fresh');

    // 3. Create mock parent tables for foreign key verification
    echo "Step 2: Creating prerequisite parent tables (users, admins, agents) in {$tempDb}...\n";
    $auditConn->statement("
        CREATE TABLE `users` (
            `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    $auditConn->statement("
        CREATE TABLE `admins` (
            `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    $auditConn->statement("
        CREATE TABLE `agents` (
            `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    // 4. Run the final migration on the fresh schema
    echo "Step 3: Running migration 2026_10_23_000001_create_user_miles_transactions_table.php...\n";
    $migrationFile = __DIR__ . '/../database/migrations/2026_10_23_000001_create_user_miles_transactions_table.php';
    $migration = require $migrationFile;

    // Temporarily swap default connection to audit_fresh for migration runner
    DB::setDefaultConnection('audit_fresh');
    
    $migration->up();

    DB::setDefaultConnection('mysql');
    echo "  [PASS] Migration up() executed successfully on fresh database!\n\n";

    // 5. Inspect generated fresh schema
    echo "Step 4: Inspecting generated fresh table schema...\n";
    $freshCreate = $auditConn->select("SHOW CREATE TABLE `user_miles_transactions`")[0]->{'Create Table'};
    $existingCreate = DB::select("SHOW CREATE TABLE `user_miles_transactions`")[0]->{'Create Table'};

    // Query information_schema for detailed column verification
    $columns = $auditConn->select("
        SELECT COLUMN_NAME, COLUMN_TYPE, IS_NULLABLE, COLUMN_KEY, COLUMN_DEFAULT, EXTRA
        FROM information_schema.COLUMNS
        WHERE TABLE_SCHEMA = ? AND TABLE_NAME = 'user_miles_transactions'
        ORDER BY ORDINAL_POSITION
    ", [$tempDb]);

    $columnMap = [];
    foreach ($columns as $c) {
        $columnMap[$c->COLUMN_NAME] = $c;
    }

    // Verify critical columns & types
    assertTest("Column 'id' is bigint unsigned AUTO_INCREMENT", str_contains($columnMap['id']->COLUMN_TYPE, 'bigint') && str_contains($columnMap['id']->EXTRA, 'auto_increment'));
    assertTest("Column 'user_id' is bigint unsigned NOT NULL", str_contains($columnMap['user_id']->COLUMN_TYPE, 'bigint') && $columnMap['user_id']->IS_NULLABLE === 'NO');
    assertTest("Column 'trx' is varchar(40) NOT NULL", str_contains($columnMap['trx']->COLUMN_TYPE, 'varchar(40)') && $columnMap['trx']->IS_NULLABLE === 'NO');
    assertTest("Column 'type' is enum('credit','debit')", str_contains($columnMap['type']->COLUMN_TYPE, "enum('credit','debit')"));
    assertTest("Column 'direction' is tinyint", str_contains($columnMap['direction']->COLUMN_TYPE, 'tinyint'));
    assertTest("Column 'miles' is int unsigned NOT NULL", str_contains($columnMap['miles']->COLUMN_TYPE, 'int') && str_contains($columnMap['miles']->COLUMN_TYPE, 'unsigned'));
    assertTest("Column 'balance_after' is int unsigned NOT NULL", str_contains($columnMap['balance_after']->COLUMN_TYPE, 'int') && str_contains($columnMap['balance_after']->COLUMN_TYPE, 'unsigned'));
    assertTest("Column 'admin_id' is bigint unsigned NULL", str_contains($columnMap['admin_id']->COLUMN_TYPE, 'bigint') && $columnMap['admin_id']->IS_NULLABLE === 'YES');
    assertTest("Column 'agent_id' is bigint unsigned NULL", str_contains($columnMap['agent_id']->COLUMN_TYPE, 'bigint') && $columnMap['agent_id']->IS_NULLABLE === 'YES');
    assertTest("Column 'reversal_of_id' is bigint unsigned NULL", str_contains($columnMap['reversal_of_id']->COLUMN_TYPE, 'bigint') && $columnMap['reversal_of_id']->IS_NULLABLE === 'YES');
    assertTest("Column 'idempotency_key' is varchar(64) NULL", str_contains($columnMap['idempotency_key']->COLUMN_TYPE, 'varchar(64)') && $columnMap['idempotency_key']->IS_NULLABLE === 'YES');
    assertTest("Column 'meta' supports JSON", str_contains($columnMap['meta']->COLUMN_TYPE, 'json') || str_contains($freshCreate, 'json_valid'));

    // Query foreign keys from information_schema
    $fks = $auditConn->select("
        SELECT CONSTRAINT_NAME, COLUMN_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME
        FROM information_schema.KEY_COLUMN_USAGE
        WHERE TABLE_SCHEMA = ? AND TABLE_NAME = 'user_miles_transactions' AND REFERENCED_TABLE_NAME IS NOT NULL
    ", [$tempDb]);

    $fkMap = [];
    foreach ($fks as $fk) {
        $fkMap[$fk->COLUMN_NAME] = $fk->REFERENCED_TABLE_NAME . '(' . $fk->REFERENCED_COLUMN_NAME . ')';
    }

    echo "\nStep 5: Verifying Foreign Key Constraints...\n";
    assertTest("FK user_id references users(id)", ($fkMap['user_id'] ?? null) === 'users(id)');
    assertTest("FK admin_id references admins(id)", ($fkMap['admin_id'] ?? null) === 'admins(id)');
    assertTest("FK agent_id references agents(id)", ($fkMap['agent_id'] ?? null) === 'agents(id)');
    assertTest("FK reversal_of_id references user_miles_transactions(id)", ($fkMap['reversal_of_id'] ?? null) === 'user_miles_transactions(id)');

    // Query Indexes
    echo "\nStep 6: Verifying Unique Keys and Composite Indexes...\n";
    $indexes = $auditConn->select("
        SELECT INDEX_NAME, NON_UNIQUE, GROUP_CONCAT(COLUMN_NAME ORDER BY SEQ_IN_INDEX) as cols
        FROM information_schema.STATISTICS
        WHERE TABLE_SCHEMA = ? AND TABLE_NAME = 'user_miles_transactions'
        GROUP BY INDEX_NAME, NON_UNIQUE
    ", [$tempDb]);

    $indexMap = [];
    foreach ($indexes as $idx) {
        $indexMap[$idx->INDEX_NAME] = [
            'unique' => $idx->NON_UNIQUE == 0,
            'cols' => $idx->cols
        ];
    }

    assertTest("Unique index on 'trx'", ($indexMap['user_miles_transactions_trx_unique']['unique'] ?? false) && $indexMap['user_miles_transactions_trx_unique']['cols'] === 'trx');
    assertTest("Unique index on 'idempotency_key'", ($indexMap['user_miles_transactions_idempotency_key_unique']['unique'] ?? false) && $indexMap['user_miles_transactions_idempotency_key_unique']['cols'] === 'idempotency_key');
    assertTest("Composite index 'idx_umt_user_created' on (user_id, created_at)", ($indexMap['idx_umt_user_created']['cols'] ?? null) === 'user_id,created_at');
    assertTest("Composite index 'idx_umt_user_metrics' on (user_id, type, is_reversal)", ($indexMap['idx_umt_user_metrics']['cols'] ?? null) === 'user_id,type,is_reversal');
    assertTest("Composite index 'idx_umt_source_ref' on (source, reference_id)", ($indexMap['idx_umt_source_ref']['cols'] ?? null) === 'source,reference_id');
    assertTest("Index 'idx_umt_admin' on admin_id", ($indexMap['idx_umt_admin']['cols'] ?? null) === 'admin_id');
    assertTest("Index 'idx_umt_agent' on agent_id", ($indexMap['idx_umt_agent']['cols'] ?? null) === 'agent_id');

    // Step 7: Compare DDL between Fresh and Existing
    echo "\nStep 7: Comparing Fresh Table DDL against Local Existing Table DDL...\n";
    // Normalize auto_increment numbers and spacing
    $cleanFresh = preg_replace('/AUTO_INCREMENT=\d+\s*/', '', $freshCreate);
    $cleanExisting = preg_replace('/AUTO_INCREMENT=\d+\s*/', '', $existingCreate);

    $isIdentical = ($cleanFresh === $cleanExisting);
    echo "Fresh Schema DDL:\n" . $cleanFresh . "\n\n";
    assertTest("Fresh DDL matches existing local Miles table DDL identically", $isIdentical);

    // Step 8: Test down() migration (rollback on fresh schema)
    echo "\nStep 8: Testing migration down() rollback method...\n";
    DB::setDefaultConnection('audit_fresh');
    $migration->down();
    DB::setDefaultConnection('mysql');

    $tableExistsAfterDown = $auditConn->select("
        SELECT COUNT(*) as cnt FROM information_schema.TABLES
        WHERE TABLE_SCHEMA = ? AND TABLE_NAME = 'user_miles_transactions'
    ", [$tempDb])[0]->cnt;

    assertTest("Migration down() drops table cleanly", $tableExistsAfterDown == 0);

    echo "\n[CLEANUP] Dropping temporary audit database {$tempDb}...\n";
    DB::statement("DROP DATABASE IF EXISTS `{$tempDb}`");
    echo "[DONE] Isolated fresh migration audit completed successfully!\n";

} catch (Throwable $e) {
    if (isset($tempDb)) {
        DB::statement("DROP DATABASE IF EXISTS `{$tempDb}`");
    }
    echo "\n[ERROR] " . $e->getMessage() . "\n" . $e->getTraceAsString() . "\n";
    exit(1);
}

function assertTest(string $description, bool $condition): void {
    if ($condition) {
        echo "  [PASS] {$description}\n";
    } else {
        echo "  [FAIL] {$description}\n";
        throw new RuntimeException("Assertion failed: {$description}");
    }
}
