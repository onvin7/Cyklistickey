# Git Branches Visualizer
# Pouziti: .\dokumentace\git-branches.ps1

Write-Host ""
Write-Host "=== GRAFICKE ZOBRAZENI BRANCHI ===" -ForegroundColor Cyan
Write-Host ""

# Aktualni branch
$currentBranch = git rev-parse --abbrev-ref HEAD
Write-Host "Aktualni branch: " -NoNewline -ForegroundColor Yellow
Write-Host "$currentBranch" -ForegroundColor Green
Write-Host ""

# Lokalni branchy
Write-Host "LOKALNI BRANCHY:" -ForegroundColor Cyan
$localBranches = git branch --format='%(refname:short)'
foreach ($branch in $localBranches) {
    if ($branch -eq $currentBranch) {
        Write-Host "  * $branch" -ForegroundColor Green
    } else {
        Write-Host "    $branch" -ForegroundColor Gray
    }
}

Write-Host ""
Write-Host "REMOTE BRANCHY:" -ForegroundColor Cyan
$remoteBranches = git branch -r --format='%(refname:short)'
foreach ($branch in $remoteBranches) {
    $branchName = $branch -replace 'origin/', ''
    Write-Host "    $branchName" -ForegroundColor DarkGray
}

# Graf commitu
Write-Host ""
Write-Host "GRAF COMMITU (poslednich 15):" -ForegroundColor Cyan
git log --graph --oneline --all --decorate -15

# Status branchi
Write-Host ""
Write-Host "STATUS BRANCHI:" -ForegroundColor Cyan
git branch -vv

Write-Host ""
