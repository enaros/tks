// *************************************************************************************
// ComparatorColumnView
// *************************************************************************************
app.ComparatorColumnView = Backbone.View.extend({
    tagName: 'div',
    className: 'col span comparator',
    events: {
        "click .close": 'close'
    },
    initialize: function(params) {
        _.each(params, function(v,k) { this[k] = v }, this); // attach each parameter to the view
        params.model = null; // I need this in order to pass the params through $.param when fetching the model
        this.subViews = new Array();
        app.hotkeys.push(this.hotkey);

        _.bindAll(this, "renderResults");
        this.model.fetch({
            success: this.renderResults,
            data: $.param(params)
        });
    },

    renderResults: function() {
        this.$el.empty().append(this.template({
            header: this.header,
            hotkey: this.hotkey
        }));
        var self = this;
        $('.editor-row .keynames').each(function() {
            self.addOne($(this).attr('id'));
        });
        //this.model.each(this.addOne, this);
        this.trigger('render:finish');
    },

    render: function () {
        return this;
    },

    addOne: function(id) {
        var m = this.model.where({ name:id }); // busco en la coleccion
        var value = '';
        if ($.isArray(m) && m.length == 1)
            value = m[0].get('value');

        var view = new app.TkComparatorItemView({value: value});
        this.subViews.push(view);
        $('#' + id).parent().append(view.render().el);
    },

    close: function(e) {
        app.hotkeys.splice(app.hotkeys.indexOf(this.hotkey), 1); // remove the hotkey from the app
        _.each(this.subViews, function(o) { o.remove() }); // remove subviews created
        this.$el.parent().find('.tooltip').remove(); // remove tooltips
        this.remove();
        this.trigger('remove');
    },

    delegateSubviewsEvents: function() {
        this.delegateEvents();
    },

    removeSubViews: function() {
        _.each(this.subViews, function(o) {
            o.remove();
        }); // delegate events subviews
    }
});

// *************************************************************************************
// TkComparatorItemView
// *************************************************************************************
app.TkComparatorItemView = Backbone.View.extend({
    tagName: 'div',
    className: 'col span comparator',
    initialize: function(params) {
        _.each(params, function(v,k) { this[k] = v }, this); // attach each parameter to the view
    },
    render: function() {
        var template = _.template('<%-value%>');
        this.$el.html(template({value: this.value}));
        return this;
    }
});
