var app = {
    views: {},
    models: {},
    hotkeys: new Array(),

    loadTemplates: function(views, callback) {
        var deferreds = [];
        $.each(views, function(index, view) {
            if (app[view]) {
                deferreds.push($.get('/bundles/tks/tpl/' + view + '.html', function(data) {
                    app[view].prototype.template = _.template(data);
                }, 'html'));
            } else {
                console.error(view + " not found");
            }
        });
        $.when.apply(null, deferreds).done(callback);
    },

    loadEditor: function(search) {
        window.location.hash = 'editor';
        if (search)
            setTimeout(function() { app.editorView.performSearch(search); }, 500);
    }
};

app.Router = Backbone.Router.extend({
    routes: {
        "": "editor",
        "editor": "editor",
        "stats": "stats",
        "bulk": "bulk",
        "bulk-create": "bulk-create"
    },

    initialize: function () {
        app.shellView = new app.ShellView({el:$('#shell')});
        app.shellView.render();
        this.$content = $("#content");
//        console.clear();
    },

    editor: function () {
        // Since the home view never changes, we instantiate it and render it only once
        if (!app.editorView) {
            app.editorView = new app.EditorView();
            app.editorView.render();
        } else {
            app.editorView.delegateSubviewsEvents(); // delegate events when the view is recycled
        }
        $('#content').html(app.editorView.el);
        if ($('#editor').is(':empty')) $('#filter').focus();
        app.shellView.selectMenuItem('editor-menu');
    },

    stats: function () {
        if (app.statsView) {
            app.statsView.remove();
        }
        app.statsView = new app.StatsView();
        app.statsView.render();

        this.$content.html(app.statsView.el);
        app.shellView.selectMenuItem('stats-menu');
    },

    bulk: function() {
        console.log('bulk');
        if (!app.bulkView) {
            app.bulkView = new app.BulkView();
            app.bulkView.render();
        } else {
            app.bulkView.delegateEvents()
        }
        this.$content.html(app.bulkView.el);
        app.shellView.selectMenuItem('bulk-menu');
    },

    'bulk-create': function() {
        console.log('bulk-create');
        if (!app.bulkCreateView) {
            app.bulkCreateView = new app.BulkCreateView();
            app.bulkCreateView.render();
        } else {
            app.bulkCreateView.delegateEvents()
        }
        this.$content.html(app.bulkCreateView.el);
        app.shellView.selectMenuItem('bulk-create-menu');
    }
});

$(document).on("ready", function () {
    app.loadTemplates([
        "EditorView",
        "SelectList",
        "TkItemView",
        "EditorColumnView",
        "ComparatorColumnView",
        "StatsView",
        "BulkView",
        "BulkCreateView",
        "ShellView"
    ],
    function () {
        app.router = new app.Router();
        Backbone.history.start();
    });
});