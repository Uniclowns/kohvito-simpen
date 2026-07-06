(function () {
    const tableCode = document.body.dataset.tableCode;

    if (!tableCode) return;

    const storageKey = `kohvito_cart_scope_${tableCode}`;

    function randomScope() {
        const randomPart = Math.random().toString(36).substring(2, 8).toUpperCase();
        return `${tableCode}_${randomPart}`;
    }

    let scope = sessionStorage.getItem(storageKey);

    if (!scope || !scope.startsWith(`${tableCode}_`)) {
        scope = randomScope();
        sessionStorage.setItem(storageKey, scope);
    }

    const currentUrl = new URL(window.location.href);
    if (currentUrl.searchParams.get('s') !== scope) {
        currentUrl.searchParams.set('s', scope);
        window.history.replaceState({}, '', currentUrl.toString());
    }

    function fillScopeTargets(root = document) {
        root.querySelectorAll('input[name="cart_scope"]').forEach((input) => {
            input.value = scope;
        });

        root.querySelectorAll('[data-cart-scope-target]').forEach((element) => {
            element.dataset.cartScope = scope;
        });
    }

    fillScopeTargets();

    document.addEventListener('submit', (event) => {
        fillScopeTargets(event.target instanceof Element ? event.target : document);
    }, true);

    document.addEventListener('click', (event) => {
        const link = event.target.closest('a[data-require-cart-scope]');
        if (!link) return;

        const url = new URL(link.href);
        url.searchParams.set('s', scope);
        link.href = url.toString();
    });

    document.addEventListener('kohvito:cart-scope-refresh', () => fillScopeTargets());

    window.KohvitoCartScope = scope;
})();
