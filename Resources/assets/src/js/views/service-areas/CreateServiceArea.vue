<template>
  <div class="animated fadeIn">
    <b-card>
      <div slot="header">
        <strong>Service area</strong> <small>Form</small>
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
      <b-form @submit.prevent="validateBeforeSubmit(area)">
        <b-form-group
          label="Type"
          label-for="type"
          :label-cols="3"
          :horizontal="true">
          <b-form-radio-group
            id="type"
            name="type"
            v-model="area.type">
            <div class="custom-control custom-radio custom-control-inline">
              <input
                type="radio"
                id="airport"
                name="type"
                v-model="area.type"
                class="custom-control-input"
                value="airport"
                checked>
              <label
                class="custom-control-label"
                for="airport">Airport</label>
            </div>
            <div class="custom-control custom-radio custom-control-inline">
              <input
                type="radio"
                id="trainStation"
                name="type"
                v-model="area.type"
                class="custom-control-input"
                value="trainStation">
              <label
                class="custom-control-label"
                for="trainStation">Train Station</label>
            </div>
            <div class="custom-control custom-radio custom-control-inline">
              <input
                type="radio"
                id="port"
                name="type"
                v-model="area.type"
                class="custom-control-input"
                value="port">
              <label
                class="custom-control-label"
                for="port">Port / Pier</label>
            </div>
          </b-form-radio-group>
        </b-form-group>
        <span class="form-error">{{ errors.first('name') }}</span>
        <b-form-group>
          <label for="name">Name </label>
          <b-form-input
            type="text"
            id="name"
            name="name"
            placeholder="Service Area name"
            v-model="area.name"
            v-validate="'required|max:255'"/>
        </b-form-group>
        <span class="form-error">{{ errors.first('country') }}</span>
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
        <span class="form-error">{{ errors.first('city') }}</span>
        <b-form-group>
          <label for="city">City</label>
          <b-form-input
            type="text"
            id="city"
            name="city"
            placeholder="City"
            v-model="area.city"
            v-validate="'required'"/>
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
          variant="primary">Save</b-button>
      </b-form>
    </b-card>
  </div>

</template>
<script>
import { createNamespacedHelpers } from 'vuex'
const { mapActions } = createNamespacedHelpers('area')
const { mapState }   = createNamespacedHelpers('company')
export default {
  name: 'CreateServiceArea',
  data () {
    return {
      area: {
        company_id: '',
        type      : '',
        name      : '',
        country   : '',
        city      : '',
        IATA      : '',
        ICAO      : '',
        FAA       : '',
      },
      serverErrors  : '',
      successMessage: '',
    }
  },
  computed: { ...mapState({ activeCompanyId: (state) => state.activeCompany.id }) },
  methods : {
    ...mapActions({ store: 'store' }),
    validateBeforeSubmit (area) {
      this.$validator.validateAll().then((result) => {
        if (result)
          this.createArea(area)
      })
    },
    createArea (area) {
      area.company_id = this.activeCompanyId
      this.store(area).then(() => {
        this.$router.push({ name: 'service areas list' })
      })
    },
  },
}
</script>
