---
name: card-box-skill
description: >-
  Provides best practices and techniques for building information cards (like news or fixture cards), specifically covering how to anchor buttons or links to the bottom of the card dynamically using Flexbox.
---

# Information Card Building

When building information cards that display dynamic data of varying lengths (e.g., team names, locations, descriptions) along with action buttons, it is common for the buttons to become misaligned across different cards in a grid.

To ensure action buttons are always pushed to the bottom of the card regardless of the content height above them:

## 1. Use Flexbox for the Card Content
Wrap the card's inner details (the container holding the content and the buttons) in a flex container that grows to fill the available height.

```css
.card-details-container {
    display: flex;
    flex-direction: column;
    justify-content: flex-start;
    height: 100%;
    flex-grow: 1;
}
```

## 2. Anchor the Action Buttons
Use `margin-top: auto` on the first button or the button wrapper. This will automatically consume all remaining vertical space above the button, pushing it to the very bottom edge of the container.

```css
.card-details-container .btn:first-of-type {
    margin-top: auto !important;
}
```

## 3. Separation from Overlapping Elements
If the card has an absolutely positioned badge (like a top label or title badge), ensure the flex container has sufficient `padding-top` (e.g., `3.5rem`) so that the dynamic text content does not overlap or sit uncomfortably close to the badge.

```css
.card-details-container {
    padding-top: 3.5rem;
}
```

## 4. Consistent Aspect Ratios and Grid Alignment
**Important Note:** When editing cards, ensure that all cards in the same section or row maintain the same aspect ratio or height.
**Reason:** If one card shrinks or grows significantly (for instance, by adding or removing extra lines of text such as 'Links available on every page footer'), it can break the visual alignment of the entire row unless the flex layout is properly configured to stretch all items identically.
