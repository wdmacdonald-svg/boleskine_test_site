# Deployment Checklist

Based on your project structure, here is the definitive guide on what needs to be uploaded to your `/test/` folder on the live server, and what should stay strictly on your local machine.

## 🟢 WHAT TO DEPLOY (Upload to Server)

These are the files that make your website look and function correctly on the internet.

### Folders
- `assets/` *(Contains all your images and media)*
- `css/` *(Extra stylesheets)*
- `gallery_files/` *(Assets specific to the gallery page)*
- `index_files/` *(Your core CSS, JS, and FontAwesome files)*
- `js/` *(Extra JavaScript files)*
- `webfonts/` *(Critical for FontAwesome icons to display)*

### Files
- `index.html` *(The main landing page)*
- `about.html` *(The new roadmap page)*
- `gallery.html` *(The gallery page)*
- `fetch-shinty-fixtures.php` *(The server-side PHP script that fetches live data)*
- `fixtures.js` *(The script that helps render the fixtures)*
- `fixtures.json` *(The data file storing the fixtures)*

*(Optional/Depending on your setup)*
- `test-team.php` *(Upload if you need to test the fixture fetching on the live server)*
- `admin-gallery.html` *(Upload only if you use this page to manage things while logged into the live server)*


---

## 🔴 WHAT TO IGNORE (Do NOT Upload)

These files are for local development, AI assistants, GitHub configuration, or local updater scripts. They do not belong on the live web server.

### Folders
- `.agents/` *(AI instructions and rules)*
- `.github/` *(GitHub Actions for CI/CD)*
- `scratch/` *(Temporary scratchpad files)*

### Files
- **Configuration & Git:**
  - `.gitignore`
  - `.hintrc` *(Linter rules)*
- **Local Scripts (Windows/Python):**
  - `fetch_fixtures.bat`
  - `fix_header.ps1`
  - `start_updater.bat`
  - `test-fixtures.bat`
  - `updater.py` *(Your local python updater program)*
- **Documentation (Markdown & Text):**
  - `boleskine_ux_session_log.md`
  - `Carousel.md`
  - `CRON_SETUP.md`
  - `Fixture_Help.txt`
  - `Fixtures.md`
  - `Updater.md`
- **Backups & Local UI:**
  - `gallery.html.bak`
  - `updater.html` *(Assuming this is the UI for your local python updater)*
  - `indexupdating.html`

---

> [!TIP]
> **Setting up Automated Deployments**
> If you ever want to set up a GitHub Action to deploy this automatically, we can use this exact list to write an `exclude` rule so GitHub knows to only push the green items to your FTP server!
