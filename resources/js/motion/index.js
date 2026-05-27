import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import Typed from 'typed.js';

gsap.registerPlugin(ScrollTrigger);

const reduceMotionQuery = window.matchMedia('(prefers-reduced-motion: reduce)');

const motion = {
    fast: 0.15,
    base: 0.3,
    emphasized: 0.42,
    slow: 0.6,
    standard: 'power3.out',
    overshoot: 'back.out(1.4)',
    exit: 'power2.in',
    stagger: 0.07,
};

/**
 * Returns whether the user requested reduced motion at OS/browser level.
 *
 * @returns {boolean}
 */
function prefersReducedMotion() {
    return reduceMotionQuery.matches;
}

/**
 * Clears initial animation styles so reduced-motion users never receive hidden content.
 *
 * @param {Iterable<Element>} elements
 * @returns {void}
 */
function showImmediately(elements) {
    gsap.set(Array.from(elements), { clearProps: 'all', opacity: 1, y: 0, scale: 1 });
}

/**
 * Initializes declarative GSAP entrance animations.
 *
 * DOM contract:
 * - data-anim="fade-up" animates one element on load.
 * - data-anim="scroll-reveal" animates one element when it reaches the viewport.
 * - data-anim="stagger" animates child elements marked with data-anim-item.
 * - data-anim="pop" applies a small overshoot pop on load.
 *
 * @returns {void}
 */
function initDeclarativeAnimations() {
    const animatedElements = document.querySelectorAll('[data-anim]');

    if (prefersReducedMotion()) {
        showImmediately(animatedElements);
        showImmediately(document.querySelectorAll('[data-anim-item]'));
        return;
    }

    document.querySelectorAll('[data-anim="fade-up"]').forEach((element) => {
        gsap.from(element, {
            y: 16,
            opacity: 0,
            duration: motion.emphasized,
            ease: motion.standard,
            clearProps: 'transform,opacity',
        });
    });

    document.querySelectorAll('[data-anim="scroll-reveal"]').forEach((element) => {
        gsap.from(element, {
            y: 16,
            opacity: 0,
            duration: motion.emphasized,
            ease: motion.standard,
            clearProps: 'transform,opacity',
            scrollTrigger: {
                trigger: element,
                start: 'top 85%',
                once: true,
            },
        });
    });

    document.querySelectorAll('[data-anim="stagger"]').forEach((group) => {
        const items = Array.from(group.querySelectorAll('[data-anim-item]'));
        if (!items.length) return;

        gsap.from(items.slice(0, 15), {
            y: 16,
            opacity: 0,
            duration: motion.emphasized,
            ease: motion.standard,
            stagger: motion.stagger,
            clearProps: 'transform,opacity',
        });
    });

    document.querySelectorAll('[data-anim="pop"]').forEach((element) => {
        gsap.from(element, {
            scale: 0.4,
            opacity: 0,
            duration: motion.base,
            ease: motion.overshoot,
            clearProps: 'transform,opacity',
        });
    });
}

/**
 * Animates KPI numbers from zero to their final numeric value.
 *
 * DOM contract: <span data-count-up="125000">0</span>.
 *
 * @returns {void}
 */
function initCountUp() {
    document.querySelectorAll('[data-count-up]').forEach((element) => {
        const target = Number.parseInt(element.getAttribute('data-count-up') || '0', 10);
        if (!Number.isFinite(target)) return;

        if (prefersReducedMotion()) {
            element.textContent = target.toLocaleString('id-ID');
            return;
        }

        const state = { value: 0 };
        gsap.to(state, {
            value: target,
            duration: motion.slow,
            ease: motion.standard,
            onUpdate: () => {
                element.textContent = Math.round(state.value).toLocaleString('id-ID');
            },
        });
    });
}

/**
 * Animates progress bars to their declared final percentage.
 *
 * DOM contract: <div data-progress-fill="60"></div>.
 *
 * @returns {void}
 */
function initProgressFill() {
    document.querySelectorAll('[data-progress-fill]').forEach((element) => {
        const target = Math.max(0, Math.min(100, Number.parseFloat(element.getAttribute('data-progress-fill') || '0')));
        element.style.width = `${target}%`;

        if (prefersReducedMotion()) return;

        gsap.fromTo(
            element,
            { width: '0%' },
            { width: `${target}%`, duration: motion.slow, ease: motion.standard },
        );
    });
}

/**
 * Applies the consumer splash typewriter effect.
 *
 * DOM contract: <span data-typed="Selamat Datang!"></span>.
 *
 * @returns {void}
 */
function initTypedText() {
    document.querySelectorAll('[data-typed]').forEach((element) => {
        const text = element.getAttribute('data-typed') || element.textContent || '';

        if (prefersReducedMotion()) {
            element.textContent = text;
            return;
        }

        element.textContent = '';
        new Typed(element, {
            strings: [text],
            typeSpeed: 42,
            showCursor: false,
            startDelay: Number.parseInt(element.getAttribute('data-typed-delay') || '0', 10),
        });
    });
}

/**
 * Lazy-loads Lottie animations only when a view declares a JSON animation.
 *
 * DOM contract:
 * - data-lottie="/lottie/payment-success.json"
 * - data-lottie-loop="true|false"
 * - optional sibling/fallback marked with data-lottie-fallback.
 *
 * @returns {Promise<void>}
 */
async function initLottie() {
    const lottieTargets = Array.from(document.querySelectorAll('[data-lottie]'));
    if (!lottieTargets.length) return;

    const { default: lottie } = await import('lottie-web');

    lottieTargets.forEach((container) => {
        const path = container.getAttribute('data-lottie');
        if (!path) return;

        const fallback = container.parentElement?.querySelector('[data-lottie-fallback]');
        const animation = lottie.loadAnimation({
            container,
            renderer: 'svg',
            loop: container.getAttribute('data-lottie-loop') === 'true',
            autoplay: !prefersReducedMotion(),
            path,
        });

        animation.addEventListener('DOMLoaded', () => {
            container.classList.remove('hidden');
            fallback?.classList.add('hidden');
            if (prefersReducedMotion()) animation.goToAndStop(animation.totalFrames, true);
        });

        animation.addEventListener('data_failed', () => {
            container.classList.add('hidden');
            fallback?.classList.remove('hidden');
        });
    });
}

/**
 * Bootstraps the app-wide motion layer after Blade has rendered the DOM.
 *
 * @returns {void}
 */
export function initMotion() {
    initDeclarativeAnimations();
    initCountUp();
    initProgressFill();
    initTypedText();
    initLottie().catch(() => {});
}

document.addEventListener('DOMContentLoaded', initMotion);
