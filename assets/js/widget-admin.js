(function( $, wpcustomize ) {
    'use strict';
    String.prototype.replaceAt = function(index, char) {
        var a = this.split("");
        a[index] = char;
        return a.join("");
    };

    if ( ! wpcustomize ) {
        wpcustomize = null;
    }

    var addParamsURL = function( url, data )
    {
        if ( ! $.isEmptyObject(data) )
        {
            url += ( url.indexOf('?') >= 0 ? '&' : '?' ) + $.param(data);
        }

        return url;
    };

    var widgetTemplate = _.memoize(function ( id ) {
        var compiled,
        /*
         * Underscore's default ERB-style templates are incompatible with PHP
         * when asp_tags is enabled, so WordPress uses Mustache-inspired templating syntax.
         *
         * @see trac ticket #22344.
         */
            options = {
                evaluate:    /<#([\s\S]+?)#>/g,
                interpolate: /\{\{\{([\s\S]+?)\}\}\}/g,
                escape:      /\{\{([^\}]+?)\}\}(?!\})/g,
                variable:    'data'
            };

        return function ( data ) {
            compiled = compiled || _.template( $( '#tmpl-' + id ).html(),  options );
            return compiled( data );
        };
    });

    var $document = $( document );

    var widgetEditor = {
        init: function( $wrapper ){
            $( 'textarea.editor', $wrapper).each( function(){
                var restoreTextMode = false;
                var id = $( this).attr( 'id' );
                // Abort building if the textarea is gone, likely due to the widget having been deleted entirely.
                if ( ! document.getElementById( id ) ) {
                    return;
                }

                // Destroy any existing editor so that it can be re-initialized after a widget-updated event.
                if ( tinymce.get( id ) ) {
                    wp.editor.remove( id );
                }

                wp.editor.initialize( id, {
                    tinymce: {
                    wpautop: true
                    },
                    quicktags: true
                });
            } );
        },

        remove: function( $wrapper ){
            $( 'textarea.editor', $wrapper).each( function(){
                var id = $( this).attr( 'id' );
                if ( tinymce.get( id ) ) {
                    wp.editor.remove( id );
                }
            });
        }

    };

    var colorPickerInit = function( wrapper ){
        $( '.color-input', wrapper).each( function(){
            var w = $( this );
            $('.color-picker', w ).wpColorPicker( {
                change: function( event, ui ){
                    $( '.color-val', w ).val( ui.color.toString()).trigger( 'change' );
                }
            } );
        } );
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
            control.template = widgetTemplate( 'widget-bundle-fields');

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


            $( '[visibly]', control.$el ).Visibly( {
                clearOnHide: true
            } );


            if ( $( '.bundle-groups', control.$el ).length > 0 ) {
                $( '.bundle-groups', control.$el).each( function(){
                    var b = $( this );
                    var id = b.data( 'id' );
                    var name = b.data( 'name' );
                    var groupControl = {
                        id: id,
                        init: function(){

                        },
                        newItem: function( index, values, closed ){
                            if ( typeof index === "undefined" ) {
                                index = new Date().getTime();
                            }
                            if ( typeof closed === "undefined" ) {
                                closed = false
                            }
                            if ( typeof values === "undefined" ) {
                                values = {};
                            }
                            var itemName = name + '['+index+']';

                            var fieldsHtml = control.template( {
                                fields: control.config[ id ].fields,
                                namePrefix: itemName,
                                values: values
                            } );

                            var html = '<div class="group-item"><div class="group-item-header"><div class="group-item-title">'+WIDGET_US.group_item_title+'</div><div class="group-item-toggle"></div></div><div class="group-fields-inner">' + fieldsHtml + '<div class="group-action"><a href="#" class="group-item-remove">'+WIDGET_US.remove+'</a></div></div></div>';
                            html = $( html );
                            b.find( '.list-groups' ).append( html );
                            if ( closed ) {
                                html.addClass( 'closed' );
                                $( '.group-fields-inner', html).hide();
                            }

                            console.log( control.config[ id ] );

                            if ( control.config[ id ].title_id ) {
                                $( '.wu-text.fid-'+control.config[ id].title_id, html).on( 'change keyup wu_init', function(){
                                    var v = $( this).val();
                                    v = v.trim();
                                    if ( ! v ) {
                                        v = WIDGET_US.group_item_title;
                                    }
                                    $( '.group-item-title', html ).text( v );
                                } );

                                $( '.wu-text.fid-'+control.config[ id].title_id, html).trigger( 'wu_init' );
                            }

                            $('[visibly]', html ).Visibly({
                                clearOnHide: true
                            });

                            $document.trigger( 'widgets-ultimate-group-item-innit', [ html ] );
                        },
                        remove: function( e ){
                            e.preventDefault();
                            var g = $( this ).closest( '.group-item' );
                            $('html,body').animate({
                                 scrollTop: g.offset().top - 100 },
                                300,
                                'swing',
                                function () {
                                    g.slideUp( 300, function(){
                                        widgetEditor.remove( g );
                                        g.remove();
                                    } );
                                }
                            );

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
                            control.group[ id ].newItem( key, value, true );
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

            $document.trigger( 'widgets-ultimate-innit', [ control.$el ] );

            control.$el.on( 'change keyup', 'input:not(.wu_input_base), select, textarea', function(){
                $( '.wu_input_base', control.$el).val( new Date().getTime()).trigger( 'change' );
                //var widget_inside = $( this).closest( '.widget-inside' );
                //if ( widget_inside ) {
                    //$( '.widget-control-save', widget_inside).click();
                //}
            } );


        } // end initialize

    });


    var widgetMedia =  {
        setAttachment: function( attachment ){
             this.attachment = attachment;
        },
        getThumb: function( attachment ){
            if ( typeof attachment !== "undefined" ) {
                this.attachment = attachment;
            }
            var t = new Date().getTime();
            if ( typeof this.attachment.sizes !== "undefined" ) {
                if ( typeof this.attachment.sizes.medium !== "undefined" ) {
                    return addParamsURL( this.attachment.sizes.medium.url, { t : t } );
                }
            }
            return addParamsURL( attachment.url, { t : t } );
        },
        getURL: function( attachment ) {
            if ( typeof attachment !== "undefined" ) {
                this.attachment = attachment;
            }
            var t = new Date().getTime();
            return addParamsURL( this.attachment.url, { t : t } );
        },
        getID: function( attachment ){
            if ( typeof attachment !== "undefined" ) {
                this.attachment = attachment;
            }
            return this.attachment.id;
        },
        getInputID: function( attachment ){
            $( '.attachment-id', this.preview ).val( );
        },
        setPreview: function( $el ){
            this.preview = $el;
        },
        insertImage: function( attachment ){
            if ( typeof attachment !== "undefined" ) {
                this.attachment = attachment;
            }

            var url = this.getThumb();
            var id = this.getID();
            $( '.media-item-preview', this.preview ).css( 'background-image', 'url("'+url+'")' );
            $( '.attachment-id', this.preview ).val( id ).trigger( 'change' );
            this.preview.addClass( 'attachment-added' );

        },
        insertVideo: function(attachment ){
            if ( typeof attachment !== "undefined" ) {
                this.attachment = attachment;
            }

            var url = this.getURL();
            var id = this.getID();
            var mime = this.attachment.mime;
            var html = '<video width="100%" height="" controls><source src="'+url+'" type="'+mime+'">Your browser does not support the video tag.</video>';
            $( '.media-item-preview', this.preview ).html( html );
            $( '.attachment-id', this.preview ).val( id ).trigger( 'change' );
            this.preview.addClass( 'attachment-added' );
        },
        insertFile: function( attachment ){
            if ( typeof attachment !== "undefined" ) {
                this.attachment = attachment;
            }

            var mime = this.attachment.mime;
            if ( mime.indexOf( 'image' ) > -1 ) {
                this.insertImage();
            } else if ( mime.indexOf( 'video' ) > -1 ) {
                this.insertVideo();
            } else {
                var id = this.getID();
                var icon = this.attachment.icon;
                $( '.media-item-preview', this.preview ).html( '<img scr="'+icon+'" alt="">' );
                $( '.attachment-id', this.preview ).val( id ).trigger( 'change' );
                this.preview.addClass( 'attachment-added' );
            }
        },
        remove: function( $el ){
            if ( typeof $el !== "undefined" ) {
                this.preview = $el;
            }

            $( '.media-item-preview', this.preview ).removeAttr( 'style').html( '' );
            $( '.attachment-id', this.preview ).val( '' ).trigger( 'change' );
            this.preview.removeClass( 'attachment-added' );
        }

    };

    var widgetMediaImage = wp.media({
        title: wp.media.view.l10n.addMedia,
        multiple: false,
        library: {type: 'image' }
    });

    widgetMediaImage.on('select', function () {
        var attachment = widgetMediaImage.state().get('selection').first().toJSON();
        widgetMedia.insertImage( attachment );
    });

    var widgetMediaVideo = wp.media({
        title: wp.media.view.l10n.addMedia,
        multiple: false,
        library: {type: 'video' }
    });

    widgetMediaVideo.on('select', function () {
        var attachment = widgetMediaVideo.state().get('selection').first().toJSON();
        //console.log( attachment );
        widgetMedia.insertVideo( attachment );
    });

    var widgetMediaFile = wp.media({
        title: wp.media.view.l10n.addMedia,
        multiple: false
    });

    widgetMediaFile.on('select', function () {
        var attachment = widgetMediaFile.state().get('selection').first().toJSON();
        //console.log( attachment );
        widgetMedia.insertFile( attachment );
    });


    function initWidget( event, widgetContainer ) {
        $( '.bundle-widget-fields', widgetContainer ).each( function(){
            var ww = $( this );
            var widgetControl = new widgetFields({
                el: ww
            });
        } );
    }

    var init = function(){
        var widgetContainers = $('.widgets-holder-wrap:not(#available-widgets)').find('div.widget');
        widgetContainers.one('click.toggle-widget-expanded', function toggleWidgetExpanded() {
            var widgetContainer = $(this);
            initWidget(new jQuery.Event('widget-added'), widgetContainer);
        });

        // Open image Media
        $document.on('click', '.widget-image-input .select-media, .widget-image-input .change-media', function (e) {
            e.preventDefault();
            widgetMedia.setPreview($(this).closest('.widget-image-input'));
            widgetMediaImage.open();
        });


        // Open Video Media
        $document.on('click', '.widget-video-input .select-media, .widget-video-input .change-media', function (e) {
            e.preventDefault();
            widgetMedia.setPreview($(this).closest('.widget-video-input'));
            //widgetMediaImage.open( );
            widgetMediaVideo.open();
        });

        // Open File Media
        $document.on('click', '.widget-file-input .select-media, .widget-file-input .change-media', function (e) {
            e.preventDefault();
            widgetMedia.setPreview($(this).closest('.widget-file-input'));
            //widgetMediaImage.open( );
            widgetMediaFile.open();
        });

        // Remove
        $document.on('click', '.widget-attachment-input .remove-media', function (e) {
            e.preventDefault();
            widgetMedia.remove($(this).closest('.widget-attachment-input'));
        });


        $document.on('widget-added widget-ultimate-added', initWidget);
        $document.on('widget-synced widget-updated', initWidget);

        // When siteorigin page builder added widget
        $document.on('panelsopen', function (e) {
            var widget = $(e.target);
            initWidget(e, widget);
        });

        $document.on('widgets-ultimate-innit widgets-ultimate-group-item-innit', function (e, wrapper) {
            widgetEditor.init(wrapper);
            colorPickerInit(wrapper);
        });

        var insertSourceToList = function(res, p ){
            $( '.object-results', p).html( '' );
            if ( res.items ) {
                $.each( res.items, function( index, item ){
                    $( '.object-results', p).append( '<li data-id="'+item.id+'">'+item.title+'</li>' );
                } );
            }
        };

        // Ajax search
        $document.on( 'keyup', '.object-source .object-ajax-input', function(){
            var obj = $( this );
            var p = $( this).closest( '.object-source' );
            var v = $( this).val();
            var id = p.data( 'source-id' ) || null;
            if ( ! id ) {
                id = 'ws-'+new Date().getTime();
                p.data( 'source-id', id );
            }
            if ( window[ id ] ) {
                window[ id ].abort();
            }

            var source = p.data( 'source' ) ||  null;

            if ( ! source ) {
                return;
            }

            p.data( 'source-loaded', false );

            source.action = 'widget_ultimate_search';
            source.search = v;
            $( '.object-results', p ).html();
            window[ id ] = $.ajax({
                data: source,
                url: WIDGET_US.ajax,
                dataType: 'json',
                error: function( res ){

                },
                success: function( res ){
                    insertSourceToList( res, p );
                }
            });

        } );

        var loadSourceDataOnece = function( p ){
           if ( p.data( 'source-loaded' ) ) {
                return;
           } else {
               var id = p.data( 'source-id' ) || null;
               if ( ! id ) {
                   id = 'ws-'+new Date().getTime();
                   p.data( 'source-id', id );
               }

               p.data( 'source-loaded', true );
               $( '.object-ajax-input', p).val( '' );

               var source = p.data( 'source' ) ||  null;

               if ( ! source ) {
                   return;
               }
               var key = '_data_source_pt_'+source.post_type+'tax_'+source.tax;
               if ( window[ id ] ) {
                   window[ 'ajax'+key ].abort();
               }

               if ( window[ key ] ) {
                   insertSourceToList( window[ key ], p );
               } else {
                   source.action = 'widget_ultimate_search';
                   source.search = '';
                   $( '.object-results', p ).html();
                   window[ 'ajax'+key ] = $.ajax({
                       data: source,
                       url: WIDGET_US.ajax,
                       dataType: 'json',
                       error: function( res ){

                       },
                       success: function( res ){
                           window[ key ] = res;
                           insertSourceToList( res, p );
                       }
                   });
               }

            }
        };


        $document.on( 'click', '.object-source .object-results li', function( e ){
            e.preventDefault();
            var p = $( this).closest( '.object-source' );
            var id = $( this).data( 'id' );
            $( '.object-id', p ).val( id).trigger( 'change' );
            $( '.object-label', p ).val( $( this).text() );
            $( '.object-ajax-search', p ).hide();
        });

        $document.on( 'click', '.object-source .object-label', function( e ){
            e.preventDefault();
            var p = $( this).closest( '.object-source' );
            $( '.object-ajax-search', p ).toggle();
            loadSourceDataOnece( p );
        });

        $document.on( 'click', '.object-source .object-clear', function( e ){
            e.preventDefault();
            var p = $( this).closest( '.object-source' );
            $( '.object-id', p ).val( '' ).trigger( 'change' );
            $( '.object-label', p ).val( '' );
        });

        // load icons
        var iconPicker, iconPickerCurrentEl, iconList ;
        iconPicker = $( '#widgets-ultimate-icons-picker' );
        $.ajax({
            data: {
                action: 'widget_ultimate_icons'
            },
            url: WIDGET_US.ajax,
            dataType: 'json',
            error: function( res ){

            },
            success: function( res ){
                iconList = res;

                $.each( res, function( icon_id, icon_config ){
                    console.log( icon_id,  icon_config );
                    $( '.media-router', iconPicker ).html( '<a href="#" data-font="'+icon_id+'" class="media-menu-item">'+icon_config.name+'</a>' );

                    // anotherString = someString.replace(/cat/g, 'dog');

                    var icon_html = '<ul class="attachments list-icons icon-'+icon_id+'">';
                    $.each( icon_config.icons, function( i, icon_class ){
                        var class_name = '';
                        if ( icon_config.class_config ) {
                            class_name = icon_config.class_config.replace(/__icon_name__/g, icon_class  );
                        } else {
                            class_name = icon_class;
                        }

                        icon_html += '<li title="'+icon_class+'" data-id="'+class_name+'"><span class="icon-wrapper"><i class="'+class_name+'"></i></span></li>';

                    } );
                    icon_html += '</ul>';

                    $( '.attachments-browser', iconPicker).append( icon_html );

                } );

                $( '.media-router a', iconPicker).eq( 0 ).addClass( 'active' );
            }
        });



        iconPicker.on( 'click', '.media-modal-close', function( e ) {
            e.preventDefault();
            iconPicker.hide();
            iconPickerCurrentEl = null;
        } );

        iconPicker.on( 'click', '.media-modal-backdrop', function( e ) {
            e.preventDefault();
            iconPicker.find( '.media-modal-close').click();
        } );

        // Search icon
        iconPicker.on( 'keyup', '#icons-search-input', function( e ) {
            var v = $( this).val();
            v = v.trim();
            if ( v ) {
                $( ".list-icons li" ).hide();
                $( ".list-icons li[data-id*='"+v+"']" ).show();
            } else {
                $( ".list-icons li" ).show();
            }

        } );

        // Open icon picker
        $document.on( 'click', '.object-icon-picker .object-label, .object-icon-picker .icon-preview', function(){
            iconPicker.show();
            iconPickerCurrentEl = $( this).closest( '.object-icon-picker' );
        } );

        // Clear icon picker
        $document.on( 'click', '.object-icon-picker .object-clear', function(){
            iconPickerCurrentEl = $( this).closest( '.object-icon-picker' );
            $( '.icon-preview', iconPickerCurrentEl).html( '' );
            $( '.object-label', iconPickerCurrentEl).val( '').trigger( 'change' );
        } );

        // Pick an icon
        iconPicker.on( 'click', '.list-icons li', function( e ) {
            e.preventDefault();
            var icon_html  = $( this ).find('.icon-wrapper').html();
            var name = $( this).data( 'id' );
            if ( iconPickerCurrentEl ) {
                $( '.object-label', iconPickerCurrentEl ).val( name ).trigger( 'change' );
                $( '.icon-preview', iconPickerCurrentEl).html( icon_html );
            }
            // Close iconPicker
            iconPicker.find( '.media-modal-close').click();
        } );

    };

    if ( ! wpcustomize ) {
        $document.ready( function (  ) {
            init();
        });
    } else {
        wpcustomize.bind( 'ready', function( e, b ) {
            init();
        } );
    }




})( jQuery, wp.customize || null );