# Boleskine Camanachd Club — Carousel Gallery CSS Skill  
Version: 1.0  
Purpose: A reusable prompt + specification for generating a full carousel CSS file that matches the Boleskine Camanachd Club design system.

---

# 🎠 Carousel Gallery — Design Requirements

## Visual Identity
- Gold borders (`#F2910E`)
- Gold hover accents (`#C5720A`)
- Charcoal background (`#141416`)
- Soft shadows and cinematic feel
- Rounded corners (4px)
- Smooth cubic-bezier transitions
- Optional running-man icon overlay

## Behaviour
- Swipe/drag support (mobile)
- Arrow navigation
- Dot navigation (optional)
- Auto-play (optional)
- Infinite looping
- Smooth fade or slide transitions

## Structure (HTML Classes)
- `.carousel-container`
- `.carousel-track`
- `.carousel-slide`
- `.carousel-nav`
- `.carousel-arrow`
- `.carousel-dot`
- `.carousel-caption` (optional)
- `.image-frame-gold` (for consistency with grid gallery)

---

# 🎨 CSS Components Required

## 1. Container
- Centered
- Max-width: 1200px
- Overflow hidden
- Gold border or gold frame option

## 2. Track
- Flexbox row
- Smooth transform transitions
- Slide width = 100%

## 3. Slide
- Position relative
- Gold frame option
- Shadow + rounded corners
- Optional dark overlay for captions

## 4. Navigation Arrows
- Gold icons
- Hover glow
- Positioned at vertical center

## 5. Dots
- Gold circles
- Active state = filled gold
- Hover = gold-light

## 6. Transitions
- `cubic-bezier(0.4, 0.0, 0.2, 1)`
- Slide or fade mode

---

# 🏃 Running Man Rule

Before generating CSS, always ask:

> “Should the running man icon appear on each slide, only in the footer, or not at all?”

Never assume placement.

---

# 🧠 Master Prompt for Generating Carousel CSS

Use this prompt whenever you want the AI to generate the full CSS file:

```
Generate a complete Carousel Gallery CSS file using the Boleskine Camanachd Club design system.

Requirements:
- Gold borders (#F2910E)
- Gold hover accents (#C5720A)
- Charcoal backgrounds (#141416)
- Rounded corners (4px)
- Soft shadows
- Smooth cubic-bezier transitions
- Responsive behaviour
- Swipe/drag support
- Arrow navigation
- Optional dot navigation
- Optional auto-play
- No inline CSS
- Use semantic class names: carousel-container, carousel-track, carousel-slide, carousel-nav, carousel-arrow, carousel-dot, carousel-caption
- Match index.html design ethics
- Ask whether to include the running man icon

Output:
- A full CSS file ready to paste into style.css
- Modular sections with comments
- No JavaScript unless requested
```

---

# 🔧 Optional Add-On Prompts

## Add JavaScript for Carousel Behaviour
```
Generate JavaScript for the carousel gallery using the Boleskine design system. 
Include swipe support, arrow navigation, and infinite looping.
```

## Add Caption Overlay
```
Add a caption overlay to the carousel slides using gold accents and a dark gradient.
```

## Convert Grid Gallery to Carousel
```
Convert this grid gallery into a carousel gallery using the Boleskine design system.
```

---

# ✔️ End of File  
Save as: **carousel-gallery-css.md**
