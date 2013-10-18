app.StatsView = Backbone.View.extend({
    drawChart: function (data) {
        this.$('#chart_div').highcharts({

            chart: { type: 'column', width: $('#content').width() },
            title: { text: 'General Statistics' },
            xAxis: { categories: data.categories },
            yAxis: {
                allowDecimals: false,
                min: 0,
                title: { text: 'Number of keys' },
                stackLabels: {
                    enabled: true,
                    style: {
                        fontWeight: 'bold',
                        color: '#333'
                    },
                    formatter: function() {
                        return  this.stack.substr(0,2);
                    }
                }
            },
            tooltip: {
                formatter: function() {
                    return '<b>'+ this.x +'</b><br/>'+
                        this.series.name +': '+ this.y + '<br/>'+
                        'Total: '+ this.point.stackTotal  +' (' + this.percentage.toFixed(2) + '%)';
                }
            },
            plotOptions: {
                column: { size:'100%', stacking: 'normal' }
            },
            legend: { enabled: false },
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