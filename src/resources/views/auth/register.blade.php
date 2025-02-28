@extends('layouts.auth')

@section('title', 'Register')

@section('content')
<div class="w-full max-w-md">
    <div class="bg-white p-8 rounded-lg shadow-md">
        <h1 class="text-2xl font-bold mb-6 text-center">Register for Translatez-moi</h1>

        <div id="error-message" class="hidden bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4"></div>

        <form method="POST" action="/register">
            @csrf
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="name">
                    Name
                </label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                    id="name" type="text" name="name" required>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="email">
                    Email
                </label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                    id="email" type="email" name="email" required>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="password">
                    Password
                </label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                    id="password" type="password" name="password" required>
            </div>
            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="password_confirmation">
                    Confirm Password
                </label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                    id="password_confirmation" type="password" name="password_confirmation" required>
            </div>
            <div class="flex items-center justify-between">
                <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" type="submit">
                    Register
                </button>
                <a class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800" href="/login">
                    Already have an account?
                </a>
            </div>
        </form>
    </div>
</div>

<script>
async function handleSubmit(event) {
    event.preventDefault();
    const form = event.target;
    const errorDiv = document.getElementById('error-message');
    
    try {
        const response = await fetch('/api/register', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
            },
            body: JSON.stringify({
                name: form.name.value,
                email: form.email.value,
                password: form.password.value,
                password_confirmation: form.password_confirmation.value
            })
        });

        const data = await response.json();

        if (response.ok) {
            // Store the token if needed
            if (data.authorisation && data.authorisation.token) {
                localStorage.setItem('token', data.authorisation.token);
            }
            // Redirect to home page
            window.location.href = '/';
        } else {
            // Handle validation errors
            if (data.errors) {
                const errorMessages = Object.values(data.errors).flat();
                errorDiv.innerHTML = errorMessages.join('<br>');
            } else {
                errorDiv.textContent = data.message || 'Registration failed';
            }
            errorDiv.classList.remove('hidden');
        }
    } catch (error) {
        console.error('Registration error:', error);
        errorDiv.textContent = 'An error occurred during registration';
        errorDiv.classList.remove('hidden');
    }
}
</script>
@endsection 