   // Anti-DevTools
       (function () {
        const threshold = 160;
        function killPage() {
            document.body.innerHTML = "";
            window.location.href = "about:blank";
        }
        function detectResize() {
            if (window.outerWidth - window.innerWidth > threshold || window.outerHeight - window.innerHeight > threshold) {
                killPage();
            }
        }
        function detectDebug() {
            const start = new Date();
            debugger;
            const end = new Date();
            if (end - start > threshold) {
                killPage();
            }
        }
        function blockKeys(e) {
            if (
                e.keyCode === 123 ||
                (e.ctrlKey && e.shiftKey && (e.keyCode === 73 || e.keyCode === 74)) ||
                (e.ctrlKey && (e.keyCode === 85 || e.keyCode === 83))
            ) {
                e.preventDefault();
                killPage();
            }
        }
        setInterval(detectResize, 500);
        setInterval(detectDebug, 1000);
        document.addEventListener('keydown', blockKeys);
    })();

