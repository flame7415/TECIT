close_tag = chr(60) + chr(47) + 'div' + chr(62)

with open('resources/views/dashboard/municipal.blade.php', 'r', encoding='utf-8') as f:
    content = f.read()

# Count how many closing tags we need to add
import re
open_count = len(re.findall(r'<div[^>]*>', content))
close_count = len(re.findall(r'</div>', content))
needed = open_count - close_count

# Insert needed closing divs before the first @endsection
marker = '@endsection'
pos = content.find(marker)
if pos != -1 and needed > 0:
    fix = (close_tag + '\n') * needed
    content = content[:pos] + fix + content[pos:]

with open('resources/views/dashboard/municipal.blade.php', 'w', encoding='utf-8') as f:
    f.write(content)

print('Added ' + str(needed) + ' closing tags')
