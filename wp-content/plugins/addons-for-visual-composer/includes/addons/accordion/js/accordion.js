jQuery(function ($) {

    // Don't do any of this if there are no accordions present here
    if ($('.lvca-accordion').length) {

        $('.lvca-accordion').each(function () {

            var accordion = $(this);

            new LVCA_Accordion(accordion);

        });
    }

});

var LVCA_Accordion = function (accordion) {

    // toggle elems
    this.panels = accordion.find('.lvca-panel');

    this.toggle = false;

    this.expanded = false;

    if (accordion.data('toggle') == true)
        this.toggle = true;

    if (accordion.data('expanded') == true)
        this.expanded = true;

    this.current = null;

    // init events
    this.initEvents();
};

LVCA_Accordion.prototype.show = function (panel) {

    if (this.toggle) {
        if (panel.hasClass('lvca-active')) {
            this.close(panel);
        }
        else {
            this.open(panel);
        }
    }
    else {
        // if the panel is already open, close it else open it after closing existing one
        if (panel.hasClass('lvca-active')) {
            this.close(panel);
            this.current = null;
        }
        else {
            this.close(this.current);
            this.open(panel);
            this.current = panel;
        }
    }

};

LVCA_Accordion.prototype.close = function (panel) {

    if (panel !== null) {
        panel.children('.lvca-panel-content').slideUp(300);
        panel.removeClass('lvca-active');
    }

};

LVCA_Accordion.prototype.open = function (panel) {

    if (panel !== null) {
        panel.children('.lvca-panel-content').slideDown(300);
        panel.addClass('lvca-active');
    }

};


LVCA_Accordion.prototype.initEvents = function () {

    var self = this;

    if (this.expanded && this.toggle) {

        // Display all panels
        this.panels.each(function () {

            var panel = jQuery(this);

            self.show(panel);

        });
    }

    this.panels.find('.lvca-panel-title').click(function (event) {

        event.preventDefault();

        var $panel = jQuery(this).parent();

        // Do not disturb existing location URL if you are going to close an accordion panel that is currently open
        if (!$panel.hasClass('lvca-active')) {

            var target = $panel.attr("id");

            history.pushState ? history.pushState(null, null, "#" + target) : window.location.hash = "#" + target;

        }
        else {
            var target = $panel.attr("id");

            if (window.location.hash == '#' + target)
                history.pushState ? history.pushState(null, null, '#') : window.location.hash = "#";
        }

        self.show($panel);

    });
};

