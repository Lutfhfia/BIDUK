{{-- BIDUK page transition loader --}}
<style>
    #biduk-page-loader {
        position: fixed;
        inset: 0;
        z-index: 20000;
        display: grid;
        place-items: center;
        background: rgba(242, 255, 248, .76);
        -webkit-backdrop-filter: blur(7px);
        backdrop-filter: blur(7px);
        opacity: 0;
        visibility: hidden;
        pointer-events: none;
        transition: opacity .2s ease, visibility .2s ease;
    }

    #biduk-page-loader.is-active {
        opacity: 1;
        visibility: visible;
        pointer-events: all;
    }

    #biduk-page-loader [hidden] {
        display: none !important;
    }

    #biduk-page-loader .biduk-loader-content {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 11px;
        padding: 20px;
        text-align: center;
    }

    /* Area logo dan bayangan */
    #biduk-page-loader .biduk-loader-visual {
        position: relative;
        display: grid;
        place-items: center;
        width: 132px;
        height: 126px;
        margin-bottom: 3px;
    }

    /* Logo resmi dari Profil Sekolah */
    #biduk-page-loader .biduk-school-logo {
        position: absolute;
        top: 2px;
        left: 50%;
        z-index: 1;
        display: block;
        width: 108px;
        height: 108px;
        object-fit: contain;
        transform: translateX(-50%);
        transform-origin: center bottom;
        animation: biduk-logo-jump 1s ease-in-out infinite;
    }

    /* Bayangan mengikuti lompatan logo */
    #biduk-page-loader .biduk-loader-shadow {
        position: absolute;
        bottom: 5px;
        left: 50%;
        width: 66px;
        height: 10px;
        border-radius: 50%;
        background: rgba(20, 103, 66, .23);
        filter: blur(4px);
        transform: translateX(-50%);
        animation: biduk-logo-shadow 1s ease-in-out infinite;
    }

    /* Ikon buku sebagai fallback jika logo tidak ada */
    #biduk-page-loader .biduk-loader-fallback {
        position: absolute;
        top: 18px;
        left: 50%;
        width: 104px;
        height: 82px;
        overflow: visible;
        transform: translateX(-50%);
        transform-origin: center bottom;
        animation: biduk-logo-jump 1s ease-in-out infinite;
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

    /* Nama sekolah */
    #biduk-page-loader .biduk-loader-school {
        margin: 0;
        color: #075e42;
        font-family: inherit;
        font-size: 13px;
        font-weight: 800;
        letter-spacing: 2px;
        line-height: 1.5;
        text-transform: uppercase;
    }

    /* Keterangan loading */
    #biduk-page-loader .biduk-loader-label {
        margin: 0;
        color: #527568;
        font-family: inherit;
        font-size: 12px;
        font-weight: 500;
        letter-spacing: .5px;
    }

    #biduk-page-loader .biduk-loader-dots::after {
        content: '';
        display: inline-block;
        width: 1.4em;
        text-align: left;
        animation: biduk-dots 1.1s steps(4, end) infinite;
    }

    @keyframes biduk-logo-jump {
        0%, 100% {
            transform: translate(-50%, 0) scale(1);
        }
        45% {
            transform: translate(-50%, -15px) scale(1.025);
        }
        60% {
            transform: translate(-50%, -13px) scale(1.02);
        }
    }

    @keyframes biduk-logo-shadow {
        0%, 100% {
            transform: translateX(-50%) scaleX(1);
            opacity: .85;
        }
        45%, 60% {
            transform: translateX(-50%) scaleX(.68);
            opacity: .45;
        }
    }

    @keyframes biduk-page-left {
        0%, 100% {
            transform: perspective(80px) rotateY(0deg) scaleX(1);
        }
        45%, 60% {
            transform: perspective(80px) rotateY(-22deg) scaleX(.93);
        }
    }

    @keyframes biduk-page-right {
        0%, 100% {
            transform: perspective(80px) rotateY(0deg) scaleX(1);
        }
        45%, 60% {
            transform: perspective(80px) rotateY(22deg) scaleX(.93);
        }
    }

    @keyframes biduk-rays {
        0%, 25%, 100% {
            opacity: .35;
            transform: translateY(3px) scale(.92);
        }
        45%, 70% {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }

    @keyframes biduk-dots {
        0% { content: ''; }
        25% { content: '.'; }
        50% { content: '..'; }
        75%, 100% { content: '...'; }
    }

    @media (max-width: 480px) {
        #biduk-page-loader .biduk-loader-visual {
            transform: scale(.92);
            margin-bottom: 0;
        }

        #biduk-page-loader .biduk-loader-school {
            font-size: 12px;
            letter-spacing: 1.5px;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        #biduk-page-loader,
        #biduk-page-loader * {
            animation: none !important;
            transition: none !important;
        }
    }
