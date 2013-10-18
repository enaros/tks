// *************************************************************************************
// EditorColumnView
// *************************************************************************************
app.EditorColumnView = Backbone.View.extend({
    className: 'editor-rows',

    events: {
        "click .pagination a" : "page",
        "click .pagination button" : "removePager",
        "keydown textarea": "chekTabToGoNewPage"
    },

    initialize: function(params) {
        _.each(params, function(v,k) { this[k] = v }, this); // attach each parameter to the view
        params.model = null; // I need this in order to pass the params through $.param when fetching the model
        this.subViews = new Array();
        this.pageSize = 150;
        _.bindAll(this, "renderResults");
        this.model.fetch({
            success: this.renderResults,
            data: $.param(params)
        });
    },

    renderResults: function() {
        this.$el.empty();
        $('#editor').prepend(this.template({
            header: this.header,
            searchingfor: $('#filter-dropdown input[type=radio]:checked').parents('label').text().trim()
        }));
        this.model.each(this.addOne, this);
        this.pager();
        this.gotopage(1);
        this.trigger('render:finish');
    },

    chekTabToGoNewPage: function(e) {
        if (e.keyCode == 9 && this.$el.find('textarea:visible').get(this.pageSize-1) == e.currentTarget) {
            e.preventDefault();
            var page = parseInt($('#pagination li.active').next().text(), 10);
            if (page) this.gotopage(page);
        }
    },

    render: function () {
        return this;
    },

    page: function(e) {
        var page = parseInt($(e.currentTarget).text(), 10);
        this.gotopage(page);
    },

    gotopage: function(page) {
        this.$el.find('#pagination li').removeClass('active');
        $(this.$el.find('#pagination li')[page-1]).addClass('active');
        var self = this;
        this.$el.find('.editor-row').hide().each(function(i, o) {
            var top = (page * self.pageSize) - 1;
            var bottom = (page - 1) * self.pageSize;
            if (i >= bottom && i <= top) {
                $(o).show();
            }
        });
        $(this.$el.find('textarea:visible').get(0)).select();
        $('body').scrollTop(0);
    },

    removePager: function() {
        this.$el.find('.editor-row').show();
        $('#pagination').remove();
    },

    pager: function() {
        var pages = this.model.length / this.pageSize;
        var dom = "";
        for (var i=1; i<=pages+1; i++) {
            dom += '<li><a>'+i+'</a></li>';
        }
        this.$el.append(
            '<div id="pagination" class="pagination pagination-centered"><ul>' +
            dom +
            '</ul><button class="btn btn-small btn-inverse"><i class="icon-remove icon-white"></i></button></div>'
        );
    },

    addOne: function(tk) {
        var view = new app.TkItemView({
            model: tk,
            language: this.language,
            deployment: this.deployment
        });
        this.subViews.push(view);
        this.$el.append(view.render().el);
    },

    delegateSubviewsEvents: function() {
        this.delegateEvents();
        _.each(this.subViews, function(o) {
            o.delegateEvents();
        }); // delegate events subviews
    },

    removeSubViews: function() {
        _.each(this.subViews, function(o) {
            o.remove();
        }); // remove events subviews
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

    rowClick: function(e) {
        e.stopImmediatePropagation();
        e.stopPropagation();
        console.log('rowclick');
    },

    copy: function(e) {
        if (e.altKey){
            index = app.hotkeys.indexOf(String.fromCharCode(e.keyCode));
            if (index != -1) {
                var item = $(e.currentTarget);
                console.log('combination pressed');
                hk = app.hotkeys[index];
                row = item.parent().parent();
                column = $('small[hotkey=' + hk + ']').parent().index() + 1;
                text = row.children(':nth-child(' + column + ')').text();
                item.val(text).trigger('autosize:resize');
            }
        } else if (e.ctrlKey && e.keyCode == 32) { // ctrl + SPACE
            e.preventDefault();
            var item = $(e.currentTarget);
            console.log('combination pressed');
            item.val('__EMPTY__');
        }
    },

    toggleColor: function(e) {
        $(e.currentTarget).parent().parent().toggleClass('hover');
    },

    blur: function(e) {
        this.toggleColor(e);
        var item = $(e.currentTarget);
        if (item.val() != this.model.get('value')) {
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
        }
    },

    focus: function(e) {
        e.preventDefault();
        this.toggleColor(e);
        $(e.currentTarget).autosize().trigger('autosize.resize').parent().removeClass('control-group error');
        //$(e.currentTarget).select();
    }
});
