<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Financial Planning Route Test</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .test-box {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        h1 {
            color: #0066FF;
            border-bottom: 3px solid #0066FF;
            padding-bottom: 10px;
        }
        .test-link {
            display: block;
            padding: 15px 20px;
            margin: 10px 0;
            background: #0066FF;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background 0.3s;
        }
        .test-link:hover {
            background: #0052CC;
        }
        .status {
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
        }
        .success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .info {
            background: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }
        .warning {
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }
        code {
            background: #f4f4f4;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: monospace;
        }
    </style>
</head>
<body>
    <div class="test-box">
        <h1>🧪 Financial Planning Route Test</h1>
        
        <div class="status info">
            <strong>Current Status:</strong><br>
            @php
                $isAuth = auth()->check();
                $user = auth()->user();
            @endphp
            Authenticated (auth()): <code>{{ $isAuth ? 'YES' : 'NO' }}</code><br>
            Authenticated (Auth::): <code>{{ \Illuminate\Support\Facades\Auth::check() ? 'YES' : 'NO' }}</code><br>
            User: <code>{{ $user ? $user->email : 'Guest' }}</code><br>
            User ID: <code>{{ auth()->id() ?? 'N/A' }}</code><br>
            Session ID: <code>{{ session()->getId() }}</code><br>
            Session Data: <code>{{ json_encode(session()->all()) }}</code><br>
            Current URL: <code>{{ request()->fullUrl() }}</code><br>
            Route Name: <code>{{ request()->route() ? request()->route()->getName() : 'N/A' }}</code>
        </div>

        <h2>Test Links (Direct URLs - Opens in Same Tab)</h2>
        <a href="/financial-planning/dashboard" class="test-link" onclick="event.preventDefault(); window.location.href='/financial-planning/dashboard'; return false;">
            ✅ Test: Financial Dashboard (/financial-planning/dashboard)
        </a>
        
        <a href="/financial-planning/admin" class="test-link" onclick="event.preventDefault(); window.location.href='/financial-planning/admin'; return false;">
            ✅ Test: Financial Admin (/financial-planning/admin)
        </a>
        
        <a href="/financial-planning/calculators" class="test-link" onclick="event.preventDefault(); window.location.href='/financial-planning/calculators'; return false;">
            ✅ Test: Calculators Index (/financial-planning/calculators)
        </a>
        
        <h2>Test Links (Direct URLs - Opens in New Tab)</h2>
        <a href="/financial-planning/dashboard" class="test-link" target="_blank" rel="noopener">
            🔗 New Tab: Financial Dashboard
        </a>
        
        <a href="/financial-planning/admin" class="test-link" target="_blank" rel="noopener">
            🔗 New Tab: Financial Admin
        </a>
        
        <a href="/financial-planning/calculators" class="test-link" target="_blank" rel="noopener">
            🔗 New Tab: Calculators Index
        </a>

        <h2>Test Links (Route Helper - Opens in Same Tab)</h2>
        <a href="{{ route('financial.dashboard') }}" class="test-link" onclick="event.preventDefault(); window.location.href='{{ route('financial.dashboard') }}'; return false;">
            🔗 Route: financial.dashboard → {{ route('financial.dashboard') }}
        </a>
        
        <a href="{{ route('financial.admin.index') }}" class="test-link" onclick="event.preventDefault(); window.location.href='{{ route('financial.admin.index') }}'; return false;">
            🔗 Route: financial.admin.index → {{ route('financial.admin.index') }}
        </a>
        
        <a href="{{ route('financial.calculators.index') }}" class="test-link" onclick="event.preventDefault(); window.location.href='{{ route('financial.calculators.index') }}'; return false;">
            🔗 Route: financial.calculators.index → {{ route('financial.calculators.index') }}
        </a>
        
        <h2>Simple Test Route (No Livewire)</h2>
        <a href="/financial-planning/test-route" class="test-link" target="_blank" onclick="event.preventDefault(); fetch('/financial-planning/test-route').then(r => r.json()).then(d => alert(JSON.stringify(d, null, 2))); return false;">
            🧪 Test Simple Route (JSON Response) - Click to test
        </a>
        
        <h2>Manual URL Entry</h2>
        <div class="status info">
            <strong>Type these URLs directly in your browser address bar:</strong><br><br>
            <code>http://127.0.0.1:8000/financial-planning/test-route</code> (Simple test - should return JSON)<br>
            <code>http://127.0.0.1:8000/financial-planning/dashboard</code> (Dashboard)<br>
            <code>http://127.0.0.1:8000/financial-planning/admin</code> (Admin)<br>
            <code>http://127.0.0.1:8000/financial-planning/calculators</code> (Calculators)
        </div>

        <h2>Route Information</h2>
        <div class="status success">
            <strong>Expected Behavior:</strong><br>
            • Financial Dashboard: Should work if logged in (no subscription required)<br>
            • Financial Admin: Should work if logged in as super_admin<br>
            • Calculators: Should work for everyone (guests + logged in users)
        </div>

        <h2>Quick Actions</h2>
        @if(auth()->check())
            <div class="status success">
                ✅ You are logged in as: <strong>{{ auth()->user()->email }}</strong><br>
                @if(auth()->user()->hasRole('super_admin', 'api') || auth()->user()->hasRole('super_admin'))
                    ✅ You have super_admin role - admin panel should be accessible
                @else
                    ⚠️ You don't have super_admin role - admin panel won't be accessible
                @endif
            </div>
            <a href="/logout" class="test-link" style="background: #dc3545;">
                🚪 Logout
            </a>
        @else
            <div class="status warning">
                ⚠️ You are not logged in<br>
                Dashboard and Admin require login, but Calculators are public.
            </div>
            <a href="/login" class="test-link" style="background: #28a745;">
                🔐 Login
            </a>
        @endif

        <h2>Debug Information</h2>
        <div class="status info" style="font-size: 12px;">
            <strong>Route Details:</strong><br>
            Route Name: <code>{{ request()->route() ? request()->route()->getName() : 'N/A' }}</code><br>
            Route URI: <code>{{ request()->route() ? request()->route()->uri() : 'N/A' }}</code><br>
            Path: <code>{{ request()->path() }}</code><br>
            Method: <code>{{ request()->method() }}</code>
        </div>

        <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd; text-align: center; color: #666;">
            <small>Financial Planning Route Test Page</small>
        </div>
    </div>
</body>
</html>

