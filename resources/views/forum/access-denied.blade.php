@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 flex items-center justify-center px-4">
    <div class="max-w-md w-full bg-white rounded-lg shadow-lg p-8 text-center">
        <div class="mb-6">
            <svg class="mx-auto h-16 w-16 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
            </svg>
        </div>
        
        <h1 class="text-2xl font-bold text-gray-900 mb-4">Access Denied</h1>
        
        <p class="text-gray-600 mb-6">
            You don't have permission to access this content. This {{ $type ?? 'item' }} is restricted to specific roles.
        </p>
        
        @if(isset($requiredRoles) && !empty($requiredRoles))
            <div class="bg-gray-50 rounded-lg p-4 mb-6">
                <p class="text-sm font-medium text-gray-700 mb-2">Required Roles:</p>
                <div class="flex flex-wrap gap-2 justify-center">
                    @foreach($requiredRoles as $role)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            {{ $role }}
                        </span>
                    @endforeach
                </div>
            </div>
        @endif
        
        <div class="space-y-3">
            <a href="{{ route('forum.category.index') }}" 
               class="block w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition-colors">
                Return to Forums
            </a>
            
            <a href="{{ url()->previous() }}" 
               class="block w-full bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium py-2 px-4 rounded-lg transition-colors">
                Go Back
            </a>
        </div>
        
        <p class="text-sm text-gray-500 mt-6">
            If you believe you should have access, please contact an administrator.
        </p>
    </div>
</div>
@endsection
