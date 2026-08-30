# Goal Description

Set up a GitHub Actions workflow (`deploy.yml`) that automatically uploads the correct files to your live server via FTP every time you push to the `master` branch. The script will strictly include only the necessary web files and exclude all development scripts (including the two optional files).

## User Review Required

> [!IMPORTANT]
> Because this script needs to log into your web host, it requires your FTP credentials. For security, **we will not write your password in the code**. 
> Instead, I will configure the script to securely read from GitHub Secrets. 
> 
> You will need to go to your repository on GitHub.com → **Settings** → **Secrets and variables** → **Actions**, and add three secrets:
> 1. `FTP_SERVER` (e.g., `ftp.yourdomain.com`)
> 2. `FTP_USERNAME` (Your FTP login)
> 3. `FTP_PASSWORD` (Your FTP password)
> 
> Are you comfortable setting up those secrets in GitHub after I write the script?

## Proposed Changes

### GitHub Actions

#### [NEW] [deploy.yml](file:///d:/Artificial%20Intelligence%20Folder/Home%20Page%20AI/Home_page%20-%20Copy/.github/workflows/deploy.yml)
- Create a new workflow file triggered by pushes to the `master` branch.
- Use a standard, reliable FTP deployment action (like `SamKirkland/FTP-Deploy-Action`).
- Configure the action to use GitHub Secrets for authentication.
- Set the target destination directory to `/test/` (or whatever your FTP root lands on).
- Provide a strict `exclude` list matching our checklist:
  - `**/.git/**`
  - `**/.github/**`
  - `**/.agents/**`
  - `**/scratch/**`
  - `**/*.bat`
  - `**/*.ps1`
  - `**/*.md`
  - `**/*.bak`
  - `updater.py`
  - `updater.html`
  - `indexupdating.html`
  - `test-team.php` *(Excluded as requested)*
  - `admin-gallery.html` *(Excluded as requested)*
  - `.hintrc`
  - `.gitignore`

## Verification Plan

### Manual Verification
1. I will write the `.yml` file and commit it.
2. The next time you push to GitHub (after setting your secrets), you can watch the "Actions" tab on GitHub.com to see it successfully sync the files to your server!
