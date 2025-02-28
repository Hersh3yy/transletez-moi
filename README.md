# Transletez-moi

An API that leverages OpenAI's API to translate a user provided json file of phrases to a target language of choice.

## Tech Stack

- PHP 8.3
- Laravel 12
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
OPENAI_ORGANIZATION-your-organization # optional
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
```

6. Start the application:
```bash
docker compose up -d

## API Endpoints

### Core Endpoints
- `GET /api/translate` - Translate a JSON object

- `GET /docs/api` - API Documentation UI
- `GET /telescope` - Development debugging dashboard

### Example Usage

Create a workout:
```bash
curl -X POST http://localhost:8000/api/translate \
  -H "Content-Type: application/json" \
  -d '{"workouts.excercise.benchpress": "Benchpress"}'
```


## Testing

Run the test suite:
```bash
docker compose exec api php artisan test
```

## OpenAI API Setup

1. Get your API key:
   - Visit [OpenAI's platform](https://platform.openai.com/api-keys)
   - Sign up or log in to your OpenAI account
   - Create a new API key
   - Copy the key (it will only be shown once)

2. Add to environment:
   ```bash
   # In src/.env
   OPENAI_API_KEY=your_api_key_here
   OPENAI_ORGANIZATION=org-... # Optional
   ```

3. Verify installation:
   ```bash
   # Create a workout to test OpenAI integration
   curl -X POST http://localhost:8000/api/workouts \
     -H "Content-Type: application/json" \
     -d '{"description": "3 sets bench press: 60kg 5 reps"}'
   ```

Note: The free tier of OpenAI API has rate limits and usage quotas. Monitor your usage on the OpenAI dashboard to avoid unexpected charges.


## API Documentation

Access the auto-generated API documentation:
- UI Documentation: http://localhost:8000/docs/api
- OpenAPI Spec: http://localhost:8000/docs/api.json

Development Tools: http://localhost:8000/telescope


## Example Response Format

```json
{
    "workout": {
        "id": 1,
        "raw_input": "Did 3 sets of bench press: 100kg for 5 reps, 110kg for 3, 120kg for 1",
        "parsed_data": {
            "exercises": [
                {
                    "name": "Bench Press",
                    "sets": [
                        {
                            "reps": 5,
                            "weight": 100
                        },
                        {
                            "reps": 3,
                            "weight": 110
                        },
                        {
                            "reps": 1,
                            "weight": 120
                        }
                    ]
                }
            ]
        },
        "performed_at": "2024-02-03T21:00:00.000000Z",
        "created_at": "2024-02-03T21:00:00.000000Z",
        "updated_at": "2024-02-03T21:00:00.000000Z"
    }
}
```
