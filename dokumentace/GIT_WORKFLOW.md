# Git Workflow - Průvodce

## 🌳 Struktura branchů

```
main          → Produkce (vždy stabilní, co běží na webu)
develop       → Pre-produkce (testování před nasazením)
feature/*     → Nové features (např. feature/novy-design)
hotfix/*      → Kritické opravy (např. hotfix/oprava-bug)
```

## 📋 Základní workflow

### 1. Pracuješ na nové feature
```bash
# Vytvoříš novou branch z develop
git checkout develop
git pull origin develop
git checkout -b feature/nazev-feature

# Pracuješ, commituješ, pushuješ
git add .
git commit -m "WIP: začátek práce na feature"
git push origin feature/nazev-feature

# Když je hotovo → merguješ do develop
git checkout develop
git merge feature/nazev-feature
git push origin develop

# Smazání feature branch (volitelné)
git branch -d feature/nazev-feature
git push origin --delete feature/nazev-feature
```

### 2. Kritický hotfix (přímo do main)
```bash
# Vytvoříš hotfix z main
git checkout main
git pull origin main
git checkout -b hotfix/kriticka-oprava

# Opravíš bug, pushneš
git add .
git commit -m "Fix: oprava kritického bugu"
git push origin hotfix/kriticka-oprava

# Merguješ do main A develop
git checkout main
git merge hotfix/kriticka-oprava
git push origin main

git checkout develop
git merge hotfix/kriticka-oprava
git push origin develop

# Smazání hotfix branch
git branch -d hotfix/kriticka-oprava
git push origin --delete hotfix/kriticka-oprava
```

## 🔄 Pracuješ na více věcech současně

### Varianta A: Přepínání mezi branchy (doporučeno)
```bash
# Pracuješ na feature A
git checkout feature/design
# ... děláš změny ...
git add .
git commit -m "WIP: design"
git push origin feature/design

# Přepneš se na hotfix
git checkout hotfix/bug
# ... opravíš bug ...
git add .
git commit -m "Fix: bug"
git push origin hotfix/bug

# Vrátíš se na feature A
git checkout feature/design
# ... pokračuješ v práci ...
```

**Výhody:**
- ✅ Jednoduché
- ✅ Jeden adresář, jeden projekt
- ✅ Stačí pro většinu případů

**Nevýhody:**
- ❌ Můžeš mít otevřenou jen jednu branch najednou v IDE

### Varianta B: Worktree (když potřebuješ více otevřených současně)
```bash
# Hlavní projekt zůstane na develop
cd C:\Users\onvin\OneDrive\Dokumenty\WEB\maturita

# Vytvoříš worktree pro feature A
git worktree add ../maturita-feature-design feature/design

# Vytvoříš worktree pro hotfix
git worktree add ../maturita-hotfix-bug hotfix/bug

# Teď máš:
# - C:\Users\onvin\OneDrive\Dokumenty\WEB\maturita (develop)
# - C:\Users\onvin\OneDrive\Dokumenty\WEB\maturita-feature-design (feature/design)
# - C:\Users\onvin\OneDrive\Dokumenty\WEB\maturita-hotfix-bug (hotfix/bug)

# Můžeš mít všechny otevřené současně v Cursor!
```

**Výhody:**
- ✅ Můžeš mít více branchí otevřených současně
- ✅ Užitečné pro porovnávání kódu
- ✅ Každá branch má svůj vlastní adresář

**Nevýhody:**
- ❌ Více adresářů k udržování
- ❌ Složitější na začátku

**Smazání worktree:**
```bash
git worktree remove ../maturita-feature-design
```

## 🎯 Praktický příklad: 3 hotfixy + 2 features

### Scénář:
- `feature/novy-design` - rozpracováno
- `feature/komentare` - rozpracováno
- `hotfix/kriticka-oprava` - rozpracováno
- `hotfix/oprava-seo` - rozpracováno
- `hotfix/oprava-upload` - rozpracováno

### Řešení s přepínáním (switch):
```bash
# 1. Pracuješ na feature/novy-design
git checkout feature/novy-design
# ... děláš práci ...
git add . && git commit -m "WIP: design" && git push

# 2. Přepneš se na hotfix/kriticka-oprava (urgentní!)
git checkout hotfix/kriticka-oprava
# ... opravíš bug ...
git add . && git commit -m "Fix: kritická oprava" && git push
git checkout main && git merge hotfix/kriticka-oprava && git push origin main

# 3. Vrátíš se na feature/novy-design
git checkout feature/novy-design
# ... pokračuješ ...
```

### Řešení s worktree (pokud potřebuješ mít více otevřených):
```bash
# Vytvoříš worktree pro každou důležitou branch
git worktree add ../maturita-feature-design feature/novy-design
git worktree add ../maturita-feature-komentare feature/komentare
git worktree add ../maturita-hotfix-kriticka hotfix/kriticka-oprava

# Teď máš všechny otevřené současně v Cursor
# Můžeš mezi nimi přepínat, porovnávat kód, atd.
```

## 📝 Best practices

1. **Pravidelně pushuj** - i rozpracované věci (WIP commits)
   ```bash
   git commit -m "WIP: rozpracováno, ještě není hotovo"
   ```

2. **Pojmenovávání branchí**
   - `feature/nazev-feature` - nové features
   - `hotfix/kratky-popis` - opravy
   - `fix/nazev-opravy` - menší opravy (můžou jít do develop)

3. **Před mergem do develop/main**
   - Otestuj si to lokálně
   - Zkontroluj, že to funguje
   - Pushni a pak merguj

4. **Čistota**
   - Smazání hotových branchí (lokálně i na remote)
   - Pravidelné `git pull` na develop/main

## 🚨 Časté situace

### "Zapomněl jsem, na které branchi jsem"
```bash
git branch  # ukáže všechny lokální branchy
git status  # ukáže aktuální branch
```

### "Chci vidět, co je na remote"
```bash
git fetch origin
git branch -r  # remote branchy
git branch -a  # všechny branchy (lokální + remote)
```

### "Chci přepnout na jinou branch, ale mám necommitnuté změny"
```bash
# Uložíš změny do stash
git stash
git checkout jiná-branch

# Vrátíš změny zpět
git checkout původní-branch
git stash pop
```

### "Chci smazat branch"
```bash
# Lokálně
git branch -d název-branch  # bezpečné (kontroluje merge)
git branch -D název-branch  # násilné (i když není mergnutá)

# Na remote
git push origin --delete název-branch
```

## 🎓 Shrnutí

**Pro většinu práce:**
- ✅ Použij **switch** (přepínání mezi branchy)
- ✅ Pracuj na jedné věci najednou
- ✅ Pushuj pravidelně (i WIP)
- ✅ Když je hotovo → merge do develop/main

**Kdy použít worktree:**
- 🔧 Potřebuješ mít více branchí otevřených současně
- 🔧 Porovnáváš kód mezi branchy
- 🔧 Pracuješ na více věcech paralelně a potřebuješ je vidět najednou

**Obecně:**
- Začni jednoduše (switch)
- Worktree použij, až když to opravdu potřebuješ

