app.StatsView = Backbone.View.extend({
    // Callback that creates and populates a data table,
    // instantiates the pie chart, passes in the data and
    // draws it.
    drawChart: function (data) {
        _.each(data.series, function(el, i) {
            _.each(el.data, function(ell, ii) {
                el.data[ii] = parseInt(ell, 10);
            });
        });
        this.$('#chart_div').highcharts({

            chart: { type: 'column', width: $('#content').width() },
            title: { text: 'General Statistics' },
            xAxis: { categories: data.categories },
            yAxis: {
                allowDecimals: false,
                min: 0,
                title: { text: 'Number of keys' }
            },
            tooltip: {
                formatter: function() {
                    return '<b>'+ this.x +'</b><br/>'+
                        this.series.name +': '+ this.y +'<br/>'+
                        'Total: '+ this.point.stackTotal;
                }
            },
            plotOptions: {
                column: { size:'100%', stacking: 'normal' }
            },

            series: data.series
        });
    },

    render:function () {
        this.$el.html(this.template());
        var self = this;
        $.getJSON('api/stats').success(function(data) {
            self.drawChart(data);
        });
        return this;
    }

});