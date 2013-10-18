// *************************************************************************************
// EditorView
// *************************************************************************************
app.BulkView = Backbone.View.extend({

    events: {
        'submit form': 'bulkcopy',
        'click .bulkcopy': 'clickBtn',
        'click img': 'clickBtn',
        'click .close': 'hideAlert',
        'change select': 'hideAlert',
        'change input': 'hideAlert'
    },

    initialize: function () {

    },

    hideAlert: function() {
        this.$('.alert').hide();;
    },

    clickBtn: function() {
        this.$('.alert b:first').text(this.$('input[name=opt]:checked').parent().text());
        this.$('.alert b.from').html(
            this.$('#bulk-source-deployments option:selected').text() + '<small>[' +
            this.$('#bulk-source-languages option:selected').text() + ']</small>'
        );
        this.$('.alert b.to').text(this.$('#bulk-target-deployments option:selected').text());
        var filter = this.$('#bulk-source-filter').val();
        if (filter == "") filter = "no filter selected";
        this.$('.alert b.filter').text(filter);
        this.$('.alert').show();
    },

    bulkcopy: function(e) {
        e.preventDefault();
        if (
            this.$('#bulk-source-deployments select').val() === this.$('#bulk-target-deployments select').val()
            || !this.$('#bulk-source-deployments select').val()
            || !this.$('#bulk-target-deployments select').val()
            ) {
            alert('Source and Target deployment can not be the same');
            return;
        }
        console.log('bulkcopy');
        this.$('.btn').addClass('disabled');
        this.hideAlert();
        this.$('#spinner').show();
        $.ajax({
            url: 'api/bulkcopy',
            data: $('form').serialize(),
            method: 'get',
            success: function() {
                console.log('success');
            },
            error: function() {
                console.log('error');
            },
            complete: function() {
                $('.btn').removeClass('disabled');
                $('#spinner').hide();
            }
        });
    },

    render:function () {
        // render core template
        this.$el.html(this.template());

        this.$('#bulk-source-languages').html(new app.SelectList({
            model: new app.Languages(),
            placeholder: 'Choose target language',
            name: 'source-language'
        }).render().el);

        this.$('#bulk-source-deployments').html(new app.SelectList({
            model: new app.Deployments(),
            name: 'source-deployment',
            placeholder: 'Choose target deployment'
        }).render().el);

        this.$('#bulk-target-deployments').html(new app.SelectList({
            model: new app.Deployments('writeAccess'),
            name: 'target-deployment',
            placeholder: 'Choose target deployment'
        }).render().el);

        return this;
    }
});
