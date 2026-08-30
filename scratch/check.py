import re
from collections import Counter
from html.parser import HTMLParser

class MyHTMLParser(HTMLParser):
    def __init__(self):
        super().__init__()
        self.tags = []
        self.errors = []
        self.void_elements = {'area', 'base', 'br', 'col', 'embed', 'hr', 'img', 'input', 'link', 'meta', 'param', 'source', 'track', 'wbr'}

    def handle_starttag(self, tag, attrs):
        if tag not in self.void_elements:
            self.tags.append((tag, self.getpos()))

    def handle_endtag(self, tag):
        if tag in self.void_elements:
            return
        if not self.tags:
            self.errors.append(f"Line {self.getpos()[0]}: Unexpected end tag </{tag}>")
            return
        last_tag, pos = self.tags.pop()
        if last_tag != tag:
            self.errors.append(f"Line {self.getpos()[0]}: Mismatched tag: expected </{last_tag}> (from line {pos[0]}), got </{tag}>")
            self.tags.append((last_tag, pos)) # push back

with open('gallery.html', 'r', encoding='utf-8') as f:
    text = f.read()

# Check IDs
ids = re.findall(r'id=\"([^\"]+)\"', text)
c = Counter(ids)
for k, v in c.items():
    if v > 1:
        print(f"Duplicate ID: {k} (count: {v})")

# Check Tags
parser = MyHTMLParser()
parser.feed(text)
for e in parser.errors:
    print(e)
if parser.tags:
    for tag, pos in parser.tags:
        print(f"Unclosed tag: <{tag}> from line {pos[0]}")
