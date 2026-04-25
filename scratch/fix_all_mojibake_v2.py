import os
import re

# CP1252 mapping for characters 128-255
CP1252_MAP = {
    0x80: '\u20AC', 0x82: '\u201A', 0x83: '\u0192', 0x84: '\u201E', 0x85: '\u2026',
    0x86: '\u2020', 0x87: '\u2021', 0x88: '\u02C6', 0x89: '\u2030', 0x8A: '\u0160',
    0x8B: '\u2039', 0x8C: '\u0152', 0x8E: '\u017D', 0x91: '\u2018', 0x92: '\u2019',
    0x93: '\u201C', 0x94: '\u201D', 0x95: '\u2022', 0x96: '\u2013', 0x97: '\u2014',
    0x98: '\u02DC', 0x99: '\u2122', 0x9A: '\u0161', 0x9B: '\u203A', 0x9C: '\u0153',
    0x9E: '\u017E', 0x9F: '\u0178'
}
REV_CP1252 = {v: k for k, v in CP1252_MAP.items()}

def to_bytes(text):
    buf = bytearray()
    for char in text:
        if ord(char) < 128:
            buf.append(ord(char))
        elif char in REV_CP1252:
            buf.append(REV_CP1252[char])
        elif 128 <= ord(char) < 256:
            buf.append(ord(char))
        else:
            # Not CP1252, might be already UTF-8 character (like Khmer)
            # We return None to signal that this string shouldn't be processed this way
            return None
    return buf

def fix_string(text):
    b = to_bytes(text)
    if b is None: return text
    try:
        return b.decode('utf-8')
    except Exception:
        return text

def process_file(filepath):
    with open(filepath, 'r', encoding='utf-8', errors='ignore') as f:
        content = f.read()
    
    # regex for sequences of characters that are likely mojibake
    # They usually start with 'á' (E1) followed by 'ž' (9E) or 'Ÿ' (9F)
    # But let's just find anything that has these markers.
    
    def repl(match):
        orig = match.group(0)
        fixed = fix_string(orig)
        return fixed

    # Find sequences of CP1252-ish characters that look like Khmer UTF-8
    # Khmer UTF-8 bytes are mostly 0xE1 0x9E 0xXX and 0xE1 0x9F 0xXX
    # In CP1252: á ž XX and á Ÿ XX
    pattern = re.compile(r'([á][žŸ][\u0080-\u02DC\u2000-\u2122]+)')
    
    new_content = pattern.sub(repl, content)
    
    if new_content != content:
        with open(filepath, 'w', encoding='utf-8') as f:
            f.write(new_content)
        return True
    return False

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
        if process_file(full_path):
            print(f"Updated {f}")
        else:
            print(f"No changes for {f}")
