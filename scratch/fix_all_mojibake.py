import os
import re

# Mapping from common mojibake sequences to Khmer characters
# We use the fact that it's just UTF-8 interpreted as CP1252
def fix_mojibake(text):
    if not any(c in text for c in "ážáŸ"):
        return text
    
    try:
        # Step 1: Encode as CP1252 to get the original UTF-8 bytes
        # Some characters might not map perfectly, but let's try.
        # Characters like '€' (0x80) and 'ž' (0x9E) are key in CP1252.
        b = text.encode('cp1252')
        return b.decode('utf-8')
    except (UnicodeEncodeError, UnicodeDecodeError):
        # Fallback: manual mapping for common sequences if encoding fails
        # This is a bit risky but can work for specific common words
        pass
    
    return text

def system_fix_mojibake(text):
    # Systematic approach:
    # Most Khmer chars start with 0xE1 0x9E or 0xE1 0x9F
    # 0xE1 -> á (225)
    # 0x9E -> ž (158)
    # 0x9F -> Ÿ (159)
    
    # We can try to find all characters > 127 and treat them as bytes
    try:
        # This covers characters in CP1252
        byte_arr = bytearray()
        for char in text:
            cp1252_map = {
                '\u017d': 0x8e, '\u017e': 0x9e, '\u0152': 0x8c, '\u0153': 0x9c,
                '\u0160': 0x8a, '\u0161': 0x9a, '\u0178': 0x9f, '\u0192': 0x83,
                '\u02c6': 0x88, '\u02dc': 0x98, '\u2013': 0x96, '\u2014': 0x97,
                '\u2018': 0x91, '\u2019': 0x92, '\u201a': 0x82, '\u201c': 0x93,
                '\u201d': 0x94, '\u201e': 0x84, '\u2020': 0x86, '\u2021': 0x87,
                '\u2022': 0x95, '\u2026': 0x85, '\u2030': 0x89, '\u2039': 0x8b,
                '\u203a': 0x9b, '\u2122': 0x99, '\u20ac': 0x80,
            }
            if ord(char) < 256 and ord(char) not in [129, 141, 143, 144, 157]:
                if char in cp1252_map:
                    byte_arr.append(cp1252_map[char])
                else:
                    byte_arr.append(ord(char))
            elif char in cp1252_map:
                byte_arr.append(cp1252_map[char])
            else:
                # If we hit a character that isn't in CP1252 or ASCII,
                # it might already be Khmer or something else.
                # We should probably stop or skip.
                return text
        
        return byte_arr.decode('utf-8')
    except Exception:
        return text

def process_file(filepath):
    with open(filepath, 'r', encoding='utf-8', errors='ignore') as f:
        content = f.read()
    
    # Regex to find sequences of extended characters that look like mojibake
    # Usually they contain 'áž' or 'áŸ'
    pattern = re.compile(r'([á][žŸ][\x80-\xFF\u0100-\u2122]+)')
    
    def repl(match):
        orig = match.group(1)
        fixed = system_fix_mojibake(orig)
        return fixed

    new_content = pattern.sub(repl, content)
    
    if new_content != content:
        with open(filepath, 'w', encoding='utf-8') as f:
            f.write(new_content)
        return True
    return False

# List of files specifically identified as having mojibake
files_to_fix = [
    r"resources\views\admin\team-staff\form.blade.php",
    r"resources\views\admin\sections\test-taking-staff-template.blade.php",
    r"resources\views\admin\sections\profile.blade.php",
    r"resources\views\admin\sections\course-template.blade.php",
    r"resources\views\admin\sections\applications.blade.php",
    r"resources\views\admin\application-show.blade.php",
    r"tests\Feature\RegistrationManagementTest.php"
]

base_path = r"d:\Project_Will To Reles\army_from_register"

for f in files_to_fix:
    full_path = os.path.join(base_path, f)
    if os.path.exists(full_path):
        try:
            if process_file(full_path):
                print(f"Updated {f}")
            else:
                print(f"No changes for {f}")
        except Exception as e:
            print(f"Failed to process {f}: {e}")
