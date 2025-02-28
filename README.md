# Translatez-moi
An API that leverages OpenAI's API to translate a user provided JSON object or JSON file to a target language of choice.

## Tech Stack
- PHP 8.4
- Laravel 11
- PostgreSQL
- Docker & Docker Compose
- OpenAI GPT
- PHPUnit for testing
- Scramble for API documentation

## Run Locally with Docker
1. Clone this repository.
2. Create environment file:
```bash
# Laravel directory
cp src/.env.example src/.env
```
3. Update src/.env with required settings:
```env
OPENAI_API_KEY=your_api_key_here
OPENAI_ORGANIZATION=your-organization # optional
```

4. Install dependencies and build:
```bash
docker compose run --rm api composer install
docker compose build
```

5. Setup application:
```bash
docker compose run --rm api php artisan key:generate
docker compose run --rm api php artisan jwt:secret
docker compose run --rm api php artisan migrate
```

6. Start the application:
```bash
docker compose up -d
```

### Run without docker
If the use of docker is not desired, change the DB_CONNECTION environment variable to sqlite to not depend on the PostgreSQL container for data storage.

```bash
php artisan serve
```

## API Endpoints

### Authentication
- `POST /api/login` - Get JWT token for API access
  - Request body: `{ "email": "user@example.com", "password": "password" }`
  - Response: `{ "status": "success", "user": {...}, "authorisation": { "token": "your_jwt_token", "type": "bearer" } }`

- `POST /api/register` - Register a new user account
  - Request body: `{ "name": "User Name", "email": "user@example.com", "password": "password", "password_confirmation": "password" }`

### Translation
- `POST /api/translate/{target_language}` - Translate a JSON object
  - Headers required: `Authorization: Bearer your_jwt_token`
  - Request body: `{ "json_data": "{\"key\":\"value to translate\"}" }`
  - Request can be sent as JSON or form data

### Documentation & Development
- `GET /docs/api` - API Documentation UI with detailed schema and examples
- `GET /telescope` - Development debugging dashboard (if enabled)

### Supported Languages
Currently, the following languages are supported:
- English (en)
- Spanish (es)
- French (fr)
- German (de)
- Italian (it)
- Dutch (nl)
- Portuguese (pt)

## Using the API

### Direct API Calls
To use the API directly, you must first authenticate to obtain a JWT token:

1. Register or login to get your token:
```bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"your@email.com","password":"your_password"}'
```

2. Use the token in subsequent API calls:
```bash
curl -X POST http://localhost:8000/api/translate/es \
  -H "Authorization: Bearer your_jwt_token" \
  -H "Content-Type: application/json" \
  -d '{"json_data":"{\"greeting\":\"Hello world\"}"}'
```

For more detailed API documentation, visit `/docs/api` in your browser after starting the application.

## Example Response Format

```json
{
  "data": {
    "greeting": "Hola mundo"
  }
}
```
### Web Interface
A simple web interface is also available by visiting the root URL (`/`) in your browser. You'll need to register or log in before using the translation interface.

The web interface provides:
- JSON validation
- File upload for JSON files
- Translation to any supported language
- Copy to clipboard and download functionality
- Loading indicator for large translation jobs

## Testing

Run the test suite:
```bash
docker compose exec api php artisan test
```

## Performance Notes
Translation of large JSON files (600+ strings) may take a significant amount of time. The application has been configured to handle longer processing times, but please be patient when translating large files.