var app = {
    views: {},
    models: {},
    hotkeys: new Array(),

    loadTemplates: function(views, callback) {
        var deferreds = [];
        $.each(views, function(index, view) {
            if (app[view]) {
                deferreds.push($.get('bundles/tks/tpl/' + view + '.html', function(data) {
                    app[view].prototype.template = _.template(data);
                }, 'html'));
            } else {
                console.error(view + " not found");
            }
        });
        $.when.apply(null, deferreds).done(callback);
    }
};

app.Router = Backbone.Router.extend({
    routes: {
        "": "home",
        "stats": "stats"
    },

    initialize: function () {
        app.shellView = new app.ShellView({el:$('body')});
        app.shellView.render();
        this.$content = $("#content");
    },

    home: function () {
        // Since the home view never changes, we instantiate it and render it only once
        if (!app.editorView) {
            app.editorView = new app.EditorView();
            app.editorView.render();
        } else {
            console.log('reusing home view');
            app.editorView.delegateEvents(); // delegate events when the view is recycled
        }
        this.$content.html(app.editorView.el);
        app.shellView.selectMenuItem('home-menu');
    },

    stats: function () {
        if (!app.statsView) {
            app.statsView = new app.StatsView();
            app.statsView.render();
        }
        this.$content.html(app.statsView.el);
        app.shellView.selectMenuItem('stats-menu');
    }
});

$(document).on("ready", function () {
    app.loadTemplates([
        "EditorView",
        "SelectList",
        "TkItemView",
        "Editor",
        "Comparator",
        "StatsView",
        "ShellView"
    ],
    function () {
        app.router = new app.Router();
        Backbone.history.start();
    });
});