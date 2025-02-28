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
            <h1 class="text-2xl font-bold mb-4">Translate JSON Data</h1>
            <form action="/api/translate" method="POST" enctype="multipart/form-data">
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
                <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">Translate</button>
            </form>
        </div>
    </body>
</html>
