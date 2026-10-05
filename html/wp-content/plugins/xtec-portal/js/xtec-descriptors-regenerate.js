// Goes to the next page of descriptors to regenerate
(function () {
    const n = (parseInt(new URLSearchParams(window.location.search).get('n'), 10) || 0) + 5;

    setTimeout(function () {
        window.location.href = '?page=ms-descriptor&action=descriptors&n=' + n +
            '&_wpnonce=' + encodeURIComponent(xtecDescriptorsRegenerateNonce);
    }, 2500);
})();
