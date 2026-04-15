<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Exception;

/**
 * ZKBio Zlink Cloud API Service
 *
 * Encapsulates all HTTP interactions with the ZKBio Zlink Cloud platform.
 * Handles Tenant Token authentication, token caching, and paginated data fetching.
 *
 * API Reference (Base URL: https://zlink-open.minervaiot.com):
 *   Auth:         POST /open-apis/authen/v1/tenantToken/internal
 *   Employees:    POST /open-apis/org/v1/employees/search
 *   Employee:     GET  /open-apis/org/v1/employees/{employeeId}
 *   Departments:  POST /open-apis/org/v1/departments/search
 *   Attendance:   Push via webhook (open:att_transaction:push)
 *
 * Authentication uses AppKey + AppSecret to obtain a Tenant Access Token.
 * Token is prefixed with "t-" and has a 1-hour validity.
 * All API calls use: Authorization: Bearer {tenantToken}
 */
class ZkBioService
{
    private string $baseUrl;
    private string $appKey;
    private string $appSecret;
    private ?string $tenantToken = null;
    private int $pageSize;

    public function __construct()
    {
        $this->baseUrl   = rtrim(config('zkbio.url', 'https://zlink-open.minervaiot.com'), '/');
        $this->appKey    = config('zkbio.app_key', '');
        $this->appSecret = config('zkbio.app_secret', '');
        $this->pageSize  = (int) config('zkbio.page_size', 100);
    }

    // ─────────────────────────────────────────────────────────────────────────
    //  AUTHENTICATION
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Authenticate and retrieve a Tenant Access Token.
     *
     * POST /open-apis/authen/v1/tenantToken/internal
     * Body: { "appKey": "...", "appSecret": "..." }
     *
     * Response: { "code": "ZCOP0000", "data": { "tenantToken": "t-xxx", "expiresIn": 3600 } }
     *
     * @throws Exception If authentication fails
     */
    public function authenticate(): string
    {
        // Try cached token first
        $cached = Cache::get('zkbio_tenant_token');
        if ($cached) {
            $this->tenantToken = $cached;
            return $this->tenantToken;
        }

        $url = "{$this->baseUrl}/open-apis/authen/v1/tenantToken/internal";

        Log::info('ZKBio Zlink: Requesting tenant token', ['url' => $url]);

        $response = Http::timeout(15)
            ->retry(3, 2000)
            ->post($url, [
                'appKey'    => $this->appKey,
                'appSecret' => $this->appSecret,
            ]);

        if ($response->failed()) {
            Log::error('ZKBio Zlink Auth HTTP Failed', [
                'status'   => $response->status(),
                'response' => $response->body(),
            ]);
            throw new Exception("ZKBio Zlink authentication failed (HTTP {$response->status()})");
        }

        $json = $response->json();
        $code = $json['code'] ?? 'UNKNOWN';

        if ($code !== 'ZCOP0000') {
            $message = $json['message'] ?? 'Unknown error';
            Log::error('ZKBio Zlink Auth Error', ['code' => $code, 'message' => $message]);
            throw new Exception("ZKBio Zlink auth error: [{$code}] {$message}");
        }

        $this->tenantToken = $json['data']['tenantToken'] ?? null;
        $expiresIn         = $json['data']['expiresIn'] ?? 3600;

        if (empty($this->tenantToken)) {
            throw new Exception('ZKBio Zlink auth response did not contain a tenantToken');
        }

        // Cache token for [expiresIn - 300] seconds (5 min safety margin)
        $cacheSeconds = max(60, $expiresIn - 300);
        Cache::put('zkbio_tenant_token', $this->tenantToken, now()->addSeconds($cacheSeconds));

        Log::info('ZKBio Zlink: Tenant token obtained', [
            'token_prefix' => substr($this->tenantToken, 0, 8) . '...',
            'expires_in'   => $expiresIn,
            'cached_for'   => $cacheSeconds,
        ]);

        return $this->tenantToken;
    }

    /**
     * Get the current tenant token (for display/diagnostics).
     */
    public function getToken(): ?string
    {
        return $this->tenantToken;
    }

