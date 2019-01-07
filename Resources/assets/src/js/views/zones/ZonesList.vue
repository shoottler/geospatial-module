<template>
  <div class="animated fadeIn">
    <b-row>
      <b-col>
        <h4><router-link :to="{ name:'Create a zone' }">Create a new zone</router-link></h4>
        <zones-table
          :company="activeCompanyId"
          caption="<i class='fa fa-align-justify'></i> Zones table"/>
      </b-col><!--/.col-->
    </b-row>
  </div>
</template>

<script>
import { createNamespacedHelpers } from 'vuex'
import ZonesTable from './ZonesTable'
const { mapState } = createNamespacedHelpers('company')
export default {
  name      : 'ZonesList',
  components: { ZonesTable },
  computed  : { ...mapState({ activeCompanyId: state => state.activeCompany.id }) },
}
</script>
