# Contributing

> Author: Grager D'souza <gragerdsouza@gmail.com>

## Workflow

1. Branch from `main`: `feat/…`, `fix/…`, `chore/…`.
2. **Write the test first**, then the code (see [docs/TESTING.md](docs/TESTING.md)).
3. `make ci` must pass locally: Pint, Rector, Larastan (max), and Pest at **100% coverage**.
4. Open a PR. CI runs the same gates plus PostgreSQL, PHP 8.6 (experimental), the asset build and the Docker build.
5. Merging to `main` deploys to Laravel Cloud automatically once CI is green.

## Conventions

- `declare(strict_types=1);` everywhere, enforced by architecture tests.
- PHPDoc on every class and method: a description, `@param Type $name Description`, `@return Type Description`
  and `@author Grager D'souza <gragerdsouza@gmail.com>` on each class or file.
- Controllers are final and thin. Put business logic in `App\Actions\*` (final readonly, one `handle()`).
- Models never depend on HTTP, the admin layer or actions.
- Conventional commits: `feat:`, `fix:`, `test:`, `refactor:`, `docs:`, `ci:`, `chore:`.
- No AI attribution trailers in commit messages.
