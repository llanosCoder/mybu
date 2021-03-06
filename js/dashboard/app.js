/*!
 *
 * Angle - Bootstrap Admin App + jQuery
 *
 * Author: @themicon_co
 * Website: http://themicon.co
 * License: http://support.wrapbootstrap.com/knowledge_base/topics/usage-licenses
 *
 */

function i() {
    var url = 'classes/administrar_eventos.php';
    var listaEventos = [];
    $.post(url, {
            accion: 2
        },
        function (data) {
            data = $.parseJSON(data);
            $.each(data.datos, function (i, datos) {
                var evento = {};
                evento.id = datos.id;
                evento.title = datos.descripcion;
                evento.start = new Date(datos.fecha_ini);
                evento.end = new Date(datos.fecha_fin);
                evento.allDay = false;
                evento.tipo = datos.color;
                var color = obtenerColor(datos.color);
                evento.backgroundColor = color;
                evento.borderColor = color;
                listaEventos.push(evento);
            });
            //return listaEventos;
        }).done(
        function () {
            $("#calendar").fullCalendar('removeEvents');
            $("#calendar").fullCalendar('addEventSource', listaEventos);
            $('.fc-event').attr('data-modal', 'form-evento');
            $('.fc-event').modalEffects();
        }
    );
}

$('#calendar').on('click', function () {
    $('.fc-event').attr('data-modal', 'form-evento');
    $('.fc-event').modalEffects();
});

