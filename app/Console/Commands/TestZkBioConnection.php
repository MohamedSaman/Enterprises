<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Services\ZkBioService;
use Exception;

class TestZkBioConnection extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'zkbio:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test connectivity with the ZKBio Zlink Cloud fingerprint system';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->newLine();
        $this->info('╔══════════════════════════════════════════════════════╗');
        $this->info('║    ZKBio Zlink Cloud — Connection Diagnostics       ║');
        $this->info('╚══════════════════════════════════════════════════════╝');
        $this->newLine();

        $baseUrl   = rtrim(config('zkbio.url', 'https://zlink-open.minervaiot.com'), '/');
        $appKey    = config('zkbio.app_key');
        $appSecret = config('zkbio.app_secret');

        // Show configuration
        $this->info('📋  Configuration:');
        $this->table(
            ['Setting', 'Value'],
            [
                ['API Base URL', $baseUrl],
                ['App Key',      $appKey],
                ['App Secret',   str_repeat('*', max(0, strlen($appSecret) - 4)) . substr($appSecret, -4)],
            ]
        );
        $this->newLine();

        $results = [];

        // ──────────────────────────────────────────────
        // TEST 1: Cloud Server Reachability
        // ──────────────────────────────────────────────
        $this->info('━━━  TEST 1: Cloud Server Reachability  ━━━');
        try {
            $start    = microtime(true);
            $response = Http::timeout(10)->get($baseUrl);
            $latency  = round((microtime(true) - $start) * 1000);

            $this->line("   HTTP Status: {$response->status()}");
            $this->line("   Latency:     {$latency}ms");

            if ($response->status() < 500) {
                $this->info('   ✅  Cloud Server is REACHABLE');
                $results['Cloud Server Reachability'] = '✅ PASS';
            } else {
                $this->error('   ❌  Cloud server returned error status');
                $results['Cloud Server Reachability'] = '❌ FAIL (HTTP ' . $response->status() . ')';
            }
        } catch (Exception $e) {
            $this->error("   ❌  Cannot reach cloud server: {$e->getMessage()}");
            $results['Cloud Server Reachability'] = '❌ FAIL';
            $this->showSummary($results);
            return Command::FAILURE;
        }
        $this->newLine();

        // ──────────────────────────────────────────────
        // TEST 2: Tenant Token Authentication
        // ──────────────────────────────────────────────
        $this->info('━━━  TEST 2: Tenant Token Authentication  ━━━');
        $this->line('   POST /open-apis/authen/v1/tenantToken/internal');

        // Clear cached token to test fresh auth
        Cache::forget('zkbio_tenant_token');

        $token = null;
        try {
            $start    = microtime(true);
            $response = Http::timeout(15)
                ->retry(2, 1500)
                ->post("{$baseUrl}/open-apis/authen/v1/tenantToken/internal", [
                    'appKey'    => $appKey,
                    'appSecret' => $appSecret,
                ]);
            $latency = round((microtime(true) - $start) * 1000);

            $this->line("   HTTP Status: {$response->status()}");
            $this->line("   Latency:     {$latency}ms");

            if ($response->successful()) {
                $json = $response->json();
                $code = $json['code'] ?? 'UNKNOWN';

                if ($code === 'ZCOP0000') {
                    $token       = $json['data']['tenantToken'] ?? null;
                    $expiresIn   = $json['data']['expiresIn'] ?? 'N/A';
                    $maskedToken = substr($token, 0, 10) . '...' . substr($token, -6);

                    $this->line("   Response Code: {$code}");
                    $this->line("   Tenant Token:  {$maskedToken}");
                    $this->line("   Expires In:    {$expiresIn} seconds");
                    $this->info('   ✅  Authentication SUCCESSFUL');
                    $results['Tenant Token Auth'] = '✅ PASS';
                } else {
                    $message = $json['message'] ?? 'Unknown';
                    $this->error("   ❌  Auth API error: [{$code}] {$message}");
                    $results['Tenant Token Auth'] = "❌ FAIL ({$code})";
                    $this->showSummary($results);
                    return Command::FAILURE;
                }
            } else {
                $this->error("   ❌  Auth HTTP failed (HTTP {$response->status()})");
                $this->line('   Response: ' . substr($response->body(), 0, 300));
                $results['Tenant Token Auth'] = '❌ FAIL (HTTP ' . $response->status() . ')';
                $this->showSummary($results);
                return Command::FAILURE;
            }
        } catch (Exception $e) {
            $this->error("   ❌  Auth exception: {$e->getMessage()}");
            $results['Tenant Token Auth'] = '❌ FAIL';
            $this->showSummary($results);
            return Command::FAILURE;
        }
        $this->newLine();

        // ──────────────────────────────────────────────
        // TEST 3: Fetch Employees
        // ──────────────────────────────────────────────
        $this->info('━━━  TEST 3: Employees (POST /open-apis/org/v1/employees/search)  ━━━');
        $results['Employee Fetch'] = $this->testPostEndpoint(
            $baseUrl, $token,
            '/open-apis/org/v1/employees/search',
            ['pageNumber' => 1, 'pageSize' => 5],
            'employees'
        );
        $this->newLine();

        // ──────────────────────────────────────────────
        // TEST 4: Fetch Departments
        // ──────────────────────────────────────────────
        $this->info('━━━  TEST 4: Departments (POST /open-apis/org/v1/departments/search)  ━━━');
        $results['Department Fetch'] = $this->testPostEndpoint(
            $baseUrl, $token,
            '/open-apis/org/v1/departments/search',
            ['pageNumber' => 1, 'pageSize' => 10],
            'departments'
        );
        $this->newLine();

        // ──────────────────────────────────────────────
        // SUMMARY
        // ──────────────────────────────────────────────
        $this->showSummary($results);

        $allPassed = collect($results)->every(fn($r) => str_contains($r, '✅'));
        return $allPassed ? Command::SUCCESS : Command::FAILURE;
    }

    /**
     * Test a POST-based ZKBio Zlink API endpoint.
     */
    private function testPostEndpoint(string $baseUrl, string $token, string $endpoint, array $body, string $label): string
    {
        try {
            $start    = microtime(true);
            $response = Http::timeout(30)
                ->retry(2, 1500)
                ->withHeaders([
                    'Authorization' => "Bearer {$token}",
                    'Content-Type'  => 'application/json',
                ])
                ->post("{$baseUrl}{$endpoint}", $body);
            $latency = round((microtime(true) - $start) * 1000);

            $this->line("   HTTP Status: {$response->status()}");
            $this->line("   Latency:     {$latency}ms");

            if ($response->successful()) {
                $json = $response->json();
                $code = $json['code'] ?? 'UNKNOWN';

                if ($code === 'ZCOP0000') {
                    $data       = $json['data'] ?? [];
                    $totalCount = $data['totalCount'] ?? $data['total'] ?? 'N/A';
                    $pageSize   = $data['pageSize'] ?? 'N/A';

                    $this->line("   API Code:     {$code}");
                    $this->line("   Total Count:  {$totalCount}");

                    // Show sample records
                    $records = $data['employee'] ?? $data['items'] ?? [];
                    $recordCount = count($records);
                    $this->line("   Page Records: {$recordCount}");

                    if ($recordCount > 0) {
                        $sample = $records[0];
                        $this->line('   Sample keys:  ' . implode(', ', array_keys($sample)));

                        // Show employee-specific info
                        if ($label === 'employees') {
                            $this->newLine();
                            $this->info("   👥  Sample employees:");
                            foreach (array_slice($records, 0, 5) as $emp) {
                                $name = $emp['firstName'] ?? ($emp['name'] ?? 'N/A');
                                $code = $emp['employeeCode'] ?? 'N/A';
                                $this->line("     • [{$code}] {$name}");
                            }
                        }

                        // Show department-specific info
                        if ($label === 'departments') {
                            $this->newLine();
                            $this->info("   🏢  Departments:");
                            foreach (array_slice($records, 0, 10) as $dept) {
                                $name = $dept['name'] ?? $dept['departmentName'] ?? 'N/A';
                                $deptCode = $dept['code'] ?? $dept['departmentCode'] ?? 'N/A';
                                $this->line("     • [{$deptCode}] {$name}");
                            }
                        }
                    }

                    $this->info("   ✅  {$label} endpoint is WORKING");
                    return "✅ PASS ({$totalCount} {$label})";
                } else {
                    $message = $json['message'] ?? 'Unknown';
                    $this->error("   ❌  API error: [{$code}] {$message}");
                    return "❌ FAIL ({$code})";
                }
            } else {
                $this->error("   ❌  HTTP failed ({$response->status()})");
                $this->line('   Response: ' . substr($response->body(), 0, 300));
                return '❌ FAIL (HTTP ' . $response->status() . ')';
            }
        } catch (Exception $e) {
            $this->error("   ❌  Exception: {$e->getMessage()}");
            return '❌ FAIL';
        }
    }

    /**
     * Display the results summary table.
     */
    private function showSummary(array $results): void
    {
        $this->newLine();
        $this->info('╔══════════════════════════════════════════════════════╗');
        $this->info('║              D I A G N O S T I C   S U M M A R Y   ║');
        $this->info('╚══════════════════════════════════════════════════════╝');
        $this->newLine();

        $rows = [];
        foreach ($results as $test => $result) {
            $rows[] = [$test, $result];
        }
        $this->table(['Test', 'Result'], $rows);

        $allPassed = collect($results)->every(fn($r) => str_contains($r, '✅'));

        $this->newLine();
        if ($allPassed) {
            $this->info('🎉  ALL TESTS PASSED — Your fingerprint system is fully connected to ZKBio Zlink Cloud!');
        } else {
            $this->warn('⚠️  Some tests failed. Check the details above for troubleshooting.');
            $this->newLine();
            $this->line('   Common issues:');
            $this->line('   • Invalid App Key or App Secret');
            $this->line('   • Server IP not in the whitelist (Security Settings)');
            $this->line('   • API permissions not granted (Permissions page)');
            $this->line('   • App not published (Version & Release)');
        }
        $this->newLine();
    }
}
