if(typeof wp == 'undefined') {
    var wp = {};
}

(function($, _) {
    'use strict';

    wp.table = {
        models: {},
        views: {},
        collections: {}
    };


    wp.table.models.Row = Backbone.Model.extend({

    });


    wp.table.collections.Rows = Backbone.Collection.extend({
        model: wp.table.models.Row,
        url: function() {
            return ajaxurl + '?' + $.param({
                action: '_fetch_' + table_args.screen.base
            });
        }
    });


    wp.table.views.Row = Backbone.View.extend({
        tagName: 'tr',
        initialize: function(data) {
            if(!data.columns) {
                throw('You must specify the table columns')
            }

            this.columns = data.columns;
        },
        render: function() {
            _.each(this.columns, function(label, column) {
                this.$el.append('<td>' + this.model.get(column) + '</td>')
            }, this);

            return this;
        }
    });

    wp.table.views.Table = Backbone.View.extend({
        tagName: 'table',
        initialize: function(data) {
            if(!data.columns) {
                throw('You must specify the table columns')
            }

            this.columns = data.columns;

            _(this).bindAll('add');
            this.collection.bind('add', this.add);
        },
        add: function(row) {
            var row = new wp.table.views.Row({
                model: row,
                columns: this.columns
            });

            this.$el.append(row.render().$el);
        },
        render: function() {
            this.$el.find('.no-items').remove();

            this.collection.each(function(row) {
                this.add(row);
            }, this);

            return this;
        }
    });

})(jQuery, _);