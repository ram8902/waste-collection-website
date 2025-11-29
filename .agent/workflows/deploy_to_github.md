---
description: How to deploy the project to GitHub
---

# Deploy to GitHub

Since `git` is not currently installed or recognized on your system, follow these steps to deploy your project.

## 1. Install Git
1.  Download Git for Windows from [git-scm.com](https://git-scm.com/download/win).
2.  Run the installer. Use the default settings, but make sure "Git from the command line and also from 3rd-party software" is selected.
3.  After installation, restart your terminal or VS Code to recognize the `git` command.

## 2. Create a GitHub Repository
1.  Log in to [GitHub](https://github.com).
2.  Click the "+" icon in the top right and select "New repository".
3.  Name your repository (e.g., `waste-collection-app`).
4.  Do **not** initialize with README, .gitignore, or License (we will do this locally).
5.  Click "Create repository".

## 3. Initialize Git Locally
Open your terminal in the project folder (`c:\xampp\htdocs\backup_mini_project`) and run:

```powershell
git init
```

## 4. Create .gitignore
Create a file named `.gitignore` to exclude unnecessary files:

```powershell
# Create .gitignore
echo "node_modules/" > .gitignore
echo ".env" >> .gitignore
echo ".DS_Store" >> .gitignore
echo "config.php" >> .gitignore
```

> **Note:** We are excluding `config.php` because it contains database credentials. You should create a `config.sample.php` with placeholder values for others to use.

## 5. Stage and Commit Files

```powershell
git add .
git commit -m "Initial commit"
```

## 6. Connect to GitHub
Replace `YOUR_USERNAME` with your actual GitHub username:

```powershell
git branch -M main
git remote add origin https://github.com/YOUR_USERNAME/waste-collection-app.git
git push -u origin main
```

## 7. Verify
Refresh your GitHub repository page to see your code.
