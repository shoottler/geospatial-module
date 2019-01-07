<template>
  <b-card >
    <div slot="header">
      <strong>Edit area</strong> <small>{{ area.name }}</small>
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
    <b-form @submit.prevent="validateBeforeSubmit(id, area)">
      <b-form-group>
        <label for="name">Area</label>
        <b-form-input
          type="text"
          id="name"
          name="name"
          placeholder="Service Area Name"
          v-model="area.name"
          v-validate="'required|max:255'"/>
      </b-form-group>
      <b-form-group>
        <label for="country">Country</label>
        <b-form-input
          type="text"
          id="country"
          name="country"
          placeholder="Country"
          v-model="area.country"
          v-validate="'required|max:255'"/>
      </b-form-group>
      <b-form-group>
        <label for="website">City</label>
        <b-form-input
          type="text"
          id="city"
          name="city"
          placeholder="City"
          v-model="area.city"
          v-validate="'required|max:255'"/>
      </b-form-group>
      <b-form-group>
        <label for="IATA">IATA Code</label>
        <b-form-input
          type="text"
          id="IATA"
          name="IATA"
          placeholder="IATA Code"
          v-model="area.IATA"/>
      </b-form-group>
      <b-form-group>
        <label for="ICAO">ICAO</label>
        <b-form-input
          type="text"
          id="ICAO"
          name="ICAO"
          placeholder="ICAO"
          v-model="area.ICAO"/>
      </b-form-group>
      <b-form-group>
        <label for="FAA">FAA</label>
        <b-form-input
          type="text"
          id="FAA"
          name="FAA"
          placeholder="FAA"
          v-model="area.FAA"/>
      </b-form-group>
      <b-button
        type="submit"
        variant="primary">Update</b-button>
      <b-button
        @click="deleteWarning = true"
        variant="link"> delete</b-button>
    </b-form>

    <b-modal
      title="You are about to delete a service area. Are you sure?"
      class="modal-danger"
      v-model="deleteWarning"
      @ok="deleteArea(id)"
      ok-variant="danger">
      When you delete a service area, all its information and all its related records get permanently erased.
      This process can't be undone.
      Proceed only if you are completely sure.
    </b-modal>
  </b-card>
</template>
<script>
import { createNamespacedHelpers } from 'vuex'
const { mapActions } = createNamespacedHelpers('area')
export default {
  name : 'EditServiceArea',
  props: { id: '' },
  data () {
    return {
      area: {
        type   : '',
        name   : '',
        country: '',
        city   : '',
        IATA   : '',
        ICAO   : '',
        FAA    : '',
      },
      loaded        : false,
      serverErrors  : '',
      successMessage: '',
      deleteWarning : false,
    }
  },
  mounted () {
    this.getArea(this.id)
  },
  methods: {
    ...mapActions({
      show   : 'show',
      update : 'update',
      destroy: 'destroy',
    }),
    getArea (id) {
      this.show(id).then((response) => {
        this.area =  response.data.data
      }).catch((err) => {
        this.serverErrors = Object.values(err.response.data.errors)
      })
    },
    validateBeforeSubmit (id, area) {
      this.$validator.validateAll().then((result) => {
        if (result)
          this.updateArea(id, area)
      })
    },
    updateArea (id, area) {
      this.update({ id, area }).then(() => {
        this.$router.push({ name: 'service areas list' })
      })
    },
    deleteArea (id) {
      this.deleteWarning = false
      this.destroy(id).then(() => this.$router.push({ name: 'service areas list' }))
    },
  },
}
</script>
