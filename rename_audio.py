#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
Skript pro přejmenování audio souborů podle ID z SQL tabulky
Načte data z SQL souboru (id a nazev), najde odpovídající audio soubory
a přejmenuje je na formát {id}.mp3
"""

import os
import re
import sys
from pathlib import Path
from typing import Dict, List, Tuple
import unicodedata

# Nastavit UTF-8 encoding pro Windows
if sys.platform == 'win32':
    import io
    sys.stdout = io.TextIOWrapper(sys.stdout.buffer, encoding='utf-8', errors='replace')
    sys.stderr = io.TextIOWrapper(sys.stderr.buffer, encoding='utf-8', errors='replace')


def normalize_text(text: str) -> str:
    """
    Normalizuje text pro porovnání - odstraní diakritiku, převede na lowercase,
    odstraní speciální znaky a převede na prostý text
    """
    # Odstranit diakritiku
    text = unicodedata.normalize('NFD', text)
    text = text.encode('ascii', 'ignore').decode('ascii')
    
    # Převedení na lowercase
    text = text.lower()
    
    # Odstranit speciální znaky, ponechat jen alfanumerické a mezery
    text = re.sub(r'[^a-z0-9\s]', '', text)
    
    # Nahradit více mezer jednou
    text = re.sub(r'\s+', ' ', text)
    
    # Odstranit mezery na začátku a konci
    text = text.strip()
    
    return text


def parse_sql_file(sql_file_path: str) -> Dict[str, int]:
    """
    Parsuje SQL soubor a extrahuje mapování nazev -> id z INSERT příkazů
    Vrátí slovník: {normalized_nazev: id}
    """
    print(f"📖 Načítám SQL soubor: {sql_file_path}")
    
    if not os.path.exists(sql_file_path):
        print(f"❌ SQL soubor neexistuje: {sql_file_path}")
        return {}
    
    nazev_to_id = {}
    id_to_original_nazev = {}  # Pro zobrazení původních názvů
    
    try:
        with open(sql_file_path, 'r', encoding='utf-8') as f:
            content = f.read()
        
        # Najít všechny INSERT INTO `clanky` sekce
        insert_sections = re.findall(
            r'INSERT INTO `clanky`[^;]+',
            content,
            re.IGNORECASE | re.DOTALL
        )
        
        total_records = 0
        
        for section in insert_sections:
            # Najít všechny řádky s hodnotami - začínají (číslo,
            # Rozdělit sekci na řádky
            lines = section.split('\n')
            
            for line in lines:
                try:
                    line = line.strip()
                    # Přeskočit prázdné řádky a řádky, které nezačínají (
                    if not line or not line.startswith('('):
                        continue
                    
                    # Odstranit koncový čárku a závorku, pokud existuje
                    line = line.rstrip(',').rstrip(')')
                    
                    # Najít číslo na začátku (ID) - první hodnota
                    id_match = re.match(r'\((\d+)', line)
                    if not id_match:
                        continue
                    
                    article_id = int(id_match.group(1))
                    
                    # Najít druhou hodnotu - nazev (v uvozovkách)
                    # Použijeme jednodušší přístup - najít text mezi první a druhou čárkou po ID
                    # Ale musíme správně zpracovat escape sekvence v stringu
                    
                    # Najít pozici po ID a čárce
                    after_id = line[id_match.end():].lstrip()
                    if not after_id.startswith(','):
                        continue
                    
                    # Odstranit první čárku
                    after_comma = after_id[1:].lstrip()
                    
                    # Pokud nezačíná apostrofem, přeskočit
                    if not after_comma.startswith("'"):
                        continue
                    
                    # Parsovat SQL string - zpracovat escape sekvence a MySQL escape ('')
                    nazev = ""
                    i = 1  # Začínáme po prvním apostrofu
                    in_string = True
                    
                    while i < len(after_comma) and in_string:
                        char = after_comma[i]
                        
                        if char == "'":
                            # Kontrola, jestli je to escape ('' = apostrof v MySQL)
                            if i + 1 < len(after_comma) and after_comma[i + 1] == "'":
                                # MySQL escape: '' = '
                                nazev += "'"
                                i += 2
                            elif i > 0 and after_comma[i - 1] == '\\':
                                # Escape: \'
                                nazev += "'"
                                i += 1
                            else:
                                # Konec stringu
                                break
                        elif char == '\\' and i + 1 < len(after_comma):
                            # Escape sekvence
                            next_char = after_comma[i + 1]
                            if next_char == "'":
                                nazev += "'"
                            elif next_char == "\\":
                                nazev += "\\"
                            else:
                                nazev += char + next_char
                            i += 2
                        else:
                            nazev += char
                            i += 1
                    
                    if nazev:
                        # Uložit originální názvu
                        id_to_original_nazev[article_id] = nazev
                        
                        # Normalizovat názvu pro matching
                        normalized_nazev = normalize_text(nazev)
                        
                        if normalized_nazev:
                            # Pokud už existuje normalizovaný název s jiným ID, použít první
                            if normalized_nazev not in nazev_to_id:
                                nazev_to_id[normalized_nazev] = article_id
                                total_records += 1
                except Exception as e:
                    # Přeskočit tento řádek a pokračovat dál
                    continue
        
        print(f"✓ Načteno {total_records} záznamů z SQL souboru")
        
        # Zobrazit prvních 5 příkladů
        print("\n📋 Příklady načtených záznamů:")
        count = 0
        for norm_nazev, article_id in list(nazev_to_id.items())[:5]:
            original = id_to_original_nazev.get(article_id, '')
            print(f"   ID {article_id}: {original[:50]}...")
            count += 1
        
        # Uložit id_to_original_nazev jako globální pro pozdější použití
        parse_sql_file._id_to_original_nazev = id_to_original_nazev
        
        return nazev_to_id
        
    except Exception as e:
        print(f"❌ Chyba při parsování SQL souboru: {e}")
        import traceback
        traceback.print_exc()
        return {}


def find_audio_files(audio_dir: str) -> List[str]:
    """
    Najde všechny audio soubory ve složce
    """
    try:
        audio_dir_path = Path(audio_dir)
        
        if not audio_dir_path.exists():
            print(f"⚠️ Složka neexistuje: {audio_dir}")
            print(f"💡 Vytvářím složku...")
            try:
                audio_dir_path.mkdir(parents=True, exist_ok=True)
                print(f"✓ Složka vytvořena")
            except Exception as e:
                print(f"❌ Nepodařilo se vytvořit složku: {e}")
                return []
        
        # Najít všechny audio soubory
        audio_extensions = ['.mp3', '.wav', '.m4a', '.MP3', '.WAV', '.M4A']
        audio_files = []
        
        for ext in audio_extensions:
            try:
                audio_files.extend(audio_dir_path.glob(f'*{ext}'))
            except Exception:
                # Přeskočit tuto příponu a pokračovat
                continue
        
        # Konvertovat na stringy a filtrovat pouze existující soubory
        audio_files = [str(f) for f in audio_files if f.exists()]
        
        return sorted(audio_files)
    except Exception as e:
        print(f"❌ Chyba při hledání audio souborů: {e}")
        return []


def match_audio_file(filename: str, nazev_map: Dict[str, int], debug: bool = False) -> Tuple[int, str]:
    """
    Najde odpovídající ID pro audio soubor podle názvu
    Vrátí (id, matching_method) nebo (None, None) pokud nenajde
    """
    try:
        # Získat název bez přípony
        base_name = Path(filename).stem
        
        # Normalizovat název souboru
        normalized_filename = normalize_text(base_name)
        
        if not normalized_filename:
            if debug:
                print(f"      ⚠️ Normalizovaný název je prázdný")
            return None, None
    except Exception as e:
        # Chyba při zpracování názvu souboru
        if debug:
            print(f"      ⚠️ Chyba při normalizaci: {e}")
        return None, None
    
    # 1. Přesná shoda normalizovaného názvu
    if normalized_filename in nazev_map:
        return nazev_map[normalized_filename], "přesná shoda"
    
    # 2. Částečná shoda - zkusit najít nejdelší společnou část
    best_match = None
    best_score = 0
    best_method = ""
    
    # Rozdělit na slova pro lepší porovnání
    filename_words = set(normalized_filename.split())
    
    for norm_nazev, article_id in nazev_map.items():
        nazev_words = set(norm_nazev.split())
        
        # Zkusit najít, jestli je filename obsažen v nazev nebo naopak
        if normalized_filename in norm_nazev:
            # Filename je obsažen v nazev - dobrá shoda
            common_words = filename_words & nazev_words
            score = len(common_words)
            if score > best_score:
                best_score = score
                best_match = article_id
                best_method = f"název souboru je obsažen v názvu článku (skóre: {score})"
        
        if norm_nazev in normalized_filename:
            # Nazev je obsažen v filename - dobrá shoda
            common_words = filename_words & nazev_words
            score = len(common_words)
            if score > best_score:
                best_score = score
                best_match = article_id
                best_method = f"název článku je obsažen v názvu souboru (skóre: {score})"
        
        # Spočítat podobnost podle počtu společných slov
        common_words = filename_words & nazev_words
        score = len(common_words)
        
        # Pokud máme alespoň 3 společná slova a je to lepší skóre
        if score >= 3 and score > best_score:
            # Kontrola, jestli je to významná část (alespoň 50% slov z kratšího názvu)
            min_length = min(len(filename_words), len(nazev_words))
            if score >= min_length * 0.5:  # Alespoň 50% shoda
                best_score = score
                best_match = article_id
                best_method = f"částečná shoda podle slov (skóre: {score})"
    
    if best_match and best_score >= 3:
        return best_match, best_method
    
    return None, None


def rename_audio_file(old_path: str, new_name: str, audio_dir: str) -> bool:
    """
    Přejmenuje audio soubor na nový název
    """
    try:
        old_file = Path(old_path)
        new_file = Path(audio_dir) / new_name
        
        # Kontrola, zda existuje původní soubor
        if not old_file.exists():
            print(f"   ⚠️ Původní soubor neexistuje: {old_path}")
            return False
        
        # Pokud už existuje soubor s cílovým názvem
        if new_file.exists():
            try:
                if new_file.samefile(old_file):
                    # Je to stejný soubor
                    return True
                else:
                    # Různé soubory - odstranit starý
                    print(f"   ⚠️ Soubor {new_name} již existuje (jiný soubor)")
                    new_file.unlink()
                    print(f"   🗑️  Odstraněn existující soubor")
            except Exception as e:
                # Chyba při kontrole/odstraňování - pokusit se přejmenovat
                print(f"   ⚠️ Chyba při kontrole existujícího souboru: {e}")
        
        old_file.rename(new_file)
        return True
    except Exception as e:
        print(f"   ❌ Chyba při přejmenování: {e}")
        return False


def main():
    """
    Hlavní funkce skriptu
    """
    print("=" * 70)
    print("🎵 PŘEJMENOVÁNÍ AUDIO SOUBORŮ PODLE ID Z SQL TABULKY")
    print("=" * 70)
    print()
    
    # Cesty
    workspace_root = Path(__file__).parent.absolute()
    sql_file = r"C:\Users\onvin\Downloads\clanky.sql"
    audio_dir = workspace_root / "web" / "uploads" / "audio"
    
    # Limit pro testování (None = všechny, jinak číslo)
    TEST_LIMIT = 50  # Prvních 50 souborů pro testování
    
    print(f"📁 Workspace: {workspace_root}")
    print(f"📄 SQL soubor: {sql_file}")
    print(f"📁 Audio složka: {audio_dir}")
    if TEST_LIMIT:
        print(f"⚠️  TESTOVACÍ REŽIM: Zpracuji pouze prvních {TEST_LIMIT} souborů")
    print()
    
    # 1. Načíst data z SQL souboru
    nazev_map = parse_sql_file(sql_file)
    
    if not nazev_map:
        print("❌ Nepodařilo se načíst data z SQL souboru. Ukončuji.")
        sys.exit(1)
    
    print()
    
    # 2. Najít audio soubory
    print(f"🔍 Hledám audio soubory v: {audio_dir}")
    audio_files = find_audio_files(str(audio_dir))
    
    if not audio_files:
        print("⚠️ Žádné audio soubory nenalezeny ve složce.")
        print(f"💡 Zkontroluj, zda jsou audio soubory ve složce: {audio_dir}")
        sys.exit(0)
    
    print(f"✓ Nalezeno {len(audio_files)} audio souborů")
    
    # Omezit na testovací limit, pokud je nastaven
    original_count = len(audio_files)
    if TEST_LIMIT and TEST_LIMIT > 0:
        audio_files = audio_files[:TEST_LIMIT]
        print(f"⚠️  Pro testování zpracuji pouze prvních {len(audio_files)} z {original_count} souborů")
    
    # Zobrazit prvních 10 souborů
    print("\n📋 Prvních 10 souborů:")
    for i, audio_file in enumerate(audio_files[:10], 1):
        filename = Path(audio_file).name
        size = os.path.getsize(audio_file)
        print(f"   {i}. {filename} ({size:,} bajtů)")
    
    if len(audio_files) > 10:
        print(f"   ... a dalších {len(audio_files) - 10} souborů")
    
    print()
    print("🚀 Začínám přejmenovávání...")
    print()
    
    # 3. Zpracovat každý soubor
    renamed_count = 0
    skipped_count = 0
    error_count = 0
    already_correct = 0
    skipped_files = []  # Pro zobrazení přeskočených souborů
    
    # Získat mapování ID -> originální název pro zobrazení
    id_to_original = getattr(parse_sql_file, '_id_to_original_nazev', {})
    
    for i, audio_file in enumerate(audio_files, 1):
        try:
            filename = Path(audio_file).name
            
            # Oddělovač
            if i > 1:
                print("-" * 70)
            
            print(f"\n[{i}/{len(audio_files)}] 📄 Zpracovávám: {filename}")
            
            # Debug: Zobrazit normalizovaný název souboru
            base_name = Path(filename).stem
            normalized_file = normalize_text(base_name)
            print(f"   🔍 Normalizovaný název souboru: '{normalized_file}'")
            
            # Najít odpovídající ID
            article_id, match_method = match_audio_file(filename, nazev_map)
            
            if not article_id:
                skipped_count += 1
                skipped_files.append(filename)
                print(f"   ❌ Nepodařilo se najít odpovídající záznam v SQL tabulce")
                
                # Debug: Zobrazit podobné názvy z SQL pro porovnání
                print(f"   🔍 Hledám podobné názvy v SQL...")
                filename_words = set(normalized_file.split())
                similar_found = []
                
                # Projít všechny názvy z SQL a najít podobné
                for norm_nazev, sql_id in nazev_map.items():
                    nazev_words = set(norm_nazev.split())
                    common = filename_words & nazev_words
                    if len(common) > 0:
                        # Spočítat procentuální shodu
                        min_words = min(len(filename_words), len(nazev_words))
                        percentage = (len(common) / min_words * 100) if min_words > 0 else 0
                        similar_found.append((sql_id, norm_nazev, len(common), percentage))
                
                if similar_found:
                    # Seřadit podle skóre (nejvíce společných slov)
                    similar_found.sort(key=lambda x: (x[2], x[3]), reverse=True)
                    print(f"   📋 Nalezeno {len(similar_found)} podobných názvů (zobrazuji top 10):")
                    for sql_id, sql_nazev, common_count, percentage in similar_found[:10]:
                        # Zobrazit originální název z SQL
                        original_nazev = id_to_original.get(sql_id, sql_nazev)
                        display_nazev = original_nazev[:70] if len(original_nazev) > 70 else original_nazev
                        print(f"      ID {sql_id}: '{display_nazev}'")
                        print(f"         Společná slova: {common_count}, Shoda: {percentage:.1f}%")
                else:
                    print(f"      ❌ Žádné podobné názvy nenalezeny (ani jedno společné slovo)")
                    # Zobrazit prvních 3 názvy z SQL pro referenci
                    print(f"      📋 Příklady názvů z SQL:")
                    for sql_id, norm_nazev in list(nazev_map.items())[:3]:
                        original_nazev = id_to_original.get(sql_id, norm_nazev)
                        display_nazev = original_nazev[:60] if len(original_nazev) > 60 else original_nazev
                        print(f"         ID {sql_id}: '{display_nazev}...'")
                        print(f"         Normalizováno: '{norm_nazev[:60]}...'")
                
                print(f"   ⏭️  Přeskočeno")
                continue
            
            # Zobrazit originální název z SQL (pokud je k dispozici)
            original_nazev = id_to_original.get(article_id, '')
            if original_nazev:
                print(f"   📝 Název v SQL: {original_nazev[:60]}{'...' if len(original_nazev) > 60 else ''}")
            
            print(f"   ✓ Nalezeno: ID {article_id} ({match_method})")
            
            # Nový název: {id}.mp3
            new_filename = f"{article_id}.mp3"
            
            # Pokud už má správný název
            if filename == new_filename:
                already_correct += 1
                print(f"   ✓ Soubor už má správný název: {new_filename}")
                continue
            
            # Přejmenovat
            print(f"   🔄 Přejmenovávám: {filename} → {new_filename}")
            
            try:
                if rename_audio_file(audio_file, new_filename, str(audio_dir)):
                    renamed_count += 1
                    print(f"   ✅ Úspěšně přejmenováno")
                else:
                    error_count += 1
                    print(f"   ❌ Chyba při přejmenování")
            except Exception as e:
                error_count += 1
                print(f"   ❌ Chyba při přejmenování: {e}")
                print(f"   ⏭️  Přeskočeno a pokračuji dál")
                
        except Exception as e:
            # Neočekávaná chyba při zpracování tohoto souboru
            error_count += 1
            print(f"\n   ❌ Neočekávaná chyba při zpracování {filename}: {e}")
            print(f"   ⏭️  Přeskočeno a pokračuji dál")
            continue
    
    # 4. Statistiky
    print()
    print("=" * 70)
    print("✅ DOKONČENO")
    print("=" * 70)
    print(f"Celkem souborů: {len(audio_files)}")
    print(f"✅ Přejmenováno: {renamed_count}")
    print(f"✓ Už správně pojmenováno: {already_correct}")
    print(f"⏭️  Přeskočeno: {skipped_count} (nenalezen odpovídající záznam)")
    print(f"❌ Chyb: {error_count}")
    
    if skipped_count > 0:
        print()
        print("💡 Tip: Pro přeskočené soubory zkontroluj:")
        print("   - Zda název souboru odpovídá názvu článku v SQL tabulce")
        print("   - Zda název není příliš odlišný (zkus normalizovat názvy)")
        
        print()
        print("📋 Přeskočené soubory:")
        for skipped_file in skipped_files[:10]:
            print(f"   - {skipped_file}")
        if len(skipped_files) > 10:
            print(f"   ... a dalších {len(skipped_files) - 10} souborů")
    
    # Zobrazit finální stav
    print()
    print("📁 Finální stav složky:")
    final_files = find_audio_files(str(audio_dir))
    if final_files:
        # Zobrazit soubory seřazené podle názvu
        sorted_files = sorted(final_files, key=lambda x: Path(x).name)
        for i, audio_file in enumerate(sorted_files[:10], 1):
            print(f"   {i}. {Path(audio_file).name}")
        if len(sorted_files) > 10:
            print(f"   ... a dalších {len(sorted_files) - 10} souborů")
    else:
        print("   (složka je prázdná)")


if __name__ == "__main__":
    try:
        main()
    except KeyboardInterrupt:
        print("\n\n⚠️ Přerušeno uživatelem")
        sys.exit(1)
    except Exception as e:
        print(f"\n\n❌ Neočekávaná chyba: {e}")
        import traceback
        traceback.print_exc()
        sys.exit(1)

