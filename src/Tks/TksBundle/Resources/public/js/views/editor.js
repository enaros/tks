function getParameterByName(name) {
    name = name.replace(/[\[]/, "\\\[").replace(/[\]]/, "\\\]");
    var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
        results = regex.exec(location.search);
    return results == null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
}

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
            placeholder: this.options.placeholder,
            select: this.options.select
        }));
        if (this.options.hide) {
            this.$el.hide();
        }
        this.$el.attr('name', this.options.name);
        if (this.options.multiple) this.$el.attr({'multiple': true, 'size': 6});
        return this;
    }
});

// *************************************************************************************
// EditorView
// *************************************************************************************
app.EditorView = Backbone.View.extend({

    events: {
        "submit #target-filters" : "refreshEditor",
        "submit #comparator-form" : "addComparator",
        "show #myModal" : "showComparator",
        "shown #myModal" : "showComparator",
        "click #csv": "csv",
        "change input[name='opt']": "showHideKeyValuesFilter"
    },

    showHideKeyValuesFilter: function(e) {
        if ($(e.currentTarget).val() != 'mine')
            this.$('#filter-value').hide();
        else
            this.$('#filter-value').show();
    },

    initialize: function () {
        this.subViews = new Array();
    },

    performSearch: function(search) {
        $('#filter').val(search.keyname);
        $('#filter-value').val(search.keyvalue);
        if (search.deploymentId) {
            $('#deployments option[value='+search.deploymentId+']')
                .prop('selected', true);
        }
        if (search.filter) {
            $('#filter-dropdown input[type=radio][value='+search.filter+']')
                .prop('checked', true);
        }
        this.refreshEditor();
    },

    render:function () {
        // render core template
        this.$el.html(this.template());
        var hideDeplList = getParameterByName('select') == 'onlyone';
        var deplString = getParameterByName('deployment');
        // add deploymentsList to target filter
        deploymentsList = new app.SelectList({
            model: new app.Deployments('writeAccess'),
            placeholder: 'Choose a deployment',
            select: deplString,
            hide: hideDeplList
        }).render().el;
        this.$('#deployments').html(deploymentsList);
        // TODO: deplString should be validated
        if (hideDeplList) {
            this.$('#deployments').append('<span><b>Deployment:</b> ' + deplString + '</span>')
        }

        // add languagesList to target filter
        this.$('#languages').html(new app.SelectList({
            model: new app.Languages(),
            placeholder: 'Choose target language'
        }).render().el);
        return this;
    },

    csv: function(e) {
        e.preventDefault();
        var rows = new Array();
        var header = new Array();
        $('.editor-header').children().each(function() {
            header.push('"' + $(this).text().replace(/\(.*\)|×/g, '').trim() + '"'); // remove hotkey
        });
        rows.push(header.join(','));
        console.log(rows);
        $('.editor-row').each(function(i,o) {
            var text = new Array();
            $(o).children().each(function(ii,oo) {
                var val = ($(oo).hasClass('editor')) ? $(oo).find('textarea').val() : val = $(oo).text();
                text.push('"' + val.replace(/"/g, '""').trim() + '"');
            });
            rows.push(text.join(','));
        });
        $.download('api/csv', 'data=' + btoa(encodeURIComponent(rows.join("\n"))));
    },

    addComparator: function(e) {
        e.preventDefault();
        this.$('#myModal').modal('hide');
        hk = this.$('#comparator-hotkey input');
        var language = this.$('#comparator-languages select').val();
        var deployment = this.$('#comparator-deployments select').val();
        var deploymentName = $('#comparator-deployments option[value=' + deployment + ']').text();
        view = new app.ComparatorColumnView({
            model: new app.TkCollection(),
            language: language,
            deployment: deployment,
            filter: $('#filter').val(),
            hotkey: hk.val(),
            header: this.headerText(deploymentName, language)
        });
        this.subViews.push(view);
        this.listenTo(view, 'render:finish', this.comparatorRendered);
        this.listenTo(view, 'remove', this.columns);
        this.$('.editor-header').append(view.render().el);
        hk.val(parseInt(hk.val())+1);
    },

    comparatorRendered: function() {
        this.columns();

        $('.editor-header .comparator').tooltip('destroy')
            .each(function(i,o) {
                $(o).tooltip({
                    title: $(o).find('.muted').text(),
                    placement: 'bottom'
                })
            });
    },

    toggleControls: function() {
        this.$('#spinner').toggle();
        this.$('#target-filters .btn').toggleClass('disabled');
    },

    showComparator: function() {
        if (this.$('#comparator-deployments').is(':empty')) {
            // add deploymentsList to comparators modal window
            this.$('#comparator-deployments').html(new app.SelectList({
                model: new app.Deployments('readAccess'),
                placeholder: 'Choose a deployment'
            }).render().el);
            this.$('#comparator-languages').html(this.$('#languages').html());
        }
        $('#comparator-hotkey input').select();
    },

    refreshEditor: function(e) {
        if (e) e.preventDefault();
        if ($('#deployments select').val() && $('#languages select').val()) {
            this.removeViews();
            this.toggleControls();
            var language = this.$('#languages select').val();
            var deployment = this.$('#deployments select').val();
            var deploymentName = $('#deployments option[value=' + deployment + ']').text();
            view = new app.EditorColumnView({
                model: new app.TkCollection(),
                language: language,
                deployment: deployment,
                filter: $('#filter').val(),
                filterValues: $('#filter-value').val(),
                header: this.headerText(deploymentName, language),
                show: $('#target-filters input[type=radio]:checked').val()
            });
            this.listenTo(view, 'render:finish', this.toggleControls);
            this.listenTo(view, 'render:finish', this.columns);
            this.subViews.push(view);
            this.$('#editor').html(view.render().el);
            this.$('#comparator').empty();
            this.$('#add-comparator').fadeIn();
        }
        else
            alert('choose a deployment and a language');
    },

    columns: function() {
        var cols = $('#editor .editor-header').children().length;
        // each column has a margin left of 2.6% except for the first one
        // that is why 100-2.6*(cols-1) is there :)
        var w = ((100 - 2.6 * (cols-1)) / cols);
        $('#editor .col').css('width', w + '%');
    },

    headerText: function(d,l) {
        return $('#languages option[value=' + l + ']').text() +
            '<small>[' + d + ']</small>';
    },

    removeViews: function() {
        _.each(this.subViews, function(o) {
            o.removeSubViews();
            o.remove();
        });
        this.subViews = new Array();
        app.hotkeys = new Array();
        $('#comparator-hotkey input').val(1);
    },

    delegateSubviewsEvents: function() {
        this.delegateEvents();
        _.each(this.subViews, function(o) {
            if (o.delegateSubviewsEvents)
                o.delegateSubviewsEvents();
        }); // delegate events subviews
    }

});