</style>

<div id="biduk-page-loader" aria-hidden="true">
    <div class="biduk-loader-content" role="status" aria-live="polite">

        <div class="biduk-loader-visual" aria-hidden="true">

            <span class="biduk-loader-shadow"></span>

            @if ($loaderSchoolProfile?->logo)
                <img
                    class="biduk-school-logo"
                    src="{{ asset('storage/' . $loaderSchoolProfile->logo) }}"
                    alt="Logo SDN 204 Palembang"
                    decoding="async"
                >
            @endif

            <svg
                class="biduk-loader-fallback"
                viewBox="0 0 110 86"
                xmlns="http://www.w3.org/2000/svg"
                @if ($loaderSchoolProfile?->logo) hidden @endif
            >
                <g class="book-rays book-line">
                    <path d="M54 22 L54 8" />
                    <path d="M31 31 L18 23" />
                    <path d="M77 31 L90 23" />
                    <path d="M26 44 L11 44" />
                    <path d="M82 44 L97 44" />
                </g>

                <path
                    class="book-line"
                    d="M12 51 C25 46 39 47 53 55 L53 67 C39 59 25 58 12 63 Z"
                />

                <path
                    class="book-line"
                    d="M98 51 C85 46 71 47 57 55 L57 67 C71 59 85 58 98 63 Z"
                />

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
        </div>

        <p class="biduk-loader-school">SDN 204 Palembang</p>

        <p class="biduk-loader-label">
            Memuat halaman<span class="biduk-loader-dots" aria-hidden="true"></span>
        </p>

        <span class="visually-hidden">
            Memuat halaman, harap tunggu.
        </span>
    </div>
</div>

<script>
(function () {
    const loader = document.getElementById('biduk-page-loader');

    if (!loader || loader.dataset.initialized === 'true') {
        return;
    }

    loader.dataset.initialized = 'true';

    const logoImage = loader.querySelector('.biduk-school-logo');
    const fallbackIcon = loader.querySelector('.biduk-loader-fallback');

    /*
     * Tampilkan ikon buku jika file logo sekolah
     * belum tersedia atau gagal dimuat.
     */
    if (logoImage && fallbackIcon) {
        const showFallback = function () {
            logoImage.hidden = true;
            fallbackIcon.hidden = false;
        };

        logoImage.addEventListener('error', showFallback);

        if (logoImage.complete && logoImage.naturalWidth === 0) {
            showFallback();
        }
    }

    const showLoader = function () {
        loader.classList.add('is-active');
        loader.setAttribute('aria-hidden', 'false');
    };

    const hideLoader = function () {
        loader.classList.remove('is-active');
        loader.setAttribute('aria-hidden', 'true');
    };

    /*
     * Loading saat membuka tautan internal.
     */
    document.addEventListener('click', function (event) {
        if (
            event.button !== 0 ||
            event.metaKey ||
            event.ctrlKey ||
            event.shiftKey ||
            event.altKey
        ) {
            return;
        }

        const target = event.target;

        if (!(target instanceof Element)) {
            return;
        }

        const link = target.closest('a[href]');

        if (
            !link ||
            link.hasAttribute('download') ||
            link.dataset.noPageLoader === 'true'
        ) {
            return;
        }

        if (link.target && link.target.toLowerCase() !== '_self') {
            return;
        }

        if (
            link.getAttribute('aria-disabled') === 'true' ||
            link.classList.contains('disabled')
        ) {
            return;
        }

        const href = link.getAttribute('href');

        if (
            !href ||
            href === '#' ||
            href.startsWith('#') ||
            href.startsWith('javascript:') ||
            href.startsWith('mailto:') ||
            href.startsWith('tel:')
        ) {
            return;
        }

        let destination;

        try {
            destination = new URL(link.href, window.location.href);
        } catch (_) {
            return;
        }

        if (destination.origin !== window.location.origin) {
            return;
        }

        if (destination.href === window.location.href) {
            return;
        }

        showLoader();
    }, true);

    /*
     * Loading saat formulir dikirim.
     */
    document.addEventListener('submit', function (event) {
        const form = event.target;

        if (
            !(form instanceof HTMLFormElement) ||
            form.dataset.noPageLoader === 'true'
        ) {
            return;
        }

        if (!event.defaultPrevented && form.checkValidity()) {
            showLoader();
        }
    });

    /*
     * Sembunyikan kembali loading ketika halaman siap
     * atau saat pengguna kembali ke halaman sebelumnya.
     */
    window.addEventListener('beforeunload', showLoader);
    window.addEventListener('pageshow', hideLoader);
    window.addEventListener('load', hideLoader);
})();
</script>
