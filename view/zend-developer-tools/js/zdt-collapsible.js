if (!zdt) {
    var zdt = {};
}

/**
 * Create collapse functionality
 * @type {collapsible}
 */
zdt.collapsible = function() {

    /**
     * Initialize links
     */
    this.init = function() {
        var base = this;
        var collapsers = document.getElementsByClassName('zdt-list-collapser');
        for (var i in collapsers) {
            collapsers[i].onclick = function (event) { return base.toggle(event.target) };
        }
    }

    /**
     * Toggle the collapsible objects
     * @param sender
     */
    this.toggle = function (sender) {
        var dom = new zdt.DOM();
        var siblings = dom.getSiblings(sender);
        if (siblings.length == 0) {
            return;
        }

        for (var i in siblings) {
            if (!siblings[i].className.match(/zdt-list-collapsible/)) {
                continue;
            }

            collapsible = siblings[i];
            if (collapsible.style.display == 'none' || !collapsible.style.display) {
                collapsible.style.display = 'block';
            } else {
                collapsible.style.display = 'none';
            }
        }

        return false;
    }



    // Initialize this functionality
    this.init();
}();

/**
 * Add DOM functionalty
 * @type {DOM}
 */
zdt.DOM = function() {

    /**
     *
     * @param n Element you want to inspect
     * @param skipMe Element that you want to skip
     * @return {Array}
     */
    this.getChildren = function(n, skipMe){
        var r = [];
        var elem = null;
        for ( ; n; n = n.nextSibling ) {
            if ( n.nodeType == 1 && n != skipMe) {
                r.push( n );
            }
        }
        return r;
    };

    /**
     *
     * @param n Element you want to inspect
     * @return {Array}
     */
    this.getSiblings = function (n) {
        return this.getChildren(n.parentNode.firstChild, n);
    }
}
