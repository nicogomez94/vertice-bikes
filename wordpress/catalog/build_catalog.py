#!/usr/bin/env python3
"""Extrae el catálogo comercial Fardella desde el PDF aprobado."""

from __future__ import annotations

import argparse
import json
import re
import shutil
import subprocess
import xml.etree.ElementTree as ET
from pathlib import Path


PRICE_RE = re.compile(r"^\d{1,3}(?:\.\d{3})*,\d{2}$")
SKU_RE = re.compile(r"^[A-ZÁÉÍÓÚÜÑ0-9][A-ZÁÉÍÓÚÜÑ0-9._/-]{2,}$")
NS = {"x": "http://www.w3.org/1999/xhtml"}
MISSING_IMAGES = {"BICBATTLE2927GR", "BICBATTLE2927N", "BICBATTLE2927V"}


def block_text(block: ET.Element) -> str:
    lines = []
    for line in block.findall("x:line", NS):
        words = [word.text or "" for word in line.findall("x:word", NS)]
        lines.append(" ".join(words))
    return " ".join(lines).strip()


def decimal_price(raw: str) -> str:
    return raw.replace(".", "").replace(",", ".")


def slugify(value: str) -> str:
    value = value.lower()
    replacements = str.maketrans("áéíóúüñ", "aeiouun")
    value = value.translate(replacements)
    value = re.sub(r"[^a-z0-9]+", "-", value).strip("-")
    return value


def parse_catalog(xml_path: Path) -> list[dict]:
    root = ET.parse(xml_path).getroot()
    products: list[dict] = []

    for page_number, page in enumerate(root.findall(".//x:page", NS), start=1):
        if page_number < 3 or page_number > 45:
            continue

        blocks = []
        for block in page.findall(".//x:block", NS):
            text = block_text(block)
            if not text:
                continue
            blocks.append(
                {
                    "text": text,
                    "x": float(block.attrib["xMin"]),
                    "y": float(block.attrib["yMin"]),
                }
            )

        header = [
            item["text"]
            for item in blocks
            if item["x"] < 300 and 36 <= item["y"] <= 55
        ]
        if not header:
            continue
        category = " ".join(header).strip().title()

        price_blocks = []
        for item in blocks:
            tokens = item["text"].split()
            matches = [token for token in tokens if PRICE_RE.fullmatch(token)]
            if "$" in tokens and matches:
                price_blocks.append((item, matches[0]))

        for price_block, raw_price in price_blocks:
            column = 0 if price_block["x"] < 297 else 1
            low_x, high_x = (40, 295) if column == 0 else (295, 555)
            candidates = [
                item
                for item in blocks
                if low_x <= item["x"] < high_x
                and price_block["y"] - 135 <= item["y"] < price_block["y"]
                and len(item["text"].split()) == 1
                and SKU_RE.fullmatch(item["text"])
                and item["text"] not in {"IVA", "ART"}
            ]
            if not candidates:
                raise RuntimeError(
                    f"No se encontró SKU en página {page_number}, precio {raw_price}"
                )
            sku_block = max(candidates, key=lambda item: item["y"])
            sku = sku_block["text"]

            name_blocks = [
                item
                for item in blocks
                if low_x <= item["x"] < high_x
                and sku_block["y"] + 8 <= item["y"] < price_block["y"] - 8
                and "$" not in item["text"]
                and "IVA" not in item["text"]
            ]
            name = " ".join(item["text"] for item in sorted(name_blocks, key=lambda item: item["y"]))
            name = re.sub(r"\s+", " ", name).strip()
            if not name:
                raise RuntimeError(f"Nombre vacío para {sku} en página {page_number}")

            products.append(
                {
                    "sku": sku,
                    "name": name,
                    "slug": slugify(f"{name}-{sku}"),
                    "category": category,
                    "category_slug": slugify(category),
                    "wholesale": decimal_price(raw_price),
                    "price_label": raw_price,
                    "page": page_number,
                    "column": column,
                    "sku_y": sku_block["y"],
                    "image": "imagen-no-disponible.jpg" if sku in MISSING_IMAGES else f"{slugify(sku)}.jpg",
                }
            )

    return products


def render_and_crop(pdf: Path, products: list[dict], work_dir: Path, image_dir: Path) -> None:
    work_dir.mkdir(parents=True, exist_ok=True)
    image_dir.mkdir(parents=True, exist_ok=True)
    subprocess.run(
        [
            "pdftoppm",
            "-f",
            "3",
            "-l",
            "45",
            "-png",
            "-r",
            "180",
            str(pdf),
            str(work_dir / "page"),
        ],
        check=True,
    )

    scale = 180 / 72
    crop_width = round(222 * scale)
    crop_height = round(176 * scale)
    for product in products:
        if product["sku"] in MISSING_IMAGES:
            continue
        source = work_dir / f"page-{product['page']:02d}.png"
        if not source.exists():
            source = work_dir / f"page-{product['page']}.png"
        x_points = 59 if product["column"] == 0 else 317
        y_points = max(70, product["sku_y"] - 189)
        crop = f"crop={crop_width}:{crop_height}:{round(x_points * scale)}:{round(y_points * scale)}"
        destination = image_dir / product["image"]
        subprocess.run(
            [
                "ffmpeg",
                "-loglevel",
                "error",
                "-y",
                "-i",
                str(source),
                "-vf",
                crop,
                "-q:v",
                "2",
                str(destination),
            ],
            check=True,
        )


def main() -> None:
    parser = argparse.ArgumentParser()
    parser.add_argument("pdf", type=Path)
    parser.add_argument("--output", type=Path, required=True)
    parser.add_argument("--images", type=Path, required=True)
    parser.add_argument("--work-dir", type=Path, required=True)
    args = parser.parse_args()

    args.work_dir.mkdir(parents=True, exist_ok=True)
    xml_path = args.work_dir / "catalog.xml"
    subprocess.run(
        ["pdftotext", "-bbox-layout", str(args.pdf), str(xml_path)],
        check=True,
    )
    products = parse_catalog(xml_path)
    if len(products) != 137:
        raise RuntimeError(f"Se esperaban 137 artículos y se extrajeron {len(products)}")
    if len({item["sku"] for item in products}) != len(products):
        raise RuntimeError("El PDF contiene SKUs duplicados")

    render_and_crop(args.pdf, products, args.work_dir / "pages", args.images)
    args.output.parent.mkdir(parents=True, exist_ok=True)
    args.output.write_text(
        json.dumps(products, ensure_ascii=False, indent=2) + "\n",
        encoding="utf-8",
    )
    shutil.rmtree(args.work_dir / "pages")
    print(f"Catálogo generado: {len(products)} artículos")


if __name__ == "__main__":
    main()
