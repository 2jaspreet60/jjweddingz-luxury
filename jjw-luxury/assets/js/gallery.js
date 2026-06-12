/**
 * assets/js/gallery.js — JJ WeddingZ Photography
 * Private client gallery: lightbox, keyboard nav, download-all.
 *
 * @package JJWeddingZ
 */

'use strict';

(function clientGallery() {
    const grid       = document.getElementById('client-gallery-grid');
    const lightbox   = document.getElementById('gallery-lightbox');
    const lbImg      = document.getElementById('lightbox-img');
    const lbCounter  = document.getElementById('lightbox-counter');
    const lbDownload = document.getElementById('lightbox-download');
    const lbPrev     = document.getElementById('lightbox-prev');
    const lbNext     = document.getElementById('lightbox-next');
    const lbClose    = document.getElementById('lightbox-close');
    const lbBackdrop = document.getElementById('lightbox-backdrop');
    const dlAllBtn   = document.getElementById('download-all-btn');

    if (!grid || !lightbox) return;

    const items    = [...grid.querySelectorAll('.gallery-item')];
    const total    = items.length;
    let current    = 0;
    let isOpen     = false;

    /* ─── Open lightbox ─────────────────────────────────────────────────── */
    function openLightbox(idx) {
        current = idx;
        updateLightbox();
        lightbox.removeAttribute('hidden');
        lightbox.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
        isOpen = true;
        lbClose && lbClose.focus();
    }

    function closeLightbox() {
        lightbox.setAttribute('hidden', '');
        lightbox.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
        isOpen = false;
        // Return focus to triggering item
        const btn = items[current]?.querySelector('.gallery-item__view-btn');
        btn && btn.focus();
    }

    function updateLightbox() {
        const item     = items[current];
        const fullUrl  = item?.dataset.full || '';
        const imgEl    = item?.querySelector('img');
        const alt      = imgEl?.alt || `Gallery photo ${current + 1}`;

        // Fade transition
        if (lbImg) {
            lbImg.style.opacity = '0';
            lbImg.style.transform = 'scale(0.96)';
            lbImg.onload = () => {
                lbImg.style.transition = 'opacity 350ms, transform 350ms';
                lbImg.style.opacity = '1';
                lbImg.style.transform = 'scale(1)';
            };
            lbImg.src = fullUrl;
            lbImg.alt = alt;
        }

        if (lbCounter) lbCounter.textContent = `${current + 1} / ${total}`;
        if (lbDownload) {
            lbDownload.href     = fullUrl;
            lbDownload.download = `jjweddingz-photo-${current + 1}.jpg`;
        }
    }

    function goTo(n) {
        current = (n + total) % total;
        updateLightbox();
    }

    /* ─── Bind view buttons ─────────────────────────────────────────────── */
    items.forEach((item, i) => {
        const btn = item.querySelector('.gallery-item__view-btn');
        if (btn) {
            btn.addEventListener('click', () => openLightbox(i));
        }
        // Click on image also opens lightbox
        const img = item.querySelector('img');
        if (img) {
            img.style.cursor = 'zoom-in';
            img.addEventListener('click', () => openLightbox(i));
        }
    });

    /* ─── Lightbox controls ─────────────────────────────────────────────── */
    lbClose   && lbClose.addEventListener('click', closeLightbox);
    lbBackdrop && lbBackdrop.addEventListener('click', closeLightbox);
    lbPrev     && lbPrev.addEventListener('click', () => goTo(current - 1));
    lbNext     && lbNext.addEventListener('click', () => goTo(current + 1));

    /* ─── Keyboard navigation ───────────────────────────────────────────── */
    document.addEventListener('keydown', (e) => {
        if (!isOpen) return;
        switch (e.key) {
            case 'Escape':    closeLightbox(); break;
            case 'ArrowLeft': goTo(current - 1); break;
            case 'ArrowRight':goTo(current + 1); break;
        }
    });

    /* ─── Touch swipe on lightbox ───────────────────────────────────────── */
    let touchX = 0;
    lightbox.addEventListener('touchstart', e => { touchX = e.touches[0].clientX; }, { passive: true });
    lightbox.addEventListener('touchend', e => {
        const diff = e.changedTouches[0].clientX - touchX;
        if (Math.abs(diff) > 60) { goTo(diff < 0 ? current + 1 : current - 1); }
    }, { passive: true });

    /* ─── Download All ──────────────────────────────────────────────────── */
    if (dlAllBtn) {
        dlAllBtn.addEventListener('click', () => {
            dlAllBtn.textContent = 'Preparing Downloads…';
            dlAllBtn.disabled = true;

            let delay = 0;
            items.forEach((item, i) => {
                const url = item.dataset.full;
                if (!url) return;
                setTimeout(() => {
                    const a = document.createElement('a');
                    a.href     = url;
                    a.download = `jjweddingz-photo-${i + 1}.jpg`;
                    a.style.display = 'none';
                    document.body.appendChild(a);
                    a.click();
                    document.body.removeChild(a);

                    if (i === items.length - 1) {
                        dlAllBtn.textContent = 'All Downloaded!';
                        setTimeout(() => {
                            dlAllBtn.textContent = 'Download All';
                            dlAllBtn.disabled = false;
                        }, 3000);
                    }
                }, delay);
                delay += 300; // stagger to prevent browser blocking
            });
        });
    }

    /* ─── Inject Gallery CSS ────────────────────────────────────────────── */
    const style = document.createElement('style');
    style.textContent = `
        /* Gallery Grid */
        #client-gallery-grid {
            columns: 3;
            column-gap: 12px;
            gap: 12px;
        }
        @media(max-width:900px){ #client-gallery-grid { columns: 2; } }
        @media(max-width:480px){ #client-gallery-grid { columns: 1; } }

        .gallery-item {
            position: relative;
            break-inside: avoid;
            margin-bottom: 12px;
            overflow: hidden;
            border-radius: 8px;
            cursor: zoom-in;
            background: #1a1a1a;
        }
        .gallery-item img {
            width: 100%;
            height: auto;
            display: block;
            transition: transform 500ms cubic-bezier(0.16,1,0.3,1);
        }
        .gallery-item:hover img { transform: scale(1.04); }
        .gallery-item__overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(to top, rgba(0,0,0,0.6) 0%, transparent 50%);
            opacity: 0;
            transition: opacity 300ms;
            display: flex;
            align-items: flex-end;
            justify-content: flex-end;
            padding: 1rem;
            gap: .5rem;
        }
        .gallery-item:hover .gallery-item__overlay { opacity: 1; }
        .gallery-item__view-btn,
        .gallery-item__dl-btn {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: rgba(255,255,255,0.95);
            color: #0a0a0a;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            transition: transform 200ms, background 200ms;
        }
        .gallery-item__view-btn:hover,
        .gallery-item__dl-btn:hover { background: #c9a96e; color: #fff; transform: scale(1.1); }

        /* Lightbox */
        .gallery-lightbox {
            position: fixed;
            inset: 0;
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .gallery-lightbox[hidden] { display: none; }
        .lightbox__backdrop {
            position: absolute;
            inset: 0;
            background: rgba(0,0,0,0.92);
            cursor: zoom-out;
        }
        .lightbox__inner {
            position: relative;
            z-index: 1;
            display: flex;
            align-items: center;
            gap: 1rem;
            max-width: 95vw;
            max-height: 95vh;
        }
        .lightbox__close {
            position: fixed;
            top: 1.5rem;
            right: 1.5rem;
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: rgba(255,255,255,0.1);
            color: #fff;
            border: 1.5px solid rgba(255,255,255,0.3);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 200ms;
            z-index: 2;
        }
        .lightbox__close:hover { background: rgba(255,255,255,0.2); }
        .lightbox__nav {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: rgba(255,255,255,0.1);
            color: #fff;
            border: 1.5px solid rgba(255,255,255,0.2);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            transition: background 200ms;
        }
        .lightbox__nav:hover { background: rgba(201,169,110,0.4); }
        .lightbox__media { max-width: 85vw; max-height: 88vh; }
        .lightbox__img {
            max-width: 85vw;
            max-height: 85vh;
            object-fit: contain;
            border-radius: 8px;
            box-shadow: 0 40px 80px rgba(0,0,0,0.5);
        }
        .lightbox__footer {
            position: fixed;
            bottom: 1.5rem;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            align-items: center;
            gap: 1.5rem;
            color: rgba(255,255,255,0.8);
            font-size: .85rem;
            z-index: 2;
        }
        .lightbox__download {
            display: inline-flex;
            align-items: center;
            gap: .4rem;
            color: #c9a96e;
            font-size: .82rem;
            font-weight: 600;
            text-decoration: none;
            transition: color 200ms;
        }
        .lightbox__download:hover { color: #dfc28a; }

        /* Gallery toolbar */
        .gallery-toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
            gap: 1rem;
        }
        .gallery-image-count { font-size: .85rem; color: var(--clr-mist); }
        .gallery-toolbar__actions { display: flex; gap: .75rem; flex-wrap: wrap; }

        /* Gallery hero */
        .gallery-hero {
            background: var(--clr-cream);
            padding: 8rem 0 4rem;
            border-bottom: 1px solid var(--clr-border);
        }
        .gallery-hero__title { margin-top: .5rem; }

        /* Access gate */
        .gallery-gate { padding-block: 5rem; }
        .gallery-gate__card {
            max-width: 520px;
            margin-inline: auto;
            background: #fff;
            border: 1px solid var(--clr-border);
            border-radius: 24px;
            padding: 3rem;
            text-align: center;
            box-shadow: 0 20px 60px rgba(0,0,0,.06);
        }
        .gallery-gate__icon { margin-bottom: 1.5rem; }
        .gallery-gate__heading { font-size: 1.8rem; margin-bottom: .75rem; }
        .gallery-gate__subtext { color: var(--clr-mist); font-size: .95rem; line-height: 1.7; margin-bottom: 2rem; }
        .gallery-gate__error {
            background: #fff5f5;
            border: 1.5px solid #fca5a5;
            border-radius: 8px;
            color: #b91c1c;
            padding: .75rem 1rem;
            font-size: .88rem;
            margin-bottom: 1.25rem;
            display: flex;
            align-items: center;
            gap: .5rem;
        }
        .gallery-gate__input {
            font-size: 1.1rem;
            text-align: center;
            letter-spacing: .1em;
            padding: 1rem 1.25rem;
        }
        .gallery-gate__hint { color: var(--clr-fog); font-size: .78rem; margin-top: .5rem; }
        .gallery-gate__submit { width: 100%; margin-top: 1.25rem; justify-content: center; }
        .gallery-gate__support { margin-top: 1.5rem; font-size: .85rem; color: var(--clr-fog); }
        .gallery-gate__support a { color: var(--clr-gold); font-weight: 600; }

        /* Empty state */
        .gallery-empty {
            text-align: center;
            padding: 5rem 0;
            color: var(--clr-mist);
        }
        .gallery-empty svg { margin: 0 auto 1.5rem; opacity: .5; }
        .gallery-empty h3 { font-size: 1.6rem; margin-bottom: .5rem; }

        /* Comments */
        .gallery-comments {
            margin-top: 4rem;
            padding-top: 3rem;
            border-top: 1px solid var(--clr-border);
        }
        .gallery-comments__title { font-size: 1.4rem; margin-bottom: .5rem; }
        .gallery-comments__desc { color: var(--clr-mist); margin-bottom: 2rem; }
        @media(max-width:560px){
            .lightbox__nav { display: none; }
            .lightbox__img { max-width: 96vw; max-height: 75vh; }
        }
    `;
    document.head.appendChild(style);
})();
