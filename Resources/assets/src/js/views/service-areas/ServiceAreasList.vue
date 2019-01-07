<template>
  <div class="animated fadeIn">
    <b-row>
      <b-col>
        <h4><router-link :to="{ name:'Create a service area' }">Create a new service area</router-link></h4>
        <service-areas-table
          :company="activeCompanyId"
          caption="<i class='fa fa-align-justify'></i> Service areas table"/>
      </b-col><!--/.col-->
    </b-row>
  </div>
</template>

<script>
import { createNamespacedHelpers } from 'vuex'
import ServiceAreasTable from './ServiceAreasTable'
const { mapState } = createNamespacedHelpers('company')
export default {
  name      : 'ServiceAreasList',
  components: { ServiceAreasTable },
  computed  : { ...mapState({ activeCompanyId: state => state.activeCompany.id }) },
}
</script>
