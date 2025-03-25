### Project tests and linter statuses:
[![Main](https://github.com/NONstop5/indigoLab-test/actions/workflows/main.yml/badge.svg)](https://github.com/NONstop5/indigoLab-test/actions)

## IndigoLab test project

### Requirements
- PHP >= 8.4
- Composer >= 2
- Make >= 4
- Docker

### Clone project
> `git clone git@github.com:NONstop5/indigoLab-test.git`

### Run Docker
> `cp ./docker/.env.dist ./docker/.env`
> 
> `make docker-build`
> 
> `make docker-up-d`
> 
> `make docker-down`

### Installation
> `cp .env.dist .env.local`
> 
> `make install`
> 
> `php bin/console doctrine:migrations:migrate`

### Run linters
> `make lint`
> 
> `make lint-fix`

### Run tests
> `make test`

### API requests
> Запрос кода подтверждения
> 
> POST
> 
> http://127.0.0.1:8000/api/user/request-code
> 
> ```json
> {
>    "phone_number": "+79157053551"
> }
> ```

> Проверка кода подтверждения
>
> POST
>
> http://127.0.0.1:8000/api/verify-code
>
> ```json
> {
>    "phone_number": "+79157053551",
>    "phone_code": "1234"
> }
> ```
