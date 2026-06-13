/**
 * assets/js/theme.js — JJ WeddingZ Photography
 * Main theme JavaScript — all non-critical interactivity.
 * Loaded with defer attribute. No jQuery dependency.
 *
 * @package JJWeddingZ
 */

'use strict';

/* ═══════════════════════════════════════════════════════════════════════════
   UTILITY
   ═══════════════════════════════════════════════════════════════════════════ */

const $ = (sel, ctx = document) => ctx.querySelector(sel);
const $$ = (sel, ctx = document) => [...ctx.querySelectorAll(sel)];
const on = (el, ev, fn, opts = {}) => el && el.addEventListener(ev, fn, opts);

/* ═══════════════════════════════════════════════════════════════════════════
   1. HEADER — SCROLL EFFECT + MOBILE NAV
   ═══════════════════════════════════════════════════════════════════════════ */

(function headerInit() {
    const header    = $('#jjwz-header');
    const toggle    = $('#mobile-menu-toggle');
    const nav       = $('#header-nav');
    const body      = document.body;
    let scrolled    = false;

    if (!header) return;

    // Scroll sticky shadow
    const handleScroll = () => {
        const should = window.scrollY > 50;
        if (should !== scrolled) {
            header.classList.toggle('is-scrolled', should);
            scrolled = should;
        }
    };
    window.addEventListener('scroll', handleScroll, { passive: true });
    handleScroll();

    // Mobile toggle
    if (toggle && nav) {
        on(toggle, 'click', () => {
            const open = nav.classList.toggle('is-open');
            toggle.setAttribute('aria-expanded', String(open));
            body.style.overflow = open ? 'hidden' : '';
        });

        // Close on outside click
        on(document, 'click', (e) => {
            if (!header.contains(e.target) && nav.classList.contains('is-open')) {
                nav.classList.remove('is-open');
                toggle.setAttribute('aria-expanded', 'false');
                body.style.overflow = '';
            }
        });

        // Close on Escape
        on(document, 'keydown', (e) => {
            if (e.key === 'Escape' && nav.classList.contains('is-open')) {
                nav.classList.remove('is-open');
                toggle.setAttribute('aria-expanded', 'false');
                body.style.overflow = '';
                toggle.focus();
            }
        });
    }
})();

/* ═══════════════════════════════════════════════════════════════════════════
   2. INTERSECTION OBSERVER — ANIMATE ON SCROLL
   ═══════════════════════════════════════════════════════════════════════════ */

(function animOnScroll() {
    const els = $$('[data-anim]');
    if (!els.length || !('IntersectionObserver' in window)) {
        els.forEach(el => el.classList.add('anim-done'));
        return;
    }

    // Inject base animation styles
    const style = document.createElement('style');
    style.textContent = `
        [data-anim]{opacity:0;transition:opacity 700ms cubic-bezier(0.16,1,0.3,1),transform 700ms cubic-bezier(0.16,1,0.3,1)}
        [data-anim="fade-up"]{transform:translateY(40px)}
        [data-anim="fade-right"]{transform:translateX(-40px)}
        [data-anim="fade-left"]{transform:translateX(40px)}
        [data-anim="fade-in"]{transform:scale(0.96)}
        [data-anim].anim-done{opacity:1;transform:none}
    `;
    document.head.appendChild(style);

    const obs = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (!entry.isIntersecting) return;
            const delay = entry.target.dataset.animDelay || 0;
            setTimeout(() => {
                entry.target.classList.add('anim-done');
                obs.unobserve(entry.target);
            }, Number(delay));
        });
    }, { threshold: 0.12, rootMargin: '0px 0px -60px 0px' });

    els.forEach(el => obs.observe(el));
})();

/* ═══════════════════════════════════════════════════════════════════════════
   3. MASONRY PORTFOLIO GRID + FILTER
   ═══════════════════════════════════════════════════════════════════════════ */

(function portfolioFilter() {
    const filterBtns = $$('.filter-btn');
    if (!filterBtns.length) return;

    filterBtns.forEach(btn => {
        on(btn, 'click', () => {
            const filter = btn.dataset.filter;

            // Update aria + active states
            filterBtns.forEach(b => {
                b.classList.remove('is-active');
                b.setAttribute('aria-selected', 'false');
            });
            btn.classList.add('is-active');
            btn.setAttribute('aria-selected', 'true');

            // Filter cards
            $$('.masonry-card').forEach(card => {
                const cat = card.dataset.category || '';
                const show = filter === 'all' || cat.toLowerCase().includes(filter.toLowerCase());
                card.style.transition = 'opacity 350ms, transform 350ms';
                if (show) {
                    card.style.opacity = '1';
                    card.style.transform = 'scale(1)';
                    card.removeAttribute('hidden');
                } else {
                    card.style.opacity = '0';
                    card.style.transform = 'scale(0.94)';
                    setTimeout(() => card.setAttribute('hidden', ''), 350);
                }
            });
        });
    });
})();

