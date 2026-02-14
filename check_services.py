
import os
import glob

# Define path to Services
services_path = r'c:\Users\eumes\Documents\IFSP\Matérias\2025-2\Desenvolvimento Web 1\Projeto pratico\Trabalho Dev Web\Telas\src\Services\*.php'
files = glob.glob(services_path)

print(f"Checking {len(files)} service files...")

for file_path in files:
    try:
        with open(file_path, 'rb') as f:
            content = f.read(50)
            
        filename = os.path.basename(file_path)
        
        if content.startswith(b'\xef\xbb\xbf'):
            print(f"[FAIL] {filename}: BOM detected!")
        else:
            # Check for leading whitespace/newlines textually
            text_content = content.decode('utf-8', errors='ignore')
            if not text_content.startswith('<?php'):
                print(f"[WARN] {filename}: Does NOT start immediately with <?php. Starts with: {repr(text_content[:10])}")
            else:
                print(f"[OK] {filename}: Clean.")
                
    except Exception as e:
        print(f"[ERR] {filename}: {e}")
