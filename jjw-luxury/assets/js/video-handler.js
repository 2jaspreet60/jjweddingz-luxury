/**
 * assets/js/video-handler.js — JJ WeddingZ Photography
 * Unified triple-source video rendering:
 *   1. HTML5 local video (lazy-load + mute-on-scroll)
 *   2. YouTube (lite embed, no cookies, poster-first)
 *   3. Vimeo Pro (privacy-enhanced)
 *
 * All videos enforce 16:9 or 9:16 aspect ratios via CSS class.
 *
 * @package JJWeddingZ
 */

'use strict';

/* ═══════════════════════════════════════════════════════════════════════════
   HTML5 VIDEO — LAZY LOAD + MUTE ON SCROLL
   ═══════════════════════════════════════════════════════════════════════════ */

(function html5VideoLazyLoad() {
    const videos = document.querySelectorAll('video[data-src], video[data-jjwz-video]');
    if (!videos.length || !('IntersectionObserver' in window)) {
        // Fallback: just ensure muted
        document.querySelectorAll('video').forEach(v => { v.muted = true; });
        return;
    }

    // Lazy-load observer: load video when 20% visible
    const loadObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            const video = entry.target;
            if (entry.isIntersecting) {
                // Load src
                if (video.dataset.src) {
                    const source = document.createElement('source');
                    source.src  = video.dataset.src;
                    source.type = video.dataset.type || 'video/mp4';
                    video.appendChild(source);
                    video.removeAttribute('data-src');
                    video.load();
                }
                loadObserver.unobserve(video);
            }
        });
    }, { threshold: 0.2 });

    videos.forEach(v => loadObserver.observe(v));

    // Mute-on-scroll: mute when <50% visible, unmute if user interacted and >50% visible
    const muteObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            const video = entry.target;
            if (entry.isIntersecting && entry.intersectionRatio >= 0.5) {
                // Only autoplay if in viewport and muted (autoplay policy)
                if (video.paused && video.muted) {
                    video.play().catch(() => {});
                }
            } else {
                if (!video.paused) {
                    video.pause();
                }
            }
        });
    }, { threshold: [0, 0.5, 1] });

    document.querySelectorAll('video[autoplay]').forEach(v => muteObserver.observe(v));
})();

/* ═══════════════════════════════════════════════════════════════════════════
   YOUTUBE — LITE EMBED (poster → iframe on click)
   ═══════════════════════════════════════════════════════════════════════════ */

(function youtubeEmbed() {
    const wrappers = document.querySelectorAll('[data-video-type="youtube"]');
    if (!wrappers.length) return;

    wrappers.forEach(wrap => {
        const videoId  = wrap.dataset.videoId;
        const autoplay = wrap.dataset.autoplay === '1';

        if (!videoId) return;

        // If autoplay (hero), iframe is already in DOM; just ensure it loaded
        if (autoplay) return;

        // Build poster-first lite embed
        const poster = `https://i.ytimg.com/vi/${videoId}/maxresdefault.jpg`;
        wrap.style.position    = 'relative';
        wrap.style.cursor      = 'pointer';
        wrap.style.background  = `#000 url(${poster}) center/cover no-repeat`;
        wrap.setAttribute('role', 'button');
        wrap.setAttribute('aria-label', 'Play video');
        wrap.setAttribute('tabindex', '0');

        // Play icon overlay
        const playBtn = document.createElement('button');
        playBtn.className   = 'yt-play-btn';
        playBtn.innerHTML   = `
            <svg width="72" height="72" viewBox="0 0 72 72" fill="none" aria-hidden="true">
                <circle cx="36" cy="36" r="36" fill="rgba(0,0,0,0.6)"/>
                <polygon points="28,20 56,36 28,52" fill="white"/>
            </svg>`;
        playBtn.style.cssText = 'position:absolute;inset:0;margin:auto;width:72px;height:72px;border:none;background:none;cursor:pointer;transition:transform 200ms';
        wrap.appendChild(playBtn);

        // Load iframe on click
        const loadIframe = () => {
            const iframe = document.createElement('iframe');
            const params = new URLSearchParams({
                autoplay: 1,
                mute: 0,
                rel: 0,
                modestbranding: 1,
                iv_load_policy: 3,
                enablejsapi: 1,
            });
            iframe.src              = `https://www.youtube-nocookie.com/embed/${videoId}?${params}`;
            iframe.allow            = 'autoplay; encrypted-media; fullscreen';
            iframe.allowFullscreen  = true;
            iframe.title            = 'YouTube video player';
            iframe.loading          = 'lazy';
            iframe.style.cssText    = 'position:absolute;inset:0;width:100%;height:100%;border:0';
            wrap.innerHTML          = '';
            wrap.appendChild(iframe);
        };

        playBtn.addEventListener('click', loadIframe);
        wrap.addEventListener('keydown', e => { if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); loadIframe(); } });

        // Lazy-load poster image via IntersectionObserver
        if ('IntersectionObserver' in window) {
            const obs = new IntersectionObserver(entries => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        // Poster is background-image, nothing extra needed
                        obs.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.1 });
            obs.observe(wrap);
        }
    });
})();

