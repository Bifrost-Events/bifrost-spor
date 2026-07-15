# Deploy (bifrost-spor)

Deployes via GitHub Actions (`deploy-release.yml`) til ProISP, samme mønster som `bifrost-admin-ui`.

## Før første deploy

1. Opprett ProISP-filområde med document root `.../bifrostspor/public/`.
   - ProISP tillater ikke bindestrek i mappenavn → `app_folder` = `bifrostspor/`.
2. Registrer deployment i **Deploy-Admin** (repo `Bifrost-Events/bifrost-spor`, environments `test` / `production`).
3. Kjør Deploy-Admin «Synk secrets til GitHub» slik at GitHub Environments får `FTP_*` og `APP_URL`.
4. Legg `.env` på serveren under `bifrostspor/.env` (ikke i git/FTP):

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://spor.example.no
```

5. Sørg for skrivbare mapper på serveren: `storage/data/`, `storage/logs/`, `storage/media/`.
   Runtime-JSON der skal ikke overskrives ved deploy (`remoteProtect` i `deploy-manifest.json`).

## Deploy

```bash
gh workflow run deploy-release.yml -R Bifrost-Events/bifrost-spor \
  -f environment=test -f release_id=<id> -f ref=<sha>
```

Smoke sjekker `GET /health` og `GET /version.json` mot `APP_URL` i valgt GitHub Environment.

## CI

Push/PR til `main` kjører PHPUnit via `.github/workflows/ci.yml`.
