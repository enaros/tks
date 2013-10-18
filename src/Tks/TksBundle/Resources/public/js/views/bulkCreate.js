app.BulkCreateView = Backbone.View.extend({

    events: {
        'submit form': 'bulkcreate'
    },

    initialize: function () {

    },

    render:function () {
        // render core template
        this.$el.html(this.template());

        this.$('#bulk-create-deployments').html(new app.SelectList({
            model: new app.Deployments(),
            name: 'deployment',
            placeholder: 'Choose deployment'
        }).render().el);

        return this;
    },

    bulkcreate: function() {
        // ajax call to create keys
        $.ajax({
            url: 'api/bulkcreate',
            data: {
                keylist: this.$('textarea').val(),
                deploymentId: this.$('#bulk-create-deployments select').val()
            },
            dataType: 'json',
            method: 'post',
            success: function(resp) {
                console.log('success', resp);
                app.loadEditor({
                    deploymentId: resp.did,
                    filter: 'today'
                });
            },
            error: function(resp) {
                console.log('error', resp);
            },
            complete: function() {

            }
        });
        return false;
    }
});
