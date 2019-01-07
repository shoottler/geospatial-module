<template>
  <b-card>
    <div slot="header">
      <strong>Edit zone</strong> <small>{{ zone.name }}</small>
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
    <b-form @submit.prevent="validateBeforeSubmit(id, zone)">
      <b-form-group>
        <label for="name">Area</label>
        <b-select-2
          id="serviceArea"
          placeholder="Please select a service area"
          :value="zone.area_id"
          :options="items"
          v-model="zone.area_id"/>
      </b-form-group>
      <b-form-group>
        <label for="name">Name</label>
        <b-form-input
          type="text"
          id="name"
          name="name"
          placeholder="Name"
          v-model="zone.name"
          v-validate="'required|min:8|max:255'"/>
      </b-form-group>
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
        variant="primary">Update</b-button>
      <b-button
        @click="deleteWarning = true"
        variant="link"> delete</b-button>
    </b-form>
    <b-modal
      title="You are about to delete a zone. Are you sure?"
      class="modal-danger"
      v-model="deleteWarning"
      @ok="deleteZone(id)"
      ok-variant="danger">
      When you delete a service area, all its information and all its related records get permanently erased.
      This process can't be undone.
      Proceed only if you are completely sure.
    </b-modal>
  </b-card>
</template>
<script>
import { createNamespacedHelpers, mapActions } from 'vuex'
import { gmapApi } from 'vue2-google-maps'
const { mapState } = createNamespacedHelpers('company')
export default {
  name : 'EditZone',
  props: { id: '' },
  data () {
    return {
      options: { disableDefaultUI: true },
      items  : [],
      zone   : {
        area_id   : '',
        name      : '',
        boundaries: '',
      },
      loaded        : false,
      serverErrors  : '',
      successMessage: '',
      deleteWarning : false,
      all_overlays  : [],
      selectedShape : '',
    }
  },
  computed: {
    ...mapState({ activeCompanyId: (state) => state.activeCompany.id }),
    google: gmapApi,
  },
  mounted () {
    this.getZone(this.id)
    this.initDrawingManager()
    if (this.activeCompanyId)
      this.getAreas()
  },
  watch: {
    zone () {
      this.drawPolygons()
    },
    activeCompanyId () {
      this.items = []
      this.getAreas()
    },
  },
  methods: {
    ...mapActions({
      show     : 'zone/show',
      update   : 'zone/update',
      destroy  : 'zone/destroy',
      areaIndex: 'area/index',
    }),
    getZone (id) {
      this.show(id).then((response) => {
        this.zone =  response.data.data
      }).catch((err) => {
        this.serverErrors = Object.values(err.response.data.errors)
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
    validateBeforeSubmit (id, zone) {
      this.$validator.validateAll().then((result) => {
        if (result) {
          zone.boundaries = this.all_overlays.map((polygon) => {
            const rBoundaries      = {}
            if (polygon.overlay.map !== null)
              rBoundaries.wktPolygon = polygon.overlay.ToWKT()
            return rBoundaries
          })
          this.updateZone(id, zone)
        }
      })
    },
    updateZone (id, zone) {
      this.update({ id, zone }).then(() => {
        this.$router.push({ name: 'zones list' })
      })
    },
    deleteZone (id) {
      this.deleteWarning                             = false
      this.destroy(id).then(() => this.$router.push({ name: 'zones list' }))
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
        })
        const bounds         = new this.google.maps.LatLngBounds()
        map.data.addListener('addfeature', (e) => {
          this.processPoints(e.feature.getGeometry(), bounds.extend, bounds)
          map.fitBounds(bounds)
        })
        if (typeof this.google.maps.Polygon.prototype.ToWKT !== 'function') {
          this.google.maps.Polygon.prototype.ToWKT = function () {
            const poly = this
            // Start the Polygon Well Known Text (WKT) expression
            let wkt     = ''
            const paths = poly.getPaths()
            for (let i = 0; i < paths.getLength(); i++) {
              const path = paths.getAt(i)
              wkt        += '('
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
    drawPolygons () {
      this.$refs.myMap.$mapPromise.then((map) => {
        map.data.addGeoJson({
          type    : 'FeatureCollection',
          features: [
            {
              type    : 'Feature',
              geometry: this.zone.boundaries,
            },
          ],
        })
      })
    },
    processPoints (geometry, callback, thisArg) {
      if (geometry instanceof this.google.maps.LatLng)
        callback.call(thisArg, geometry)
      else if (geometry instanceof this.google.maps.Data.Point)
        callback.call(thisArg, geometry.get())
      else {
        geometry.getArray().forEach((g) => {
          this.processPoints(g, callback, thisArg)
        })
      }
    },
  },
}
</script>
