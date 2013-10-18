app.Tk = Backbone.Model.extend({
    urlRoot : "api/tks",
    defauls: {
        id: '',
        value: ''
    },
    validate: function(attribs) {
        if (attribs.value == '')
            return 'value can not be empty';
    }
});

app.TkCollection = Backbone.Collection.extend({
    model : app.Tk,
    url : "api/get_translations"
});

app.Deployments = Backbone.Collection.extend({
    url : "api/deployments",
    initialize:function(access) {
        if (access) {
            this.url += '/' + access;
        }
    }
});

app.Languages = Backbone.Collection.extend({
    url : "api/languages"
});