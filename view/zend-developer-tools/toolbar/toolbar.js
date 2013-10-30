(function() {

    /**
     * @param {Cookie} cookie
     * @returns {Toolbar}
     * @constructor
     */
    var Toolbar = function(cookie) {
        /** @type {Toolbar} */
        var self = this;
        /** @type {HTMLElement} */
        var container = document.getElementById("zend-developer-toolbar");
        /** @type {number} */
        var width = container.offsetWidth;
        /** @type {HTMLElement} */
        var toggleTrigger = document.getElementById("zdf-toolbar-toggle");
        /** @type {boolean} */
        var hidden;
        var cookieKeyHidden = "zdt-hidden";

        self.toggle = function() {
            !self.isHidden() ? self.hide() : self.show();
        };

        /**
         * @returns {boolean}
         * @throws {Error}
         */
        self.isHidden = function() {
            if (typeof(hidden) == "undefined") {
                throw new Error("Field 'hidden' didn't initialize.");
            }

            return hidden;
        };

        self.hide = function() {
            slide((25 - width));

            toggleTrigger.innerHTML = "►";
            toggleTrigger.setAttribute("title", "Show Toolbar");
            hidden = true;

            cookie.set(cookieKeyHidden, 1);
        };

        self.show = function() {
            slide(0);

            toggleTrigger.innerHTML = "◄";
            toggleTrigger.setAttribute("title", "Hide Toolbar");
            hidden = false;

            cookie.set(cookieKeyHidden, 0);
        };

        init();

        function init() {
            (cookie.get(cookieKeyHidden) == 1)
                ? self.hide()
                : self.show();

            initEvents();
        }

        function initEvents() {
            toggleTrigger.addEventListener("click", self.toggle);
        }

        /**
         * @param {number} toPosition
         */
        function slide(toPosition) {
            var increment = 30;

            var currentPosition = (container.style.left.length > 0)
                ? parseInt(container.style.left)
                : 0;

            if (currentPosition == toPosition) {
                return;
            }

            var moveLeft = (toPosition < currentPosition);
            var newPosition = toPosition;

            if (moveLeft) {
                var leftStep = currentPosition - increment;

                if (leftStep > toPosition) {
                    newPosition = leftStep;
                }
            } else {
                var rightStep = currentPosition + increment;

                if (rightStep < toPosition) {
                    newPosition = rightStep;
                }
            }

            container.style.left = newPosition + "px";

            setTimeout(function() { slide(toPosition); }, 3);
        }

        return self;
    };

    /**
     * @returns {Cookie}
     * @constructor
     */
    var Cookie = function() {
        /** @type {Cookie} */
        var self = this;

        /**
         * @param {string} key
         * @returns {string|null}
         */
        self.get = function(key) {
            var cookie = document.cookie;

            if (cookie.indexOf(key + "=") == -1) {
                return null;
            }

            var regexp = new RegExp(key + "\=([^;]+)");

            return regexp.exec(cookie)[1];
        };

        /**
         * @param {string} key
         * @param {string} value
         */
        self.set = function(key, value) {
            document.cookie = key + "=" + value;
        };

        return self;
    };

    window.ZDT = new Toolbar(new Cookie());

})();