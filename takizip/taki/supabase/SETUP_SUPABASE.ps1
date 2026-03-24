# Supabase Setup Guide for Smart Learning Backend (Windows)

Write-Host "═══════════════════════════════════════════════════════════" -ForegroundColor Cyan
Write-Host "Supabase Database Setup for Smart Learning" -ForegroundColor Cyan
Write-Host "═══════════════════════════════════════════════════════════" -ForegroundColor Cyan
Write-Host ""

Write-Host "STEP 1: Load Schema into Supabase" -ForegroundColor Yellow
Write-Host "─────────────────────────────────────────────────────────" -ForegroundColor Yellow
Write-Host "1. Go to https://supabase.com/projects"
Write-Host "2. Select your project"
Write-Host "3. Go to SQL Editor (left sidebar)"
Write-Host "4. Click 'New Query'"
Write-Host "5. Copy-paste ALL content from: supabase/schema.sql"
Write-Host "6. Click 'Run'"
Write-Host ""

Write-Host "STEP 2: Load Seed Data (Optional)" -ForegroundColor Yellow
Write-Host "─────────────────────────────────────────────────────────" -ForegroundColor Yellow
Write-Host "1. In SQL Editor, click 'New Query' again"
Write-Host "2. Copy-paste ALL content from: supabase/seed.sql"
Write-Host "3. Click 'Run'"
Write-Host ""

Write-Host "STEP 3: Verify Connection" -ForegroundColor Yellow
Write-Host "─────────────────────────────────────────────────────────" -ForegroundColor Yellow
Write-Host "1. Open PowerShell and run:"
Write-Host "   cd takizip/taki"
Write-Host "   php -S localhost:8000"
Write-Host "2. Open browser: http://localhost:8000/pages/health.php"
Write-Host "3. You should see SUCCESS with database info"
Write-Host ""

Write-Host "STEP 4: Test the App" -ForegroundColor Yellow
Write-Host "─────────────────────────────────────────────────────────" -ForegroundColor Yellow
Write-Host "1. Go to: http://localhost:8000"
Write-Host "2. Create an account (register)"
Write-Host "3. Login with your credentials"
Write-Host "4. Check Supabase Table Editor - new user should appear in 'users' table"
Write-Host ""

Write-Host "═══════════════════════════════════════════════════════════" -ForegroundColor Cyan
Write-Host "✓ Environment variables are already set in: supabase/.env" -ForegroundColor Green
Write-Host "✓ Connection config: supabase/database.php" -ForegroundColor Green
Write-Host "═══════════════════════════════════════════════════════════" -ForegroundColor Cyan
