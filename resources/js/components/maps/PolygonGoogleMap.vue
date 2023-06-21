<template>
    <div id="google_map">
        <input v-if="searchEnabled" ref="map_input" :placeholder="$t('search_by_place')" class="form-control map_input"
               type="text"/>
        <div id="map" :style="{ height: height.toString()+`px` }" class="rounded"></div>

    </div>
</template>


<script lang="ts">


// var script = document.createElement('script');
// script.src = 'https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&callback=initMap';
// script.async = true;
//

import {useLayoutStore} from "@js/services/state/states";
import {MapUtil} from "@js/shared/utils";
import UtilMixin from "@js/shared/mixins/UtilMixin";
import {defineComponent} from "vue";

let last_polygon = null;

export default defineComponent({
    el: '#map',
    mixins: [UtilMixin],
    data() {
        return {
            map: null as google.maps.Map,
            drawing_manager: null,
        }
    },

    props: {
        height: {
            type: [Number, String],
            default: 250
        },
        searchEnabled: {
            type: Boolean,
            default: false
        },
        onPolygonChange: Function,
        polygon: null
    },
    watch: {
        polygon() {
            this.recalculate();
        }
    },
    mounted() {
        let loc = {lat: -24.397, lng: 150.644};
        if (this.polygon != null && this.polygon.length > 0) {
            loc = {
                lat: this.polygon[0][1],
                lng: this.polygon[0][0]
            }
        }
        this.map = new google.maps.Map(document.getElementById('map'), {
            center: loc,
            zoom: 12,
        });

        // console.info(this.polygon);
        this.initTheme();
        this.initSearch();

        this.initDrawingManager();
        this.recalculate();


    },
    methods: {
        initTheme() {
            const self = this;
            let store = useLayoutStore();


            let changeMapTheme = () => {
                if (store.layout.is_dark) {
                    self.map.setOptions({
                        styles: MapUtil.getDarkStyle()
                    })
                } else {
                    self.map.setOptions({
                        styles: null
                    })
                }
            }
            store.$subscribe(() => {
                changeMapTheme();
            });
            changeMapTheme();
        },
        initSearch() {
            if (this.searchEnabled) {
                const self = this;
                const input = this.$refs['map_input'];
                const mapSearch = new google.maps.places.SearchBox(input);
                this.map.controls[google.maps.ControlPosition.TOP_CENTER].push(input);
                mapSearch.addListener("places_changed", () => {
                    const places = mapSearch.getPlaces();
                    console.info(places);
                    if (places.length > 0) {
                        const lat = places[0].geometry.location.lat();
                        const lng = places[0].geometry.location.lng()
                        self.$emit('change-location', {latitude: lat, longitude: lng})
                        this.moveToLocation(lat, lng)
                    }
                });
            }
        },
        moveToLocation(latitude, longitude) {

            const pos = {
                lat: parseFloat(latitude),
                lng: parseFloat(longitude),
            };
            const zoom = this.map.getZoom();
            this.map.panTo(pos);
            this.map.setZoom(zoom > 16 ? zoom : 16);
        },
        initDrawingManager() {
            const self = this;

            this.drawing_manager = new google.maps.drawing.DrawingManager({
                drawingMode: google.maps.drawing.OverlayType.POLYGON,
                drawingControl: true,
                drawingControlOptions: {
                    position: google.maps.ControlPosition.TOP_CENTER,
                    drawingModes: [google.maps.drawing.OverlayType.POLYGON]
                },
                polygonOptions: {
                    editable: true
                }
            });
            this.drawing_manager.setMap(this.map);
            this.drawing_manager.addListener("overlaycomplete", function (event) {
                if (last_polygon != null) {
                    last_polygon.setMap(null);
                }
                last_polygon = event.overlay;
                last_polygon.setOptions({zIndex: 1});
                // last_polygon.setZIndex(google.maps.Marker.MAX_ZINDEX + 1);
                if (self.onPolygonChange) {
                    self.onPolygonChange(last_polygon);
                }
            });
        },
        recalculate() {
            if (this.polygon != null && this.map != null) {
                let list = [];
                let center = null;
                for (const newValElement of this.polygon) {
                    list.push({
                        lat: newValElement[1],
                        lng: newValElement[0]
                    });
                }
                last_polygon = new google.maps.Polygon({
                    paths: list,
                    strokeColor: "#050df2",
                    strokeOpacity: 0.8,
                    strokeWeight: 2,
                    fillOpacity: 0,
                });
                last_polygon.setMap(this.map);
            } else {
                // last_polygon?.setMap(null);
            }
        }
    },


});
</script>

<style scoped>

</style>
