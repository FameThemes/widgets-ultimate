(function( $ ) {
    'use strict';
    String.prototype.replaceAt = function(index, char) {
        var a = this.split("");
        a[index] = char;
        return a.join("");
    };

    var widgetFields = Backbone.View.extend({

        /**
         * View events.
         *
         * @type {Object}
         */
        events: {},

        /**
         * Initialize.
         *
         * @param {Object}         options - Options.
         * @param {Backbone.Model} options.model - Model.
         * @param {jQuery}         options.el - Control container element.
         * @returns {void}
         */
        initialize: function initialize( options ) {
            var control = this;

            if ( ! options.el ) {
                throw new Error( 'Missing options.el' );
            }

            Backbone.View.prototype.initialize.call( control, options );
            if ( control.$el.hasClass( 'added' ) ) {
                return ;
            }

            var id = control.$el.data( 'widget') || false;

            if ( ! id || ! window[ id ] ) {
                return ;
            }

            control.$el.addClass( 'added' );

            var prefixName = control.$el.data( 'name') || '';
            control.config = window[ id ];
            prefixName = prefixName.replace( '[__wname__]', '' );
            control.template = wp.template( 'widget-bundle-fields');

            control.savValues = control.$el.data( 'values' );
            if ( typeof control.savValues !== 'object' ) {
                control.savValues = {};
            }

            control.group = {};

            control.$el.append( control.template( {
                fields: control.config,
                namePrefix: prefixName,
                values: control.savValues
            } ) );


            if ( $( '.bundle-groups', control.$el ).length > 0 ) {
                $( '.bundle-groups', control.$el).each( function(){
                    var b = $( this );
                    var id = b.data( 'id' );
                    var name = b.data( 'name' );
                    var groupControl = {
                        id: id,
                        init: function(){

                        },
                        newItem: function( index, values ){
                            if ( typeof index === "undefined" ) {
                                index = new Date().getTime();
                            }
                            if ( typeof values === "undefined" ) {
                                values = {};
                            }
                            var itemName = name + '['+index+']';

                            var fieldsHtml = control.template( {
                                fields: control.config[ id].fields,
                                namePrefix: itemName,
                                values: values
                            } );

                            var html = '<div class="group-item"><div class="group-item-header"><div class="group-item-title">Title here</div><div class="group-item-toggle"></div></div><div class="group-fields-inner">' + fieldsHtml + '<div class="group-action"><a href="#" class="group-item-remove">Remove</a></div></div></div>';
                            b.find( '.list-groups' ).append( html );
                        },
                        remove: function( e ){
                            e.preventDefault();
                            $( this ).closest( '.group-item' ).remove();
                        },
                        toggle: function( e ){
                            e.preventDefault();
                            var p = $( this ).closest( '.group-item' );
                            if ( p.hasClass( 'closed' ) ) {
                                p.removeClass( 'closed' );
                                $( '.group-fields-inner', p).slideDown();
                            } else {
                                p.addClass( 'closed' );
                                $( '.group-fields-inner', p).slideUp();
                            }
                        },
                        add: function( e ){
                            e.preventDefault();
                            var index = new Date().getTime();
                            groupControl.newItem( index, {} );
                        }
                    };

                    control.group[ id ] = groupControl;

                    if ( typeof control.savValues[ id ] !== "undefined" ) {
                        $.each( control.savValues[ id ],  function( key, value ) {
                            control.group[ id ].newItem( key, value );
                        } );
                    }

                    b.on( 'click', '.new-item', control.group[ id ].add );
                    b.on( 'click', '.group-item-remove', control.group[ id ].remove );
                    b.on( 'click', '.group-item-toggle', control.group[ id ].toggle );
                    $( ".list-groups", b ).sortable({
                        handle: '.group-item-title',
                    });


                } );
            }

        } // end initialize

    });

    function initWidget( event, widgetContainer ) {
        $( '.bundle-widget-fields', widgetContainer ).each( function(){
            var ww = $( this );
            var widgetControl = new widgetFields({
                el: ww
            });
        } );
    }


    var $document = $( document );
    $document.ready( function($) {

        var widgetContainers = $( '.widgets-holder-wrap:not(#available-widgets)' ).find( 'div.widget' );
        widgetContainers.one( 'click.toggle-widget-expanded', function toggleWidgetExpanded() {
            var widgetContainer = $( this );
            initWidget( new jQuery.Event( 'widget-added' ), widgetContainer );
        });

    } );


    $document.on( 'widget-added', initWidget );
    $document.on( 'widget-synced widget-updated', initWidget );

    // When siteorigin page builder added widget
    $document.on( 'panelsopen', function( e ) {
        var widget = $( e.target );
        console.log( widget );
        initWidget( e, widget );
    } );




})( jQuery );