/* ═══════════════════════════════════════════════════════════════════════════
   VIMEO PRO — PRIVACY-ENHANCED EMBED
   ═══════════════════════════════════════════════════════════════════════════ */

(function vimeoEmbed() {
    const wrappers = document.querySelectorAll('[data-video-type="vimeo"]');
    if (!wrappers.length) return;

    wrappers.forEach(wrap => {
        const videoId  = wrap.dataset.videoId;
        const autoplay = wrap.dataset.autoplay === '1';
        if (!videoId) return;

        if (!('IntersectionObserver' in window)) {
            loadVimeoIframe(wrap, videoId, autoplay);
            return;
        }

        const obs = new IntersectionObserver(entries => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    loadVimeoIframe(wrap, videoId, autoplay);
                    obs.unobserve(entry.target);
                }
            });
        }, { threshold: 0.15 });
        obs.observe(wrap);
    });

    function loadVimeoIframe(wrap, videoId, autoplay) {
        const params = new URLSearchParams({
            autoplay:   autoplay ? 1 : 0,
            muted:      autoplay ? 1 : 0,
            loop:       autoplay ? 1 : 0,
            title:      0,
            byline:     0,
            portrait:   0,
            dnt:        1, // Privacy-enhanced: do not track
            quality:    'auto',
        });
        const iframe = document.createElement('iframe');
        iframe.src            = `https://player.vimeo.com/video/${videoId}?${params}`;
        iframe.allow          = 'autoplay; fullscreen; picture-in-picture';
        iframe.allowFullscreen = true;
        iframe.title          = 'Vimeo video player';
        iframe.loading        = 'lazy';
        iframe.style.cssText  = 'position:absolute;inset:0;width:100%;height:100%;border:0';
        wrap.style.position   = 'relative';
        wrap.appendChild(iframe);
    }
})();

/* ═══════════════════════════════════════════════════════════════════════════
   ASPECT RATIO ENFORCEMENT
   Ensures all video containers maintain 16:9 or 9:16
   ═══════════════════════════════════════════════════════════════════════════ */

(function enforceAspectRatio() {
    const style = document.createElement('style');
    style.textContent = `
        .jjwz-video-wrap {
            position: relative;
            width: 100%;
            overflow: hidden;
            background: #000;
        }
        .jjwz-video-wrap--16-9 { aspect-ratio: 16 / 9; }
        .jjwz-video-wrap--9-16 { aspect-ratio: 9 / 16; max-width: 420px; }
        .jjwz-video-wrap iframe,
        .jjwz-video-wrap video {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .yt-play-btn:hover { transform: scale(1.1); }
    `;
    document.head.appendChild(style);

    // Add wrapper class to all [data-video-type] elements that lack it
    document.querySelectorAll('[data-video-type]').forEach(el => {
        if (!el.classList.contains('jjwz-video-wrap')) {
            el.classList.add('jjwz-video-wrap', 'jjwz-video-wrap--16-9');
        }
    });
})();
