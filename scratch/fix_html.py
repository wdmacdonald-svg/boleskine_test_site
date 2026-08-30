import re

with open('gallery.html', 'r', encoding='utf-8') as f:
    text = f.read()

def replace_empty_alt(match):
    tag = match.group(0)
    if 'data-alt=""' in tag:
        title_match = re.search(r'data-title="([^"]+)"', tag)
        if title_match:
            title = title_match.group(1)
            tag = tag.replace('data-alt=""', f'data-alt="{title}"')
    return tag

text = re.sub(r'<div class="gallery-item"[^>]+>', replace_empty_alt, text)

text = text.replace(
    'data-desc=\'Brian Yates pipes the players  to the tune "The Shinty Boys of Stratherrick", a composition by Ross Hutcheson  of Eudinuagain, Torness.\'', 
    'data-desc="Brian Yates pipes the players  to the tune &quot;The Shinty Boys of Stratherrick&quot;, a composition by Ross Hutcheson  of Eudinuagain, Torness."'
)

with open('gallery.html', 'w', encoding='utf-8') as f:
    f.write(text)
