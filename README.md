# Bifrost Spor

Selvstendig MVP for digitale løyper (routes) og poster (stops) med stabile QR-tokens.

## Hva MVP-en inneholder

- Registrerte brukere oppretter og administrerer egne løyper (eierskap via `owner_id`)
- Data lagres i JSON-filer bak repository-interfaces
- Hver post får et stabilt, unikt QR-token
- Deltakerregistrering og innlogging for konkurranse
- Flervalgsspørsmål på hver post med poengtelling
- Resultattavle per løype
- Utskriftsklare postskilt (A4, QR-kode, nettleserutskrift)
- Offentlig URL `/q/{token}` videresender til kanonisk post-URL
- Mobiltilpasset offentlig visning

## Kom i gang lokalt (XAMPP / PHP 8.2)

```bash
cd C:\xampp\htdocs\bifrost\bifrost-spor
composer install
copy .env.example .env
```

Åpne i nettleser:

```
http://localhost/bifrost/bifrost-spor/public/
```

## Innlogging og eierskap

Det finnes **ett** brukersystem for både konkurranse og redigering:

1. **Registrer deg** på `/registrer`
2. **Logg inn** på `/logg-inn`
3. **Mine løyper** på `/admin/routes` — viser kun løyper du eier

Nye løyper knyttes automatisk til innlogget bruker (`owner_id`). Kun eieren kan redigere løypa og legge til poster.

Bjørgan natursti i demo-data eies av første registrerte deltaker.

### Postskilt

Fra **Mine løyper** → løypedetalj:

- **Skriv ut post** — forhåndsvisning og utskrift av ett skilt
- **Skriv ut alle postskilt** — alle poster; velg **1, 2 eller 4 skilt per A4-side** i forhåndsvisningen

URL-er:

```
/admin/stops/{stopId}/print
/admin/routes/{routeId}/print
/admin/routes/{routeId}/print?per_page=2
/admin/routes/{routeId}/print?per_page=4
```

QR-koden peker til `/q/{token}` og viser posten **direkte uten innlogging**. Uinnloggede ser innholdet med en beskjed om at innlogging registrerer besøket. Forrige/neste skjules ved QR-besøk.

**Viktig:** Sett `APP_URL` i `.env` til en adresse telefonen din kan nå (f.eks. `http://spor.bifrost.local/public`), ikke `localhost`, ellers fungerer ikke QR-kodene fra mobil.

## JSON-data

Runtime-data lagres i:

- `storage/data/routes.json`
- `storage/data/stops.json`
- `storage/data/participants.json`
- `storage/data/answers.json`

Disse filene er **ikke** versjonert i git (se `.gitignore`).

Eksempelfiler med `schema_version` ligger i `storage/examples/` og deployes med appen. Ved oppstart kopieres/suppleres manglende løyper og poster derfra inn i `storage/data/` (eksisterende slug/id overskrives ikke).

```bash
# Regenerer examples fra seed-script (valgfritt)
php scripts/seed-themed-routes.php
```

### Format

```json
{
  "schema_version": 1,
  "items": []
}
```

Hvert element i `items` speiler framtidige databasetabeller med stabile tekst-ID-er (`route_...`, `stop_...`, `participant_...`).

Løyper har `owner_id` som peker på en deltaker.

## Tester

```bash
composer test
```

Tester bruker midlertidige filer og skriver aldri til `storage/data/`.

## CI/CD

Repo: [Bifrost-Events/bifrost-spor](https://github.com/Bifrost-Events/bifrost-spor)

| Workflow | Trigger | Formål |
|----------|---------|--------|
| `ci.yml` | push/PR til `main` | PHPUnit |
| `deploy-release.yml` | manuell (`workflow_dispatch`) | FTP-deploy + HTTP smoke |

Se [DEPLOY.md](DEPLOY.md) for ProISP/Deploy-Admin-oppsett.

## Bytte til database senere

Applikasjonen avhenger av:

- `App\Contracts\Repositories\RouteRepository`
- `App\Contracts\Repositories\StopRepository`

Implementer f.eks. `PdoRouteRepository` og `PdoStopRepository`, og bytt binding i `App\Support\Container`. Controllere og domene-tjenester forblir uendret.

## Bevisst utsatt

- Bifrost Admin / Events-integrasjon
- Database og migreringer
- Bildeopplasting
- QR-bildegenerering
- Full autentisering og roller (organisasjoner)
- Organisasjoner, betaling, flerspråk, PWA

Den gamle separate admin-innloggingen via `.env` (`ADMIN_USERNAME` / `ADMIN_PASSWORD_HASH`) er fjernet.
