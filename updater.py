"""
Gallery Carousel Updater — Boleskine Camanachd Club
Flask backend that reads/writes gallery.html's #item-pool data-* attributes.

Usage:
    pip install flask beautifulsoup4
    python updater.py

Then open http://localhost:5000 in your browser.
"""
import os
import shutil
import html
from pathlib import Path
from bs4 import BeautifulSoup
from flask import Flask, jsonify, request, send_file, send_from_directory

app = Flask(__name__)

# --- FIX: Securely Serve BOTH Assets Folders and Strip Leading Dots ---
@app.route('/assets/<path:filename>')
def serve_assets(filename):
    # If the browser requests "./assets/file.jpg", we strip any extra dot-segments
    clean_filename = filename.lstrip('./').replace('assets/', '')
    return send_from_directory('assets', clean_filename)

@app.route('/gallery_files/<path:filename>')
def serve_gallery_files(filename):
    # Handle the "gallery_files" directory cleanly
    clean_filename = filename.lstrip('./').replace('gallery_files/', '')
    return send_from_directory('gallery_files', clean_filename)

# Note: The folder is named "gallery_files" in your script's list, 
# but if you actually use "gallery assets" on disk, we have this fallback:
@app.route('/gallery assets/<path:filename>')
def serve_gallery_assets(filename):
    clean_filename = filename.lstrip('./').replace('gallery assets/', '')
    return send_from_directory('gallery assets', clean_filename)


BASE_DIR = Path(__file__).resolve().parent
GALLERY_FILE = BASE_DIR / "gallery.html"
BACKUP_FILE = BASE_DIR / "gallery.html.bak"
IMAGE_DIRS = [BASE_DIR / "gallery_files", BASE_DIR / "assets"]

VALID_CATEGORIES = {"match", "training", "heritage", "community", "awards"}

IMAGE_EXTENSIONS = {".jpg", ".jpeg", ".png", ".gif", ".webp", ".svg", ".jfif", ".bmp", ".ico"}


# ── File helpers ────────────────────────────────────

def read_gallery():
    return GALLERY_FILE.read_text(encoding="utf-8")


def write_gallery(html_content):
    if GALLERY_FILE.exists():
        shutil.copy2(GALLERY_FILE, BACKUP_FILE)
    GALLERY_FILE.write_text(html_content, encoding="utf-8")


def find_item_pool(soup):
    return soup.find("div", id="item-pool")


def parse_items_from_pool(pool_div):
    items = []
    if not pool_div:
        return items
    for child in pool_div.find_all("div", class_="gallery-item", recursive=False):
        items.append({
            "title": child.get("data-title", ""),
            "desc": child.get("data-desc", ""),
            "category": child.get("data-category", ""),
            "img": child.get("data-img", ""),
            "alt": child.get("data-alt", ""),
            "year": child.get("data-year", ""),
            "location": child.get("data-location", ""),
        })
    return items


def build_item_tag(data):
    attrs = [
        ("class", "gallery-item"),
        ("data-category", data.get("category", "")),
        ("data-title", data.get("title", "")),
        ("data-desc", data.get("desc", "")),
        ("data-img", data.get("img", "")),
        ("data-alt", data.get("alt", "")),
        ("data-year", data.get("year", "")),
        ("data-location", data.get("location", "")),
    ]
    parts = ["<div"]
    for key, val in attrs:
        parts.append(f' {key}="{html.escape(val, quote=True)}"')
    parts.append("></div>")
    return "".join(parts)


# ── Routes ──────────────────────────────────────────

@app.route("/")
def index():
    return send_file(BASE_DIR / "updater.html")


@app.route("/api/slides", methods=["GET"])
def get_slides():
    html_text = read_gallery()
    soup = BeautifulSoup(html_text, "html.parser")
    pool = find_item_pool(soup)
    if not pool:
        return jsonify({"error": "div#item-pool not found in gallery.html"}), 500
    items = parse_items_from_pool(pool)
    return jsonify(items)


@app.route("/api/slides/add", methods=["POST"])
def add_slide():
    data = request.get_json()
    if not data:
        return jsonify({"error": "No JSON payload provided"}), 400

    category = data.get("category", "")
    if category not in VALID_CATEGORIES:
        return jsonify({
            "error": f"Invalid category '{category}'. Must be one of: {', '.join(sorted(VALID_CATEGORIES))}"
        }), 400

    img = data.get("img", "")
    if img and not img.startswith("./"):
        return jsonify({"error": "Image path must be relative (start with ./)"}), 400

    html_text = read_gallery()
    soup = BeautifulSoup(html_text, "html.parser")
    pool = find_item_pool(soup)
    if not pool:
        return jsonify({"error": "div#item-pool not found in gallery.html"}), 500

    new_item_html = build_item_tag(data)
    new_item_soup = BeautifulSoup(new_item_html, "html.parser")
    new_tag = new_item_soup.find("div")

    first_item = pool.find("div", class_="gallery-item", recursive=False)
    if first_item:
        first_item.insert_before(new_tag)
    else:
        pool.append(new_tag)

    write_gallery(str(soup))
    return jsonify({"status": "ok", "message": f"Added '{data.get('title', 'Untitled')}' to gallery.html"})


@app.route("/api/slides/delete", methods=["POST"])
def delete_slide():
    data = request.get_json()
    if not data:
        return jsonify({"error": "No JSON payload provided"}), 400

    target_title = data.get("title", "")
    target_img = data.get("img", "")

    if not target_title and not target_img:
        return jsonify({"error": "Provide 'title' and/or 'img' to identify the slide to delete"}), 400

    html_text = read_gallery()
    soup = BeautifulSoup(html_text, "html.parser")
    pool = find_item_pool(soup)
    if not pool:
        return jsonify({"error": "div#item-pool not found in gallery.html"}), 500

    found = None
    for child in pool.find_all("div", class_="gallery-item", recursive=False):
        match_title = not target_title or child.get("data-title", "") == target_title
        match_img = not target_img or child.get("data-img", "") == target_img
        if match_title and match_img:
            found = child
            break

    if not found:
        return jsonify({
            "error": f"No slide found matching title='{target_title}' img='{target_img}'"
        }), 404

    removed_title = found.get("data-title", "Unknown")
    found.decompose()
    write_gallery(str(soup))
    return jsonify({"status": "ok", "message": f"Deleted '{removed_title}' from gallery.html"})


@app.route("/api/images", methods=["GET"])
def list_images():
    all_images = []
    for img_dir in IMAGE_DIRS:
        if not img_dir.is_dir():
            continue
        dir_label = img_dir.name
        for f in sorted(img_dir.iterdir()):
            if f.is_file() and f.suffix.lower() in IMAGE_EXTENSIONS:
                all_images.append({
                    "name": f.name,
                    "path": f"./{dir_label}/{f.name}",
                    "source": dir_label,
                })
    return jsonify(all_images)


if __name__ == "__main__":
    print(f"Gallery Updater running at http://localhost:5000")
    print(f"Target: {GALLERY_FILE}")
    print(f"Backup: {BACKUP_FILE}")
    app.run(host="127.0.0.1", port=5000, debug=True)