/* ═══════════════════════════════════════════════════════════════════════════
   4. TESTIMONIAL SLIDER
   ═══════════════════════════════════════════════════════════════════════════ */

(function testiSlider() {
    const track    = $('#testimonials-track');
    const prevBtn  = $('#testi-prev');
    const nextBtn  = $('#testi-next');
    const dotsWrap = $('#testi-dots');
    if (!track) return;

    const cards = $$('.testimonial-card', track);
    if (cards.length < 2) return;

    let current = 0;
    let autoTimer = null;

    // Create dots
    cards.forEach((_, i) => {
        const dot = document.createElement('button');
        dot.className = 'testi-dot' + (i === 0 ? ' is-active' : '');
        dot.setAttribute('aria-label', `Go to testimonial ${i + 1}`);
        on(dot, 'click', () => goTo(i));
        dotsWrap && dotsWrap.appendChild(dot);
    });

    function goTo(n) {
        cards[current].classList.remove('is-active');
        $$('.testi-dot', dotsWrap || document).forEach(d => d.classList.remove('is-active'));
        current = (n + cards.length) % cards.length;
        cards[current].classList.add('is-active');
        const dots = $$('.testi-dot', dotsWrap || document);
        if (dots[current]) dots[current].classList.add('is-active');
    }

    // Init first card
    cards.forEach(c => c.classList.remove('is-active'));
    cards[0].classList.add('is-active');

    on(prevBtn, 'click', () => { goTo(current - 1); resetAuto(); });
    on(nextBtn, 'click', () => { goTo(current + 1); resetAuto(); });

    // Auto-advance
    function startAuto() { autoTimer = setInterval(() => goTo(current + 1), 5500); }
    function resetAuto() { clearInterval(autoTimer); startAuto(); }
    startAuto();

    // Pause on hover/focus
    track.addEventListener('mouseenter', () => clearInterval(autoTimer));
    track.addEventListener('mouseleave', startAuto);

    // Touch/swipe
    let touchStartX = 0;
    on(track, 'touchstart', e => { touchStartX = e.touches[0].clientX; }, { passive: true });
    on(track, 'touchend', e => {
        const diff = e.changedTouches[0].clientX - touchStartX;
        if (Math.abs(diff) > 50) { goTo(diff < 0 ? current + 1 : current - 1); resetAuto(); }
    }, { passive: true });
})();

/* ═══════════════════════════════════════════════════════════════════════════
   5. FAQ ACCORDION
   ═══════════════════════════════════════════════════════════════════════════ */

(function faqAccordion() {
    $$('.accordion__trigger').forEach(btn => {
        on(btn, 'click', () => {
            const item   = btn.closest('.accordion__item');
            const panel  = btn.nextElementSibling;
            const open   = btn.getAttribute('aria-expanded') === 'true';

            // Close all siblings
            const accordion = btn.closest('.jjwz-accordion');
            if (accordion) {
                $$('.accordion__trigger', accordion).forEach(b => {
                    if (b !== btn) {
                        b.setAttribute('aria-expanded', 'false');
                        const p = b.nextElementSibling;
                        if (p) {
                            p.style.maxHeight = '0';
                            p.hidden = true;
                        }
                        b.closest('.accordion__item')?.classList.remove('is-open');
                    }
                });
            }

            // Toggle current
            if (open) {
                btn.setAttribute('aria-expanded', 'false');
                panel.style.maxHeight = '0';
                setTimeout(() => { panel.hidden = true; }, 300);
                item.classList.remove('is-open');
            } else {
                btn.setAttribute('aria-expanded', 'true');
                panel.removeAttribute('hidden');
                panel.style.maxHeight = panel.scrollHeight + 'px';
                item.classList.add('is-open');
            }
        });
    });
})();

/* ═══════════════════════════════════════════════════════════════════════════
   6. SERVICES FAQ TABS
   ═══════════════════════════════════════════════════════════════════════════ */

(function faqTabs() {
    const tabBtns  = $$('.faq-tab-btn');
    const panels   = $$('.faq-panel');
    if (!tabBtns.length) return;

    tabBtns.forEach(btn => {
        on(btn, 'click', () => {
            const target = btn.dataset.tab;
            tabBtns.forEach(b => { b.classList.remove('is-active'); b.setAttribute('aria-selected', 'false'); });
            panels.forEach(p => { p.classList.remove('is-active'); p.hidden = true; });
            btn.classList.add('is-active');
            btn.setAttribute('aria-selected', 'true');
            const panel = $(`#faq-panel-${target}`);
            if (panel) { panel.classList.add('is-active'); panel.removeAttribute('hidden'); }
        });
    });
})();

