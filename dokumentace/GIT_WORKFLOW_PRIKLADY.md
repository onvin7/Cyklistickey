# Git Workflow - Praktické příklady

## ✅ Odpovědi na tvé otázky

### 1. "Pro každou feature/hotfix mám vytvořit novou branch?"
**ANO!** Každá feature/hotfix = nová branch s popisným názvem.

### 2. "Pojmenovat ji podle toho, co to je?"
**ANO!** Použij popisný název, co jasně říká, co děláš.

### 3. "Worktree funguje stejně jako switch?"
**NE!** To je důležitý rozdíl - vysvětlím níže.

---

## 📝 Příklad: Vytvoření branchí

### Scénář: Máš 3 úkoly

**Úkol 1:** Přidat nový design pro články
```bash
git checkout develop
git pull origin develop
git checkout -b feature/novy-design-clanku
# Pracuješ, pushuješ...
```

**Úkol 2:** Opravit kritický bug s uploadem
```bash
git checkout main
git pull origin main
git checkout -b hotfix/oprava-upload-souboru
# Opravíš, pushuješ, merguješ do main...
```

**Úkol 3:** Přidat systém komentářů
```bash
git checkout develop
git pull origin develop
git checkout -b feature/system-komentaru
# Pracuješ, pushuješ...
```

**Výsledek:**
```
main
develop
feature/novy-design-clanku      ← tvoje práce
hotfix/oprava-upload-souboru      ← tvoje práce
feature/system-komentaru          ← tvoje práce
```

---

## 🔄 Switch vs Worktree - ROZDÍL

### SWITCH (přepínání) - jeden adresář
```bash
# Jsi v: C:\Users\onvin\OneDrive\Dokumenty\WEB\maturita

# Přepneš se na feature/design
git checkout feature/novy-design-clanku
# → Stále jsi ve STEJNÉM adresáři
# → Jen se změnil obsah souborů (teď vidíš kód z feature/design)

# Přepneš se na hotfix
git checkout hotfix/oprava-upload-souboru
# → Stále jsi ve STEJNÉM adresáři
# → Teď vidíš kód z hotfix
```

**Jak to funguje:**
- ✅ Jeden adresář
- ✅ Přepínáš se mezi branchy
- ✅ Vždy vidíš jen jednu branch najednou
- ✅ Jednoduché a rychlé

**V Cursor:**
- Otevřeš jeden projekt
- Přepínáš branchy → soubory se změní
- Můžeš mít otevřenou jen jednu branch najednou

---

### WORKTREE - více adresářů
```bash
# Hlavní projekt (develop)
C:\Users\onvin\OneDrive\Dokumenty\WEB\maturita

# Vytvoříš worktree pro feature/design
git worktree add ../maturita-design feature/novy-design-clanku
# → Nový adresář: C:\Users\onvin\OneDrive\Dokumenty\WEB\maturita-design

# Vytvoříš worktree pro hotfix
git worktree add ../maturita-hotfix hotfix/oprava-upload-souboru
# → Nový adresář: C:\Users\onvin\OneDrive\Dokumenty\WEB\maturita-hotfix
```

**Jak to funguje:**
- ✅ Každá branch má SVŮJ VLASTNÍ adresář
- ✅ Můžeš mít všechny otevřené současně
- ✅ Každý adresář = jiná branch
- ✅ Můžeš porovnávat kód mezi adresáři

**V Cursor:**
- Otevřeš více projektů (každý = jiná branch)
- Můžeš mít otevřené všechny současně
- Můžeš mezi nimi přepínat v Cursor

---

## 🎯 Praktický příklad

### Situace: Máš rozpracované 3 věci

**1. feature/novy-design-clanku** - rozpracováno
**2. hotfix/oprava-upload** - rozpracováno  
**3. feature/system-komentaru** - rozpracováno

---

### Varianta A: SWITCH (doporučeno)

```bash
# Jsi v: C:\Users\onvin\OneDrive\Dokumenty\WEB\maturita

# Pracuješ na design
git checkout feature/novy-design-clanku
# ... děláš změny ...
git add . && git commit -m "WIP: design" && git push

# Přepneš se na hotfix (urgentní!)
git checkout hotfix/oprava-upload
# ... opravíš bug ...
git add . && git commit -m "Fix: upload" && git push

# Vrátíš se na design
git checkout feature/novy-design-clanku
# ... pokračuješ ...
```

**Výsledek:**
- Jeden adresář
- Přepínáš se mezi branchy
- Vždy vidíš jen jednu branch

---

### Varianta B: WORKTREE (když potřebuješ více současně)

```bash
# Hlavní projekt (zůstane na develop)
cd C:\Users\onvin\OneDrive\Dokumenty\WEB\maturita
git checkout develop

# Vytvoříš worktree pro design
git worktree add ../maturita-design feature/novy-design-clanku

# Vytvoříš worktree pro hotfix
git worktree add ../maturita-hotfix hotfix/oprava-upload

# Vytvoříš worktree pro komentáře
git worktree add ../maturita-komentare feature/system-komentaru
```

**Výsledek:**
```
C:\Users\onvin\OneDrive\Dokumenty\WEB\
  ├── maturita          (develop)
  ├── maturita-design   (feature/novy-design-clanku)
  ├── maturita-hotfix   (hotfix/oprava-upload)
  └── maturita-komentare (feature/system-komentaru)
```

**V Cursor:**
- Otevřeš všechny 4 projekty současně
- Můžeš mezi nimi přepínat
- Můžeš porovnávat kód

---

## 📊 Srovnání

| Vlastnost | SWITCH | WORKTREE |
|-----------|--------|----------|
| Počet adresářů | 1 | Více (každá branch = adresář) |
| Současně otevřené | 1 branch | Všechny branchy |
| Složitost | Jednoduché | Složitější |
| Kdy použít | Většina práce | Potřebuješ více současně |
| Rychlost | Rychlé | Pomalejší (více adresářů) |

---

## 💡 Doporučení

**Začni se SWITCH:**
- ✅ Jednoduché
- ✅ Rychlé
- ✅ Stačí pro 99% práce

**Worktree použij jen když:**
- 🔧 Potřebuješ mít více branchí otevřených současně
- 🔧 Porovnáváš kód mezi branchy
- 🔧 Pracuješ paralelně a potřebuješ je vidět najednou

---

## 🎓 Shrnutí

1. **Každá feature/hotfix = nová branch** ✅
2. **Pojmenuj ji popisně** ✅
3. **Worktree ≠ Switch** - worktree = více adresářů, switch = přepínání v jednom adresáři
4. **Začni se switch, worktree použij až když to potřebuješ** ✅