    // ─────────────────────────────────────────────────────────────────────────
    //  PAGINATED FETCH (POST endpoints)
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Fetch ALL records from a paginated ZKBio Zlink POST endpoint.
     *
     * ZKBio Zlink uses POST with JSON body for search/query endpoints.
     * Request: { "pageNumber": 1, "pageSize": 100, ...filters }
     * Response: { "code": "ZCOP0000", "data": { "totalCount": N, "employee/items": [...] } }
     *
     * @param string $endpoint  e.g. "/open-apis/org/v1/employees/search"
     * @param array  $filters   Optional filters to include in the body
     * @param string $dataKey   The key in response.data that contains the array (e.g. "employee", "items")
     */
    public function fetchAllPaginated(string $endpoint, array $filters = [], string $dataKey = 'items'): array
    {
        $this->ensureAuthenticated();

        $allData     = [];
        $pageNumber  = 1;
        $url         = "{$this->baseUrl}{$endpoint}";

        do {
            $body = array_merge($filters, [
                'pageNumber' => $pageNumber,
                'pageSize'   => $this->pageSize,
            ]);

            $response = Http::timeout(30)
                ->retry(3, 2000)
                ->withHeaders([
                    'Authorization' => "Bearer {$this->tenantToken}",
                    'Content-Type'  => 'application/json',
                ])
                ->post($url, $body);

            if ($response->failed()) {
                Log::warning("ZKBio Zlink fetch failed for {$endpoint}", [
                    'url'    => $url,
                    'body'   => $body,
                    'status' => $response->status(),
                ]);
                break;
            }

            $json = $response->json();
            $code = $json['code'] ?? 'UNKNOWN';

            if ($code !== 'ZCOP0000') {
                Log::warning("ZKBio Zlink API error for {$endpoint}", [
                    'code'    => $code,
                    'message' => $json['message'] ?? '',
                ]);
                break;
            }

            $data = $json['data'] ?? [];

            // The ZKBio Zlink API uses different keys for different endpoints
            // employees/search → "employee", departments/search → "items", etc.
            $records = $data[$dataKey] ?? $data['employee'] ?? $data['items'] ?? [];
            $allData = array_merge($allData, $records);

            $totalCount = $data['totalCount'] ?? $data['total'] ?? 0;
            $totalPages = $data['totalPages'] ?? ceil($totalCount / $this->pageSize);

            $pageNumber++;

        } while ($pageNumber <= $totalPages && count($records) > 0);

        return $allData;
    }

    /**
     * Fetch data from a GET endpoint.
     *
     * @param string $endpoint  e.g. "/open-apis/org/v1/employees/{id}"
     * @param array  $query     Optional query parameters
     */
    public function fetchGet(string $endpoint, array $query = []): array
    {
        $this->ensureAuthenticated();

        $url = "{$this->baseUrl}{$endpoint}";

        $response = Http::timeout(30)
            ->retry(3, 2000)
            ->withHeaders([
                'Authorization' => "Bearer {$this->tenantToken}",
            ])
            ->get($url, $query);

        if ($response->failed()) {
            Log::warning("ZKBio Zlink GET failed for {$endpoint}", [
                'status' => $response->status(),
            ]);
            return [];
        }

        $json = $response->json();
        if (($json['code'] ?? '') !== 'ZCOP0000') {
            Log::warning("ZKBio Zlink GET API error for {$endpoint}", [
                'code'    => $json['code'] ?? 'UNKNOWN',
                'message' => $json['message'] ?? '',
            ]);
            return [];
        }

        return $json['data'] ?? [];
    }

    // ─────────────────────────────────────────────────────────────────────────
    //  HIGH-LEVEL API METHODS
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Get all employees (paginated).
     * POST /open-apis/org/v1/employees/search
     */
    public function getEmployees(array $filters = []): array
    {
        return $this->fetchAllPaginated(
            '/open-apis/org/v1/employees/search',
            $filters,
            'employee'
        );
    }

    /**
     * Get a single employee's details.
     * GET /open-apis/org/v1/employees/{employeeId}
     */
    public function getEmployeeDetails(string $employeeId): array
    {
        return $this->fetchGet("/open-apis/org/v1/employees/{$employeeId}");
    }

    /**
     * Get all departments (paginated).
     * POST /open-apis/org/v1/departments/search
     */
    public function getDepartments(array $filters = []): array
    {
        return $this->fetchAllPaginated(
            '/open-apis/org/v1/departments/search',
            $filters,
            'items'
        );
    }

    /**
     * Ensure we have a valid tenant token before making API calls.
     */
    private function ensureAuthenticated(): void
    {
        if (empty($this->tenantToken)) {
            $this->authenticate();
        }
    }
}
