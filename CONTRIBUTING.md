# Contributing

Thank you for considering contributing!

Before submitting a pull request, ensure all tests pass.

The project uses:

- **Pest** for unit and feature tests.
- **PHPStan** for static analysis.
- **Pint** for code styling.

All three are automatically run on every pull request.

You can run them locally (recommended) with:

```bash
vendor/bin/pest
vendor/bin/phpstan analyse
vendor/bin/pint
```