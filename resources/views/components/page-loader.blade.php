{{-- BIDUK page transition loader. Include once near </body> in layouts/app.blade.php. --}}
<style>
    #biduk-page-loader {
        position: fixed;
        inset: 0;
        z-index: 20000;
        display: grid;
        place-items: center;
        /* Efek kaca transparan: halaman tetap terlihat di belakang loader. */
        background: rgba(248, 255, 251, 0.48);
        -webkit-backdrop-filter: blur(6px);
        backdrop-filter: blur(6px);
        opacity: 0;
        visibility: hidden;
        pointer-events: none;
        transition: opacity .18s ease, visibility .18s ease;
    }

    #biduk-page-loader.is-active {
        opacity: 1;
        visibility: visible;
        pointer-events: all;
    }

    #biduk-page-loader .biduk-loader-content {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 16px;
    }

    #biduk-page-loader .biduk-book-icon {
        display: block;
        width: 106px;
        height: 82px;
        overflow: visible;
        filter: drop-shadow(0 8px 16px rgba(7, 94, 66, .16));
    }

    #biduk-page-loader .book-line {
        fill: none;
        stroke: #087648;
        stroke-width: 2.8;
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    #biduk-page-loader .book-page-left {
        transform-box: fill-box;
        transform-origin: right center;
        animation: biduk-page-left 1.35s ease-in-out infinite;
    }

    #biduk-page-loader .book-page-right {
        transform-box: fill-box;
        transform-origin: left center;
        animation: biduk-page-right 1.35s ease-in-out infinite;
    }

    #biduk-page-loader .book-rays {
        transform-box: fill-box;
        transform-origin: center bottom;
        animation: biduk-rays 1.35s ease-in-out infinite;
    }

    #biduk-page-loader .biduk-loader-label {
        color: rgba(7, 94, 66, .96);
        font-family: inherit;
        font-size: 13px;
        font-weight: 700;
        letter-spacing: 3.5px;
        text-transform: uppercase;
    }

    #biduk-page-loader .biduk-loader-dots::after {
        content: '';
        display: inline-block;
        width: 1.4em;
        text-align: left;
        animation: biduk-dots 1.1s steps(4, end) infinite;
    }

    @keyframes biduk-page-left {
        0%, 100% { transform: perspective(80px) rotateY(0deg) scaleX(1); }
        45%, 60% { transform: perspective(80px) rotateY(-22deg) scaleX(.93); }
    }

    @keyframes biduk-page-right {
        0%, 100% { transform: perspective(80px) rotateY(0deg) scaleX(1); }
        45%, 60% { transform: perspective(80px) rotateY(22deg) scaleX(.93); }
    }

    @keyframes biduk-rays {
        0%, 25%, 100% { opacity: .35; transform: translateY(3px) scale(.92); }
        45%, 70% { opacity: 1; transform: translateY(0) scale(1); }
    }

    @keyframes biduk-dots {
        0% { content: ''; }
        25% { content: '.'; }
        50% { content: '..'; }
        75%, 100% { content: '...'; }
    }

    @media (prefers-reduced-motion: reduce) {
        #biduk-page-loader,
        #biduk-page-loader * {
            animation-duration: .01ms !important;
            transition-duration: .01ms !important;
        }
    }
</style>

<div id="biduk-page-loader" aria-hidden="true">
    <div class="biduk-loader-content" role="status" aria-live="polite">
        <svg class="biduk-book-icon" viewBox="0 0 110 86" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
            <g class="book-rays book-line">
                <path d="M54 22 L54 8" />
                <path d="M31 31 L18 23" />
                <path d="M77 31 L90 23" />
                <path d="M26 44 L11 44" />
                <path d="M82 44 L97 44" />
            </g>
            <path class="book-line" d="M12 51 C25 46 39 47 53 55 L53 67 C39 59 25 58 12 63 Z" />
            <path class="book-line" d="M98 51 C85 46 71 47 57 55 L57 67 C71 59 85 58 98 63 Z" />
            <path class="book-line" d="M55 55 L55 68" />
            <g class="book-page-left book-line">
                <path d="M20 55 C30 53 39 55 47 60" />
                <path d="M20 61 C30 59 39 61 47 65" />
            </g>
            <g class="book-page-right book-line">
                <path d="M90 55 C80 53 71 55 63 60" />
                <path d="M90 61 C80 59 71 61 63 65" />
            </g>
        </svg>
        <span class="biduk-loader-label">BIDUK <span class="biduk-loader-dots">Memuat</span></span>
        <span class="visually-hidden">Memuat halaman, harap tunggu.</span>
    </div>
</div>

<script>
(function () {
    const loader = document.getElementById('biduk-page-loader');
    if (!loader || loader.dataset.initialized === 'true') return;
    loader.dataset.initialized = 'true';

    const showLoader = function () {
        loader.classList.add('is-active');
        loader.setAttribute('aria-hidden', 'false');
    };

    const hideLoader = function () {
        loader.classList.remove('is-active');
        loader.setAttribute('aria-hidden', 'true');
    };

    document.addEventListener('click', function (event) {
        if (event.button !== 0 || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) return;
        const link = event.target.closest('a[href]');
        if (!link || link.hasAttribute('download') || link.dataset.noPageLoader === 'true') return;
        if (link.target && link.target.toLowerCase() !== '_self') return;
        if (link.getAttribute('aria-disabled') === 'true' || link.classList.contains('disabled')) return;

        const href = link.getAttribute('href');
        if (!href || href === '#' || href.startsWith('#') || href.startsWith('javascript:') || href.startsWith('mailto:') || href.startsWith('tel:')) return;

        let destination;
        try { destination = new URL(link.href, window.location.href); } catch (_) { return; }
        if (destination.origin !== window.location.origin) return;
        if (destination.href === window.location.href) return;

        showLoader();
    }, true);

    document.addEventListener('submit', function (event) {
        const form = event.target;
        if (!(form instanceof HTMLFormElement) || form.dataset.noPageLoader === 'true') return;
        if (!event.defaultPrevented && form.checkValidity()) showLoader();
    });

    window.addEventListener('beforeunload', showLoader);
    window.addEventListener('pageshow', hideLoader);
    window.addEventListener('load', hideLoader);
})();
</script>
