/**
 * Dedicated alumni ID card print preview.
 *
 * Printing the full dashboard can inherit transforms, hidden panes, and page
 * chrome. This helper prints only the rendered card sides in a clean document.
 */
(function (global) {
  "use strict";

  const CARD_CSS_HREF = "assets/css/id-card-design.css?v=20260518.08";
  const PRINT_WINDOW_NAME = "alumniIdCardPrintPreview";

  function escapeHtml(value) {
    return String(value || "")
      .replace(/&/g, "&amp;")
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;")
      .replace(/"/g, "&quot;");
  }

  function resolveAssetUrl(path) {
    if (!global.location || typeof URL === "undefined") {
      return path;
    }

    const base = global.document?.baseURI || global.location.href;
    return new URL(path, base).href;
  }

  function replaceCanvasCopies(source, clone) {
    const sourceCanvases = Array.from(source.querySelectorAll("canvas")).filter(
      (canvas) => !canvas.closest("#qrCodeCanvas, .qr-container"),
    );
    const clonedCanvases = Array.from(clone.querySelectorAll("canvas")).filter(
      (canvas) => !canvas.closest("#qrCodeCanvas, .qr-container"),
    );

    sourceCanvases.forEach((canvas, index) => {
      const clonedCanvas = clonedCanvases[index];

      if (!clonedCanvas) {
        return;
      }

      try {
        const image = clone.ownerDocument.createElement("img");
        image.src = canvas.toDataURL("image/png");
        image.width = canvas.width;
        image.height = canvas.height;
        image.alt = canvas.getAttribute("aria-label") || "QR code";
        image.className = canvas.className || "";
        clonedCanvas.replaceWith(image);
      } catch (error) {
        console.warn("Unable to copy ID card canvas for print:", error);
      }
    });
  }

  function getQrDataUrl(source) {
    const qrRoot =
      source.querySelector("#qrCodeCanvas") || source.querySelector(".qr-container");

    if (!qrRoot) {
      return "";
    }

    const canvas = qrRoot.querySelector("canvas");
    if (canvas && canvas.width > 0 && canvas.height > 0) {
      try {
        return canvas.toDataURL("image/png");
      } catch (error) {
        console.warn("Unable to snapshot QR canvas for print:", error);
      }
    }

    const image = qrRoot.querySelector("img");
    const imageSource = image?.currentSrc || image?.src || image?.getAttribute("src");
    return imageSource || "";
  }

  function replaceQrForPrint(source, clone) {
    const dataUrl = getQrDataUrl(source);
    if (!dataUrl) {
      return;
    }

    const qrRoot =
      clone.querySelector("#qrCodeCanvas") || clone.querySelector(".qr-container");

    if (!qrRoot) {
      return;
    }

    const image = clone.ownerDocument.createElement("img");
    image.src = dataUrl;
    image.width = 108;
    image.height = 108;
    image.alt = "QR code";
    image.className = "qr-print-image";
    image.decoding = "sync";

    qrRoot.innerHTML = "";
    qrRoot.appendChild(image);
  }

  function absolutizeImageSources(clone) {
    clone.querySelectorAll("img").forEach((image) => {
      const src = image.getAttribute("src");
      if (src) {
        image.setAttribute("src", resolveAssetUrl(src));
      }
    });
  }

  function waitForSourceImages(elements, timeoutMs = 1600) {
    const images = elements
      .flatMap((element) => Array.from(element.querySelectorAll("img")))
      .filter((image) => image.currentSrc || image.src || image.getAttribute("src"));

    if (!images.length) {
      return Promise.resolve();
    }

    const imagePromises = images.map((image) => {
      if (image.complete && image.naturalWidth > 0) {
        return Promise.resolve();
      }

      return new Promise((resolve) => {
        const done = () => resolve();
        image.addEventListener("load", done, { once: true });
        image.addEventListener("error", done, { once: true });
      });
    });

    return Promise.race([
      Promise.all(imagePromises),
      new Promise((resolve) => setTimeout(resolve, timeoutMs)),
    ]);
  }

  function cloneSideForPrint(element) {
    const clone = element.cloneNode(true);
    replaceQrForPrint(element, clone);
    replaceCanvasCopies(element, clone);
    absolutizeImageSources(clone);
    clone.classList.remove("flipped");
    clone.removeAttribute("style");
    return clone.outerHTML;
  }

  function buildPrintDocument(options) {
    const title = escapeHtml(options.browserTitle || options.title || "Alumni ID Card");
    const cssHref = escapeHtml(options.cssHref || CARD_CSS_HREF);
    const rawCardWidth = Math.round(Number(options.cardWidth) || 500);
    const cardWidth = Math.min(500, Math.max(320, rawCardWidth));
    const cardHeight = Math.round(cardWidth / 1.586);
    const fontHref =
      "https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Sora:wght@500;600;700;800&display=swap";

    return `<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>${title}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="${fontHref}">
    <link rel="stylesheet" href="${cssHref}">
    <style>
      @page {
        size: A4 portrait;
        margin: 1cm;
      }

      * {
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
        color-adjust: exact !important;
        box-sizing: border-box !important;
      }

      html,
      body {
        width: auto !important;
        min-width: 0 !important;
        min-height: 0 !important;
        margin: 0 !important;
        padding: 0 !important;
        background: #ffffff !important;
        font-family: "Manrope", "Inter", Arial, sans-serif;
      }

      .id-card-print-document {
        width: 100%;
        min-width: 0;
        margin: 0;
        padding: 0;
        overflow-x: auto;
      }

      .id-card-print-sheet {
        width: 100%;
        min-width: 0;
        min-height: 0;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: flex-start;
        gap: 12px;
        margin: 0;
        padding: 0;
      }

      .id-card-print-document .id-card-front,
      .id-card-print-document .id-card-back,
      .id-card-print-document .id-card-front.new-design {
        display: flex !important;
        flex-direction: column !important;
        position: relative !important;
        inset: auto !important;
        width: ${cardWidth}px !important;
        height: ${cardHeight}px !important;
        max-width: none !important;
        min-width: ${cardWidth}px !important;
        min-height: ${cardHeight}px !important;
        margin: 0 auto !important;
        flex: 0 0 ${cardHeight}px !important;
        overflow: hidden !important;
        transform: none !important;
        backface-visibility: visible !important;
        break-before: auto !important;
        break-after: auto !important;
        break-inside: avoid !important;
        page-break-before: auto !important;
        page-break-after: auto !important;
        page-break-inside: avoid !important;
        transition: none !important;
      }

      .id-card-print-document .id-card-back {
        background: #ffffff !important;
      }

      .id-card-print-document .back-body {
        min-height: 0 !important;
        overflow: hidden !important;
      }

      .id-card-print-document .front-header-new {
        top: 1.2rem !important;
        left: 1.5rem !important;
      }

      .id-card-print-document .front-header-new h2 {
        font-size: 1.05rem !important;
        color: #f8fffb !important;
        -webkit-text-fill-color: #f8fffb !important;
      }

      .id-card-print-document .front-header-new p {
        font-size: 0.55rem !important;
        color: #c7f9df !important;
      }

      .id-card-print-document .front-seal-new {
        top: 1rem !important;
        right: 1.2rem !important;
        width: 55px !important;
        height: 55px !important;
      }

      .id-card-print-document .front-photo-container {
        top: 5.5rem !important;
      }

      .id-card-print-document .front-photo {
        width: 110px !important;
        height: 145px !important;
      }

      .id-card-print-document .field-name-value-under {
        font-size: 0.95rem !important;
        color: #ffffff !important;
      }

      .id-card-print-document .front-info-new {
        margin-top: 4.8rem !important;
      }

      .id-card-print-document .field-alumni-id-value {
        font-size: 1.15rem !important;
      }

      .id-card-print-document .field-row span.value {
        font-size: 0.85rem !important;
      }

      .id-card-print-document .back-header {
        padding: 0.6rem 1.5rem !important;
      }

      .id-card-print-document .back-title {
        font-size: 0.95rem !important;
        color: #f8fffb !important;
        -webkit-text-fill-color: #f8fffb !important;
      }

      .id-card-print-document .valid-badge {
        font-size: 0.65rem !important;
        color: #fff7cc !important;
        -webkit-text-fill-color: #fff7cc !important;
      }

      .id-card-print-document .back-left {
        padding: 1rem 1.5rem !important;
        gap: 0.35rem !important;
      }

      .id-card-print-document .back-left .info-label {
        font-size: 0.5rem !important;
      }

      .id-card-print-document .back-left .info-value {
        font-size: 0.7rem !important;
      }

      .id-card-print-document .back-right {
        width: 160px !important;
        padding: 1rem 1rem 1rem 0 !important;
        gap: 0.5rem !important;
      }

      .id-card-print-document .back-right > div {
        display: flex !important;
        flex-direction: column !important;
        align-items: center !important;
        max-width: 100% !important;
      }

      .id-card-print-document .qr-container,
      .id-card-print-document #qrCodeCanvas {
        width: 122px !important;
        height: 122px !important;
        min-width: 122px !important;
        min-height: 122px !important;
        max-width: 122px !important;
        max-height: 122px !important;
        aspect-ratio: 1 / 1 !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        padding: 0.35rem !important;
        border: 2px solid #155e3a !important;
        border-radius: 8px !important;
        background: #ffffff !important;
        overflow: hidden !important;
        line-height: 0 !important;
        flex: 0 0 122px !important;
      }

      .id-card-print-document .qr-container > #qrCodeCanvas {
        padding: 0 !important;
        border: 0 !important;
        display: flex !important;
      }

      .id-card-print-document .qr-container canvas,
      .id-card-print-document .qr-container img,
      .id-card-print-document .qr-container .qr-print-image,
      .id-card-print-document #qrCodeCanvas canvas,
      .id-card-print-document #qrCodeCanvas img,
      .id-card-print-document #qrCodeCanvas .qr-print-image {
        display: block !important;
        width: 108px !important;
        height: 108px !important;
        min-width: 108px !important;
        min-height: 108px !important;
        max-width: 108px !important;
        max-height: 108px !important;
        object-fit: contain !important;
        flex: 0 0 108px !important;
      }

      .id-card-print-document .qr-container table,
      .id-card-print-document #qrCodeCanvas table {
        display: table !important;
        width: 108px !important;
        height: 108px !important;
        min-width: 108px !important;
        min-height: 108px !important;
        max-width: 108px !important;
        max-height: 108px !important;
        border-collapse: collapse !important;
        table-layout: fixed !important;
      }

      .id-card-print-document .qr-container table td,
      .id-card-print-document #qrCodeCanvas table td {
        padding: 0 !important;
        line-height: 0 !important;
      }

      @media screen {
        body {
          background: #eef2f7 !important;
          padding: 24px 0 !important;
        }

        .id-card-print-sheet {
          min-height: auto;
          padding: 24px 0;
        }
      }

      @media print {
        body {
          padding: 0 !important;
        }

        .id-card-print-sheet {
          padding: 0 !important;
          gap: 12px !important;
        }
      }
    </style>
  </head>
  <body>
    <main class="id-card-print-document" aria-label="Printable alumni ID card">
      <section class="id-card-print-sheet" aria-label="Front and back of alumni ID card">
        ${options.frontHtml || ""}
        ${options.backHtml || ""}
      </section>
    </main>
    <script>
      (function () {
        function waitForImages() {
          var images = Array.prototype.slice.call(document.images || []);
          if (!images.length) {
            return Promise.resolve();
          }

          return Promise.all(images.map(function (image) {
            if (image.complete) {
              return Promise.resolve();
            }

            return new Promise(function (resolve) {
              image.onload = resolve;
              image.onerror = resolve;
            });
          }));
        }

        var fontsReady = document.fonts && document.fonts.ready
          ? document.fonts.ready
          : Promise.resolve();

        Promise.all([fontsReady, waitForImages()]).then(function () {
          setTimeout(function () {
            window.focus();
            window.print();
          }, 180);
        });
      })();
    </script>
  </body>
</html>`;
  }

  async function print(options = {}) {
    const documentRef = options.document || global.document;
    const front = documentRef.querySelector(options.frontSelector || ".id-card-front");
    const back = documentRef.querySelector(options.backSelector || ".id-card-back");

    if (!front || !back) {
      if (global.Utils && typeof global.Utils.error === "function") {
        global.Utils.error("ID card is not ready to print yet.");
      }
      return false;
    }

    const printWindow = global.open(
      "",
      PRINT_WINDOW_NAME,
      "width=960,height=720",
    );

    if (!printWindow || !printWindow.document) {
      global.print();
      return false;
    }

    printWindow.document.open();
    printWindow.document.write(`<!doctype html><title>${escapeHtml(options.title || "Alumni ID Card")}</title><body style="font-family:Arial,sans-serif;margin:24px;">Preparing ID card...</body>`);
    printWindow.document.close();

    await waitForSourceImages([front, back]);

    const frontRect = front.getBoundingClientRect();
    const sourceWidth = Math.min(500, Math.max(320, Math.round(frontRect.width || 500)));

    const html = buildPrintDocument({
      browserTitle: options.title || "Alumni ID Card",
      cssHref: resolveAssetUrl(options.cssHref || CARD_CSS_HREF),
      frontHtml: cloneSideForPrint(front),
      backHtml: cloneSideForPrint(back),
      cardWidth: options.cardWidth || sourceWidth,
    });

    printWindow.document.open();
    printWindow.document.write(html);
    printWindow.document.close();

    return true;
  }

  const IdCardPrinter = {
    print,
    buildPrintDocument,
    cloneSideForPrint,
  };

  global.IdCardPrinter = IdCardPrinter;

  if (typeof module !== "undefined" && module.exports) {
    module.exports = IdCardPrinter;
  }
})(typeof window !== "undefined" ? window : globalThis);
