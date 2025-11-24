# 🌳 Přehled Branchí - Grafické Zobrazení

## 📋 Rychlé příkazy pro zobrazení

### Zobrazit všechny branchy (graficky)
```bash
git log --graph --oneline --all --decorate -20
```

### Zobrazit status všech branchí
```bash
git branch -vv
```

### Spustit vizualizaci (PowerShell skript)
```bash
.\dokumentace\git-branches.ps1
```

---

## 🎯 Aktuální struktura branchí

```
main          → Produkce (stabilní, co běží na webu)
develop       → Pre-produkce (testování před nasazením)
test          → Stará testovací branch (diverged - 21/21 commitů)
```

### Vizuální reprezentace:

```
                    main (produkce)
                     │
                     │
                    develop (pre-produkce)
                     │
                     │
                    test (stará, diverged)
```

---

## 📊 Detailní přehled

### Lokální branchy:
- ✅ **develop** - aktuální branch, synchronizovaná s origin/develop
- ✅ **main** - synchronizovaná s origin/main
- ⚠️ **test** - diverged (21 commitů ahead, 21 behind)

### Remote branchy:
- `origin/main` - produkční branch na serveru
- `origin/develop` - develop branch na serveru
- `origin/test` - test branch na serveru (diverged)

---

## 🔄 Workflow s novými branchy

Když vytvoříš nové feature/hotfix branchy, budou vypadat takto:

```
                    main
                     │
                     │
                    develop
                     │
         ┌───────────┼───────────┐
         │           │           │
    feature/    feature/    hotfix/
    design    komentare    bug-fix
```

---

## 🛠️ Užitečné aliasy

Můžeš si přidat do `.gitconfig` tyto aliasy:

```bash
git config --global alias.tree "log --graph --oneline --all --decorate"
git config --global alias.branches "branch -vv"
git config --global alias.visualize "!f() { git log --graph --oneline --all --decorate -20; }; f"
```

Pak můžeš použít:
- `git tree` - grafické zobrazení
- `git branches` - status branchí
- `git visualize` - kompletní přehled

---

## 📝 Jak číst graf

```
* commit-hash (branch-name) commit message
│
├─* další commit
│
└─* merge commit
```

- `*` = commit
- `│` = pokračování branchy
- `├─` = větvení
- `└─` = konec větve
- `(branch-name)` = kde se nachází branch

---

## 🎨 Barevné značení

V terminálu:
- 🟢 **Zelená** = aktuální branch
- ⚪ **Šedá** = ostatní branchy
- 🔴 **Červená** = diverged (rozešlé) branchy

---

## 💡 Tipy

1. **Pravidelně kontroluj status:**
   ```bash
   git branch -vv
   ```

2. **Podívej se na graf před mergem:**
   ```bash
   git log --graph --oneline --all --decorate -20
   ```

3. **Zkontroluj, co je na remote:**
   ```bash
   git fetch origin
   git branch -a
   ```

