(function () {
    // Check if the browser supports "prefetch"
    var link = document.createElement("link");
    var supportsPrefetch = link.relList && link.relList.supports && link.relList.supports("prefetch");

    // Exit if the browser does not support prefetching
    if (!supportsPrefetch) return;

    // Check for slow connections or data-saving mode
    var connection = navigator.connection || {};
    var slowConnection = connection.saveData || /2g/.test(connection.effectiveType);

    // Exit if the user is on a slow connection
    if (slowConnection) return;

    var hoveredLinks = new Set(); // To prevent duplicate prefetches
    var timeoutId; // Used to delay prefetching

    // Prefetch function
    function prefetch(url) {
        if (hoveredLinks.has(url) || url.includes("?") || url === window.location.href) return;

        var prefetchLink = document.createElement("link");
        prefetchLink.rel = "prefetch";
        prefetchLink.href = url;
        document.head.appendChild(prefetchLink);

        hoveredLinks.add(url);
    }

    // Handle mouse hover event
    function onMouseOver(event) {
        var link = event.target.closest("a");
        if (link && link.href) {
            timeoutId = setTimeout(function () {
                prefetch(link.href);
            }, 50); // Delay of 50ms
        }
    }

    // Handle touchstart event (for mobile users)
    function onTouchStart(event) {
        var link = event.target.closest("a");
        if (link && link.href) {
            prefetch(link.href);
        }
    }

    // Cancel prefetch if user moves away quickly
    function onMouseOut(event) {
        var link = event.target.closest("a");
        if (link && link.href) {
            clearTimeout(timeoutId);
        }
    }

    // Attach event listeners
    document.addEventListener("mouseover", onMouseOver, { capture: true, passive: true });
    document.addEventListener("mouseout", onMouseOut, { capture: true, passive: true });
    document.addEventListener("touchstart", onTouchStart, { capture: true, passive: true });
})();
