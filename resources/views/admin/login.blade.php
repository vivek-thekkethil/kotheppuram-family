<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    @vite(['resources/css/app.css'])
</head>
<body class="min-h-screen bg-gray-100 flex items-center justify-center p-4">
    <div class="w-full max-w-md bg-white shadow rounded-lg p-6">
        <h1 class="text-2xl font-bold mb-1">Admin Login</h1>
        <p class="text-sm text-gray-600 mb-6">Sign in to access the admin dashboard.</p>

        @if ($errors->any())
            <div class="mb-4 rounded bg-red-100 text-red-700 px-4 py-2 text-sm">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('admin.login.submit') }}" class="space-y-4">
            @csrf
            <div>
                <label for="email" class="block text-sm font-medium mb-1">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required class="w-full border rounded-md px-3 py-2 focus:outline-none focus:ring focus:ring-indigo-200">
            </div>

            <div>
                <label for="password" class="block text-sm font-medium mb-1">Password</label>
                <input id="password" name="password" type="password" required class="w-full border rounded-md px-3 py-2 focus:outline-none focus:ring focus:ring-indigo-200">
            </div>

            <button type="submit" class="w-full bg-indigo-600 text-white py-2 rounded-md hover:bg-indigo-700">Login</button>
        </form>
    </div>
</body>
</html>
