# Deploy (bifrost-spor)

Deployes via GitHub Actions (`deploy-release.yml`) til ProISP, samme mønster som `bifrost-admin-ui`.

## Før første deploy

1. Opprett ProISP-filområde med document root `.../bifrostspor/public/`.
   - ProISP tillater ikke bindestrek i mappenavn → `app_folder` = `bifrostspor/`.
2. Registrer deployment i **Deploy-Admin** (repo `Bifrost-Events/bifrost-spor`).
3. Kjør Deploy-Admin «Synk secrets til GitHub» slik at GitHub Environment `spor_hjellum_net` får `FTP_*` og `APP_URL`.
4. ProISP document root: `.../r1466061/bifrostspor/public/`.
5. Legg `.env` på serveren under `bifrostspor/.env` (ikke i git/FTP):

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://spor.hjellum.net
```

5. Sørg for skrivbare mapper på serveren: `storage/data/`, `storage/logs/`, `storage/media/`.
   Runtime-JSON der skal ikke overskrives ved deploy (`remoteProtect` i `deploy-manifest.json`).
6. Ved oppstart fyller appen inn manglende løyper/poster fra `storage/examples/` (over skriver ikke eksisterende slug/id).
   Eksempelfiler følger med i deploy.

## Deploy

```bash
gh workflow run deploy-release.yml -R Bifrost-Events/bifrost-spor \
  -f environment=spor_hjellum_net -f release_id=<id> -f ref=<sha>
```

| Miljø | URL | GitHub Environment |
|-------|-----|--------------------|
| Prod | https://spor.hjellum.net | `spor_hjellum_net` |

Smoke sjekker `GET /health` og `GET /version.json` mot `APP_URL`.

## CI

Push/PR til `main` kjører PHPUnit via `.github/workflows/ci.yml`.
