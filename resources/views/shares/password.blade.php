<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Required - YWC</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full">
            <div class="bg-white rounded-lg shadow-lg p-8">
                <div class="text-center mb-6">
                    <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-lock text-blue-600 text-2xl"></i>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900">Password Required</h2>
                    <p class="mt-2 text-sm text-gray-600">
                        This document is password protected. Please enter the password to continue.
                    </p>
                </div>

                @if($errors->any())
                <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-md">
                    <p class="text-sm text-red-800">
                        <i class="fas fa-exclamation-circle mr-2"></i>{{ $errors->first() }}
                    </p>
                </div>
                @endif

                <form method="POST" action="{{ route('shared.verify-password', $token) }}">
                    @csrf

                    <div class="mb-6">
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                            Password
                        </label>
                        <input type="password" name="password" id="password" required autofocus
                               class="w-full px-4 py-3 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="Enter password">
                    </div>

                    <button type="submit"
                            class="w-full px-4 py-3 bg-blue-600 text-white font-medium rounded-md hover:bg-blue-700 transition-colors">
                        <i class="fas fa-unlock mr-2"></i>Unlock Document
                    </button>
                </form>

                <div class="mt-6 text-center">
                    <p class="text-xs text-gray-500">
                        <i class="fas fa-shield-alt mr-1"></i>
                        This link is secured and encrypted
                    </p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
