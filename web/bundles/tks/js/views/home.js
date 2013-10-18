// *************************************************************************************
app.SelectList = Backbone.View.extend({
    tagName: 'select',
    initialize: function() {
        this.model.fetch({reset: true});
        this.listenTo(this.model, 'reset', this.render);
    },

    render: function () {
        this.$el.html(this.template({
            o: this.model.toJSON(),
            placeholder: this.options.placeholder
        }));
        return this;
    }
});
// *************************************************************************************
// Editor
// *************************************************************************************
app.Editor = Backbone.View.extend({
    className: 'editor-rows',
    initialize: function(params) {
        _.each(params, function(v,k) { this[k] = v }, this); // attach each parameter to the view
        params.model = null; // I need this in order to pass the params through $.param when fetching the model
        _.bindAll(this, "render");
        this.model.fetch({
            success: this.render,
            data: $.param(params)
        });
    },

    render: function () {
        this.$el.empty();
        if (this.model.length) { // to make sure
            $('#editor').prepend(this.template({
                header: this.header
            }));
            this.model.each(this.addOne, this);
        }
        $($('#editor input').get(0)).focus();
        return this;
    },

    addOne: function(tk) {
        var view = new app.TkItemView({
            model: tk,
            language: this.language,
            deployment: this.deployment
        });
        this.$el.append(view.render().el);
    }
});
// *************************************************************************************
// Comparator
// *************************************************************************************
app.Comparator = Backbone.View.extend({
    tagName: 'div',
    className: 'span3',
    events: {
        "click .close": 'close'
    },
    initialize: function(params) {
        _.each(params, function(v,k) { this[k] = v }, this); // attach each parameter to the view
        params.model = null; // I need this in order to pass the params through $.param when fetching the model
        this.subViews = new Array();
        app.hotkeys.push(this.hotkey);

        _.bindAll(this, "render");
        this.model.fetch({
            success: this.render,
            data: $.param(params)
        });
    },

    render: function () {
        this.$el.empty().append(this.template({
            header: this.header,
            hotkey: this.hotkey
        }));
        this.model.each(this.addOne, this);
        return this;
    },

    addOne: function(tk) {
        var view = new app.TkComparatorItemView({model:tk});
        this.subViews.push(view);
        $('#' + tk.attributes.name).parent().append(view.render().el);
    },

    close: function(e) {
        app.hotkeys.splice(app.hotkeys.indexOf(this.hotkey), 1); // remove the hotkey from the app
        _.each(this.subViews, function(o) { o.remove() }); // remove subviews created
        this.remove();
    }
});
// *************************************************************************************
// TkComparatorItemView
// *************************************************************************************
app.TkComparatorItemView = Backbone.View.extend({
    tagName: 'div',
    className: 'span3 comparator',
    render: function() {
        var template = _.template('<%-value%>');
        this.$el.html(template({value: this.model.get('value')}));
        return this;
    }
});
// *************************************************************************************
// TkItemView
// *************************************************************************************
app.TkItemView = Backbone.View.extend({
    tagName: 'div',
    className: 'editor-row row-fluid',
    events: {
        // "click .editable": "edit"
        "blur textarea": 'blur',
        "focus textarea": 'focus',
        "keydown textarea": 'copy'
    },
    initialize: function(params) {
        _.each(params, function(v,k) { this[k] = v }, this); // attach each parameter to the view
        var self = this;
        this.model.on("invalid", function(model, error){
            self.$('textarea').attr('placeholder', error).parent().addClass('control-group error');
        });
    },

    render: function () {
        jsonModel = this.model.toJSON();
        this.$el.html(this.template({
            tk: jsonModel,
            editable: this.editable,
            renderKeys: this.renderKeys
        }));
        return this;
    },

    copy: function(e) {
        if(e.altKey){
            index = app.hotkeys.indexOf(String.fromCharCode(e.keyCode));
            if (index != -1) {
                var item = $(e.currentTarget);
                console.log('combination pressed');
                hk = app.hotkeys[index];
                row = item.parent().parent();
                column = $('small[hotkey=' + hk + ']').parent().index() + 1;
                text = row.children(':nth-child(' + column + ')').text();
                item.val(text).trigger('autosize');
            }
        }
    },

    blur: function(e) {
        var item = $(e.currentTarget);
        this.model.save(
        {
            value: item.val(),
            language: this.language,
            deployment: this.deployment
        },
        {
            success: function() { console.log('TkItemView.save -> success'); },
            error: function() { console.log('TkItemView.save -> error'); item.parent().addClass('control-group error'); }
        });
    },

    focus: function(e) {
        $(e.currentTarget).autosize().parent().removeClass('control-group error');
    }
});
// *************************************************************************************

app.HomeView = Backbone.View.extend({

    events: {
        "submit #target-filters" : "refreshEditor",
        "click #myModal .btn-primary" : "addComparator",
        "show #myModal" : "showComparator"
    },

    initialize: function () {
        this.subViews = new Array();
    },

    render:function () {
        // render core template
        this.$el.html(this.template());

        // add deploymentsList to target filter
        deploymentsList = new app.SelectList({
            model: new app.Deployments(),
            placeholder: 'Choose a deployment'
        }).render().el;
        this.$('#deployments').html(deploymentsList);

        // add languagesList to target filter
        this.$('#languages').html(new app.SelectList({
            model: new app.Languages(),
            placeholder: 'Choose target language'
        }).render().el);
        return this;
    },

    addComparator: function() {
        hk = this.$('#comparator-hotkey input');
        var language = this.$('#comparator-languages select').val();
        var deployment = this.$('#comparator-deployments select').val();
        view = new app.Comparator({
            model: new app.TkCollection(),
            language: language,
            deployment: deployment,
            filter: $('#filter').val(),
            hotkey: hk.val(),
            header: this.headerText(deployment, language)
        });
        this.subViews.push(view);
        this.$('.editor-header').append(view.render().el);
        // TODO: add logic to enable endless comparators look nice
//        if ($('#editor .editor-header').children().length == 4)
//            $('#editor .span4').removeClass('span4').addClass('span3')
        hk.val(parseInt(hk.val())+1);
    },

    showComparator: function() {
        if (this.$('#comparator-deployments').is(':empty')) {
            this.$('#comparator-deployments').html(this.$('#deployments').html());
            this.$('#comparator-languages').html(this.$('#languages').html());
        }
    },

    refreshEditor: function(e) {
        e.preventDefault();
        if ($('#deployments select').val() && $('#languages select').val()) {
            this.removeViews();
            var language = this.$('#languages select').val();
            var deployment = this.$('#deployments select').val();
            view = new app.Editor({
                model: new app.TkCollection(),
                language: language,
                deployment: deployment,
                filter: $('#filter').val(),
                header: this.headerText(deployment, language),
                show: $('#target-filters input[type=radio]:checked').val()
            });
            this.subViews.push(view);
            this.$('#editor').html(view.render().el);
            this.$('#comparator').empty();
            this.$('#add-comparator').fadeIn();
        }
        else
            alert('choose a deployment and a language');
    },

    headerText: function(d,l) {
        return $('#languages option[value=' + l + ']').text() + '<small>[' + $('#deployments option[value=' + d + ']').text() + ']</small>';
    },

    removeViews: function() {
        _.each(this.subViews, function(o) { if (o) o.remove() });
        this.subViews = new Array();
        app.hotkeys = new Array();
        $('#comparator-hotkey input').val(1);
    }

});