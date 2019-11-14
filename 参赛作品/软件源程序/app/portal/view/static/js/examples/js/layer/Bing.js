
/**
 * @requires SuperMap/Util.js
 * @requires SuperMap/Layer/CanvasLayer.js
 */

/**
 * Class: SuperMap.Layer.Bing
 * 用于对接Bing Map。
 *
 * Inherits from:
 * - <SuperMap.CanvasLayer>
 */

SuperMap.Layer.Bing = SuperMap.Class(SuperMap.CanvasLayer, {

    /**
     * APIProperty: layerName
     * {String} 地图名称。
     */
    layerName: null,

    /**
     * APIProperty: key
     * {String} API key for Bing maps, get your own key
     *     at http://bingmapsportal.com/ .
     */
    key: null,

    /**
     * Property: serverResolutions
     * {Array} the resolutions provided by the Bing servers.
     */
    serverResolutions: [
        156543.03390625, 78271.516953125, 39135.7584765625,
        19567.87923828125, 9783.939619140625, 4891.9698095703125,
        2445.9849047851562, 1222.9924523925781, 611.4962261962891,
        305.74811309814453, 152.87405654907226, 76.43702827453613,
        38.218514137268066, 19.109257068634033, 9.554628534317017,
        4.777314267158508, 2.388657133579254, 1.194328566789627,
        0.5971642833948135, 0.29858214169740677, 0.14929107084870338,
        0.07464553542435169
    ],

    /**
     * Property: attributionTemplate
     * {String}
     */
    attributionTemplate: '<span class="olBingAttribution ${type}">' +
        '<div><a target="_blank" href="http://www.bing.com/maps/">' +
        '<img src="${logo}" /></a></div>${copyrights}' +
        '<a style="white-space: nowrap" target="_blank" '+
        'href="http://www.microsoft.com/maps/product/terms.html">' +
        'Terms of Use</a></span>',

    /**
     * Property: metadata
     * {Object} Metadata for this layer, as returned by the callback script
     */
    metadata: null,

    /**
     * APIProperty: type
     * {String} The layer identifier.  Any non-birdseye imageryType
     *     from http://msdn.microsoft.com/en-us/library/ff701716.aspx can be
     *     used.  Default is "Road".
     */
    type: "Road",

    /**
     * APIProperty: culture
     * {String} The culture identifier.  See http://msdn.microsoft.com/en-us/library/ff701709.aspx
     * for the definition and the possible values.  Default is "en-US".
     */
    culture: "zh-Hans",

    /**
     * APIProperty: metadataParams
     * {Object} Optional url parameters for the Get Imagery Metadata request
     * as described here: http://msdn.microsoft.com/en-us/library/ff701716.aspx
     */
    metadataParams: null,

    /** APIProperty: tileOptions
     *  {Object} optional configuration options for <SuperMap.Tile> instances
     *  created by this Layer. Default is
     *
     *  (code)
     *  {crossOriginKeyword: 'anonymous'}
     *  (end)
     */
    tileOptions: null,

    /**
     * Constructor: SuperMap.Layer.Bing
     * 创建一个Bing图层。
     *
     * Example：
     * (code)
     *
     * var road = new SuperMap.Layer.Bing({
     *       name: "My Bing Road Layer",
     *       key: myapiKey,
     *       type: "Road"
     * });
     * (end)
     *
     * 默认为墨卡托投影，所以当需要地图定位以及添加元素在地图上时都需要坐标转换
     * Example:
     * (code)
     *
     * var markers = new SuperMap.Layer.Markers( "Markers" );
     * map.addLayer(markers);
     * var size = new SuperMap.Size(21,25);
     * var offset = new SuperMap.Pixel(-(size.w/2), -size.h);
     * var icon = new SuperMap.Icon('图片地址', size, offset);
     * markers.addMarker(new SuperMap.Marker(new SuperMap.LonLat(118,40 ).transform(
     * new SuperMap.Projection("EPSG:4326"),
     * map.getProjectionObject()),icon));
     *
     * (end)
     *
     * Parameters:
     * options - {Object}  附加到图层属性上的可选项。
     *
     * Required options properties:
     * key - {String} Bing Maps API key for your application. Get one at
     *     http://bingmapsportal.com/.
     * type - {String} 图层标识符.  Any non-birdseye imageryType
     *     from http://msdn.microsoft.com/en-us/library/ff701716.aspx can be
     *     used.
     * Any other documented layer properties can be provided in the config object.
     */
    initialize: function(options) {
        var me = this;
        options = SuperMap.Util.applyDefaults({
            sphericalMercator: true,
            projection:"EPSG:3857",
            numZoomLevels: 16
        }, options);

        var name = options.name || "Bing " + (options.type || this.type);
        me.layerName = name;
        me.culture= "zh-Hans";
        me.metadata = {};

        var newArgs = [name, null,null, options];
        SuperMap.CanvasLayer.prototype.initialize.apply(this, newArgs);
        this.tileOptions = SuperMap.Util.extend({
            crossOriginKeyword: 'anonymous'
        }, this.options.tileOptions);
        this.loadMetadata();
    },

    /**
     * APIMethod: clone
     * 创建当前图层的副本。
     *
     * Parameters:
     * obj - {Object}
     *
     * Returns:
     * {<SuperMap.CanvasLayer>}
     */
    clone: function (obj) {
        var me = this;
        if (obj == null) {
            obj = new SuperMap.CanvasLayer(
                me.name, me.url, me.layerName, me.getOptions());
        }

        obj = SuperMap.CanvasLayer.prototype.clone.apply(me, [obj]);

        return obj;
    },

    /**
     * APIMethod: destroy
     * 解构TiledCachedLayer类，释放资源。
     */
    destroy: function () {
        var me = this;
        SuperMap.CanvasLayer.prototype.destroy.apply(me, arguments);
        me.layerName = null;
        me.urlTemplate = null;
        me.culture = null;
    },

    /**
     * Method: loadMetadata
     */
    loadMetadata: function() {
        this._callbackId = "_callback_" + this.id.replace(/\./g, "_");
        // link the processMetadata method to the global scope and bind it
        // to this instance
        window[this._callbackId] = SuperMap.Function.bind(
            SuperMap.Layer.Bing.processMetadata, this
        );
        var params = SuperMap.Util.applyDefaults({
            key: this.key,
            jsonp: this._callbackId,
            include: "ImageryProviders"
        }, this.metadataParams);
        var url = "http://dev.virtualearth.net/REST/v1/Imagery/Metadata/" +
            this.type + "?" + SuperMap.Util.getParameterString(params);
        var script = document.createElement("script");
        script.type = "text/javascript";
        script.src = url;
        script.id = this._callbackId;
        document.getElementsByTagName("head")[0].appendChild(script);
    },

    /**
     * Method: initLayer
     *
     * Sets layer properties according to the metadata provided by the API
     */
    initLayer: function() {
        var res = this.metadata.resourceSets[0].resources[0];
        var url = res.imageUrl.replace("{quadkey}", "${quadkey}");
        url = url.replace("{culture}", this.culture);
        this.url = [];
        for (var i=0; i<res.imageUrlSubdomains.length; ++i) {
            var u = url.replace("{subdomain}", res.imageUrlSubdomains[i]);
            if(this.type == "Road")
            {
                u = u.replace(/ecn.t([0-9])/,"r$1");
                u = u.replace(/virtualearth.net/,"ditu.live.com");
                u = u.replace(/g=[0-9]*/,"g=91");
            }
            this.url.push(u);
        }
        this.addOptions({
            maxResolution: Math.min(
                this.serverResolutions[res.zoomMin],
                this.maxResolution || Number.POSITIVE_INFINITY
            ),
            numZoomLevels: Math.min(
                res.zoomMax + 1 - res.zoomMin, this.numZoomLevels
            )
        }, true);
    },

    /**
     * Method: getTileUrl
     * 获取瓦片的URL。
     *
     * Parameters:
     * xyz - {Object} 一组键值对，表示瓦片X, Y, Z方向上的索引。
     *
     * Returns
     * {String} 瓦片的 URL 。
     */
    getTileUrl: function (xyz) {
        if (!this.url) {
            return;
        }
        var x = xyz.x, y = xyz.y, z = xyz.z;
        var quadDigits = [];
        for (var i = z; i > 0; --i) {
            var digit = '0';
            var mask = 1 << (i - 1);
            if ((x & mask) != 0) {
                digit++;
            }
            if ((y & mask) != 0) {
                digit++;
                digit++;
            }
            quadDigits.push(digit);
        }
        var quadKey = quadDigits.join("");
        var url = this.selectUrl('' + x + y + z, this.url);

        return SuperMap.String.format(url, {'quadkey': quadKey});
    },

    /**
     * Method: updateAttribution
     * Updates the attribution according to the requirements outlined in
     * http://gis.638310.n2.nabble.com/Bing-imagery-td5789168.html
     */
    updateAttribution: function() {
        var metadata = this.metadata;
        if (!metadata.resourceSets || !this.map || !this.map.center) {
            return;
        }
        var res = metadata.resourceSets[0].resources[0];
        var extent = this.map.getExtent().transform(
            this.map.getProjectionObject(),
            new SuperMap.Projection("EPSG:4326")
        );
        var providers = res.imageryProviders,
            zoom = SuperMap.Util.indexOf(this.serverResolutions,
                this.getServerResolution()),
            copyrights = "", provider, i, ii, j, jj, bbox, coverage;
        for (i=0,ii=providers.length; i<ii; ++i) {
            provider = providers[i];
            for (j=0,jj=provider.coverageAreas.length; j<jj; ++j) {
                coverage = provider.coverageAreas[j];
                // axis order provided is Y,X
                bbox = SuperMap.Bounds.fromArray(coverage.bbox, true);
                if (extent.intersectsBounds(bbox) &&
                    zoom <= coverage.zoomMax && zoom >= coverage.zoomMin) {
                    copyrights += provider.attribution + " ";
                }
            }
        }
        this.attribution = SuperMap.String.format(this.attributionTemplate, {
            type: this.type.toLowerCase(),
            logo: metadata.brandLogoUri,
            copyrights: copyrights
        });
        this.map && this.map.events.triggerEvent("changelayer", {
            layer: this,
            property: "attribution"
        });
    },

    /**
     * Method: setMap
     */
    setMap: function() {
        SuperMap.CanvasLayer.prototype.setMap.apply(this, arguments);
        this.updateAttribution();
        this.map.events.register("moveend", this, this.updateAttribution);
    },

    CLASS_NAME: "SuperMap.Layer.Bing"
});

/**
 * Function: SuperMap.Layer.Bing.processMetadata
 * This function will be bound to an instance, linked to the global scope with
 * an id, and called by the JSONP script returned by the API.
 *
 * Parameters:
 * metadata - {Object} metadata as returned by the API
 */
SuperMap.Layer.Bing.processMetadata = function(metadata) {
    this.metadata = metadata;
    this.initLayer();
    var script = document.getElementById(this._callbackId);
    script.parentNode.removeChild(script);
    window[this._callbackId] = undefined; // cannot delete from window in IE
    delete this._callbackId;
};