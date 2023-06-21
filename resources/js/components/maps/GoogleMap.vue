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
import {ILatLng} from "@js/types/models/models";
import {MapUtil} from "@js/shared/utils";
import {PropType} from "vue";
import {useLayoutStore} from "@js/services/state/states";
import UtilMixin from "@js/shared/mixins/UtilMixin";

let marker: google.maps.Marker;
let last_polygon = null;

export default {
    el: '#map',
    mixins: [UtilMixin],
    data() {
        return {
            map: null as google.maps.Map | null,
        }
    },
    props: {
        height: {
            type: Number,
            default: 250
        },
        location: {
            type: Object as PropType<ILatLng>,
        },
        pickEnabled: {
            type: Boolean,
            default: false
        },
        searchEnabled: {
            type: Boolean,
            default: false
        },
        polygon: null
    },
    watch: {
        polygon(newVal, oldVal) {
            this.onPolygonChange();
        },

        location(newVal: ILatLng, oldVal) {
            if (this.map) {
                this.setLocationToMap(newVal.latitude, newVal.longitude);
            }
        },
    },
    mounted() {
        const self = this;
        this.map = new google.maps.Map(document.getElementById('map'), {
            center: {lat: -34.397, lng: 150.644},
            zoom: 8,
        });

        if (this.location != null) {
            this.setLocationToMap(this.location.latitude, this.location.longitude);
        }


        this.initTheme();
        this.initSearch();
        this.initPick();
        this.onPolygonChange();
    },
    methods: {
        initPick() {
            if (this.pickEnabled) {
                const self = this;
                google.maps.event.addListener(this.map, "click", function (event) {
                    self.setLocationToMap(event.latLng.lat(), event.latLng.lng());
                    self.$emit('change-location', {latitude: event.latLng.lat(), longitude: event.latLng.lng()})
                });
            }
        },
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
            store.$subscribe((mut, state) => {
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
                        this.setLocationToMap(lat, lng)
                    }
                });
            }
        },
        setLocationToMap(latitude, longitude, byLocation = false) {
            if (!latitude || !longitude) {
                if (marker) {
                    marker.setMap(null);
                    marker = null;
                }
                return;
            }

            const pos = {
                lat: parseFloat(latitude),
                lng: parseFloat(longitude),
            };
            if (marker) {
                marker.setMap(this.map);
                marker.setPosition(pos);
            } else {
                marker = new google.maps.Marker({
                    position: pos,
                    map: this.map,
                    title: 'here'
                });
            }
            // this.map.rem
            // this.infoWindow.setPosition(pos);
            // if (byLocation) this.infoWindow.setContent("You are here");
            // else this.infoWindow.setContent("You select here");
            // this.infoWindow.open(this.map);
            const zoom = this.map.getZoom();
            const self = this;
            setTimeout(function () {
                self.map.panTo(pos);
                self.map.setZoom(zoom > 18 ? zoom : 18);
            }, 10)


        },
        onPolygonChange() {
            if (this.polygon != null && this.map != null) {
                let list = [];
                let center = null;
                for (const newValElement of this.polygon) {
                    list.push({
                        lat: newValElement[1],
                        lng: newValElement[0]
                    });
                    if (center == null) {
                        center = {
                            lat: newValElement[1],
                            lng: newValElement[0]
                        };
                        const cameraOptions = {
                            tilt: 0,
                            heading: 0,
                            zoom: 3,
                            center: center,
                        };
                        this.map.moveCamera(cameraOptions);
                    }
                }
                if(last_polygon!=null){
                    last_polygon.setMap(null);
                }
                last_polygon = new google.maps.Polygon({
                    paths: list,
                    strokeColor: "#050df2",
                    strokeOpacity: 0.8,
                    strokeWeight: 2,
                    fillOpacity: 0,
                    clickable: false

                });
                last_polygon.setMap(this.map);
            } else {
                last_polygon?.setMap(null);
            }
        }
    },
    beforeUnmount() {
        marker = null;
        last_polygon = null;
    }
}

</script>

<style scoped>
.pac-item {
    background-color: red !important;
}
</style>