! function (e, t, o) {
    if ("undefined" == typeof o) throw new Error("This application's JavaScript requires jQuery");
    o(function () {
        var e = o("body");
        (new StateToggler).restoreState(e), o("#chk-fixed").prop("checked", e.hasClass("layout-fixed")), o("#chk-collapsed").prop("checked", e.hasClass("aside-collapsed")), o("#chk-boxed").prop("checked", e.hasClass("layout-boxed")), o("#chk-float").prop("checked", e.hasClass("aside-float")), o("#chk-hover").prop("checked", e.hasClass("aside-hover"))
    })
}(window, document, window.jQuery),
function (e, t, o) {
    o(function () {
        o('[data-toggle="popover"]').popover(), o('[data-toggle="tooltip"]').tooltip({
            container: "body"
        }), o(".dropdown input").on("click focus", function (e) {
            e.stopPropagation()
        })
    })
}(window, document, window.jQuery),
function (e, t, o) {
    function n(e, t) {
        var n = o("#remove-after-drop");
        e.fullCalendar({
            lang: 'es',
            header: {
                left: "prev,next today",
                center: "title",
                right: "month,agendaWeek,agendaDay"
            },
            buttonIcons: {
                prev: " fa fa-caret-left",
                next: " fa fa-caret-right"
            },
            eventClick: function (calEvent, jsEvent, view) {
                cargarModalEvento(false, calEvent);
            },
            buttonText: {
                today: "hoy",
                month: "mes",
                week: "semana",
                day: "día"
            },
            editable: !0,
            droppable: !0,
            drop: function (t, a, g) {
                var i = o(this),
                    r = i.data("calendarEventObject");
                if (r) {
                    var l = o.extend({}, r);
                    l.start = t, l.allDay = a, l.backgroundColor = i.css("background-color"), l.borderColor = i.css("border-color"), e.fullCalendar("renderEvent", l, !0), n.is(":checked") && i.remove()
                }
                crearEvento(t.format("YYYY-MM-DD HH:mm:ss"), t.format("YYYY-MM-DD HH:mm:ss"), a.target.innerHTML, g.helper[0].className, 0);
            },
            eventDragStart: function (e) {
                r = e
            },
            events: t
        });

    }

    function a(e) {
        var t = o(".external-events");
        new l(t.children("div"));
        var n = "#f6504d",
            a = o(".external-event-add-btn"),
            i = o(".external-event-name"),
            s = o(".external-event-color-selector .circle"),
            claseColorActual;
        o(".external-events-trash").droppable({
            accept: ".fc-event",
            activeClass: "active",
            hoverClass: "hovered",
            tolerance: "touch",
            drop: function (t, o) {
                if (r) {
                    var n = r.id || r._id;
                    e.fullCalendar("removeEvents", n), o.draggable.remove(), r = null
                }
            }
        }), s.click(function (e) {
            e.preventDefault();

            var t = o(this);
            n = t.css("background-color"), s.removeClass("selected"), t.addClass("selected")
            var clases = t.attr('class').split(' ');
            claseColorActual = clases[1];
        }), a.click(function (e) {
            e.preventDefault();
            var a = i.val();
            if ('' !== o.trim(a)) {
                var r = o("<div/>").css({
                    "background-color": n,
                    "border-color": n,
                    color: "#fff"
                }).html(a);
                t.prepend(r), new l(r), i.val("")
                r.addClass(cambiarClase(claseColorActual));
            }
        })
    }

    $(document).ready(function () {
        i();
        $('#btn_agregar').modalEffects();
    });
    if (o.fn.fullCalendar) {
        o(function () {
            var e = o("#calendar");
                //t = i();
            a(e), n(e, t)
        });
        var r = null,
            l = function (e) {
                e && e.each(function () {
                    var e = o(this),
                        t = {
                            title: o.trim(e.text())
                        };
                    e.data("calendarEventObject", t), e.draggable({
                        zIndex: 1070,
                        revert: !0,
                        revertDuration: 0
                    })
                })
            }
    }
}(window, document, window.jQuery),
function (e, t, o) {
    o(function () {
        if ("undefined" != typeof Chart) {
            var e = function () {
                    return Math.round(100 * Math.random())
                },
                o = {
                    labels: ["January", "February", "March", "April", "May", "June", "July"],
                    datasets: [{
                        label: "My First dataset",
                        fillColor: "rgba(114,102,186,0.2)",
                        strokeColor: "rgba(114,102,186,1)",
                        pointColor: "rgba(114,102,186,1)",
                        pointStrokeColor: "#fff",
                        pointHighlightFill: "#fff",
                        pointHighlightStroke: "rgba(114,102,186,1)",
                        data: [e(), e(), e(), e(), e(), e(), e()]
                    }, {
                        label: "My Second dataset",
                        fillColor: "rgba(35,183,229,0.2)",
                        strokeColor: "rgba(35,183,229,1)",
                        pointColor: "rgba(35,183,229,1)",
                        pointStrokeColor: "#fff",
                        pointHighlightFill: "#fff",
                        pointHighlightStroke: "rgba(35,183,229,1)",
                        data: [e(), e(), e(), e(), e(), e(), e()]
                    }]
                },
                n = {
                    scaleShowGridLines: !0,
                    scaleGridLineColor: "rgba(0,0,0,.05)",
                    scaleGridLineWidth: 1,
                    bezierCurve: !0,
                    bezierCurveTension: .4,
                    pointDot: !0,
                    pointDotRadius: 4,
                    pointDotStrokeWidth: 1,
                    pointHitDetectionRadius: 20,
                    datasetStroke: !0,
                    datasetStrokeWidth: 2,
                    datasetFill: !0,
                    responsive: !0
                },
                a = t.getElementById("chartjs-linechart").getContext("2d"),
                i = (new Chart(a).Line(o, n), {
                    labels: ["January", "February", "March", "April", "May", "June", "July"],
                    datasets: [{
                        fillColor: "#23b7e5",
                        strokeColor: "#23b7e5",
                        highlightFill: "#23b7e5",
                        highlightStroke: "#23b7e5",
                        data: [e(), e(), e(), e(), e(), e(), e()]
                    }, {
                        fillColor: "#5d9cec",
                        strokeColor: "#5d9cec",
                        highlightFill: "#5d9cec",
                        highlightStroke: "#5d9cec",
                        data: [e(), e(), e(), e(), e(), e(), e()]
                    }]
                }),
                r = {
                    scaleBeginAtZero: !0,
                    scaleShowGridLines: !0,
                    scaleGridLineColor: "rgba(0,0,0,.05)",
                    scaleGridLineWidth: 1,
                    barShowStroke: !0,
                    barStrokeWidth: 2,
                    barValueSpacing: 5,
                    barDatasetSpacing: 1,
                    responsive: !0
                },
                l = t.getElementById("chartjs-barchart").getContext("2d"),
                s = (new Chart(l).Bar(i, r), [{
                    value: 300,
                    color: "#7266ba",
                    highlight: "#7266ba",
                    label: "Purple"
                }, {
                    value: 50,
                    color: "#23b7e5",
                    highlight: "#23b7e5",
                    label: "Info"
                }, {
                    value: 100,
                    color: "#fad732",
                    highlight: "#fad732",
                    label: "Yellow"
                }]),
                c = {
                    segmentShowStroke: !0,
                    segmentStrokeColor: "#fff",
                    segmentStrokeWidth: 2,
                    percentageInnerCutout: 85,
                    animationSteps: 100,
                    animationEasing: "easeOutBounce",
                    animateRotate: !0,
                    animateScale: !1,
                    responsive: !0
                },
                d = t.getElementById("chartjs-doughnutchart").getContext("2d"),
                u = (new Chart(d).Doughnut(s, c), [{
                    value: 300,
                    color: "#7266ba",
                    highlight: "#7266ba",
                    label: "Purple"
                }, {
                    value: 40,
                    color: "#fad732",
                    highlight: "#fad732",
                    label: "Yellow"
                }, {
                    value: 120,
                    color: "#23b7e5",
                    highlight: "#23b7e5",
                    label: "Info"
                }]),
                f = {
                    segmentShowStroke: !0,
                    segmentStrokeColor: "#fff",
                    segmentStrokeWidth: 2,
                    percentageInnerCutout: 0,
                    animationSteps: 100,
                    animationEasing: "easeOutBounce",
                    animateRotate: !0,
                    animateScale: !1,
                    responsive: !0
                },
                p = t.getElementById("chartjs-piechart").getContext("2d"),
                h = (new Chart(p).Pie(u, f), [{
                    value: 300,
                    color: "#f532e5",
                    highlight: "#f532e5",
                    label: "Red"
                }, {
                    value: 50,
                    color: "#7266ba",
                    highlight: "#7266ba",
                    label: "Green"
                }, {
                    value: 100,
                    color: "#f532e5",
                    highlight: "#f532e5",
                    label: "Yellow"
                }, {
                    value: 140,
                    color: "#7266ba",
                    highlight: "#7266ba",
                    label: "Grey"
                }]),
                g = {
                    scaleShowLabelBackdrop: !0,
                    scaleBackdropColor: "rgba(255,255,255,0.75)",
                    scaleBeginAtZero: !0,
                    scaleBackdropPaddingY: 1,
                    scaleBackdropPaddingX: 1,
                    scaleShowLine: !0,
                    segmentShowStroke: !0,
                    segmentStrokeColor: "#fff",
                    segmentStrokeWidth: 2,
                    animationSteps: 100,
                    animationEasing: "easeOutBounce",
                    animateRotate: !0,
                    animateScale: !1,
                    responsive: !0
                },
                m = t.getElementById("chartjs-polarchart").getContext("2d"),
                v = (new Chart(m).PolarArea(h, g), {
                    labels: ["Eating", "Drinking", "Sleeping", "Designing", "Coding", "Cycling", "Running"],
                    datasets: [{
                        label: "My First dataset",
                        fillColor: "rgba(114,102,186,0.2)",
                        strokeColor: "rgba(114,102,186,1)",
                        pointColor: "rgba(114,102,186,1)",
                        pointStrokeColor: "#fff",
                        pointHighlightFill: "#fff",
                        pointHighlightStroke: "rgba(114,102,186,1)",
                        data: [65, 59, 90, 81, 56, 55, 40]
                    }, {
                        label: "My Second dataset",
                        fillColor: "rgba(151,187,205,0.2)",
                        strokeColor: "rgba(151,187,205,1)",
                        pointColor: "rgba(151,187,205,1)",
                        pointStrokeColor: "#fff",
                        pointHighlightFill: "#fff",
                        pointHighlightStroke: "rgba(151,187,205,1)",
                        data: [28, 48, 40, 19, 96, 27, 100]
                    }]
                }),
                b = {
                    scaleShowLine: !0,
                    angleShowLineOut: !0,
                    scaleShowLabels: !1,
                    scaleBeginAtZero: !0,
                    angleLineColor: "rgba(0,0,0,.1)",
                    angleLineWidth: 1,
                    pointLabelFontFamily: "'Arial'",
                    pointLabelFontStyle: "bold",
                    pointLabelFontSize: 10,
                    pointLabelFontColor: "#565656",
                    pointDot: !0,
                    pointDotRadius: 3,
                    pointDotStrokeWidth: 1,
                    pointHitDetectionRadius: 20,
                    datasetStroke: !0,
                    datasetStrokeWidth: 2,
                    datasetFill: !0,
                    responsive: !0
                },
                w = t.getElementById("chartjs-radarchart").getContext("2d");
            new Chart(w).Radar(v, b)
        }
    })
}(window, document, window.jQuery),
function (e, t, o) {
    o(function () {
        function t() {
            var e = o(this),
                t = e.data();
            t && (t.triggerInView ? (i.scroll(function () {
                n(e, t)
            }), n(e, t)) : a(e, t))
        }

        function n(e, t) {
            var n = -20;
            !e.hasClass(r) && o.Utils.isInView(e, {
                topoffset: n
            }) && a(e, t)
        }

        function a(e, t) {
            e.ClassyLoader(t).addClass(r)
        }
        var i = o(e),
            r = "js-is-in-view";
        o("[data-classyloader]").each(t)
    })
}(window, document, window.jQuery),
function (e, t, o) {
    "use strict";
    var n = "[data-reset-key]";
    e(o).on("click", n, function (o) {
        o.preventDefault();
        var n = e(this).data("resetKey");
        n ? (e.localStorage.remove(n), t.location.reload()) : e.error("No storage key specified for reset.")
    })
}(jQuery, window, document),
function (e) {
    e.APP_COLORS = {
        primary: "#5d9cec",
        success: "#27c24c",
        info: "#23b7e5",
        warning: "#ff902b",
        danger: "#f05050",
        inverse: "#131e26",
        green: "#37bc9b",
        pink: "#f532e5",
        purple: "#7266ba",
        dark: "#3a3f51",
        yellow: "#fad732",
        "gray-darker": "#232735",
        "gray-dark": "#3a3f51",
        gray: "#dde6e9",
        "gray-light": "#e4eaec",
        "gray-lighter": "#edf1f2"
    }, e.APP_MEDIAQUERY = {
        desktopLG: 1200,
        desktop: 992,
        tablet: 768,
        mobile: 480
    }
}(window, document, window.jQuery),
function (e, t, o) {
    o(function () {
        o(".flatdoc").each(function () {
            Flatdoc.run({
                fetcher: Flatdoc.file("documentation/readme.md"),
                root: ".flatdoc",
                menu: ".flatdoc-menu",
                title: ".flatdoc-title",
                content: ".flatdoc-content"
            })
        })
    })
}(window, document, window.jQuery),
function (e, t, o) {
    "undefined" != typeof screenfull && o(function () {
        function e(e) {
            screenfull.isFullscreen ? e.children("em").removeClass("fa-expand").addClass("fa-compress") : e.children("em").removeClass("fa-compress").addClass("fa-expand")
        }
        var n = o(t),
            a = o("[data-toggle-fullscreen]");
        a.is(":visible") && (a.on("click", function (t) {
            t.preventDefault(), screenfull.enabled ? (screenfull.toggle(), e(a)) : console.log("Fullscreen not enabled")
        }), screenfull.raw && screenfull.raw.fullscreenchange && n.on(screenfull.raw.fullscreenchange, function () {
            e(a)
        }))
    })
}(window, document, window.jQuery),
function (e) {
    "use strict";
    var t = [{
            featureType: "water",
            stylers: [{
                visibility: "on"
            }, {
                color: "#bdd1f9"
            }]
        }, {
            featureType: "all",
            elementType: "labels.text.fill",
            stylers: [{
                color: "#334165"
            }]
        }, {
            featureType: "landscape",
            stylers: [{
                color: "#e9ebf1"
            }]
        }, {
            featureType: "road.highway",
            elementType: "geometry",
            stylers: [{
                color: "#c5c6c6"
            }]
        }, {
            featureType: "road.arterial",
            elementType: "geometry",
            stylers: [{
                color: "#fff"
            }]
        }, {
            featureType: "road.local",
            elementType: "geometry",
            stylers: [{
                color: "#fff"
            }]
        }, {
            featureType: "transit",
            elementType: "geometry",
            stylers: [{
                color: "#d8dbe0"
            }]
        }, {
            featureType: "poi",
            elementType: "geometry",
            stylers: [{
                color: "#cfd5e0"
            }]
        }, {
            featureType: "administrative",
            stylers: [{
                visibility: "on"
            }, {
                lightness: 33
            }]
        }, {
            featureType: "poi.park",
            elementType: "labels",
            stylers: [{
                visibility: "on"
            }, {
                lightness: 20
            }]
        }, {
            featureType: "road",
            stylers: [{
                color: "#d8dbe0",
                lightness: 20
            }]
        }],
        o = "[data-gmap]";
    if (e.fn.gMap) {
        var n = [];
        e(o).each(function () {
            var o = e(this),
                a = o.data("address") && o.data("address").split(";"),
                i = o.data("title") && o.data("title").split(";"),
                r = o.data("zoom") || 14,
                l = o.data("maptype") || "ROADMAP",
                s = [];
            if (a) {
                for (var c in a) "string" == typeof a[c] && s.push({
                    address: a[c],
                    html: i && i[c] || "",
                    popup: !0
                });
                var d = {
                        controls: {
                            panControl: !0,
                            zoomControl: !0,
                            mapTypeControl: !0,
                            scaleControl: !0,
                            streetViewControl: !0,
                            overviewMapControl: !0
                        },
                        scrollwheel: !1,
                        maptype: l,
                        markers: s,
                        zoom: r
                    },
                    u = o.gMap(d),
                    f = u.data("gMap.reference");
                n.push(f), void 0 !== o.data("styled") && f.setOptions({
                    styles: t
                })
            }
        })
    }
}(jQuery, window, document),
function (e, t, o) {
    function n(e) {
        var t = "autoloaded-stylesheet",
            n = o("#" + t).attr("id", t + "-old");
        return o("head").append(o("<link/>").attr({
            id: t,
            rel: "stylesheet",
            href: e
        })), n.length && n.remove(), o("#" + t)
    }
    o(function () {
        o("[data-load-css]").on("click", function (e) {
            var t = o(this);
            t.is("a") && e.preventDefault();
            var a, i = t.data("loadCss");
            i ? (a = n(i), a || o.error("Error creating stylesheet link element.")) : o.error("No stylesheet location defined.")
        })
    })
}(window, document, window.jQuery),
function (e, t, o) {
    var n = "en",
        a = "i18n",
        i = "site",
        r = "jq-appLang";
    o(function () {
        function e(e) {
            o("[data-localize]").localize(i, e)
        }

        function t(e) {
            var t = e.parents(".dropdown-menu");
            if (t.length) {
                var o = t.prev("button, a");
                o.text(e.text())
            }
        }
        if (o.fn.localize) {
            var l = o.localStorage.get(r) || n,
                s = {
                    language: l,
                    pathPrefix: a,
                    callback: function (e, t) {
                        o.localStorage.set(r, l), t(e)
                    }
                };
            e(s), o("[data-set-lang]").on("click", function () {
                l = o(this).data("setLang"), l && (s.language = l, e(s), t(o(this)))
            })
        }
    })
}(window, document, window.jQuery),
function (e) {
    e.defaultColors = {
        markerColor: "#23b7e5",
        bgColor: "transparent",
        scaleColors: ["#878c9a"],
        regionFill: "#bbbec6"
    }, e.VectorMap = function (e, t, o) {
        function n(e, t, o, n) {
            e.vectorMap({
                map: t.mapName,
                backgroundColor: t.bgColor,
                zoomMin: 1,
                zoomMax: 8,
                zoomOnScroll: !1,
                regionStyle: {
                    initial: {
                        fill: t.regionFill,
                        "fill-opacity": 1,
                        stroke: "none",
                        "stroke-width": 1.5,
                        "stroke-opacity": 1
                    },
                    hover: {
                        "fill-opacity": .8
                    },
                    selected: {
                        fill: "blue"
                    },
                    selectedHover: {}
                },
                focusOn: {
                    x: .4,
                    y: .6,
                    scale: t.scale
                },
                markerStyle: {
                    initial: {
                        fill: t.markerColor,
                        stroke: t.markerColor
                    }
                },
                onRegionLabelShow: function (e, t, n) {
                    o && o[n] && t.html(t.html() + ": " + o[n] + " visitors")
                },
                markers: n,
                series: {
                    regions: [{
                        values: o,
                        scale: t.scaleColors,
                        normalizeFunction: "polynomial"
                    }]
                }
            })
        }
        if (e && e.length) {
            var a = e.data(),
                i = a.height || "300",
                r = {
                    markerColor: a.markerColor || defaultColors.markerColor,
                    bgColor: a.bgColor || defaultColors.bgColor,
                    scale: a.scale || 1,
                    scaleColors: a.scaleColors || defaultColors.scaleColors,
                    regionFill: a.regionFill || defaultColors.regionFill,
                    mapName: a.mapName || "world_mill_en"
                };
            e.css("height", i), n(e, r, t, o)
        }
    }
}(window, document, window.jQuery),
function (e, t, o) {
    o(function () {
        var e = new n,
            a = o("[data-search-open]");
        a.on("click", function (e) {
            e.stopPropagation()
        }).on("click", e.toggle);
        var i = o("[data-search-dismiss]"),
            r = '.navbar-form input[type="text"]';
        o(r).on("click", function (e) {
            e.stopPropagation()
        }).on("keyup", function (t) {
            27 == t.keyCode && e.dismiss()
        }), o(t).on("click", e.dismiss), i.on("click", function (e) {
            e.stopPropagation()
        }).on("click", e.dismiss)
    });
    var n = function () {
        var e = "form.navbar-form";
        return {
            toggle: function () {
                var t = o(e);
                t.toggleClass("open");
                var n = t.hasClass("open");
                t.find("input")[n ? "focus" : "blur"]()
            },
            dismiss: function () {
                o(e).removeClass("open").find('input[type="text"]').blur().val("")
            }
        }
    }
}(window, document, window.jQuery),
function (e, t, o) {
    "use strict";

    function n(t) {
        var o = t.data("message"),
            n = t.data("options");
        o || e.error("Notify: No message specified"), e.notify(o, n || {})
    } {
        var a = "[data-notify]";
        e(o)
    }
    e(function () {
        e(a).each(function () {
            var t = e(this),
                o = t.data("onload");
            void 0 !== o && setTimeout(function () {
                n(t)
            }, 800), t.on("click", function (e) {
                e.preventDefault(), n(t)
            })
        })
    })
}(jQuery, window, document),
function (e) {
    var t = {},
        o = {},
        n = function (t) {
            return "string" == e.type(t) && (t = {
                message: t
            }), arguments[1] && (t = e.extend(t, "string" == e.type(arguments[1]) ? {
                status: arguments[1]
            } : arguments[1])), new i(t).show()
        },
        a = function (e, t) {
            if (e)
                for (var n in o) e === o[n].group && o[n].close(t);
            else
                for (var n in o) o[n].close(t)
        },
        i = function (n) {
            this.options = e.extend({}, i.defaults, n), this.uuid = "ID" + (new Date).getTime() + "RAND" + Math.ceil(1e5 * Math.random()), this.element = e(['<div class="uk-notify-message alert-dismissable">', '<a class="close">&times;</a>', "<div>" + this.options.message + "</div>", "</div>"].join("")).data("notifyMessage", this), this.options.status && (this.element.addClass("alert alert-" + this.options.status), this.currentstatus = this.options.status), this.group = this.options.group, o[this.uuid] = this, t[this.options.pos] || (t[this.options.pos] = e('<div class="uk-notify uk-notify-' + this.options.pos + '"></div>').appendTo("body").on("click", ".uk-notify-message", function () {
                e(this).data("notifyMessage").close()
            }))
        };
    return e.extend(i.prototype, {
        uuid: !1,
        element: !1,
        timout: !1,
        currentstatus: "",
        group: !1,
        show: function () {
            if (!this.element.is(":visible")) {
                var e = this;
                t[this.options.pos].show().prepend(this.element);
                var o = parseInt(this.element.css("margin-bottom"), 10);
                return this.element.css({
                    opacity: 0,
                    "margin-top": -1 * this.element.outerHeight(),
                    "margin-bottom": 0
                }).animate({
                    opacity: 1,
                    "margin-top": 0,
                    "margin-bottom": o
                }, function () {
                    if (e.options.timeout) {
                        var t = function () {
                            e.close()
                        };
                        e.timeout = setTimeout(t, e.options.timeout), e.element.hover(function () {
                            clearTimeout(e.timeout)
                        }, function () {
                            e.timeout = setTimeout(t, e.options.timeout)
                        })
                    }
                }), this
            }
        },
        close: function (e) {
            var n = this,
                a = function () {
                    n.element.remove(), t[n.options.pos].children().length || t[n.options.pos].hide(), delete o[n.uuid]
                };
            this.timeout && clearTimeout(this.timeout), e ? a() : this.element.animate({
                opacity: 0,
                "margin-top": -1 * this.element.outerHeight(),
                "margin-bottom": 0
            }, function () {
                a()
            })
        },
        content: function (e) {
            var t = this.element.find(">div");
            return e ? (t.html(e), this) : t.html()
        },
        status: function (e) {
            return e ? (this.element.removeClass("alert alert-" + this.currentstatus).addClass("alert alert-" + e), this.currentstatus = e, this) : this.currentstatus
        }
    }), i.defaults = {
        message: "",
        status: "normal",
        timeout: 5e3,
        group: null,
        pos: "top-center"
    }, e.notify = n, e.notify.message = i, e.notify.closeAll = a, n
}(jQuery, window, document),
function (e, t, o) {
    o(function () {
        o("[data-now]").each(function () {
            function e() {
                var e = moment(new Date).format(n);
                t.text(e)
            }
            var t = o(this),
                n = t.data("format");
            e(), setInterval(e, 1e3)
        })
    })
}(window, document, window.jQuery),
function (e, t, o) {
    "use strict";
    var n = '[data-tool="panel-dismiss"]',
        a = "panel.remove",
        i = "panel.removed";
    e(o).on("click", n, function () {
        function t() {
            e.support.animation ? n.animo({
                animation: "bounceOut"
            }, o) : o()
        }

        function o() {
            var t = n.parent();
            e.when(n.trigger(i, [n])).done(function () {
                n.remove(), t.trigger(i).filter(function () {
                    var t = e(this);
                    return t.is('[class*="col-"]:not(.sortable)') && 0 === t.children("*").length
                }).remove()
            })
        }
        var n = e(this).closest(".panel"),
            r = new e.Deferred;
        n.trigger(a, [n, r]), r.done(t)
    })
}(jQuery, window, document),
function (e, t, o) {
    "use strict";

    function n(e) {
        e.removeClass("fa-plus").addClass("fa-minus")
    }

    function a(e) {
        e.removeClass("fa-minus").addClass("fa-plus")
    }

    function i(t, o) {
        var n = e.localStorage.get(s);
        n || (n = {}), n[t] = o, e.localStorage.set(s, n)
    }

    function r(t) {
        var o = e.localStorage.get(s);
        return o ? o[t] || !1 : void 0
    }
    var l = '[data-tool="panel-collapse"]',
        s = "jq-panelState";
    e(l).each(function () {
        var t = e(this),
            o = t.closest(".panel"),
            l = o.find(".panel-wrapper"),
            s = {
                toggle: !1
            },
            c = t.children("em"),
            d = o.attr("id");
        l.length || (l = o.children(".panel-heading").nextAll().wrapAll("<div/>").parent().addClass("panel-wrapper"), s = {}), l.collapse(s).on("hide.bs.collapse", function () {
            a(c), i(d, "hide")
        }).on("show.bs.collapse", function () {
            n(c), i(d, "show")
        });
        var u = r(d);
        u && (setTimeout(function () {
            l.collapse(u)
        }, 0), i(d, u))
    }), e(o).on("click", l, function () {
        var t = e(this).closest(".panel"),
            o = t.find(".panel-wrapper");
        o.collapse("toggle")
    })
}(jQuery, window, document),
function (e, t, o) {
    "use strict";

    function n() {
        this.removeClass(r)
    }
    var a = '[data-tool="panel-refresh"]',
        i = "panel.refresh",
        r = "whirl",
        l = "standard";
    e(o).on("click", a, function () {
        var t = e(this),
            o = t.parents(".panel").eq(0),
            a = t.data("spinner") || l;
        o.addClass(r + " " + a), o.removeSpinner = n, t.trigger(i, [o])
    })
}(jQuery, window, document),
function (e, t, o) {
    "use strict";
    var n = "[data-animate]";
    e(function () {
        var a = e(t).add("body, .wrapper");
        e(n).each(function () {
            function t(t) {
                !t.hasClass("anim-running") && e.Utils.isInView(t, {
                    topoffset: n
                }) && (t.addClass("anim-running"), setTimeout(function () {
                    t.addClass("anim-done").animo({
                        animation: r,
                        duration: .7
                    })
                }, i))
            }
            var o = e(this),
                n = o.data("offset"),
                i = o.data("delay") || 100,
                r = o.data("play") || "bounce";
            "undefined" != typeof n && (t(o), a.scroll(function () {
                t(o)
            }))
        }), e(o).on("click", n, function () {
            var t = e(this),
                o = t.data("target"),
                n = t.data("play") || "bounce",
                a = e(o);
            a && a && a.animo({
                animation: n
            })
        })
    })
}(jQuery, window, document),
function (e) {
    "use strict";

    function t() {
        var t = e.localStorage.get(a);
        t || (t = {}), t[this.id] = e(this).sortable("toArray"), t && e.localStorage.set(a, t)
    }

    function o() {
        var t = e.localStorage.get(a);
        if (t) {
            var o = this.id,
                n = t[o];
            if (n) {
                var i = e("#" + o);
                e.each(n, function (t, o) {
                    e("#" + o).appendTo(i)
                })
            }
        }
    }
    if (e.fn.sortable) {
        var n = '[data-toggle="portlet"]',
            a = "jq-portletState";
        e(function () {
            e(n).sortable({
                connectWith: n,
                items: "div.panel",
                handle: ".portlet-handler",
                opacity: .7,
                placeholder: "portlet box-placeholder",
                cancel: ".portlet-cancel",
                forcePlaceholderSize: !0,
                iframeFix: !1,
                tolerance: "pointer",
                helper: "original",
                revert: 200,
                forceHelperSize: !0,
                update: t,
                create: o
            })
        })
    }
}(jQuery, window, document),
function (e, t, o) {
    function n() {
        var e = o("<div/>", {
            "class": "dropdown-backdrop"
        });
        e.insertAfter(".aside").on("click mouseenter", function () {
            r()
        })
    }

    function a(e) {
        e.siblings("li").removeClass("open").end().toggleClass("open")
    }

    function i(e) {
        r();
        var t = e.children("ul");
        if (!t.length) return o();
        if (e.hasClass("open")) return a(e), o();
        var n = o(".aside"),
            i = o(".aside-inner"),
            l = parseInt(i.css("padding-top"), 0) + parseInt(n.css("padding-top"), 0),
            s = t.clone().appendTo(n);
        a(e);
        var d = e.position().top + l - h.scrollTop(),
            f = u.height();
        return s.addClass("nav-floating").css({
            position: c() ? "fixed" : "absolute",
            top: d,
            bottom: s.outerHeight(!0) + d > f ? 0 : "auto"
        }), s.on("mouseleave", function () {
            a(e), s.remove()
        }), s
    }

    function r() {
        o(".sidebar-subnav.nav-floating").remove(), o(".dropdown-backdrop").remove(), o(".sidebar li.open").removeClass("open")
    }

    function l() {
        return f.hasClass("touch")
    }

    function s() {
        return p.hasClass("aside-collapsed")
    }

    function c() {
        return p.hasClass("layout-fixed")
    }

    function d() {
        return p.hasClass("aside-hover")
    }
    var u, f, p, h, g;
    o(function () {
        u = o(e), f = o("html"), p = o("body"), h = o(".sidebar"), g = APP_MEDIAQUERY;
        var t = h.find(".collapse");
        t.on("show.bs.collapse", function (e) {
            e.stopPropagation(), 0 === o(this).parents(".collapse").length && t.filter(".in").collapse("hide")
        });
        var a = o(".sidebar .active").parents("li");
        d() || a.addClass("active").children(".collapse").collapse("show"), h.find("li > a + ul").on("show.bs.collapse", function (e) {
            d() && e.preventDefault()
        });
        var r = l() ? "click" : "mouseenter",
            c = o();
        h.on(r, ".nav > li", function () {
            (s() || d()) && (c.trigger("mouseleave"), c = i(o(this)), n())
        })
    })
}(window, document, window.jQuery),
function (e, t, o) {
    o(function () {
        o("[data-skycon]").each(function () {
            var e = o(this),
                t = new Skycons({
                    color: e.data("color") || "white"
                });
            e.html('<canvas width="' + e.data("width") + '" height="' + e.data("height") + '"></canvas>'), t.add(e.children()[0], e.data("skycon")), t.play()
        })
    })
}(window, document, window.jQuery),
function (e, t, o) {
    o(function () {
        o("[data-scrollable]").each(function () {
            var e = o(this),
                t = 250;
            e.slimScroll({
                height: e.data("height") || t
            })
        })
    })
}(window, document, window.jQuery),
function (e, t, o) {
    o(function () {
        function t() {
            var t = o(this),
                n = t.data(),
                a = n.values && n.values.split(",");
            n.type = n.type || "bar", n.disableHiddenCheck = !0, t.sparkline(a, n), n.resize && o(e).resize(function () {
                t.sparkline(a, n)
            })
        }
        o("[data-sparkline]").each(t)
    })
}(window, document, window.jQuery),
function (e, t, o) {
    o(function () {
        o("[data-check-all]").on("change", function () {
            var e = o(this),
                t = e.index() + 1,
                n = e.find('input[type="checkbox"]'),
                a = e.parents("table");
            a.find("tbody > tr > td:nth-child(" + t + ') input[type="checkbox"]').prop("checked", n[0].checked)
        })
    })
}(window, document, window.jQuery),
function (e, t, o, n) {
    o(function () {
        var e = o("body");
        toggle = new StateToggler, o("[data-toggle-state]").on("click", function () {
            var t = o(this),
                a = t.data("toggleState"),
                i = t.attr("data-no-persist") !== n;
            a && (e.hasClass(a) ? (e.removeClass(a), i || toggle.removeState(a)) : (e.addClass(a), i || toggle.addState(a)))
        })
    }), e.StateToggler = function () {
        var e = "jq-toggleState",
            t = {
                hasWord: function (e, t) {
                    return new RegExp("(^|\\s)" + t + "(\\s|$)").test(e)
                },
                addWord: function (e, t) {
                    return this.hasWord(e, t) ? void 0 : e + (e ? " " : "") + t
                },
                removeWord: function (e, t) {
                    return this.hasWord(e, t) ? e.replace(new RegExp("(^|\\s)*" + t + "(\\s|$)*", "g"), "") : void 0
                }
            };
        return {
            addState: function (n) {
                var a = o.localStorage.get(e);
                a = a ? t.addWord(a, n) : n, o.localStorage.set(e, a)
            },
            removeState: function (n) {
                var a = o.localStorage.get(e);
                a && (a = t.removeWord(a, n), o.localStorage.set(e, a))
            },
            restoreState: function (t) {
                //var n = o.localStorage.get(e);
                n && t.addClass(n)
            }
        }
    }
}(window, document, window.jQuery),
function (e, t, o) {
    "use strict";
    var n = e("html"),
        a = e(t);
    e.support.transition = function () {
        var e = function () {
            var e, t = o.body || o.documentElement,
                n = {
                    WebkitTransition: "webkitTransitionEnd",
                    MozTransition: "transitionend",
                    OTransition: "oTransitionEnd otransitionend",
                    transition: "transitionend"
                };
            for (e in n)
                if (void 0 !== t.style[e]) return n[e]
        }();
        return e && {
            end: e
        }
    }(), e.support.animation = function () {
        var e = function () {
            var e, t = o.body || o.documentElement,
                n = {
                    WebkitAnimation: "webkitAnimationEnd",
                    MozAnimation: "animationend",
                    OAnimation: "oAnimationEnd oanimationend",
                    animation: "animationend"
                };
            for (e in n)
                if (void 0 !== t.style[e]) return n[e]
        }();
        return e && {
            end: e
        }
    }(), e.support.requestAnimationFrame = t.requestAnimationFrame || t.webkitRequestAnimationFrame || t.mozRequestAnimationFrame || t.msRequestAnimationFrame || t.oRequestAnimationFrame || function (e) {
        t.setTimeout(e, 1e3 / 60)
    }, e.support.touch = "ontouchstart" in t && navigator.userAgent.toLowerCase().match(/mobile|tablet/) || t.DocumentTouch && document instanceof t.DocumentTouch || t.navigator.msPointerEnabled && t.navigator.msMaxTouchPoints > 0 || t.navigator.pointerEnabled && t.navigator.maxTouchPoints > 0 || !1, e.support.mutationobserver = t.MutationObserver || t.WebKitMutationObserver || t.MozMutationObserver || null, e.Utils = {}, e.Utils.debounce = function (e, t, o) {
        var n;
        return function () {
            var a = this,
                i = arguments,
                r = function () {
                    n = null, o || e.apply(a, i)
                },
                l = o && !n;
            clearTimeout(n), n = setTimeout(r, t), l && e.apply(a, i)
        }
    }, e.Utils.removeCssRules = function (e) {
        var t, o, n, a, i, r, l, s, c, d;
        e && setTimeout(function () {
            try {
                for (d = document.styleSheets, a = 0, l = d.length; l > a; a++) {
                    for (n = d[a], o = [], n.cssRules = n.cssRules, t = i = 0, s = n.cssRules.length; s > i; t = ++i) n.cssRules[t].type === CSSRule.STYLE_RULE && e.test(n.cssRules[t].selectorText) && o.unshift(t);
                    for (r = 0, c = o.length; c > r; r++) n.deleteRule(o[r])
                }
            } catch (u) {}
        }, 0)
    }, e.Utils.isInView = function (t, o) {
        var n = e(t);
        if (!n.is(":visible")) return !1;
        var i = a.scrollLeft(),
            r = a.scrollTop(),
            l = n.offset(),
            s = l.left,
            c = l.top;
        return o = e.extend({
            topoffset: 0,
            leftoffset: 0
        }, o), c + n.height() >= r && c - o.topoffset <= r + a.height() && s + n.width() >= i && s - o.leftoffset <= i + a.width() ? !0 : !1
    }, e.Utils.options = function (t) {
        if (e.isPlainObject(t)) return t;
        var o = t ? t.indexOf("{") : -1,
            n = {};
        if (-1 != o) try {
            n = new Function("", "var json = " + t.substr(o) + "; return JSON.parse(JSON.stringify(json));")()
        } catch (a) {}
        return n
    }, e.Utils.events = {}, e.Utils.events.click = e.support.touch ? "tap" : "click", e.langdirection = "rtl" == n.attr("dir") ? "right" : "left", e(function () {
        if (e.support.mutationobserver) {
            var t = new e.support.mutationobserver(e.Utils.debounce(function () {
                e(o).trigger("domready")
            }, 300));
            t.observe(document.body, {
                childList: !0,
                subtree: !0
            })
        }
    }), n.addClass(e.support.touch ? "touch" : "no-touch")
}(jQuery, window, document),
function (e, t, o) {
    o(function () {})
}(window, document, window.jQuery);