/* ═══════════════════════════════════════════════════════════════════════════
   7. STAT COUNTER ANIMATION
   ═══════════════════════════════════════════════════════════════════════════ */

(function statCounters() {
    const counters = $$('.stat-counter');
    if (!counters.length || !('IntersectionObserver' in window)) return;

    const obs = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (!entry.isIntersecting) return;
            const el     = entry.target;
            const target = parseInt(el.dataset.target, 10) || 0;
            const dur    = 1800;
            const step   = dur / target;
            let current  = 0;

            const timer = setInterval(() => {
                current += Math.ceil(target / (dur / 30));
                if (current >= target) {
                    el.textContent = target;
                    clearInterval(timer);
                } else {
                    el.textContent = current;
                }
            }, 30);

            obs.unobserve(el);
        });
    }, { threshold: 0.5 });

    counters.forEach(c => obs.observe(c));
})();

/* ═══════════════════════════════════════════════════════════════════════════
   8. BLOG POST — AUTO TABLE OF CONTENTS
   ═══════════════════════════════════════════════════════════════════════════ */

(function autoTOC() {
    const content = $('#post-content');
    const tocNav  = $('#post-toc-nav');
    const tocBox  = $('#post-toc');
    if (!content || !tocNav) return;

    const headings = $$('h2, h3', content);
    if (headings.length < 3) return;

    tocBox.removeAttribute('hidden');
    const list = document.createElement('ol');
    list.className = 'toc-list';

    headings.forEach((h, i) => {
        if (!h.id) h.id = 'toc-heading-' + i;
        const li = document.createElement('li');
        li.className = 'toc-item toc-item--' + h.tagName.toLowerCase();
        li.innerHTML = `<a href="#${h.id}">${h.textContent}</a>`;
        list.appendChild(li);
    });
    tocNav.appendChild(list);
})();

/* ═══════════════════════════════════════════════════════════════════════════
   9. FLOATING WHATSAPP BUTTON — HIDE ON SCROLL UP, SHOW ON SCROLL DOWN
   ═══════════════════════════════════════════════════════════════════════════ */

(function waFloat() {
    const waBtn = $('#wa-float-btn');
    if (!waBtn) return;
    let lastY  = 0;
    let ticking = false;

    window.addEventListener('scroll', () => {
        if (!ticking) {
            requestAnimationFrame(() => {
                const y = window.scrollY;
                // Show after 400px scroll
                if (y > 400) {
                    waBtn.classList.add('is-visible');
                } else {
                    waBtn.classList.remove('is-visible');
                }
                lastY = y;
                ticking = false;
            });
            ticking = true;
        }
    }, { passive: true });
})();

/* ═══════════════════════════════════════════════════════════════════════════
   10. SMOOTH ANCHOR SCROLLING
   ═══════════════════════════════════════════════════════════════════════════ */

(function smoothAnchors() {
    $$('a[href^="#"]').forEach(a => {
        on(a, 'click', (e) => {
            const id = a.getAttribute('href').slice(1);
            if (!id) return;
            const target = document.getElementById(id);
            if (target) {
                e.preventDefault();
                const headerH = parseInt(getComputedStyle(document.documentElement).getPropertyValue('--header-height'), 10) || 80;
                const top = target.getBoundingClientRect().top + window.scrollY - headerH - 16;
                window.scrollTo({ top, behavior: 'smooth' });
                // Focus for accessibility
                target.setAttribute('tabindex', '-1');
                target.focus({ preventScroll: true });
            }
        });
    });
})();

/* ═══════════════════════════════════════════════════════════════════════════
   11. AJAX CONTACT FORM SUBMISSION
   ═══════════════════════════════════════════════════════════════════════════ */
(function contactFormAjax() {
    const form = $('#jjwz-contact-form');
    if (!form) return;

    const btn = $('button[type="submit"]', form);
    const responseDiv = $('#form-response');

    on(form, 'submit', (e) => {
        e.preventDefault();
        if (!window.JJWZ_FORMS) {
            console.error('JJWZ_FORMS dynamic localizer not found.');
            return;
        }

        const formData = new FormData(form);
        formData.append('action', 'jjwz_submit_lead');
        formData.append('nonce', window.JJWZ_FORMS.nonce);

        if (btn) {
            btn.disabled = true;
            btn.dataset.originalText = btn.textContent;
            btn.textContent = window.JJWZ_FORMS.strings.sending || 'Sending...';
        }

        if (responseDiv) {
            responseDiv.className = 'form-response-msg';
            responseDiv.textContent = '';
            responseDiv.hidden = true;
        }

        fetch(window.JJWZ_FORMS.ajaxUrl, {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (responseDiv) {
                responseDiv.hidden = false;
                if (data.success) {
                    responseDiv.className = 'form-response-msg is-success';
                    responseDiv.textContent = data.data.message || window.JJWZ_FORMS.strings.success;
                    form.reset();
                } else {
                    responseDiv.className = 'form-response-msg is-error';
                    responseDiv.textContent = data.data.message || window.JJWZ_FORMS.strings.error;
                }
            }
        })
        .catch(err => {
            console.error('Lead submission error:', err);
            if (responseDiv) {
                responseDiv.hidden = false;
                responseDiv.className = 'form-response-msg is-error';
                responseDiv.textContent = window.JJWZ_FORMS.strings.error;
            }
        })
        .finally(() => {
            if (btn) {
                btn.disabled = false;
                btn.textContent = btn.dataset.originalText || 'Send Message';
            }
        });
    });
})();

