<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>TranslatezMoi - JSON Translation</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css">
    <style>
        /* Loading spinner */
        .spinner {
            border: 3px solid rgba(0, 0, 0, 0.1);
            border-radius: 50%;
            border-top: 3px solid #3498db;
            width: 24px;
            height: 24px;
            animation: spin 1s linear infinite;
            display: inline-block;
            vertical-align: middle;
            margin-right: 8px;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        /* JSON validation styling */
        .json-valid {
            border-color: #10B981 !important;
        }
        
        .json-invalid {
            border-color: #EF4444 !important;
        }
    </style>
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
                <div class="flex justify-between">
                    <label for="json_data" class="block text-sm font-medium text-gray-700">JSON Data (paste JSON here)</label>
                    <span id="json_status" class="text-sm"></span>
                </div>
                <textarea id="json_data" name="json_data" rows="4" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-opacity-50" placeholder='{"key": "value to translate"}' oninput="validateJson(this.value)"></textarea>
            </div>
            <div class="mb-4">
                <label for="file_upload" class="block text-sm font-medium text-gray-700">Upload JSON File</label>
                <input type="file" id="file_upload" accept=".json" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-opacity-50">
                <p class="mt-1 text-sm text-gray-500">File contents will be loaded into the JSON data field above</p>
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
                <textarea id="result" name="result" rows="4" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-opacity-50" placeholder="Translation will appear here..." readonly></textarea>
                <div class="mt-2 flex space-x-2">
                    <button type="button" id="copy_result" class="px-3 py-1 text-sm bg-gray-200 hover:bg-gray-300 rounded">
                        Copy to Clipboard
                    </button>
                    <button type="button" id="download_result" class="px-3 py-1 text-sm bg-gray-200 hover:bg-gray-300 rounded">
                        Download JSON
                    </button>
                </div>
            </div>
            <div id="error-message" class="mb-4 text-red-500 hidden"></div>
            <button type="submit" id="translate_btn" class="inline-flex items-center justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500" disabled>
                Translate
            </button>
            <div id="loading" class="hidden mt-4 flex items-center">
                <div class="spinner"></div>
                <span class="ml-2">Translating... This may take a while for large files</span>
            </div>
        </form>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // File upload handler
                const fileUpload = document.getElementById('file_upload');
                const jsonDataTextarea = document.getElementById('json_data');
                const errorDiv = document.getElementById('error-message');
                const translateBtn = document.getElementById('translate_btn');
                
                // Initial validation check
                validateJson(jsonDataTextarea.value);
                
                fileUpload.addEventListener('change', function(e) {
                    const file = e.target.files[0];
                    if (!file) return;
                    
                    const reader = new FileReader();
                    
                    reader.onload = function(event) {
                        try {
                            // Validate JSON by parsing it
                            const json = JSON.parse(event.target.result);
                            // Format it nicely and put in textarea
                            jsonDataTextarea.value = JSON.stringify(json, null, 2);
                            
                            // Validate and update UI
                            validateJson(jsonDataTextarea.value);
                            
                            // Hide any previous error
                            errorDiv.classList.add('hidden');
                            errorDiv.textContent = '';
                        } catch (err) {
                            errorDiv.classList.remove('hidden');
                            errorDiv.textContent = 'Invalid JSON file. Please check the file format.';
                            fileUpload.value = ''; // Clear the file input
                            
                            // Mark as invalid
                            jsonDataTextarea.classList.remove('json-valid');
                            jsonDataTextarea.classList.add('json-invalid');
                            document.getElementById('json_status').textContent = 'Invalid JSON';
                            document.getElementById('json_status').className = 'text-sm text-red-500';
                            translateBtn.disabled = true;
                        }
                    };
                    
                    reader.onerror = function() {
                        errorDiv.classList.remove('hidden');
                        errorDiv.textContent = 'Error reading file. Please try again.';
                        fileUpload.value = ''; // Clear the file input
                    };
                    
                    reader.readAsText(file);
                });
                
                // Copy to clipboard functionality
                const copyButton = document.getElementById('copy_result');
                const resultTextarea = document.getElementById('result');
                
                copyButton.addEventListener('click', function() {
                    resultTextarea.select();
                    document.execCommand('copy');
                    
                    // Visual feedback
                    copyButton.textContent = 'Copied!';
                    setTimeout(() => {
                        copyButton.textContent = 'Copy to Clipboard';
                    }, 2000);
                });
                
                // Download JSON functionality
                const downloadButton = document.getElementById('download_result');
                
                downloadButton.addEventListener('click', function() {
                    const result = resultTextarea.value;
                    if (!result) return;
                    
                    try {
                        // Create a Blob with the JSON data
                        const blob = new Blob([result], { type: 'application/json' });
                        const url = URL.createObjectURL(blob);
                        
                        // Create a temporary download link
                        const a = document.createElement('a');
                        a.href = url;
                        a.download = 'translated_data.json';
                        document.body.appendChild(a);
                        a.click();
                        
                        // Clean up
                        setTimeout(() => {
                            document.body.removeChild(a);
                            URL.revokeObjectURL(url);
                        }, 0);
                    } catch (err) {
                        errorDiv.classList.remove('hidden');
                        errorDiv.textContent = 'Error downloading file. Please try again.';
                    }
                });
            });
            
            // JSON validation function
            function validateJson(jsonString) {
                const jsonDataTextarea = document.getElementById('json_data');
                const jsonStatus = document.getElementById('json_status');
                const translateBtn = document.getElementById('translate_btn');
                
                // If empty, just reset
                if (!jsonString.trim()) {
                    jsonDataTextarea.classList.remove('json-valid', 'json-invalid');
                    jsonStatus.textContent = '';
                    translateBtn.disabled = true;
                    return false;
                }
                
                try {
                    JSON.parse(jsonString);
                    // Valid JSON
                    jsonDataTextarea.classList.remove('json-invalid');
                    jsonDataTextarea.classList.add('json-valid');
                    jsonStatus.textContent = 'Valid JSON';
                    jsonStatus.className = 'text-sm text-green-500';
                    translateBtn.disabled = false;
                    return true;
                } catch (e) {
                    // Invalid JSON
                    jsonDataTextarea.classList.remove('json-valid');
                    jsonDataTextarea.classList.add('json-invalid');
                    jsonStatus.textContent = 'Invalid JSON';
                    jsonStatus.className = 'text-sm text-red-500';
                    translateBtn.disabled = true;
                    return false;
                }
            }

            async function handleSubmit(event) {
                event.preventDefault();

                const jsonData = document.getElementById('json_data').value;
                const targetLang = document.getElementById('target_language').value;
                const errorDiv = document.getElementById('error-message');
                const resultArea = document.getElementById('result');
                const loadingIndicator = document.getElementById('loading');
                const translateBtn = document.getElementById('translate_btn');

                // Check JSON validity one more time
                if (!validateJson(jsonData)) {
                    errorDiv.classList.remove('hidden');
                    errorDiv.textContent = 'Please enter valid JSON data.';
                    return;
                }

                try {
                    // Show loading indicator
                    loadingIndicator.classList.remove('hidden');
                    translateBtn.disabled = true;
                    
                    // Use the web route with session authentication
                    const response = await fetch(`/translate/${targetLang}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                        },
                        body: JSON.stringify({ json_data: jsonData }),
                        credentials: 'same-origin'
                    });

                    if (response.status === 401) {
                        window.location.href = '/login';
                        return;
                    }

                    const data = await response.json();

                    if (!response.ok) {
                        throw new Error(data.message || data.error || 'Translation failed');
                    }

                    resultArea.value = JSON.stringify(data.data, null, 2);
                    errorDiv.classList.add('hidden');
                    errorDiv.textContent = '';
                } catch (error) {
                    errorDiv.classList.remove('hidden');
                    errorDiv.textContent = error.message;
                    resultArea.value = '';
                } finally {
                    // Hide loading indicator
                    loadingIndicator.classList.add('hidden');
                    translateBtn.disabled = false;
                }
            }
        </script>
    </div>
</body>

</html>