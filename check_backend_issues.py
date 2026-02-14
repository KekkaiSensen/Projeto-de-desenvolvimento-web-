
import os

files_to_check = [
    r'c:\Users\eumes\Documents\IFSP\Matérias\2025-2\Desenvolvimento Web 1\Projeto pratico\Trabalho Dev Web\Telas\Banco de dados\processa_pedido.php',
    r'c:\Users\eumes\Documents\IFSP\Matérias\2025-2\Desenvolvimento Web 1\Projeto pratico\Trabalho Dev Web\Telas\Banco de dados\conexao.php'
]

for file_path in files_to_check:
    try:
        with open(file_path, 'rb') as f:
            content = f.read(20)
            print(f"Checking {os.path.basename(file_path)}...")
            print(f"  First 20 bytes: {content}")
            
        if content.startswith(b'\xef\xbb\xbf'):
            print(f"  BOM detected in {os.path.basename(file_path)}!")
        else:
            print(f"  No BOM detected in {os.path.basename(file_path)}.")
            
    except Exception as e:
        print(f"Error checking {file_path}: {e}")

log_file = r'c:\Users\eumes\Documents\IFSP\Matérias\2025-2\Desenvolvimento Web 1\Projeto pratico\Trabalho Dev Web\Telas\Banco de dados\log_erros_pedido.txt'
if os.path.exists(log_file):
    print(f"\nContent of log_erros_pedido.txt:")
    with open(log_file, 'r', encoding='utf-8', errors='ignore') as f:
        print(f.read())
else:
    print(f"\nlog_erros_pedido.txt does not exist.")