/* ═══════════════════════════════════════════════════════════════════════════
   12. SERVICES CAROUSEL
   ═══════════════════════════════════════════════════════════════════════════ */
(function servicesCarousel() {
    const carousel = $('#services-carousel');
    if (!carousel) return;
    
    const viewport = $('#services-viewport', carousel);
    const track = $('#services-track', carousel);
    const prevBtn = $('#services-prev');
    const nextBtn = $('#services-next');
    const dotsWrap = $('#services-dots', carousel);
    
    if (!viewport || !track) return;
    
    const slides = $$('.services-carousel__slide', track);
    if (!slides.length) return;
    
    let current = 0;
    
    function getMaxIndex() {
        if (!slides.length) return 0;
        const slideWidth = slides[0].getBoundingClientRect().width;
        const containerWidth = viewport.getBoundingClientRect().width;
        const V = Math.round(containerWidth / slideWidth) || 1;
        return Math.max(0, slides.length - V);
    }
    
    function updateDots() {
        if (!dotsWrap) return;
        dotsWrap.innerHTML = '';
        const maxIndex = getMaxIndex();
        
        if (maxIndex <= 0) {
            dotsWrap.style.display = 'none';
            return;
        }
        dotsWrap.style.display = 'flex';
        
        for (let i = 0; i <= maxIndex; i++) {
            const dot = document.createElement('button');
            dot.className = 'services-carousel__dot' + (i === current ? ' is-active' : '');
            dot.setAttribute('aria-label', `Go to slide ${i + 1}`);
            on(dot, 'click', () => {
                current = i;
                slide();
            });
            dotsWrap.appendChild(dot);
        }
    }
    
    function slide() {
        const maxIndex = getMaxIndex();
        if (current > maxIndex) current = maxIndex;
        if (current < 0) current = 0;
        
        const slideWidth = slides[0].getBoundingClientRect().width;
        const gap = parseFloat(window.getComputedStyle(track).gap) || 0;
        
        const translation = -current * (slideWidth + gap);
        track.style.transform = `translateX(${translation}px)`;
        
        // Update dots active class
        const dots = $$('.services-carousel__dot', dotsWrap);
        dots.forEach((dot, idx) => {
            dot.classList.toggle('is-active', idx === current);
        });
        
        // Disable/enable arrows
        if (prevBtn) prevBtn.disabled = (current === 0);
        if (nextBtn) nextBtn.disabled = (current === maxIndex);
    }
    
    // Set up listeners
    if (prevBtn) {
        on(prevBtn, 'click', () => {
            if (current > 0) {
                current--;
                slide();
            }
        });
    }
    
    if (nextBtn) {
        on(nextBtn, 'click', () => {
            const maxIndex = getMaxIndex();
            if (current < maxIndex) {
                current++;
                slide();
            }
        });
    }
    
    // Touch gestures
    let touchStartX = 0;
    let touchStartY = 0;
    
    on(track, 'touchstart', e => {
        touchStartX = e.touches[0].clientX;
        touchStartY = e.touches[0].clientY;
    }, { passive: true });
    
    on(track, 'touchend', e => {
        const diffX = e.changedTouches[0].clientX - touchStartX;
        const diffY = e.changedTouches[0].clientY - touchStartY;
        
        if (Math.abs(diffX) > 40 && Math.abs(diffX) > Math.abs(diffY)) {
            const maxIndex = getMaxIndex();
            if (diffX < 0) {
                if (current < maxIndex) {
                    current++;
                    slide();
                }
            } else {
                if (current > 0) {
                    current--;
                    slide();
                }
            }
        }
    }, { passive: true });
    
    // Handle resize
    let resizeTimer;
    window.addEventListener('resize', () => {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(() => {
            updateDots();
            slide();
        }, 100);
    });
    
    // Initialize
    updateDots();
    slide();
})();

