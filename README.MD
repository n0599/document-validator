Install dependencies
```
docker run --rm -v $(pwd):/app -w /app composer:2.2 composer install
```

Example script:
```
docker run --rm -v $(pwd):/app -w /app composer:2.2 php example/run.php
```

PHPStan:
```
docker run --rm -v $(pwd):/app -w /app composer:2.2 php vendor/bin/phpstan analyse --level 10 ./src
```

Tests:
```
docker run --rm -v $(pwd):/app -w /app composer:2.2 vendor/bin/phpunit
```

# Design Reasoning

## General approach
- The service is designed to be simple and compact, in a style that is common for small/medium Laravel or CakePHP projects.
- External code works only with the `DocumentValidator` facade. Everything internal – rules, configurations, repository – is hidden inside the service namespace.
- `DocumentValidatorInterface` exists for mocking the service in tests, not for having many implementations.
- Multi-tenancy is intentionally reduced to the minimum: I assume a shared DB with a `tenant_id` column in the tables; the service is called in the context of an authorized user, and `tenant_id` is passed explicitly.

## Rules
- Rule configuration is loaded from the DB, and a rule instance is created based on it. I considered two ways to pass parameters: the rule receives an array and checks the required keys by itself – or a constructor with typed arguments. I chose the second one – it is explicit, easy to read and easy to test. The unpacking of the array into constructor arguments is done by a callable during rule registration; a separate factory for every rule looked like too much for this task.
- I considered two ways to add new rules: a "hardcoded" `name => class` mapping inside the factory – or registration with a callable. I chose the second one: if a rule needs some specific dependency, we can get it from the DI container inside the callable. In a real project, this registration would live in a service provider. Also, `RuleFactory` stays very simple and easy to test. The downside: in a real project, the name used during registration would also be needed on the UI side, so there would be more details to think about.
- In `RuleInterface`, I thought about passing the data for validation not as `$content` and `$metadata` arguments, but as an immutable class: if new fields for validation appear, we would not need to change signatures in several classes. I stopped on the simpler variant – for the current set of fields it is enough.
- I considered a rule interface with two methods, `isValid()` + `getErrors()` – this is familiar to many people, but the dependency on the order of calls does not look good. I stopped on a single `validate()` method that returns errors.
- Tenant rule configuration comes from a repository, and we work with it through an interface – this is useful in tests. If there was a UI, the service facade would have methods for managing the settings, and they would save the configuration to the DB through the same repository.

## Validation result
- Rules return `ValidationError` directly – the same type that `ValidationResult` returns. I considered an intermediate per-rule result with mapping, but decided not to add extra layers.
- I assumed that a document without rules is valid; however, this is already a question of business logic.
- Localization of error messages is intentionally out of scope.
- The `Validation` prefix in `ValidationResult` and `ValidationError` – the names are intentionally unambiguous and "long", so that there are no collisions and aliases when importing them in other parts of the system.

## Exceptions
- All service exceptions implement `DocumentValidatorExceptionInterface` – this makes it easy to catch all of them at once. The name is long for the same reason as `ValidationResult`.
