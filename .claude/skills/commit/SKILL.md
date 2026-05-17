## Commit Workflow

### Before Committing
1. Run `git diff --staged` to review exactly what's staged
2. Ensure the commit corresponds to one logical unit of work (one story, one fix)
3. Never commit `.env`, `vendor/`, or `writable/`

### Commit Message Format

Use **Conventional Commits** with a Shortcut story reference:

```
<type>(<scope>): <short description> [sc-<story_id>]
```

- `type`: `feat` | `fix` | `refactor` | `chore` | `docs` | `test`
- `scope`: optional — e.g. `newsletters`, `checkout`, `auth`
- `[sc-<story_id>]`: Shortcut story ID — **mandatory if the commit closes or advances a story**

**Examples:**
```
feat(newsletters): add newsletter domain entities and repository interface [sc-605]
fix(checkout): read customer token from cookie before Bearer header [sc-587]
chore: run newsletter DB migrations
refactor(auth): remove stale customer token localStorage fallback [sc-612]
```

### Shortcut Integration

Including `sc-{id}` in the commit message **automatically links the commit to the Shortcut story** via the GitHub ↔ Shortcut integration. This is how progress is tracked without manual status updates.

- The **branch name** must also follow the Shortcut convention: `kennethsylar/sc-{id}/{story-slug}` (e.g. `kennethsylar/sc-604/nws-s1-create-newsletters-db-migration`)
- Both the branch name AND the commit message should reference the same `sc-{id}`

### After Committing
- Ask the user if they want to push
- Never force-push to `main` without explicit instruction
- If the story is done: remind the user to move it to **In Review** or **Done** in Shortcut
