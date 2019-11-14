/* Copyright (c) 2006-2012 by SuperMap Contributors (see authors.txt for
 * full list of contributors). Published under the 2-clause BSD license.
 * See license.txt in the SuperMap distribution or repository for the
 * full text of the license. */

/**
 * @requires SuperMap/Layer.js
 */

/**
 * Class: SuperMap.Layer.Elements
 * 此图层用于添加任意对象，用户可以向该图层的div上添加任意dom对象。
 *
 *
 * Inherits from:
 *  - <SuperMap.Layer>
 */
SuperMap.Layer.Elements = SuperMap.Class(SuperMap.Layer, {
    /**
     * Property: curReferencePoint
     * {<SuperMap.Pixel>} 当前参考点
     */
    curReferencePoint:null,
    /**
     * Property: firstReferencePoint
     * {<SuperMap.Pixel>}初始参考点
     */
    firstReferencePoint:null,
    /**
     * Constructor: SuperMap.Layer.Elements
     * 创建一个Elements layer
     *
     * Parameters:
     * name - {String} 图层的名称。
     * (start code)
     * var elementsLayer = new SuperMap.Layer.Elements("elementsLayer");
     * map.addLayers([elementsLayer]);
     * var div = elementsLayer.getDiv();
     * (end)
     */
    initialize: function(name,options) {
        SuperMap.Layer.prototype.initialize.apply(this, [name, options]);
    },

    /**
     * APIMethod: getDiv
     * 获取该图层的div，用户往这个div里添加任意对象、
     *
     * Return:
     * {<HTMLElement>}
     */
    getDiv:function(){
       return this.div;
    },

    /**
     * Method: moveTo
     * Create the tile for the image or resize it for the new resolution 、
     * 创建瓦片或者调整瓦片
     *
     * Parameters:
     * bounds - {<SuperMap.Bounds>}当前级别下计算出的地图范围
     * zoomChanged - {Boolean}缩放级别是否改变
     * dragging - {Boolean}是否为拖动触发的
     */
    moveTo:function(bounds, zoomChanged, dragging) {
        SuperMap.Layer.prototype.moveTo.apply(this, arguments);
        var offsetLeft = parseInt(this.map.layerContainerDiv.style.left, 10);
        offsetLeft = -Math.round(offsetLeft);
        var offsetTop = parseInt(this.map.layerContainerDiv.style.top, 10);
        offsetTop = -Math.round(offsetTop);
        this.div.style.left = offsetLeft + 'px';
        this.div.style.top = offsetTop + 'px';

        var a = this.getLayerPxFromLonLat(new SuperMap.LonLat(0,0));
        if(!this.curReferencePoint){
            this.curReferencePoint  = this.firstReferencePoint = a;
        }
        else{
            //this.last0_0 = this.cur0_0;
            this.curReferencePoint = a;
        }
    },

    /**
     * APIMethod: getDiv
     * 获取该图层的div，用户往这个div里添加任意对象、
     *
     * Parameters:
     * lonlat - {<SuperMap.LonLat>}从地理坐标转换为该图层的像素坐标
     *
     * Return:
     * {<SuperMap.Pixel>}
     */
    getLayerPxFromLonLat:function(lonlat){
        var tempPoint = this.map.getLayerPxFromLonLat(lonlat);
        var offsetLeft = parseInt(this.map.layerContainerDiv.style.left, 10);
        offsetLeft = Math.round(offsetLeft);
        var offsetTop = parseInt(this.map.layerContainerDiv.style.top, 10);
        offsetTop = Math.round(offsetTop);

        return tempPoint.add(offsetLeft,offsetTop);
    },

    /**
     * APIMethod: getOffset
     * 获取当前图层相对于左上角点的像素偏移量。
     */
    getOffset:function(){
        return {
            x:this.curReferencePoint.x - this.firstReferencePoint.x,
            y:this.curReferencePoint.y - this.firstReferencePoint.y
        }
    },

    CLASS_NAME: "SuperMap.Layer.Elements"
});
