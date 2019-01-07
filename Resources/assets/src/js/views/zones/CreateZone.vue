<template>
  <div class="animated fadeIn">
    <b-card>
      <div slot="header">
        <strong>Zone</strong> <small>Form</small>
      </div>
      <div
        v-if="serverErrors"
        class="server-error">
        <div
          v-for="(value, key) in serverErrors"
          :key="key">
          {{ value[0] }}
        </div>
      </div>
      <b-form
        id="myForm"
        @submit.prevent="validateBeforeSubmit(zone)">
        <b-form-group>
          <label for="area">Service Area</label>
          <b-select-2
            id="serviceArea"
            placeholder="Please select a service area"
            :options="items"
            v-model="zone.area_id"
            name="area"/>
        </b-form-group>
        <span class="form-error">{{ errors.first('area') }}</span>
        <b-form-group>
          <label for="name">Name </label>
          <b-form-input
            type="text"
            id="name"
            name="name"
            placeholder="Zone name"
            v-model="zone.name"
            v-validate="'required|max:255'"
          />
        </b-form-group>
        <span class="form-error">{{ errors.first('name') }}</span>
        <b-form-group>
          <label for="name">Quick jump in the map to: </label>
          <GmapAutocomplete
            class="form-control"
            type="text"
            placeholder="Jump to:"
            @place_changed="setPlace"
          />
        </b-form-group>
        <span class="form-error">{{ errors.first('name') }}</span>
        <GmapMap
          ref="myMap"
          :center="{lat:10, lng:10}"
          :zoom="10"
          :options="options"
          style="width: 100%;height: 300px"
        />
        <b-button @click="deleteSelectedShape()">
          delete selected shape
        </b-button>
        <b-button @click="deleteAllShape()">
          delete all shapes
        </b-button>
        <b-button
          type="submit"
          variant="primary">Save</b-button>
      </b-form>
    </b-card>
  </div>

</template>

<script>
import { createNamespacedHelpers } from 'vuex'
import { mapActions } from 'vuex'
import { gmapApi } from 'vue2-google-maps'
const { mapState } = createNamespacedHelpers('company')

export default {
  name: 'CreateZone',
  data () {
    return {
      options: { disableDefaultUI: true },
      items  : [],
      zone   : {
        area_id      : '',
        name      : '',
        boundaries: {},
      },
      loaded        : '',
      serverErrors  : '',
      successMessage: '',
      all_overlays  : [],
      selectedShape : '',
    }
  },
  computed: {
    token () {
      return this.$store.state.token
    },
    ...mapState({ activeCompanyId: (state) => state.activeCompany.id }),
    google: gmapApi,
  },
  mounted () {
    this.geoLocate()
    this.initDrawingManager()
    if (this.activeCompanyId)
      this.getAreas()
  },
  watch: {
    activeCompanyId () {
      this.items = []
      this.getAreas()
    },
  },
  methods: {
    ...mapActions({
      store    : 'zone/store',
      areaIndex: 'area/index',
    }),
    validateBeforeSubmit (zone) {
      this.$validator.validateAll().then((result) => {
        if (result) {
          zone.boundaries = this.all_overlays.map((polygon) => {
            const rBoundaries      = {}
            if (polygon.overlay.map !== null)
              rBoundaries.wktPolygon = polygon.overlay.ToWKT()
            return rBoundaries
          })
          this.createZone(zone)
        }
      })
    },
    getAreas () {
      this.areaIndex().then((response) => {
        this.items  = response.data.data.map((area) => {
          const rArea = {}
          rArea.value = area.id
          rArea.text  = area.name
          return rArea
        })
      })
    },
    createZone (zone) {
      this.store(zone).then(() => {
        this.$router.push({ name: 'zones list' })
      })
    },
    geoLocate: function () {
      navigator.geolocation.getCurrentPosition((position) => {
        this.$refs.myMap.$mapPromise.then((map) => {
          map.panTo({ lat: position.coords.latitude, lng: position.coords.longitude })
        })
      })
    },
    initDrawingManager: function () {
      this.$refs.myMap.$mapPromise.then((map) => {
        const drawingManager = new this.google.maps.drawing.DrawingManager({
          drawingMode          : this.google.maps.drawing.OverlayType.POLYGON,
          drawingControl       : true,
          drawingControlOptions: {
            position    : this.google.maps.ControlPosition.TOP_CENTER,
            drawingModes: ['polygon'],
          },
          polygonOptions: {
            fillColor   : '#ffff00',
            fillOpacity : 0.5,
            strokeWeight: 3,
            clickable   : true,
            editable    : true,
            zIndex      : 1,
          },
        })
        drawingManager.setMap(map)
        this.google.maps.event.addListener(drawingManager, 'overlaycomplete', (event) => {
          this.all_overlays.push(event)
          drawingManager.setDrawingMode(null)
          const newShape = event.overlay
          newShape.type  = event.type
          this.google.maps.event.addListener(newShape, 'click', (event) => {
            this.setSelection(newShape)
          })
          this.setSelection(newShape)
        }
        )
        if (typeof this.google.maps.Polygon.prototype.ToWKT !== 'function') {
          this.google.maps.Polygon.prototype.ToWKT = function () {
            const poly = this
            // Start the Polygon Well Known Text (WKT) expression
            let wkt     = ''
            const paths = poly.getPaths()
            for (let i = 0; i < paths.getLength(); i++) {
              const path = paths.getAt(i)
              // Open a ring grouping in the Polygon Well Known Text
              wkt += '('
              for (let j = 0; j < path.getLength(); j++) {
                // add each vertice, automatically anticipating another vertice (trailing comma)
                wkt += `${path.getAt(j).lng().toString()} ${path.getAt(j).lat().toString()},`
              }
              // Google's approach assumes the closing point is the same as the opening
              // point for any given ring, so we have to refer back to the initial point
              // and append it to the end of our polygon wkt, properly closing it.
              //
              // Additionally, close the ring grouping and anticipate another ring (trailing comma)
              wkt += `${path.getAt(0).lng().toString()} ${path.getAt(0).lat().toString()}),`
            }
            // resolve the last trailing "," and close the Polygon
            wkt = wkt.substring(0, wkt.length - 1)
            return wkt
          }
        }
      })
    },
    setPlace (place) {
      this.$refs.myMap.panTo({ lat: place.geometry.location.lat(), lng: place.geometry.location.lng() })
    },
    setSelection (shape) {
      this.clearSelection()
      this.selectedShape = shape
      shape.setEditable(true)
    },
    clearSelection () {
      if (this.selectedShape) {
        this.selectedShape.setEditable(false)
        this.selectedShape = null
      }
    },
    deleteAllShape () {
      for (let i = 0; i < this.all_overlays.length; i++)
        this.all_overlays[i].overlay.setMap(null)
      this.all_overlays = []
    },
    deleteSelectedShape () {
      if (this.selectedShape)
        this.selectedShape.setMap(null)
    },
  },
}
</script>
