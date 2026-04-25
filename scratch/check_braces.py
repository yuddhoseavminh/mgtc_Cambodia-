
def check_braces(file_path):
    with open(file_path, 'r', encoding='utf-8') as f:
        content = f.read()
    
    import re
    style_blocks = re.findall(r'<style>(.*?)</style>', content, re.DOTALL)
    
    for i, block in enumerate(style_blocks):
        stack = []
        for line_num, line in enumerate(block.splitlines(), 1):
            for char in line:
                if char == '{':
                    stack.append(line_num)
                elif char == '}':
                    if not stack:
                        print(f"Extra closing brace in block {i} at relative line {line_num}")
                    else:
                        stack.pop()
        
        if stack:
            print(f"Unclosed braces in block {i} starting at relative lines: {stack}")

check_braces(r'd:\Project_Will To Reles\army_from_register\resources\views\staff\profile.blade.php')
