<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>TranslateIt - JSON Translation</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css">
    </head>
    <body class="font-sans antialiased bg-gray-100">
        <div class="container mx-auto p-6">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold">Translate JSON Data</h1>
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                        Logout
                    </button>
                </form>
            </div>
            <form id="translateForm" onsubmit="handleSubmit(event)">
                @csrf
                <div class="mb-4">
                    <label for="json_data" class="block text-sm font-medium text-gray-700">JSON Data (paste JSON here)</label>
                    <textarea id="json_data" name="json_data" rows="4" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-opacity-50" placeholder='{"key": "value to translate"}'></textarea>
                </div>
                <div class="mb-4">
                    <label for="file" class="block text-sm font-medium text-gray-700">Upload JSON File</label>
                    <input type="file" id="file" name="file" accept=".json" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-opacity-50">
                </div>
                <div class="mb-4">
                    <label for="target_language" class="block text-sm font-medium text-gray-700">Target Language</label>
                    <select id="target_language" name="target_language" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-opacity-50">
                        <option value="en">English</option>
                        <option value="es">Spanish</option>
                        <option value="fr">French</option>
                        <option value="de">German</option>
                        <option value="it">Italian</option>
                        <option value="nl">Dutch</option>
                        <option value="pt">Portuguese</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label for="result" class="block text-sm font-medium text-gray-700">Translation Result</label>
                    <textarea id="result" name="result" rows="4" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-opacity-50" placeholder="Translation will appear here..." disabled></textarea>
                </div>
                <div id="error-message" class="mb-4 text-red-500 hidden"></div>
                <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">Translate</button>
            </form>
            <script>
                async function handleSubmit(event) {
                    event.preventDefault();
                    
                    const form = event.target;
                    const formData = new FormData(form);
                    const jsonData = formData.get('json_data');

                    // Encode the JSON data if it's not empty
                    if (jsonData) {
                        try {
                            // Validate JSON format
                            JSON.parse(jsonData); // This will throw an error if the JSON is invalid
                        } catch (e) {
                            const errorDiv = document.getElementById('error-message');
                            errorDiv.textContent = 'Invalid JSON data provided.';
                            errorDiv.classList.remove('hidden');
                            return;
                        }
                    }

                    const targetLang = formData.get('target_language');
                    const errorDiv = document.getElementById('error-message');
                    const resultArea = document.getElementById('result');
                    
                    // Retrieve the token from local storage
                    const token = localStorage.getItem('jwt_token'); // Ensure this matches the token you set during login

                    if (!token) {
                        errorDiv.textContent = 'You are not logged in. Please log in first.';
                        errorDiv.classList.remove('hidden');
                        return;
                    }

                    try {
                        const response = await fetch(`/api/translate/${targetLang}`, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'Accept': 'application/json',
                                'Authorization': `Bearer ${token}`, // Send the token here
                                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value // Include CSRF token for web routes
                            }
                        });

                        if (response.status === 401) {
                            // Redirect to login if unauthorized
                            window.location.href = '/login';
                            return;
                        }

                        const data = await response.json();
                        
                        if (!response.ok) {
                            throw new Error(data.message || 'Translation failed');
                        }

                        resultArea.value = JSON.stringify(data, null, 2);
                        errorDiv.classList.add('hidden');
                        errorDiv.textContent = '';
                    } catch (error) {
                        errorDiv.classList.remove('hidden');
                        errorDiv.textContent = error.message;
                        resultArea.value = '';
                    }
                }
            </script>
        </div>
    </body>
</html>
