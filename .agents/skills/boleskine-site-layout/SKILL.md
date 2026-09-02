---
name: boleskine-site-layout
description: >-
  Provides best practices, guidelines, and structural rules for building headers, footers, and page layouts across the Boleskine Camanachd Club website.
---

# Site Layout & Templating

When building or updating pages for the Boleskine Camanachd Club, adhere to the following layout and templating rules to maintain consistency across the entire site.

## 1. Template Injection
Do not hardcode the header or footer into individual HTML pages. Instead, use the global template injection method to avoid CORS issues when testing locally:

```html
<!-- Template Injection Placeholders -->
<div id="site-header"></div>

<!-- Page Content Goes Here -->

<!-- Template Injection Placeholders -->
<div id="site-footer"></div>
<script src="./index_files/templates.js"></script>
```

## 2. Footer Structure & Columns
The site footer must strictly follow a 3-column layout:
1. **Club Info (`footer-info`)**: Contains the club logo, club name, and the standard descriptive blurb.
2. **Quick Links**: Contains navigation links to key sections (Home, Heritage, Fixtures, Membership, Contact). The club's email address should also be positioned at the bottom of this column for prominence.
3. **Training & Match Ground**: Contains the physical address of Smith Park. Social media icons (Facebook and YouTube) should sit at the bottom of this column.

**CRITICAL RULE:** Do NOT include telephone numbers anywhere in the footer or header. All communication is routed strictly through social media and email.

## 3. Contact Details Positioning
- **Email Address**: Placed at the bottom of the right column (Training & Match Ground).
- **Social Media (Facebook/YouTube)**: Placed at the bottom of the left column (Club Info).

## 4. Active Navigation Links
The `templates.js` script handles automatic highlighting of the current active page in the navigation bar. Do not try to hardcode `class="active"` on navigation links inside individual HTML files.

## 5. CSS Styling Best Practices
Always avoid using in-line CSS (e.g., `style="..."`) within HTML tags. This prevents IDE warnings and maintains clean markup.
- **For lighter, smaller pages:** Place your CSS inside a `<style>` block within the `<head>` section of the document.
- **For larger, more demanding pages:** Always use an external CSS file (like `style.css`) and link it in the `<head>` section.
