# TASK: Build a Gallery Carousel Updater (Local Web App)

## The Project
I want to create a local helper app to manage and update a `gallery.html` file located in the root of my website folder. This tool will allow a non-technical editor to add, preview, and remove images and text within the gallery carousel.

Because standard web browsers cannot write to local files directly due to security restrictions, please build this tool using a **Single-Page HTML Utility with a local Python/Node script helper** OR a **clean HTML page that outputs/modifies the raw code block for copy-pasting**. Let me know which architecture is easiest for my local environment.

---

## 1. Directory & File Structure
The updater app will live in the root of the website alongside the files it interacts with:
*   `/` (Root folder where the updater app lives)
*   `/updater.py` (Python/Flask backend script)
*   `/updater.html` (Single-page dashboard UI)
*   `/gallery.html` (The target file containing the carousel)
*   `/gallery_files/` (Primary image repository — referenced by the carousel's `data-img` attributes)
*   `/assets/` (Secondary image repository — also browsable by the updater)
*   `/admin-gallery.html` (Legacy admin page — superseded by the new updater)

---

## 2. The User Flow
1. The user will manually copy new images into `/assets/` or `/gallery assets/`.
2. They open the updater app.
3. They browse and select one of these newly added images.
4. They fill in the metadata fields.
5. They preview the image and click "Add to Carousel".
6. The updater injects the new slide into `gallery.html` and saves the file (or provides the updated HTML file/code to overwrite).
7. The user can also view a list of current slides and delete any that are out of date.

---

## 3. UI/UX & Brand Layout Guidelines
*   **Design:** A clean, professional, responsive dashboard layout.
*   **Aesthetics:** Match the existing brand colors and accents found in our site (modern dark/light accents).
*   **Logo:** Place one small "running man" logo gracefully on the page (use a placeholder image tag like `logo.png` or an inline SVG vector).
*   **File Browser:** Since standard web files cannot list local directory files automatically, provide a clean custom input where the user can select/browse files, or type/paste the file path (e.g., `assets/photo1.jpg`).
*   **Live Preview:** Display a live image preview box showing the selected photo before they submit it.

---

## 4. Metadata Input Fields
The GUI must capture the following fields for each new slide:
*   **Title:** (Short name of the picture)
*   **Category:** (Dropdown menu containing exactly five choices: *Match Day, Training, Heritage, Community, Awards & Events*) which maps to `data-category=""`.
*   **Carousel Comment:** (A descriptive caption for the picture)
*   **Metadata Tag 1 (Date):** (When the photo was taken)
*   **Metadata Tag 2 (Place):** (Where the photo was taken)
*   **Alt Text:** (Optional field for screen-reader accessibility)

### Data-Attribute Mapping
The carousel JS reads from `<div id="item-pool">` inside `gallery.html`. Each slide is a self-closing `.gallery-item` div with `data-*` attributes. The backend script must write items in this exact structure:

| GUI Field | `data-*` Attribute | Example Value |
|---|---|---|
| Category (display name) | `data-category` | `heritage` |
| Title | `data-title` | `The Determined Spirit` |
| Carousel Comment | `data-desc` | `A Boleskine player concentrates on the ball.` |
| Image Path | `data-img` | `./gallery_files/Photo1.jpg` |
| Alt Text (or Title fallback) | `data-alt` | `Determined Boleskine shinty player with caman` |
| Metadata Tag 1 (Date) | `data-year` | `2018` |
| Metadata Tag 2 (Place) | `data-location` | `Smith Park` |

**Category key mapping** (dropdown value → `data-category` value):
| Display Name | `data-category` Key |
|---|---|
| Match Day | `match` |
| Training | `training` |
| Heritage | `heritage` |
| Community | `community` |
| Awards & Events | `awards` |

---

## 5. Carousel Integration Rules (The Missing Ending)
When a picture is saved to `gallery.html`:
*   **Write Target:** The backend script must only modify the contents of `<div id="item-pool">` (hidden data pool, currently lines 767–806 of `gallery.html`). No other section of the file is touched.
*   **Position:** Prepend the new slide to the **top/beginning** of the item pool so the newest pictures appear first (insert before the first existing `.gallery-item` child).
*   **Structure:** Generate a self-closing `.gallery-item` div with `data-*` attributes. The carousel JS reads these attributes at runtime to build the visible DOM. For example:
    ```html
    <div class="gallery-item" data-category="heritage" data-title="The Determined Spirit" data-desc="A Boleskine player concentrates on the ball." data-img="./gallery_files/Photo1.jpg" data-alt="Determined Boleskine shinty player with caman" data-year="2018" data-location="Smith Park"></div>
    ```
    **Note:** This element has no child HTML. The carousel's `buildSlides()` function in `gallery.html` reads the `data-*` attributes and constructs the visible slide markup dynamically.
*   **Removal Feature:** Provide an "Active Slides" dashboard panel showing a list of existing images in `gallery.html` with a trash/delete button next to each to easily strip out old slides.
*   **Deletion Key:** Match items by a compound key of `data-title` + `data-img` for uniqueness, since titles could theoretically repeat.
*   **Safety:** Back up `gallery.html` to `gallery.html.bak` before every write operation. Validate that `div#item-pool` exists before modifying.